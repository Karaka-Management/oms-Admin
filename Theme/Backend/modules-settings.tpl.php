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

/**
 * @var \phpOMS\Views\View $this
 */
$settings = $this->data['settings'] ?? [];

echo $this->data['nav']->render();

if ($this->hasData('settingsTpl')
    && \is_file($this->getData('settingsTpl'))
) :
    include $this->data['settingsTpl'];
else : ?>
<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Settings'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="settingsList" class="default sticky">
                <thead>
                <tr>
                    <td>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <label for="settingsList-sort-1">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-1">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="settingsList-sort-2">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-2">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Name'); ?>
                        <label for="settingsList-sort-3">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-3">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="settingsList-sort-4">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-4">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Value'); ?>
                    <td><?= $this->getHtml('Unit'); ?>
                        <label for="settingsList-sort-5">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-5">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="settingsList-sort-6">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-6">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('App'); ?>
                        <label for="settingsList-sort-5">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-5">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="settingsList-sort-6">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-6">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Group'); ?>
                        <label for="settingsList-sort-7">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-7">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="settingsList-sort-8">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-8">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Account'); ?>
                        <label for="settingsList-sort-9">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-9">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="settingsList-sort-10">
                            <input type="radio" name="settingsList-sort" id="settingsList-sort-10">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                <tbody>
                <?php $count = 0;
                    foreach ($settings as $key => $setting) : ++$count;
                ?>
                <tr tabindex="0">
                    <td><i class="g-icon">settings</i>
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><?= $setting->id; ?>
                    <td data-label="<?= $this->getHtml('Name'); ?>">
                        <?php
                        $name = $setting->name;

                        if ($this->getData('settings_class') !== null) {
                            $name = $this->getData('settings_class')::getName($setting->name);

                            if (!\is_string($name)) {
                                $name = $setting->name;
                            }
                        }
                        ?>
                        <?= $this->printHtml($name); ?>
                    <td data-label="<?= $this->getHtml('Value'); ?>"><?= $this->printHtml($setting->content); ?>
                    <td data-label="<?= $this->getHtml('Unit'); ?>"><?= $this->printHtml((string) $setting->unit); ?>
                    <td data-label="<?= $this->getHtml('App'); ?>"><?= $this->printHtml((string) $setting->app); ?>
                    <td data-label="<?= $this->getHtml('Group'); ?>"><?= $this->printHtml($setting->group); ?>
                    <td data-label="<?= $this->getHtml('Account'); ?>"><?= $this->printHtml($setting->account); ?>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
