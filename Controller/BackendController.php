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

use Model\SettingMapper;
use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\AccountPermissionMapper;
use Modules\Admin\Models\AppMapper;
use Modules\Admin\Models\GroupMapper;
use Modules\Admin\Models\GroupPermissionMapper;
use Modules\Admin\Models\ModuleMapper;
use Modules\Admin\Models\SettingsEnum;
use Modules\Auditor\Models\AuditMapper;
use Modules\Media\Models\MediaMapper;
use Modules\Organization\Models\UnitMapper;
use phpOMS\Autoloader;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Utils\Parser\Markdown\Markdown;
use phpOMS\Utils\StringUtils;
use phpOMS\Views\View;
use Web\Backend\Views\TableView;

/**
 * Admin controller class.
 *
 * This class is responsible for the basic admin activities such as managing accounts, groups, permissions and modules.
 *
 * @package Modules\Admin
 * @license OMS License 2.0
 * @link    https://jingga.app
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
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewForgot(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
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
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewEmptyCommand(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Cli/empty-command');

        return $view;
    }

    /**
     * Method which generates the account list view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewAccountList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/accounts-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000104001, $request, $response);

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member = \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if ($request->hasData('accountslist-f-' . $member . '-f1')) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('accountslist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('accountslist-f-' . $member . '-o1'),
                    'value2' => $request->getData('accountslist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('accountslist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit               = 25;
        $view->data['pageLimit'] = $pageLimit;

        $mapper = AccountMapper::getAll()->with('createdBy');
        $list   = AccountMapper::find(
            search: $request->getDataString('search'),
            mapper: $mapper,
            id: $request->getDataInt('id') ?? 0,
            secondaryId: $request->getDataString('subid') ?? '',
            type: $request->getDataString('pType'),
            pageLimit: empty($request->getDataInt('limit') ?? 0) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getDataString('sort_by') ?? '',
            sortOrder: $request->getDataString('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->data['accounts'] = $list['data'];

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Admin';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/Backend/Themes/table-title');
        $tableView->setExportTemplate('/Web/Backend/Themes/popup-export-data');
        $tableView->setExportTemplates([]);
        $tableView->setColumnHeaderElementTemplate('/Web/Backend/Themes/header-element-table');
        $tableView->setFilterTemplate('/Web/Backend/Themes/popup-filter-table');
        $tableView->setSortTemplate('/Web/Backend/Themes/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->data['tableView'] = $tableView;

        return $view;
    }

    /**
     * Method which generates the account view of a single account.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewAccountSettings(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/accounts-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000104001, $request, $response);

        /** @var \Modules\Admin\Models\Account $account */
        $account = AccountMapper::get()
            ->with('groups')
            ->with('l11n')
            ->where('id', (int) $request->getData('id'))
            ->execute();

        if ($account->l11n->id === 0) {
            $account->l11n->loadFromLanguage($request->header->l11n->language);
        }

        $view->data['account'] = $account;

        /** @var \Modules\Admin\Models\AccountPermission[] $permissions */
        $permissions = AccountPermissionMapper::getAll()
            ->where('account', (int) $request->getData('id'))
            ->execute();

        $view->data['permissions'] = $permissions;

        $view->data['units']   = UnitMapper::getAll()->execute();
        $view->data['apps']    = AppMapper::getAll()->execute();
        $view->data['modules'] = ModuleMapper::getAll()->execute();

        $accGrpSelector            = new \Modules\Admin\Theme\Backend\Components\GroupTagSelector\GroupTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['grpSelector'] = $accGrpSelector;

        // Auditor log
        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member = \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if ($request->hasData('auditlist-f-' . $member . '-f1')) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit               = 25;
        $view->data['pageLimit'] = $pageLimit;

        $mapper = AuditMapper::getAll()->with('createdBy');

        /** @var \Modules\Auditor\Models\Audit[] $list */
        $list = AuditMapper::find(
            search: $request->getDataString('search'),
            mapper: $mapper,
            id: $request->getDataInt('id') ?? 0,
            secondaryId: $request->getDataString('subid') ?? '',
            type: $request->getDataString('pType'),
            pageLimit: empty($request->getDataInt('limit') ?? 0) ? 100 : $request->getDataInt('limit'),
            sortBy: $request->getDataString('sort_by') ?? '',
            sortOrder: $request->getDataString('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->data['audits'] = $list['data'];

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/Backend/Themes/table-title');
        $tableView->setExportTemplate('/Web/Backend/Themes/popup-export-data');
        $tableView->setExportTemplates([]);
        $tableView->setColumnHeaderElementTemplate('/Web/Backend/Themes/header-element-table');
        $tableView->setFilterTemplate('/Web/Backend/Themes/popup-filter-table');
        $tableView->setSortTemplate('/Web/Backend/Themes/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->data['tableView'] = $tableView;

        return $view;
    }

    /**
     * Method which generates the create account view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewAccountCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/accounts-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000104001, $request, $response);

        return $view;
    }

    /**
     * Method which generates the group list view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewGroupList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/groups-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000103001, $request, $response);

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member = \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if ($request->hasData('groupslist-f-' . $member . '-f1')) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('groupslist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('groupslist-f-' . $member . '-o1'),
                    'value2' => $request->getData('groupslist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('groupslist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit               = 25;
        $view->data['pageLimit'] = $pageLimit;

        $mapper = GroupMapper::getAll()->with('createdBy');
        $list   = GroupMapper::find(
            search: $request->getDataString('search'),
            mapper: $mapper,
            id: $request->getDataInt('id') ?? 0,
            secondaryId: $request->getDataString('subid') ?? '',
            type: $request->getDataString('pType'),
            pageLimit: empty($request->getDataInt('limit') ?? 0) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getDataString('sort_by') ?? '',
            sortOrder: $request->getDataString('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->data['groups'] = $list['data'];

        $memberCount               = GroupMapper::countMembers();
        $view->data['memberCount'] = $memberCount;

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Admin';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/Backend/Themes/table-title');
        $tableView->setExportTemplate('/Web/Backend/Themes/popup-export-data');
        $tableView->setExportTemplates([]);
        $tableView->setColumnHeaderElementTemplate('/Web/Backend/Themes/header-element-table');
        $tableView->setFilterTemplate('/Web/Backend/Themes/popup-filter-table');
        $tableView->setSortTemplate('/Web/Backend/Themes/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->data['tableView'] = $tableView;

        return $view;
    }

    /**
     * Method which generates the group view of a single group.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewGroupSettings(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/groups-view');

        $view->data['nav'] = $this->app->moduleManager->get('Navigation')
            ->createNavigationMid(1000103001, $request, $response);

        $view->data['group'] = GroupMapper::get()
            ->with('accounts')
            ->where('id', (int) $request->getData('id'))
            ->execute();

        /** @var \Modules\Admin\Models\GroupPermission[] $permissions */
        $permissions = GroupPermissionMapper::getAll()
            ->where('group', (int) $request->getData('id'))
            ->execute();

        $view->data['permissions'] = $permissions;

        $view->data['units']   = UnitMapper::getAll()->execute();
        $view->data['apps']    = AppMapper::getAll()->execute();
        $view->data['modules'] = ModuleMapper::getAll()->execute();

        $accGrpSelector               = new \Modules\Profile\Theme\Backend\Components\AccountGroupSelector\BaseView($this->app->l11nManager, $request, $response);
        $view->data['accGrpSelector'] = $accGrpSelector;

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        $mapperQuery = AuditMapper::getAll()
            ->with('createdBy')
            ->where('module', self::NAME)
            ->where('type', StringUtils::intHash(GroupMapper::class))
            ->where('ref', $request->getDataString('id') ?? '0')
            ->limit(25);

        // audit log
        if ($request->getData('ptype') === 'p') {
            $view->data['auditlogs'] = $mapperQuery->where('id', $request->getDataInt('audit') ?? 0, '<')->limit(25)->execute();
        } elseif ($request->getData('ptype') === 'n') {
            $view->data['auditlogs'] = $mapperQuery->where('id', $request->getDataInt('audit') ?? 0, '>')->limit(25)->execute();
        } else {
            $view->data['auditlogs'] = $mapperQuery->where('id', 0, '>')->limit(25)->execute();
        }

        return $view;
    }

    /**
     * Method which generates the group create view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewGroupCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/groups-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000103001, $request, $response);

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Method which generates the module list view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewModuleList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-list');

        $view->data['modules']   = $this->app->moduleManager->getAllModules();
        $view->data['active']    = $this->app->moduleManager->getActiveModules();
        $view->data['installed'] = $this->app->moduleManager->getInstalledModules();

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Admin';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/Backend/Themes/table-title');
        $tableView->setExportTemplate('/Web/Backend/Themes/popup-export-data');
        $tableView->setExportTemplates([]);
        $tableView->setColumnHeaderElementTemplate('/Web/Backend/Themes/header-element-table');
        $tableView->setFilterTemplate('/Web/Backend/Themes/popup-filter-table');
        $tableView->setSortTemplate('/Web/Backend/Themes/sort-table');

        $view->data['tableView'] = $tableView;

        return $view;
    }

    /**
     * Method which generates the module profile view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewModuleInfo(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-info');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000105001, $request, $response);

        $id                      = $request->getDataString('id') ?? '';
        $view->data['modules']   = $this->app->moduleManager->getAllModules();
        $view->data['active']    = $this->app->moduleManager->getActiveModules();
        $view->data['installed'] = $this->app->moduleManager->getInstalledModules();
        $view->data['id']        = $id;

        $type     = 'Help';
        $page     = 'introduction';
        $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/' . $request->header->l11n->language;
        $path     = \realpath($basePath . '/' . $page . '.md');

        if ($path === false) {
            $basePath = __DIR__ . '/../../' . $request->getData('id') . '/Docs/' . $type . '/' . $this->app->l11nServer->language;
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

        $view->data['introduction'] = $content;

        return $view;
    }

    /**
     * Method which generates the module profile view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewModuleLog(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-log');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000105001, $request, $response);

        $id = $request->getDataString('id') ?? '';

        $queryMapper = AuditMapper::getAll()
            ->with('createdBy')
            ->where('module', $id);

        // audit log
        if ($request->getData('ptype') === 'p') {
            $view->data['auditlogs'] = $queryMapper->where('id', (int) $request->getData('audit'), '<')->limit(25)->execute();
        } elseif ($request->getData('ptype') === 'n') {
            $view->data['auditlogs'] = $queryMapper->where('id', (int) $request->getData('audit'), '>')->limit(25)->execute();
        } else {
            $view->data['auditlogs'] = $queryMapper->where('id', 0, '>')->limit(25)->execute();
        }

        return $view;
    }

    /**
     * Method which generates the module profile view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewModuleRouteList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-route-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000105001, $request, $response);

        $module               = $request->getDataString('id') ?? '';
        $view->data['module'] = $module;

        $appPath      = __DIR__ . '/../../../Web';
        $activeRoutes = [];

        $apps = \scandir($appPath);
        if ($apps === false) {
            $apps = [];
        }

        foreach ($apps as $app) {
            if (!\is_file(__DIR__ . '/../../../Web/' . $app . '/Routes.php')) {
                continue;
            }

            $activeRoutes['Web/' . $app] = include __DIR__ . '/../../../Web/' . $app . '/Routes.php';
        }

        if (\is_file(__DIR__ . '/../../../Cli/Routes.php')) {
            $activeRoutes['Cli'] = include __DIR__ . '/../../../Cli/Routes.php';
        }

        if (\is_file(__DIR__ . '/../../../Socket/Routes.php')) {
            $activeRoutes['Socket'] = include __DIR__ . '/../../../Socket/Routes.php';
        }

        $view->data['routes'] = $activeRoutes;

        return $view;
    }

    /**
     * Method which generates the module profile view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewModuleHookList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Backend/modules-hook-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000105001, $request, $response);

        $module               = $request->getDataString('id') ?? '';
        $view->data['module'] = $module;

        $appPath     = __DIR__ . '/../../../Web';
        $activeHooks = [];

        $apps = \scandir($appPath);
        if ($apps === false) {
            $apps = [];
        }

        foreach ($apps as $app) {
            if (!\is_file(__DIR__ . '/../../../Web/' . $app . '/Hooks.php')) {
                continue;
            }

            $activeHooks['Web/' . $app] = include __DIR__ . '/../../../Web/' . $app . '/Hooks.php';
        }

        if (\is_file(__DIR__ . '/../../../Cli/Hooks.php')) {
            $activeHooks['Cli'] = include __DIR__ . '/../../../Cli/Hooks.php';
        }

        if (\is_file(__DIR__ . '/../../../Socket/Hooks.php')) {
            $activeHooks['Socket'] = include __DIR__ . '/../../../Socket/Hooks.php';
        }

        $view->data['hooks'] = $activeHooks;

        return $view;
    }

    /**
     * Method which generates the module profile view.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     */
    public function viewModuleSettings(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view              = new View($this->app->l11nManager, $request, $response);
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000105001, $request, $response);

        $id = $request->getDataString('id') ?? '';

        /** @var \Model\Setting[] $settings */
        $settings = SettingMapper::getAll()->where('module', $id)->execute();
        if (empty($settings)) {
            $view->data['settings'] = $settings;
        }

        $class = '\\Modules\\' . $request->getData('id') . '\\Models\\SettingsEnum';
        if (!Autoloader::exists($class)) {
            $class = null;
        }

        $view->data['settings_class'] = $class;

        if ($request->getData('id') === 'Admin') {
            $view->setTemplate('/Modules/' . $request->getData('id') . '/Admin/Settings/Theme/Backend/settings');
        } elseif (\is_file(__DIR__ . '/../../' . ($request->getDataString('id') ?? '') . '/Admin/Settings/Theme/Backend/settings.tpl.php')) {
            return $this->app->moduleManager->get($request->getDataString('id') ?? '')
                ->viewModuleSettings($request, $response, $data);
        } else {
            $view->setTemplate('/Modules/Admin/Theme/Backend/modules-settings');
        }

        /** @var \Model\Setting[] $generalSettings */
        $generalSettings = $this->app->appSettings->get(
            names: [
                SettingsEnum::PASSWORD_PATTERN, SettingsEnum::LOGIN_TIMEOUT, SettingsEnum::PASSWORD_INTERVAL, SettingsEnum::PASSWORD_HISTORY, SettingsEnum::LOGIN_TRIES, SettingsEnum::LOGGING_STATUS, SettingsEnum::LOGGING_PATH, SettingsEnum::DEFAULT_UNIT,
                SettingsEnum::LOGIN_STATUS, SettingsEnum::MAIL_SERVER_ADDR,
            ],
            module: 'Admin'
        );

        $view->data['generalSettings']      = $generalSettings;
        $view->data['default_localization'] = $this->app->l11nServer;

        return $view;
    }
}
