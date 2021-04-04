<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
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
    public string $name = '';

    /**
     * Addition.
     *
     * @var string
     * @since 1.0.0
     */
    public string $addition = '';

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        $data = parent::toArray();

        $data['name']     = $this->name;
        $data['addition'] = $this->addition;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize() : string
    {
        return (string) \json_encode($this->jsonSerialize());
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized) : void
    {
        parent::unserialize($serialized);

        $data = \json_decode($serialized, true);

        $this->name     = $data['name'];
        $this->addition = $data['addition'];
    }
}
