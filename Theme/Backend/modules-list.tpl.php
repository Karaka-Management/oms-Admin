<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    Modules\Admin\Template\Backend
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */

/**
 * @var \phpOMS\Views\View $this
 */

$modules   = $this->app->moduleManager->getAllModules();
$active    = $this->app->moduleManager->getActiveModules();
$installed = $this->app->moduleManager->getInstalledModules();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box wf-100">
            <table class="table red">
                <caption><?= $this->getHtml('Modules') ?></caption>
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', 0, 0); ?>
                    <td class="wf-100"><?= $this->getHtml('Name') ?>
                    <td><?= $this->getHtml('Version') ?>
                    <td><?= $this->getHtml('Status') ?>
                        <tfoot>
                <tr>
                    <td colspan="4">
                        <tbody>
                        <?php $count = 0; foreach ($modules as $key => $module) : $count++;
                        $url = \phpOMS\Uri\UriFactory::build('/{/lang}/backend/admin/module/settings?{?}&id=' . $module['name']['internal']); ?>
                <tr data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', 0, 0) ?>"><a href="<?= $url; ?>"><?= $this->printHtml($module['name']['id']); ?></a>
                    <td data-label="<?= $this->getHtml('Name') ?>"><a href="<?= $url; ?>"><?= $this->printHtml($module['name']['external']); ?></a>
                    <td data-label="<?= $this->getHtml('Version') ?>"><a href="<?= $url; ?>"><?= $this->printHtml($module['version']); ?></a>
                    <td data-label="<?= $this->getHtml('Status') ?>"><a href="<?= $url; ?>"><?php if (isset($active[$module['name']['internal']]))
                            echo \mb_strtolower($this->getHtml('Active'));
                        elseif (isset($installed[$module['name']['internal']]))
                            echo \mb_strtolower($this->getHtml('Inactive'));
                        else
                            echo \mb_strtolower($this->getHtml('Available')); ?></a>
                        <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="4" class="empty"><?= $this->getHtml('Empty', 0, 0); ?>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
