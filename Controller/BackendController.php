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

use Model\SettingsEnum;
use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\AccountPermissionMapper;
use Modules\Admin\Models\GroupMapper;
use Modules\Admin\Models\GroupPermissionMapper;
use Modules\Admin\Models\LocalizationMapper;
use Modules\Admin\Models\NullAccountPermission;
use Modules\Admin\Models\NullGroupPermission;
use Modules\Auditor\Models\Audit;
use Modules\Auditor\Models\AuditMapper;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\RelationType;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Module\ModuleInfo;
use phpOMS\Utils\StringUtils;
use phpOMS\Views\View;

/**
 * Admin controller class.
 *
 * This class is responsible for the basic admin activities such as managing accounts, groups, permissions and modules.
 *
 * @package Modules\Admin
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller
{
    /**
     * Method which shows the password forgotten
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewForgot(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        return new View();
    }

    /**
     * Method which generates the general settings view.
     *
     * In this view general settings for the entire application can be seen and adjusted. Settings which can be modified
     * here are localization, password, database, etc.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewSettingsGeneral(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view     = new View($this->app->l11nManager, $request, $response);
        $settings = $this->app->appSettings->get(null, [
                SettingsEnum::PASSWORD_PATTERN, SettingsEnum::LOGIN_TIMEOUT, SettingsEnum::PASSWORD_INTERVAL, SettingsEnum::PASSWORD_HISTORY, SettingsEnum::LOGIN_TRIES, SettingsEnum::LOGGING_STATUS, SettingsEnum::LOGGING_PATH, SettingsEnum::DEFAULT_ORGANIZATION,
                SettingsEnum::LOGIN_STATUS, SettingsEnum::DEFAULT_LOCALIZATION, SettingsEnum::ADMIN_MAIL,
            ]);

        $view->setTemplate('/Modules/Admin/Theme/Backend/settings-general');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000104001, $request, $response));
        $view->setData('settings', $settings);
        $view->setData('defaultlocalization', LocalizationMapper::get((int) $settings[SettingsEnum::DEFAULT_LOCALIZATION]));

        return $view;
    }

    /**
     * Method which generates the general settings view.
     *
     * In this view general settings for the entire application can be seen and adjusted. Settings which can be modified
     * here are localization, password, database, etc.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewEmptyCommand(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Console/empty-command');

        return $view;
    }

    /**
     * Method which generates the account list view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewAccountList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/accounts-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000104001, $request, $response));

        if ($request->getData('ptype') === 'p') {
            $view->setData('accounts', AccountMapper::getBeforePivot((int) ($request->getData('id') ?? 0), null, 25));
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('accounts', AccountMapper::getAfterPivot((int) ($request->getData('id') ?? 0), null, 25));
        } else {
            $view->setData('accounts', AccountMapper::getAfterPivot(0, null, 25));
        }

        return $view;
    }

    /**
     * Method which generates the account view of a single account.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewAccountSettings(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/accounts-single');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000104001, $request, $response));
        $view->addData('account', AccountMapper::get((int) $request->getData('id'), RelationType::ALL, 2));

        $permissions = AccountPermissionMapper::getFor((int) $request->getData('id'), 'account');

        if (!isset($permissions) || $permissions instanceof NullAccountPermission) {
            $permissions = [];
        } elseif (!\is_array($permissions)) {
            $permissions = [$permissions];
        }

        $view->addData('permissions', $permissions);

        $accGrpSelector = new \Modules\Admin\Theme\Backend\Components\GroupTagSelector\GroupTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('grpSelector', $accGrpSelector);

        // audit log
        if ($request->getData('ptype') === 'p') {
            $view->setData('auditlogs', AuditMapper::withConditional('createdBy', (int) $request->getData('id'), [Audit::class])::getBeforePivot((int) $request->getData('audit'), null, 25));
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('auditlogs', AuditMapper::withConditional('createdBy', (int) $request->getData('id'), [Audit::class])::getAfterPivot((int) $request->getData('audit'), null, 25));
        } else {
            $view->setData('auditlogs', AuditMapper::withConditional('createdBy', (int) $request->getData('id'), [Audit::class])::getAfterPivot(0, null, 25));
        }

        return $view;
    }

    /**
     * Method which generates the create account view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewAccountCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/accounts-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000104001, $request, $response));

        return $view;
    }

    /**
     * Method which generates the group list view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewGroupList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/groups-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000103001, $request, $response));

        if ($request->getData('ptype') === 'p') {
            $view->setData('groups', GroupMapper::getBeforePivot((int) ($request->getData('id') ?? 0), null, 25));
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('groups', GroupMapper::getAfterPivot((int) ($request->getData('id') ?? 0), null, 25));
        } else {
            $view->setData('groups', GroupMapper::getAfterPivot(0, null, 25));
        }

        return $view;
    }

    /**
     * Method which generates the group view of a single group.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewGroupSettings(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/groups-single');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000103001, $request, $response));
        $view->addData('group', GroupMapper::get((int) $request->getData('id'), RelationType::ALL, 2));

        $permissions = GroupPermissionMapper::getFor((int) $request->getData('id'), 'group');

        if (!isset($permissions) || $permissions instanceof NullGroupPermission) {
            $permissions = [];
        } elseif (!\is_array($permissions)) {
            $permissions = [$permissions];
        }

        $view->addData('permissions', $permissions);

        $accGrpSelector = new \Modules\Profile\Theme\Backend\Components\AccountGroupSelector\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('accGrpSelector', $accGrpSelector);

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('editor', $editor);

        // audit log
        if ($request->getData('ptype') === 'p') {
            $view->setData('auditlogs',
                AuditMapper::withConditional('module', self::MODULE_NAME, [Audit::class])
                    ::withConditional('type', StringUtils::intHash(GroupMapper::class), [Audit::class])
                    ::withConditional('ref', (string) $request->getData('id') ?? '0', [Audit::class])
                    ::getBeforePivot((int) $request->getData('audit'), null, 25));
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('auditlogs',
                AuditMapper::withConditional('module', self::MODULE_NAME, [Audit::class])
                    ::withConditional('type', StringUtils::intHash(GroupMapper::class), [Audit::class])
                    ::withConditional('ref', (string) $request->getData('id') ?? '0', [Audit::class])
                    ::getAfterPivot((int) $request->getData('audit'), null, 25));
        } else {
            $view->setData('auditlogs',
                AuditMapper::withConditional('module', self::MODULE_NAME, [Audit::class])
                    ::withConditional('type', StringUtils::intHash(GroupMapper::class), [Audit::class])
                    ::withConditional('ref', (string) $request->getData('id') ?? '0', [Audit::class])
                    ::getAfterPivot(0, null, 25));
        }

        return $view;
    }

    /**
     * Method which generates the group create view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewGroupCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/groups-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000103001, $request, $response));

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('editor', $editor);

        return $view;
    }

    /**
     * Method which generates the module list view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewModuleList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-list');

        $view->setData('modules', $this->app->moduleManager->getAllModules());
        $view->setData('active', $this->app->moduleManager->getActiveModules());
        $view->setData('installed', $this->app->moduleManager->getInstalledModules());

        return $view;
    }

    /**
     * Method which generates the module profile view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewModuleProfile(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-single');

        $id = $request->getData('id') ?? '';
        $view->setData('modules', $this->app->moduleManager->getAllModules());
        $view->setData('active', $this->app->moduleManager->getActiveModules());
        $view->setData('installed', $installed = $this->app->moduleManager->getInstalledModules());
        $view->setData('id', $id);

        $path = \realpath(__DIR__ . '/../' . $id . '/info.json');

        if (isset($installed[$id]) && $path !== false) {
            $info = new ModuleInfo($path);
            $info->load();

            $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(
                $info->getId(),
                $request, $response
            ));
        }

        $groupPermission = GroupMapper::getPermissionForModule($id);
        $view->setData('groupPermissions', $groupPermission);

        // audit log
        if ($request->getData('ptype') === 'p') {
            $view->setData('auditlogs', AuditMapper::withConditional('module', (string) $request->getData('id'), [Audit::class])::getBeforePivot((int) $request->getData('audit'), null, 25));
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('auditlogs', AuditMapper::withConditional('module', (string) $request->getData('id'), [Audit::class])::getAfterPivot((int) $request->getData('audit'), null, 25));
        } else {
            $view->setData('auditlogs', AuditMapper::withConditional('module', (string) $request->getData('id'), [Audit::class])::getAfterPivot(0, null, 25));
        }

        return $view;
    }
}
