<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
 * Account group class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Group extends \phpOMS\Account\Group
{
    /**
     * Created at.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Created by.
     *
     * @var Account
     * @since 1.0.0
     */
    public Account $createdBy;

    /**
     * Group raw description.
     *
     * @var string
     * @since 1.0.0
     */
    public string $descriptionRaw = '';

    /**
     * Accounts
     *
     * @var Account[]
     * @since 1.0.0
     */
    public array $accounts = [];

    /**
     * Constructor
     *
     * @param string $name Group name
     *
     * @since 1.0.0
     */
    public function __construct(string $name = '')
    {
        $this->createdBy = new NullAccount();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->name      = $name;
    }

    /**
     * Get accounts
     *
     * @return Account[] Accounts
     *
     * @since 1.0.0
     */
    public function getAccounts() : array
    {
        return $this->accounts;
    }
}
