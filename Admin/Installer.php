<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Admin;

use Model\Setting;
use Model\SettingMapper;
use Modules\Admin\Models\LocalizationMapper;
use Modules\Admin\Models\SettingsEnum;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\Config\SettingsInterface;
use phpOMS\DataStorage\Database\Connection\SQLiteConnection;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Localization\Localization;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;
use phpOMS\System\File\PathException;
use phpOMS\System\OperatingSystem;
use phpOMS\System\SystemType;
use phpOMS\Uri\HttpUri;

/**
 * Installer class.
 *
 * @package Modules\Admin\Admin
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;

    /**
     * {@inheritdoc}
     */
    public static function install(ApplicationAbstract $app, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        parent::install($app, $info, $cfgHandler);

        $sqlite = new SQLiteConnection([
            'db'       => 'sqlite',
            'database' => __DIR__ . '/../../../phpOMS/Localization/Defaults/localization.sqlite',
        ]);

        self::installCountries($sqlite, $app->dbPool);
        self::installLanguages($sqlite, $app->dbPool);
        self::installCurrencies($sqlite, $app->dbPool);
        self::installDefaultSettings();

        $sqlite->close();

        $settings = include __DIR__ . '/Install/settings.php';
        foreach ($settings as $setting) {
            self::createSettings($app, $setting);
        }
    }

    /**
     * Install settings
     *
     * @return void
     *
     * @since 1.0.0
     **/
    private static function installDefaultSettings() : void
    {
        $cmdResult = \shell_exec(
            (OperatingSystem::getSystem() === SystemType::WIN
                ? 'php.exe'
                : 'php'
            ) . ' ' . __DIR__ . '/../../../../Cli/cli.php -v'
        );
        $cmdResult = $cmdResult === null || $cmdResult === false ? '' : $cmdResult;

        SettingMapper::create()->execute(
            new Setting(
                0,
                SettingsEnum::CLI_ACTIVE,
                (string) (\stripos($cmdResult, 'Version:') !== false)
            )
        );

        $l11n = Localization::fromLanguage('en');
        LocalizationMapper::create()->execute($l11n);
    }

    /**
     * Install countries
     *
     * @param SQLiteConnection $sqlite SQLLite database connection of the source data
     * @param DatabasePool     $dbPool Database pool to save data to
     *
     * @return void
     *
     * @since 1.0.0
     */
    private static function installCountries(SQLiteConnection $sqlite, DatabasePool $dbPool) : void
    {
        $con = $dbPool->get();

        $query = new Builder($con);
        $query->insert('country_name', 'country_code2', 'country_code3', 'country_numeric', 'country_region', 'country_developed')
            ->into('country');

        $querySqlite = new Builder($sqlite);
        $countries   = $querySqlite->select('*')->from('country')->execute();

        if ($countries === null) {
            return;
        }

        foreach ($countries as $country) {
            $query->values(
                $country['country_name'] === null ? null : \trim((string) $country['country_name']),
                $country['country_code2'] === null ? null : \trim((string) $country['country_code2']),
                $country['country_code3'] === null ? null : \trim((string) $country['country_code3']),
                $country['country_numeric'],
                $country['country_region'],
                (int) $country['country_developed']
            );
        }

        $query->execute();
    }

    /**
     * Install languages
     *
     * @param SQLiteConnection $sqlite SQLLite database connection of the source data
     * @param DatabasePool     $dbPool Database pool to save data to
     *
     * @return void
     *
     * @since 1.0.0
     */
    private static function installLanguages(SQLiteConnection $sqlite, DatabasePool $dbPool) : void
    {
        $con = $dbPool->get();

        $query = new Builder($con);
        $query->insert('language_name', 'language_native', 'language_639_1', 'language_639_2T', 'language_639_2B', 'language_639_3')
            ->into('language');

        $querySqlite = new Builder($sqlite);
        $languages   = $querySqlite->select('*')->from('language')->execute();

        if ($languages === null) {
            return;
        }

        foreach ($languages as $language) {
            $query->values(
                $language['language_name'] === null ? null : \trim((string) $language['language_name']),
                $language['language_native'] === null ? null : \trim((string) $language['language_native']),
                $language['language_639_1'] === null ? null : \trim((string) $language['language_639_1']),
                $language['language_639_2T'] === null ? null : \trim((string) $language['language_639_2T']),
                $language['language_639_2B'] === null ? null : \trim((string) $language['language_639_2B']),
                $language['language_639_3'] === null ? null : \trim((string) $language['language_639_3'])
            );
        }

        $query->execute();
    }

    /**
     * Install currencies
     *
     * @param SQLiteConnection $sqlite SQLLite database connection of the source data
     * @param DatabasePool     $dbPool Database pool to save data to
     *
     * @return void
     *
     * @since 1.0.0
     */
    private static function installCurrencies(SQLiteConnection $sqlite, DatabasePool $dbPool) : void
    {
        $con = $dbPool->get();

        $query = new Builder($con);
        $query->insert('currency_id', 'currency_name', 'currency_code', 'currency_number', 'currency_symbol', 'currency_subunits', 'currency_decimal', 'currency_countries')
            ->into('currency');

        $querySqlite = new Builder($sqlite);
        $currencies  = $querySqlite->select('*')->from('currency')->execute();

        if ($currencies === null) {
            return;
        }

        foreach ($currencies as $currency) {
            $query->values(
                $currency['currency_id'],
                $currency['currency_name'] === null ? null : \trim((string) $currency['currency_name']),
                $currency['currency_code'] === null ? null : \trim((string) $currency['currency_code']),
                $currency['currency_number'] === null ? null : \trim((string) $currency['currency_number']),
                $currency['currency_symbol'] === null ? null : \trim((string) $currency['currency_symbol']),
                $currency['currency_subunits'],
                $currency['currency_decimal'] === null ? null : \trim((string) $currency['currency_decimal']),
                $currency['currency_countries'] === null ? null : \trim((string) $currency['currency_countries'])
            );
        }

        $query->execute();
    }

    /**
     * Install data from providing modules.
     *
     * @param ApplicationAbstract $app  Application
     * @param array               $data Additional data
     *
     * @return array
     *
     * @throws PathException
     * @throws \Exception
     *
     * @since 1.0.0
     */
    public static function installExternal(ApplicationAbstract $app, array $data) : array
    {
        if (!\is_file($data['path'] ?? '')) {
            throw new PathException($data['path'] ?? '');
        }

        $adminFile = \file_get_contents($data['path'] ?? '');
        if ($adminFile === false) {
            throw new PathException($data['path'] ?? ''); // @codeCoverageIgnore
        }

        $adminData = \json_decode($adminFile, true) ?? [];
        if (!\is_array($adminData)) {
            throw new \Exception(); // @codeCoverageIgnore
        }

        $result = [
            'settings' => [],
        ];

        foreach ($adminData as $admin) {
            switch ($admin['type']) {
                case 'setting':
                    $result['settings'][] = self::createSettings($app, $admin);
                    break;
                default:
            }
        }

        return $result;
    }

    /**
     * Create settings.
     *
     * @param ApplicationAbstract $app  Database instance
     * @param array               $data Setting data
     *
     * @return array
     *
     * @since 1.0.0
     */
    private static function createSettings(ApplicationAbstract $app, array $data) : array
    {
        /** @var \Modules\Admin\Controller\ApiController $module */
        $module = $app->moduleManager->get('Admin');

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('id', $data['id'] ?? 0);
        $request->setData('name', $data['name'] ?? '');
        $request->setData('content', $data['content'] ?? '');
        $request->setData('pattern', $data['pattern'] ?? '');
        $request->setData('unit', $data['unit'] ?? null);
        $request->setData('app', $data['app'] ?? null);
        $request->setData('module', $data['module'] ?? null);
        $request->setData('group', $data['group'] ?? null);
        $request->setData('account', $data['account'] ?? null);

        $module->apiSettingsCreate($request, $response);

        $responseData = $response->get('');
        if (!\is_array($responseData)) {
            return [];
        }

        return !\is_array($responseData['response'])
            ? $responseData['response']->toArray()
            : $responseData['response'];
    }
}
