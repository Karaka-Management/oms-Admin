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

use Modules\Admin\Models\DataChange;

/**
 * @testdox Modules\Admin\tests\Models\DataChangeTest: DataChange model
 *
 * @internal
 */
final class DataChangeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Modules\Admin\Models\DataChange
     * @group module
     */
    public function testDefault() : void
    {
        $change = new DataChange();
        self::assertEquals(0, $change->id);
        self::assertEquals(32, \strlen($change->getHash()));
        self::assertInstanceOf('\DateTimeImmutable', $change->createdAt);
    }

    public function testReHash() : void
    {
        $change = new DataChange();
        $hash   = $change->getHash();

        $change->reHash();
        self::assertNotEquals($hash, $change->getHash());
    }

    public function testToArray() : void
    {
        $change = new DataChange();
        self::assertEquals(
            [
                'id'   => 0,
                'data' => '',
            ],
            $change->toArray()
        );
    }

    public function testJsonSerialize() : void
    {
        $change = new DataChange();
        self::assertEquals(
            [
                'id'   => 0,
                'data' => '',
            ],
            $change->jsonSerialize()
        );
    }
}
