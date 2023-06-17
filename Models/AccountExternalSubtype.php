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
 * Type for external references
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class AccountExternalSubtype extends Enum
{
    public const STRIPE = 1;

    public const PAYPAL = 2;

    public const GOOGLE_PAY = 3;

    public const AMAZON_PAY = 4;

    public const VENMO = 5;
}
