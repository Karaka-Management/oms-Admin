<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\GroupPermission;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Admin\Models\GroupPermission::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Admin\tests\Models\GroupPermissionTest: Group permission')]
final class GroupPermissionTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The group permission has the expected default values after initialization')]
    public function testDefault() : void
    {
        $group = new GroupPermission();
        self::assertEquals(0, $group->getGroup());
    }
}
