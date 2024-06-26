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

namespace Modules\Admin\Controller;

use Model\SettingMapper;
use Modules\Admin\Models\SettingsEnum;
use phpOMS\Application\ApplicationStatus;
use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Security\EncryptionHelper;
use phpOMS\Views\View;

/**
 * Admin controller class.
 *
 * This class is responsible for the basic admin activities such as managing accounts, groups, permissions and modules.
 *
 * @package Modules\Admin
 * @license OMS License 2.0
 * @link    https://jingga.app
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
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewEmptyCommand(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        if ($request->hasData('v')) {
            $view->setTemplate('/Modules/Admin/Theme/Cli/version-command');
        } else {
            $view->setTemplate('/Modules/Admin/Theme/Cli/empty-command');
        }

        return $view;
    }

    /**
     * Find and run events
     *
     * This is mostly used by the web applications to offload searching for event hooks and of course running the events which might take a long time for complex events.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function cliRunEvent(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $event = $this->app->eventManager->triggerSimilar(
            $request->getDataString('-g') ?? '',
            $request->getDataString('-i') ?? '',
            $request->getDataJson('-d')
        );

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Cli/event-result');

        $view->data['event'] = $event;

        return $view;
    }

    /**
     * Find and run events
     *
     * This is mostly used by the web applications to offload searching for event hooks and of course running the events which might take a long time for complex events.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface Response can be rendered
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function cliEncryptionChange(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $event = $this->app->eventManager->trigger(
            'Module:' . self::NAME . '-encryption-change', '', [
                'old' => $request->getDataString('-old') ?? '',
                'new' => $request->getDataString('-new') ?? '',
            ]
        );

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Admin/Theme/Cli/encryption-change');

        $view->data['event'] = $event;

        return $view;
    }

    /**
     * Api method to make a call to the cli app
     *
     * @param string ...$data Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function runEncryptionChangeFromHook(mixed ...$data) : void
    {
        /** @var \Model\Setting $setting */
        $setting = $this->app->appSettings->get(null, names: SettingsEnum::LOGIN_STATUS);
        $oldMode = $setting->content;

        // Enter read only mode
        $setting->content = (string) ApplicationStatus::READ_ONLY;
        $this->app->appSettings->save([$setting]);

        $mapper = SettingMapper::yield()
            ->where('isEncrypted', true);

        foreach ($mapper->executeYield() as $setting) {
            $decrypted = empty($data['old']) || empty($setting->content)
                ? $setting->content
                : EncryptionHelper::decryptShared($setting->content ?? '', $data['old']);

            $encrypted = empty($data['new']) || empty($decrypted)
                ? $decrypted
                : EncryptionHelper::encryptShared($decrypted ?? '', $data['new']);

            $setting->content = $encrypted;

            SettingMapper::update()->execute($setting);
        }

        // Restore old mode
        $setting->content = $oldMode;
        $this->app->appSettings->save([$setting]);
    }
}
