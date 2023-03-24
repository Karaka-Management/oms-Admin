<?php
/**
 * Karaka
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

use phpOMS\Account\PermissionType;
use phpOMS\DataStorage\Database\Connection\ConnectionAbstract;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Query\Where;

/**
 * Query builder for selects which immediately check if a user/group has the appropriate permissions
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class PermissionQueryBuilder
{
    /**
     * Database connection
     *
     * @var ConnectionAbstract
     * @since 1.0.0
     */
    private ConnectionAbstract $connection;

    /**
     * Group ids.
     *
     * @var array
     * @since 1.0.0
     */
    private array $groups = [];

    /**
     * Account id.
     *
     * @var int
     */
    private int $account = 0;

    /**
     * Unit ids.
     *
     * @var array
     * @since 1.0.0
     */
    private array $units = [null];

    /**
     * Ap ids.
     *
     * @var array
     * @since 1.0.0
     */
    private array $apps = [null];

    /**
     * Module names.
     *
     * @var array
     * @since 1.0.0
     */
    private array $modules = [null];

    /**
     * Category ids.
     *
     * @var array
     * @since 1.0.0
     */
    private array $categories = [null];

    /**
     * Permission flag
     *
     * @var int
     * @since 1.0.0
     */
    private int $permission = 0;

    /**
     * Constructor.
     *
     * @param ConnectionAbstract $connection Database connection
     *
     * @since 1.0.0
     */
    public function __construct(ConnectionAbstract $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Set group ids
     *
     * @param array $groups Group ids
     *
     * @return self
     *
     * @since 1.0.0
     */
    public function groups(array $groups) : self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Set account id
     *
     * @param int $account Account id
     *
     * @return self
     *
     * @since 1.0.0
     */
    public function account(int $account) : self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Set unit ids
     *
     * @param array $units Unit ids
     *
     * @return self
     *
     * @since 1.0.0
     */
    public function units(array $units) : self
    {
        $this->units = $units;

        return $this;
    }

    /**
     * Set app ids
     *
     * @param array $apps App ids
     *
     * @return self
     *
     * @since 1.0.0
     */
    public function apps(array $apps) : self
    {
        $this->apps = $apps;

        return $this;
    }

    /**
     * Set category ids
     *
     * @param array $categories Category ids
     *
     * @return self
     *
     * @since 1.0.0
     */
    public function categories(array $categories) : self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Set module ids
     *
     * @param array $modules Module ids
     *
     * @return self
     *
     * @since 1.0.0
     */
    public function modules(array $modules) : self
    {
        $this->modules = $modules;

        return $this;
    }

    /**
     * Set permission flags
     *
     * @param int $permission Permission flags
     *
     * @return self
     *
     * @since 1.0.0
     */
    public function permission(int $permission) : self
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Create permission sub query for
     *
     * The sub query checks permissons only for specific models/db entries.
     * More general permissions for an entier module etc. are handled differently.
     * The reason individual models/db entries are handled this way is because this process is very slow and therefore the general check should be done first and only if that doesn't give results this very specifc solution should be used.
     *
     * @param string $idField Table column which contains the primary id (this is the field the permission is associated with)
     *
     * @return Builder
     *
     * @since 1.0.0
     */
    public function query(string $idField) : Builder
    {
        $where = new Where($this->connection);

        $hasRead       = ($this->permission & PermissionType::READ) === PermissionType::READ;
        $hasCreate     = ($this->permission & PermissionType::CREATE) === PermissionType::CREATE;
        $hasModify     = ($this->permission & PermissionType::MODIFY) === PermissionType::MODIFY;
        $hasDelete     = ($this->permission & PermissionType::DELETE) === PermissionType::DELETE;
        $hasPermission = ($this->permission & PermissionType::PERMISSION) === PermissionType::PERMISSION;

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
            foreach ($this->categories as $category) {
                $subWhere->orWhere('account_permission_category', '=', $category);
            }

            $accountPermission->where($subWhere);

            if ($hasRead) {
                $accountPermission->where('account_permission_hasread', '=', $hasRead);
            }

            if ($hasCreate) {
                $accountPermission->where('account_permission_hascreate', '=', $hasCreate);
            }

            if ($hasModify) {
                $accountPermission->where('account_permission_hasmodify', '=', $hasModify);
            }

            if ($hasDelete) {
                $accountPermission->where('account_permission_hasdelete', '=', $hasDelete);
            }

            if ($hasPermission) {
                $accountPermission->where('account_permission_haspermission', '=', $hasPermission);
            }

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
            foreach ($this->categories as $category) {
                $subWhere->orWhere('group_permission_category', '=', $category);
            }

            $groupPermission->where($subWhere);

            if ($hasRead) {
                $groupPermission->where('group_permission_hasread', '=', $hasRead);
            }

            if ($hasCreate) {
                $groupPermission->where('group_permission_hascreate', '=', $hasCreate);
            }

            if ($hasModify) {
                $groupPermission->where('group_permission_hasmodify', '=', $hasModify);
            }

            if ($hasDelete) {
                $groupPermission->where('group_permission_hasdelete', '=', $hasDelete);
            }

            if ($hasPermission) {
                $groupPermission->where('group_permission_haspermission', '=', $hasPermission);
            }

            $where->orWhere($idField, 'in', $groupPermission);
        }

        return $where;
    }
}
