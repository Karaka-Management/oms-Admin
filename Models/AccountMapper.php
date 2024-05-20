<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
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
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Account
 * @extends DataMapperFactory<T>
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
        'account_id'           => ['name' => 'account_id',           'type' => 'int',      'internal' => 'id'],
        'account_status'       => ['name' => 'account_status',       'type' => 'int',      'internal' => 'status'],
        'account_type'         => ['name' => 'account_type',         'type' => 'int',      'internal' => 'type'],
        'account_login'        => ['name' => 'account_login',        'type' => 'string',   'internal' => 'login', 'autocomplete' => true],
        'account_name1'        => ['name' => 'account_name1',        'type' => 'string',   'internal' => 'name1', 'autocomplete' => true, 'annotations' => ['gdpr' => true]],
        'account_name2'        => ['name' => 'account_name2',        'type' => 'string',   'internal' => 'name2', 'autocomplete' => true, 'annotations' => ['gdpr' => true]],
        'account_name3'        => ['name' => 'account_name3',        'type' => 'string',   'internal' => 'name3', 'autocomplete' => true, 'annotations' => ['gdpr' => true]],
        'account_email'        => ['name' => 'account_email',        'type' => 'string',   'internal' => 'email', 'autocomplete' => true, 'annotations' => ['gdpr' => true]],
        'account_tries'        => ['name' => 'account_tries',        'type' => 'int',      'internal' => 'tries'],
        'account_lactive'      => ['name' => 'account_lactive',      'type' => 'DateTime', 'internal' => 'lastActive'],
        'account_localization' => ['name' => 'account_localization', 'type' => 'int',      'internal' => 'l11n'],
        'account_created_at'   => ['name' => 'account_created_at',   'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'l11n' => [
            'mapper'   => LocalizationMapper::class,
            'external' => 'account_localization',
        ],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'permissions' => [
            'mapper'   => AccountPermissionMapper::class,
            'table'    => 'account_permission',
            'external' => null,
            'self'     => 'account_permission_account',
        ],
        'groups' => [
            'mapper'   => GroupMapper::class,
            'table'    => 'account_group',
            'external' => 'account_group_group',
            'self'     => 'account_group_account',
        ],
        'parents' => [
            'mapper'   => self::class,
            'table'    => 'account_account_rel',
            'external' => 'account_account_rel_root',
            'self'     => 'account_account_rel_child',
        ],
        'addresses' => [
            'mapper'   => AddressMapper::class,
            'table'    => 'account_address_rel',
            'external' => 'account_address_rel_address',
            'self'     => 'account_address_rel_account',
        ],
        'contacts' => [
            'mapper'   => ContactMapper::class,
            'table'    => 'account_contact_rel',
            'external' => 'account_contact_rel_contact',
            'self'     => 'account_contact_rel_account',
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = Account::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'account';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'account_id';

    /**
     * Created at column
     *
     * @var string
     * @since 1.0.0
     */
    public const CREATED_AT = 'account_created_at';

    /**
     * Get account with permissions
     *
     * @param int $id Account id
     *
     * @return Account
     *
     * @since 1.0.0
     */
    public static function getWithPermissions(int $id) : Account
    {
        if ($id < 1) {
            return new NullAccount();
        }

        /*
            select *
            from account
            left join l11n on account.account_localization = l11n.l11n_id
            where account.account_id = ((int) $id);
        */

        /*
            select *
            from account_permission
            where account_permission.account_permission_account = ((int) $id)
        */

        /*
            select group_id, group_name, group_permission.*
            from `group`
            join group_permission on `group`.group_id = group_permission.group_permission_group
            join account_group on group_permission.group_permission_group = account_group.account_group_group
                AND account_group.account_group_account = ((int) $id)
            where `group`.group_status = 1;
        */

        return self::get()
            ->with('groups')
            ->with('groups/permissions')
            ->with('permissions')
            ->with('l11n')
            ->where('id', $id)
            ->where('permissions/element', null)
            ->where('groups/permissions/element', null)
            ->execute();
    }

    /**
     * Login user.
     *
     * @param string $login    Username
     * @param string $password Password
     * @param int    $tries    Allowed login tries
     *
     * @return int Login code
     *
     * @since 1.0.0
     */
    public static function login(string $login, string $password, int $tries = 3) : int
    {
        if (empty($password)) {
            return LoginReturnType::WRONG_PASSWORD;
        }

        try {
            $result = null;

            $query  = new Builder(self::$db);
            $result = $query->select('account_id', 'account_login', 'account_password', 'account_password_temp', 'account_tries', 'account_status')
                ->from('account')
                ->where('account_login', '=', $login)
                ->execute()
                ?->fetchAll();

            if ($result === null || !isset($result[0])) {
                return LoginReturnType::WRONG_USERNAME;
            }

            $result = $result[0];

            if ($result['account_tries'] >= $tries) {
                return LoginReturnType::WRONG_INPUT_EXCEEDED;
            }

            if ($result['account_status'] !== AccountStatus::ACTIVE) {
                return LoginReturnType::INACTIVE;
            }

            if (empty($result['account_password'])) {
                return LoginReturnType::EMPTY_PASSWORD;
            }

            if (\password_verify($password, $result['account_password'] ?? '')) {
                $query = new Builder(self::$db);
                $query->update('account')
                    ->set([
                        'account_lactive' => new \DateTime('now'),
                        'account_tries'   => 0,
                    ])
                    ->where('account_id', '=', (int) $result['account_id'])
                    ->execute();

                return $result['account_id'];
            }

            if (!empty($result['account_password_temp'])
                && $result['account_password_temp_limit'] !== null
                && (new \DateTime('now'))->getTimestamp() < (new \DateTime($result['account_password_temp_limit']))->getTimestamp()
                && \password_verify($password, $result['account_password_temp'] ?? '')
            ) {
                $query = new Builder(self::$db);
                $query->update('account')
                    ->set([
                        'account_password_temp' => '',
                        'account_lactive'       => new \DateTime('now'),
                        'account_tries'         => 0,
                    ])
                    ->where('account_id', '=', (int) $result['account_id'])
                    ->execute();

                return $result['account_id'];
            }

            $query = new Builder(self::$db);
            $query->update('account')
                ->set([
                    'account_tries' => $result['account_tries'] + 1,
                ])
                ->where('account_id', '=', (int) $result['account_id'])
                ->execute();

            return LoginReturnType::WRONG_PASSWORD;
        } catch (\Exception $_) {
            return LoginReturnType::FAILURE; // @codeCoverageIgnore
        }
    }

    /**
     * Find accounts that have read permission
     *
     * @param int    $unitId   Unit id
     * @param string $module   Module name
     * @param int    $category Category
     * @param int    $element  Element id
     *
     * @return int[] Account ids
     *
     * @since 1.0.0
     */
    public static function findReadPermission(
        int $unitId,
        string $module,
        int $category,
        int $element,
    ) : array
    {
        $accounts = [];

        $sql = <<<SQL
        SELECT account_permission_account as account
        FROM account_permission
        WHERE (account_permission_unit = {$unitId} OR account_permission_unit IS NULL)
            AND (account_permission_module = "{$module}" OR account_permission_module IS NULL)
            AND (account_permission_category = {$category} OR account_permission_category IS NULL)
            AND (account_permission_element = {$element} OR account_permission_element IS NULL)
            AND account_permission_hasread = 1;
        SQL;

        $query   = new Builder(self::$db);
        $results = $query->raw($sql)->execute()?->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        foreach ($results as $result) {
            $accounts[] = (int) $result['account'];
        }

        $sql = <<<SQL
        SELECT account_group_account as account
        FROM account_group
        LEFT JOIN group_permission ON account_group.account_group_group = group_permission.group_permission_group
        WHERE (group_permission_unit = {$unitId} OR group_permission_unit IS NULL)
            AND (group_permission_module = "{$module}" OR group_permission_module IS NULL)
            AND (group_permission_category = {$category} OR group_permission_category IS NULL)
            AND (group_permission_element = {$element} OR group_permission_element IS NULL)
            AND group_permission_hasread = 1;
        SQL;

        $query   = new Builder(self::$db);
        $results = $query->raw($sql)->execute()?->fetchAll(\PDO::FETCH_ASSOC) ?? [];

        foreach ($results as $result) {
            $accounts[] = (int) $result['account'];
        }

        return \array_unique($accounts);
    }
}
