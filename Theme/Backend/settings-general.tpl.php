<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Organization\Models\UnitMapper;
use phpOMS\Localization\NullLocalization;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */
$generalSettings = $this->data['generalSettings'] ?? [];
$settings        = $this->data['settings'] ?? [];

$countryCodes    = \phpOMS\Localization\ISO3166TwoEnum::getConstants();
$countries       = \phpOMS\Localization\ISO3166NameEnum::getConstants();
$timezones       = \phpOMS\Localization\TimeZoneEnumArray::getConstants();
$timeformats     = \phpOMS\Localization\ISO8601EnumArray::getConstants();
$languages       = \phpOMS\Localization\ISO639Enum::getConstants();
$currencies      = \phpOMS\Localization\ISO4217Enum::getConstants();
$l11nDefinitions = \phpOMS\System\File\Local\Directory::list(__DIR__ . '/../../../../phpOMS/Localization/Defaults/Definitions');

$weights      = \phpOMS\Utils\Converter\WeightType::getConstants();
$speeds       = \phpOMS\Utils\Converter\SpeedType::getConstants();
$areas        = \phpOMS\Utils\Converter\AreaType::getConstants();
$lengths      = \phpOMS\Utils\Converter\LengthType::getConstants();
$volumes      = \phpOMS\Utils\Converter\VolumeType::getConstants();
$temperatures = \phpOMS\Utils\Converter\TemperatureType::getConstants();

$l11n = $this->getData('default_localization') ?? new NullLocalization();
?>

<div class="tabview tab-2 url-rewrite">
    <div class="box">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('General'); ?></label>
            <li><label for="c-tab-2"><?= $this->getHtml('Localization'); ?></label>
            <li><label for="c-tab-3"><?= $this->getHtml('Settings'); ?></label>
            <li><label for="c-tab-4"><?= $this->getHtml('Design'); ?></label>
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="iGeneralSettings" action="<?= UriFactory::build('{/api}admin/settings/general'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Settings'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iOname"><?= $this->getHtml('OrganizationName'); ?></label>
                                    <select id="iOname" name="settings_1000000009">
                                        <?php $unit = UnitMapper::get()->where('id', (int) $generalSettings[1000000009])->execute(); ?>
                                            <option value="<?= $unit->id; ?>"><?= $this->printHtml($unit->name); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="portlet-foot"><input id="iSubmitGeneral" name="submitGeneral" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>"></div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="iSecuritySettings" action="<?= UriFactory::build('{/api}admin/settings/general'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Security'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iPassword">
                                        <?= $this->getHtml('PasswordRegex'); ?>
                                        <i class="tooltip" data-tooltip="<?= $this->getHtml('i:PasswordRegex'); ?>"><i class="g-icon">info</i></i>
                                    </label>

                                    <input id="iPassword" name="settings_1000000001" type="text" value="<?= $this->printHtml($generalSettings['1000000001']->content); ?>" placeholder="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[A-Za-z\d$@$!%*?&;:\(\)\[\]=\{\}\+\-]{8,}">
                                </div>

                                <div class="form-group">
                                    <label for="iLoginRetries">
                                        <?= $this->getHtml('LoginRetries'); ?>
                                        <i class="tooltip" data-tooltip="<?= $this->getHtml('i:LoginRetries'); ?>"><i class="g-icon">info</i></i>
                                    </label>

                                    <input id="iLoginRetries" name="settings_1000000005" type="number" value="<?= $this->printHtml($generalSettings['1000000005']->content); ?>" min="-1">
                                </div>

                                <div class="form-group">
                                    <label for="iTimeoutPeriod">
                                        <?= $this->getHtml('TimeoutPeriod'); ?>
                                        <i class="tooltip" data-tooltip="<?= $this->getHtml('i:TimeoutPeriod'); ?>"><i class="g-icon">info</i></i>
                                    </label>

                                    <input id="iTimeoutPeriod" name="settings_1000000002" type="number" value="<?= $this->printHtml($generalSettings['1000000002']->content); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="iPasswordChangeInterval">
                                        <?= $this->getHtml('PasswordChangeInterval'); ?>
                                        <i class="tooltip" data-tooltip="<?= $this->getHtml('i:PasswordChangeInterval'); ?>"><i class="g-icon">info</i></i>
                                    </label>

                                    <input id="iPasswordChangeInterval" name="settings_1000000003" type="number" value="<?= $this->printHtml($generalSettings['1000000003']->content); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="iPasswordHistory">
                                        <?= $this->getHtml('PasswordHistory'); ?>
                                        <i class="tooltip" data-tooltip="<?= $this->getHtml('i:PasswordHistory'); ?>"><i class="g-icon">info</i></i>
                                    </label>

                                    <input id="iPasswordHistory" name="settings_1000000004" type="number" value="<?= $this->printHtml($generalSettings['1000000004']->content); ?>">
                                </div>

                            </div>
                            <div class="portlet-foot">
                                <input id="iSubmitGeneral" name="submitGeneral" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="iLoggingSettings"
                            action="<?= UriFactory::build('{/api}admin/settings/general'); ?>"
                            method="post">
                            <div class="portlet-head"><?= $this->getHtml('Logging'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label class="checkbox" for="iLog">
                                        <input id="iLog" type="checkbox" name="settings_1000000006" value="1">
                                        <span class="checkmark"></span>
                                        <?= $this->getHtml('Log'); ?>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label for="iLogPath"><?= $this->getHtml('LogPath'); ?></label>
                                    <input id="iLogPath" name="settings_1000000007" type="text" value="<?= $this->printHtml($generalSettings['1000000007']->content); ?>" placeholder="/Logs">
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input id="iSubmitGeneral" name="submitGeneral" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <form id="fLocalization"
                            name="fLocalization"
                            action="<?= UriFactory::build('{/api}profile/settings/localization'); ?>"
                            method="post">
                            <div class="portlet-head"><?= $this->getHtml('Localization'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iDefaultLocalizations"><?= $this->getHtml('Defaults'); ?></label>
                                    <div class="ipt-wrap wf-100">
                                        <div class="ipt-first"><select id="iDefaultLocalizations" name="localization_load">
                                                <option selected disabled><?= $this->getHtml('Customized'); ?>
                                                <?php foreach ($l11nDefinitions as $def) : ?>
                                                    <option value="<?= $this->printHtml(\explode('.', $def)[0]); ?>"><?= $this->printHtml(\explode('.', $def)[0]); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="ipt-second"><input type="submit" name="loadDefaultLocalization" formaction="<?= UriFactory::build('{/api}profile/settings/localization?load=1'); ?>" value="<?= $this->getHtml('Load'); ?>"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="iCountries"><?= $this->getHtml('Country'); ?></label>
                                    <select id="iCountries" name="settings_country">
                                        <?php foreach ($countryCodes as $code3 => $code2) : ?>
                                        <option value="<?= $this->printHtml($code2); ?>"<?= $this->printHtml($code2 === $l11n->getCountry() ? ' selected' : ''); ?>><?= $this->printHtml($countries[$code3]); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iLanguages"><?= $this->getHtml('Language'); ?></label>
                                    <select id="iLanguages" name="settings_language">
                                        <?php foreach ($languages as $code => $language) : $code = \strtolower(\substr($code, 1)); ?>
                                        <option value="<?= $this->printHtml($code); ?>"<?= $this->printHtml($code === $l11n->language ? ' selected' : ''); ?>><?= $this->printHtml($language); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iTemperature"><?= $this->getHtml('Temperature'); ?></label>
                                    <select id="iTemperature" name="settings_temperature">
                                        <?php foreach ($temperatures as $temperature) : ?>
                                        <option value="<?= $this->printHtml($temperature); ?>"<?= $this->printHtml($temperature === $l11n->getTemperature() ? ' selected' : ''); ?>><?= $this->printHtml($temperature); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input id="iSubmitLocalization" name="submitLocalization" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div id="settings-localization-time" class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Time'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iTimezones"><?= $this->getHtml('Timezone'); ?></label>
                                <select form="fLocalization" id="iTimezones" name="settings_timezone">
                                    <?php foreach ($timezones as $timezone) : ?>
                                    <option value="<?= $this->printHtml($timezone); ?>"<?= $this->printHtml($timezone === $l11n->getTimezone() ? ' selected' : ''); ?>><?= $this->printHtml($timezone); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iTimeformatVeryShort"><?= $this->getHtml('VeryShort'); ?></label>
                                <input form="fLocalization" id="iTimeformatVeryShort" name="settings_timeformat_vs" type="text" value="<?= $this->printHtml($l11n->getDatetime()['very_short']); ?>" placeholder="Y" required>
                            </div>

                            <h2><?= $this->getHtml('Timeformat'); ?></h2>

                            <div class="form-group">
                                <label for="iTimeformatShort"><?= $this->getHtml('Short'); ?></label>
                                <input form="fLocalization" id="iTimeformatShort" name="settings_timeformat_s" type="text" value="<?= $this->printHtml($l11n->getDatetime()['short']); ?>" placeholder="Y" required>
                            </div>

                            <div class="form-group">
                                <label for="iTimeformatMedium"><?= $this->getHtml('Medium'); ?></label>
                                <input form="fLocalization" id="iTimeformatMedium" name="settings_timeformat_m" type="text" value="<?= $this->printHtml($l11n->getDatetime()['medium']); ?>" placeholder="Y" required>
                            </div>

                            <div class="form-group">
                                <label for="iTimeformatLong"><?= $this->getHtml('Long'); ?></label>
                                <input form="fLocalization" id="iTimeformatLong" name="settings_timeformat_l" type="text" value="<?= $this->printHtml($l11n->getDatetime()['long']); ?>" placeholder="Y" required>
                            </div>

                            <div class="form-group">
                                <label for="iTimeformatVeryLong"><?= $this->getHtml('VeryLong'); ?></label>
                                <input form="fLocalization" id="iTimeformatVeryLong" name="settings_timeformat_vl" type="text" value="<?= $this->printHtml($l11n->getDatetime()['very_long']); ?>" placeholder="Y" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div id="settings-localization-numeric" class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Numeric'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iCurrencies"><?= $this->getHtml('Currency'); ?></label>
                                <select form="fLocalization" id="iCurrencies" name="settings_currency">
                                    <?php foreach ($currencies as $code => $currency) : $code = \substr($code, 1); ?>
                                    <option value="<?= $this->printHtml($code); ?>"<?= $this->printHtml($code === $l11n->getCurrency() ? ' selected' : ''); ?>><?= $this->printHtml($currency); ?>
                                        <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $this->getHtml('Currencyformat'); ?></label>
                                <select form="fLocalization" name="settings_currencyformat">
                                    <option value="0"<?= $this->printHtml($l11n->getCurrencyFormat() === '0' ? ' selected' : ''); ?>><?= $this->getHtml('Amount') , ' ' , $this->printHtml($l11n->getCurrency()); ?>
                                    <option value="1"<?= $this->printHtml($l11n->getCurrencyFormat() === '1' ? ' selected' : ''); ?>><?= $this->printHtml($l11n->getCurrency()) , ' ' , $this->getHtml('Amount'); ?>
                                </select>
                            </div>

                            <h2><?= $this->getHtml('Numberformat'); ?></h2>

                            <div class="form-group">
                                <div class="input-control">
                                    <label for="iDecimalPoint"><?= $this->getHtml('DecimalPoint'); ?></label>
                                    <input form="fLocalization" id="iDecimalPoint" name="settings_decimal" type="text" value="<?= $this->printHtml($l11n->getDecimal()); ?>" placeholder="." required>
                                </div>

                                <div class="input-control">
                                    <label for="iThousandSep"><?= $this->getHtml('ThousandsSeparator'); ?></label>
                                    <input form="fLocalization" id="iThousandSep" name="settings_thousands" type="text" value="<?= $this->printHtml($l11n->getThousands()); ?>" placeholder="," required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div id="settings-localization-precision" class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Precision'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iPrecisionVeryShort"><?= $this->getHtml('VeryShort'); ?></label>
                                <input form="fLocalization" id="iPrecisionVeryShort" name="settings_precision_vs" value="<?= $l11n->getPrecision()['very_short']; ?>" type="number">
                            </div>

                            <div class="form-group">
                                <label for="iPrecisionShort"><?= $this->getHtml('Short'); ?></label>
                                <input form="fLocalization" id="iPrecisionLight" name="settings_precision_s" value="<?= $l11n->getPrecision()['short']; ?>" type="number">
                            </div>

                            <div class="form-group">
                                <label for="iPrecisionMedium"><?= $this->getHtml('Medium'); ?></label>
                                <input form="fLocalization" id="iPrecisionMedium" name="settings_precision_m" value="<?= $l11n->getPrecision()['medium']; ?>" type="number">
                            </div>

                            <div class="form-group">
                                <label for="iPrecisionLong"><?= $this->getHtml('Long'); ?></label>
                                <input form="fLocalization" id="iPrecisionLong" name="settings_precision_l" value="<?= $l11n->getPrecision()['long']; ?>" type="number">
                            </div>

                            <div class="form-group">
                                <label for="iPrecisionVeryLong"><?= $this->getHtml('VeryLong'); ?></label>
                                <input form="fLocalization" id="iPrecisionVeryLong" name="settings_precision_vl" value="<?= $l11n->getPrecision()['very_long']; ?>" type="number">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div id="settings-localization-weight" class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Weight'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iWeightVeryLight"><?= $this->getHtml('VeryLight'); ?></label>
                                <select form="fLocalization" id="iWeightVeryLight" name="settings_weight_vl">
                                    <?php foreach ($weights as $code => $weight) : ?>
                                    <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['very_light'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iWeightLight"><?= $this->getHtml('Light'); ?></label>
                                <select form="fLocalization" id="iWeightLight" name="settings_weight_l">
                                    <?php foreach ($weights as $code => $weight) : ?>
                                    <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['light'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iWeightMedium"><?= $this->getHtml('Medium'); ?></label>
                                <select form="fLocalization" id="iWeightMedium" name="settings_weight_m">
                                    <?php foreach ($weights as $code => $weight) : ?>
                                    <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iWeightHeavy"><?= $this->getHtml('Heavy'); ?></label>
                                <select form="fLocalization" id="iWeightHeavy" name="settings_weight_h">
                                    <?php foreach ($weights as $code => $weight) : ?>
                                    <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['heavy'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iWeightVeryHeavy"><?= $this->getHtml('VeryHeavy'); ?></label>
                                <select form="fLocalization" id="iWeightVeryHeavy" name="settings_weight_vh">
                                    <?php foreach ($weights as $code => $weight) : ?>
                                    <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['very_heavy'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div id="settings-localization-speed" class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Speed'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iSpeedVerySlow"><?= $this->getHtml('VerySlow'); ?></label>
                                <select form="fLocalization" id="iSpeedVerySlow" name="settings_speed_vs">
                                    <?php foreach ($speeds as $code => $speed) : ?>
                                    <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['very_slow'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iSpeedSlow"><?= $this->getHtml('Slow'); ?></label>
                                <select form="fLocalization" id="iSpeedSlow" name="settings_speed_s">
                                    <?php foreach ($speeds as $code => $speed) : ?>
                                    <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['slow'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iSpeedMedium"><?= $this->getHtml('Medium'); ?></label>
                                <select form="fLocalization" id="iSpeedMedium" name="settings_speed_m">
                                    <?php foreach ($speeds as $code => $speed) : ?>
                                    <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iSpeedFast"><?= $this->getHtml('Fast'); ?></label>
                                <select form="fLocalization" id="iSpeedFast" name="settings_speed_f">
                                    <?php foreach ($speeds as $code => $speed) : ?>
                                    <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['fast'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iSpeedVeryFast"><?= $this->getHtml('VeryFast'); ?></label>
                                <select form="fLocalization" id="iSpeedVeryFast" name="settings_speed_vf">
                                    <?php foreach ($speeds as $code => $speed) : ?>
                                    <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['very_fast'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iSpeedSea"><?= $this->getHtml('Sea'); ?></label>
                                <select form="fLocalization" id="iSpeedSea" name="settings_speed_sea">
                                    <?php foreach ($speeds as $code => $speed) : ?>
                                    <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['sea'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div id="settings-localization-length" class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Length'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iLengthVeryShort"><?= $this->getHtml('VeryShort'); ?></label>
                                <select form="fLocalization" id="iLengthVeryShort" name="settings_length_vs">
                                    <?php foreach ($lengths as $code => $length) : ?>
                                    <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['very_short'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iLengthShort"><?= $this->getHtml('Short'); ?></label>
                                <select form="fLocalization" id="iLengthShort" name="settings_length_s">
                                    <?php foreach ($lengths as $code => $length) : ?>
                                    <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['short'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iLengthMedium"><?= $this->getHtml('Medium'); ?></label>
                                <select form="fLocalization" id="iLengthMedium" name="settings_length_m">
                                    <?php foreach ($lengths as $code => $length) : ?>
                                    <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iLengthLong"><?= $this->getHtml('Long'); ?></label>
                                <select form="fLocalization" id="iLengthLong" name="settings_length_l">
                                    <?php foreach ($lengths as $code => $length) : ?>
                                    <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['long'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iLengthVeryLong"><?= $this->getHtml('VeryLong'); ?></label>
                                <select form="fLocalization" id="iLengthVeryLong" name="settings_length_vl">
                                    <?php foreach ($lengths as $code => $length) : ?>
                                    <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['very_long'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iLengthSea"><?= $this->getHtml('Sea'); ?></label>
                                <select form="fLocalization" id="iLengthSea" name="settings_length_sea">
                                    <?php foreach ($lengths as $code => $length) : ?>
                                    <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['sea'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div id="settings-localization-area" class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Area'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iAreaVerySmall"><?= $this->getHtml('VerySmall'); ?></label>
                                <select form="fLocalization" id="iAreaVerySmall" name="settings_area_vs">
                                    <?php foreach ($areas as $code => $area) : ?>
                                    <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['very_small'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iAreaSmall"><?= $this->getHtml('Small'); ?></label>
                                <select form="fLocalization" id="iAreaSmall" name="settings_area_s">
                                    <?php foreach ($areas as $code => $area) : ?>
                                    <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['small'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iAreaMedium"><?= $this->getHtml('Medium'); ?></label>
                                <select form="fLocalization" id="iAreaMedium" name="settings_area_m">
                                    <?php foreach ($areas as $code => $area) : ?>
                                    <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iAreaLarge"><?= $this->getHtml('Large'); ?></label>
                                <select form="fLocalization" id="iAreaLarge" name="settings_area_l">
                                    <?php foreach ($areas as $code => $area) : ?>
                                    <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['large'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iAreaVeryLarge"><?= $this->getHtml('VeryLarge'); ?></label>
                                <select form="fLocalization" id="iAreaVeryLarge" name="settings_area_vl">
                                    <?php foreach ($areas as $code => $area) : ?>
                                    <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['very_large'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div id="settings-localization-volume" class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Volume'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iVolumeVerySmall"><?= $this->getHtml('VerySmall'); ?></label>
                                <select form="fLocalization" id="iVolumeVerySmall" name="settings_volume_vs">
                                    <?php foreach ($volumes as $code => $volume) : ?>
                                    <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['very_small'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iVolumeSmall"><?= $this->getHtml('Small'); ?></label>
                                <select form="fLocalization" id="iVolumeSmall" name="settings_volume_s">
                                    <?php foreach ($volumes as $code => $volume) : ?>
                                    <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['small'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iVolumeMedium"><?= $this->getHtml('Medium'); ?></label>
                                <select form="fLocalization" id="iVolumeMedium" name="settings_volume_m">
                                    <?php foreach ($volumes as $code => $volume) : ?>
                                    <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iVolumeLarge"><?= $this->getHtml('Large'); ?></label>
                                <select form="fLocalization" id="iVolumeLarge" name="settings_volume_l">
                                    <?php foreach ($volumes as $code => $volume) : ?>
                                    <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['large'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iVolumeVeryLarge"><?= $this->getHtml('VeryLarge'); ?></label>
                                <select form="fLocalization" id="iVolumeVeryLarge" name="settings_volume_vl">
                                    <?php foreach ($volumes as $code => $volume) : ?>
                                    <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['very_large'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iVolumeTeaspoon"><?= $this->getHtml('Teaspoon'); ?></label>
                                <select form="fLocalization" id="iVolumeTeaspoon" name="settings_volume_teaspoon">
                                    <?php foreach ($volumes as $code => $volume) : ?>
                                    <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['teaspoon'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iVolumeTablespoon"><?= $this->getHtml('Tablespoon'); ?></label>
                                <select form="fLocalization" id="iVolumeTablespoon" name="settings_volume_tablespoon">
                                    <?php foreach ($volumes as $code => $volume) : ?>
                                    <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['tablespoon'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iVolumeGlass"><?= $this->getHtml('Glass'); ?></label>
                                <select form="fLocalization" id="iVolumeGlass" name="settings_volume_glass">
                                    <?php foreach ($volumes as $code => $volume) : ?>
                                    <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['glass'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-3"
            name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-3' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Settings'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <div class="slider">
                        <table id="settingsList" class="default sticky">
                            <thead>
                            <tr>
                                <td>
                                <td><?= $this->getHtml('ID', '0', '0'); ?>
                                    <label for="settingsList-sort-1">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-1">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="settingsList-sort-2">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-2">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('Name'); ?>
                                    <label for="settingsList-sort-3">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-3">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="settingsList-sort-4">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-4">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td class="wf-100"><?= $this->getHtml('Value'); ?>
                                <td><?= $this->getHtml('Module'); ?>
                                    <label for="settingsList-sort-5">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-5">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="settingsList-sort-6">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-6">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('Group'); ?>
                                    <label for="settingsList-sort-7">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-7">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="settingsList-sort-8">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-8">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                                <td><?= $this->getHtml('Account'); ?>
                                    <label for="settingsList-sort-9">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-9">
                                        <i class="sort-asc g-icon">expand_less</i>
                                    </label>
                                    <label for="settingsList-sort-10">
                                        <input type="radio" name="settingsList-sort" id="settingsList-sort-10">
                                        <i class="sort-desc g-icon">expand_more</i>
                                    </label>
                                    <label>
                                        <i class="filter g-icon">filter_alt</i>
                                    </label>
                            <tbody>
                            <?php $count          = 0;
                                $previousSettings = empty($settings) ? 'admin/settings/general' : 'admin/settings/general?{?}&sid=' . \reset($settings)->id . '&ptype=p';
                                $nextSettings     = empty($settings) ? 'admin/settings/general' : 'admin/settings/general?{?}&sid=' . \end($settings)->id . '&ptype=n';

                                foreach ($settings as $key => $setting) : ++$count;
                            ?>
                            <tr tabindex="0">
                                <td><i class="g-icon">settings</i>
                                <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><?= $setting->id; ?>
                                <td data-label="<?= $this->getHtml('Name'); ?>"><?= $this->printHtml($setting->name); ?>
                                <td data-label="<?= $this->getHtml('Value'); ?>"><?= $this->printHtml($setting->content); ?>
                                <td data-label="<?= $this->getHtml('Module'); ?>"><?= $this->printHtml($setting->module); ?>
                                <td data-label="<?= $this->getHtml('Group'); ?>"><?= $this->printHtml($setting->group); ?>
                                <td data-label="<?= $this->getHtml('Account'); ?>"><?= $this->printHtml($setting->account); ?>
                            <?php endforeach; ?>
                            <?php if ($count === 0) : ?>
                                <tr><td colspan="7" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                            <?php endif; ?>
                        </table>
                        </div>
                        <div class="portlet-foot">
                            <a tabindex="0" class="button" href="<?= UriFactory::build($previousSettings); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                            <a tabindex="0" class="button" href="<?= UriFactory::build($nextSettings); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-4"
            name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-4' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Images'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iLoginImage"><?= $this->getHtml('LoginImage'); ?></label>
                                <div>
                                    <img id="preview-loginImage"
                                        alt="<?= $this->getHtml('LoginImage'); ?>"
                                        itemprop="logo" loading="lazy"
                                        src="<?= UriFactory::build('Web/Backend/img/logo.png'); ?>"
                                        width="50px">
                                    <div>
                                        <a id="iLoginImageUploadButton" href="#upload" data-action='[
                                        {"listener": "click", "key": 1, "action": [
                                            {"key": 1, "type": "event.prevent"},
                                            {"key": 2, "type": "dom.click", "selector": "#iLoginImageUpload"}
                                        ]}]'><?= $this->getHtml('Change'); ?></a>
                                        <form id="iLoginImageUploadForm" action="<?= UriFactory::build('{/api}admin/settings/design'); ?>" method="post">
                                            <input class="preview" data-action='[
                                                {"listener": "change", "key": 1, "action": [
                                                    {"key": 1, "type": "form.submit", "selector": "#iLoginImageUploadForm"}
                                                ]}]' id="iLoginImageUpload" name="loginImage" type="file" accept="image/png" style="display: none;">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
