<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use Modules\Admin\Controller\BackendController;
use Modules\Admin\Models\PermissionState;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/forgot.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewForgot',
            'verb'       => RouteVerb::GET,
            'permission' => [
            ],
        ],
    ],

    '^.*/admin/module/settings\?id=Admin.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleSettings',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => \Modules\Admin\Models\PermissionState::MODULE,
            ],
        ],
    ],

    '^.*/admin/account/list.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewAccountList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::ACCOUNT,
            ],
        ],
    ],
    '^.*/admin/account/settings.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewAccountSettings',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::ACCOUNT,
            ],
        ],
    ],
    '^.*/admin/account/create.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewAccountCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::ACCOUNT,
            ],
        ],
    ],
    '^.*/admin/group/list.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewGroupList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::GROUP,
            ],
        ],
    ],
    '^.*/admin/group/settings.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewGroupSettings',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionState::GROUP,
            ],
        ],
    ],
    '^.*/admin/group/create.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewGroupCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::GROUP,
            ],
        ],
    ],
    '^.*/admin/module/list.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::MODULE,
            ],
        ],
    ],
    '^.*/admin/module/info\?.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleInfo',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::MODULE,
            ],
        ],
    ],
    '^.*/admin/module/log\?.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleLog',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::MODULE,
            ],
        ],
    ],
    '^.*/admin/module/route/list\?.*$' => [
        [
            'dest'       => '\Modules\Admin\Controller\BackendController:viewModuleRouteList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::MODULE,
            ],
        ],
    ],
];
