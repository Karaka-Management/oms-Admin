<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Status for external references
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class AccountExternalStatus extends Enum
{
    public const ACTIVATE = 1;

    public const INACTIVE = 2;
}
