<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Admin\Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Admin\Install;

use Modules\Admin\Models\SettingsEnum;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;

/**
 * Media class.
 *
 * @package Modules\Admin\Admin\Install
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Messages
{
    /**
     * Install media providing
     *
     * @param ApplicationAbstract $app  Application
     * @param string              $path Module path
     *
     * @return void
     *
     * @since 1.0.0
     */
    public static function install(ApplicationAbstract $app, string $path) : void
    {
        $messages = \Modules\Messages\Admin\Installer::installExternal($app, ['path' => __DIR__ . '/Messages.install.json']);

        /** @var \Modules\Admin\Controller\ApiController $module */
        $module = $app->moduleManager->get('Admin');

        $settings = [
            [
                'id'      => null,
                'name'    => SettingsEnum::LOGIN_MAIL_REGISTRATION_TEMPLATE,
                'content' => (string) $messages['email_template'][0]['id'],
                'module'  => 'Admin',
            ],
            [
                'id'      => null,
                'name'    => SettingsEnum::LOGIN_MAIL_FORGOT_PASSWORD_TEMPLATE,
                'content' => (string) $messages['email_template'][1]['id'],
                'module'  => 'Admin',
            ],
            [
                'id'      => null,
                'name'    => SettingsEnum::LOGIN_MAIL_FAILED_TEMPLATE,
                'content' => (string) $messages['email_template'][2]['id'],
                'module'  => 'Admin',
            ],
            [
                'id'      => null,
                'name'    => SettingsEnum::LOGIN_MAIL_RESET_PASSWORD_TEMPLATE,
                'content' => (string) $messages['email_template'][3]['id'],
                'module'  => 'Admin',
            ],
        ];

        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('settings', \json_encode($settings));

        $module->apiSettingsSet($request, $response);
    }
}
