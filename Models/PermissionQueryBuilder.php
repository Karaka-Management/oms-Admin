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

use phpOMS\DataStorage\Database\Connection\ConnectionAbstract;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Query\Where;

/**
 * Mapper class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class PermissionQueryBuilder
{
    private ConnectionAbstract $connection;

    private array $groups = [];

    private int $account = 0;

    private array $units = [null];

    private array $apps = [null];

    private array $modules = [null];

    private array $types = [null];

    private int $permission = 0;

    public function __construct(ConnectionAbstract $connection)
    {
        $this->connection = $connection;
    }

    public function groups(array $groups) : self
    {

        $this->groups = $groups;

        return $this;
    }

    public function account(int $account) : self
    {
        $this->account = $account;

        return $this;
    }

    public function units(array $units) : self
    {
        $this->units = $units;

        return $this;
    }

    public function apps(array $apps) : self
    {
        $this->apps = $apps;

        return $this;
    }

    public function types(array $types) : self
    {
        $this->types = $types;

        return $this;
    }

    public function modules(array $modules) : self
    {
        $this->modules = $modules;

        return $this;
    }

    public function permission(int $permission) : self
    {
        $this->permission = $permission;

        return $this;
    }

    public function query(string $idField) : Builder
    {
        $where = new Where($this->connection);

        // Handle account permissions
        if (!empty($this->account)) {
            $accountPermission = new Builder($this->connection);
            $accountPermission->select('account_permission_element')
                ->from('account_permission')
                ->where('account_permission_account', '=', $this->account);

            $subWhere = new Where($this->connection);
            foreach ($this->units as $unit) {
                $subWhere->orWhere('account_permission_unit', '=', $unit);
            }

            $accountPermission->where($subWhere);

            $subWhere = new Where($this->connection);
            foreach ($this->apps as $app) {
                $subWhere->orWhere('account_permission_app', '=', $app);
            }

            $accountPermission->where($subWhere);

            $subWhere = new Where($this->connection);
            foreach ($this->modules as $module) {
                $subWhere->orWhere('account_permission_module', '=', $module);
           }

            $accountPermission->where($subWhere);

            $subWhere = new Where($this->connection);
            foreach ($this->types as $type) {
                $subWhere->orWhere('account_permission_type', '=', $type);
            }

            $accountPermission->where($subWhere);

            $accountPermission->where('account_permission_permission', '>', $this->permission);
            $where->where($idField, 'in', $accountPermission);
        }

        // Handle group permissions
        if (!empty($this->groups)) {
            $groupPermission = new Builder($this->connection);
            $groupPermission->select('group_permission_element')
                ->from('group_permission')
                ->where('group_permission_group', 'IN', $this->groups);

            $subWhere = new Where($this->connection);
            foreach ($this->units as $unit) {
                $subWhere->orWhere('group_permission_unit', '=', $unit);
            }

            $groupPermission->where($subWhere);

            $subWhere = new Where($this->connection);
            foreach ($this->apps as $app) {
                $subWhere->orWhere('group_permission_app', '=', $app);
            }

            $groupPermission->where($subWhere);

            $subWhere = new Where($this->connection);
            foreach ($this->modules as $module) {
                $subWhere->orWhere('group_permission_module', '=', $module);
            }

            $groupPermission->where($subWhere);

            $subWhere = new Where($this->connection);
            foreach ($this->types as $type) {
                $subWhere->orWhere('group_permission_type', '=', $type);
            }

            $groupPermission->where($subWhere);

            $groupPermission->where('group_permission_permission', '>', $this->permission);
            $where->orWhere($idField, 'in', $groupPermission);
        }

        return $where;
    }
}
