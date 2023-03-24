<?php
/**
 * Karaka
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
 * Permision state enum.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class PermissionCategory extends Enum
{
    public const SETTINGS = 1;

    public const ACCOUNT = 2;

    public const GROUP = 3;

    public const MODULE = 4;

    public const LOG = 5;

    public const ROUTE = 6;

    public const APP = 7;

    public const ACCOUNT_SETTINGS = 8;

    public const SEARCH = 9;

    public const API = 9;
}
