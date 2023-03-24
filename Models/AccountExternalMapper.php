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

use phpOMS\Account\AccountStatus;
use phpOMS\Auth\LoginReturnType;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Database\Query\Builder;

/**
 * Account mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class AccountMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'account_external_id'                  => ['name' => 'account_external_id',           'type' => 'int',      'internal' => 'id'],
        'account_external_status'              => ['name' => 'account_external_status',       'type' => 'int',      'internal' => 'status'],
        'account_external_type'                => ['name' => 'account_external_type',         'type' => 'int',      'internal' => 'type'],
        'account_external_subtype'               => ['name' => 'account_external_subtype',        'type' => 'int',   'internal' => 'subtype',],
        'account_external_key'               => ['name' => 'account_external_key',        'type' => 'string',   'internal' => 'key'],
        'account_external_name'               => ['name' => 'account_external_name',        'type' => 'string',   'internal' => 'name'],
        'account_external_auth'               => ['name' => 'account_external_auth',        'type' => 'string',   'internal' => 'auth', ],
        'account_external_account'          => ['name' => 'account_external_account',   'type' => 'int', 'internal' => 'account'],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string
     * @since 1.0.0
     */
    public const MODEL = AccountExternal::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'account_external';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'account_external_id';
}
