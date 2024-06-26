<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Application\ApplicationStatus;
use phpOMS\Application\ApplicationType;

/**
 * App model.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class App implements \JsonSerializable
{
    /**
     * Id
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Name
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * Theme
     *
     * @var string
     * @since 1.0.0
     */
    public string $theme = '';

    /**
     * Status
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = ApplicationStatus::NORMAL;

    /**
     * Status
     *
     * @var int
     * @since 1.0.0
     */
    public int $type = ApplicationType::WEB;

    /**
     * Default unit
     *
     * @var null|int
     * @since 1.0.0
     */
    public ?int $defaultUnit = null;

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'type'   => $this->type,
            'status' => $this->status,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
