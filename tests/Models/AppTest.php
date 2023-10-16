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

use Modules\Admin\Models\App;
use phpOMS\Application\ApplicationStatus;
use phpOMS\Application\ApplicationType;

/**
 * @testdox Modules\Admin\tests\Models\AppTest: App model
 *
 * @internal
 */
final class AppTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Admin\Models\App
     * @group module
     */
    public function testDefault() : void
    {
        $app = new App();
        self::assertEquals(0, $app->id);
    }

    public function testToArray() : void
    {
        $app = new App();
        self::assertEquals(
            [
                'id' => 0,
                'name' => '',
                'type' => ApplicationType::WEB,
                'status' => ApplicationStatus::NORMAL,
            ],
            $app->toArray()
        );
    }

    public function testJsonSerialize() : void
    {
        $app = new App();
        self::assertEquals(
            [
                'id' => 0,
                'name' => '',
                'type' => ApplicationType::WEB,
                'status' => ApplicationStatus::NORMAL,
            ],
            $app->jsonSerialize()
        );
    }
}
