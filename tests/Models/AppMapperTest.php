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

use Modules\Admin\Models\App;
use Modules\Admin\Models\AppMapper;
use phpOMS\Application\ApplicationStatus;

/**
 * @testdox Modules\Admin\tests\Models\AppMapperTest: App database mapper
 *
 * @internal
 */
final class AppMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The model can be created and read from the database
     * @covers Modules\Admin\Models\AppMapper
     * @group module
     */
    public function testCR() : void
    {
        $app = new App();

        $app->name   = 'TestAppName';
        $app->theme  = 'Default';
        $app->status = ApplicationStatus::NORMAL;

        $id = AppMapper::create()->execute($app);
        self::assertGreaterThan(0, $app->getId());
        self::assertEquals($id, $app->getId());

        $appR = AppMapper::get()->where('id', $app->getId())->execute();
        self::assertEquals($app->name, $appR->name);
        self::assertEquals($app->theme, $appR->theme);
        self::assertEquals($app->status, $appR->status);
    }
}