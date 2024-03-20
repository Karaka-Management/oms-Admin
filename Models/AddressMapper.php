<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
use phpOMS\Localization\Defaults\CountryMapper;
use phpOMS\Stdlib\Base\Address;

/**
 * Address mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Address
 * @extends DataMapperFactory<T>
 */
final class AddressMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'address_id'       => ['name' => 'address_id',      'type' => 'int',    'internal' => 'id'],
        'address_name'     => ['name' => 'address_name',  'type' => 'string', 'internal' => 'name'],
        'address_fao'      => ['name' => 'address_fao',  'type' => 'string', 'internal' => 'fao'],
        'address_address'  => ['name' => 'address_address',  'type' => 'string', 'internal' => 'address'],
        'address_addition' => ['name' => 'address_addition',  'type' => 'string', 'internal' => 'addressAddition'],
        'address_postal'   => ['name' => 'address_postal',  'type' => 'string', 'internal' => 'postal'],
        'address_state'    => ['name' => 'address_state',   'type' => 'string', 'internal' => 'state'],
        'address_city'     => ['name' => 'address_city',    'type' => 'string', 'internal' => 'city'],
        'address_country'  => ['name' => 'address_country', 'type' => 'string', 'internal' => 'country'],
        'address_type'     => ['name' => 'address_type', 'type' => 'int',    'internal' => 'type'],
        'address_lat'      => ['name' => 'address_lat', 'type' => 'float',    'internal' => 'lat'],
        'address_lon'      => ['name' => 'address_lon', 'type' => 'float',    'internal' => 'lon'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'country' => [
            'mapper'      => CountryMapper::class,
            'external'    => 'address_country',
            'by'          => 'code2',
            'column'      => 'code2',
            'conditional' => true,
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'address';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'address_id';

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = Address::class;
}
