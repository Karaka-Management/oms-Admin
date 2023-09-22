<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Controller;

use Model\Setting;
use Model\SettingMapper;
use Modules\Admin\Models\Account;
use Modules\Admin\Models\AccountCredentialMapper;
use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\AccountPermission;
use Modules\Admin\Models\AccountPermissionMapper;
use Modules\Admin\Models\App;
use Modules\Admin\Models\AppMapper;
use Modules\Admin\Models\Contact;
use Modules\Admin\Models\ContactMapper;
use Modules\Admin\Models\DataChange;
use Modules\Admin\Models\DataChangeMapper;
use Modules\Admin\Models\Group;
use Modules\Admin\Models\GroupMapper;
use Modules\Admin\Models\GroupPermission;
use Modules\Admin\Models\GroupPermissionMapper;
use Modules\Admin\Models\LocalizationMapper;
use Modules\Admin\Models\Module;
use Modules\Admin\Models\ModuleMapper;
use Modules\Admin\Models\ModuleStatusUpdateType;
use Modules\Admin\Models\NullAccount;
use Modules\Admin\Models\PermissionCategory;
use Modules\Admin\Models\SettingsEnum;
use Modules\Media\Models\Collection;
use Modules\Media\Models\CollectionMapper;
use Modules\Media\Models\UploadFile;
use Modules\Messages\Models\EmailMapper;
use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Account\GroupStatus;
use phpOMS\Account\PermissionAbstract;
use phpOMS\Account\PermissionOwner;
use phpOMS\Account\PermissionType;
use phpOMS\Application\ApplicationInfo;
use phpOMS\Application\ApplicationManager;
use phpOMS\Application\ApplicationType;
use phpOMS\Auth\LoginReturnType;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Localization\Localization;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestMethod;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\Http\Rest;
use phpOMS\Message\Mail\Email;
use phpOMS\Message\Mail\MailHandler;
use phpOMS\Message\Mail\Smtp;
use phpOMS\Message\Mail\SubmitType;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\Model\Message\Reload;
use phpOMS\Module\ModuleInfo;
use phpOMS\Module\ModuleStatus;
use phpOMS\Security\EncryptionHelper;
use phpOMS\System\File\Local\File;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Uri\UriFactory;
use phpOMS\Utils\ArrayUtils;
use phpOMS\Utils\Parser\Markdown\Markdown;
use phpOMS\Utils\Parser\Php\ArrayParser;
use phpOMS\Utils\RnG\StringUtils as StringRng;
use phpOMS\Utils\StringUtils;
use phpOMS\Validation\Network\Email as EmailValidator;
use phpOMS\Version\Version;

/**
 * Admin controller class.
 *
 * This class is responsible for the basic admin activities such as managing accounts, groups, permissions and modules.
 *
 * @package Modules\Admin
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Api method to login
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiLogin(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);

        $login = AccountMapper::login(
            $request->getDataString('user') ?? '',
            $request->getDataString('pass') ?? ''
        );

        if ($login > LoginReturnType::OK) {
            $this->app->sessionManager->set('UID', $login, true);
            $response->set($request->uri->__toString(), new Reload());
        } elseif ($login === LoginReturnType::NOT_ACTIVATED) {
            $response->header->status = RequestStatusCode::R_401;
            $this->fillJsonResponse(
                $request,
                $response,
                NotificationLevel::WARNING,
                '',
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'NOT_ACTIVATED'),
                null
            );
        } elseif ($login === LoginReturnType::WRONG_INPUT_EXCEEDED) {
            $response->header->status = RequestStatusCode::R_401;
            $this->fillJsonResponse(
                $request,
                $response,
                NotificationLevel::WARNING,
                '',
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'WRONG_INPUT_EXCEEDED'),
                null
            );
        } else {
            $response->header->status = RequestStatusCode::R_401;
            $this->fillJsonResponse(
                $request,
                $response,
                NotificationLevel::WARNING,
                '',
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'LOGIN_ERROR'),
                null
            );
        }
    }

    /**
     * Api method to login
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiLogout(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);

        $this->app->sessionManager->remove('UID');
        $this->app->sessionManager->save();

        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
        $response->set($request->uri->__toString(), [
            'status'   => NotificationLevel::OK,
            'title'    => $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'LogoutSuccessfulTitle'),
            'message'  => $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'LogoutSuccessfulMsg'),
            'response' => null,
        ]);
    }

    /**
     * Create basic server mail handler
     *
     * @return MailHandler
     *
     * @since 1.0.0
     **/
    public function setUpServerMailHandler() : MailHandler
    {
        /** @var \Model\Setting[] $emailSettings */
        $emailSettings = $this->app->appSettings->get(
            names: [
                SettingsEnum::MAIL_SERVER_OUT,
                SettingsEnum::MAIL_SERVER_PORT_OUT,
                SettingsEnum::MAIL_SERVER_TYPE,
                SettingsEnum::MAIL_SERVER_USER,
                SettingsEnum::MAIL_SERVER_PASS,
                SettingsEnum::MAIL_SERVER_TLS,
            ],
            module: 'Admin'
        );

        if (empty($emailSettings)) {
            /** @var \Model\Setting[] $emailSettings */
            $emailSettings = $this->app->appSettings->get(
                names: [
                    SettingsEnum::MAIL_SERVER_OUT,
                    SettingsEnum::MAIL_SERVER_PORT_OUT,
                    SettingsEnum::MAIL_SERVER_TYPE,
                    SettingsEnum::MAIL_SERVER_USER,
                    SettingsEnum::MAIL_SERVER_PASS,
                    SettingsEnum::MAIL_SERVER_TLS,
                ],
                module: 'Admin'
            );
        }

        $handler = new MailHandler();
        $handler->setMailer($emailSettings[SettingsEnum::MAIL_SERVER_TYPE]->content ?? SubmitType::MAIL);
        $handler->useAutoTLS = (bool) ($emailSettings[SettingsEnum::MAIL_SERVER_TLS]->content ?? false);

        if (($emailSettings[SettingsEnum::MAIL_SERVER_TYPE]->content ?? SubmitType::MAIL) === SubmitType::SMTP) {
            $smtp          = new Smtp();
            $handler->smtp = $smtp;
        }

        if (!empty($port = $emailSettings[SettingsEnum::MAIL_SERVER_PORT_OUT]->content)) {
            $handler->port = (int) $port;
        }

        $handler->host     = $emailSettings[SettingsEnum::MAIL_SERVER_OUT]->content ?? 'localhost';
        $handler->hostname = $emailSettings[SettingsEnum::MAIL_SERVER_OUT]->content ?? '';
        $handler->username = $emailSettings[SettingsEnum::MAIL_SERVER_USER]->content ?? '';
        $handler->password = $emailSettings[SettingsEnum::MAIL_SERVER_PASS]->isEncrypted && !empty($_SERVER['OMS_PRIVATE_KEY_I'] ?? '')
            ? EncryptionHelper::decryptShared($emailSettings[SettingsEnum::MAIL_SERVER_PASS]->content ?? '', $_SERVER['OMS_PRIVATE_KEY_I'])
            : $emailSettings[SettingsEnum::MAIL_SERVER_PASS]->content ?? '';

        return $handler;
    }

    /**
     * Api method to send forgotten password email
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiForgot(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Admin\Models\Account $account */
        $account = $request->hasData('user')
            ? AccountMapper::get()->where('login', (string) $request->getData('user'))->execute()
            : AccountMapper::get()->where('email', (string) $request->getData('email'))->execute();

        /** @var \Model\Setting[] $forgotten */
        $forgotten = $this->app->appSettings->get(
            names: [SettingsEnum::LOGIN_FORGOTTEN_DATE, SettingsEnum::LOGIN_FORGOTTEN_COUNT],
            module: 'Admin',
            account: $account->id
        );

        if ((int) $forgotten[SettingsEnum::LOGIN_FORGOTTEN_COUNT]->content > 3) {
            $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
            $response->set($request->uri->__toString(), [
                'status'   => NotificationLevel::ERROR,
                'title'    => $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'PasswordResetTitle'),
            'message'      => $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'PasswordResetMsg'),
                'response' => null,
            ]);
        }

        $token     = (string) \random_bytes(64);
        $handler   = $this->setUpServerMailHandler();
        $resetLink = UriFactory::build('{/base}/reset?user=' . $account->id . '&token=' . $token);

        /** @var \Model\Setting[] $emailSettings */
        $emailSettings = $this->app->appSettings->get(
            names: [SettingsEnum::MAIL_SERVER_ADDR, SettingsEnum::LOGIN_MAIL_FORGOT_PASSWORD_TEMPLATE],
            module: 'Admin'
        );

        /** @var \Modules\Messages\Models\Email $mail */
        $mail = EmailMapper::get()
            ->with('l11n')
            ->where('id', (int) $emailSettings[SettingsEnum::LOGIN_MAIL_FORGOT_PASSWORD_TEMPLATE]->content)
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $mail->setFrom($emailSettings[SettingsEnum::MAIL_SERVER_ADDR]->content);
        $mail->addTo($account->email);

        // @todo: load default l11n if no translation is available
        $mailL11n = $mail->getL11nByLanguage($response->header->l11n->language);

        $mail->subject = $mailL11n->subject;

        // @todo: improve, the /tld link could be api.myurl.com which of course is not the url of the respective app.
        // Maybe store the uri in the $app model? or store all urls in the config file
        $mail->body = \str_replace(
            [
                '{reset_link}',
                '{user_name}',
            ],
            [
                $resetLink,
                $account->login,
            ],
            $mailL11n->body
        );

        $mail->bodyAlt = \str_replace(
            [
                '{reset_link}',
                '{user_name}',
            ],
            [
                $resetLink,
                $account->login,
            ],
            $mailL11n->bodyAlt
        );

        $handler->send($mail);

        $this->app->appSettings->set([
            [
                'name'    => SettingsEnum::LOGIN_FORGOTTEN_DATE,
                'module'  => self::NAME,
                'account' => $account->id,
                'content' => (string) \time(),
            ],
            [
                'name'    => SettingsEnum::LOGIN_FORGOTTEN_COUNT,
                'module'  => self::NAME,
                'account' => $account->id,
                'content' => (string) (((int) $forgotten[SettingsEnum::LOGIN_FORGOTTEN_COUNT]->content) + 1),
            ],
            [
                'name'    => SettingsEnum::LOGIN_FORGOTTEN_TOKEN,
                'module'  => self::NAME,
                'account' => $account->id,
                'content' => $token,
            ],
        ], true);

        /*
        if (!empty($emailSettings[SettingsEnum::MAIL_SERVER_CERT]->content)
            && !empty($emailSettings[SettingsEnum::MAIL_SERVER_KEY]->content)
        ) {
            $mail->sign(
                $emailSettings[SettingsEnum::MAIL_SERVER_CERT]->content,
                $emailSettings[SettingsEnum::MAIL_SERVER_KEY]->content,
                $emailSettings[SettingsEnum::MAIL_SERVER_KEYPASS]->content
            );
        }
        */

        // $handler->send($mail);

        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
        $response->set($request->uri->__toString(), [
            'status'   => NotificationLevel::OK,
            'title'    => $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'PasswordResetTitle'),
            'message'  => $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'PasswordResetEmailMsg'),
            'response' => null,
        ]);
    }

    /**
     * Api method to reset the password
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResetPassword(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Model\Setting[] $forgotten */
        $forgotten = $this->app->appSettings->get(
            names: [SettingsEnum::LOGIN_FORGOTTEN_DATE, SettingsEnum::LOGIN_FORGOTTEN_TOKEN],
            module: self::NAME,
            account: (int) $request->getData('user')
        );

        $date  = new \DateTime($forgotten[SettingsEnum::LOGIN_FORGOTTEN_DATE]->content);
        $token = $forgotten[SettingsEnum::LOGIN_FORGOTTEN_TOKEN]->content;

        if ($date->getTimestamp() < \time() - 60 * 10
            || !$request->hasData('token')
            || $request->getData('token') !== $token
        ) {
            $response->header->status = RequestStatusCode::R_405;
            $response->set($request->uri->__toString(), [
                'status'   => NotificationLevel::OK,
                'title'    => $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'PasswordResetTitle'),
                'message'  => $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'PasswordResetInvalidMsg'),
                'response' => null,
            ]);

            return;
        }

        /** @var \Modules\Admin\Models\Account $account */
        $account = AccountMapper::get()->where('id', (int) $request->getData('user'))->execute();

        $account->generatePassword($pass = StringRng::generateString(10, 14, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_-+=/\\{}<>?'));

        AccountMapper::update()->execute($account);

        $handler = $this->setUpServerMailHandler();

        /** @var \Model\Setting[] $emailSettings */
        $emailSettings = $this->app->appSettings->get(
            names: [SettingsEnum::MAIL_SERVER_ADDR, SettingsEnum::LOGIN_MAIL_FORGOT_PASSWORD_TEMPLATE],
            module: 'Admin'
        );

        /** @var \Modules\Messages\Models\Email $mail */
        $mail = EmailMapper::get()
            ->with('l11n')
            ->where('id', (int) $emailSettings[SettingsEnum::LOGIN_MAIL_FORGOT_PASSWORD_TEMPLATE]->content)
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $mail->setFrom($emailSettings[SettingsEnum::MAIL_SERVER_ADDR]->content);
        $mail->addTo($account->email);

        // @todo: load default l11n if no translation is available
        $mailL11n = $mail->getL11nByLanguage($response->header->l11n->language);

        $mail->subject = $mailL11n->subject;

        // @todo: improve, the /tld link could be api.myurl.com which of course is not the url of the respective app.
        // Maybe store the uri in the $app model? or store all urls in the config file
        $mail->body = \str_replace(
            [
                '{new_password}',
                '{user_name}',
            ],
            [
                $pass,
                $account->login,
            ],
            $mailL11n->body
        );

        $mail->bodyAlt = \str_replace(
            [
                '{new_password}',
                '{user_name}',
            ],
            [
                $pass,
                $account->login,
            ],
            $mailL11n->bodyAlt
        );

        $handler->send($mail);

        $this->app->appSettings->set([
            [
                'name'    => SettingsEnum::LOGIN_FORGOTTEN_COUNT,
                'module'  => self::NAME,
                'account' => $account->id,
                'content' => '0',
            ],
            [
                'name'    => SettingsEnum::LOGIN_FORGOTTEN_TOKEN,
                'module'  => self::NAME,
                'account' => $account->id,
                'content' => '',
            ],
        ], true);

        if (!empty($emailSettings[SettingsEnum::MAIL_SERVER_CERT]->content)
            && !empty($emailSettings[SettingsEnum::MAIL_SERVER_KEY]->content)
        ) {
            $mail->sign(
                $emailSettings[SettingsEnum::MAIL_SERVER_CERT]->content,
                $emailSettings[SettingsEnum::MAIL_SERVER_KEY]->content,
                $emailSettings[SettingsEnum::MAIL_SERVER_KEYPASS]->content
            );
        }

        $handler->send($mail);

        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
        $response->set($request->uri->__toString(), [
            'status'   => NotificationLevel::OK,
            'title'    => 'Password Reset',
            'message'  => 'You received a new password.',
            'response' => null,
        ]);
    }

    /**
     * Api method to get settings
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSettingsGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $response->set(
            $request->uri->__toString(),
            [
                'response' => $this->app->appSettings->get(
                    $request->getDataInt('id'),
                    $request->getDataString('name'),
                    $request->getDataInt('unit'),
                    $request->getDataInt('app'),
                    $request->getDataString('module'),
                    $request->getDataInt('group'),
                    $request->getDataInt('account')
                ),
            ]
        );
    }

    /**
     * Set app config
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAppConfigSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $dataSettings = $request->getDataJson('settings');

        $config = include __DIR__ . '/../../../config.php';

        foreach ($dataSettings as $data) {
            $config = ArrayUtils::setArray($data['path'], $config, $data['value'], '/', true);
        }

        \file_put_contents(__DIR__ . '/../../../config.php', "<?php\ndeclare(strict_types=1);\nreturn " . ArrayParser::serializeArray($config) . ';');

        $this->createStandardUpdateResponse($request, $response, $dataSettings);
    }

    /**
     * Api method for modifying settings
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSettingsSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $dataSettings = $request->getDataJson('settings');

        foreach ($dataSettings as $data) {
            $id        = isset($data['id']) && !empty($data['id']) ? (int) $data['id'] : null;
            $name      = $data['name'] ?? null;
            $content   = $data['content'] ?? null;
            $unit      = $data['unit'] ?? null;
            $app       = $data['app'] ?? null;
            $module    = $data['module'] ?? null;
            $pattern   = $data['pattern'] ?? '';
            $encrypted = $data['encrypted'] ?? null;
            $group     = isset($data['group']) ? (int) $data['group'] : null;
            $account   = isset($data['account']) ? (int) $data['account'] : null;

            /** @var \Model\Setting $old */
            $old = $this->app->appSettings->get($id, $name, $unit, $app, $module, $group, $account);
            if ($old->id === 0) {
                $internalResponse = new HttpResponse();
                $internalRequest  = new HttpRequest($request->uri);

                $internalRequest->header->account = $request->header->account;
                $internalRequest->setData('id', $id);
                $internalRequest->setData('name', $name);
                $internalRequest->setData('content', $content);
                $internalRequest->setData('pattern', $pattern);
                $internalRequest->setData('unit', $unit);
                $internalRequest->setData('app', $app);
                $internalRequest->setData('module', $module);
                $internalRequest->setData('group', $group);
                $internalRequest->setData('account', $account);
                $internalRequest->setData('encrypted', $encrypted);

                $this->apiSettingsCreate($internalRequest, $internalResponse, $data);

                continue;
            }

            $new = clone $old;

            $new->name        = $name ?? $new->name;
            $new->isEncrypted = $encrypted ?? $new->isEncrypted;
            $new->content     = $new->isEncrypted && !empty($content) && !empty($_SERVER['OMS_PRIVATE_KEY_I'] ?? '')
                ? EncryptionHelper::encryptShared($content, $_SERVER['OMS_PRIVATE_KEY_I'])
                : $content ?? $new->content;
            $new->unit        = $unit ?? $new->unit;
            $new->app         = $app ?? $new->app;
            $new->module      = $module ?? $new->module;
            $new->group       = $group ?? $new->group;
            $new->account     = $account ?? $new->account;

            // @todo: this function call seems stupid, it should just pass the $new object.
            $this->app->appSettings->set([
                [
                    'id'          => $new->id,
                    'name'        => $new->name,
                    'content'     => $new->content,
                    'unit'        => $new->unit,
                    'app'         => $new->app,
                    'module'      => $new->module,
                    'group'       => $new->group,
                    'account'     => $new->account,
                    'isEncrypted' => $new->isEncrypted,
                ],
            ], false);

            $this->updateModel($request->header->account, $old, $new, SettingMapper::class, 'settings', $request->getOrigin());
        }

        $this->createStandardUpdateResponse($request, $response, $dataSettings);
    }

    /**
     * Api method for modifying settings
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSettingsCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSettingsCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $setting = $this->createSettingFromRequest($request);
        $this->createModel($request->header->account, $setting, SettingMapper::class, 'setting', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $setting);
    }

    /**
     * Validate password update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateSettingsCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = !$request->hasData('name'))) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create group from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Setting
     *
     * @since 1.0.0
     */
    private function createSettingFromRequest(RequestAbstract $request) : Setting
    {
        $setting = new Setting(
            id: $request->getDataInt('id') ?? 0,
            name: $request->getDataString('name') ?? '',
            content: $request->getDataString('content') ?? '',
            pattern: $request->getDataString('pattern') ?? '',
            unit: $request->getDataInt('unit'),
            app: $request->getDataInt('app'),
            module: $request->getDataString('module'),
            group: $request->getDataInt('group'),
            account: $request->getDataInt('account'),
            isEncrypted: $request->getDataBool('encrypted') ?? false
        );

        return $setting;
    }

    /**
     * Api method for modifying account password
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSettingsAccountPasswordSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        // has required data
        if (!empty($val = $this->validatePasswordUpdate($request))) {
            $response->data['password_update'] = new FormValidation($val);
            $response->header->status          = RequestStatusCode::R_400;

            return;
        }

        $requestAccount = $request->header->account;

        // request account is valid
        if ($requestAccount <= 0) {
            $this->fillJsonResponse($request, $response, NotificationLevel::HIDDEN, '', '', []);
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        /** @var Account $account */
        $account = AccountMapper::get()
            ->where('id', $requestAccount)
            ->execute();

        // test old password is correct
        if ($account->login === null
            || AccountMapper::login($account->login, (string) $request->getData('oldpass')) !== $requestAccount
        ) {
            $this->fillJsonResponse($request, $response, NotificationLevel::HIDDEN, '', '', []);
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        // test password repetition
        if (((string) $request->getData('newpass')) !== ((string) $request->getData('reppass'))) {
            $this->fillJsonResponse($request, $response, NotificationLevel::HIDDEN, '', '', []);
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        // test password complexity
        /** @var \Model\Setting $complexity */
        $complexity = $this->app->appSettings->get(names: SettingsEnum::PASSWORD_PATTERN, module: 'Admin');
        if (\preg_match($complexity->content, (string) $request->getData('newpass')) !== 1) {
            $this->fillJsonResponse($request, $response, NotificationLevel::HIDDEN, '', '', []);
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        $account->generatePassword((string) $request->getData('newpass'));

        AccountMapper::update()->execute($account);

        $this->createStandardUpdateResponse($request, $response, $account);
    }

    /**
     * Validate password update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validatePasswordUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['oldpass'] = !$request->hasData('oldpass'))
            || ($val['newpass'] = !$request->hasData('newpass'))
            || ($val['reppass'] = !$request->hasData('reppass'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method for modifying account localization
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSettingsAccountLocalizationSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $requestAccount = $request->header->account;
        $accountId      = (int) $request->getData('account_id');

        if ($requestAccount !== $accountId
            && !$this->app->accountManager->get($accountId)->hasPermission(
                PermissionType::MODIFY,
                $this->app->unitId,
                $this->app->appId,
                self::NAME,
                PermissionCategory::ACCOUNT_SETTINGS,
                $accountId
            )
        ) {
            $this->fillJsonResponse($request, $response, NotificationLevel::HIDDEN, '', '', []);
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        /** @var \Modules\Admin\Models\Account $account */
        $account = AccountMapper::get()
            ->with('l11n')
            ->where('id', $accountId)
            ->execute();

        if (($request->getDataString('localization_load') ?? '-1') !== '-1') {
            $locale = \explode('_', $request->getDataString('localization_load') ?? '');
            $old    = clone $account->l11n;

            $account->l11n->loadFromLanguage($locale[0], $locale[1]);

            $this->updateModel($request->header->account, $old, $account->l11n, LocalizationMapper::class, 'l11n', $request->getOrigin());
            $this->createStandardUpdateResponse($request, $response, $account->l11n);

            return;
        }

        $old = clone $account->l11n;

        $dataSettings = $request->getLike('settings_(.*)');

        $account->l11n->setCountry($dataSettings['settings_country']);
        $account->l11n->setLanguage($dataSettings['settings_language']);
        $account->l11n->setTemperature($dataSettings['settings_temperature']);

        $account->l11n->setTimezone($dataSettings['settings_timezone']);
        $account->l11n->setDatetime(
            [
                'very_short' => $dataSettings['settings_timeformat_vs'],
                'short'      => $dataSettings['settings_timeformat_s'],
                'medium'     => $dataSettings['settings_timeformat_m'],
                'long'       => $dataSettings['settings_timeformat_l'],
                'very_long'  => $dataSettings['settings_timeformat_vl'],
            ]
        );

        $account->l11n->setCurrency($dataSettings['settings_currency']);
        $account->l11n->setCurrencyFormat($dataSettings['settings_currencyformat']);

        $account->l11n->setDecimal($dataSettings['settings_decimal']);
        $account->l11n->setThousands($dataSettings['settings_thousands']);

        $account->l11n->setPrecision(
            [
                'very_short' => $dataSettings['settings_precision_vs'],
                'short'      => $dataSettings['settings_precision_s'],
                'medium'     => $dataSettings['settings_precision_m'],
                'long'       => $dataSettings['settings_precision_l'],
                'very_long'  => $dataSettings['settings_precision_vl'],
            ]
        );

        $account->l11n->setWeight(
            [
                'very_light' => $dataSettings['settings_weight_vl'],
                'light'      => $dataSettings['settings_weight_l'],
                'medium'     => $dataSettings['settings_weight_m'],
                'heavy'      => $dataSettings['settings_weight_h'],
                'very_heavy' => $dataSettings['settings_weight_vh'],
            ]
        );

        $account->l11n->setSpeed(
            [
                'very_slow' => $dataSettings['settings_speed_vs'],
                'slow'      => $dataSettings['settings_speed_s'],
                'medium'    => $dataSettings['settings_speed_m'],
                'fast'      => $dataSettings['settings_speed_f'],
                'very_fast' => $dataSettings['settings_speed_vf'],
                'sea'       => $dataSettings['settings_speed_sea'],
            ]
        );

        $account->l11n->setLength(
            [
                'very_short' => $dataSettings['settings_length_vs'],
                'short'      => $dataSettings['settings_length_s'],
                'medium'     => $dataSettings['settings_length_m'],
                'long'       => $dataSettings['settings_length_l'],
                'very_long'  => $dataSettings['settings_length_vl'],
                'sea'        => $dataSettings['settings_length_sea'],
            ]
        );

        $account->l11n->setArea(
            [
                'very_small' => $dataSettings['settings_area_vs'],
                'small'      => $dataSettings['settings_area_s'],
                'medium'     => $dataSettings['settings_area_m'],
                'large'      => $dataSettings['settings_area_l'],
                'very_large' => $dataSettings['settings_area_vl'],
            ]
        );

        $account->l11n->setVolume(
            [
                'very_small' => $dataSettings['settings_volume_vs'],
                'small'      => $dataSettings['settings_volume_s'],
                'medium'     => $dataSettings['settings_volume_m'],
                'large'      => $dataSettings['settings_volume_l'],
                'very_large' => $dataSettings['settings_volume_vl'],
                'tablespoon' => $dataSettings['settings_volume_tablespoon'],
                'teaspoon'   => $dataSettings['settings_volume_teaspoon'],
                'glass'      => $dataSettings['settings_volume_glass'],
            ]
        );

        $this->updateModel($request->header->account, $old, $account->l11n, LocalizationMapper::class, 'l11n', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $account->l11n);
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSettingsDesignSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $uploadedFiles = $request->files;

        if (!empty($uploadedFiles)) {
            $upload                   = new UploadFile();
            $upload->preserveFileName = false;
            $upload->outputDir        = __DIR__ . '/../../../Web/Backend/img';

            $status = $upload->upload($uploadedFiles, ['logo.png'], true);
        }

        $this->createStandardUpdateResponse($request, $response, []);
    }

    /**
     * Api method to install a application
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateApplicationCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $app = $this->createApplicationFromRequest($request);
        $this->createModel($request->header->account, $app, AppMapper::class, 'application', $request->getOrigin());

        $this->createDefaultAppSettings($app, $request);

        /** @var \Model\Setting $setting */
        $setting = $this->app->appSettings->get(null, SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_APP);
        if ($setting->content === '1') {
            $newRequest                  = new HttpRequest();
            $newRequest->header->account = $request->header->account;
            $newRequest->setData('name', 'app:' . \strtolower($app->name));
            $newRequest->setData('status', GroupStatus::ACTIVE);
            $this->apiGroupCreate($newRequest, $response, $data);
        }

        $this->createStandardCreateResponse($request, $response, $app);
    }

    /**
     * Create default settings for new app
     *
     * @param App             $app     Application to create new settings for
     * @param RequestAbstract $request Request used to create the app
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function createDefaultAppSettings(App $app, RequestAbstract $request) : void
    {
        $settings   = [];
        $settings[] = new Setting(0, SettingsEnum::REGISTRATION_ALLOWED, '0', '\\d+', app: $app->id, module: 'Admin');
        $settings[] = new Setting(0, SettingsEnum::APP_DEFAULT_GROUPS, '[]', app: $app->id, module: 'Admin');

        foreach ($settings as $setting) {
            $this->createModel($request->header->account, $setting, SettingMapper::class, 'setting', $request->getOrigin());
        }
    }

    /**
     * Validate app create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateApplicationCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = !$request->hasData('name'))) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create task from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return App Returns the created application from the request
     *
     * @since 1.0.0
     */
    private function createApplicationFromRequest(RequestAbstract $request) : App
    {
        $app              = new App();
        $app->name        = $request->getDataString('name') ?? '';
        $app->type        = $request->getDataInt('type') ?? ApplicationType::WEB;
        $app->defaultUnit = $request->getDataInt('default_unit');

        return $app;
    }

    /**
     * Api method to install a application
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiInstallApplication(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $appManager = new ApplicationManager($this->app);

        $app = \rtrim($request->getDataString('appSrc') ?? '', '/\\ ');
        if (!\is_dir(__DIR__ . '/../../../' . $app)) {
            $response->header->status = RequestStatusCode::R_400;
            return;
        }

        $appInfo = new ApplicationInfo(__DIR__ . '/../../../' . $app . '/info.json');
        $appInfo->load();

        // handle dependencies
        $dependencies = $appInfo->getDependencies();
        $installed    = $this->app->moduleManager->getInstalledModules();

        foreach ($dependencies as $key => $version) {
            if (!isset($installed[$key])) {
                $this->app->moduleManager->install($key);
            }
        }

        // handle app installation
        $result = $appManager->install(
            __DIR__ . '/../../../' . $app,
            __DIR__ . '/../../../' . ($request->getDataString('appDest') ?? ''),
            $request->getDataString('theme') ?? 'Default'
        );

        // handle providing
        if ($result) {
            $providing = $appInfo->getProviding();

            foreach ($providing as $key => $version) {
                if (isset($installed[$key])) {
                    $this->app->moduleManager->installProviding($app, $key);
                }
            }
        }

        // handle Routes of already installed modules
        foreach ($installed as $module => $data) {
            $class = '\Modules\\' . $module . '\Admin\Status';

            $moduleInfo = new ModuleInfo(__DIR__ . '/../../../Modules/' . $module . '/info.json');
            $moduleInfo->load();

            $class::activateRoutes($moduleInfo, $appInfo);
            $class::activateHooks($moduleInfo, $appInfo);
        }
    }

    /**
     * Api method to get a group
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiGroupGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Admin\Models\Group $group */
        $group = GroupMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->createStandardReturnResponse($request, $response, $group);
    }

    /**
     * Api method for modifying a group
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiGroupUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Admin\Models\Group $old */
        $old = GroupMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateGroupFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, GroupMapper::class, 'group', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update group from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Group
     *
     * @since 1.0.0
     */
    private function updateGroupFromRequest(RequestAbstract $request, Group $group) : Group
    {
        $group->name = $request->getDataString('name') ?? $group->name;
        $group->setStatus($request->getDataInt('status') ?? $group->getStatus());
        $group->description    = Markdown::parse($request->getDataString('description') ?? $group->descriptionRaw);
        $group->descriptionRaw = $request->getDataString('description') ?? $group->descriptionRaw;

        return $group;
    }

    /**
     * Validate group create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateGroupCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = !$request->hasData('name'))
            || ($val['status'] = !GroupStatus::isValidValue((int) $request->getData('status')))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create a group
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiGroupCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateGroupCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $group = $this->createGroupFromRequest($request);
        $this->createModel($request->header->account, $group, GroupMapper::class, 'group', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $group);
    }

    /**
     * Method to create group from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Group
     *
     * @since 1.0.0
     */
    private function createGroupFromRequest(RequestAbstract $request) : Group
    {
        $group            = new Group();
        $group->createdBy = new NullAccount($request->header->account);
        $group->name      = $request->getDataString('name') ?? '';
        $group->setStatus($request->getDataInt('status') ?? GroupStatus::INACTIVE);
        $group->description    = Markdown::parse($request->getDataString('description') ?? '');
        $group->descriptionRaw = $request->getDataString('description') ?? '';

        return $group;
    }

    /**
     * Api method to delete a group
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiGroupDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateGroupDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        if (((int) $request->getData('id')) === 3) {
            // admin group cannot be deleted
            $this->createInvalidDeleteResponse($request, $response, []);

            return;
        }

        /** @var \Modules\Admin\Models\Group $group */
        $group = GroupMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $group, GroupMapper::class, 'group', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $group);
    }

    /**
     * Validate Group delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateGroupDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to find groups
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiGroupFind(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Admin\Models\Group[] $groups */
        $groups = GroupMapper::getAll()
            ->where('name', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
            ->execute();

        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values($groups)
        );
    }

    /**
     * Api method to get an accoung
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Account $account */
        $account = AccountMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->createStandardReturnResponse($request, $response, $account);
    }

    /**
     * Api method to find accounts
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountFind(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Admin\Models\Account[] $accounts */
        $accounts =  AccountMapper::getAll()
            ->where('login', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
            ->where('email', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE', 'OR')
            ->where('name1', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE', 'OR')
            ->where('name2', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE', 'OR')
            ->where('name3', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE', 'OR')
            ->execute();

        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values($accounts)
        );
    }

    /**
     * Api method to find accounts and or groups
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountGroupFind(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Account[] $accounts */
        $accounts = AccountMapper::getAll()
            ->where('login', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
            ->where('email', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE', 'OR')
            ->where('name1', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE', 'OR')
            ->where('name2', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE', 'OR')
            ->where('name3', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE', 'OR')
            ->execute();

        $data = [];

        foreach ($accounts as $account) {
            /** @var array $temp */
            $temp                = $account->jsonSerialize();
            $temp['type_prefix'] = 'a';
            $temp['type_name']   = 'Account';

            $data[] = $temp;
        }

        /** @var Group[] $groups */
        $groups = GroupMapper::getAll()
            ->where('name', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
            ->execute();

        foreach ($groups as $group) {
            /** @var array $temp */
            $temp                = $group->jsonSerialize();
            $temp['name']        = [$temp['name']];
            $temp['email']       = '---';
            $temp['type_prefix'] = 'g';
            $temp['type_name']   = 'Group';

            $data[] = $temp;
        }

        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set($request->uri->__toString(), $data);
    }

    /**
     * Method to validate account creation from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateAccountCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name1'] = !$request->hasData('name1'))
            || ($val['type'] = !AccountType::isValidValue((int) $request->getData('type')))
            || ($val['status'] = !AccountStatus::isValidValue((int) $request->getData('status')))
            || ($val['email'] = $request->hasData('email') && !EmailValidator::isValid((string) $request->getData('email')))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create an account
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAccountCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $account = $this->createAccountFromRequest($request);
        $this->createModel($request->header->account, $account, AccountCredentialMapper::class, 'account', $request->getOrigin());

        if ($request->hasData('create_profile')) {
            $this->createProfileForAccount($account, $request);
        }

        $collection = $this->createMediaDirForAccount($account->id, $account->login ?? '', $request->header->account);
        $this->createModel($request->header->account, $collection, CollectionMapper::class, 'collection', $request->getOrigin());

        // find default groups and create them
        $defaultGroupIds = [];

        if ($request->hasData('app')) {
            /** @var \Model\Setting $defaultGroupSettings */
            $defaultGroupSettings = $this->app->appSettings->get(
                names: SettingsEnum::APP_DEFAULT_GROUPS,
                app:  (int) $request->getData('app'),
                module: 'Admin'
            );

            if (!empty($defaultGroupSettings)) {
                $temp = \json_decode($defaultGroupSettings->content, true);
                if (!\is_array($temp)) {
                    $temp = [];
                }

                $defaultGroupIds = \array_merge($defaultGroupIds, $temp);
            }
        }

        if ($request->hasData('unit')) {
            /** @var \Model\Setting $defaultGroupSettings */
            $defaultGroupSettings = $this->app->appSettings->get(
                names: SettingsEnum::UNIT_DEFAULT_GROUPS,
                unit: (int) $request->getData('unit'),
                module: 'Admin'
            );

            if (!empty($defaultGroupSettings)) {
                $temp = \json_decode($defaultGroupSettings->content, true);
                if (!\is_array($temp)) {
                    $temp = [];
                }

                $defaultGroupIds = \array_merge($defaultGroupIds, $temp);
            }
        }

        if (!empty($defaultGroupIds)) {
            $this->createModelRelation(
                $account->id,
                $account->id,
                $defaultGroupIds,
                AccountMapper::class,
                'groups',
                'account',
                $request->getOrigin()
            );
        }

        $this->fillJsonResponse(
            $request,
            $response,
            NotificationLevel::OK,
            '',
            \str_replace(
                '{url}',
                UriFactory::build('{/base}/admin/account/settings?{?}&id=' . $account->id),
                $this->app->l11nManager->getText($response->header->l11n->language, '0', '0', 'SuccessfulCreate'
            )),
            $account
        );
    }

    /**
     * Api method to register an account
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountRegister(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if ($request->header->account === 0) {
            $request->header->account = 1;
        }

        if (!empty($val = $this->validateRegistration($request))) {
            $response->header->status = RequestStatusCode::R_400;

            $this->fillJsonResponse(
                $request,
                $response,
                NotificationLevel::ERROR,
                '',
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'FormDataInvalid'),
                $val
            );

            return;
        }

        /** @var \Modules\Admin\Models\App */
        $app = AppMapper::get()
            ->where('id', (int) $request->getData('app'))
            ->execute();

        /** @var \Model\Setting $allowed */
        $allowed = $this->app->appSettings->get(
            names: SettingsEnum::REGISTRATION_ALLOWED,
            app: (int) $request->getData('app'),
            module: 'Admin'
        );

        if ($allowed->content !== '1') {
            $response->header->status = RequestStatusCode::R_400;

            $this->fillJsonResponse(
                $request,
                $response,
                NotificationLevel::ERROR,
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationTitle'),
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationNotAllowed'),
                []
            );

            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        /** @var \Model\Setting $complexity */
        $complexity = $this->app->appSettings->get(names: SettingsEnum::PASSWORD_PATTERN, module: 'Admin');
        if ($request->hasData('password')
            && \preg_match($complexity->content, (string) $request->getData('password')) !== 1
        ) {
            $response->header->status = RequestStatusCode::R_400;

            $this->fillJsonResponse(
                $request,
                $response,
                NotificationLevel::ERROR,
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationTitle'),
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationInvalidPasswordFormat'),
                []
            );

            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        // Check if account already exists
        /** @var Account $emailAccount */
        $emailAccount = AccountMapper::get()
            ->where('email', (string) $request->getData('email'))
            ->execute();

        /** @var Account $loginAccount */
        $loginAccount = AccountMapper::get()
            ->where('login', (string) ($request->getData('user') ?? $request->getData('email')))
            ->execute();

        /** @var null|Account $account */
        $account = null;

        // email already in use
        if ($emailAccount->id > 0
            && $emailAccount->login !== null
            && AccountMapper::login($emailAccount->login, (string) $request->getData('password')) !== LoginReturnType::OK
        ) {
            $response->header->status = RequestStatusCode::R_400;

            $this->fillJsonResponse(
                $request,
                $response,
                NotificationLevel::OK,
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationTitle'),
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationEmailInUse'),
                []
            );

            $response->header->status = RequestStatusCode::R_400;

            return;
        } elseif ($emailAccount->id > 0) {
            $account = $emailAccount;
        }

        // login already in use by different email
        if ($account === null
            && $loginAccount->id > 0
            && $loginAccount->getEmail() !== $request->getData('email')
        ) {
            $response->header->status = RequestStatusCode::R_400;

            $this->fillJsonResponse(
                $request,
                $response,
                NotificationLevel::ERROR,
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationTitle'),
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationLoginInUse'),
                []
            );

            $response->header->status = RequestStatusCode::R_400;

            return;
        } elseif ($account === null
            && $loginAccount->id > 0
            && $loginAccount->login !== null
            && AccountMapper::login($loginAccount->login, (string) $request->getData('password')) !== LoginReturnType::OK
        ) {
            $account = $loginAccount;
        }

        // Already registered
        if ($account !== null) {
            /** @var Account $account */
            $account = AccountMapper::get()
                ->with('groups')
                ->where('id', $account->id)
                ->execute();

            $defaultGroupIds = [];

            if ($request->hasData('app')) {
                /** @var \Model\Setting $defaultGroupSettings */
                $defaultGroupSettings = $this->app->appSettings->get(
                    names: SettingsEnum::APP_DEFAULT_GROUPS,
                    app:  (int) $request->getDataInt('app'),
                    module: 'Admin'
                );

                if (!empty($defaultGroupSettings)) {
                    $temp = \json_decode($defaultGroupSettings->content, true);
                    if (!\is_array($temp)) {
                        $temp = [];
                    }

                    $defaultGroupIds = \array_merge(
                        $defaultGroupIds,
                        $temp
                    );
                }
            }

            if ($request->hasData('unit')) {
                /** @var \Model\Setting $defaultGroupSettings */
                $defaultGroupSettings = $this->app->appSettings->get(
                    names: SettingsEnum::UNIT_DEFAULT_GROUPS,
                    app:  (int) $request->getDataInt('unit'),
                    module: 'Admin'
                );

                if (!empty($defaultGroupSettings)) {
                    $temp = \json_decode($defaultGroupSettings->content, true);
                    if (!\is_array($temp)) {
                        $temp = [];
                    }

                    $defaultGroupIds = \array_merge(
                        $defaultGroupIds,
                        $temp
                    );
                }
            }

            foreach ($defaultGroupIds as $index => $id) {
                if ($account->hasGroup($id)) {
                    unset($defaultGroupIds[$index]);
                }
            }

            if (empty($defaultGroupIds)
                && $account->getStatus() === AccountStatus::INACTIVE
            ) {
                $response->header->status = RequestStatusCode::R_400;

                // Account not active
                $this->fillJsonResponse(
                    $request,
                    $response,
                    NotificationLevel::ERROR,
                    $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationTitle'),
                    $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationNotActivated'),
                    []
                );

                $response->header->status = RequestStatusCode::R_403;

                return;
            }

            if (!empty($defaultGroupIds)) {
                // Create missing account / group relationships
                $this->createModelRelation(
                    $account->id,
                    $account->id,
                    $defaultGroupIds,
                    AccountMapper::class,
                    'groups',
                    'registration',
                    $request->getOrigin()
                );
            }
        } else {
            // New account
            $request->setData('status', AccountStatus::INACTIVE, true);
            $request->setData('type', AccountType::USER, true);
            $request->setData('create_profile', (string) true);
            $request->setData('name1', !$request->hasData('name1')
                ? (!$request->hasData('user')
                    ? \explode('@', $request->getDataString('email') ?? '')[0]
                    : $request->getDataString('user')
                )
                : $request->getDataString('name1')
                , true);

            $request->setData('user', !$request->hasData('user')
                ? $request->getDataString('email')
                : $request->getDataString('user')
                , true);

            $this->apiAccountCreate($request, $response, $data);

            /** @var Account $account */
            $account = $response->get($request->uri->__toString())['response'];

            // Create confirmation pending entry
            $dataChange            = new DataChange();
            $dataChange->type      = 'account';
            $dataChange->createdBy = $account->id;

            $dataChange->data = \json_encode([
                'status' => AccountStatus::ACTIVE,
            ]);

            $tries = 0;
            do {
                $dataChange->reHash();
                $this->createModel($account->id, $dataChange, DataChangeMapper::class, 'datachange', $request->getOrigin());

                ++$tries;
            } while ($dataChange->id === 0 && $tries < 5);

            $handler = $this->setUpServerMailHandler();

            /** @var \Model\Setting[] $emailSettings */
            $emailSettings = $this->app->appSettings->get(
                names: [SettingsEnum::MAIL_SERVER_ADDR, SettingsEnum::LOGIN_MAIL_REGISTRATION_TEMPLATE],
                module: 'Admin'
            );

            /** @var \Modules\Messages\Models\Email $mail */
            $mail = EmailMapper::get()
                ->with('l11n')
                ->where('id', (int) $emailSettings[SettingsEnum::LOGIN_MAIL_REGISTRATION_TEMPLATE]->content)
                ->where('l11n/language', $response->header->l11n->language)
                ->execute();

            $mail->setFrom($emailSettings[SettingsEnum::MAIL_SERVER_ADDR]->content);
            $mail->addTo((string) $request->getData('email'));

            // @todo: load default l11n if no translation is available
            $mailL11n = $mail->getL11nByLanguage($response->header->l11n->language);

            $mail->subject = $mailL11n->subject;

            // @todo: improve, the /tld link could be api.myurl.com which of course is not the url of the respective app.
            // Maybe store the uri in the $app model? or store all urls in the config file
            $mail->body = \str_replace(
                [
                    '{confirmation_link}',
                    '{user_name}',
                ],
                [
                    UriFactory::hasQuery('/' . \strtolower($app->name))
                        ? UriFactory::build('{/' . \strtolower($app->name) . '}/' . \strtolower($app->name) . '/signup/confirmation?hash=' . $dataChange->getHash())
                        : UriFactory::build('{/tld}/{/lang}/' . \strtolower($app->name) . '/signup/confirmation?hash=' . $dataChange->getHash()),
                    $account->login,
                ],
                $mailL11n->body
            );

            $mail->bodyAlt = \str_replace(
                [
                    '{confirmation_link}',
                    '{user_name}',
                ],
                [
                    UriFactory::hasQuery('/' . \strtolower($app->name))
                        ? UriFactory::build('{/' . \strtolower($app->name) . '}/' . \strtolower($app->name) . '/signup/confirmation?hash=' . $dataChange->getHash())
                        : UriFactory::build('{/tld}/{/lang}/' . \strtolower($app->name) . '/signup/confirmation?hash=' . $dataChange->getHash()),
                    $account->login,
                ],
                $mailL11n->bodyAlt
            );

            $handler->send($mail);
        }

        // Create client
        if ($request->hasData('client')) {
            $client = $this->app->moduleManager->get('ClientManagement', 'Api')
                ->findClientForAccount($account->id, $request->getDataInt('unit'));

            if ($client === null) {
                $internalRequest  = new HttpRequest();
                $internalResponse = new HttpResponse();

                $internalRequest->header->account = $account->id;
                $internalRequest->setData('account', $account->id);
                $internalRequest->setData('number', 100000 + $account->id);
                $internalRequest->setData('address', $request->getDataString('address') ?? '');
                $internalRequest->setData('postal', $request->getDataString('postal') ?? '');
                $internalRequest->setData('city', $request->getDataString('city') ?? '');
                $internalRequest->setData('country', $request->getDataString('country'));
                $internalRequest->setData('state', $request->getDataString('state') ?? '');
                $internalRequest->setData('vat_id', $request->getDataString('vat_id') ?? '');
                $internalRequest->setData('unit', $request->getDataInt('unit'));

                $this->app->moduleManager->get('ClientManagement', 'Api')->apiClientCreate($internalRequest, $internalResponse);
            }
        }

        $this->fillJsonResponse(
            $request,
            $response,
            NotificationLevel::OK,
            $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationTitle'),
            $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'RegistrationSuccessful'),
            $account
        );
    }

    /**
     * Method to validate account registration from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateRegistration(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['email'] = $request->hasData('email')
                && !EmailValidator::isValid((string) $request->getData('email')))
            || ($val['unit'] = !$request->hasData('unit'))
            || ($val['app'] = !$request->hasData('app'))
            || ($val['password'] = !$request->hasData('password'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to perform a pending model change
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     * @todo: maybe move to job/workflow??? This feels very much like a job/event especially if we make the 'type' an event-trigger
     *
     * @since 1.0.0
     */
    public function apiDataChange(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateDataChange($request))) {
            $response->data['data_change'] = new FormValidation($val);
            $response->header->status      = RequestStatusCode::R_400;

            return;
        }

        /** @var DataChange $dataChange */
        $dataChange = DataChangeMapper::get()->where('hash', (string) $request->getData('hash'))->execute();
        if ($dataChange->id === 0) {
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        switch ($dataChange->type) {
            case 'account':
                /** @var Account $old */
                $old = AccountMapper::get()->where('id', $dataChange->createdBy)->execute();
                $new = clone $old;

                $data = \json_decode($dataChange->data, true);
                if (!\is_array($data)) {
                    break;
                }

                $new->setStatus((int) ($data['status'] ?? -1));

                $this->updateModel($dataChange->createdBy, $old, $new, AccountMapper::class, 'datachange', $request->getOrigin());
                $this->deleteModel($dataChange->createdBy, $dataChange, DataChangeMapper::class, 'datachange', $request->getOrigin());

                break;
        }
    }

    /**
     * Method to validate account registration from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateDataChange(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['hash'] = !$request->hasData('hash'))) {
            return $val;
        }

        return [];
    }

    /**
     * Create directory for an account
     *
     * @param int    $id        Account id
     * @param string $name      Name of the directory/account
     * @param int    $createdBy Creator of the directory
     *
     * @return Collection
     *
     * @since 1.0.0
     */
    private function createMediaDirForAccount(int $id, string $name, int $createdBy) : Collection
    {
        $collection       = new Collection();
        $collection->name = ((string) $id) . ' ' . $name;
        $collection->setVirtualPath('/Accounts');
        $collection->setPath('/Modules/Media/Files/Accounts/' . ((string) $id));
        $collection->createdBy = new NullAccount($createdBy);

        return $collection;
    }

    /**
     * Create profile for account
     *
     * @param Account         $account Account to create profile for
     * @param RequestAbstract $request Request
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    private function createProfileForAccount(Account $account, RequestAbstract $request) : void
    {
        if (($request->getDataString('password') ?? '') === ''
            || ($request->getDataString('user') ?? '') === ''
        ) {
            return;
        }

        $this->app->moduleManager->get('Profile')->apiProfileCreateDbEntry(
            new \Modules\Profile\Models\Profile($account),
            $request
        );
    }

    /**
     * Method to create an account from a request
     *
     * @param RequestAbstract $request Request
     *
     * @return Account
     *
     * @since 1.0.0
     */
    private function createAccountFromRequest(RequestAbstract $request) : Account
    {
        $account        = new Account();
        $account->login = $request->getDataString('user') ?? '';
        $account->name1 = $request->getDataString('name1') ?? '';
        $account->name2 = $request->getDataString('name2') ?? '';
        $account->name3 = $request->getDataString('name3') ?? '';
        $account->setStatus($request->getDataInt('status') ?? AccountStatus::INACTIVE);
        $account->setType($request->getDataInt('type') ?? AccountType::USER);
        $account->setEmail($request->getDataString('email') ?? '');
        $account->generatePassword($request->getDataString('password') ?? '');

        if (!$request->hasData('locale')) {
            $account->l11n = Localization::fromJson(
                    $this->app->l11nServer === null
                        ? $request->header->l11n->jsonSerialize()
                        : $this->app->l11nServer->jsonSerialize()
                );
        } else {
            $locale = \explode('_', $request->getdataString('locale') ?? '');

            $account->l11n
                ->loadFromLanguage(
                    $locale[0] ?? $this->app->l11nServer->language,
                    $locale[1] ?? $this->app->l11nServer->country
                );
        }

        return $account;
    }

    /**
     * Api method to delete an account
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Account $account */
        $account = AccountMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $account, AccountMapper::class, 'account', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $account);
    }

    /**
     * Api method to update an account
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Account $old */
        $old = AccountMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $new = $this->updateAccountFromRequest($request, clone $old);
        $this->updateModel($request->header->account, $old, $new, AccountMapper::class, 'account', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update an account from a request
     *
     * @param RequestAbstract $request       Request
     * @param Account         $account       Account
     * @param bool            $allowPassword Allow to change password
     *
     * @return Account
     *
     * @since 1.0.0
     */
    private function updateAccountFromRequest(RequestAbstract $request, Account $account, bool $allowPassword = false) : Account
    {
        $account->login = $request->getDataString('user') ?? $account->login;
        $account->name1 = $request->getDataString('name1') ?? $account->name1;
        $account->name2 = $request->getDataString('name2') ?? $account->name2;
        $account->name3 = $request->getDataString('name3') ?? $account->name3;
        $account->setEmail($request->getDataString('email') ?? $account->getEmail());
        $account->setStatus($request->getDataInt('status') ?? $account->getStatus());
        $account->setType($request->getDataInt('type') ?? $account->getType());

        if ($allowPassword && $request->hasData('password')) {
            $account->generatePassword((string) $request->getData('password'));
        }

        return $account;
    }

    /**
     * Api method to update the module settigns
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiModuleStatusUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $module = $request->getDataString('module') ?? '';
        $status = (int) $request->getData('status');

        if (empty($module) || empty($status)) {
            $this->fillJsonResponse(
                $request,
                $response,
                NotificationLevel::WARNING,
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleStatusTitle'),
                $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'UnknownModuleOrStatusChange'),
                []
            );

            $response->header->status = RequestStatusCode::R_403;
            return;
        }

        /** @var \Modules\Admin\Models\Module $old */
        $old = ModuleMapper::get()->where('id', $module)->execute();

        $this->app->eventManager->triggerSimilar(
            'PRE:Module:Admin-module-status-update', '',
            [
                $request->header->account,
                ['status' => $status, 'module' => $module],
            ]
        );
        switch ($status) {
            case ModuleStatusUpdateType::ACTIVATE:
                $done = $module === 'Admin' ? false : $this->app->moduleManager->activate($module);
                $msg  = $done
                    ? $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleActivatedSuccessful')
                    : $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleActivatedFailure');

                $new = clone $old;
                $new->setStatus(ModuleStatusUpdateType::ACTIVATE);
                ModuleMapper::update()->execute($new);

                break;
            case ModuleStatusUpdateType::DEACTIVATE:
                $done = $module === 'Admin' ? false : $this->app->moduleManager->deactivate($module);
                $msg  = $done
                    ? $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleDeactivatedSuccessful')
                    : $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleDeactivatedFailure');

                $new = clone $old;
                $new->setStatus(ModuleStatusUpdateType::DEACTIVATE);
                ModuleMapper::update()->execute($new);

                break;
            case ModuleStatusUpdateType::INSTALL:
                $done = $this->app->moduleManager->isInstalled($module);
                $msg  = $done
                    ? $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleInstalledSuccessful')
                    : $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleInstalledFailure');

                if ($done) {
                    break;
                }

                if (!\is_file(__DIR__ . '/../../../Modules/' . $module . '/info.json')) {
                    $msg  = $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'UnknownModuleChange');
                    $done = false;
                    break;
                }

                $moduleInfo = new ModuleInfo(__DIR__ . '/../../../Modules/' . $module . '/info.json');
                $moduleInfo->load();

                // install dependencies
                $dependencies = $moduleInfo->getDependencies();
                foreach ($dependencies as $key => $_) {
                    $iResponse                 = new HttpResponse();
                    $iRequest                  = new HttpRequest(new HttpUri(''));
                    $iRequest->header->account = 1;
                    $iRequest->setData('status', ModuleStatusUpdateType::INSTALL);
                    $iRequest->setData('module', $key);

                    $this->apiModuleStatusUpdate($iRequest, $iResponse);
                }

                // install module
                $moduleObj          = new Module();
                $moduleObj->id      = $module;
                $moduleObj->theme   = 'Default';
                $moduleObj->path    = $moduleInfo->getDirectory();
                $moduleObj->version = $moduleInfo->getVersion();
                $moduleObj->name    = $moduleInfo->getExternalName();

                $moduleObj->setStatus(ModuleStatus::AVAILABLE);

                $this->createModel($request->header->account, $moduleObj, ModuleMapper::class, 'module', $request->getOrigin());

                $done = $this->app->moduleManager->install($module);
                $msg  = $done
                    ? $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleInstalledSuccessful')
                    : $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleInstalledFailure');

                $old = clone $moduleObj;
                $moduleObj->setStatus(ModuleStatus::ACTIVE);

                $this->updateModel($request->header->account, $old, $moduleObj, ModuleMapper::class, 'module', $request->getOrigin());

                $queryLoad = new Builder($this->app->dbPool->get('insert'));
                $queryLoad->insert('module_load_pid', 'module_load_type', 'module_load_from', 'module_load_for', 'module_load_file')
                    ->into('module_load');

                $load = $moduleInfo->getLoad();
                foreach ($load as $val) {
                    foreach ($val['pid'] as $pid) {
                        $queryLoad->values(
                            \sha1(\str_replace('/', '', $pid)),
                            (int) $val['type'],
                            $val['from'],
                            $val['for'],
                            $val['file']
                        );
                    }
                }

                if (!empty($queryLoad->getValues())) {
                    $queryLoad->execute();
                }

                // install receiving from application (receiving from module is already installed during the module installation)
                $appManager = new ApplicationManager($this->app);
                $receiving  = $appManager->getProvidingForModule($module);
                foreach ($receiving as $app => $modules) {
                    foreach ($modules as $module) {
                        $this->app->moduleManager->installProviding('/Web/' . $app, $module);
                    }
                }

                break;
            case ModuleStatusUpdateType::UNINSTALL:
                $done = $module === 'Admin' ? false : $this->app->moduleManager->uninstall($module);
                $msg  = $done
                    ? $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleUninstalledSuccessful')
                    : $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleUninstalledFailure');

                $new = clone $old;
                $new->setStatus(ModuleStatusUpdateType::DELETE);
                ModuleMapper::delete()->execute($new);

                break;
            default:
                $done                     = false;
                $msg                      = $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'UnknownModuleStatusChange');
                $response->header->status = RequestStatusCode::R_400;
        }

        if ($done) {
            $new = ModuleMapper::get()->where('id', $module)->execute();

            $this->app->eventManager->triggerSimilar(
                'POST:Module:Admin-module-status-update', '',
                [
                    $request->header->account,
                    $old, $new,
                    StringUtils::intHash(ModuleMapper::class), 'module-status',
                    self::NAME,
                    $module,
                    '',
                    $request->getOrigin(),
                ]
            );
        } else {
            $response->header->status = RequestStatusCode::R_400;
        }

        $this->fillJsonResponse(
            $request,
            $response,
            $done ? NotificationLevel::OK : NotificationLevel::WARNING,
            $this->app->l11nManager->getText($response->header->l11n->language, 'Admin', 'Api', 'ModuleStatusTitle'),
            $msg,
            []
        );
    }

    /**
     * Api method to get a user permission
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountPermissionGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var AccountPermission $account */
        $account = AccountPermissionMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->createStandardReturnResponse($request, $response, $account);
    }

    /**
     * Api method to get a group permission
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiGroupPermissionGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var GroupPermission $group */
        $group = GroupPermissionMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->createStandardReturnResponse($request, $response, $group);
    }

    /**
     * Api method to delete a group permission
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiGroupPermissionDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateGroupPermissionDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var GroupPermission $permission */
        $permission = GroupPermissionMapper::get()->where('id', (int) $request->getData('id'))->execute();

        if ($permission->getGroup() === 3) {
            // admin group cannot be deleted
            $this->createInvalidDeleteResponse($request, $response, []);

            return;
        }

        $this->deleteModel($request->header->account, $permission, GroupPermissionMapper::class, 'group-permission', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $permission);
    }

    /**
     * Api method to delete a user permission
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountPermissionDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAccountPermissionDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var AccountPermission $permission */
        $permission = AccountPermissionMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $permission, AccountPermissionMapper::class, 'user-permission', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $permission);
    }

    /**
     * Api method to add a permission to a group
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddGroupPermission(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validatePermissionCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        if (((int) $request->getData('permissionref')) === 3) {
            // admin group cannot be deleted
            $this->createInvalidUpdateResponse($request, $response, []);

            return;
        }

        $permission = $this->createPermissionFromRequest($request);

        if (!($permission instanceof GroupPermission)) {
            $response->data['permission_create'] = new FormValidation($val);
            $response->header->status            = RequestStatusCode::R_400;

            return;
        }

        $this->createModel($request->header->account, $permission, GroupPermissionMapper::class, 'group-permission', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $permission);
    }

    /**
     * Api method to add a permission to a account
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddAccountPermission(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validatePermissionCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $permission = $this->createPermissionFromRequest($request);

        if (!($permission instanceof AccountPermission)) {
            $response->data['permission_create'] = new FormValidation($val);
            $response->header->status            = RequestStatusCode::R_400;

            return;
        }

        $this->createModel($request->header->account, $permission, AccountPermissionMapper::class, 'account-permission', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $permission);
    }

    /**
     * Api method to add a permission to a account-model combination
     *
     * @param PermissionAbstract $permission Permission to create for account-model combination
     * @param int                $account    Account creating this model
     * @param string             $ip         Ip
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function createAccountModelPermission(PermissionAbstract $permission, int $account, string $ip) : void
    {
        $this->createModel($account, $permission, AccountPermissionMapper::class, 'account-permission', $ip);
    }

    /**
     * Validate permission create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validatePermissionCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['permissionowner'] = !PermissionOwner::isValidValue((int) $request->getData('permissionowner')))
            || ($val['permissionref'] = !\is_numeric($request->getData('permissionref')))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create a permission from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return AccountPermission|GroupPermission
     *
     * @since 1.0.0
     */
    public function createPermissionFromRequest(RequestAbstract $request) : PermissionAbstract
    {
        /** @var AccountPermission|GroupPermission $permission */
        $permission = ($request->getDataInt('permissionowner')) === PermissionOwner::GROUP
            ? new GroupPermission((int) $request->getData('permissionref'))
            : new AccountPermission((int) $request->getData('permissionref'));

        $permission->unit      = $request->getDataInt('permissionunit');
        $permission->app       = $request->getDataInt('permissionapp');
        $permission->module    = $request->getDataString('permissionmodule');
        $permission->category  = $request->getDataInt('permissioncategory');
        $permission->element   = $request->getDataInt('permissionelement');
        $permission->component = $request->getDataInt('permissioncomponent');
        $permission->setPermission(
            ($request->getDataInt('permissionread') ?? 0)
                | ($request->getDataInt('permissioncreate') ?? 0)
                | ($request->getDataInt('permissionupdate') ?? 0)
                | ($request->getDataInt('permissiondelete') ?? 0)
                | ($request->getDataInt('permissionpermission') ?? 0)
        );

        return $permission;
    }

    /**
     * Api method to update a account permission
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAccountPermissionUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAccountPermissionUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var AccountPermission $old */
        $old = AccountPermissionMapper::get()->where('id', (int) $request->getData('id'))->execute();

        /** @var AccountPermission $new */
        $new = $this->updatePermissionFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, AccountPermissionMapper::class, 'account-permission', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Api method to update a group permission
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiGroupPermissionUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateGroupPermissionUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var GroupPermission $old */
        $old = GroupPermissionMapper::get()->where('id', (int) $request->getData('id'))->execute();

        if ($old->getGroup() === 3) {
            // admin group cannot be deleted
            $this->createInvalidUpdateResponse($request, $response, []);

            return;
        }

        /** @var GroupPermission $new */
        $new = $this->updatePermissionFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, GroupPermissionMapper::class, 'group-permission', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update a group permission from a request
     *
     * @param RequestAbstract    $request    Request
     * @param PermissionAbstract $permission Permission model
     *
     * @return PermissionAbstract
     *
     * @since 1.0.0
     */
    private function updatePermissionFromRequest(RequestAbstract $request, PermissionAbstract $permission) : PermissionAbstract
    {
        $permission->unit      = $request->getDataInt('permissionunit') ?? $permission->unit;
        $permission->app       = $request->getDataInt('permissionapp') ?? $permission->app;
        $permission->module    = $request->getDataString('permissionmodule') ?? $permission->module;
        $permission->category  = $request->getDataInt('permissioncategory') ?? $permission->category;
        $permission->element   = $request->getDataInt('permissionelement') ?? $permission->element;
        $permission->component = $request->getDataInt('permissioncomponent') ?? $permission->component;
        $permission->setPermission(($request->getDataInt('permissioncreate') ?? 0)
            | ($request->getDataInt('permissionread') ?? 0)
            | ($request->getDataInt('permissionupdate') ?? 0)
            | ($request->getDataInt('permissiondelete') ?? 0)
            | ($request->getDataInt('permissionpermission') ?? 0));

        return $permission;
    }

    /**
     * Api method to add a group to an account
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddGroupToAccount(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAddGroupToAccount($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidAddResponse($request, $response, $val);

            return;
        }

        $account = (int) $request->getData('account');
        $groups  = [$request->getDataInt('account-list') ?? 0];

        $this->createModelRelation($request->header->account, $account, $groups, AccountMapper::class, 'groups', 'account-group', $request->getOrigin());
        $this->createStandardAddResponse($request, $response, $groups);
    }

    /**
     * Validate adding a group to an account request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateAddGroupToAccount(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['account'] = !$request->hasData('account'))
            || ($val['accountlist'] = !$request->hasData('account-list'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to add an account to a group
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiAddAccountToGroup(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateAddAccountToGroup($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidAddResponse($request, $response, $val);

            return;
        }

        $group    = (int) $request->getData('group');
        $accounts = [$request->getDataInt('group-list') ?? 0];

        $this->createModelRelation($request->header->account, $group, $accounts, GroupMapper::class, 'accounts', 'group-account', $request->getOrigin());
        $this->createStandardAddResponse($request, $response, $accounts);
    }

    /**
     * Validate adding an account to a group request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateAddAccountToGroup(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['group'] = !$request->hasData('group'))
            || ($val['grouplist'] = !$request->hasData('group-list'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to add a group to an account
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDeleteGroupFromAccount(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $account = (int) $request->getData('account');
        $groups  = \array_map('intval', $request->getDataList('igroup-idlist'));

        if (\in_array(3, $groups) && $account === $request->header->account) {
            // admin group cannot be deleted
            $this->createInvalidRemoveResponse($request, $response, []);

            return;
        }

        $this->deleteModelRelation($request->header->account, $account, $groups, AccountMapper::class, 'groups', 'account-group', $request->getOrigin());
        $this->createStandardRemoveResponse($request, $response, $groups);
    }

    /**
     * Api method to add an account to a group
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDeleteAccountFromGroup(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $group    = (int) $request->getData('group');
        $accounts = \array_map('intval', $request->getDataList('iaccount-idlist'));

        if (\in_array($request->header->account, $accounts) && $group === 3) {
            // admin group cannot be deleted
            $this->createInvalidRemoveResponse($request, $response, []);

            return;
        }

        $this->deleteModelRelation($request->header->account, $group, $accounts, GroupMapper::class, 'accounts', 'group-account', $request->getOrigin());
        $this->createStandardRemoveResponse($request, $response, $accounts);
    }

    /**
     * Api re-init routes
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiReInit(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $directories = \glob(__DIR__ . '/../../../Web/*', \GLOB_ONLYDIR);

        if ($directories !== false) {
            foreach ($directories as $directory) {
                if (\is_file($path = $directory . '/Routes.php')) {
                    \file_put_contents($path, '<?php return [];');
                }

                if (\is_file($path = $directory . '/Hooks.php')) {
                    \file_put_contents($path, '<?php return [];');
                }
            }
        }

        if (\is_file($path = __DIR__ . '/../../../Cli/Routes.php')) {
            \file_put_contents($path, '<?php return [];');
        }

        if (\is_file($path = __DIR__ . '/../../../Socket/Routes.php')) {
            \file_put_contents($path, '<?php return [];');
        }

        $installedModules = $this->app->moduleManager->getActiveModules();
        foreach ($installedModules as $name => $module) {
            $this->app->moduleManager->reInit($name);
        }
    }

    /**
     * Api check for updates
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiCheckForUpdates(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        // this is only a temp... in the future this logic will change but for current purposes this is the easiest way to implement updates
        $request = new HttpRequest(new HttpUri('https://api.github.com/repos/Karaka/Updates/contents'));
        $request->setMethod(RequestMethod::GET);
        $request->header->set('User-Agent', 'spl1nes');

        $updateFilesJson = Rest::request($request)->getJsonData();

        /** @var array<string, array<string, mixed>> */
        $toUpdate = [];

        foreach ($updateFilesJson as $file) {
            $name = \explode('_', $file['name']);
            $path = '';

            if (\is_dir(__DIR__ . '/../../../' . $name[0])) {
                $path = __DIR__ . '/../../../' . $name[0];
            } elseif (\is_dir(__DIR__ . '/../../' . $name[0])) {
                $path = __DIR__ . '/../../' . $name[0];
            }

            if ($path === '') {
                return;
            }

            $currentVersion = '';
            $remoteVersion  = \substr($file[1], 0, -5);

            if (Version::compare($currentVersion, $remoteVersion) < 0) {
                $toUpdate[$name[0]][$remoteVersion] = $file;

                \uksort($toUpdate[$name[0]], [Version::class, 'compare']);
            }
        }

        $this->apiUpdate($toUpdate);
    }

    /**
     * Api update file
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUpdateFile(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $this->apiUpdate([[
            'name'         => 'temp.json',
            'download_url' => 'https://raw.githubusercontent.com/Karaka-Management/' . ($request->getDataString('url') ?? ''),
        ]]);
    }

    /**
     * Update the system or a module
     *
     * @param array $toUpdate Array of updte resources
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function apiUpdate(array $toUpdate) : void
    {
        // this is only a temp... in the future this logic will change but for current purposes this is the easiest way to implement updates

        foreach ($toUpdate as $update) {
            $dest = __DIR__ . '/../Updates/' . \explode('.', $update['name'])[0];
            \mkdir($dest);
            $this->downloadUpdate($update['download_url'], $dest . '/' . $update['name']);
            $this->runUpdate($dest . '/' . $update['name']);
        }
    }

    /**
     * Package to download
     *
     * @param string $url  Url to download from
     * @param string $dest Local destination of the download
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function downloadUpdate(string $url, string $dest) : void
    {
        // this is only a temp... in the future this logic will change but for current purposes this is the easiest way to implement updates
        $request = new HttpRequest(new HttpUri($url));
        $request->setMethod(RequestMethod::GET);

        $updateFile = Rest::request($request)->getBody();
        File::put($dest, $updateFile);
    }

    /**
     * Run the update
     *
     * @param string $updateFile Update file/package
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function runUpdate(string $updateFile) : void
    {
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiContactCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateContactCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $contact = $this->createContactFromRequest($request);

        $this->createModel($request->header->account, $contact, ContactMapper::class, 'account_contact', $request->getOrigin());

        $this->createModelRelation(
            $request->header->account,
            (int) $request->getData('account'),
            $contact->id,
            AccountMapper::class, 'contacts', '', $request->getOrigin()
        );

        $this->createStandardCreateResponse($request, $response, $contact);
    }

    /**
     * Validate contact element create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    public function validateContactCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['account'] = !$request->hasData('account'))
            || ($val['type'] = !\is_numeric($request->getData('type')))
            || ($val['content'] = !$request->hasData('content'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create a account element from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Contact
     *
     * @since 1.0.0
     */
    public function createContactFromRequest(RequestAbstract $request) : Contact
    {
        /** @var Contact $element */
        $element = new Contact();
        $element->setType($request->getDataInt('type') ?? 0);
        $element->setSubtype($request->getDataInt('subtype') ?? 0);
        $element->content = $request->getDataString('content') ?? '';
        $element->account = $request->getDataInt('account') ?? 0;

        return $element;
    }

    /**
     * Api method to delete Settings
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSettingsDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateSettingsDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        $settings = SettingMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $settings, SettingMapper::class, 'settings', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $settings);
    }

    /**
     * Validate Settings delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateSettingsDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to update Application
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateApplicationUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var App $old */
        $old = AppMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateApplicationFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, AppMapper::class, 'application', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update Application from request.
     *
     * @param RequestAbstract $request Request
     * @param App             $new     Model to modify
     *
     * @return App
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    public function updateApplicationFromRequest(RequestAbstract $request, App $new) : App
    {
        return $new;
    }

    /**
     * Validate Application update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateApplicationUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to delete Application
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateApplicationDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var \Modules\Admin\Models\App $application */
        $application = AppMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $application, AppMapper::class, 'application', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $application);
    }

    /**
     * Validate Application delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateApplicationDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Validate GroupPermission update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateGroupPermissionUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Validate GroupPermission delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateGroupPermissionDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Method to update AccountPermission from request.
     *
     * @param RequestAbstract   $request Request
     * @param AccountPermission $new     Model to modify
     *
     * @return AccountPermission
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    public function updateAccountPermissionFromRequest(RequestAbstract $request, AccountPermission $new) : AccountPermission
    {
        return $new;
    }

    /**
     * Validate AccountPermission update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateAccountPermissionUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Validate AccountPermission delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateAccountPermissionDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to update Contact
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiContactUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateContactUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var Contact $old */
        $old = ContactMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateContactFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, ContactMapper::class, 'contact', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update Contact from request.
     *
     * @param RequestAbstract $request Request
     * @param Contact         $new     Model to modify
     *
     * @return Contact
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    public function updateContactFromRequest(RequestAbstract $request, Contact $new) : Contact
    {
        return $new;
    }

    /**
     * Validate Contact update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateContactUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to delete Contact
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiContactDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateContactDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var \Modules\Admin\Models\Contact $contact */
        $contact = ContactMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $contact, ContactMapper::class, 'contact', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $contact);
    }

    /**
     * Validate Contact delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateContactDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create Data
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDataChangeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateDataChangeCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $data = $this->createDataChangeFromRequest($request);
        $this->createModel($request->header->account, $data, DataChangeMapper::class, 'data', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $data);
    }

    /**
     * Method to create DataChange from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return DataChange
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function createDataChangeFromRequest(RequestAbstract $request) : DataChange
    {
        $data = new DataChange();

        return $data;
    }

    /**
     * Validate Data create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateDataChangeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (false) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to delete DataChange
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDataChangeDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateDataChangeDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var \Modules\Admin\Models\DataChange $data */
        $data = DataChangeMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $data, DataChangeMapper::class, 'data', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $data);
    }

    /**
     * Validate DataChange delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validateDataChangeDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }
}
