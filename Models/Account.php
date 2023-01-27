<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Stdlib\Base\Location;

/**
 * Account class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Account extends \phpOMS\Account\Account
{
    /**
     * Remaining login tries.
     *
     * @var int
     * @since 1.0.0
     */
    public int $tries = 0;

    /**
     * Password.
     *
     * @var string
     * @since 1.0.0
     */
    public string $tempPassword = '';

    /**
     * Parents.
     *
     * @var Account[]
     * @since 1.0.0
     */
    public array $parents = [];

    /**
     * Remaining login tries.
     *
     * @var null|\DateTimeImmutable
     * @since 1.0.0
     */
    public ?\DateTimeImmutable $tempPasswordLimit = null;

    /**
     * Location data.
     *
     * @var Location[]
     * @since 1.0.0
     */
    protected array $locations = [];

    /**
     * Contact data.
     *
     * @var Contact[]
     * @since 1.0.0
     */
    protected array $contacts = [];

    /**
     * Get account locations.
     *
     * @return Location[]
     *
     * @since 1.0.0
     */
    public function getLocations() : array
    {
        return $this->locations;
    }

    /**
     * Add location.
     *
     * @param Location $location Location
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addLocation(Location $location) : void
    {
        $this->locations[] = $location;
    }

    /**
     * Get account contact element.
     *
     * @return Contact[]
     *
     * @since 1.0.0
     */
    public function getContacts() : array
    {
        return $this->contacts;
    }

    /**
     * Add contact element.
     *
     * @param Contact $contact Contact Element
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addContact(Contact $contact) : void
    {
        $this->contacts[] = $contact;
    }
}
