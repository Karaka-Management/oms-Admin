<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    Modules\Admin
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace Modules\Admin\Controller;

use Model\Settings;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\AccountPermission;
use Modules\Admin\Models\AccountPermissionMapper;
use Modules\Admin\Models\Group;
use Modules\Admin\Models\GroupMapper;
use Modules\Admin\Models\GroupPermission;
use Modules\Admin\Models\GroupPermissionMapper;
use Modules\Admin\Models\ModuleStatusUpdateType;

use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Account\GroupStatus;
use phpOMS\Account\PermissionAbstract;
use phpOMS\Account\PermissionOwner;
use phpOMS\Message\Http\Request;
use phpOMS\Message\Http\RequestMethod;
use phpOMS\Message\Http\Rest;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Uri\Http;
use phpOMS\Model\Message\FormValidation;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\File\Local\File;
use phpOMS\System\File\ContentPutMode;
use phpOMS\System\MimeType;
use phpOMS\Utils\Parser\Markdown\Markdown;
use phpOMS\Utils\StringUtils;
use phpOMS\Validation\Network\Email;
use phpOMS\Version\Version;

/**
 * Admin controller class.
 *
 * This class is responsible for the basic admin activities such as managing accounts, groups, permissions and modules.
 *
 * @package    Modules\Admin
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
final class ApiController extends Controller
{
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
     * @since  1.0.0
     */
    public function apiSettingsGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->set(
            $request->getUri()->__toString(),
            [
                'response' => $this->app->appSettings->get((int) $request->getData('id')),
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
     * @since  1.0.0
     */
    public function apiSettingsSet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $data = $request->getDataJson('settings');
        $keys = \array_keys($data);

        $this->updateModel($request, $this->app->appSettings->get($keys), $data, function() use($data) : void {
            $this->app->appSettings->set($data, true);
        }, 'settings');

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Settigns', 'Settings successfully modified', $data);
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
     * @since  1.0.0
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
     * @since  1.0.0
     */
    public function apiGroupUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $old = clone GroupMapper::get((int) $request->getData('id'));
        $new = $this->updateGroupFromRequest($request);
        $this->updateModel($request, $old, $new, GroupMapper::class, 'group');
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Group successfully updated', $new);
    }

    /**
     * Method to update group from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Group
     *
     * @since  1.0.0
     */
    private function updateGroupFromRequest(RequestAbstract $request) : Group
    {
        $group = GroupMapper::get((int) $request->getData('id'));
        $group->setName((string) ($request->getData('name') ?? $group->getName()));
        $group->setStatus((int) ($request->getData('status') ?? $group->getStatus()));
        $group->setDescription(Markdown::parse((string) ($request->getData('description') ?? $group->getDescriptionRaw())));
        $group->setDescriptionRaw((string) ($request->getData('description') ?? $group->getDescriptionRaw()));

        return $group;
    }

    /**
     * Validate group create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since  1.0.0
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
     * @since  1.0.0
     */
    public function apiGroupCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateGroupCreate($request))) {
            $response->set('group_create', new FormValidation($val));

            return;
        }

        $group = $this->createGroupFromRequest($request);
        $this->createModel($request, $group, GroupMapper::class, 'group');
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Group successfully created', $group);
    }

    /**
     * Method to create group from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Group
     *
     * @since  1.0.0
     */
    private function createGroupFromRequest(RequestAbstract $request) : Group
    {
        $group = new Group();
        $group->setCreatedBy($request->getHeader()->getAccount());
        $group->setName((string) ($request->getData('name') ?? ''));
        $group->setStatus((int) ($request->getData('status') ?? GroupStatus::INACTIVE));
        $group->setDescription(Markdown::parse((string) ($request->getData('description') ?? '')));
        $group->setDescriptionRaw((string) ($request->getData('description') ?? ''));

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
     * @since  1.0.0
     */
    public function apiGroupDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $group = GroupMapper::get((int) $request->getData('id'));
        $this->deleteModel($request, $group, GroupMapper::class, 'group');
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
     * @since  1.0.0
     */
    public function apiGroupFind(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->getHeader()->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->getUri()->__toString(),
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
     * @since  1.0.0
     */
    public function apiAccountGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
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
     * @since  1.0.0
     */
    public function apiAccountFind(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->getHeader()->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->getUri()->__toString(),
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
     * @since  1.0.0
     */
    public function apiAccountGroupFind(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $accounts = \array_values(AccountMapper::find((string) ($request->getData('search') ?? '')));
        $groups   = \array_values(GroupMapper::find((string) ($request->getData('search') ?? '')));
        $data     = [];

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

        $response->getHeader()->set('Content-Type', MimeType::M_JSON, true);
        $response->set($request->getUri()->__toString(), $data);
    }

    /**
     * Method to validate account creation from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since  1.0.0
     */
    private function validateAccountCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['login'] = empty($request->getData('login')))
            || ($val['name1'] = empty($request->getData('name1')))
            || ($val['type'] = !AccountType::isValidValue((int) $request->getData('type')))
            || ($val['status'] = !AccountStatus::isValidValue((int) $request->getData('status')))
            || ($val['email'] = !empty($request->getData('email')) && !Email::isValid((string) $request->getData('email')))
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
     * @since  1.0.0
     */
    public function apiAccountCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateAccountCreate($request))) {
            $response->set('account_create', new FormValidation($val));

            return;
        }

        $account = $this->createAccountFromRequest($request);
        $this->createModel($request, $account, AccountMapper::class, 'account');
        $this->createProfileForAccount($account, $request);
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Account', 'Account successfully created', $account);
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
     * @since  1.0.0
     */
    private function createProfileForAccount(Account $account, RequestAbstract $request) : void
    {
        if (((string) ($request->getData('password') ?? '')) !== ''
            && ((string) ($request->getData('login') ?? '')) !== ''
        ) {
            $old = clone $account;

            $this->app->moduleManager->get('Profile')->apiProfileCreateDbEntry(
                new \Modules\Profile\Models\Profile($account),
                $request
            );

            $this->updateModel($request, $old, $account, function() use($account) : void {
                $account->setLoginTries((int) $this->app->appSettings->get(Settings::LOGIN_TRIES));
                AccountMapper::update($account);
            }, 'account');
        }
    }

    /**
     * Method to create an account from a request
     *
     * @param RequestAbstract $request Request
     *
     * @return Account
     *
     * @since  1.0.0
     */
    private function createAccountFromRequest(RequestAbstract $request) : Account
    {
        $account = new Account();
        $account->setStatus((int) ($request->getData('status') ?? AccountStatus::INACTIVE));
        $account->setType((int) ($request->getData('type') ?? AccountType::USER));
        $account->setName((string) ($request->getData('login') ?? ''));
        $account->setName1((string) ($request->getData('name1') ?? ''));
        $account->setName2((string) ($request->getData('name2') ?? ''));
        $account->setName3((string) ($request->getData('name3') ?? ''));
        $account->setEmail((string) ($request->getData('email') ?? ''));
        $account->generatePassword((string) ($request->getData('password') ?? ''));

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
     * @since  1.0.0
     */
    public function apiAccountDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $account = AccountMapper::get((int) ($request->getData('id')));
        $this->deleteModel($request, $account, AccountMapper::class, 'account');
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
     * @since  1.0.0
     */
    public function apiAccountUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $old = clone AccountMapper::get((int) $request->getData('id'));
        $new = $this->updateAccountFromRequest($request);
        $this->updateModel($request, $old, $new, AccountMapper::class, 'account');

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
     * @since  1.0.0
     */
    private function updateAccountFromRequest(RequestAbstract $request, bool $allowPassword = false) : Account
    {
        $account = AccountMapper::get((int) ($request->getData('id')));
        $account->setName((string) ($request->getData('login') ?? $account->getName()));
        $account->setName1((string) ($request->getData('name1') ?? $account->getName1()));
        $account->setName2((string) ($request->getData('name2') ?? $account->getName2()));
        $account->setName3((string) ($request->getData('name3') ?? $account->getName3()));
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
     * @since  1.0.0
     */
    public function apiModuleStatusUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $module = $request->getData('module');
        $status = (int) $request->getData('status');

        if (empty($module) || empty($status)) {
            $response->set($request->getUri()->__toString(), [
                'status' => 'warning',
                'title' => 'Module',
                'message' => 'Invalid module or status',
                'response' => [],
            ]);

            $response->getHeader()->setStatusCode(RequestStatusCode::R_403);
            return;
        }

        $this->app->eventManager->trigger('PRE:Module:Admin-module-status', '', ['status' => $status, 'module' => $module]);
        switch ($status) {
            case ModuleStatusUpdateType::ACTIVATE:
                $done = $module === 'Admin' ? false : $this->app->moduleManager->activate($module);
                $msg  = $done ? 'Module successfully activated.' : 'Module not activated.';

                break;
            case ModuleStatusUpdateType::DEACTIVATE:
                $done = $module === 'Admin' ? false : $this->app->moduleManager->deactivate($module);
                $msg  = $done ? 'Module successfully deactivated.' : 'Module not deactivated.';

                break;
            case ModuleStatusUpdateType::INSTALL:
                $done = $module === 'Admin' ? false : $this->app->moduleManager->install($module);
                $msg  = $done ? 'Module successfully installed.' : 'Module not installed.';

                break;
            case ModuleStatusUpdateType::UNINSTALL:
                $done = $module === 'Admin' ? false : $this->app->moduleManager->uninstall($module);
                $msg  = $done ? 'Module successfully uninstalled.' : 'Module not uninstalled.';

                break;
            default:
                $done = false;
                $msg  = 'Unknown module status change request.';
        }
        $this->app->eventManager->trigger('POST:Module:Admin-module-status', '', ['status' => $status, 'module' => $module]);

        $this->fillJsonResponse($request, $response, $done ? NotificationLevel::OK : NotificationLevel::WARNING, 'Module', $msg, []);
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
     * @since  1.0.0
     */
    public function apiAddGroupPermission(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validatePermissionCreate($request))) {
            $response->set('permission_create', new FormValidation($val));

            return;
        }

        $permission = $this->createPermissionFromRequest($request);

        if (!($permission instanceof GroupPermission)) {
            $response->set('permission_create', new FormValidation($val));

            return;
        }

        $this->createModel($request, $permission, GroupPermissionMapper::class, 'group-permission');
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
     * @since  1.0.0
     */
    public function apiAddAccountPermission(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validatePermissionCreate($request))) {
            $response->set('permission_create', new FormValidation($val));

            return;
        }

        $permission = $this->createPermissionFromRequest($request);

        if (!($permission instanceof AccountPermission)) {
            $response->set('permission_create', new FormValidation($val));

            return;
        }

        $this->createModel($request, $permission, AccountPermissionMapper::class, 'account-permission');
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Account', 'Account permission successfully created', $permission);
    }

    /**
     * Validate permission create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since  1.0.0
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
     * @since  1.0.0
     */
    public function createPermissionFromRequest(RequestAbstract $request) : PermissionAbstract
    {
        $permission = ((int) $request->getData('permissionowner')) === PermissionOwner::GROUP ? new GroupPermission((int) $request->getData('permissionref')) : new AccountPermission((int) $request->getData('permissionref'));
        $permission->setUnit(empty($request->getData('permissionunit')) ? null : (int) $request->getData('permissionunit'));
        $permission->setApp(empty($request->getData('permissionapp')) ? null : (string) $request->getData('permissionapp'));
        $permission->setModule(empty($request->getData('permissionmodule')) ? null : (string) $request->getData('permissionmodule'));
        $permission->setType(empty($request->getData('permissiontype')) ? null : (int) $request->getData('permissiontype'));
        $permission->setElement(empty($request->getData('permissionelement')) ? null : (int) $request->getData('permissionelement'));
        $permission->setComponent(empty($request->getData('permissioncomponent')) ? null : (int) $request->getData('permissioncomponent'));
        $permission->setPermission(
            (int) $request->getData('permissioncreate')
            | (int) $request->getData('permissionread')
            | (int) $request->getData('permissionupdate')
            | (int) $request->getData('permissiondelete')
            | (int) $request->getData('permissionpermission')
        );

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
     * @since  1.0.0
     */
    public function apiAddGroupToAccount(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $account = (int) $request->getData('account');
        $groups  = \array_map('intval', $request->getDataList('igroup-idlist'));

        $this->createModelRelation($request, $account, $groups, AccountMapper::class, 'groups', 'account-group');
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
     * @since  1.0.0
     */
    public function apiAddAccountToGroup(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $group    = (int) $request->getData('group');
        $accounts = \array_map('intval', $request->getDataList('iaccount-idlist'));

        $this->createModelRelation($request, $group, $accounts, GroupMapper::class, 'accounts', 'group-account');
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Group', 'Relation added', []);
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
     * @since  1.0.0
     */
    public function apiReInit(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $p           = __DIR__ . '/../../../Web/*';
        $directories = \glob(__DIR__ . '/../../../Web/*' , \GLOB_ONLYDIR);

        foreach ($directories as $directory) {
            if (\file_exists($path = $directory . '/Routes.php')) {
                \file_put_contents($path, '<?php return [];');
            }

            if (\file_exists($path = $directory . '/Hooks.php')) {
                \file_put_contents($path, '<?php return [];');
            }
        }

        if (\file_exists($path = __DIR__ . '/../../../Console/Routes.php')) {
            \file_put_contents($path, '<?php return [];');
        }

        if (\file_exists($path = __DIR__ . '/../../../Socket/Routes.php')) {
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
     * @since  1.0.0
     */
    public function apiCheckForUpdates(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        // this is only a temp... in the future this logic will change but for current purposes this is the easiest way to implement updates
        $request = new Request(new Http('https://api.github.com/repos/Orange-Management/Updates/contents'));
        $request->setMethod(RequestMethod::GET);
        $request->getHeader()->set('User-Agent', 'spl1nes');

        $updateFilesJson = Rest::request($request)->getJsonData();

        /** @var array<string, array<string, mixed>> */
        $toUpdate = [];

        foreach ($updateFilesJson as $file) {
            $name = \explode('_', $file['name']);
            $path = '';

            if (\file_exists(__DIR__ . '/../../../' . $name[0])) {
                $path = __DIR__ . '/../../../' . $name[0];
            } elseif (\file_exists(__DIR__ . '/../../' . $name[0])) {
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
     * @since  1.0.0
     */
    public function apiUpdateFile(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $this->apiUpdate([[
            'name'         => 'temp.json',
            'download_url' => 'https://raw.githubusercontent.com/Orange-Management/' . ($request->getData('url') ?? ''),
        ]]);
    }

    private function apiUpdate(array $toUpdate) : void
    {
        // this is only a temp... in the future this logic will change but for current purposes this is the easiest way to implement updates

        foreach ($toUpdate as $update) {
            $dest = __DIR__ . '/../Updates/' . \explode('.', $update['name'])[0];
            \mkdir($dest);
            $this->downloadUpdate($update['download_url'], $dest . '/' . $update['name']);
            $this->runUpdate($dest . '/' . $update['name']);
            $this->cleanupUpdate(__DIR__ . '/../Updates/' . $dest);
        }
    }

    private function downloadUpdate(string $url, string $dest) : void
    {
        // this is only a temp... in the future this logic will change but for current purposes this is the easiest way to implement updates
        $request = new Request(new Http($url));
        $request->setMethod(RequestMethod::GET);

        $updateFile = Rest::request($request)->getBody();
        File::put($dest, $updateFile);
    }

    private function runUpdate(string $updateFile) : void
    {
        $updateContent = \file_get_contents($updateFile);
        if ($updateContent === false) {
            return;
        }

        $package    = \json_decode($updateContent, true);
        $updatePath = \dirname($updateFile);
        $subPath    = $package['type'] === 'Modules/' ? $package['name'] : '';

        foreach ($package['add'] as $src => $dest) {
            $src  = StringUtils::startsWith($src, 'http') ? $src : $updatePath . '/' . $subPath . $src;
            $dest = __DIR__ . '/../../../' . $package['type'] . '/' . $subPath . $dest;

            $this->updateAdd($src, $dest);
        }

        foreach ($package['remove'] as $src => $dest) {
            $dest = __DIR__ . '/../../../' . $package['type'] . '/' . $subPath . $dest;

            $this->updateRemove($dest);
        }

        foreach ($package['replace'] as $src => $dest) {
            $src  = StringUtils::startsWith($src, 'http') ? $src : $updatePath . '/' . $subPath . $src;
            $dest = __DIR__ . '/../../../' . $package['type'] . '/' . $subPath . $dest;

            $this->updateReplace($src, $dest);
        }

        foreach ($package['move'] as $src => $dest) {
            $src  = __DIR__ . '/../../../' . $package['type'] . '/' . $subPath . $src;
            $dest = __DIR__ . '/../../../' . $package['type'] . '/' . $subPath . $dest;

            $this->updateMove($src, $dest);
        }

        foreach ($package['run'] as $dest) {
            $dest = $updatePath . '/' . $dest;

            $this->updateRun($dest);
        }

        foreach ($package['cmd'] as $cmd) {
            $this->updateCmd($cmd);
        }
    }

    private function updateAdd(string $src, string $dest) : void
    {
        if (StringUtils::startsWith($src, 'http')) {
            $request = new Request(new Http($src));
            $request->setMethod(RequestMethod::GET);

            $content = Rest::request($request)->getBody();
            File::put($dest, $content, ContentPutMode::CREATE);
        } else {
            if (\is_dir($src)) {
                Directory::copy($src, $dest);
            } else {
                File::copy($src, $dest);
            }
        }
    }

    private function updateRemove(string $dest) : void
    {
        if (\is_dir($dest)) {
            Directory::delete($dest);
        } else {
            File::delete($dest);
        }
    }

    private function updateReplace(string $src, string $dest) : void
    {
        $this->updateRemove($dest);
        $this->updateAdd($src, $dest);
    }

    private function updateMove(string $src, string $dest) : void
    {
        $this->updateAdd($src, $dest);
        $this->updateRemove($src);
    }

    private function updateRun(string $dest) : void
    {

    }

    private function updateCmd(string $cmd) : void
    {

    }

    private function cleanupUpdate(string $path) : void
    {
        // this is only a temp... in the future this logic will change but for current purposes this is the easiest way to implement updates
        Directory::delete($path);
    }
}
