<?php
declare(strict_types=1);

use phpOMS\Router\RouteVerb;

return [
    '^/* .*?$' => [
        [
            'dest' => '\Modules\Admin\Controller\CliController:viewEmptyCommand',
            'verb' => RouteVerb::ANY,
        ],
    ],
    '^/admin/event.*$' => [
        [
            'dest' => '\Modules\Admin\Controller\CliController:cliRunEvent',
            'verb' => RouteVerb::ANY,
        ],
    ],
];
