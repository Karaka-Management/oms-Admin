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

use Modules\Admin\Models\ModuleStatusUpdateType;
use phpOMS\Message\Http\HttpHeader;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */
$modules   = $this->getData('modules');
$active    = $this->getData('active');
$installed = $this->getData('installed');
$id        = $this->getData('id');
$audits    = $this->getData('auditlogs') ?? [];

$nav = $this->getData('nav');

$previous = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? '{/prefix}admin/module/settings?id={?id}#{\#}' : '{/prefix}admin/module/settings?{?}&audit=' . \reset($audits)->getId() . '&ptype=p#{\#}';
$next     = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? '{/prefix}admin/module/settings?id={?id}#{\#}' : '{/prefix}admin/module/settings?{?}&audit=' . \end($audits)->getId() . '&ptype=n#{\#}';

if ($nav !== null) {
    echo $this->getData('nav')->render();
}
?>
<div class="tabview tab-2">
    <div class="box wf-100 col-xs-12">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('General'); ?></label></li>
            <li><label for="c-tab-2"><?= $this->getHtml('AuditLog'); ?></label></li>
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->getUri()->getFragment() === 'c-tab-1' ? ' checked' : '' ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->printHtml($modules[$id]['name']['external']); ?></div>

                        <div class="portlet-body">
                            <table class="list wf-100">
                                <tbody>
                                <tr>
                                    <td><?= $this->getHtml('Name'); ?>
                                    <td><?= $this->printHtml($modules[$id]['name']['external']); ?>
                                <tr>
                                    <td><?= $this->getHtml('Version'); ?>
                                    <td><?= $this->printHtml($modules[$id]['version']); ?>
                                <tr>
                                    <td><?= $this->getHtml('CreatedBy'); ?>
                                    <td><?= $this->printHtml($modules[$id]['creator']['name']); ?>
                                <tr>
                                    <td><?= $this->getHtml('Website'); ?>
                                    <td><?= $this->printHtml($modules[$id]['creator']['website']); ?>
                                <tr>
                                    <td><?= $this->getHtml('Description'); ?>
                                    <td><?= $this->printHtml($modules[$id]['description']); ?>
                            </table>
                        </div>
                        <div class="portlet-foot">
                            <?php if (isset($active[$id])) : ?>
                                <form id="fModuleDeactivate" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id); ?>" method="POST">
                                    <button id="fModuleDeactivateButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::DEACTIVATE ?>"><?= $this->getHtml('Deactivate'); ?></button>
                                </form>
                            <?php elseif (isset($installed[$id])) : ?>
                                <div class="ipt-wrap">
                                    <div class="ipt-first">
                                        <form id="fModuleUninstall" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id); ?>" method="POST">
                                            <button id="fModuleUninstallButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::UNINSTALL ?>"><?= $this->getHtml('Uninstall'); ?></button>
                                        </form>
                                    </div>
                                    <div class="ipt-second">
                                        <form id="fModuleActivate" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id); ?>" method="POST">
                                            <button id="fModuleActivateButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::ACTIVATE ?>"><?= $this->getHtml('Activate'); ?></button>
                                        </form>
                                    </div>
                                </div>
                            <?php elseif (isset($modules[$id])) : ?>
                                <div class="ipt-wrap">
                                    <div class="ipt-first">
                                        <form id="fModuleInstall" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id); ?>" method="POST">
                                            <button id="fModuleInstallButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::INSTALL ?>"><?= $this->getHtml('Install'); ?></button>
                                        </form>
                                    </div>
                                    <div class="ipt-second">
                                        <form id="fModuleDelete" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id); ?>" method="POST">
                                            <button id="fModuleDeleteButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::DELETE ?>"><?= $this->getHtml('Delete'); ?></button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Settings'); ?></div>

                        <div class="portlet-body">

                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="portlet">
                        <table id="iModuleGroupList" class="default">
                            <caption><?= $this->getHtml('Permissions') ?><i class="fa fa-download floatRight download btn"></i></caption>
                            <thead>
                                <tr>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td>Type<i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td class="wf-100"><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                            <tbody>
                                <?php $c = 0; $groupPermissions = $this->getData('groupPermissions');
                                foreach ($groupPermissions as $key => $value) : ++$c;
                                $url = UriFactory::build('{/prefix}admin/group/settings?{?}&id=' . $value->getId()); ?>
                                <tr data-href="<?= $url; ?>">
                                    <td><a href="<?= $url; ?>"><i class="fa fa-times"></i></a>
                                    <td><a href="<?= $url; ?>">Group</a>
                                    <td><a href="<?= $url; ?>"><?= $value->getName(); ?></a>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->getUri()->getFragment() === 'c-tab-2' ? ' checked' : '' ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Audits', 'Auditor') ?><i class="fa fa-download floatRight download btn"></i></div>
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
                                <td ><?= $this->getHtml('Module') ?>
                                <td ><?= $this->getHtml('Type') ?>
                                <td ><?= $this->getHtml('Subtype') ?>
                                <td ><?= $this->getHtml('Old') ?>
                                <td ><?= $this->getHtml('New') ?>
                                <td ><?= $this->getHtml('Content') ?>
                                <td ><?= $this->getHtml('By') ?>
                                <td ><?= $this->getHtml('Ref') ?>
                                <td ><?= $this->getHtml('Date') ?>
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
