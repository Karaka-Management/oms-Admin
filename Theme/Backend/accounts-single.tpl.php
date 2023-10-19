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

use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Account\PermissionOwner;
use phpOMS\Account\PermissionType;
use phpOMS\Localization\ISO3166NameEnum;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO4217Enum;
use phpOMS\Localization\ISO639Enum;
use phpOMS\Localization\ISO8601EnumArray;
use phpOMS\Localization\TimeZoneEnumArray;
use phpOMS\System\File\Local\Directory;
use phpOMS\Uri\UriFactory;
use phpOMS\Utils\Converter\AreaType;
use phpOMS\Utils\Converter\LengthType;
use phpOMS\Utils\Converter\SpeedType;
use phpOMS\Utils\Converter\TemperatureType;
use phpOMS\Utils\Converter\VolumeType;
use phpOMS\Utils\Converter\WeightType;

/**
 * @var \phpOMS\Views\View $this
 */
$account     = $this->data['account'];
$permissions = $this->data['permissions'];
$l11n        = $account->l11n;

$audits = $this->data['audits'] ?? [];

$tableView            = $this->data['tableView'];
$tableView->id        = 'auditList';
$tableView->baseUri   = '{/base}/admin/account/settings?id=' . $account->id;
$tableView->exportUri = '{/api}auditor/list/export';
$tableView->setObjects($audits);

$previous = $tableView->getPreviousLink(
    $this->request,
    empty($tableView->objects) || !$this->getData('hasPrevious') ? null : \reset($tableView->objects)
);

$next = $tableView->getNextLink(
    $this->request,
    empty($tableView->objects) ? null : \end($tableView->objects),
    $this->getData('hasNext') ?? false
);

echo $this->data['nav']->render(); ?>

<div id="iaccount-tabs" class="tabview tab-2 url-rewrite">
    <div class="box">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('General'); ?></label></li>
            <li><label for="c-tab-2"><?= $this->getHtml('Groups'); ?></label></li>
            <li><label for="c-tab-3"><?= $this->getHtml('Permissions'); ?></label></li>
            <li><label for="c-tab-4"><?= $this->getHtml('Localization'); ?></label></li>
            <li><label for="c-tab-5"><?= $this->getHtml('AuditLog'); ?></label></li>
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="portlet">
                        <form id="account-edit" action="<?= UriFactory::build('{/api}admin/account'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Account'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iId"><?= $this->getHtml('ID', '0', '0'); ?></label>
                                    <input id="iId" name="iaccount-idlist" type="text" value="<?= $account->id; ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="iType"><?= $this->getHtml('Type'); ?></label>
                                    <select id="iType" name="type">
                                        <option value="<?= AccountType::USER; ?>"<?= $this->printHtml($account->getType() === AccountType::USER ? ' selected' : ''); ?>><?= $this->getHtml('Person'); ?>
                                        <option value="<?= AccountType::GROUP; ?>"<?= $this->printHtml($account->getType() === AccountType::GROUP ? ' selected' : ''); ?>><?= $this->getHtml('Organization'); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                                    <select id="iStatus" name="status">
                                        <option value="<?= AccountStatus::ACTIVE; ?>"<?= $this->printHtml($account->getStatus() === AccountStatus::ACTIVE ? ' selected' : ''); ?>><?= $this->getHtml('Active'); ?>
                                        <option value="<?= AccountStatus::INACTIVE; ?>"<?= $this->printHtml($account->getStatus() === AccountStatus::INACTIVE ? ' selected' : ''); ?>><?= $this->getHtml('Inactive'); ?>
                                        <option value="<?= AccountStatus::TIMEOUT; ?>"<?= $this->printHtml($account->getStatus() === AccountStatus::TIMEOUT ? ' selected' : ''); ?>><?= $this->getHtml('Timeout'); ?>
                                        <option value="<?= AccountStatus::BANNED; ?>"<?= $this->printHtml($account->getStatus() === AccountStatus::BANNED ? ' selected' : ''); ?>><?= $this->getHtml('Banned'); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="iUsername"><?= $this->getHtml('Username'); ?></label>
                                    <span class="input">
                                        <button class="inactive" type="button"><i class="g-icon">person</i></button>
                                        <input id="iUsername" name="name" type="text" autocomplete="off" spellcheck="false" value="<?= $this->printHtml($account->login); ?>">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="iName1"><?= $this->getHtml('Name1'); ?></label>
                                    <span class="input">
                                        <button class="inactive" type="button"><i class="g-icon">person</i></button>
                                        <input id="iName1" name="name1" type="text" autocomplete="off" spellcheck="false" value="<?= $this->printHtml($account->name1); ?>" required>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="iName2"><?= $this->getHtml('Name2'); ?></label>
                                    <span class="input">
                                        <button class="inactive" type="button"><i class="g-icon">person</i></button>
                                        <input id="iName2" name="name2" type="text" autocomplete="off" spellcheck="false" value="<?= $this->printHtml($account->name2); ?>">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="iName3"><?= $this->getHtml('Name3'); ?></label>
                                    <span class="input">
                                        <button class="inactive" type="button"><i class="g-icon">person</i></button>
                                        <input id="iName3" name="name3" type="text" autocomplete="off" spellcheck="false" value="<?= $this->printHtml($account->name3); ?>">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="iEmail"><?= $this->getHtml('Email'); ?></label>
                                    <span class="input">
                                        <button class="inactive" type="button"><i class="g-icon">mail</i></button>
                                        <input id="iEmail" name="email" type="email" autocomplete="off" spellcheck="false" value="<?= $this->printHtml($account->getEmail()); ?>">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="iPassword"><?= $this->getHtml('Password'); ?></label>
                                    <div class="ipt-wrap">
                                        <div class="ipt-first">
                                            <span class="input">
                                                <button class="inactive" type="button"><i class="g-icon">lock</i></button>
                                                <input id="iPassword" name="password" type="password">
                                            </span>
                                        </div>
                                        <div class="ipt-second"><div class="nowrap"> or <button><?= $this->getHtml('Reset'); ?></button></div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input id="account-edit-submit" name="editSubmit" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                                <button id="account-profile-create" data-action='[
                                    {
                                        "key": 1, "listener": "click", "action": [
                                            {"key": 1, "type": "event.prevent"},
                                            {"key": 2, "type": "dom.getvalue", "base": "", "selector": "#iId"},
                                            {"key": 3, "type": "message.request", "uri": "{/base}/{/lang}/api/profile", "method": "PUT", "request_type": "json"},
                                            {"key": 4, "type": "message.log"}
                                        ]
                                    }
                                ]'><?= $this->getHtml('CreateProfile'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="portlet">
                        <form id="iAddGroupToAccount" action="<?= UriFactory::build('{/api}admin/account/group'); ?>" method="put">
                            <div class="portlet-head"><?= $this->getHtml('Groups'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iGroup"><?= $this->getHtml('Name'); ?></label>
                                    <?= $this->getData('grpSelector')->render('iGroup', true); ?>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input name="account" type="hidden" value="<?= $account->id; ?>">
                                <input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Groups'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <table id="groupTable" class="default">
                            <thead>
                                <tr>
                                    <td>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                    <td class="wf-100"><?= $this->getHtml('Name'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                            <tbody>
                                <?php
                                    $c      = 0;
                                    $groups = $account->getGroups();
                                    foreach ($groups as $key => $value) : ++$c;
                                        $url = UriFactory::build('{/base}/admin/group/settings?{?}&id=' . $value->id);
                                ?>
                                <tr data-href="<?= $url; ?>">
                                    <td><a href="#"><i class="g-icon">close</i></a>
                                    <td><a href="<?= $url; ?>"><?= $value->id; ?></a>
                                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                    <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-3" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-3' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                <div class="portlet">
                        <form id="permissionForm"
                            action="<?= UriFactory::build('{/api}admin/account/permission'); ?>"
                            data-ui-container="#permissionTable tbody"
                            data-add-form="permissionForm"
                            data-add-tpl="#permissionTable tbody .oms-add-tpl-permission"
                            method="put">
                            <div class="portlet-head"><?= $this->getHtml('Permissions'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iPermissionId"><?= $this->getHtml('ID', '0', '0'); ?></label>
                                    <input id="iPermissionId" name="permissionref" type="text" data-tpl-text="/id" data-tpl-value="/id" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="iPermissionUnit"><?= $this->getHtml('Unit'); ?></label>
                                    <select id="iPermissionUnit" name="permissionunit" data-tpl-text="/unit" data-tpl-value="/unit">
                                        <option value="">
                                        <?php
                                        foreach ($this->data['units'] as $unit) : ?>
                                            <option value="<?= $unit->id; ?>"><?= $this->printHtml($unit->name); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="iPermissionApp"><?= $this->getHtml('App'); ?></label>
                                    <select id="iPermissionApp" name="permissionapp" data-tpl-text="/app" data-tpl-value="/app">
                                        <option value="">
                                        <?php
                                        foreach ($this->data['apps'] as $app) : ?>
                                            <option value="<?= $app->id; ?>"><?= $this->printHtml($app->name); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="iPermissionModule"><?= $this->getHtml('Module'); ?></label>
                                    <select id="iPermissionModule" name="permissionmodule" data-tpl-text="/module" data-tpl-value="/module">
                                        <option value="">
                                        <?php
                                        foreach ($this->data['modules'] as $module) : ?>
                                            <option value="<?= $module->name; ?>"><?= $this->printHtml($module->name); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="iPermissionType"><?= $this->getHtml('Type'); ?></label>
                                    <input id="iPermissionType" name="permissiontype" type="text" data-tpl-text="/type" data-tpl-value="/type">
                                </div>
                                <div class="form-group">
                                    <label for="iPermissionElement"><?= $this->getHtml('Element'); ?></label>
                                    <input id="iPermissionElement" name="permissionelement" type="text" data-tpl-text="/ele" data-tpl-value="/ele">
                                </div>
                                <div class="form-group">
                                    <label for="iPermissionComponent"><?= $this->getHtml('Component'); ?></label>
                                    <input id="iPermissionComponent" name="permissioncomponent" type="text" data-tpl-text="/comp" data-tpl-value="/comp">
                                </div>
                                <div class="form-group">
                                    <label><?= $this->getHtml('Permission'); ?></label>
                                    <span class="checkbox">
                                        <label class="checkbox" for="iPermissionCreate">
                                            <input id="iPermissionCreate" type="checkbox" name="permissioncreate" value="<?= PermissionType::CREATE; ?>" data-tpl-text="/perm/c" data-tpl-value="/perm/c">
                                            <span class="checkmark"></span>
                                            <?= $this->getHtml('Create'); ?>
                                        </label>
                                    </span>

                                    <span class="checkbox">
                                        <label class="checkbox" for="iPermissionRead">
                                            <input id="iPermissionRead" type="checkbox" name="permissionread" value="<?= PermissionType::READ; ?>" data-tpl-text="/perm/r" data-tpl-value="/perm/r">
                                            <span class="checkmark"></span>
                                            <?= $this->getHtml('Read'); ?>
                                        </label>
                                    </span>

                                    <span class="checkbox">
                                        <label class="checkbox" for="iPermissionUpdate">
                                            <input id="iPermissionUpdate" type="checkbox" name="permissionupdate" value="<?= PermissionType::MODIFY; ?>" data-tpl-text="/perm/u" data-tpl-value="/perm/u">
                                            <span class="checkmark"></span>
                                            <?= $this->getHtml('Update'); ?>
                                        </label>
                                    </span>

                                    <span class="checkbox">
                                        <label class="checkbox" for="iPermissionDelete">
                                            <input id="iPermissionDelete" type="checkbox" name="permissiondelete" value="<?= PermissionType::DELETE; ?>" data-tpl-text="/perm/d" data-tpl-value="/perm/d">
                                            <span class="checkmark"></span>
                                            <?= $this->getHtml('Delete'); ?>
                                        </label>
                                    </span>

                                    <span class="checkbox">
                                        <label class="checkbox" for="iPermissionPermission">
                                            <input id="iPermissionPermission" type="checkbox" name="permissionpermission" value="<?= PermissionType::PERMISSION; ?>" data-tpl-text="/perm/p" data-tpl-value="/perm/p">
                                            <span class="checkmark"></span>
                                            <?= $this->getHtml('Permission'); ?>
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input type="hidden" name="permissionowner" value="<?= PermissionOwner::GROUP; ?>">

                                <input id="bPermissionAdd" formmethod="put" type="submit" class="add-form" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                                <input id="bPermissionSave" formmethod="post" type="submit" class="save-form hidden button save" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                                <input type="submit" class="cancel-form hidden button close" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Permissions'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <div class="slider">
                            <table id="permissionTable" class="default"
                                data-tag="form"
                                data-ui-element="tr"
                                data-add-tpl=".oms-add-tpl-permission"
                                data-update-form="permissionForm">
                                <thead>
                                    <tr>
                                        <td>
                                        <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                        <td><?= $this->getHtml('Unit'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                        <td><?= $this->getHtml('App'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                        <td><?= $this->getHtml('Module'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                        <td><?= $this->getHtml('Type'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                        <td><?= $this->getHtml('Ele'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                        <td><?= $this->getHtml('Comp'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                        <td class="wf-100"><?= $this->getHtml('Perm'); ?>
                                <tbody>
                                    <template class="oms-add-tpl-permission">
                                        <tr data-id="" draggable="false">
                                            <td><i class="g-icon btn update-form">settings</i>
                                                <input id="permissionTable-remove-0" type="checkbox" class="hidden">
                                                <label for="permissionTable-remove-0" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                                <span class="checked-visibility">
                                                    <label for="permissionTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                    <label for="permissionTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                                </span>
                                            <td></td>
                                            <td data-tpl-text="/unit" data-tpl-value="/unit" data-value=""></td>
                                            <td data-tpl-text="/app" data-tpl-value="/app" data-value=""></td>
                                            <td data-tpl-text="/module" data-tpl-value="/module" data-value=""></td>
                                            <td data-tpl-text="/type" data-tpl-value="/type" data-value=""></td>
                                            <td data-tpl-text="/ele" data-tpl-value="/ele" data-value=""></td>
                                            <td data-tpl-text="/comp" data-tpl-value="/comp" data-value=""></td>
                                            <td>
                                                <span data-tpl-text="/perm/c" data-tpl-value="/perm/c" data-value=""><span>
                                                <span data-tpl-text="/perm/r" data-tpl-value="/perm/r" data-value=""><span>
                                                <span data-tpl-text="/perm/u" data-tpl-value="/perm/u" data-value=""><span>
                                                <span data-tpl-text="/perm/d" data-tpl-value="/perm/d" data-value=""><span>
                                                <span data-tpl-text="/perm/p" data-tpl-value="/perm/p" data-value=""><span>
                                            </td>
                                        </tr>
                                    </template>
                                    <?php $c = 0;
                                    foreach ($permissions as $key => $value) : ++$c;
                                        $permission = $value->getPermission(); ?>
                                    <tr data-id="<?= $value->id; ?>">
                                        <td><i class="g-icon btn update-form">settings</i>
                                            <input id="permissionTable-remove-<?= $value->id; ?>" type="checkbox" class="hidden">
                                            <label for="permissionTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                            <span class="checked-visibility">
                                                <label for="permissionTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="permissionTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                        <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                                        <td data-tpl-text="/unit" data-tpl-value="/unit" data-value="<?= $this->printHtml((string) $value->unit); ?>"><?= $this->printHtml(isset($this->data['units'][$value->unit]) ? $this->data['units'][$value->unit]->name : ''); ?>
                                        <td data-tpl-text="/app" data-tpl-value="/app" data-value="<?= $this->printHtml((string) $value->app); ?>"><?= $this->printHtml(isset($this->data['apps'][$value->app]) ? $this->data['apps'][$value->app]->name : ''); ?>
                                        <td data-tpl-text="/module" data-tpl-value="/module" data-value="<?= $this->printHtml($value->module); ?>"><?= $this->printHtml($value->module); ?>
                                        <td data-tpl-text="/type" data-tpl-value="/type"><?= $this->printHtml((string) $value->category); ?>
                                        <td data-tpl-text="/ele" data-tpl-value="/ele"><?= $this->printHtml((string) $value->element); ?>
                                        <td data-tpl-text="/comp" data-tpl-value="/comp"><?= $this->printHtml((string) $value->component); ?>
                                        <td>
                                            <?php if ((PermissionType::CREATE | $permission) === $permission) : ?>
                                                <span data-tpl-text="/perm/c" data-tpl-value="/perm/c" data-value="<?= PermissionType::CREATE; ?>">C</span>
                                            <?php endif; ?>
                                            <?php if ((PermissionType::READ | $permission) === $permission) : ?>
                                                <span data-tpl-text="/perm/r" data-tpl-value="/perm/r" data-value="<?= PermissionType::READ; ?>">R</span>
                                            <?php endif; ?>
                                            <?php if ((PermissionType::MODIFY | $permission) === $permission) : ?>
                                                <span data-tpl-text="/perm/u" data-tpl-value="/perm/u" data-value="<?= PermissionType::MODIFY; ?>">U</span>
                                            <?php endif; ?>
                                            <?php if ((PermissionType::DELETE | $permission) === $permission) : ?>
                                                <span data-tpl-text="/perm/d" data-tpl-value="/perm/d" data-value="<?= PermissionType::DELETE; ?>">D</span>
                                            <?php endif; ?>
                                            <?php if ((PermissionType::PERMISSION | $permission) === $permission) : ?>
                                                <span data-tpl-text="/perm/p" data-tpl-value="/perm/p" data-value="<?= PermissionType::PERMISSION; ?>">P</span>
                                            <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if ($c === 0) : ?>
                                    <tr><td colspan="9" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                    <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
            $countryCodes    = ISO3166TwoEnum::getConstants();
            $countries       = ISO3166NameEnum::getConstants();
            $timezones       = TimeZoneEnumArray::getConstants();
            $timeformats     = ISO8601EnumArray::getConstants();
            $languages       = ISO639Enum::getConstants();
            $currencies      = ISO4217Enum::getConstants();
            $l11nDefinitions = Directory::list(__DIR__ . '/../../../../phpOMS/Localization/Defaults/Definitions');

            $weights      = WeightType::getConstants();
            $speeds       = SpeedType::getConstants();
            $areas        = AreaType::getConstants();
            $lengths      = LengthType::getConstants();
            $volumes      = VolumeType::getConstants();
            $temperatures = TemperatureType::getConstants();
        ?>
        <input type="radio" id="c-tab-4" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-4' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <form id="fLocalization" name="fLocalization" action="<?= UriFactory::build('{/api}admin/account/localization'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Localization'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iDefaultLocalizations"><?= $this->getHtml('Defaults'); ?></label>
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
                                </div>
                                <div class="form-group">
                                    <label for="iCountries"><?= $this->getHtml('Country'); ?></label>
                                    <select id="iCountries" name="settings_country">
                                        <?php foreach ($countryCodes as $code3 => $code2) : ?>
                                        <option value="<?= $this->printHtml($code2); ?>"<?= $this->printHtml($code2 === $l11n->country ? ' selected' : ''); ?>><?= $this->printHtml($countries[$code3]); ?>
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
                                <input type="hidden" name="account_id" value="<?= $account->id; ?>">
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
                                <div class="form-group">
                                    <label for="iTimezones"><?= $this->getHtml('Timezone'); ?></label>
                                    <select form="fLocalization" id="iTimezones" name="settings_timezone">
                                        <?php foreach ($timezones as $timezone) : ?>
                                        <option value="<?= $this->printHtml($timezone); ?>"<?= $this->printHtml($timezone === $l11n->getTimezone() ? ' selected' : ''); ?>><?= $this->printHtml($timezone); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <h2><?= $this->getHtml('Timeformat'); ?></h2>
                                <div class="form-group">
                                    <label for="iTimeformatVeryShort"><?= $this->getHtml('VeryShort'); ?></label>
                                    <input form="fLocalization" id="iTimeformatVeryShort" name="settings_timeformat_vs" type="text" value="<?= $this->printHtml($l11n->getDatetime()['very_short']); ?>" placeholder="Y" required>
                                </div>
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
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Numeric'); ?></div>
                        <div class="portlet-body">
                            <form>
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
                                <div class="form-group">
                                    <label><?= $this->getHtml('Numberformat'); ?>   </label>
                                    <div class="form-group">
                                        <label for="iDecimalPoint"><?= $this->getHtml('DecimalPoint'); ?></label>
                                        <input form="fLocalization" id="iDecimalPoint" name="settings_decimal" type="text" value="<?= $this->printHtml($l11n->getDecimal()); ?>" placeholder="." required>
                                    </div>
                                    <div class="form-group">
                                        <label for="iThousandSep"><?= $this->getHtml('ThousandsSeparator'); ?></label>
                                        <input form="fLocalization" id="iThousandSep" name="settings_thousands" type="text" value="<?= $this->printHtml($l11n->getThousands()); ?>" placeholder="," required>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Precision'); ?></div>
                        <div class="portlet-body">
                            <form>
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
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Weight'); ?></div>
                        <div class="portlet-body">
                            <form>
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
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Speed'); ?></div>
                        <div class="portlet-body">
                            <form>
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
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Length'); ?></div>
                        <div class="portlet-body">
                            <form>
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
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Area'); ?></div>
                        <div class="portlet-body">
                            <form>
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
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Volume'); ?></div>
                        <div class="portlet-body">
                            <form>
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-5" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-5' ? ' checked' : ''; ?>>
        <div class="tab">
        <div class="row">
            <div class="col-xs-12">
                <div class="portlet">
                    <div class="portlet-head"><?= $tableView->renderTitle($this->getHtml('Audits', 'Auditor', 'Backend')); ?></div>
                    <div class="slider">
                    <table id="<?= $tableView->id; ?>" class="default sticky">
                        <thead>
                        <tr>
                            <td><?= $tableView->renderHeaderElement('id',  $this->getHtml('ID', '0', '0'), 'number'); ?>
                            <td><?= $tableView->renderHeaderElement('module', $this->getHtml('Module', 'Auditor', 'Backend'), 'text'); ?>
                            <td><?= $tableView->renderHeaderElement('action', $this->getHtml('Action', 'Auditor', 'Backend'), 'select',
                                [
                                    'create' => $this->getHtml('CREATE', 'Auditor', 'Backend'),
                                    'modify' => $this->getHtml('UPDATE', 'Auditor', 'Backend'),
                                    'delete' => $this->getHtml('DELETE', 'Auditor', 'Backend'),
                                ],
                                false // don't render sort
                            ); ?>
                            <td><?= $tableView->renderHeaderElement('type', $this->getHtml('Type', 'Auditor', 'Backend'), 'number'); ?>
                            <td class="wf-100"><?= $tableView->renderHeaderElement('trigger', $this->getHtml('Trigger', 'Auditor', 'Backend'), 'text'); ?>
                            <td><?= $tableView->renderHeaderElement('createdBy', $this->getHtml('By', 'Auditor', 'Backend'), 'text'); ?>
                            <td><?= $tableView->renderHeaderElement('ref', $this->getHtml('Ref', 'Auditor', 'Backend'), 'text', [], true, true, false); ?>
                            <td><?= $tableView->renderHeaderElement('createdAt', $this->getHtml('Date', 'Auditor', 'Backend'), 'date'); ?>
                        <tbody>
                        <?php
                        $count = 0;
                        foreach ($audits as $key => $audit) : ++$count;
                            $url = UriFactory::build('{/base}/admin/audit/single?id=' . $audit->id); ?>
                            <tr tabindex="0" data-href="<?= $url; ?>">
                                <td><?= $audit->id; ?>
                                <td><?= $this->printHtml($audit->module); ?>
                                <td><?php if ($audit->old === null) : echo $this->getHtml('CREATE', 'Auditor', 'Backend'); ?>
                                    <?php elseif ($audit->old !== null && $audit->new !== null) : echo $this->getHtml('UPDATE', 'Auditor', 'Backend'); ?>
                                    <?php elseif ($audit->new === null) : echo $this->getHtml('DELETE', 'Auditor', 'Backend'); ?>
                                    <?php else : echo $this->getHtml('UNKNOWN', 'Auditor', 'Backend'); ?>
                                    <?php endif; ?>
                                <td><?= $this->printHtml((string) $audit->type); ?>
                                <td><?= $this->printHtml($audit->trigger); ?>
                                <td><a class="content" href="<?= UriFactory::build('{/base}/admin/account/settings?id=' . $audit->createdBy->id); ?>"><?= $this->printHtml(
                                        $this->renderUserName('%3$s %2$s %1$s', [$audit->createdBy->name1, $audit->createdBy->name2, $audit->createdBy->name3, $audit->createdBy->login])
                                    ); ?></a>
                                <td><?= $this->printHtml((string) $audit->ref); ?>
                                <td><?= $audit->createdAt->format('Y-m-d H:i:s'); ?>
                        <?php endforeach; ?>
                        <?php if ($count === 0) : ?>
                            <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                        <?php endif; ?>
                    </table>
                    </div>
                    <?php if ($this->getData('hasPrevious') || $this->getData('hasNext')) : ?>
                        <div class="portlet-foot">
                            <?php if ($this->getData('hasPrevious')) : ?>
                                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><i class="g-icon">chevron_left</i></a>
                            <?php endif; ?>
                            <?php if ($this->getData('hasNext')) : ?>
                                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><i class="g-icon">chevron_right</i></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        </div>
    </div>
</div>
