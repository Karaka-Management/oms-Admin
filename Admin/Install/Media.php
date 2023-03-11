<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Admin\Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Admin\Install;

use Model\Setting;
use Model\SettingMapper;
use Modules\Admin\Models\SettingsEnum;
use phpOMS\Application\ApplicationAbstract;

/**
 * Media class.
 *
 * @package Modules\Admin\Admin\Install
 * @license OMS License 1.0
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

        SettingMapper::create()->execute(
            new Setting(
                0,
                SettingsEnum::DEFAULT_LIST_EXPORTS,
                (string) $media['collection'][4]['id'],
                '\\d+',
                module: 'Admin'
            )
        );

        SettingMapper::create()->execute(
            new Setting(
                0,
                SettingsEnum::DEFAULT_LETTERS,
                (string) $media['collection'][5]['id'],
                '\\d+',
                module: 'Admin'
            )
        );

        SettingMapper::create()->execute(
            new Setting(
                0,
                SettingsEnum::DEFAULT_ASSETS,
                (string) $media['upload'][0]['id'],
                '\\d+',
                module: 'Admin'
            )
        );

        SettingMapper::create()->execute(
            new Setting(
                0,
                SettingsEnum::DEFAULT_TEMPLATES,
                (string) $media['upload'][1]['id'],
                '\\d+',
                module: 'Admin'
            )
        );
    }
}
