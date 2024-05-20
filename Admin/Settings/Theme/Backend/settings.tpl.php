<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Admin\Models\SettingsEnum;
use Modules\Organization\Models\UnitMapper;
use phpOMS\Application\ApplicationStatus;
use phpOMS\Localization\NullLocalization;
use phpOMS\Message\Mail\SubmitType;
use phpOMS\Uri\UriFactory;

$generalSettings = $this->data['generalSettings'] ?? [];
$settings        = $this->data['settings'] ?? [];

$serverModes     = ApplicationStatus::getConstants();
$mailServerModes = SubmitType::getConstants();

$l11n = $this->data['default_localization'] ?? new NullLocalization();

echo $this->data['nav']->render();
?>

<div id="iSettings" class="tabview tab-2 url-rewrite">
    <div class="box">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('General'); ?></label>
            <li><label for="c-tab-2"><?= $this->getHtml('Localization'); ?></label>
            <li><label for="c-tab-3"><?= $this->getHtml('Design'); ?></label>
            <li><label for="c-tab-5"><?= $this->getHtml('Settings'); ?></label>
        </ul>
    </div>
<div class="tab-content">
    <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
    <div class="tab">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <section class="portlet">
                    <form id="iGeneralSettings" action="<?= UriFactory::build('{/api}admin/settings/general?csrf={$CSRF}'); ?>" method="post">
                        <div class="portlet-head"><?= $this->getHtml('Settings'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iOname"><?= $this->getHtml('OrganizationName'); ?></label>
                                <select id="iOname" name="settings_<?= SettingsEnum::DEFAULT_UNIT; ?>">
                                    <?php $unit = UnitMapper::get()->where('id', 1)->execute(); ?>
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
                    <form id="iGeneralSettings" action="<?= UriFactory::build('{/api}admin/settings?csrf={$CSRF}'); ?>" method="post">
                        <div class="portlet-head"><?= $this->getHtml('ServerStatus'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iStatus"><?= $this->getHtml('ServerStatus'); ?></label>
                                <select id="iStatus" name="settings_<?= SettingsEnum::LOGIN_STATUS; ?>">
                                    <?php foreach ($serverModes as $mode) : ?>
                                    <option value="<?= $this->printHtml((string) $mode); ?>"<?= $mode === $generalSettings[SettingsEnum::LOGIN_STATUS]->content ? ' selected' : ''; ?>><?= $this->getHtml('ServerMode-' . $mode); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="portlet-foot"><input id="iSubmitGeneral" name="submitGeneral" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>"></div>
                    </form>
                </section>
            </div>

            <div class="col-xs-12 col-md-6">
                <section class="portlet">
                    <form id="iSecuritySettings" action="<?= UriFactory::build('{/api}admin/settings?csrf={$CSRF}'); ?>" method="post">
                        <div class="portlet-head"><?= $this->getHtml('Security'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iPassword">
                                    <?= $this->getHtml('PasswordRegex'); ?>
                                    <i class="tooltip" data-tooltip="<?= $this->getHtml('i:PasswordRegex'); ?>"><i class="g-icon">info</i></i>
                                </label>

                                <input id="iPassword" name="settings_<?= SettingsEnum::PASSWORD_PATTERN; ?>" type="text" value="<?= $this->printHtml($generalSettings[SettingsEnum::PASSWORD_PATTERN]->content); ?>" placeholder="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&;:\(\)\[\]=\{\}\+\-])[A-Za-z\d$@$!%*?&;:\(\)\[\]=\{\}\+\-]{8,}">
                            </div>

                            <!-- @implement
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
                            -->

                        </div>
                        <div class="portlet-foot">
                            <input id="iSubmitGeneral" name="submitGeneral" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                        </div>
                    </form>
                </section>
            </div>

            <div class="col-xs-12 col-md-6">
                <section class="portlet">
                    <form id="iEmailSettings" action="<?= UriFactory::build('{/api}admin/settings?csrf={$CSRF}'); ?>" method="post">
                        <div class="portlet-head"><?= $this->getHtml('Email'); ?></div>
                        <div class="portlet-body">
                            <div class="form-group">
                                <label for="iOutServer"><?= $this->getHtml('OutServer'); ?></label>
                                <input id="iOutServer" type="text" name="settings_<?= SettingsEnum::MAIL_SERVER_OUT; ?>" value="<?= $this->printHtml($generalSettings[SettingsEnum::MAIL_SERVER_OUT]->content); ?>">
                            </div>

                            <div class="form-group">
                                <label for="iOutPort"><?= $this->getHtml('OutPort'); ?></label>
                                <input id="iOutPort" type="text" name="settings_<?= SettingsEnum::MAIL_SERVER_PORT_OUT; ?>" value="<?= $this->printHtml($generalSettings[SettingsEnum::MAIL_SERVER_PORT_OUT]->content); ?>">
                            </div>

                            <div class="form-group">
                                <label for="iInServer"><?= $this->getHtml('InServer'); ?></label>
                                <input id="iInServer" type="text" name="settings_<?= SettingsEnum::MAIL_SERVER_IN; ?>" value="<?= $this->printHtml($generalSettings[SettingsEnum::MAIL_SERVER_IN]->content); ?>">
                            </div>

                            <div class="form-group">
                                <label for="iInPort"><?= $this->getHtml('InPort'); ?></label>
                                <input id="iInPort" type="text" name="settings_<?= SettingsEnum::MAIL_SERVER_PORT_IN; ?>" value="<?= $this->printHtml($generalSettings[SettingsEnum::MAIL_SERVER_PORT_IN]->content); ?>">
                            </div>

                            <div class="form-group">
                                <label for="iEmailType"><?= $this->getHtml('EmailType'); ?></label>
                                <select id="iEmailType" name="settings_<?= SettingsEnum::MAIL_SERVER_TYPE; ?>">
                                    <?php foreach ($mailServerModes as $mode) : ?>
                                    <option value="<?= $this->printHtml((string) $mode); ?>"<?= $mode === $generalSettings[SettingsEnum::MAIL_SERVER_TYPE]->content ? ' selected' : ''; ?>><?= $this->printHtml($mode); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iEmailUsername"><?= $this->getHtml('EmailUsername'); ?></label>
                                <input id="iEmailUsername" type="text" name="settings_<?= SettingsEnum::MAIL_SERVER_USER; ?>" value="<?= $this->printHtml($generalSettings[SettingsEnum::MAIL_SERVER_USER]->content); ?>">
                            </div>

                            <div class="form-group">
                                <label for="iEmailPassword"><?= $this->getHtml('EmailPassword'); ?></label>
                                <input id="iEmailPassword" type="password" name="settings_<?= SettingsEnum::MAIL_SERVER_PASS; ?>" value="<?= $this->printHtml($generalSettings[SettingsEnum::MAIL_SERVER_PASS]->content); ?>">
                            </div>

                            <div class="form-group">
                                <label for="iEmailAddress"><?= $this->getHtml('EmailAddress'); ?></label>
                                <input id="iEmailAddress" type="text" name="settings_<?= SettingsEnum::MAIL_SERVER_ADDR; ?>" value="<?= $this->printHtml($generalSettings[SettingsEnum::MAIL_SERVER_ADDR]->content); ?>">
                            </div>
                        </div>
                        <div class="portlet-foot">
                            <input type="hidden" name="module" value="Admin">
                            <input id="iSubmitEmail" name="submitEmail" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                        </div>
                    </form>
                </section>
            </div>

        </div>
    </div>
    <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
    <div class="tab">
        <?= include __DIR__ . "/../../../../Theme/Backend/Components/Localization/l11n-view.tpl.php"; ?>
    </div>
    <input type="radio" id="c-tab-3"
        name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-3' ? ' checked' : ''; ?>>
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
                                    <form id="iLoginImageUploadForm" action="<?= UriFactory::build('{/api}admin/settings/design?csrf={$CSRF}'); ?>" method="post">
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
    <input type="radio" id="c-tab-5"
        name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-5' ? ' checked' : ''; ?>>
        <div class="tab">
            <?php include __DIR__ . '/../../../../Theme/Backend/settings.tpl.php'; ?>
        </div>
    </div>
</div>
