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

/**
 * Data change model.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class DataChange
{
    /**
     * Id
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Hash
     *
     * @var string
     * @since 1.0.0
     */
    protected string $hash = '';

    /**
     * Data for the change
     *
     * @var string
     * @since 1.0.0
     */
    public string $data = '';

    /**
     * Change type
     *
     * @var string
     * @since 1.0.0
     */
    public string $type = '';

    /**
     * Created by account
     *
     * @var int
     * @since 1.0.0
     */
    public int $createdBy = 0;

    /**
     * Created at
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
        $this->reHash();
    }

    /**
     * Create hash for data change as identifier
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function reHash() : void
    {
        $this->hash = \bin2hex(\random_bytes(16));
    }

    /**
     * Get hash
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getHash() : string
    {
        return $this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'   => $this->id,
            'data' => $this->data,
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
