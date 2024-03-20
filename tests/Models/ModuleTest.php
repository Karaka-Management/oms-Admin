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

use Modules\Admin\Models\Module;
use phpOMS\Module\ModuleStatus;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Admin\Models\Module::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Admin\tests\Models\ModuleTest: Module container')]
final class ModuleTest extends \PHPUnit\Framework\TestCase
{
    protected Module $module;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->module = new Module();
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The module has the expected default values after initialization')]
    public function testDefault() : void
    {
        self::assertEquals('', $this->module->id);
        self::assertInstanceOf('\DateTimeImmutable', $this->module->createdAt);
        self::assertEquals('', $this->module->name);
        self::assertEquals(ModuleStatus::INACTIVE, $this->module->status);
        self::assertEquals(\json_encode($this->module->jsonSerialize()), $this->module->__toString());
        self::assertEquals($this->module->jsonSerialize(), $this->module->toArray());
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The name can be set and returned')]
    public function testNameInputOutput() : void
    {
        $this->module->name = 'Name';
        self::assertEquals('Name', $this->module->name);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The status can be set and returned')]
    public function testStatusInputOutput() : void
    {
        $this->module->status = ModuleStatus::ACTIVE;
        self::assertEquals(ModuleStatus::ACTIVE, $this->module->status);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The module can be serialized')]
    public function testSerializations() : void
    {
        self::assertEquals(\json_encode($this->module->jsonSerialize()), $this->module->__toString());
        self::assertEquals($this->module->jsonSerialize(), $this->module->toArray());
    }
}
