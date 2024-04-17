<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Module\ModuleStatus;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */

$modules   = $this->data['modules'] ?? [];
$active    = $this->data['active'] ?? [];
$installed = $this->data['installed'] ?? [];

$tableView            = $this->data['tableView'];
$tableView->id        = 'moduleList';
$tableView->baseUri   = 'admin/module/list';
$tableView->exportUri = '{/api}admin/module/list/export?csrf={$CSRF}';
$tableView->setObjects($modules);
?>
<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head">
                <?= $tableView->renderTitle(
                    $this->getHtml('Modules')
                ); ?>
            </div>
            <div class="slider">
            <table id="<?= $tableView->id; ?>" class="default sticky">
                <thead>
                <tr>
                    <td><?= $tableView->renderHeaderElement(
                        'id',
                        $this->getHtml('ID', '0', '0'),
                        'number'
                    ); ?>
                    <td class="wf-100"><?= $tableView->renderHeaderElement(
                            'name',
                            $this->getHtml('Name'),
                            'text'
                        ); ?>
                    <td><?= $tableView->renderHeaderElement(
                            'version',
                            $this->getHtml('Version'),
                            'text',
                            [],
                            false,
                            false,
                            false
                        ); ?>
                    <td><?= $tableView->renderHeaderElement(
                            'action',
                            $this->getHtml('Status'),
                            'select',
                            [
                                'active'    => $this->getHtml('active'),
                                'available' => $this->getHtml('available'),
                                'inactive'  => $this->getHtml('inactive'),
                            ],
                            false // don't render sort
                        ); ?>
                <tbody>
                    <?php $count = 0;
                        foreach ($modules as $key => $module) : ++$count;
                            $url = UriFactory::build('{/base}/admin/module/info?{?}&id=' . $module->getInternalName());

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
                        <span class="tag module-status-<?= $status; ?>">
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
            </div>
        </section>
    </div>
</div>
