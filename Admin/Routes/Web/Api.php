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

use Modules\Admin\Controller\ApiController;
use Modules\Admin\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/admin/worker(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiDataChange',
            'verb'       => RouteVerb::ANY,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
            ],
        ],
    ],

    '^.*/login(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiLogin',
            'verb'       => RouteVerb::SET,
            'active'     => true,
            'permission' => [
            ],
        ],
    ],
    '^.*/signup(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountRegister',
            'verb'       => RouteVerb::SET,
            'active'     => true,
            'permission' => [
            ],
        ],
    ],

    '^.*/logout(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiLogout',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
            ],
        ],
    ],

    '^.*/forgot(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiForgot',
            'verb'       => RouteVerb::SET,
            'active'     => true,
            'permission' => [
            ],
        ],
    ],
    '^.*/reset(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiResetPassword',
            'verb'       => RouteVerb::SET,
            'active'     => true,
            'permission' => [
            ],
        ],
    ],

    '^.*/admin/settings(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiSettingsSet',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::SETTINGS,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiSettingsGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SETTINGS,
            ],
        ],
    ],

    '^.*/admin/settings/design(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiSettingsDesignSet',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::SETTINGS,
            ],
        ],
    ],

    '^.*/admin/group$' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiGroupCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::GROUP,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiGroupUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::GROUP,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiGroupDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::GROUP,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiGroupGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::GROUP,
            ],
        ],
    ],

    '^.*/admin/find/account(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountFind',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SEARCH,
            ],
        ],
    ],
    '^.*/admin/find/group(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiGroupFind',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SEARCH,
            ],
        ],
    ],
    '^.*/admin/find/accgrp(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountGroupFind',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SEARCH,
            ],
        ],
    ],

    '^.*/admin/account(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::ACCOUNT,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::ACCOUNT,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::ACCOUNT,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ACCOUNT,
            ],
        ],
    ],
    '^.*/admin/localization(\?.*|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiSettingsAccountLocalizationSet',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::ACCOUNT_SETTINGS,
            ],
        ],
    ],

    '^.*/admin/module/status(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiModuleStatusUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],

    '^.*/admin/group/account(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAddAccountToGroup',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],
    '^.*/admin/account/group(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAddGroupToAccount',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],

    '^.*/admin/group/permission(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiGroupPermissionGet',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::PERMISSION,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAddGroupPermission',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::PERMISSION,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiGroupPermissionUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::PERMISSION,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiGroupPermissionDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::PERMISSION,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],
    '^.*/admin/account/permission(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountPermissionGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::PERMISSION,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAddAccountPermission',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::PERMISSION,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountPermissionUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::PERMISSION,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAccountPermissionDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::PERMISSION,
                'state'  => PermissionCategory::MODULE,
            ],
        ],
    ],
    '^.*/admin/module/reinit(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiReInit',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::ROUTE,
            ],
        ],
    ],

    '^.*/admin/update/url(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiUpdateFile',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APP,
            ],
        ],
    ],
    '^.*/admin/update/check(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiCheckForUpdates',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APP,
            ],
        ],
    ],
    '^.*/admin/update/component(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiCheckForUpdates',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APP,
            ],
        ],
    ],
    '^.*/account/address$' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAddressCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::ADDRESS,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAddressUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::ADDRESS,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiAddressDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::ADDRESS,
            ],
        ],
    ],
    '^.*/account/contact$' => [
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiContactCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::ADDRESS,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiContactUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::ADDRESS,
            ],
        ],
        [
            'dest'       => '\Modules\Admin\Controller\ApiController:apiContactDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::ADDRESS,
            ],
        ],
    ],
];
