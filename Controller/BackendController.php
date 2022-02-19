<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Admin\Controller;

use Model\NullSetting;
use Model\SettingMapper;
use Model\SettingsEnum;
use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\AccountPermissionMapper;
use Modules\Admin\Models\GroupMapper;
use Modules\Admin\Models\GroupPermissionMapper;
use Modules\Admin\Models\LocalizationMapper;
use Modules\Admin\Models\NullAccountPermission;
use Modules\Admin\Models\NullGroupPermission;
use Modules\Auditor\Models\AuditMapper;
use phpOMS\Contract\RenderableInterface;
use phpOMS\Localization\NullLocalization;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Utils\Parser\Markdown\Markdown;
use phpOMS\Utils\StringUtils;
use phpOMS\Views\View;

/**
 * Admin controller class.
 *
 * This class is responsible for the basic admin activities such as managing accounts, groups, permissions and modules.
 *
 * @package Modules\Admin
 * @license OMS License 1.0
 * @link    https://karaka.app
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
            $view->setData('accounts', AccountMapper::getAll()->where('id', (int) ($request->getData('id') ?? 0), '<')->limit(25)->execute());
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('accounts', AccountMapper::getAll()->where('id', (int) ($request->getData('id') ?? 0), '>')->limit(25)->execute());
        } else {
            $view->setData('accounts', AccountMapper::getAll()->where('id', 0, '>')->limit(25)->execute());
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

        /** @var \Modules\Admin\Models\Account $account */
        $account = AccountMapper::get()->with('groups')->with('l11n')->where('id', (int) $request->getData('id'))->execute();
        if ($account->l11n instanceof NullLocalization) {
            $account->l11n->loadFromLanguage($request->getLanguage());
        }

        $view->addData('account', $account);

        $permissions = AccountPermissionMapper::getAll()->where('account', (int) $request->getData('id'))->execute();

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
            $view->setData('auditlogs',
                    AuditMapper::getAll()->with('createdBy')->where('id', (int) ($request->getData('audit') ?? 0), '<')->limit(25)->execute()
                );
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('auditlogs',
                    AuditMapper::getAll()->with('createdBy')->where('id', (int) ($request->getData('audit') ?? 0), '>')->limit(25)->execute()
                );
        } else {
            $view->setData('auditlogs',
                    AuditMapper::getAll()->with('createdBy')->where('id', 0, '>')->limit(25)->execute()
                );
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
            $view->setData('groups', GroupMapper::getAll()->with('createdBy')->where('id', (int) ($request->getData('id') ?? 0), '<')->limit(25)->execute());
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('groups', GroupMapper::getAll()->with('createdBy')->where('id', (int) ($request->getData('id') ?? 0), '>')->limit(25)->execute());
        } else {
            $view->setData('groups', GroupMapper::getAll()->where('id', 0, '>')->limit(25)->execute());
        }

        $memberCount = GroupMapper::countMembers();
        $view->setData('memberCount', $memberCount);

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
        $view->addData('group',
            GroupMapper::get()->with('accounts')->where('id', (int) $request->getData('id'))->execute()
        );

        $permissions = GroupPermissionMapper::getAll()->where('group', (int) $request->getData('id'))->execute();

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

        $mapperQuery = AuditMapper::getAll()
            ->with('createdBy')
            ->where('module', self::NAME)
            ->where('type', StringUtils::intHash(GroupMapper::class))
            ->where('ref', (string) ($request->getData('id') ?? '0'))
            ->limit(25);

        // audit log
        if ($request->getData('ptype') === 'p') {
            $view->setData('auditlogs', $mapperQuery->where('id', (int) ($request->getData('audit') ?? 0), '<')->limit(25)->execute());
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('auditlogs', $mapperQuery->where('id', (int) ($request->getData('audit') ?? 0), '>')->limit(25)->execute());
        } else {
            $view->setData('auditlogs', $mapperQuery->where('id', 0, '>')->limit(25)->execute());
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
    public function viewModuleInfo(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-info');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000105001, $request, $response));

        $id = $request->getData('id') ?? '';
        $view->setData('modules', $this->app->moduleManager->getAllModules());
        $view->setData('active', $this->app->moduleManager->getActiveModules());
        $view->setData('installed',$this->app->moduleManager->getInstalledModules());
        $view->setData('id', $id);

        $type     = 'Help';
        $page     = 'introduction';
        $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/' . $request->getLanguage();
        $path     = \realpath($basePath . '/' . $page . '.md');

        if ($path === false) {
            $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/' . $this->app->l11nServer->getLanguage();
            $path     = \realpath($basePath . '/' . $page . '.md');
        }

        if ($path === false) {
            $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/en';
            $path     = \realpath($basePath . '/' . $page . '.md');
        }

        if ($path === false) {
            $path = \realpath($basePath . '/introduction.md');
        }

        $toParse = $path === false ? '' : \file_get_contents($path);
        $content = Markdown::parse($toParse === false ? '' : $toParse);

        $view->setData('introduction', $content);

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
    public function viewModuleLog(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-log');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000105001, $request, $response));

        $id = (string) ($request->getData('id') ?? '');

        // audit log
        if ($request->getData('ptype') === 'p') {
            $view->setData('auditlogs', AuditMapper::getAll()->where('module', $id)->where('id', (int) $request->getData('audit'), '<')->limit(25)->execute());
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('auditlogs', AuditMapper::getAll()->where('module', $id)->where('id', (int) $request->getData('audit'), '>')->limit(25)->execute());
        } else {
            $view->setData('auditlogs', AuditMapper::getAll()->where('module', $id)->where('id', 0, '>')->limit(25)->execute());
        }

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
    public function viewModuleRouteList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-route-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000105001, $request, $response));

        $module = $request->getData('id') ?? '';
        $view->setData('module', $module);

        $appPath      = __DIR__ . '/../../../Web';
        $activeRoutes = [];
        $apps         = \scandir($appPath);

        foreach ($apps as $app) {
            if (!\is_file(__DIR__ . '/../../../Web/' . $app . '/Routes.php')) {
                continue;
            }

            $activeRoutes[$app] = include __DIR__ . '/../../../Web/' . $app . '/Routes.php';
        }

        $view->setData('routes', $activeRoutes);

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
    public function viewModuleSettings(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1000105001, $request, $response));

        $id = $request->getData('id') ?? '';

        $settings = SettingMapper::getAll()->where('module', $id)->execute();
        if (!($settings instanceof NullSetting)) {
            $view->setData('settings', !\is_array($settings) ? [$settings] : $settings);
        }

        if (\is_file(__DIR__ . '/../Admin/Settings/Theme/Backend/settings.tpl.php')) {
            $view->setTemplate('/Modules/' . static::NAME . '/Admin/Settings/Theme/Backend/settings');
        } else {
            $view->setTemplate('/Modules/Admin/Theme/Backend/modules-settings');
        }

        $generalSettings = $this->app->appSettings->get(null, [
                SettingsEnum::PASSWORD_PATTERN, SettingsEnum::LOGIN_TIMEOUT, SettingsEnum::PASSWORD_INTERVAL, SettingsEnum::PASSWORD_HISTORY, SettingsEnum::LOGIN_TRIES, SettingsEnum::LOGGING_STATUS, SettingsEnum::LOGGING_PATH, SettingsEnum::DEFAULT_ORGANIZATION,
                SettingsEnum::LOGIN_STATUS, SettingsEnum::DEFAULT_LOCALIZATION, SettingsEnum::MAIL_SERVER_ADDR,
            ]);

        $view->setData('generalSettings', $generalSettings);
        $view->setData('defaultlocalization', LocalizationMapper::get()->where('id', (int) $generalSettings[SettingsEnum::DEFAULT_LOCALIZATION]->content)->execute());

        return $view;
    }
}
