<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\LocalizationMapper;
use phpOMS\Localization\Localization;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Admin\Models\LocalizationMapper::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Admin\tests\Models\LocalizationMapperTest: Localization database mapper')]
final class LocalizationMapperTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The model can be created and read from the database')]
    public function testCR() : void
    {
        $localization = Localization::fromJson(
            \json_decode(\file_get_contents(__DIR__ . '/om_OMS.json'), true)
        );

        $id                 = LocalizationMapper::create()->execute($localization);
        $localizationRemote = LocalizationMapper::get()->where('id', $id)->execute();

        self::assertEquals('fahrenheit', $localizationRemote->getTemperature());
        self::assertEquals($localization->jsonSerialize(), $localizationRemote->jsonSerialize());
    }
}
