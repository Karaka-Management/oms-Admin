<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Module\ModuleStatus;
use phpOMS\Stdlib\Base\Exception\InvalidEnumValue;

/**
 * Module class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
class Module
{
    /**
     * Module id.
     *
     * @var string
     * @since 1.0.0
     */
    public string $id = '';

    /**
     * Module name.
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * Module path.
     *
     * @var string
     * @since 1.0.0
     */
    public string $path = '';

    /**
     * Module version.
     *
     * @var string
     * @since 1.0.0
     */
    public string $version = '';

    /**
     * Module theme.
     *
     * @var string
     * @since 1.0.0
     */
    public string $theme = '';

    /**
     * Group status.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $status = ModuleStatus::INACTIVE;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
    }

    /**
     * Get module id.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Get module status.
     *
     * @return int Module status
     *
     * @since 1.0.0
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Set module status.
     *
     * @param int $status Module status
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setStatus(int $status) : void
    {
        if (!ModuleStatusUpdateType::isValidValue($status)) {
            throw new InvalidEnumValue($status);
        }

        $this->status = $status;
    }

    /**
     * Get string representation.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function __toString()
    {
        return (string) \json_encode($this->toArray());
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
    public function toArray() : array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'path'           => $this->path,
            'version'        => $this->version,
            'status'         => $this->status,
            'createdAt'      => $this->createdAt,
        ];
    }
}
