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

    /**
     * Create object from array
     *
     * @param array{id:int, name:array{0:string, 1:string, 2:string}, email:string, login:string, type:int, status:int, groups:array, permissions:array, tries:?int, addresses?:array, contacts?:array, parents?:array, l11n:array} $account Account data
     *
     * @return self
     *
     * @since 1.0.0
     */
    public static function fromJson(array $account) : self
    {
        $new = new self();
        $new->from($account);

        $new->tries = $account['tries'] ?? 0;

        foreach (($account['addresses'] ?? []) as $address) {
            $new->addresses[] = \phpOMS\Stdlib\Base\Address::fromJson($address);
        }

        foreach (($account['contacts'] ?? []) as $contact) {
            $new->contacts[] = Contact::fromJson($contact);
        }

        foreach (($account['parents'] ?? []) as $parent) {
            $new->parents[] = self::fromJson($parent);
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return \array_merge(
            parent::toArray(),
            [
                'tries'     => $this->tries,
                'addresses' => $this->addresses,
                'contacts'  => $this->contacts,
                'parents'   => $this->parents,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
