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
 * Contact mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Contact
 * @extends DataMapperFactory<T>
 */
final class ContactMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'contact_id'      => ['name' => 'contact_id', 'type' => 'int', 'internal' => 'id'],
        'contact_title'   => ['name' => 'contact_title', 'type' => 'string', 'internal' => 'title'],
        'contact_type'    => ['name' => 'contact_type', 'type' => 'int', 'internal' => 'type'],
        'contact_subtype' => ['name' => 'contact_subtype', 'type' => 'int', 'internal' => 'subtype'],
        'contact_content' => ['name' => 'contact_content', 'type' => 'string', 'internal' => 'content'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'contact';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'contact_id';
}
