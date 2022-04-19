<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Admin\Admin;

use phpOMS\Stdlib\Base\Enum;

/**
 * Install type enum.
 *
 * @package Modules\Admin\Admin
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class InstallType extends Enum
{
    public const GROUP = 1;
}
