<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    Modules\Admin
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use Modules\Media\Models\MediaMapper;

use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\DatabaseType;
use phpOMS\Auth\LoginReturnType;

/**
 * Account mapper class.
 *
 * @package    Modules\Admin
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class AccountMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array<string, string|bool>>
     * @since 1.0.0
     */
    protected static $columns = [
        'account_id'         => ['name' => 'account_id', 'type' => 'int', 'internal' => 'id', 'autocomplete' => true],
        'account_status'     => ['name' => 'account_status', 'type' => 'int', 'internal' => 'status'],
        'account_type'       => ['name' => 'account_type', 'type' => 'int', 'internal' => 'type'],
        'account_login'      => ['name' => 'account_login', 'type' => 'string', 'internal' => 'login', 'autocomplete' => true],
        'account_name1'      => ['name' => 'account_name1', 'type' => 'string', 'internal' => 'name1', 'autocomplete' => true],
        'account_name2'      => ['name' => 'account_name2', 'type' => 'string', 'internal' => 'name2', 'autocomplete' => true],
        'account_name3'      => ['name' => 'account_name3', 'type' => 'string', 'internal' => 'name3', 'autocomplete' => true],
        'account_password'   => ['name' => 'account_password', 'type' => 'string', 'internal' => 'password'],
        'account_email'      => ['name' => 'account_email', 'type' => 'string', 'internal' => 'email', 'autocomplete' => true],
        //'account_tries'    => ['name' => 'account_tries', 'type' => 'int', 'internal' => 'tries'],
        'account_lactive'    => ['name' => 'account_lactive', 'type' => 'DateTime', 'internal' => 'lastActive'],
        'account_created_at' => ['name' => 'account_created_at', 'type' => 'DateTime', 'internal' => 'createdAt'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array<string, string>>
     * @since 1.0.0
     */
    protected static $hasMany = [
        'groups' => [
            'mapper' => GroupMapper::class,
            'table'  => 'account_group',
            'dst'    => 'account_group_account',
            'src'    => 'account_group_group',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static $table = 'account';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static $primaryField = 'account_id';

    /**
     * Created at column
     *
     * @var string
     * @since 1.0.0
     */
    protected static $createdAt = 'account_created_at';

    /**
     * Login user.
     *
     * @param string $login    Username
     * @param string $password Password
     *
     * @return int Login code
     *
     * @todo   move this to the admin accountMapper
     *
     * @since  1.0.0
     */
    public static function login(string $login, string $password) : int
    {
        if (empty($password)) {
            return LoginReturnType::WRONG_PASSWORD;
        }

        try {
            $result = null;

            switch (self::$db->getType()) {
                case DatabaseType::MYSQL:
                    $sth = self::$db->con->prepare(
                        'SELECT
                            `' . self::$db->prefix . 'account`.*
                        FROM
                            `' . self::$db->prefix . 'account`
                        WHERE
                            `account_login` = :login'
                    );
                    $sth->bindValue(':login', $login, \PDO::PARAM_STR);
                    $sth->execute();

                    $result = $sth->fetchAll();
                    break;
            }

            // TODO: check if user is allowed to login on THIS page (backend|frontend|etc...)

            if (!isset($result[0])) {
                return LoginReturnType::WRONG_USERNAME;
            }

            $result = $result[0];

            if ($result['account_tries'] <= 0) {
                return LoginReturnType::WRONG_INPUT_EXCEEDED;
            }

            if (empty($result['account_password'])) {
                return LoginReturnType::EMPTY_PASSWORD;
            }

            if (password_verify($password, $result['account_password'])) {
                return $result['account_id'];
            }

            return LoginReturnType::WRONG_PASSWORD;
        } catch (\Exception $e) {
            return LoginReturnType::FAILURE;
        }
    }
}
