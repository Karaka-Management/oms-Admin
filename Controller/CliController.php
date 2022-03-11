<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Admin\Controller;

use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * Admin controller class.
 *
 * This class is responsible for the basic admin activities such as managing accounts, groups, permissions and modules.
 *
 * @package Modules\Admin
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class CliController extends Controller
{
    /**
     * Method which generates the general settings view.
     *
     * In this view general settings for the entire application can be seen and adjusted. Settings which can be modified
     * here are localization, password, database, etc.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewEmptyCommand(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Cli/empty-command');

        return $view;
    }

    /**
     * Find and run events
     *
     * This is mostly used by the web applications to offload searching for event hooks and of course running the events which might take a long time for complex events.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function cliRunEvent(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $event = $this->app->eventManager->triggerSimilar(
            $request->getData('g'),
            $request->getData('i'),
            \json_decode($request->getData('d'), true)
        );

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Cli/event-result');

        $view->setData('event', $event);

        return $view;
    }
}
