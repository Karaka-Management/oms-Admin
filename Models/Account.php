<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

/**
 * Account class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
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
}
