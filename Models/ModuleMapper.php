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
 * Module mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class ModuleMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'module_id'      => ['name' => 'module_id',     'type' => 'string', 'internal' => 'id'],
        'module_path'    => ['name' => 'module_path', 'type' => 'string',    'internal' => 'path'],
        'module_theme'   => ['name' => 'module_theme', 'type' => 'string',    'internal' => 'theme'],
        'module_version' => ['name' => 'module_version', 'type' => 'string',    'internal' => 'version'],
        'module_status'  => ['name' => 'module_status', 'type' => 'int',    'internal' => 'status'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'module';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='module_id';

    public const AUTOINCREMENT = false;
}
