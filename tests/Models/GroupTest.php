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

use Modules\Admin\Models\Group;
use Modules\Admin\Models\NullAccount;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Admin\Models\Group::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Admin\tests\Models\GroupTest: Group model')]
final class GroupTest extends \PHPUnit\Framework\TestCase
{
    protected Group $group;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->group = new Group();
    }

    /**
     * @group module
     */
    #[\PHPUnit\Framework\Attributes\TestDox('The group has the expected default values after initialization')]
    public function testDefault() : void
    {
        self::assertEquals((new \DateTime('now'))->format('Y-m-d'), $this->group->createdAt->format('Y-m-d'));
        self::assertEquals(0, $this->group->createdBy->id);
        self::assertEquals('', $this->group->descriptionRaw);
        self::assertEquals([], $this->group->getAccounts());
    }

    /**
     * @group module
     */
    #[\PHPUnit\Framework\Attributes\TestDox('The description can be set and returned')]
    public function testDescriptionInputOutput() : void
    {
        $this->group->descriptionRaw = 'Some test';
        self::assertEquals('Some test', $this->group->descriptionRaw);
    }

    /**
     * @group module
     */
    #[\PHPUnit\Framework\Attributes\TestDox('The creator can be set and returned')]
    public function testCreatorInputOutput() : void
    {
        $this->group->createdBy = new NullAccount(3);
        self::assertEquals(3, $this->group->createdBy->id);
    }
}
