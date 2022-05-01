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
 * @link      https://karaka.app
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
 * @link    https://karaka.app
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

        $defaultPdfExport   = (int) \reset($media['upload'][0]);
        $defaultExcelExport = (int) \reset($media['upload'][1]);
        $defaultCsvExport   = (int) \reset($media['upload'][2]);
        $defaultWordExport  = (int) \reset($media['upload'][3]);
        $defaultEmailExport = (int) \reset($media['upload'][4]);

        SettingMapper::create()->execute(
            new Setting(0, SettingsEnum::DEFAULT_PDF_EXPORT_TEMPLATE, (string) $defaultPdfExport, '\\d+', 1, 'Admin')
        );
        SettingMapper::create()->execute(
            new Setting(0, SettingsEnum::DEFAULT_EXCEL_EXPORT_TEMPLATE, (string) $defaultExcelExport, '\\d+', 1, 'Admin')
        );
        SettingMapper::create()->execute(
            new Setting(0, SettingsEnum::DEFAULT_CSV_EXPORT_TEMPLATE, (string) $defaultCsvExport, '\\d+', 1, 'Admin')
        );
        SettingMapper::create()->execute(
            new Setting(0, SettingsEnum::DEFAULT_WORD_EXPORT_TEMPLATE, (string) $defaultWordExport, '\\d+', 1, 'Admin')
        );
        SettingMapper::create()->execute(
            new Setting(0, SettingsEnum::DEFAULT_EMAIL_EXPORT_TEMPLATE, (string) $defaultEmailExport, '\\d+', 1, 'Admin')
        );
    }
}
