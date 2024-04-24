<?php

use phpOMS\Uri\UriFactory;
?>
<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Settings'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="settingsList" class="default sticky"
                data-uri="<?= UriFactory::build('{/api}admin/settings?csrf={$CSRF}'); ?>"
                data-tag="form"
                data-ui-container="tbody"
                data-ui-element="tr"
                data-update-tpl="#settingsList tbody .oms-update-tpl-setting">
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
                    <template class="oms-update-tpl-setting">
                        <tr data-id="" draggable="false">
                            <td><i class="g-icon btn save-form">check</i>
                                <i class="g-icon btn cancel-form">close</i>
                            <td data-tpl-text="/id" data-tpl-value="/id">
                            <td data-tpl-text="/name" data-tpl-value="/name">
                            <td><input name="content" type="text" data-tpl-text="/content" data-tpl-value="/content">
                            <td data-tpl-text="/unit" data-tpl-value="/unit">
                            <td data-tpl-text="/app" data-tpl-value="/app">
                            <td data-tpl-text="/group" data-tpl-value="/group">
                            <td data-tpl-text="/account" data-tpl-value="/account">
                    </template>
                    <template class="oms-add-tpl-setting">
                        <tr data-id="" draggable="false">
                            <td><i class="g-icon btn save-form">settings</i>
                            <td data-tpl-text="/id" data-tpl-value="/id">
                            <td data-tpl-text="/name" data-tpl-value="/name">
                            <td><input name="content" type="text" data-tpl-text="/content" data-tpl-value="/content">
                            <td data-tpl-text="/unit" data-tpl-value="/unit">
                            <td data-tpl-text="/app" data-tpl-value="/app">
                            <td data-tpl-text="/group" data-tpl-value="/group">
                            <td data-tpl-text="/account" data-tpl-value="/account">
                    </template>
                <?php $count = 0;
                    foreach ($settings as $key => $setting) : ++$count;
                ?>
                <tr tabindex="0" data-id="<?= $setting->id; ?>">
                    <td><i class="g-icon btn update-form">settings</i>
                    <td data-tpl-text="/id" data-tpl-value="/id" data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><?= $setting->id; ?>
                    <td data-tpl-text="/name" data-tpl-value="/name" data-label="<?= $this->getHtml('Name'); ?>">
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
                    <td data-tpl-text="/content" data-tpl-value="/content" data-label="<?= $this->getHtml('Value'); ?>"><?= $this->printHtml($setting->content); ?>
                    <td data-tpl-text="/unit" data-tpl-value="/unit" data-label="<?= $this->getHtml('Unit'); ?>"><?= $this->printHtml((string) $setting->unit); ?>
                    <td data-tpl-text="/app" data-tpl-value="/app" data-label="<?= $this->getHtml('App'); ?>"><?= $this->printHtml((string) $setting->app); ?>
                    <td data-tpl-text="/group" data-tpl-value="/group" data-label="<?= $this->getHtml('Group'); ?>"><?= $this->printHtml($setting->group); ?>
                    <td data-tpl-text="/account" data-tpl-value="/account" data-label="<?= $this->getHtml('Account'); ?>"><?= $this->printHtml($setting->account); ?>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>