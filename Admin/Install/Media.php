<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Admin\Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Admin\Install;

use Modules\Admin\Models\SettingsEnum;
use phpOMS\Application\ApplicationAbstract;

/**
 * Media class.
 *
 * @package Modules\Admin\Admin\Install
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Media
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
        $media = \Modules\Media\Admin\Installer::installExternal($app, ['path' => __DIR__ . '/Media.install.json']);

        \Modules\Admin\Admin\Installer::installExternal($app,
            [
                'data' => [
                    [
                        'type'    => 'setting',
                        'name'    => SettingsEnum::DEFAULT_LIST_EXPORTS,
                        'content' => (string) $media['collection'][4]['id'],
                        'pattern' => '\\d+',
                        'module'  => 'Admin',
                    ],
                    [
                        'type'    => 'setting',
                        'name'    => SettingsEnum::DEFAULT_LETTERS,
                        'content' => (string) $media['collection'][5]['id'],
                        'pattern' => '\\d+',
                        'module'  => 'Admin',
                    ],
                    [
                        'type'    => 'setting',
                        'name'    => SettingsEnum::DEFAULT_ASSETS,
                        'content' => (string) $media['upload'][0]['id'],
                        'pattern' => '\\d+',
                        'module'  => 'Admin',
                    ],
                    [
                        'type'    => 'setting',
                        'name'    => SettingsEnum::DEFAULT_TEMPLATES,
                        'content' => (string) $media['upload'][1]['id'],
                        'pattern' => '\\d+',
                        'module'  => 'Admin',
                    ],
                ],
            ]
        );
    }
}
