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

use phpOMS\Stdlib\Base\Location;

/**
 * Account class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Account extends \phpOMS\Account\Account
{
    /**
     * Remaining login tries.
     *
     * @var int
     * @since 1.0.0
     */
    public int $tries = 0;

    /**
     * Password.
     *
     * @var string
     * @since 1.0.0
     */
    public string $tempPassword = '';

    /**
     * Parents.
     *
     * @var Account[]
     * @since 1.0.0
     */
    public array $parents = [];

    /**
     * Remaining login tries.
     *
     * @var null|\DateTimeImmutable
     * @since 1.0.0
     */
    public ?\DateTimeImmutable $tempPasswordLimit = null;

    /**
     * Location data.
     *
     * @var Location[]
     * @since 1.0.0
     */
    public array $locations = [];

    /**
     * Contact data.
     *
     * @var Contact[]
     * @since 1.0.0
     */
    public array $contacts = [];
}
