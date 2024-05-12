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
 * Contact element class.
 *
 * Information such as phone number, email, ...
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Contact
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Contact element type.
     *
     * @var int
     * @since 1.0.0
     */
    public int $type = ContactType::EMAIL;

    /**
     * Contact element subtype.
     *
     * @var int
     * @since 1.0.0
     */
    public int $subtype = 0;

    public string $title = '';

    /**
     * Content.
     *
     * @var string
     * @since 1.0.0
     */
    public string $content = '';

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
            'id' => $this->id,
            'type' => $this->type,
            'subtype'  => $this->subtype,
            'title'  => $this->title,
            'content'  => $this->content,
        ];
    }

    public static function fromJson(array $address) : self
    {
        $new = new self();
        $new->id = $address['id'];
        $new->type = $address['type'];
        $new->subtype = $address['subtype'];
        $new->title = $address['title'];
        $new->content = $address['content'];

        return $new;
    }
}
