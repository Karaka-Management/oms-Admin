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

return [
    'Module:Admin-encryption-change' => [
        'callback' => ['\Modules\Admin\Controller\CliController:runEncryptionChangeFromHook'],
    ],
];
