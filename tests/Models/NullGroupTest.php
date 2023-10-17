<?php
/**
 * Jingga
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

use Modules\Admin\Models\NullGroup;

/**
 * @internal
 */
final class NullGroupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Admin\Models\NullGroup
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Admin\Models\Group', new NullGroup());
    }

    /**
     * @covers Modules\Admin\Models\NullGroup
     * @group module
     */
    public function testId() : void
    {
        $null = new NullGroup(2);
        self::assertEquals(2, $null->id);
    }

    /**
     * @covers Modules\Admin\Models\NullModule
     * @group module
     */
    public function testJsonSerialize() : void
    {
        $null = new NullGroup(2);
        self::assertEquals(['id' => 2], $null->jsonSerialize());
    }
}
