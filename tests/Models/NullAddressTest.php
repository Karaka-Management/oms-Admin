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

use Modules\Admin\Models\NullAddress;

/**
 * @internal
 */
final class NullAddressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Admin\Models\NullAddress
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Admin\Models\Address', new NullAddress());
    }

    /**
     * @covers Modules\Admin\Models\NullAddress
     * @group module
     */
    public function testId() : void
    {
        $null = new NullAddress(2);
        self::assertEquals(2, $null->id);
    }
}
