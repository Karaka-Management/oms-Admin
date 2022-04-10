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

return [
    '/.*/' => [
        'callback' => ['\Modules\Admin\Controller\ApiController:cliEventCall'],
    ],
];