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

use phpOMS\DataStorage\Database\Connection\ConnectionAbstract;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Permission mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of \phpOMS\Account\PermissionAbstract
 * @extends DataMapperFactory<T>
 */
final class PermissionAbstractMapper extends DataMapperFactory
{
    /**
     * Create a permission query
     *
     * @param ConnectionAbstract $connection Connection
     *
     * @return PermissionQueryBuilder
     *
     * @since 1.0.0
     */
    public static function helper(ConnectionAbstract $connection) : PermissionQueryBuilder
    {
        return new PermissionQueryBuilder($connection);
    }
}
