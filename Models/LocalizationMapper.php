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
use phpOMS\Localization\Defaults\CurrencyMapper;
use phpOMS\Localization\Defaults\LanguageMapper;
use phpOMS\Localization\Localization;

/**
 * Localization mapper.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Localization
 * @extends DataMapperFactory<T>
 */
final class LocalizationMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'l11n_id'                   => ['name' => 'l11n_id',                   'type' => 'int',    'internal' => 'id'],
        'l11n_country'              => ['name' => 'l11n_country',              'type' => 'string', 'internal' => 'country'],
        'l11n_language'             => ['name' => 'l11n_language',             'type' => 'string', 'internal' => 'language'],
        'l11n_currency'             => ['name' => 'l11n_currency',             'type' => 'string', 'internal' => 'currency'],
        'l11n_currency_format'      => ['name' => 'l11n_currency_format',      'type' => 'string', 'internal' => 'currencyFormat'],
        'l11n_number_thousand'      => ['name' => 'l11n_number_thousand',      'type' => 'string', 'internal' => 'thousands'],
        'l11n_number_decimal'       => ['name' => 'l11n_number_decimal',       'type' => 'string', 'internal' => 'decimal'],
        'l11n_angle'                => ['name' => 'l11n_angle',                'type' => 'string', 'internal' => 'angle'],
        'l11n_temperature'          => ['name' => 'l11n_temperature',          'type' => 'string', 'internal' => 'temperature'],
        'l11n_weight_very_light'    => ['name' => 'l11n_weight_very_light',    'type' => 'string', 'internal' => 'weight/very_light'],
        'l11n_weight_light'         => ['name' => 'l11n_weight_light',         'type' => 'string', 'internal' => 'weight/light'],
        'l11n_weight_medium'        => ['name' => 'l11n_weight_medium',        'type' => 'string', 'internal' => 'weight/medium'],
        'l11n_weight_heavy'         => ['name' => 'l11n_weight_heavy',         'type' => 'string', 'internal' => 'weight/heavy'],
        'l11n_weight_very_heavy'    => ['name' => 'l11n_weight_very_heavy',    'type' => 'string', 'internal' => 'weight/very_heavy'],
        'l11n_speed_very_slow'      => ['name' => 'l11n_speed_very_slow',      'type' => 'string', 'internal' => 'speed/very_slow'],
        'l11n_speed_slow'           => ['name' => 'l11n_speed_slow',           'type' => 'string', 'internal' => 'speed/slow'],
        'l11n_speed_medium'         => ['name' => 'l11n_speed_medium',         'type' => 'string', 'internal' => 'speed/medium'],
        'l11n_speed_fast'           => ['name' => 'l11n_speed_fast',           'type' => 'string', 'internal' => 'speed/fast'],
        'l11n_speed_very_fast'      => ['name' => 'l11n_speed_very_fast',      'type' => 'string', 'internal' => 'speed/very_fast'],
        'l11n_speed_sea'            => ['name' => 'l11n_speed_sea',            'type' => 'string', 'internal' => 'speed/sea'],
        'l11n_length_very_short'    => ['name' => 'l11n_length_very_short',    'type' => 'string', 'internal' => 'length/very_short'],
        'l11n_length_short'         => ['name' => 'l11n_length_short',         'type' => 'string', 'internal' => 'length/short'],
        'l11n_length_medium'        => ['name' => 'l11n_length_medium',        'type' => 'string', 'internal' => 'length/medium'],
        'l11n_length_long'          => ['name' => 'l11n_length_long',          'type' => 'string', 'internal' => 'length/long'],
        'l11n_length_very_long'     => ['name' => 'l11n_length_very_long',     'type' => 'string', 'internal' => 'length/very_long'],
        'l11n_length_sea'           => ['name' => 'l11n_length_sea',           'type' => 'string', 'internal' => 'length/sea'],
        'l11n_area_very_small'      => ['name' => 'l11n_area_very_small',      'type' => 'string', 'internal' => 'area/very_small'],
        'l11n_area_small'           => ['name' => 'l11n_area_small',           'type' => 'string', 'internal' => 'area/small'],
        'l11n_area_medium'          => ['name' => 'l11n_area_medium',          'type' => 'string', 'internal' => 'area/medium'],
        'l11n_area_large'           => ['name' => 'l11n_area_large',           'type' => 'string', 'internal' => 'area/large'],
        'l11n_area_very_large'      => ['name' => 'l11n_area_very_large',      'type' => 'string', 'internal' => 'area/very_large'],
        'l11n_volume_very_small'    => ['name' => 'l11n_volume_very_small',    'type' => 'string', 'internal' => 'volume/very_small'],
        'l11n_volume_small'         => ['name' => 'l11n_volume_small',         'type' => 'string', 'internal' => 'volume/small'],
        'l11n_volume_medium'        => ['name' => 'l11n_volume_medium',        'type' => 'string', 'internal' => 'volume/medium'],
        'l11n_volume_large'         => ['name' => 'l11n_volume_large',         'type' => 'string', 'internal' => 'volume/large'],
        'l11n_volume_very_large'    => ['name' => 'l11n_volume_very_large',    'type' => 'string', 'internal' => 'volume/very_large'],
        'l11n_volume_teaspoon'      => ['name' => 'l11n_volume_teaspoon',      'type' => 'string', 'internal' => 'volume/teaspoon'],
        'l11n_volume_tablespoon'    => ['name' => 'l11n_volume_tablespoon',    'type' => 'string', 'internal' => 'volume/tablespoon'],
        'l11n_volume_glass'         => ['name' => 'l11n_volume_glass',         'type' => 'string', 'internal' => 'volume/glass'],
        'l11n_timezone'             => ['name' => 'l11n_timezone',             'type' => 'string', 'internal' => 'timezone'],
        'l11n_datetime_very_short'  => ['name' => 'l11n_datetime_very_short',  'type' => 'string', 'internal' => 'datetime/very_short'],
        'l11n_datetime_short'       => ['name' => 'l11n_datetime_short',       'type' => 'string', 'internal' => 'datetime/short'],
        'l11n_datetime_medium'      => ['name' => 'l11n_datetime_medium',      'type' => 'string', 'internal' => 'datetime/medium'],
        'l11n_datetime_long'        => ['name' => 'l11n_datetime_long',        'type' => 'string', 'internal' => 'datetime/long'],
        'l11n_datetime_very_long'   => ['name' => 'l11n_datetime_very_long',   'type' => 'string', 'internal' => 'datetime/very_long'],
        'l11n_precision_very_short' => ['name' => 'l11n_precision_very_short', 'type' => 'int',    'internal' => 'precision/very_short'],
        'l11n_precision_short'      => ['name' => 'l11n_precision_short',      'type' => 'int',    'internal' => 'precision/short'],
        'l11n_precision_medium'     => ['name' => 'l11n_precision_medium',     'type' => 'int',    'internal' => 'precision/medium'],
        'l11n_precision_long'       => ['name' => 'l11n_precision_long',       'type' => 'int',    'internal' => 'precision/long'],
        'l11n_precision_very_long'  => ['name' => 'l11n_precision_very_long',  'type' => 'int',    'internal' => 'precision/very_long'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     *
     * @question Is there a real significant difference between by and column?
     *      By defines the reference id
     *      Column defines the data which is stored in the variable IFF a scalar type should be stored instead of an object
     *      Maybe using a flag instead of column would be better: e.g. useScalar = true -> don't create object
     *      But sometimes we don't define 'by' if the 'by' is the primary key:
     *          by => id
     *          column => whatever
     *      But we don't type this and only define column.
     */
    public const OWNS_ONE = [
        'country' => [
            'mapper'   => CountryMapper::class,
            'external' => 'l11n_country',
            'by'       => 'code2',
            'column'   => 'code2',
        ],
        'language' => [
            'mapper'   => LanguageMapper::class,
            'external' => 'l11n_language',
            'by'       => 'code2',
            'column'   => 'code2',
        ],
        'currency' => [
            'mapper'   => CurrencyMapper::class,
            'external' => 'l11n_currency',
            'by'       => 'code',
            'column'   => 'code',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'l11n_id';

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = Localization::class;
}
