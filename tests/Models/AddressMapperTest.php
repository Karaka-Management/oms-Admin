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

use Modules\Admin\Models\Address;
use Modules\Admin\Models\AddressMapper;
use phpOMS\Utils\TestUtils;
use phpOMS\Stdlib\Base\AddressType;

/**
 * @testdox Modules\Admin\tests\Models\AddressMapperTest: Address database mapper
 *
 * @internal
 */
class AddressMapperTest extends \PHPUnit\Framework\TestCase
{
	/**
     * @covers Modules\Admin\Models\AddressMapper
     * @group module
     */
	public function testCR() : void
    {
        $address = new Address();

        $address->name = 'name';
        $address->addition = 'addition';
        $address->postal = '0123456789';
        $address->setType(AddressType::BUSINESS);
        $address->city    = 'city';
        $address->address = 'Some address here';
        $address->state   = 'This is a state 123';
        $address->setCountry('DE');
        $address->setGeo(['lat' => 12.1, 'long' => 11.2,]);

        $id = AddressMapper::create($address);
        self::assertGreaterThan(0, $address->getId());
        self::assertEquals($id, $address->getId());

        $addressR = AddressMapper::get($address->getId());
        self::assertEquals($address->name, $addressR->name);
        self::assertEquals($address->addition, $addressR->addition);
        self::assertEquals($address->getType(), $addressR->getType());
        self::assertEquals($address->postal, $addressR->postal);
        self::assertEquals($address->address, $addressR->address);
        self::assertEquals($address->state, $addressR->state);
    }
}