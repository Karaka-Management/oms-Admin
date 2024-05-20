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

/**
 * Account external class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
class AccountExternal
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * External type.
     *
     * @var int
     * @since 1.0.0
     */
    public int $type = AccountExternalType::PAYMENT;

    /**
     * External subtype.
     *
     * @var int
     * @since 1.0.0
     */
    public int $subtype = AccountExternalSubtype::STRIPE;

    /**
     * External status.
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = AccountExternalStatus::ACTIVATE;

    /**
     * Name of the external service
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * External uid
     *
     * @var string
     * @since 1.0.0
     */
    public string $uid = '';

    /**
     * External login
     *
     * @var string
     * @since 1.0.0
     */
    public string $login = '';

    /**
     * External password
     *
     * @var string
     * @since 1.0.0
     */
    public string $password = '';

    /**
     * External key
     *
     * @var string
     * @since 1.0.0
     */
    public string $key = '';

    /**
     * Belongs to.
     *
     * @var Account
     * @since 1.0.0
     */
    public Account $account;
}
