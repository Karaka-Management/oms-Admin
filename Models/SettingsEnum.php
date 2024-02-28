<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
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
 * @license OMS License 2.0
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

    /* Localization settings */
    public const DEFAULT_LOCALIZATION = '1000000010';

    /* Cli settings */
    public const CLI_ACTIVE = '1000000011';

    /* Login settings */
    public const LOGIN_FORGOTTEN_COUNT = '1000000101';

    public const LOGIN_FORGOTTEN_DATE = '1000000102';

    public const LOGIN_FORGOTTEN_TOKEN = '1000000103';

    public const LOGIN_STATUS = '1000000104';

    public const LOGIN_MAIL = '....';

    public const LOGIN_MAIL_REGISTRATION_TEMPLATE = '1000000106';

    public const LOGIN_MAIL_FORGOT_PASSWORD_TEMPLATE = '1000000107';

    public const LOGIN_MAIL_FAILED_TEMPLATE = '1000000108';

    public const LOGIN_MAIL_RESET_PASSWORD_TEMPLATE = '1000000109';

    /* Mail server settings */
    public const MAIL_SERVER_OUT = '1000000201';

    public const MAIL_SERVER_PORT_OUT = '1000000202';

    public const MAIL_SERVER_IN = '1000000203';

    public const MAIL_SERVER_PORT_IN = '1000000204';

    public const MAIL_SERVER_ADDR = '1000000205';

    public const MAIL_SERVER_TYPE = '1000000206';

    public const MAIL_SERVER_USER = '1000000207';

    public const MAIL_SERVER_PASS = '1000000208';

    public const MAIL_SERVER_CERT = '1000000209';

    public const MAIL_SERVER_KEY = '1000000200';

    public const MAIL_SERVER_KEYPASS = '1000000210';

    public const MAIL_SERVER_TLS = '1000000211';

    /* Global default templates */
    //public const DEFAULT_LIST_EXPORTS = '1000000301';

    public const DEFAULT_LETTERS = '1000000302';

    public const DEFAULT_TEMPLATES = '1000000303';

    public const DEFAULT_ASSETS = '1000000304';

    /* App settings */
    public const REGISTRATION_ALLOWED = '1000000401';

    public const GROUP_GENERATE_AUTOMATICALLY_APP = '1000000402';

    public const APP_DEFAULT_GROUPS = '1000000403';
}
