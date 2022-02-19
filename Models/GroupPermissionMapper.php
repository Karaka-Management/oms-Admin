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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Group permission mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class GroupPermissionMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'group_permission_id'         => ['name' => 'group_permission_id',         'type' => 'int',    'internal' => 'id'],
        'group_permission_group'      => ['name' => 'group_permission_group',      'type' => 'int',    'internal' => 'group'],
        'group_permission_unit'       => ['name' => 'group_permission_unit',       'type' => 'int',    'internal' => 'unit'],
        'group_permission_app'        => ['name' => 'group_permission_app',        'type' => 'string', 'internal' => 'app'],
        'group_permission_module'     => ['name' => 'group_permission_module',     'type' => 'string', 'internal' => 'module'],
        'group_permission_from'       => ['name' => 'group_permission_from',       'type' => 'string',    'internal' => 'from'],
        'group_permission_type'       => ['name' => 'group_permission_type',       'type' => 'int',    'internal' => 'type'],
        'group_permission_element'    => ['name' => 'group_permission_element',    'type' => 'int',    'internal' => 'element'],
        'group_permission_component'  => ['name' => 'group_permission_component',  'type' => 'int',    'internal' => 'component'],
        'group_permission_permission' => ['name' => 'group_permission_permission', 'type' => 'int',    'internal' => 'permission'],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var string
     * @since 1.0.0
     */
    public const MODEL = GroupPermission::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'group_permission';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='group_permission_id';
}
