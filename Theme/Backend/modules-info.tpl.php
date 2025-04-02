<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Admin\Models\ModuleStatusUpdateType;
use phpOMS\Module\ModuleInfo;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */
$modules   = $this->data['modules'];
$active    = $this->data['active'];
$installed = $this->data['installed'];
$id        = $this->data['id'];

$module = $modules[$id] ?? new ModuleInfo('');

if (isset($installed[$id])) {
    echo $this->data['nav']->render();
}

// @todo If no id is specified in the url this page looks horrible. Either clean up or return 404 page or something similar.
?>

<div class="row">
    <div class="col-xs-12 col-md-6 col-lg-4">
        <section class="portlet">
            <div class="portlet-head"><?= $this->printHtml($module->getExternalName()); ?></div>

            <div class="portlet-body">
                <table class="list wf-100">
                    <tbody>
                    <tr>
                        <td><?= $this->getHtml('Name'); ?>
                        <td><?= $this->printHtml($module->getExternalName()); ?>
                    <tr>
                        <td><?= $this->getHtml('Version'); ?>
                        <td><?= $this->printHtml($module->getVersion()); ?>
                    <tr>
                        <td><?= $this->getHtml('CreatedBy'); ?>
                        <td><?= $this->printHtml($module->get()['creator']['name'] ?? ''); ?>
                    <tr>
                        <td><?= $this->getHtml('Website'); ?>
                        <td><?= $this->printHtml($module->get()['creator']['website'] ?? ''); ?>
                </table>
            </div>
            <div class="portlet-foot">
                <?php if (isset($active[$id])) : ?>
                    <form id="fModuleDeactivate" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id . '&csrf={$CSRF}'); ?>" method="POST"
                                data-redirect="<?= UriFactory::build('{%}'); ?>">
                        <button id="fModuleDeactivateButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::DEACTIVATE; ?>"><?= $this->getHtml('Deactivate'); ?></button>
                    </form>
                <?php elseif (isset($installed[$id])) : ?>
                    <div class="ipt-wrap">
                        <div class="ipt-first">
                            <form id="fModuleUninstall" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id . '&csrf={$CSRF}'); ?>" method="POST"
                                data-redirect="<?= UriFactory::build('{%}'); ?>">
                                <button id="fModuleUninstallButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::UNINSTALL; ?>"><?= $this->getHtml('Uninstall'); ?></button>
                            </form>
                        </div>
                        <div class="ipt-second">
                            <form id="fModuleActivate" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id . '&csrf={$CSRF}'); ?>" method="POST"
                                data-redirect="<?= UriFactory::build('{%}'); ?>">
                                <button id="fModuleActivateButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::ACTIVATE; ?>"><?= $this->getHtml('Activate'); ?></button>
                            </form>
                        </div>
                    </div>
                <?php elseif (isset($module)) : ?>
                    <div class="ipt-wrap">
                        <div class="ipt-first">
                            <form id="fModuleInstall" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id . '&csrf={$CSRF}'); ?>"
                                method="POST"
                                data-redirect="<?= UriFactory::build('{%}'); ?>">
                                <button id="fModuleInstallButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::INSTALL; ?>">
                                    <?= $this->getHtml('Install'); ?>
                                </button>
                            </form>
                        </div>
                        <div class="ipt-second">
                            <form id="fModuleDelete" action="<?= UriFactory::build('{/api}admin/module/status?module=' . $id . '&csrf={$CSRF}'); ?>" method="POST"
                                data-redirect="<?= UriFactory::build('{%}'); ?>">
                                <button id="fModuleDeleteButton" name="status" type="submit" value="<?= ModuleStatusUpdateType::DELETE; ?>"><?= $this->getHtml('Delete'); ?></button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php if (!empty($this->data['introduction'])) : ?>
    <div class="col-xs-12 col-md-8">
        <section class="portlet">
            <div class="portlet-body">
                <article><?= $this->data['introduction']; ?></article>
            </div>
        </section>

        <?php if (!empty($this->data['info']) && !empty($this->data['info']['dependencies'])) : ?>
            <section class="portlet">
                <div class="portlet-head"><?= $this->getHtml('Dependencies'); ?></div>
                <div class="portlet-body">
                    <ul>
                        <?php foreach ($this->data['info']['dependencies'] as $name => $version) :
                            $length = \strlen($name);
                            $result = '';

                            for ($i = 0; $i < $length; ++$i) {
                                if ($i > 0 && \ctype_upper($name[$i]) && \ctype_lower($name[$i - 1])) {
                                    $result .= ' ';
                                }

                                $result .= $name[$i];
                            }
                        ?>
                        <li><a class="content" href="<?= UriFactory::build('{%}?id=' . $name); ?>"><?= $this->printHtml($result); ?></a>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($this->data['info']) && !empty($this->data['info']['providing'])) : ?>
            <section class="portlet">
                <div class="portlet-head"><?= $this->getHtml('Providing'); ?></div>
                <div class="portlet-body">
                    <ul>
                        <?php foreach ($this->data['info']['providing'] as $name => $version) :
                            $length = \strlen($name);
                            $result = '';

                            for ($i = 0; $i < $length; ++$i) {
                                if ($i > 0 && \ctype_upper($name[$i]) && \ctype_lower($name[$i - 1])) {
                                    $result .= ' ';
                                }

                                $result .= $name[$i];
                            }
                        ?>
                        <li><a class="content" href="<?= UriFactory::build('{%}?id=' . $name); ?>"><?= $this->printHtml($result); ?></a>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
