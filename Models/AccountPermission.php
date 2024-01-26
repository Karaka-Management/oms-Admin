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

use phpOMS\Account\PermissionAbstract;
use phpOMS\Account\PermissionType;

/**
 * Account permission class.
 *
 * A single permission for an account consisting of read, create, modify, delete and permission flags.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class AccountPermission extends PermissionAbstract
{
    /**
     * Account id
     *
     * @var int
     * @since 1.0.0
     */
    public int $account = 0;

    /**
     * Constructor.
     *
     * @param int         $account    Group id
     * @param null|int    $unit       Unit Unit to check (null if all are acceptable)
     * @param null|int    $app        App App to check  (null if all are acceptable)
     * @param null|string $module     Module to check  (null if all are acceptable)
     * @param null|string $from       Module providing this permission
     * @param null|int    $category   Category (e.g. customer) (null if all are acceptable)
     * @param null|int    $element    (e.g. customer id) (null if all are acceptable)
     * @param null|int    $component  (e.g. address) (null if all are acceptable)
     * @param int         $permission Permission to check
     *
     * @since 1.0.0
     */
    public function __construct(
        int $account = 0,
        ?int $unit = null,
        ?int $app = null,
        ?string $module = null,
        ?string $from = null,
        ?int $category = null,
        ?int $element = null,
        ?int $component = null,
        int $permission = PermissionType::NONE
    ) {
        $this->account = $account;
        parent::__construct($unit, $app, $module, $from, $category, $element, $component, $permission);
    }

    /**
     * Get account id
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getAccount() : int
    {
        return $this->account;
    }
}
