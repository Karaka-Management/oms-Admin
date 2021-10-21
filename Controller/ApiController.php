<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Admin\Controller;

use Model\App;
use Modules\Admin\Models\Account;
use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\AccountPermission;
use Modules\Admin\Models\AccountPermissionMapper;
use Modules\Admin\Models\Group;
use Modules\Admin\Models\GroupMapper;
use Modules\Admin\Models\GroupPermission;
use Modules\Admin\Models\GroupPermissionMapper;
use Modules\Admin\Models\LocalizationMapper;
use Modules\Admin\Models\Module;
use Modules\Admin\Models\ModuleMapper;
use Modules\Admin\Models\ModuleStatusUpdateType;
use Modules\Admin\Models\NullAccount;
use Modules\Admin\Models\PermissionState;
use Modules\Media\Models\Collection;
use Modules\Media\Models\CollectionMapper;
use Modules\Media\Models\UploadFile;
use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Account\GroupStatus;
use phpOMS\Account\PermissionAbstract;
use phpOMS\Account\PermissionOwner;
use phpOMS\Account\PermissionType;
use phpOMS\Application\ApplicationInfo;
use phpOMS\Application\ApplicationManager;
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
use phpOMS\Message\Mail\SubmitType;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\Model\Message\Notify;
use phpOMS\Model\Message\NotifyType;
use phpOMS\Model\Message\Reload;
use phpOMS\Module\ModuleInfo;
use phpOMS\Module\ModuleStatus;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\File\Local\File;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Uri\UriFactory;
use phpOMS\Utils\Parser\Markdown\Markdown;
use phpOMS\Utils\StringUtils;
use phpOMS\Validation\Network\Email as EmailValidator;
use phpOMS\Version\Version;

/**
 * Admin controller class.
 *
 * This class is responsible for the basic admin activities such as managing accounts, groups, permissions and modules.
 *
 * @package Modules\Admin
 * @license OMS License 1.0
 * @link    https://orange-management.org
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
    public function apiLogin(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);

        $login = AccountMapper::login((string) ($request->getData('user') ?? ''), (string) ($request->getData('pass') ?? ''));

        if ($login >= LoginReturnType::OK) {
            $this->app->sessionManager->set('UID', $login, true);
            $this->app->sessionManager->save();
            $response->set($request->uri->__toString(), new Reload());
        } else {
            $response->set($request->uri->__toString(), new Notify(
                'Login failed due to wrong login information',
                NotifyType::INFO
            ));
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
    public function apiLogout(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);

        $this->app->sessionManager->remove('UID');
        $this->app->sessionManager->save();

        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
        $response->set($request->uri->__toString(), [
            'status'   => NotificationLevel::OK,
            'title'    => 'Logout successfull',
            'message'  => 'You are redirected to the login page',
            'response' => null,
        ]);
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
    public function apiForgot(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $account = AccountMapper::getBy((string) $request->getData('login'), 'login');

        $forgotten = $this->app->appSettings->get(
            names: ['forgott_date', 'forgrott_count'],
            module: self::NAME,
            account: $account->getId()
        );

        if ((int) $forgotten['forgrotten_count'] > 3) {
            $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
            $response->set($request->uri->__toString(), [
                'status'   => NotificationLevel::ERROR,
                'title'    => 'Password Reset',
                'message'  => 'Password reset failed due to invalid login data or too many reset attemps.',
                'response' => null,
            ]);
        }

        $handler = new MailHandler();
        $handler->setMailer(SubmitType::MAIL);

        $mail = new Email();
        $mail->setFrom('test1@orange-management.email', 'Orange-Management');
        $mail->addTo($account->email, \trim($account->name1 . ' ' . $account->name2 . ' ' . $account->name3));
        $mail->subject = 'Orange Management: Forgot Password';
        $mail->body    = 'Please reset your password at: .....';

        $this->app->appSettings->set([
            ['name' => 'forgott_date', 'module' => self::NAME, 'account' => $account->getId(), 'content' => (string) \time()],
            ['name' => 'forgotten_count', 'module' => self::NAME, 'account' => $account->getId(), 'content' => (string) (((int) $forgotten['forgrotten_count']) + 1)],
        ], true);

        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
        $response->set($request->uri->__toString(), [
            'status'   => NotificationLevel::OK,
            'title'    => 'Password Reset',
            'message'  => 'You received a pasword reset email.',
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
    public function apiResetPassword(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        // @todo: implement
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
    public function apiSettingsGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $id      = $request->getData('id');
        $group   = $request->getData('group');
        $account = $request->getData('account');

        $response->set(
            $request->uri->__toString(),
            [
                'response' => $this->app->appSettings->get(
                    $id !== null ? (int) $id : $id,
                    $request->getData('name') ?? '',
                    $request->getData('app') ?? null,
                    $request->getData('module') ?? null,
                    $group !== null ? (int) $group : $group,
                    $account !== null ? (int) $account : $account
                ),
            ]
        );
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
    public function apiSettingsSet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $dataSettings = $request->getDataJson('settings');

        foreach ($dataSettings as $data) {
            $id      = isset($data['id']) ? (int) $data['id'] : null;
            $name    = $data['name'] ?? null;
            $content = $data['content'] ?? null;
            $app     = $data['app'] ?? null;
            $module  = $data['module'] ?? null;
            $group   = isset($data['group']) ? (int) $data['group'] : null;
            $account = isset($data['account']) ? (int) $data['account'] : null;

            $this->updateModel(
                $request->header->account,
                $this->app->appSettings->get($id, $name, $app, $module, $group, $account),
                $data,
                function () use ($id, $name, $content, $app, $module, $group, $account) : void {
                    $this->app->appSettings->set([
                        [
                            'id'      => $id,
                            'name'    => $name,
                            'content' => $content,
                            'app'     => $app,
                            'module'  => $module,
                            'group'   => $group,
                            'account' => $account,
                        ],
                    ], true);
                },
                'settings',
                $request->getOrigin()
            );
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Settings', 'Settings successfully modified', $dataSettings);
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
    public function apiSettingsAccountLocalizationSet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $requestAccount = $request->header->account;
        $accountId      = (int) $request->getData('account_id');

        if ($requestAccount !== $accountId
            && !$this->app->accountManager->get($accountId)->hasPermission(
                PermissionType::MODIFY,
                $this->app->orgId,
                $this->app->appName,
                self::NAME,
                PermissionState::ACCOUNT_SETTINGS,
                $accountId
            )
        ) {
            $this->fillJsonResponse($request, $response, NotificationLevel::HIDDEN, '', '', []);
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        /** @var Localization $l11n */
        $l11n = AccountMapper::get($accountId)->l11n;

        if ((bool) ($request->getData('load') ?? false)) {
            $locale = \explode('_', $request->getData('localization_load'));
            $l11n->loadFromLanguage($locale[0], $locale[1]);

            LocalizationMapper::update($l11n);

            $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Localization successfully modified', $l11n);

            return;
        }

        $dataSettings = $request->getLike('settings_(.*)');

        $l11n->setCountry($dataSettings['settings_country']);
        $l11n->setLanguage($dataSettings['settings_language']);
        $l11n->setTemperature($dataSettings['settings_temperature']);

        $l11n->setTimezone($dataSettings['settings_timezone']);
        $l11n->setDatetime(
            [
                'very_short' => $dataSettings['settings_timeformat_vs'],
                'short'      => $dataSettings['settings_timeformat_s'],
                'medium'     => $dataSettings['settings_timeformat_m'],
                'long'       => $dataSettings['settings_timeformat_l'],
                'very_long'  => $dataSettings['settings_timeformat_vl'],
            ]
        );

        $l11n->setCurrency($dataSettings['settings_currency']);
        $l11n->setCurrencyFormat($dataSettings['settings_currencyformat']);

        $l11n->setDecimal($dataSettings['settings_decimal']);
        $l11n->setThousands($dataSettings['settings_thousands']);

        $l11n->setPrecision(
            [
                'very_short' => $dataSettings['settings_precision_vs'],
                'short'      => $dataSettings['settings_precision_s'],
                'medium'     => $dataSettings['settings_precision_m'],
                'long'       => $dataSettings['settings_precision_l'],
                'very_long'  => $dataSettings['settings_precision_vl'],
            ]
        );

        $l11n->setWeight(
            [
                'very_light' => $dataSettings['settings_weight_vl'],
                'light'      => $dataSettings['settings_weight_l'],
                'medium'     => $dataSettings['settings_weight_m'],
                'heavy'      => $dataSettings['settings_weight_h'],
                'very_heavy' => $dataSettings['settings_weight_vh'],
            ]
        );

        $l11n->setSpeed(
            [
                'very_slow' => $dataSettings['settings_speed_vs'],
                'slow'      => $dataSettings['settings_speed_s'],
                'medium'    => $dataSettings['settings_speed_m'],
                'fast'      => $dataSettings['settings_speed_f'],
                'very_fast' => $dataSettings['settings_speed_vf'],
                'sea'       => $dataSettings['settings_speed_sea'],
            ]
        );

        $l11n->setLength(
            [
                'very_short' => $dataSettings['settings_length_vs'],
                'short'      => $dataSettings['settings_length_s'],
                'medium'     => $dataSettings['settings_length_m'],
                'long'       => $dataSettings['settings_length_l'],
                'very_long'  => $dataSettings['settings_length_vl'],
                'sea'        => $dataSettings['settings_length_sea'],
            ]
        );

        $l11n->setArea(
            [
                'very_small' => $dataSettings['settings_area_vs'],
                'small'      => $dataSettings['settings_area_s'],
                'medium'     => $dataSettings['settings_area_m'],
                'large'      => $dataSettings['settings_area_l'],
                'very_large' => $dataSettings['settings_area_vl'],
            ]
        );

        $l11n->setVolume(
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

        LocalizationMapper::update($l11n);

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Localization successfully modified', $l11n);
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
    public function apiSettingsDesignSet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $uploadedFiles = $request->getFiles() ?? [];

        if (!empty($uploadedFiles)) {
            $upload                   = new UploadFile();
            $upload->preserveFileName = false;
            $upload->outputDir        = __DIR__ . '/../../../Web/Backend/img';

            $status = $upload->upload($uploadedFiles, ['logo.png'], true);
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Design', 'Design successfully updated', []);
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
    public function apiInstallApplication(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $appManager = new ApplicationManager($this->app);

        $app = $request->getData('appSrc');
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
            __DIR__ . '/../../../' . $request->getData('appDest') ?? '',
            $request->getData('theme') ?? 'Default'
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
    public function apiGroupGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $group = GroupMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Group successfully returned', $group);
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
    public function apiGroupUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Group $old */
        $old = clone GroupMapper::get((int) $request->getData('id'));
        $new = $this->updateGroupFromRequest($request);
        $this->updateModel($request->header->account, $old, $new, GroupMapper::class, 'group', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Group successfully updated', $new);
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
    private function updateGroupFromRequest(RequestAbstract $request) : Group
    {
        $group       = GroupMapper::get((int) $request->getData('id'));
        $group->name = (string) ($request->getData('name') ?? $group->name);
        $group->setStatus((int) ($request->getData('status') ?? $group->getStatus()));
        $group->description    = Markdown::parse((string) ($request->getData('description') ?? $group->descriptionRaw));
        $group->descriptionRaw = (string) ($request->getData('description') ?? $group->descriptionRaw);

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
        if (($val['name'] = empty($request->getData('name')))
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
    public function apiGroupCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateGroupCreate($request))) {
            $response->set('group_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $group = $this->createGroupFromRequest($request);
        $this->createModel($request->header->account, $group, GroupMapper::class, 'group', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Group successfully created', $group);
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
        $group->name      = (string) ($request->getData('name') ?? '');
        $group->setStatus((int) ($request->getData('status') ?? GroupStatus::INACTIVE));
        $group->description    = Markdown::parse((string) ($request->getData('description') ?? ''));
        $group->descriptionRaw = (string) ($request->getData('description') ?? '');

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
    public function apiGroupDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (((int) $request->getData('id')) === 3) {
            // admin group cannot be deleted
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Group', 'Admin group cannot be deleted', []);

            return;
        }

        $group = GroupMapper::get((int) $request->getData('id'));
        $this->deleteModel($request->header->account, $group, GroupMapper::class, 'group', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Group successfully deleted', $group);
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
    public function apiGroupFind(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values(
                GroupMapper::find((string) ($request->getData('search') ?? ''))
            )
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
    public function apiAccountGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Account $account */
        $account = AccountMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Account', 'Account successfully returned', $account);
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
    public function apiAccountFind(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values(
                AccountMapper::find((string) ($request->getData('search') ?? ''))
            )
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
    public function apiAccountGroupFind(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Account[] $accounts */
        $accounts = \array_values(AccountMapper::find((string) ($request->getData('search') ?? '')));

        /** @var Group[] $groups */
        $groups = \array_values(GroupMapper::find((string) ($request->getData('search') ?? '')));
        $data   = [];

        foreach ($accounts as $account) {
            $temp                = $account->jsonSerialize();
            $temp['type_prefix'] = 'a';
            $temp['type_name']   = 'Account';

            $data[] = $temp;
        }

        foreach ($groups as $group) {
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
        if (($val['name1'] = empty($request->getData('name1')))
            || ($val['type'] = !AccountType::isValidValue((int) $request->getData('type')))
            || ($val['status'] = !AccountStatus::isValidValue((int) $request->getData('status')))
            || ($val['email'] = !empty($request->getData('email')) && !EmailValidator::isValid((string) $request->getData('email')))
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
    public function apiAccountCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateAccountCreate($request))) {
            $response->set('account_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $account = $this->createAccountFromRequest($request);

        $this->createModel($request->header->account, $account, AccountMapper::class, 'account', $request->getOrigin());
        $this->createProfileForAccount($account, $request);
        $this->createMediaDirForAccount($account->getId(), $account->login ?? '', $request->header->account);

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Account', 'Account successfully created. Link: <a href="' . (UriFactory::build('{/prefix}admin/account/settings?{?}&id=' . $account->getId())) . '">Account</a>', $account);
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

        CollectionMapper::create($collection);

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
        if (((string) ($request->getData('password') ?? '')) === ''
            || ((string) ($request->getData('login') ?? '')) === ''
        ) {
            return;
        }

        $old = clone $account;

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
        $account = new Account();
        $account->setStatus((int) ($request->getData('status') ?? AccountStatus::INACTIVE));
        $account->setType((int) ($request->getData('type') ?? AccountType::USER));
        $account->login = (string) ($request->getData('login') ?? '');
        $account->name1 = (string) ($request->getData('name1') ?? '');
        $account->name2 = (string) ($request->getData('name2') ?? '');
        $account->name3 = (string) ($request->getData('name3') ?? '');
        $account->setEmail((string) ($request->getData('email') ?? ''));
        $account->generatePassword((string) ($request->getData('password') ?? ''));

        if ($request->getData('locale') === null) {
            $account->l11n = Localization::fromJson(
                    $this->app->l11nServer === null ? $request->header->l11n->jsonSerialize() : $this->app->l11nServer->jsonSerialize()
                );
        } else {
            $locale = \explode('_', $request->getData('locale') ?? '');

            $account->l11n
                ->loadFromLanguage(
                    $locale[0] ?? $this->app->l11nServer->getLanguage(),
                    $locale[1] ?? $this->app->l11nServer->getCountry()
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
    public function apiAccountDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Account $account */
        $account = AccountMapper::get((int) ($request->getData('id')));
        $this->deleteModel($request->header->account, $account, AccountMapper::class, 'account', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Account', 'Account successfully deleted', $account);
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
    public function apiAccountUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Account $old */
        $old = clone AccountMapper::get((int) $request->getData('id'));
        $new = $this->updateAccountFromRequest($request);
        $this->updateModel($request->header->account, $old, $new, AccountMapper::class, 'account', $request->getOrigin());

        if (\Modules\Profile\Models\ProfileMapper::getFor($new->getId(), 'account') instanceof \Modules\Profile\Models\NullProfile) {
            $this->createProfileForAccount($new, $request);
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Account', 'Account successfully updated', $new);
    }

    /**
     * Method to update an account from a request
     *
     * @param RequestAbstract $request       Request
     * @param bool            $allowPassword Allow to change password
     *
     * @return Account
     *
     * @since 1.0.0
     */
    private function updateAccountFromRequest(RequestAbstract $request, bool $allowPassword = false) : Account
    {
        /** @var Account $account */
        $account        = AccountMapper::get((int) ($request->getData('id')));
        $account->login = (string) ($request->getData('login') ?? $account->login);
        $account->name1 = (string) ($request->getData('name1') ?? $account->name1);
        $account->name2 = (string) ($request->getData('name2') ?? $account->name2);
        $account->name3 = (string) ($request->getData('name3') ?? $account->name3);
        $account->setEmail((string) ($request->getData('email') ?? $account->getEmail()));
        $account->setStatus((int) ($request->getData('status') ?? $account->getStatus()));
        $account->setType((int) ($request->getData('type') ?? $account->getType()));

        if ($allowPassword && !empty($request->getData('password'))) {
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
    public function apiModuleStatusUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $module = $request->getData('module');
        $status = (int) $request->getData('status');

        if (empty($module) || empty($status)) {
            $response->set($request->uri->__toString(), [
                'status'   => 'warning',
                'title'    => 'Module',
                'message'  => 'Invalid module or status',
                'response' => [],
            ]);

            $response->header->status = RequestStatusCode::R_403;
            return;
        }

        $old = ModuleMapper::get($module);

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
                $msg  = $done ? 'Module successfully activated.' : 'Module not activated.';

                $new = clone $old;
                $new->setStatus(ModuleStatusUpdateType::ACTIVATE);
                ModuleMapper::update($new);

                break;
            case ModuleStatusUpdateType::DEACTIVATE:
                $done = $module === 'Admin' ? false : $this->app->moduleManager->deactivate($module);
                $msg  = $done ? 'Module successfully deactivated.' : 'Module not deactivated.';

                $new = clone $old;
                $new->setStatus(ModuleStatusUpdateType::DEACTIVATE);
                ModuleMapper::update($new);

                break;
            case ModuleStatusUpdateType::INSTALL:
                $done = $this->app->moduleManager->isInstalled($module) ? true : false;
                $msg  = $done ? 'Module successfully installed.' : 'Module not installed.';

                if ($done) {
                    break;
                }

                if (!\is_file(__DIR__ . '/../../../Modules/' . $module . '/info.json')) {
                    $msg  = 'Status change for unknown module requested';
                    $done = false;
                    break;
                }

                $moduleInfo = new ModuleInfo(__DIR__ . '/../../../Modules/' . $module . '/info.json');
                $moduleInfo->load();

                // install dependencies
                $dependencies = $moduleInfo->getDependencies();
                foreach ($dependencies as $key => $version) {
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

                $moduleObj->setStatus(ModuleStatus::AVAILABLE);

                ModuleMapper::create($moduleObj);

                $done = $this->app->moduleManager->install($module);
                $msg  = $done ? 'Module successfully installed.' : 'Module not installed.';

                $moduleObj->setStatus(ModuleStatus::ACTIVE);
                ModuleMapper::update($moduleObj);

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
                $msg  = $done ? 'Module successfully uninstalled.' : 'Module not uninstalled.';

                $new = clone $old;
                $new->setStatus(ModuleStatusUpdateType::DELETE);
                ModuleMapper::delete($new);

                break;
            default:
                $done                     = false;
                $msg                      = 'Unknown module status change request.';
                $response->header->status = RequestStatusCode::R_400;
        }
        ModuleMapper::clearCache();

        if ($done) {
            $new = ModuleMapper::get($module);

            $this->app->eventManager->triggerSimilar(
                'POST:Module:Admin-module-status-update', '',
                [
                    $request->header->account,
                    $old, $new,
                    StringUtils::intHash(ModuleMapper::class), 'module-status',
                    $module,
                    self::NAME,
                    $request->getOrigin(),
                ]
            );
        } else {
            $response->header->status = RequestStatusCode::R_400;
        }

        $this->fillJsonResponse(
            $request, $response,
            $done ? NotificationLevel::OK : NotificationLevel::WARNING,
            'Module', $msg, []
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
    public function apiAccountPermissionGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var AccountPermission $account */
        $account = AccountPermissionMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Permission', 'Permission successfully returned', $account);
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
    public function apiGroupPermissionGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var GroupPermission $group */
        $group = GroupPermissionMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Permission', 'Permission successfully returned', $group);
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
    public function apiGroupPermissionDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var GroupPermission $permission */
        $permission = GroupPermissionMapper::get((int) $request->getData('id'));

        if ($permission->getGroup() === 3) {
            // admin group cannot be deleted
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Group', 'Admin group permissions cannot be deleted', []);

            return;
        }

        $this->deleteModel($request->header->account, $permission, GroupPermissionMapper::class, 'group-permission', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Permission', 'Permission successfully deleted', $permission);
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
    public function apiAccountPermissionDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var AccountPermission $permission */
        $permission = AccountPermissionMapper::get((int) $request->getData('id'));
        $this->deleteModel($request->header->account, $permission, AccountPermissionMapper::class, 'user-permission', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Permission', 'Permission successfully deleted', $permission);
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
    public function apiAddGroupPermission(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (((int) $request->getData('permissionref')) === 3) {
            // admin group cannot be deleted
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Group', 'Admin group permissions cannot get modified', []);

            return;
        }

        if (!empty($val = $this->validatePermissionCreate($request))) {
            $response->set('permission_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $permission = $this->createPermissionFromRequest($request);

        if (!($permission instanceof GroupPermission)) {
            $response->set('permission_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $this->createModel($request->header->account, $permission, GroupPermissionMapper::class, 'group-permission', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Group permission successfully created', $permission);
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
    public function apiAddAccountPermission(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validatePermissionCreate($request))) {
            $response->set('permission_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $permission = $this->createPermissionFromRequest($request);

        if (!($permission instanceof AccountPermission)) {
            $response->set('permission_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $this->createModel($request->header->account, $permission, AccountPermissionMapper::class, 'account-permission', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Account', 'Account permission successfully created', $permission);
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
        $permission = ((int) $request->getData('permissionowner')) === PermissionOwner::GROUP ? new GroupPermission((int) $request->getData('permissionref')) : new AccountPermission((int) $request->getData('permissionref'));
        $permission->setUnit(empty($request->getData('permissionunit')) ? null : (int) $request->getData('permissionunit'));
        $permission->setApp(empty($request->getData('permissionapp')) ? null : (string) $request->getData('permissionapp'));
        $permission->setModule(empty($request->getData('permissionmodule')) ? null : (string) $request->getData('permissionmodule'));
        $permission->setType(empty($request->getData('permissiontype')) ? null : (int) $request->getData('permissiontype'));
        $permission->setElement(empty($request->getData('permissionelement')) ? null : (int) $request->getData('permissionelement'));
        $permission->setComponent(empty($request->getData('permissioncomponent')) ? null : (int) $request->getData('permissioncomponent'));
        $permission->setPermission(
            (int) ($request->getData('permissioncreate') ?? 0)
                | (int) ($request->getData('permissionread') ?? 0)
                | (int) ($request->getData('permissionupdate') ?? 0)
                | (int) ($request->getData('permissiondelete') ?? 0)
                | (int) ($request->getData('permissionpermission') ?? 0)
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
    public function apiAccountPermissionUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var AccountPermission $old */
        $old = clone AccountPermissionMapper::get((int) $request->getData('id'));

        /** @var AccountPermission $new */
        $new = $this->updatePermissionFromRequest(AccountPermissionMapper::get((int) $request->getData('id')), $request);

        $this->updateModel($request->header->account, $old, $new, AccountPermissionMapper::class, 'account-permission', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Permission', 'Permission successfully updated', $new);
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
    public function apiGroupPermissionUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var GroupPermission $old */
        $old = clone GroupPermissionMapper::get((int) $request->getData('id'));

        if ($old->getGroup() === 3) {
            // admin group cannot be deleted
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Group', 'Admin group permissions cannot get modified', []);

            return;
        }

        /** @var GroupPermission $new */
        $new = $this->updatePermissionFromRequest(GroupPermissionMapper::get((int) $request->getData('id')), $request);

        $this->updateModel($request->header->account, $old, $new, GroupPermissionMapper::class, 'group-permission', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Permission', 'Permission successfully updated', $new);
    }

    /**
     * Method to update a group permission from a request
     *
     * @param PermissionAbstract $permission Permission model
     * @param RequestAbstract    $request    Request
     *
     * @return PermissionAbstract
     *
     * @since 1.0.0
     */
    private function updatePermissionFromRequest(PermissionAbstract $permission, RequestAbstract $request) : PermissionAbstract
    {
        $permission->setUnit(empty($request->getData('permissionunit')) ? $permission->getUnit() : (int) $request->getData('permissionunit'));
        $permission->setApp(empty($request->getData('permissionapp')) ? $permission->getApp() : (string) $request->getData('permissionapp'));
        $permission->setModule(empty($request->getData('permissionmodule')) ? $permission->getModule() : (string) $request->getData('permissionmodule'));
        $permission->setType(empty($request->getData('permissiontype')) ? $permission->getType() : (int) $request->getData('permissiontype'));
        $permission->setElement(empty($request->getData('permissionelement')) ? $permission->getElement() : (int) $request->getData('permissionelement'));
        $permission->setComponent(empty($request->getData('permissioncomponent')) ? $permission->getComponent() : (int) $request->getData('permissioncomponent'));
        $permission->setPermission((int) ($request->getData('permissioncreate') ?? 0)
            | (int) ($request->getData('permissionread') ?? 0)
            | (int) ($request->getData('permissionupdate') ?? 0)
            | (int) ($request->getData('permissiondelete') ?? 0)
            | (int) ($request->getData('permissionpermission') ?? 0));

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
    public function apiAddGroupToAccount(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $account = (int) $request->getData('account');
        $groups  = \array_map('intval', $request->getDataList('igroup-idlist'));

        $this->createModelRelation($request->header->account, $account, $groups, AccountMapper::class, 'groups', 'account-group', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Account', 'Relation added', []);
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
    public function apiAddAccountToGroup(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $group    = (int) $request->getData('group');
        $accounts = \array_map('intval', $request->getDataList('iaccount-idlist'));

        $this->createModelRelation($request->header->account, $group, $accounts, GroupMapper::class, 'accounts', 'group-account', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Relation added', []);
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
    public function apiDeleteGroupFromAccount(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $account = (int) $request->getData('account');
        $groups  = \array_map('intval', $request->getDataList('igroup-idlist'));

        if (\in_array(3, $groups) && $account === $request->header->account) {
            // admin group cannot be deleted
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Account', 'Admin group cannot be removed from yourself', []);

            return;
        }

        $this->deleteModelRelation($request->header->account, $account, $groups, AccountMapper::class, 'groups', 'account-group', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Account', 'Relation deleted', []);
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
    public function apiDeleteAccountFromGroup(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $group    = (int) $request->getData('group');
        $accounts = \array_map('intval', $request->getDataList('iaccount-idlist'));

        if (\in_array($request->header->account, $accounts) && $group === 3) {
            // admin group cannot be deleted
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Group', 'Admin group cannot be removed from yourself', []);

            return;
        }

        $this->deleteModelRelation($request->header->account, $group, $accounts, GroupMapper::class, 'accounts', 'group-account', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Relation deleted', []);
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
    public function apiReInit(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
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

        if (\is_file($path = __DIR__ . '/../../../Console/Routes.php')) {
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
     * This update logic below is only temp. And will be replaced once the package management is implemented (not part of this application but part of the service provided by the organization website)
     */

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
     * @todo implement
     */
    public function apiCheckForUpdates(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        // this is only a temp... in the future this logic will change but for current purposes this is the easiest way to implement updates
        $request = new HttpRequest(new HttpUri('https://api.github.com/repos/Orange-Management/Updates/contents'));
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
    public function apiUpdateFile(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $this->apiUpdate([[
            'name'         => 'temp.json',
            'download_url' => 'https://raw.githubusercontent.com/Orange-Management/' . ($request->getData('url') ?? ''),
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
}
