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

use phpOMS\Account\AccountStatus;
use phpOMS\Auth\LoginReturnType;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Database\Query\Builder;

/**
 * Account mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class ApiKeyMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'account_api_id'         => ['name' => 'account_api_id',           'type' => 'int',      'internal' => 'id'],
        'account_api_key'        => ['name' => 'account_api_key',       'type' => 'string',      'internal' => 'key'],
        'account_api_status'     => ['name' => 'account_api_status',       'type' => 'int',      'internal' => 'status'],
        'account_api_account'    => ['name' => 'account_api_account',       'type' => 'int',      'internal' => 'account'],
        'account_api_created_at' => ['name' => 'account_api_created_at',       'type' => 'DateTime',      'internal' => 'createdAt'],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string
     * @since 1.0.0
     */
    public const MODEL = ApiKey::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'account_api';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='account_api_id';

    /**
     * Created at column
     *
     * @var string
     * @since 1.0.0
     */
    public const CREATED_AT = 'account_api_created_at';

    /**
     * Authenticates a user based on an api key
     *
     * @param string $api Api key
     *
     * @return int
     *
     * @since 1.0.0
     */
    public static function authenticateApiKey(string $api) : int
    {
        if (empty($api)) {
            return LoginReturnType::WRONG_PASSWORD;
        }

        try {
            $result = null;

            $query  = new Builder(self::$db);
            $result = $query->select('account.account_id', 'account.account_status')
                ->from('account')
                ->innerJoin('account_api')->on('account.account_id', '=', 'account_api.account_api_account')
                ->where('account_api.account_api_key', '=', $api)
                ->execute()
                ?->fetchAll();

            if ($result === null || !isset($result[0])) {
                return LoginReturnType::WRONG_USERNAME; // wrong api key
            }

            $result = $result[0];

            if ($result['account_status'] !== AccountStatus::ACTIVE) {
                return LoginReturnType::INACTIVE;
            }

            if (empty($result['account_password'])) {
                return LoginReturnType::EMPTY_PASSWORD;
            }

            $query->update('account')
                ->set([
                    'account_lactive' => new \DateTime('now'),
                ])
                ->where('account_id', '=', (int) $result['account_id'])
                ->execute();

            return (int) $result['account_id'];
        } catch (\Exception $e) {
            return LoginReturnType::FAILURE; // @codeCoverageIgnore
        }
    }
}
