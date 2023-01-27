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
 * Contact mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
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
        'account_contact_id'      => ['name' => 'account_contact_id', 'type' => 'int', 'internal' => 'id'],
        'account_contact_type'    => ['name' => 'account_contact_type', 'type' => 'int', 'internal' => 'type'],
        'account_contact_subtype' => ['name' => 'account_contact_subtype', 'type' => 'int', 'internal' => 'subtype'],
        'account_contact_order'   => ['name' => 'account_contact_order', 'type' => 'int', 'internal' => 'order'],
        'account_contact_content' => ['name' => 'account_contact_content', 'type' => 'string', 'internal' => 'content'],
        'account_contact_module' => ['name' => 'account_contact_module', 'type' => 'string', 'internal' => 'module'],
        'account_contact_account' => ['name' => 'account_contact_account', 'type' => 'int', 'internal' => 'account'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'account_contact';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='account_contact_id';
}
