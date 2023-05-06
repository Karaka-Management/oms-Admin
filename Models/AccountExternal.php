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

/**
 * Account external class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
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
    private int $subtype = AccountExternalSubtype::STRIPE;

    /**
     * External status.
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = AccountExternalStatus::ACTIVATE;

    /**
     * External key
     *
     * (e.g. user id on the external platform).
     *
     * @var string
     * @since 1.0.0
     */
    public string $key = '';

    /**
     * External name
     *
     * (e.g. user name on the external platform).
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * External auth
     *
     * (e.g. user authentication on the external platform such as password or api key).
     *
     * @var string
     * @since 1.0.0
     */
    public string $auth = '';

    /**
     * Belongs to.
     *
     * @var Account
     * @since 1.0.0
     */
    public Account $account;
}
