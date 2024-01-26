<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Module\ModuleStatus;

/**
 * Module class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
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
    public int $status = ModuleStatus::INACTIVE;

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
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'path'      => $this->path,
            'version'   => $this->version,
            'status'    => $this->status,
            'createdAt' => $this->createdAt,
        ];
    }
}
