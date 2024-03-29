<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Attribute
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Account\PermissionType;
use phpOMS\Localization\ISO639Enum;

$permissions = $this->permissions;
$categories  = ISO639Enum::getConstants();

?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('PermissionSelector', 'Admin', 'Backend'); ?></div>
            <div class="portlet-body">
                <div class="form-group">
                    <label><?= $this->getHtml('GroupAccount', 'Admin', 'Backend'); ?></label>
                    <div class="ipt-wrap wf-100">
                        <div class="ipt-first">
                            <span class="input">
                                <button type="button" id="<?= $this->id; ?>-book-button" data-action='[
                                    {
                                        "key": 1, "listener": "click", "action": [
                                            {"key": 1, "type": "dom.popup", "selector": "#group-selector-tpl", "aniIn": "fadeIn", "id": "<?= $this->id; ?>"},
                                            {"key": 2, "type": "message.request", "uri": "<?= \phpOMS\Uri\UriFactory::build('{/base}/admin/group?filter=some&limit=10'); ?>", "method": "GET", "request_type": "json"},
                                            {"key": 3, "type": "dom.table.append", "id": "acc-table", "aniIn": "fadeIn", "data": [], "bindings": {"id": "id", "name": "name/0"}, "position": -1},
                                            {"key": 4, "type": "message.request", "uri": "<?= \phpOMS\Uri\UriFactory::build('{/base}/admin/group?filter=some&limit=10'); ?>", "method": "GET", "request_type": "json"},
                                            {"key": 5, "type": "dom.table.append", "id": "grp-table", "aniIn": "fadeIn", "data": [], "bindings": {"id": "id", "name": "name/0"}, "position": -1}
                                        ]
                                    }
                                ]'><i class="g-icon">book</i></button>
                                <input type="text" list="<?= $this->id; ?>-datalist" id="<?= $this->id; ?>" name="receiver" data-action='[
                                    {
                                        "key": 1, "listener": "keyup", "action": [
                                            {"key": 1, "type": "validate.keypress", "pressed": "!13!37!38!39!40"},
                                            {"key": 2, "type": "utils.timer", "id": "<?= $this->id; ?>", "delay": 500, "resets": true},
                                            {"key": 3, "type": "dom.datalist.clear", "id": "<?= $this->id; ?>-datalist"},
                                            {"key": 4, "type": "message.request", "uri": "{/base}/{/lang}/api/admin/find/group?search={!#<?= $this->id; ?>}", "method": "GET", "request_type": "json"},
                                            {"key": 5, "type": "dom.datalist.append", "id": "<?= $this->id; ?>-datalist", "value": "id", "text": "name"}
                                        ]
                                    },
                                    {
                                        "key": 2, "listener": "keydown", "action" : [
                                            {"key": 1, "type": "validate.keypress", "pressed": "13|9"},
                                            {"key": 2, "type": "message.request", "uri": "{/base}/{/lang}/api/admin/find/group?search={!#<?= $this->id; ?>}", "method": "GET", "request_type": "json"},
                                            {"key": 3, "type": "dom.setvalue", "overwrite": true, "selector": "#<?= $this->id; ?>-idlist", "value": "{0/id}", "data": ""},
                                            {"key": 4, "type": "dom.setvalue", "overwrite": true, "selector": "#<?= $this->id; ?>-taglist", "value": "<span id=\"<?= $this->id; ?>-taglist-{0/id}\" class=\"tag red\" data-id=\"{0/id}\"><i class=\"g-icon\">close</i> {0/name}</span>", "data": ""},
                                            {"key": 5, "type": "dom.setvalue", "overwrite": true, "selector": "#<?= $this->id; ?>", "value": "", "data": ""}
                                        ]
                                    }
                                ]'>
                                <datalist id="<?= $this->id; ?>-datalist"></datalist>
                                <input name="datalist-list" type="hidden" id="<?= $this->id; ?>-idlist">
                            </span>
                        </div>
                        <div class="ipt-second"><button><?= $this->getHtml('Add', '0', '0'); ?></button></div>
                    </div>
                    <div class="box taglist" id="<?= $this->id; ?>-taglist" data-action='[
                        {
                            "key": 1, "listener": "click", "selector": "#<?= $this->id; ?>-taglist span fa", "action": [
                                {"key": 1, "type": "dom.getvalue", "base": "self"},
                                {"key": 2, "type": "dom.removevalue", "selector": "#<?= $this->id; ?>-idlist", "data": ""},
                                {"key": 3, "type": "dom.remove", "base": "self"}
                            ]
                        }
                    ]'></div>
                </div>

                <div class="form-group">
                    <label><?= $this->getHtml('Category', 'Admin', 'Backend'); ?></label>
                    <select>
                        <option>All
                        <option>...
                    </select>
                </div>

                <input type="hidden" name="element" value="board-id">

                <div class="form-group">
                    <label><?= $this->getHtml('Component', 'Admin', 'Backend'); ?></label>
                    <select>
                        <option selected>Own
                        <option>All
                        <option>...
                    </select>
                </div>

                <div class="form-group">
                    <label><?= $this->getHtml('Permission'); ?></label>
                    <div>
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

                    <div class="form-group">
                    <label><?= $this->getHtml('OwnPermission'); ?></label>
                    <div>
                        <span class="checkbox">
                            <label class="checkbox" for="iOwnPermissionCreate">
                                <input id="iOwnPermissionCreate" type="checkbox" name="ownpermissioncreate" value="<?= PermissionType::CREATE; ?>" data-tpl-text="/perm/c" data-tpl-value="/perm/c">
                                <span class="checkmark"></span>
                                <?= $this->getHtml('Create'); ?>
                            </label>
                        </span>

                        <span class="checkbox">
                            <label class="checkbox" for="iOwnPermissionRead">
                                <input id="iOwnPermissionRead" type="checkbox" name="ownpermissionread" value="<?= PermissionType::READ; ?>" data-tpl-text="/perm/r" data-tpl-value="/perm/r">
                                <span class="checkmark"></span>
                                <?= $this->getHtml('Read'); ?>
                            </label>
                        </span>

                        <span class="checkbox">
                            <label class="checkbox" for="iOwnPermissionUpdate">
                                <input id="iOwnPermissionUpdate" type="checkbox" name="ownpermissionupdate" value="<?= PermissionType::MODIFY; ?>" data-tpl-text="/perm/u" data-tpl-value="/perm/u">
                                <span class="checkmark"></span>
                                <?= $this->getHtml('Update'); ?>
                            </label>
                        </span>

                        <span class="checkbox">
                            <label class="checkbox" for="iOwnPermissionDelete">
                                <input id="iOwnPermissionDelete" type="checkbox" name="ownpermissiondelete" value="<?= PermissionType::DELETE; ?>" data-tpl-text="/perm/d" data-tpl-value="/perm/d">
                                <span class="checkmark"></span>
                                <?= $this->getHtml('Delete'); ?>
                            </label>
                        </span>

                        <span class="checkbox">
                            <label class="checkbox" for="iOwnPermissionPermission">
                                <input id="iOwnPermissionPermission" type="checkbox" name="ownpermissionpermission" value="<?= PermissionType::PERMISSION; ?>" data-tpl-text="/perm/p" data-tpl-value="/perm/p">
                                <span class="checkmark"></span>
                                <?= $this->getHtml('Permission'); ?>
                            </label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="portlet-foot">
                <input type="Submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
            </div>
        </section>
    </div>

    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Permissions', 'Admin', 'Backend'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="attributeTable" class="default sticky"
                data-tag="form"
                data-ui-element="tr"
                data-add-tpl=".oms-add-tpl-attribute"
                data-update-form="attributeForm">
                <thead>
                    <tr>
                        <td>
                        <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <td><?= $this->getHtml('Account', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Category', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Element', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                <tbody>
                    <template class="oms-add-tpl-attribute">
                        <tr data-id="" draggable="false">
                            <td>
                                <i class="g-icon btn update-form">settings</i>
                                <input id="attributeTable-remove-0" type="checkbox" class="vh">
                                <label for="attributeTable-remove-0" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                <span class="checked-visibility">
                                    <label for="attributeTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                    <label for="attributeTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                </span>
                            <td data-tpl-text="/id" data-tpl-value="/id"></td>
                            <td data-tpl-text="/type" data-tpl-value="/type" data-value=""></td>
                            <td data-tpl-text="/value" data-tpl-value="/value"></td>
                            <td data-tpl-text="/unit" data-tpl-value="/unit"></td>
                        </tr>
                    </template>
                    <?php $c = 0;
                    foreach ($permissions as $key => $value) : ++$c; ?>
                        <tr data-id="<?= $value->id; ?>">
                            <td>
                                <i class="g-icon btn update-form">settings</i>
                                <?php if (!$value->type->isRequired) : ?>
                                <input id="attributeTable-remove-<?= $value->id; ?>" type="checkbox" class="vh">
                                <label for="attributeTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                <span class="checked-visibility">
                                    <label for="attributeTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                    <label for="attributeTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                </span>
                                <?php endif; ?>
                            <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                            <td data-tpl-text="/type" data-tpl-value="/type" data-value="<?= $value->type->id; ?>"><?= $this->printHtml($value->type->getL11n()); ?>
                            <td data-tpl-text="/value" data-tpl-value="/value"><?= $value->value->getValue() instanceof \DateTime ? $value->value->getValue()->format('Y-m-d') : $this->printHtml((string) $value->value->getValue()); ?>
                            <td data-tpl-text="/unit" data-tpl-value="/unit" data-value="<?= $value->value->unit; ?>"><?= $this->printHtml($value->value->unit); ?>
                    <?php endforeach; ?>
                    <?php if ($c === 0) : ?>
                    <tr>
                        <td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                    <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>