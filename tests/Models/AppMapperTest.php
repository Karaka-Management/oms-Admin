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
use Modules\Admin\Models\AppMapper;
use phpOMS\Application\ApplicationStatus;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Admin\Models\AppMapper::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Admin\tests\Models\AppMapperTest: App database mapper')]
final class AppMapperTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The model can be created and read from the database')]
    public function testCR() : void
    {
        $app = new App();

        $app->name   = 'TestAppName';
        $app->theme  = 'Default';
        $app->status = ApplicationStatus::NORMAL;

        $id = AppMapper::create()->execute($app);
        self::assertGreaterThan(0, $app->id);
        self::assertEquals($id, $app->id);

        $appR = AppMapper::get()->where('id', $app->id)->execute();
        self::assertEquals($app->name, $appR->name);
        self::assertEquals($app->theme, $appR->theme);
        self::assertEquals($app->status, $appR->status);
    }
}
