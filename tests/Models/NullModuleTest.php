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

use Modules\Admin\Models\NullModule;

/**
 * @internal
 */
final class NullModuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Admin\Models\NullModule
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Admin\Models\Module', new NullModule());
    }

    /**
     * @covers Modules\Admin\Models\NullModule
     * @group module
     */
    public function testId() : void
    {
        $null = new NullModule('Test');
        self::assertEquals('Test', $null->id);
    }

    /**
     * @covers Modules\Admin\Models\NullModule
     * @group module
     */
    public function testJsonSerialize() : void
    {
        $null = new NullModule('Test');
        self::assertEquals(['id' => 'Test'], $null->jsonSerialize());
    }
}
