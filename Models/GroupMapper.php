<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Database\RelationType;
use phpOMS\DataStorage\Database\Query\Builder;

/**
 * Group mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class GroupMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'group_id'       => ['name' => 'group_id',       'type' => 'int',      'internal' => 'id'],
        'group_name'     => ['name' => 'group_name',     'type' => 'string',   'internal' => 'name', 'autocomplete' => true],
        'group_status'   => ['name' => 'group_status',   'type' => 'int',      'internal' => 'status'],
        'group_desc'     => ['name' => 'group_desc',     'type' => 'string',   'internal' => 'description'],
        'group_desc_raw' => ['name' => 'group_desc_raw', 'type' => 'string',   'internal' => 'descriptionRaw'],
        'group_created'  => ['name' => 'group_created',  'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $model = Group::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'group';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'group_id';

    /**
     * Created at column
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $createdAt = 'group_created';

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    protected static array $hasMany = [
        'accounts' => [
            'mapper'   => AccountMapper::class,
            'table'    => 'account_group',
            'external' => 'account_group_account',
            'self'     => 'account_group_group',
        ],
    ];

    /**
     * Get groups which reference a certain module
     *
     * @param string $module Module
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function getPermissionForModule(string $module) : array
    {
        $depth = 3;
        $query = self::getQuery();
        $query->innerJoin(GroupPermissionMapper::getTable())
            ->on(self::$table . '_' . $depth . '.group_id', '=', GroupPermissionMapper::getTable() . '.group_permission_group')
            ->where(GroupPermissionMapper::getTable() . '.group_permission_module', '=', $module);

        return self::getAllByQuery($query, RelationType::ALL, $depth);
    }

    public static function countMembers(int $group = 0) : array
    {
        $query  = new Builder(self::$db);
        $query->select(self::$hasMany['accounts']['self'])
            ->select('COUNT(' . self::$hasMany['accounts']['external'] . ')')
            ->from(self::$hasMany['accounts']['table'])
            ->groupBy(self::$hasMany['accounts']['self']);

        if ($group !== 0) {
            $query->where(self::$hasMany['accounts']['self'], '=', $group);
        }

        $result = $query->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);

        return $result;
    }
}
