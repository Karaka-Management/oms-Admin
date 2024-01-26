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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Account permission mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of AccountPermission
 * @extends DataMapperFactory<T>
 */
final class AccountPermissionMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'account_permission_id'            => ['name' => 'account_permission_id',         'type' => 'int',    'internal' => 'id'],
        'account_permission_account'       => ['name' => 'account_permission_account',    'type' => 'int',    'internal' => 'account'],
        'account_permission_unit'          => ['name' => 'account_permission_unit',       'type' => 'int',    'internal' => 'unit'],
        'account_permission_app'           => ['name' => 'account_permission_app',        'type' => 'int', 'internal' => 'app'],
        'account_permission_module'        => ['name' => 'account_permission_module',     'type' => 'string', 'internal' => 'module'],
        'account_permission_from'          => ['name' => 'account_permission_from',       'type' => 'string',    'internal' => 'from'],
        'account_permission_category'      => ['name' => 'account_permission_category',       'type' => 'int',    'internal' => 'category'],
        'account_permission_element'       => ['name' => 'account_permission_element',    'type' => 'int',    'internal' => 'element'],
        'account_permission_component'     => ['name' => 'account_permission_component',  'type' => 'int',    'internal' => 'component'],
        'account_permission_hasread'       => ['name' => 'account_permission_hasread', 'type' => 'bool',    'internal' => 'hasRead'],
        'account_permission_hascreate'     => ['name' => 'account_permission_hascreate', 'type' => 'bool',    'internal' => 'hasCreate'],
        'account_permission_defaultcperm'  => ['name' => 'account_permission_defaultcperm', 'type' => 'string',    'internal' => 'defaultCPermissions'],
        'account_permission_hasmodify'     => ['name' => 'account_permission_hasmodify', 'type' => 'bool',    'internal' => 'hasModify'],
        'account_permission_hasdelete'     => ['name' => 'account_permission_hasdelete', 'type' => 'bool',    'internal' => 'hasDelete'],
        'account_permission_haspermission' => ['name' => 'account_permission_haspermission', 'type' => 'bool',    'internal' => 'hasPermission'],
        'account_permission_defaultpperm'  => ['name' => 'account_permission_defaultpperm', 'type' => 'string',    'internal' => 'defaultPPermissions'],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = AccountPermission::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'account_permission';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'account_permission_id';
}
