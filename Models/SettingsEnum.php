<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Default settings enum.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class SettingsEnum extends Enum
{
    /* Logging settings */
    public const PASSWORD_PATTERN = '1000000001';

    public const LOGIN_TIMEOUT = '1000000002';

    public const PASSWORD_INTERVAL = '1000000003';

    public const PASSWORD_HISTORY = '1000000004';

    public const LOGIN_TRIES = '1000000005';

    public const LOGGING_STATUS = '1000000006';

    public const LOGGING_PATH = '1000000007';

    /* Organization settings */
    public const DEFAULT_UNIT = '1000000008';

    public const UNIT_DEFAULT_GROUPS = '1000000009';

    /* Login settings */
    public const LOGIN_FORGOTTEN_COUNT = '1000000010';

    public const LOGIN_FORGOTTEN_DATE = '1000000011';

    public const LOGIN_FORGOTTEN_TOKEN = '1000000012';

    public const LOGIN_STATUS = '1000000013';

    /* Localization settings */
    public const DEFAULT_LOCALIZATION = '1000000014';

    /* Mail settings */
    public const MAIL_SERVER_ADDR = '1000000015';

    public const MAIL_SERVER_TYPE = '1000000016';

    public const MAIL_SERVER_USER = '1000000017';

    public const MAIL_SERVER_PASS = '1000000018';

    public const MAIL_SERVER_CERT = '1000000019';

    public const MAIL_SERVER_KEY = '1000000020';

    public const MAIL_SERVER_KEYPASS = '1000000021';

    public const MAIL_SERVER_TLS = '1000000022';

    /* Cli settings */
    public const CLI_ACTIVE = '1000000023';

    /* Global default templates */
    public const DEFAULT_LIST_EXPORTS = '1000000024';

    public const DEFAULT_LETTERS = '1000000025';

    /* App settings */
    public const REGISTRATION_ALLOWED = '1000000029';

    public const GROUP_GENERATE_AUTOMATICALLY_APP = '1000000030';

    public const APP_DEFAULT_GROUPS = '1000000031';
}
