<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Theme\Backend\Components\AddressEditor;

use phpOMS\Localization\L11nManager;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * Component view.
 *
 * @package Modules\Admin
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
class AddressView extends View
{
    /**
     * Address
     *
     * @var \phpOMS\Stdlib\Base\Address[]
     * @since 1.0.0
     */
    public array $addresses = [];

    /**
     * Units
     *
     * @var \Modules\Organization\Models\Unit[]
     * @since 1.0.0
     */
    public array $units = [];

    /**
     * API Uri for address actions
     *
     * @var string
     * @since 1.0.0
     */
    public string $apiUri = '';

    /**
     * Reference id
     *
     * @var string
     * @since 1.0.0
     */
    public int $refId = 0;

    /**
     * {@inheritdoc}
     */
    public function __construct(L11nManager $l11n = null, RequestAbstract $request, ResponseAbstract $response)
    {
        parent::__construct($l11n, $request, $response);
        $this->setTemplate('/Modules/Admin/Theme/Backend/Components/AddressEditor/addresses');
    }

    /**
     * {@inheritdoc}
     */
    public function render(mixed ...$data) : string
    {
        /** @var array{0:\phpOMS\Stdlib\Base\Address[]} $data */
        $this->addresses     = $data[0];
        $this->units          = $data[1];
        $this->apiUri         = $data[2];
        $this->refId          = $data[3];

        return parent::render();
    }
}
