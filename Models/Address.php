<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Stdlib\Base\Location;

/**
 * Address model
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class Address extends Location
{
    /**
     * Name.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $name = '';

    /**
     * Addition.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $addition = '';

    /**
     * Get name
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * Get addition
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getAddition() : string
    {
        return $this->addition;
    }

    /**
     * Set addition
     *
     * @param string $addition Addition
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setAddition(string $addition) : void
    {
        $this->addition = $addition;
    }
}
