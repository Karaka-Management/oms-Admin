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

use phpOMS\Account\AccountStatus;

/**
 * Api key class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class ApiKey
{
    /**
     * Id.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Key.
     *
     * @var string
     * @since 1.0.0
     */
    public string $key = '';

    /**
     * Api key status.
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = AccountStatus::INACTIVE;

    /**
     * Creator.
     *
     * @var int
     * @since 1.0.0
     */
    public int $account = 0;

    /**
     * Created.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->key       = \random_bytes(128);
        $this->createdAt = new \DateTimeImmutable('now');
    }
}
