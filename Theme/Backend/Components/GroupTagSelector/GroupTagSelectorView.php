<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Theme\Backend\Components\GroupTagSelector;

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
class GroupTagSelectorView extends View
{
    /**
     * Dom id
     *
     * @var string
     * @since 1.0.0
     */
    public string $id = '';

    /**
     * Is required?
     *
     * @var bool
     * @since 1.0.0
     */
    public bool $isRequired = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(L11nManager $l11n, RequestAbstract $request, ResponseAbstract $response)
    {
        parent::__construct($l11n, $request, $response);
        $this->setTemplate('/Modules/Admin/Theme/Backend/Components/GroupTagSelector/group-selector');

        $view = new GroupTagSelectorPopupView($l11n, $request, $response);
        $this->addData('group-selector-popup', $view);
    }

    /**
     * Is required?
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function isRequired() : bool
    {
        return $this->isRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function render(mixed ...$data) : string
    {
        /** @var array{0:string, 1:null|bool} $data */
        $this->id         = $data[0];
        $this->isRequired = $data[1] ?? false;

        $this->getData('group-selector-popup')->id = $this->id;

        return parent::render();
    }
}
