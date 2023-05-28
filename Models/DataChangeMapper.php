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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Account mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of DataChange
 * @extends DataMapperFactory<T>
 */
final class DataChangeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'data_change_id'         => ['name' => 'data_change_id',     'type' => 'int',    'internal' => 'id'],
        'data_change_type'       => ['name' => 'data_change_type',   'type' => 'string', 'internal' => 'type'],
        'data_change_hash'       => ['name' => 'data_change_hash',   'type' => 'string', 'internal' => 'hash'],
        'data_change_data'       => ['name' => 'data_change_data',  'type' => 'string', 'internal' => 'data'],
        'data_change_created_by' => ['name' => 'data_change_created_by', 'type' => 'int',    'internal' => 'createdBy'],
        'data_change_created_at' => ['name' => 'data_change_created_at', 'type' => 'DateTimeImmutable',    'internal' => 'createdAt'],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = DataChange::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'data_change';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'data_change_id';
}
