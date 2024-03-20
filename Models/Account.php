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
 * Account class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 2.0
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
     * @var \phpOMS\Stdlib\Base\Address[]
     * @since 1.0.0
     */
    public array $addresses = [];

    /**
     * Contact data.
     *
     * @var Contact[]
     * @since 1.0.0
     */
    public array $contacts = [];

    /**
     * Get the main contact element by type
     *
     * @param int $type Contact element type
     *
     * @return Contact
     *
     * @since 1.0.0
     */
    public function getContactByType(int $type) : Contact
    {
        foreach ($this->contacts as $element) {
            if ($element->type === $type) {
                return $element;
            }
        }

        return new NullContact();
    }
}
