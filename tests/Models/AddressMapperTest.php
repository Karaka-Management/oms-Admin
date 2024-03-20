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

use Modules\Admin\Models\AddressMapper;
use phpOMS\Stdlib\Base\Address;
use phpOMS\Stdlib\Base\AddressType;

/**
 * @testdox Modules\Admin\tests\Models\AddressMapperTest: Address database mapper
 *
 * @internal
 */
final class AddressMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Modules\Admin\Models\AddressMapper
     * @group module
     */
    public function testCR() : void
    {
        $address = new Address();

        $address->name            = 'name';
        $address->fao             = 'fao';
        $address->addressAddition = 'addition';
        $address->postal          = '0123456789';
        $address->type            = AddressType::BUSINESS;
        $address->city            = 'city';
        $address->address         = 'Some address here';
        $address->state           = 'This is a state 123';
        $address->country         = 'DE';
        $address->lat             = 12.1;
        $address->lon             = 11.2;

        $id = AddressMapper::create()->execute($address);
        self::assertGreaterThan(0, $address->id);
        self::assertEquals($id, $address->id);

        $addressR = AddressMapper::get()->where('id', $address->id)->execute();
        self::assertEquals($address->name, $addressR->name);
        self::assertEquals($address->fao, $addressR->fao);
        self::assertEquals($address->addressAddition, $addressR->addressAddition);
        self::assertEquals($address->type, $addressR->type);
        self::assertEquals($address->postal, $addressR->postal);
        self::assertEquals($address->address, $addressR->address);
        self::assertEquals($address->state, $addressR->state);
    }
}
