<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Attribute
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Localization\ISO639Enum;
use phpOMS\Uri\UriFactory;

$account = $this->accounts;
$categories = ISO639Enum::getConstants();

?>

<div class="col-xs-12 col-md-6">
    <section class="portlet">
        <form id="attributeForm" action="<?= UriFactory::build($this->apiUri); ?>" method="post"
            data-ui-container="#attributeTable tbody"
            data-add-form="attributeForm"
            data-add-tpl="#attributeTable tbody .oms-add-tpl-attribute">
            <div class="portlet-head"><?= $this->getHtml('Attribute', 'Attribute', 'Backend'); ?></div>
            <div class="portlet-body">
                <div class="form-group">
                    <label for="iAttributeId"><?= $this->getHtml('ID', '0', '0'); ?></label>
                    <input type="text" id="iAttributeId" name="id" data-tpl-text="/id" data-tpl-value="/id" disabled>
                </div>

                <!--
                <div class="form-group">
                    <label for="iAttributesLanguage"><?= $this->getHtml('Language', 'Attribute', 'Backend'); ?></label>
                    <select id="iAttributesLanguage" name="language" data-tpl-text="/language" data-tpl-value="/language">
                            <option value="">
                        <?php foreach ($categories as $code => $category) : ?>
                            <option value="<?= $this->printHtml($code); ?>" <?= $this->printHtml(\strtolower($code) === $l11n->language ? ' selected' : ''); ?>><?= $this->printHtml($category); ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                -->

                <div class="form-group">
                    <label for="iAttributesType"><?= $this->getHtml('Type', 'Attribute', 'Backend'); ?></label>
                    <select id="iAttributesType" name="type" data-tpl-text="/type" data-tpl-value="/type">
                        <?php
                        foreach ($types as $type) : ?>
                            <option value="<?= $type->id; ?>"><?= $this->printHtml($type->getL11n()); ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="iAttributesUnit"><?= $this->getHtml('Unit', 'Attribute', 'Backend'); ?></label>
                    <select id="iAttributesUnit" name="unit" data-tpl-text="/unit" data-tpl-value="/unit">
                        <option value="">
                        <?php
                        foreach ($units as $unit) : ?>
                            <option value="<?= $unit->id; ?>"><?= $this->printHtml($unit->name); ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="iAttributeValue"><?= $this->getHtml('Value', 'Attribute', 'Backend'); ?></label>
                    <pre class="textarea contenteditable" id="iAttributeValue" data-name="value" data-tpl-value="/value" contenteditable></pre>
                </div>
            </div>
            <div class="portlet-foot">
                <input id="bAttributeAdd" formmethod="put" type="submit" class="add-form" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                <input id="bAttributeSave" formmethod="post" type="submit" class="save-form hidden button save" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                <input type="submit" class="cancel-form hidden button close" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
            </div>
        </form>
    </section>
</div>

<div class="col-xs-12 col-md-6">
    <section class="portlet">
        <div class="portlet-head"><?= $this->getHtml('Attributes', 'Attribute', 'Backend'); ?><i class="lni lni-download download btn end-xs"></i></div>
        <div class="slider">
        <table id="attributeTable" class="default"
            data-tag="form"
            data-ui-element="tr"
            data-add-tpl=".oms-add-tpl-attribute"
            data-update-form="attributeForm">
            <thead>
                <tr>
                    <td>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                    <td><?= $this->getHtml('Name', 'Attribute', 'Backend'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td class="wf-100"><?= $this->getHtml('Value', 'Attribute', 'Backend'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                    <td><?= $this->getHtml('Unit', 'Attribute', 'Backend'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
            <tbody>
                <template class="oms-add-tpl-attribute">
                    <tr data-id="" draggable="false">
                        <td>
                            <i class="fa fa-cogs btn update-form"></i>
                            <input id="attributeTable-remove-0" type="checkbox" class="hidden">
                            <label for="attributeTable-remove-0" class="checked-visibility-alt"><i class="fa fa-times btn form-action"></i></label>
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
                foreach ($attribute as $key => $value) : ++$c; ?>
                    <tr data-id="<?= $value->id; ?>">
                        <td>
                            <i class="fa fa-cogs btn update-form"></i>
                            <?php if (!$value->type->isRequired) : ?>
                            <input id="attributeTable-remove-<?= $value->id; ?>" type="checkbox" class="hidden">
                            <label for="attributeTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="fa fa-times btn form-action"></i></label>
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
