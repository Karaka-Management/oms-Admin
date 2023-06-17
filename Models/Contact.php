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
    private int $subtype = 0;

    /**
     * Content.
     *
     * @var string
     * @since 1.0.0
     */
    public string $content = '';

    /**
     * Order.
     *
     * @var int
     * @since 1.0.0
     */
    public int $order = 0;

    public int $account = 0;

    public string $module = '';

    /**
     * Get id.
     *
     * @return int Model id
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param int $type Type
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setType(int $type) : void
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * Set subtype
     *
     * @param int $subtype Subtype
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setSubtype(int $subtype) : void
    {
        $this->subtype = $subtype;
    }

    /**
     * Get subtype
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getSubtype() : int
    {
        return $this->subtype;
    }
}
