<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Theme\Backend\Components\ContactEditor;

use phpOMS\Localization\L11nManager;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * Component view.
 *
 * @package Modules\Admin
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
class ContactView extends View
{
    /**
     * Contact
     *
     * @var \Modules\Admin\Models\Contact[]
     * @since 1.0.0
     */
    public array $contacts = [];

    /**
     * Form id
     *
     * @var string
     * @since 1.0.0
     */
    public string $form = '';

    /**
     * Virtual path of the media file
     *
     * @var string
     * @since 1.0.0
     */
    public string $virtualPath = '';

    /**
     * Name of the image preview
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * API Uri for attribute actions
     *
     * @var string
     * @since 1.0.0
     */
    public string $apiUri = '';

    /**
     * Reference id
     *
     * @var int
     * @since 1.0.0
     */
    public int $refId = 0;

    /**
     * {@inheritdoc}
     */
    public function __construct(L11nManager $l11n, RequestAbstract $request, ResponseAbstract $response)
    {
        parent::__construct($l11n, $request, $response);
        $this->setTemplate('/Modules/Admin/Theme/Backend/Components/ContactEditor/contacts');
    }

    /**
     * {@inheritdoc}
     */
    public function render(mixed ...$data) : string
    {
        /** @var array{0:string, 1?:string, 2?:array} $data */
        $this->form        = $data[0];
        $this->virtualPath = $data[1] ?? $this->virtualPath;
        $this->contacts    = $data[2] ?? $this->contacts;

        return parent::render();
    }
}
