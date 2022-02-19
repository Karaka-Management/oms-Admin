<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\NullGroupPermission;

/**
 * @internal
 */
final class NullGroupPermissionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Admin\Models\NullGroupPermission
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Admin\Models\GroupPermission', new NullGroupPermission());
    }

    /**
     * @covers Modules\Admin\Models\NullGroupPermission
     * @group module
     */
    public function testId() : void
    {
        $null = new NullGroupPermission(2);
        self::assertEquals(2, $null->getId());
    }
}
