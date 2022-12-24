<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Theme
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Account\GroupStatus;
use phpOMS\Account\PermissionOwner;
use phpOMS\Account\PermissionType;
use phpOMS\Message\Http\HttpHeader;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */
$group       = $this->getData('group');
$permissions = $this->getData('permissions');
$accounts    = $group->getAccounts();
$audits      = $this->getData('auditlogs') ?? [];

$previous = empty($audits)
    ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/group/settings?id={?id}#{\#}'
    : 'admin/group/settings?{?}&audit=' . \reset($audits)->getId() . '&ptype=p#{\#}';
$next     = empty($audits)
    ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/group/settings?id={?id}#{\#}'
    : 'admin/group/settings?{?}&audit=' . \end($audits)->getId() . '&ptype=n#{\#}';

echo $this->getData('nav')->render(); ?>

<div id="igroup-tabs" class="tabview tab-2 url-rewrite">
    <div class="box wf-100 col-xs-12">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('General'); ?></label></li>
            <li><label for="c-tab-2"><?= $this->getHtml('Accounts'); ?></label></li>
            <li><label for="c-tab-3"><?= $this->getHtml('Permissions'); ?></label></li>
            <li><label for="c-tab-4"><?= $this->getHtml('AuditLog'); ?></label></li>
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= empty($this->request->uri->fragment) || $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="portlet">
                        <form id="fGroupEdit" action="<?= UriFactory::build('{/api}admin/group'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Group'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iGid"><?= $this->getHtml('ID', '0', '0'); ?></label>
                                    <input id="iGid" name="id" type="text" value="<?= $group->getId(); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="iGname"><?= $this->getHtml('Name'); ?></label>
                                    <input id="iGname" name="name" type="text" spellcheck="false" autocomplete="off" placeholder="&#xf0c0; Guest" value="<?= $this->printHtml($group->name); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="iGstatus"><?= $this->getHtml('Status'); ?></label>
                                    <select id="iGstatus" name="status">
                                        <?php $status = GroupStatus::getConstants(); foreach ($status as $stat) : ?>
                                        <option value="<?= $stat; ?>"<?= $stat === $group->getStatus() ? ' selected' : ''; ?>><?= $this->getHtml('GroupStatus' . $stat); ?>
                                    <?php endforeach; ?>
                                    </select>
                                </div>
                                <?= $this->getData('editor')->render('group-editor'); ?>
                                <?= $this->getData('editor')->getData('text')->render(
                                    'group-editor',
                                    'description',
                                    'fGroupEdit',
                                    $group->descriptionRaw,
                                    $group->description
                                ); ?>
                            </div>
                            <div class="portlet-foot">
                                <input id="groupSubmit" name="groupsubmit" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
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
                        <form id="iAddAccountToGroup" action="<?= UriFactory::build('{/api}admin/group/account'); ?>" method="put">
                            <div class="portlet-head"><?= $this->getHtml('Accounts'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iAccount"><?= $this->getHtml('Name'); ?></label>
                                    <?= $this->getData('accGrpSelector')->render('iAccount', 'group', true); ?>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Accounts'); ?><i class="fa fa-download floatRight download btn"></i></div>
                        <table class="default">
                            <thead>
                                <tr>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td class="wf-100"><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                            <tbody>
                                <?php $c = 0; foreach ($accounts as $key => $value) : ++$c; $url = UriFactory::build('{/lang}/{/app}/admin/account/settings?{?}&id=' . $value->getId()); ?>
                                <tr data-href="<?= $url; ?>">
                                    <td><a href="#"><i class="fa fa-times"></i></a>
                                    <td><a href="<?= $url; ?>"><?= $value->name1; ?> <?= $value->name2; ?></a>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                    <tr><td colspan="2" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                <?php endif; ?>
                        </table>
                        <div class="portlet-foot"></div>
                    </div>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-3" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-3' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                <div class="portlet">
                        <form id="fGroupAddPermission" action="<?= UriFactory::build('{/api}admin/group/permission'); ?>" method="put">
                            <div class="portlet-head"><?= $this->getHtml('Permissions'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iPermissionUnit"><?= $this->getHtml('Unit'); ?></label>
                                    <input id="iPermissionUnit" name="permissionunit" type="text" data-tpl-text="/unit" data-tpl-value="/unit">
                                </div>
                                <div class="form-group">
                                    <label for="iPermissionApp"><?= $this->getHtml('App'); ?></label>
                                    <input id="iPermissionApp" name="permissionapp" type="text" data-tpl-text="/app" data-tpl-value="/app">
                                </div>
                                <div class="form-group">
                                    <label for="iPermissionModule"><?= $this->getHtml('Module'); ?></label>
                                    <input id="iPermissionModule" name="permissionmodule" type="text" data-tpl-text="/module" data-tpl-value="/module">
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
                                <input type="hidden" name="permissionref" value="<?= $group->getId(); ?>">
                                <input type="hidden" name="permissionowner" value="<?= PermissionOwner::GROUP; ?>">
                                <input type="submit" class="cancel hidden" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
                                <input type="submit" class="update hidden" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                                <input type="submit" class="save" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Permissions'); ?><i class="fa fa-download floatRight download btn"></i></div>
                        <div class="slider">
                            <table id="groupPermissions" class="default"
                                data-update-content="tbody"
                                data-update-element="tr"
                                data-tag="form"
                                data-update-form="fGroupAddPermission"
                                data-table-form="fGroupAddPermission">
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
                                    <?php $c = 0;
                                    foreach ($permissions as $key => $value) : ++$c;
                                        $permission = $value->getPermission(); ?>
                                    <tr>
                                        <td><a href="#"><i class="fa fa-times"></i></a>
                                        <td><i class="fa fa-cogs update btn"></i>
                                        <td><?= $value->getId(); ?>
                                        <td data-tpl-text="/unit" data-tpl-value="/unit"><?= $value->getUnit(); ?>
                                        <td data-tpl-text="/app" data-tpl-value="/app"><?= $value->getApp(); ?>
                                        <td data-tpl-text="/module" data-tpl-value="/module"><?= $value->getModule(); ?>
                                        <td data-tpl-text="/type" data-tpl-value="/type"><?= $value->getCategory(); ?>
                                        <td data-tpl-text="/ele" data-tpl-value="/ele"><?= $value->getElement(); ?>
                                        <td data-tpl-text="/comp" data-tpl-value="/comp"><?= $value->getComponent(); ?>
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
                                    <tr><td colspan="10" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                    <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-4" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-4' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Audits', 'Auditor'); ?><i class="fa fa-download floatRight download btn"></i></div>
                        <table class="default fixed">
                            <thead>
                            <tr>
                                <td><?= $this->getHtml('ID', '0', '0'); ?>
                                <td><?= $this->getHtml('Module', 'Auditor'); ?>
                                <td><?= $this->getHtml('Type', 'Auditor'); ?>
                                <td><?= $this->getHtml('Trigger', 'Auditor'); ?>
                                <td><?= $this->getHtml('By', 'Auditor'); ?>
                                <td><?= $this->getHtml('Ref', 'Auditor'); ?>
                                <td><?= $this->getHtml('Date', 'Auditor'); ?>
                            <tbody>
                            <?php $count = 0; foreach ($audits as $key => $audit) : ++$count;
                            $url         = UriFactory::build('{/lang}/{/app}/admin/audit/single?{?}&id=' . $audit->getId()); ?>
                                <tr tabindex="0" data-href="<?= $url; ?>">
                                    <td><?= $audit->getId(); ?>
                                    <td><?= $this->printHtml($audit->module); ?>
                                    <td><?= $audit->getType(); ?>
                                    <td><?= $this->printHtml($audit->trigger); ?>
                                    <td><?= $this->printHtml($audit->createdBy->login); ?>
                                    <td><?= $this->printHtml($audit->ref); ?>
                                    <td><?= $audit->createdAt->format('Y-m-d H:i'); ?>
                            <?php endforeach; ?>
                            <?php if ($count === 0) : ?>
                                <tr><td colspan="7" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
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
