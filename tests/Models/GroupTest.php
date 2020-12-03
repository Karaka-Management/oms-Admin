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

use Modules\Admin\Models\Group;
use Modules\Admin\Models\NullAccount;

/**
 * @testdox Modules\Admin\tests\Models\GroupTest: Group model
 *
 * @internal
 */
class GroupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The group has the expected default values after initialization
     * @covers Modules\Admin\Models\Group
     * @group module
     */
    public function testDefault() : void
    {
        $group = new Group();
        self::assertEquals((new \DateTime('now'))->format('Y-m-d'), $group->createdAt->format('Y-m-d'));
        self::assertEquals(0, $group->createdBy->getId());
        self::assertEquals('', $group->descriptionRaw);
        self::assertEquals([], $group->getAccounts());
    }

    /**
     * @testdox The description can be set and returned
     * @covers Modules\Admin\Models\Group
     * @group module
     */
    public function testDescriptionInputOutput() : void
    {
        $group = new Group();

        $group->descriptionRaw = 'Some test';
        self::assertEquals('Some test', $group->descriptionRaw);
    }

    /**
     * @testdox The creator can be set and returned
     * @covers Modules\Admin\Models\Group
     * @group module
     */
    public function testCreatorInputOutput() : void
    {
        $group = new Group();

        $group->createdBy = new NullAccount(3);
        self::assertEquals(3, $group->createdBy->getId());
    }
}
