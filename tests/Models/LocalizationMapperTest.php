<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\LocalizationMapper;
use phpOMS\Localization\Localization;

/**
 * @testdox Modules\Admin\tests\Models\LocalizationMapperTest: Localization database mapper
 *
 * @internal
 */
final class LocalizationMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The model can be created and read from the database
     * @covers Modules\Admin\Models\LocalizationMapper
     * @group module
     */
    public function testCR() : void
    {
        $localization = Localization::fromJson(
            \json_decode(\file_get_contents(__DIR__ . '/om_OMS.json'), true)
        );

        $id                 = LocalizationMapper::create($localization);
        $localizationRemote = LocalizationMapper::get($id);

        self::assertEquals('fahrenheit', $localizationRemote->getTemperature());
        self::assertEquals($localization->jsonSerialize(), $localizationRemote->jsonSerialize());
    }
}
