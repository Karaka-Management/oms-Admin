<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Admin\Models\GroupMapper::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Admin\tests\Models\GroupMapperTest: Group mapper')]
final class GroupMapperTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('All groups which have permissions for a module can be returned')]
    public function testGroupPermissionForModule() : void
    {
        $group   = new Group('test');
        $groupId = GroupMapper::create()->execute($group);

        $permission = new GroupPermission($groupId, null, null, 'Admin');
        GroupPermissionMapper::create()->execute($permission);

        $permissions = GroupMapper::getPermissionForModule('Admin');

        foreach ($permissions as $p) {
            if ($p->id === $groupId) {
                self::assertTrue(true);
                return;
            }
        }

        self::assertTrue(false);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testCountMembers() : void
    {
        self::assertEquals([3 => 1, 1 => 2], GroupMapper::countMembers());
        self::assertEquals([1 => 2], GroupMapper::countMembers(1));
        self::assertEquals([], GroupMapper::countMembers(2));
    }
}
