<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Admin\Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Admin\Models\SettingsEnum;
use phpOMS\Message\Mail\SubmitType;

return [
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::PASSWORD_PATTERN,
        'content' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[.,\/\(\)\{\}\[\]#?!@$%^&*+=\':"-]).{8,}$/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::LOGIN_TRIES,
        'content' => '3',
        'pattern' => '/\\d+/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::PASSWORD_INTERVAL,
        'content' => '90',
        'pattern' => '/\\d+/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::PASSWORD_HISTORY,
        'content' => '3',
        'pattern' => '/\\d+/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::LOGGING_STATUS,
        'content' => '1',
        'pattern' => '/[0-3]/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::LOGGING_PATH,
        'content' => '',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::DEFAULT_UNIT,
        'content' => '1',
        'pattern' => '/\\d+/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::LOGIN_STATUS,
        'content' => '1',
        'pattern' => '/[0-3]/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::LOGIN_MAIL_REGISTRATION_TEMPLATE,
        'content' => '',
        'pattern' => '/\\d*/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::LOGIN_MAIL_FORGOT_PASSWORD_TEMPLATE,
        'content' => '',
        'pattern' => '/\\d*/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::LOGIN_MAIL_FAILED_TEMPLATE,
        'content' => '',
        'pattern' => '/\\d*/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::DEFAULT_LOCALIZATION,
        'content' => '1',
        'pattern' => '/\\d+/',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::MAIL_SERVER_OUT,
        'content' => '',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::MAIL_SERVER_PORT_OUT,
        'content' => '',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::MAIL_SERVER_IN,
        'content' => '',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::MAIL_SERVER_PORT_IN,
        'content' => '',
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::MAIL_SERVER_ADDR,
        'content' => '',
        'pattern' => "/(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/",
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::MAIL_SERVER_TYPE,
        'content' => SubmitType::MAIL,
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::MAIL_SERVER_USER,
        'content' => '',
        'module'  => 'Admin',
    ],
    [
        'type'      => 'setting',
        'name'      => SettingsEnum::MAIL_SERVER_PASS,
        'content'   => '',
        'module'    => 'Admin',
        'encrypted' => true,
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::MAIL_SERVER_CERT,
        'content' => '',
        'module'  => 'Admin',
    ],
    [
        'type'      => 'setting',
        'name'      => SettingsEnum::MAIL_SERVER_KEY,
        'content'   => '',
        'module'    => 'Admin',
        'encrypted' => true,
    ],
    [
        'type'      => 'setting',
        'name'      => SettingsEnum::MAIL_SERVER_KEYPASS,
        'content'   => '',
        'module'    => 'Admin',
        'encrypted' => true,
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::MAIL_SERVER_TLS,
        'content' => (string) false,
        'module'  => 'Admin',
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_APP,
        'content' => (string) true,
        'module'  => 'Admin',
    ],
];
