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
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Stdlib\Base\Location;

/**
 * Address model
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
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
    public function jsonSerialize() : mixed
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
    public function unserialize(mixed $serialized) : void
    {
        parent::unserialize($serialized);

        if (!\is_string($serialized)) {
            return;
        }

        /** @var array{name:string, addition:string} $data */
        $data = \json_decode($serialized, true);
        if (!\is_array($data)) {
            return;
        }

        $this->name     = $data['name'];
        $this->addition = $data['addition'];
    }
}
