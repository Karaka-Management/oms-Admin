<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Admin\Controller\BackendController;
use Modules\Admin\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/forgot(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewForgot',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
            ],
        ],
    ],

    '^/admin/module/settings(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleSettings',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => \Modules\Admin\Models\PermissionCategory::MODULE,
            ],
        ],
    ],

    '^/admin/account/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewAccountList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ACCOUNT,
            ],
        ],
    ],
    '^/admin/account/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewAccountView',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ACCOUNT,
            ],
        ],
    ],
    '^/admin/account/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewAccountCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::ACCOUNT,
            ],
        ],
    ],
    '^/admin/group/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewGroupList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::GROUP,
            ],
        ],
    ],
    '^/admin/group/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewGroupView',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::GROUP,
            ],
        ],
    ],
    '^/admin/group/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewGroupCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::GROUP,
            ],
        ],
    ],
    '^/admin/module/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],
    '^/admin/module/info(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleInfo',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],
    '^/admin/module/log(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleLog',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],
    '^/admin/module/route/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleRouteList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],
    '^/admin/module/hook/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleHookList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],
];
