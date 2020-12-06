<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use phpOMS\Module\ModuleStatus;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */

$modules   = $this->getData('modules') ?? [];
$active    = $this->getData('active') ?? [];
$isntalled = $this->getData('isntalled') ?? [];
?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Modules'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <table id="moduleList" class="default">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td><?= $this->getHtml('Version'); ?>
                    <td><?= $this->getHtml('Status'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                <tbody>
                    <?php $count = 0;
                        foreach ($modules as $key => $module) : ++$count;
                            $url = UriFactory::build('{/prefix}admin/module/settings?{?}&id=' . $module->getInternalName());

                            if (isset($active[$module->getInternalName()])) {
                                $status = ModuleStatus::ACTIVE;
                            } elseif (isset($installed[$module->getInternalName()])) {
                                $status = ModuleStatus::INACTIVE;
                            } else {
                                $status = ModuleStatus::AVAILABLE;
                            }
                    ?>
                <tr tabindex="0" data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $module->getId(); ?></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($module->getExternalName()); ?></a>
                    <td data-label="<?= $this->getHtml('Version'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($module->getVersion()); ?></a>
                    <td data-label="<?= $this->getHtml('Status'); ?>">
                        <span class="tag">
                            <a href="<?= $url; ?>">
                                <?php if ($status === ModuleStatus::ACTIVE)
                                    echo \mb_strtolower($this->getHtml('Active'));
                                elseif ($status === ModuleStatus::INACTIVE)
                                    echo \mb_strtolower($this->getHtml('Inactive'));
                                else
                                    echo \mb_strtolower($this->getHtml('Available')); ?>
                            </a>
                            <?php endforeach; ?>
                        </span>
                <?php if ($count === 0) : ?>
                <tr><td colspan="4" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            <div class="portlet-foot"></div>
        </div>
    </div>
</div>
