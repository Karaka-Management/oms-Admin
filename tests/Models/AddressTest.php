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

use Modules\Admin\Models\Address;
use phpOMS\Stdlib\Base\AddressType;

/**
 * @testdox Modules\Admin\tests\Models\AddressTest: Address model
 *
 * @internal
 */
final class AddressTest extends \PHPUnit\Framework\TestCase
{
    protected Address $address;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->address = new Address();
    }

    /**
     * @covers Modules\Admin\Models\Address
     * @group module
     */
    public function testDefault() : void
    {
        $expected = [
            'postal'  => '',
            'city'    => '',
            'country' => 'XX',
            'address' => '',
            'state'   => '',
            'geo'     => [
                'lat'  => 0,
                'long' => 0,
            ],
            'name'      => '',
            'addition'  => '',
        ];

        self::assertEquals('', $this->address->name);
        self::assertEquals('', $this->address->addition);
        self::assertEquals($expected, $this->address->toArray());
        self::assertEquals($expected, $this->address->jsonSerialize());
    }

    public function testToArray() : void
    {
        $expected = [
            'postal'  => '0123456789',
            'city'    => 'city',
            'country' => 'Country',
            'address' => 'Some address here',
            'state'   => 'This is a state 123',
            'lat'       => 12.1,
            'lon'       => 11.2,
            'name'      => 'name',
            'addition'  => 'addition',
        ];

        $this->address->name     = 'name';
        $this->address->addition = 'addition';
        $this->address->postal   = '0123456789';
        $this->address->setType(AddressType::BUSINESS);
        $this->address->city    = 'city';
        $this->address->address = 'Some address here';
        $this->address->state   = 'This is a state 123';
        $this->address->setCountry('Country');
        $this->address->lat = 12.1;
        $this->address->lon = 11.2;

        self::assertEquals($expected, $this->address->toArray());
    }

    public function testJsonSerialize() : void
    {
        $expected = [
            'postal'  => '0123456789',
            'city'    => 'city',
            'country' => 'Country',
            'address' => 'Some address here',
            'state'   => 'This is a state 123',
            'lat'       => 12.1,
            'lon'       => 11.2,
            'name'      => 'name',
            'addition'  => 'addition',
        ];

        $this->address->name     = 'name';
        $this->address->addition = 'addition';
        $this->address->postal   = '0123456789';
        $this->address->setType(AddressType::BUSINESS);
        $this->address->city    = 'city';
        $this->address->address = 'Some address here';
        $this->address->state   = 'This is a state 123';
        $this->address->setCountry('Country');
        $this->address->lat = 12.1;
        $this->address->lon = 11.2;

        self::assertEquals($expected, $this->address->jsonSerialize());
        self::assertEquals(\json_encode($this->address->jsonSerialize()), $this->address->serialize());
    }

    public function testUnserialize() : void
    {
        $expected = [
            'postal'   => '0123456789',
            'city'     => 'city',
            'country'  => 'Country',
            'address'  => 'Some address here',
            'state'    => 'This is a state 123',
            'lat'      => 12.1,
            'lon'      => 11.2,
            'name'     => 'name',
            'addition' => 'addition',
        ];

        $this->address->unserialize(\json_encode($expected));
        self::assertEquals(\json_encode($expected), $this->address->serialize());
    }
}
