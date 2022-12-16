<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\GroupPermission;

/**
 * @testdox Modules\Admin\tests\Models\GroupPermissionTest: Group permission
 *
 * @internal
 */
final class GroupPermissionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The group permission has the expected default values after initialization
     * @covers Modules\Admin\Models\GroupPermission
     * @group module
     */
    public function testDefault() : void
    {
        $group = new GroupPermission();
        self::assertEquals(0, $group->getGroup());
    }
}
