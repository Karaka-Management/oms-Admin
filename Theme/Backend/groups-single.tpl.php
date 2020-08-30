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

use phpOMS\Account\GroupStatus;
use phpOMS\Account\PermissionOwner;
use phpOMS\Account\PermissionType;
use phpOMS\Message\Http\HttpHeader;
use phpOMS\Uri\UriFactory;

/**
 * @todo Orange-Management/Modules#122
 *  Add group account removal
 *  In front of every account there should be a red x which allows to remove an account from the group.
 *
 * @todo Orange-Management/Modules#127
 *  Add account group removal
 *  Add red x in front of every group which removes the group for this account.
 *
 * @todo Orange-Management/Modules#124
 *  Add group permission removal
 *  Add a red x in front of every permission which removes the permission from the group.
 *
 * @todo Orange-Management/Modules#123
 *  Add group permission adjustment
 *  In front of every permission there should be a cogs sign for modifying permissions.
 *  Clicking on it will put the info into the form below and change the button from add to update.
 *  At the same time there should be a clear button which allows to clear all these info and show the add button again.
 *  Alternatively the modification could be done inline in the permission list.
 *
 * @todo Orange-Management/Modules#125
 *  Add group log tab for audits
 *  A new tab should be added where all changes can be audited if required.
 *  This tab should also show all the places where the group is mentioned.
 */

/**
 * @var \phpOMS\Views\View $this
 */
$group       = $this->getData('group');
$permissions = $this->getData('permissions');
$accounts    = $group->getAccounts();
$audits      = $this->getData('auditlogs') ?? [];

$previous = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? '{/prefix}admin/group/settings?id={?id}#{\#}' : '{/prefix}admin/group/settings?{?}&audit=' . \reset($audits)->getId() . '&ptype=p#{\#}';
$next     = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? '{/prefix}admin/group/settings?id={?id}#{\#}' : '{/prefix}admin/group/settings?{?}&audit=' . \end($audits)->getId() . '&ptype=n#{\#}';

echo $this->getData('nav')->render(); ?>

<div class="tabview tab-2">
    <div class="box wf-100 col-xs-12">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('General'); ?></label></li>
            <li><label for="c-tab-2"><?= $this->getHtml('AuditLog'); ?></label></li>
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->getUri()->getFragment() === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="portlet">
                        <form id="fGroupEdit" action="<?= UriFactory::build('{/api}admin/group'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Group'); ?></div>
                            <div class="portlet-body">
                                <label for="iGid"><?= $this->getHtml('ID', '0', '0'); ?></label>
                                <input id="iGid" name="id" type="text" value="<?= $this->printHtml($group->getId()); ?>" disabled>
                                <label for="iGname"><?= $this->getHtml('Name'); ?></label>
                                <input id="iGname" name="name" type="text" spellcheck="false" autocomplete="off" placeholder="&#xf0c0; Guest" value="<?= $this->printHtml($group->getName()); ?>">
                                <label for="iGstatus"><?= $this->getHtml('Status'); ?></label>
                                <select id="iGstatus" name="status">
                                    <?php $status = GroupStatus::getConstants(); foreach ($status as $stat) : ?>
                                    <option value="<?= $stat; ?>"<?= $stat === $group->getStatus() ? ' selected' : ''; ?>><?= $this->getHtml('GroupStatus' . $stat); ?>
                                <?php endforeach; ?>
                                    </select>
                                <?= $this->getData('editor')->render('group-editor'); ?>
                                <?= $this->getData('editor')->getData('text')->render(
                                    'group-editor',
                                    'description',
                                    'fGroupEdit',
                                    $group->getDescriptionRaw(),
                                    $group->getDescription()
                                ); ?>
                            </div>
                            <div class="portlet-foot">
                                <input id="groupSubmit" name="groupsubmit" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Accounts'); ?><i class="fa fa-download floatRight download btn"></i></div>
                        <table class="default">
                            <thead>
                                <tr>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td class="wf-100"><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                            <tbody>
                                <?php $c = 0; foreach ($accounts as $key => $value) : ++$c; $url = UriFactory::build('{/prefix}admin/account/settings?{?}&id=' . $value->getId()); ?>
                                <tr data-href="<?= $url; ?>">
                                    <td><a href="#"><i class="fa fa-times"></i></a>
                                    <td><a href="<?= $url; ?>"><?= $value->getName1(); ?></a>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                <tr><td colspan="2" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                <?php endif; ?>
                        </table>
                        <div class="portlet-foot"></div>
                    </div>

                    <div class="portlet">
                        <form id="iAddAccountToGroup" action="<?= UriFactory::build('{/api}admin/group/account'); ?>" method="put">
                            <div class="portlet-head"><?= $this->getHtml('Accounts'); ?></div>
                            <div class="portlet-body">
                                <label for="iAccount"><?= $this->getHtml('Name'); ?></label>
                                <?= $this->getData('accGrpSelector')->render('iAccount', 'group', true); ?>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Permissions'); ?><i class="fa fa-download floatRight download btn"></i></div>
                        <div style="overflow-x:auto;">
                            <table id="groupPermissions" class="default" data-table-form="fGroupAddPermission">
                                <thead>
                                    <tr>
                                        <td>
                                        <td>
                                        <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                        <td><?= $this->getHtml('Unit'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                        <td><?= $this->getHtml('App'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                        <td><?= $this->getHtml('Module'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                        <td><?= $this->getHtml('Type'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                        <td><?= $this->getHtml('Ele'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                        <td><?= $this->getHtml('Comp'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                        <td class="wf-100"><?= $this->getHtml('Perm'); ?>
                                <tbody>
                                    <template>
                                        <tr>
                                            <td><a href="#"><i class="fa fa-times"></i></a>
                                            <td><a href="#"><i class="fa fa-cogs"></i></a>
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
                                    <?php $c = 0; foreach ($permissions as $key => $value) : ++$c; $permission = $value->getPermission(); ?>
                                    <tr>
                                        <td><a href="#"><i class="fa fa-times"></i></a>
                                        <td><a href="#"><i class="fa fa-cogs"></i></a>
                                        <td><?= $value->getId(); ?>
                                        <td><?= $value->getUnit(); ?>
                                        <td><?= $value->getApp(); ?>
                                        <td><?= $value->getModule(); ?>
                                        <td><?= $value->getType(); ?>
                                        <td><?= $value->getElement(); ?>
                                        <td><?= $value->getComponent(); ?>
                                        <td>
                                            <?= (PermissionType::CREATE | $permission) === $permission ? 'C' : ''; ?>
                                            <?= (PermissionType::READ | $permission) === $permission ? 'R' : ''; ?>
                                            <?= (PermissionType::MODIFY | $permission) === $permission ? 'U' : ''; ?>
                                            <?= (PermissionType::DELETE | $permission) === $permission ? 'D' : ''; ?>
                                            <?= (PermissionType::PERMISSION | $permission) === $permission ? 'P' : ''; ?>
                                    <?php endforeach; ?>
                                    <?php if ($c === 0) : ?>
                                    <tr><td colspan="10" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                    <?php endif; ?>
                            </table>
                        </div>
                    </div>


                    <div class="portlet">
                        <form id="fGroupAddPermission" action="<?= UriFactory::build('{/api}admin/group/permission'); ?>" method="put">
                            <div class="portlet-head"><?= $this->getHtml('Permissions'); ?></div>
                            <div class="portlet-body">
                                <table class="layout wf-100">
                                    <tbody>
                                    <tr><td><label for="iPermissionUnit"><?= $this->getHtml('Unit'); ?></label>
                                    <tr><td><input id="iPermissionUnit" name="permissionunit" type="text" data-tpl-text="/unit" data-tpl-value="/unit">
                                    <tr><td><label for="iPermissionApp"><?= $this->getHtml('App'); ?></label>
                                    <tr><td><input id="iPermissionApp" name="permissionapp" type="text" data-tpl-text="/app" data-tpl-value="/app">
                                    <tr><td><label for="iPermissionModule"><?= $this->getHtml('Module'); ?></label>
                                    <tr><td><input id="iPermissionModule" name="permissionmodule" type="text" data-tpl-text="/module" data-tpl-value="/module">
                                    <tr><td><label for="iPermissionType"><?= $this->getHtml('Type'); ?></label>
                                    <tr><td><input id="iPermissionType" name="permissiontype" type="text" data-tpl-text="/type" data-tpl-value="/type">
                                    <tr><td><label for="iPermissionElement"><?= $this->getHtml('Element'); ?></label>
                                    <tr><td><input id="iPermissionElement" name="permissionelement" type="text" data-tpl-text="/ele" data-tpl-value="/ele">
                                    <tr><td><label for="iPermissionComponent"><?= $this->getHtml('Component'); ?></label>
                                    <tr><td><input id="iPermissionComponent" name="permissioncomponent" type="text" data-tpl-text="/comp" data-tpl-value="/comp">
                                    <tr><td><label><?= $this->getHtml('Permission'); ?></label>
                                    <tr><td>
                                        <span class="checkbox">
                                            <input id="iPermissionCreate" name="permissioncreate" type="checkbox" value="<?= PermissionType::CREATE; ?>" data-tpl-text="/perm/c" data-tpl-value="/perm/c">
                                            <label for="iPermissionCreate"><?= $this->getHtml('Create'); ?></label>
                                        </span>
                                        <span class="checkbox">
                                            <input id="iPermissionRead" name="permissionread" type="checkbox" value="<?= PermissionType::READ; ?>" data-tpl-text="/perm/r" data-tpl-value="/perm/r">
                                            <label for="iPermissionRead"><?= $this->getHtml('Read'); ?></label>
                                        </span>
                                        <span class="checkbox">
                                            <input id="iPermissionUpdate" name="permissionupdate" type="checkbox" value="<?= PermissionType::MODIFY; ?>" data-tpl-text="/perm/u" data-tpl-value="/perm/u">
                                            <label for="iPermissionUpdate"><?= $this->getHtml('Update'); ?></label>
                                        </span>
                                        <span class="checkbox">
                                            <input id="iPermissionDelete" name="permissiondelete" type="checkbox" value="<?= PermissionType::DELETE; ?>" data-tpl-text="/perm/d" data-tpl-value="/perm/d">
                                            <label for="iPermissionDelete"><?= $this->getHtml('Delete'); ?></label>
                                        </span>
                                        <span class="checkbox">
                                            <input id="iPermissionPermission" name="permissionpermission" type="checkbox" value="<?= PermissionType::PERMISSION; ?>" data-tpl-text="/perm/p" data-tpl-value="/perm/p">
                                            <label for="iPermissionPermission"><?= $this->getHtml('Permission'); ?></label>
                                        </span>
                                </table>
                            </div>
                            <div class="portlet-foot">
                                <input type="hidden" name="permissionref" value="<?= $this->printHtml($group->getId()); ?>">
                                <input type="hidden" name="permissionowner" value="<?= PermissionOwner::GROUP; ?>">
                                <input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->getUri()->getFragment() === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Audits', 'Auditor'); ?><i class="fa fa-download floatRight download btn"></i></div>
                        <table class="default fixed">
                            <colgroup>
                                <col style="width: 75px">
                                <col style="width: 150px">
                                <col style="width: 100px">
                                <col style="width: 75px">
                                <col>
                                <col>
                                <col>
                                <col style="width: 125px">
                                <col style="width: 75px">
                                <col style="width: 150px">
                            </colgroup>
                            <thead>
                            <tr>
                                <td><?= $this->getHtml('ID', '0', '0'); ?>
                                <td ><?= $this->getHtml('Module', 'Auditor'); ?>
                                <td ><?= $this->getHtml('Type', 'Auditor'); ?>
                                <td ><?= $this->getHtml('Subtype', 'Auditor'); ?>
                                <td ><?= $this->getHtml('Old', 'Auditor'); ?>
                                <td ><?= $this->getHtml('New', 'Auditor'); ?>
                                <td ><?= $this->getHtml('Content', 'Auditor'); ?>
                                <td ><?= $this->getHtml('By', 'Auditor'); ?>
                                <td ><?= $this->getHtml('Ref', 'Auditor'); ?>
                                <td ><?= $this->getHtml('Date', 'Auditor'); ?>
                            <tbody>
                            <?php $count = 0; foreach ($audits as $key => $audit) : ++$count;
                            $url = UriFactory::build('{/prefix}admin/audit/single?{?}&id=' . $audit->getId()); ?>
                                <tr tabindex="0" data-href="<?= $url; ?>">
                                    <td><?= $audit->getId(); ?>
                                    <td><?= $this->printHtml($audit->getModule()); ?>
                                    <td><?= $audit->getType(); ?>
                                    <td><?= $audit->getSubtype(); ?>
                                    <td><?= $this->printHtml($audit->getOld()); ?>
                                    <td><?= $this->printHtml($audit->getNew()); ?>
                                    <td><?= $this->printHtml($audit->getContent()); ?>
                                    <td><?= $this->printHtml($audit->getCreatedBy()->getName()); ?>
                                    <td><?= $this->printHtml($audit->getRef()); ?>
                                    <td><?= $audit->getCreatedAt()->format('Y-m-d H:i'); ?>
                            <?php endforeach; ?>
                            <?php if ($count === 0) : ?>
                                <tr><td colspan="9" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                            <?php endif; ?>
                        </table>
                        <div class="portlet-foot">
                            <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                            <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
