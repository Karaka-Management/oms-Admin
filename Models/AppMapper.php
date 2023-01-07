<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
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
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class AppMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'app_id'     => ['name' => 'app_id',     'type' => 'int',    'internal' => 'id'],
        'app_name'   => ['name' => 'app_name',   'type' => 'string', 'internal' => 'name'],
        'app_theme'  => ['name' => 'app_theme',  'type' => 'string', 'internal' => 'theme'],
        'app_status' => ['name' => 'app_status', 'type' => 'int',    'internal' => 'status'],
        'app_type' => ['name' => 'app_type', 'type' => 'int',    'internal' => 'type'],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var string
     * @since 1.0.0
     */
    public const MODEL = App::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'app';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='app_id';
}
