<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use Modules\Organization\Models\UnitMapper;
use phpOMS\Localization\NullLocalization;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */
$settings = $this->getData('settings') ?? [];

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

$l11n = $this->getData('defaultlocalization') ?? new NullLocalization();
?>

<div class="tabview tab-2">
    <div class="box wf-100 col-xs-12">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('General'); ?></label></li>
            <li><label for="c-tab-2"><?= $this->getHtml('Localization'); ?></label></li>
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->getUri()->getFragment() === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="iGeneralSettings" action="<?= UriFactory::build('{/api}admin/settings/general'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Settings'); ?></div>
                            <div class="portlet-body">
                                <table class="layout wf-100">
                                    <tbody>
                                        <tr><td><label for="iOname"><?= $this->getHtml('OrganizationName'); ?></label>
                                        <tr><td>
                                            <select id="iOname" name="settings_1000000009">
                                                <?php $unit = UnitMapper::get((int) $settings[1000000009]); ?>
                                                    <option value="<?= $this->printHtml($unit->getId()); ?>"><?= $this->printHtml($unit->getName()); ?>
                                            </select>
                                </table>
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
                                <table class="layout wf-100">
                                    <tbody>
                                        <tr><td>
                                            <label for="iPassword"><?= $this->getHtml('PasswordRegex'); ?></label>
                                            <i class="tooltip" data-tooltip="<?= $this->getHtml('i:PasswordRegex'); ?>"><i class="fa fa-info-circle"></i></i>
                                        <tr><td><input id="iPassword" name="settings_1000000001" type="text" value="<?= $this->printHtml($settings['1000000001']['content']); ?>" placeholder="&#xf023; ^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[A-Za-z\d$@$!%*?&;:\(\)\[\]=\{\}\+\-]{8,}">
                                        <tr><td>
                                            <label for="iLoginRetries"><?= $this->getHtml('LoginRetries'); ?></label>
                                            <i class="tooltip" data-tooltip="<?= $this->getHtml('i:LoginRetries'); ?>"><i class="fa fa-info-circle"></i></i>
                                        <tr><td><input id="iLoginRetries" name="settings_1000000005" type="number" value="<?= $this->printHtml($settings['1000000005']['content']); ?>" min="-1">
                                        <tr><td>
                                            <label for="iTimeoutPeriod"><?= $this->getHtml('TimeoutPeriod'); ?></label>
                                            <i class="tooltip" data-tooltip="<?= $this->getHtml('i:TimeoutPeriod'); ?>"><i class="fa fa-info-circle"></i></i>
                                        <tr><td><input id="iTimeoutPeriod" name="settings_1000000002" type="number" value="<?= $this->printHtml($settings['1000000002']['content']); ?>">
                                        <tr><td>
                                            <label for="iPasswordChangeInterval"><?= $this->getHtml('PasswordChangeInterval'); ?></label>
                                            <i class="tooltip" data-tooltip="<?= $this->getHtml('i:PasswordChangeInterval'); ?>"><i class="fa fa-info-circle"></i></i>
                                        <tr><td><input id="iPasswordChangeInterval" name="settings_1000000003" type="number" value="<?= $this->printHtml($settings['1000000003']['content']); ?>">
                                        <tr><td>
                                            <label for="iPasswordHistory"><?= $this->getHtml('PasswordHistory'); ?></label>
                                            <i class="tooltip" data-tooltip="<?= $this->getHtml('i:PasswordHistory'); ?>"><i class="fa fa-info-circle"></i></i>
                                        <tr><td><input id="iPasswordHistory" name="settings_1000000004" type="number" value="<?= $this->printHtml($settings['1000000004']['content']); ?>">
                                </table>
                            </div>
                            <div class="portlet-foot"><input id="iSubmitGeneral" name="submitGeneral" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>"></div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="iLoggingSettings" action="<?= UriFactory::build('{/api}admin/settings/general'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Logging'); ?></div>
                            <div class="portlet-body">
                                <table class="layout wf-100">
                                    <tbody>
                                        <tr><td>
                                            <label class="checkbox" for="iLog">
                                                <input id="iLog" type="checkbox" name="settings_1000000006" value="1">
                                                <span class="checkmark"></span>
                                                <?= $this->getHtml('Log'); ?>
                                            </label>
                                        <tr><td><label for="iLogPath"><?= $this->getHtml('LogPath'); ?></label>
                                        <tr><td><input id="iLogPath" name="settings_1000000007" type="text" value="<?= $this->printHtml($settings['1000000007']['content']); ?>" placeholder="&#xf023; /Logs">
                                </table>
                            </div>
                            <div class="portlet-foot"><input id="iSubmitGeneral" name="submitGeneral" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>"></div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->getUri()->getFragment() === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <form id="fLocalization" name="fLocalization" action="<?= UriFactory::build('{/api}profile/settings/localization'); ?>" method="post">
                        <div class="portlet-head"><?= $this->getHtml('Localization'); ?></div>
                        <div class="portlet-body">
                                <table class="layout wf-100">
                                    <tbody>
                                    <tr><td><label for="iDefaultLocalizations"><?= $this->getHtml('Defaults'); ?></label>
                                    <tr><td>
                                        <div class="ipt-wrap">
                                            <div class="ipt-first"><select id="iDefaultLocalizations" name="localization_load">
                                                    <option selected disabled><?= $this->getHtml('Customized'); ?>
                                                    <?php foreach ($l11nDefinitions as $def) : ?>
                                                        <option value="<?= $this->printHtml(\explode('.', $def)[0]); ?>"><?= $this->printHtml(\explode('.', $def)[0]); ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="ipt-second"><input type="submit" name="loadDefaultLocalization" formaction="<?= UriFactory::build('{/api}profile/settings/localization?load=1'); ?>" value="<?= $this->getHtml('Load'); ?>"></div>
                                        </div>
                                    <tr><td colspan="2"><label for="iCountries"><?= $this->getHtml('Country'); ?></label>
                                    <tr><td colspan="2">
                                            <select id="iCountries" name="settings_country">
                                                <?php foreach ($countryCodes as $code3 => $code2) : ?>
                                                <option value="<?= $this->printHtml($code2); ?>"<?= $this->printHtml($code2 === $l11n->getCountry() ? ' selected' : ''); ?>><?= $this->printHtml($countries[$code3]); ?>
                                                <?php endforeach; ?>
                                            </select>
                                    <tr><td colspan="2"><label for="iLanguages"><?= $this->getHtml('Language'); ?></label>
                                    <tr><td colspan="2">
                                            <select id="iLanguages" name="settings_language">
                                                <?php foreach ($languages as $code => $language) : $code = \strtolower(\substr($code, 1)); ?>
                                                <option value="<?= $this->printHtml($code); ?>"<?= $this->printHtml($code === $l11n->getLanguage() ? ' selected' : ''); ?>><?= $this->printHtml($language); ?>
                                                <?php endforeach; ?>
                                            </select>
                                    <tr><td colspan="2"><label for="iTemperature"><?= $this->getHtml('Temperature'); ?></label>
                                    <tr><td colspan="2">
                                            <select id="iTemperature" name="settings_temperature">
                                                <?php foreach ($temperatures as $temperature) : ?>
                                                <option value="<?= $this->printHtml($temperature); ?>"<?= $this->printHtml($temperature === $l11n->getTemperature() ? ' selected' : ''); ?>><?= $this->printHtml($temperature); ?>
                                                <?php endforeach; ?>
                                            </select>
                                </table>
                            </div>
                            <div class="portlet-foot">
                                <input id="iSubmitLocalization" name="submitLocalization" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Time'); ?></div>
                        <div class="portlet-body">
                            <form>
                            <table class="layout wf-100">
                                <tbody>
                                <tr><td><label for="iTimezones"><?= $this->getHtml('Timezone'); ?></label>
                                <tr><td>
                                        <select form="fLocalization" id="iTimezones" name="settings_timezone">
                                            <?php foreach ($timezones as $timezone) : ?>
                                            <option value="<?= $this->printHtml($timezone); ?>"<?= $this->printHtml($timezone === $l11n->getTimezone() ? ' selected' : ''); ?>><?= $this->printHtml($timezone); ?>
                                            <?php endforeach; ?>
                                        </select>
                                <tr><td><h2><?= $this->getHtml('Timeformat'); ?></h2>
                                <tr><td><label for="iTimeformatVeryShort"><?= $this->getHtml('VeryShort'); ?></label>
                                <tr><td><input form="fLocalization" id="iTimeformatVeryShort" name="settings_timeformat_vs" type="text" value="<?= $this->printHtml($l11n->getDatetime()['very_short']); ?>" placeholder="Y" required>
                                <tr><td><label for="iTimeformatShort"><?= $this->getHtml('Short'); ?></label>
                                <tr><td><input form="fLocalization" id="iTimeformatShort" name="settings_timeformat_s" type="text" value="<?= $this->printHtml($l11n->getDatetime()['short']); ?>" placeholder="Y" required>
                                <tr><td><label for="iTimeformatMedium"><?= $this->getHtml('Medium'); ?></label>
                                <tr><td><input form="fLocalization" id="iTimeformatMedium" name="settings_timeformat_m" type="text" value="<?= $this->printHtml($l11n->getDatetime()['medium']); ?>" placeholder="Y" required>
                                <tr><td><label for="iTimeformatLong"><?= $this->getHtml('Long'); ?></label>
                                <tr><td><input form="fLocalization" id="iTimeformatLong" name="settings_timeformat_l" type="text" value="<?= $this->printHtml($l11n->getDatetime()['long']); ?>" placeholder="Y" required>
                                <tr><td><label for="iTimeformatVeryLong"><?= $this->getHtml('VeryLong'); ?></label>
                                <tr><td><input form="fLocalization" id="iTimeformatVeryLong" name="settings_timeformat_vl" type="text" value="<?= $this->printHtml($l11n->getDatetime()['very_long']); ?>" placeholder="Y" required>
                            </table>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Numeric'); ?></div>
                        <div class="portlet-body">
                            <form>
                            <table class="layout wf-100">
                                        <tr><td colspan="2"><label for="iCurrencies"><?= $this->getHtml('Currency'); ?></label>
                                    <tr><td colspan="2">
                                            <select form="fLocalization" id="iCurrencies" name="settings_currency">
                                                <?php foreach ($currencies as $code => $currency) : $code = \substr($code, 1); ?>
                                                <option value="<?= $this->printHtml($code); ?>"<?= $this->printHtml($code === $l11n->getCurrency() ? ' selected' : ''); ?>><?= $this->printHtml($currency); ?>
                                                    <?php endforeach; ?>
                                            </select>
                                    <tr><td colspan="2"><label><?= $this->getHtml('Currencyformat'); ?></label>
                                    <tr><td colspan="2">
                                            <select form="fLocalization" name="settings_currencyformat">
                                                <option value="0"<?= $this->printHtml('0' === $l11n->getCurrencyFormat() ? ' selected' : ''); ?>><?= $this->getHtml('Amount') , ' ' , $this->printHtml($l11n->getCurrency()); ?>
                                                <option value="1"<?= $this->printHtml('1' === $l11n->getCurrencyFormat() ? ' selected' : ''); ?>><?= $this->printHtml($l11n->getCurrency()) , ' ' , $this->getHtml('Amount'); ?>
                                            </select>
                                    <tr><td colspan="2"><h2><?= $this->getHtml('Numberformat'); ?></h2>
                                    <tr><td><label for="iDecimalPoint"><?= $this->getHtml('DecimalPoint'); ?></label>
                                        <td><label for="iThousandSep"><?= $this->getHtml('ThousandsSeparator'); ?></label>
                                    <tr><td><input form="fLocalization" id="iDecimalPoint" name="settings_decimal" type="text" value="<?= $this->printHtml($l11n->getDecimal()); ?>" placeholder="." required>
                                        <td><input form="fLocalization" id="iThousandSep" name="settings_thousands" type="text" value="<?= $this->printHtml($l11n->getThousands()); ?>" placeholder="," required>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Precision'); ?></div>
                        <div class="portlet-body">
                            <form>
                                <table class="layout wf-100">
                                    <tbody>
                                    <tr><td><label for="iPrecisionVeryShort"><?= $this->getHtml('VeryShort'); ?></label>
                                    <tr><td>
                                        <input form="fLocalization" id="iPrecisionVeryShort" name="settings_precision_vs" value="<?= $this->printHtml($l11n->getPrecision()['very_short']); ?>" type="number">
                                    <tr><td><label for="iPrecisionShort"><?= $this->getHtml('Short'); ?></label>
                                    <tr><td>
                                        <input form="fLocalization" id="iPrecisionLight" name="settings_precision_s" value="<?= $this->printHtml($l11n->getPrecision()['short']); ?>" type="number">
                                    <tr><td><label for="iPrecisionMedium"><?= $this->getHtml('Medium'); ?></label>
                                    <tr><td>
                                        <input form="fLocalization" id="iPrecisionMedium" name="settings_precision_m" value="<?= $this->printHtml($l11n->getPrecision()['medium']); ?>" type="number">
                                    <tr><td><label for="iPrecisionLong"><?= $this->getHtml('Long'); ?></label>
                                    <tr><td>
                                        <input form="fLocalization" id="iPrecisionLong" name="settings_precision_l" value="<?= $this->printHtml($l11n->getPrecision()['long']); ?>" type="number">
                                    <tr><td><label for="iPrecisionVeryLong"><?= $this->getHtml('VeryLong'); ?></label>
                                    <tr><td>
                                        <input form="fLocalization" id="iPrecisionVeryLong" name="settings_precision_vl" value="<?= $this->printHtml($l11n->getPrecision()['very_long']); ?>" type="number">
                                </table>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Weight'); ?></div>
                        <div class="portlet-body">
                            <form>
                                <table class="layout wf-100">
                                    <tbody>
                                    <tr><td><label for="iWeightVeryLight"><?= $this->getHtml('VeryLight'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iWeightVeryLight" name="settings_weight_vl">
                                            <?php foreach ($weights as $code => $weight) : ?>
                                            <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['very_light'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iWeightLight"><?= $this->getHtml('Light'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iWeightLight" name="settings_weight_l">
                                            <?php foreach ($weights as $code => $weight) : ?>
                                            <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['light'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iWeightMedium"><?= $this->getHtml('Medium'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iWeightMedium" name="settings_weight_m">
                                            <?php foreach ($weights as $code => $weight) : ?>
                                            <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iWeightHeavy"><?= $this->getHtml('Heavy'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iWeightHeavy" name="settings_weight_h">
                                            <?php foreach ($weights as $code => $weight) : ?>
                                            <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['heavy'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iWeightVeryHeavy"><?= $this->getHtml('VeryHeavy'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iWeightVeryHeavy" name="settings_weight_vh">
                                            <?php foreach ($weights as $code => $weight) : ?>
                                            <option value="<?= $this->printHtml($weight); ?>"<?= $this->printHtml($weight === $l11n->getWeight()['very_heavy'] ? ' selected' : ''); ?>><?= $this->printHtml($weight); ?>
                                            <?php endforeach; ?>
                                        </select>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Speed'); ?></div>
                        <div class="portlet-body">
                            <form>
                                <table class="layout wf-100">
                                    <tbody>
                                    <tr><td><label for="iSpeedVerySlow"><?= $this->getHtml('VerySlow'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iSpeedVerySlow" name="settings_speed_vs">
                                            <?php foreach ($speeds as $code => $speed) : ?>
                                            <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['very_slow'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iSpeedSlow"><?= $this->getHtml('Slow'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iSpeedSlow" name="settings_speed_s">
                                            <?php foreach ($speeds as $code => $speed) : ?>
                                            <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['slow'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iSpeedMedium"><?= $this->getHtml('Medium'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iSpeedMedium" name="settings_speed_m">
                                            <?php foreach ($speeds as $code => $speed) : ?>
                                            <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iSpeedFast"><?= $this->getHtml('Fast'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iSpeedFast" name="settings_speed_f">
                                            <?php foreach ($speeds as $code => $speed) : ?>
                                            <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['fast'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iSpeedVeryFast"><?= $this->getHtml('VeryFast'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iSpeedVeryFast" name="settings_speed_vf">
                                            <?php foreach ($speeds as $code => $speed) : ?>
                                            <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['very_fast'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iSpeedSea"><?= $this->getHtml('Sea'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iSpeedSea" name="settings_speed_sea">
                                            <?php foreach ($speeds as $code => $speed) : ?>
                                            <option value="<?= $this->printHtml($speed); ?>"<?= $this->printHtml($speed === $l11n->getSpeed()['sea'] ? ' selected' : ''); ?>><?= $this->printHtml($speed); ?>
                                            <?php endforeach; ?>
                                        </select>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Length'); ?></div>
                        <div class="portlet-body">
                            <form>
                                <table class="layout wf-100">
                                    <tbody>
                                    <tr><td><label for="iLengthVeryShort"><?= $this->getHtml('VeryShort'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iLengthVeryShort" name="settings_length_vs">
                                            <?php foreach ($lengths as $code => $length) : ?>
                                            <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['very_short'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iLengthShort"><?= $this->getHtml('Short'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iLengthShort" name="settings_length_s">
                                            <?php foreach ($lengths as $code => $length) : ?>
                                            <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['short'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iLengthMedium"><?= $this->getHtml('Medium'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iLengthMedium" name="settings_length_m">
                                            <?php foreach ($lengths as $code => $length) : ?>
                                            <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iLengthLong"><?= $this->getHtml('Long'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iLengthLong" name="settings_length_l">
                                            <?php foreach ($lengths as $code => $length) : ?>
                                            <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['long'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iLengthVeryLong"><?= $this->getHtml('VeryLong'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iLengthVeryLong" name="settings_length_vl">
                                            <?php foreach ($lengths as $code => $length) : ?>
                                            <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['very_long'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iLengthSea"><?= $this->getHtml('Sea'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iLengthSea" name="settings_length_sea">
                                            <?php foreach ($lengths as $code => $length) : ?>
                                            <option value="<?= $this->printHtml($length); ?>"<?= $this->printHtml($length === $l11n->getLength()['sea'] ? ' selected' : ''); ?>><?= $this->printHtml($length); ?>
                                            <?php endforeach; ?>
                                        </select>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Area'); ?></div>
                        <div class="portlet-body">
                            <form>
                                <table class="layout wf-100">
                                    <tbody>
                                    <tr><td><label for="iAreaVerySmall"><?= $this->getHtml('VerySmall'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iAreaVerySmall" name="settings_area_vs">
                                            <?php foreach ($areas as $code => $area) : ?>
                                            <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['very_small'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iAreaSmall"><?= $this->getHtml('Small'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iAreaSmall" name="settings_area_s">
                                            <?php foreach ($areas as $code => $area) : ?>
                                            <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['small'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iAreaMedium"><?= $this->getHtml('Medium'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iAreaMedium" name="settings_area_m">
                                            <?php foreach ($areas as $code => $area) : ?>
                                            <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iAreaLarge"><?= $this->getHtml('Large'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iAreaLarge" name="settings_area_l">
                                            <?php foreach ($areas as $code => $area) : ?>
                                            <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['large'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iAreaVeryLarge"><?= $this->getHtml('VeryLarge'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iAreaVeryLarge" name="settings_area_vl">
                                            <?php foreach ($areas as $code => $area) : ?>
                                            <option value="<?= $this->printHtml($area); ?>"<?= $this->printHtml($area === $l11n->getArea()['very_large'] ? ' selected' : ''); ?>><?= $this->printHtml($area); ?>
                                            <?php endforeach; ?>
                                        </select>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Volume'); ?></div>
                        <div class="portlet-body">
                            <form>
                                <table class="layout wf-100">
                                    <tbody>
                                    <tr><td><label for="iVolumeVerySmall"><?= $this->getHtml('VerySmall'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iVolumeVerySmall" name="settings_volume_vs">
                                            <?php foreach ($volumes as $code => $volume) : ?>
                                            <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['very_small'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iVolumeSmall"><?= $this->getHtml('Small'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iVolumeSmall" name="settings_volume_s">
                                            <?php foreach ($volumes as $code => $volume) : ?>
                                            <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['small'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iVolumeMedium"><?= $this->getHtml('Medium'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iVolumeMedium" name="settings_volume_m">
                                            <?php foreach ($volumes as $code => $volume) : ?>
                                            <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['medium'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iVolumeLarge"><?= $this->getHtml('Large'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iVolumeLarge" name="settings_volume_l">
                                            <?php foreach ($volumes as $code => $volume) : ?>
                                            <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['large'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iVolumeVeryLarge"><?= $this->getHtml('VeryLarge'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iVolumeVeryLarge" name="settings_volume_vl">
                                            <?php foreach ($volumes as $code => $volume) : ?>
                                            <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['very_large'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iVolumeTeaspoon"><?= $this->getHtml('Teaspoon'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iVolumeTeaspoon" name="settings_volume_teaspoon">
                                            <?php foreach ($volumes as $code => $volume) : ?>
                                            <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['teaspoon'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iVolumeTablespoon"><?= $this->getHtml('Tablespoon'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iVolumeTablespoon" name="settings_volume_tablespoon">
                                            <?php foreach ($volumes as $code => $volume) : ?>
                                            <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['tablespoon'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <tr><td><label for="iVolumeGlass"><?= $this->getHtml('Glass'); ?></label>
                                    <tr><td>
                                        <select form="fLocalization" id="iVolumeGlass" name="settings_volume_glass">
                                            <?php foreach ($volumes as $code => $volume) : ?>
                                            <option value="<?= $this->printHtml($volume); ?>"<?= $this->printHtml($volume === $l11n->getVolume()['glass'] ? ' selected' : ''); ?>><?= $this->printHtml($volume); ?>
                                            <?php endforeach; ?>
                                        </select>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
