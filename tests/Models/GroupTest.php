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

use Modules\Admin\Models\Group;
use Modules\Admin\Models\NullAccount;

/**
 * @testdox Modules\Admin\tests\Models\GroupTest: Group model
 *
 * @internal
 */
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
     * @testdox The group has the expected default values after initialization
     * @covers Modules\Admin\Models\Group
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals((new \DateTime('now'))->format('Y-m-d'), $this->group->createdAt->format('Y-m-d'));
        self::assertEquals(0, $this->group->createdBy->getId());
        self::assertEquals('', $this->group->descriptionRaw);
        self::assertEquals([], $this->group->getAccounts());
    }

    /**
     * @testdox The description can be set and returned
     * @covers Modules\Admin\Models\Group
     * @group module
     */
    public function testDescriptionInputOutput() : void
    {
        $this->group->descriptionRaw = 'Some test';
        self::assertEquals('Some test', $this->group->descriptionRaw);
    }

    /**
     * @testdox The creator can be set and returned
     * @covers Modules\Admin\Models\Group
     * @group module
     */
    public function testCreatorInputOutput() : void
    {
        $this->group->createdBy = new NullAccount(3);
        self::assertEquals(3, $this->group->createdBy->getId());
    }
}
