<?php
/**
 * Karaka
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

/**
 * @testdox Modules\Admin\tests\Models\AppTest: App model
 *
 * @internal
 */
final class AppTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The account has the expected default values after initialization
     * @covers Modules\Admin\Models\App
     * @group module
     */
    public function testDefault() : void
    {
        $account = new App();
        self::assertEquals(0, $account->id);
    }
}
