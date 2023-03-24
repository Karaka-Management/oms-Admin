<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\Group;
use Modules\Admin\Models\GroupMapper;
use Modules\Admin\Models\GroupPermission;
use Modules\Admin\Models\GroupPermissionMapper;

/**
 * @testdox Modules\Admin\tests\Models\GroupMapperTest: Group mapper
 *
 * @internal
 */
final class GroupMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox All groups which have permissions for a module can be returned
     * @covers Modules\Admin\Models\GroupMapper
     * @group module
     */
    public function testGroupPermissionForModule() : void
    {
        $group   = new Group('test');
        $groupId = GroupMapper::create()->execute($group);

        $permission = new GroupPermission($groupId, null, null, 'Admin');
        GroupPermissionMapper::create()->execute($permission);

        $permissions = GroupMapper::getPermissionForModule('Admin');

        foreach ($permissions as $p) {
            if ($p->getId() === $groupId) {
                self::assertTrue(true);
                return;
            }
        }

        self::assertTrue(false);
    }

    /**
     * @covers Modules\Admin\Models\GroupMapper
     * @group module
     */
    public function testCountMembers() : void
    {
        self::assertEquals([3 => 1], GroupMapper::countMembers());
        self::assertEquals([], GroupMapper::countMembers(1));
    }
}
