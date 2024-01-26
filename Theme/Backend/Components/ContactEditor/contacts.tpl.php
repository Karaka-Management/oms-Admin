<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Contact
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Admin\Models\ContactType;
use phpOMS\Localization\ISO639Enum;
use phpOMS\Stdlib\Base\AddressType;
use phpOMS\Uri\UriFactory;

$contact   = $this->contacts;
$languages = ISO639Enum::getConstants();
$types     = ContactType::getConstants();
$subtypes  = AddressType::getConstants();
?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <form id="contactForm" action="<?= UriFactory::build('{api}account/contact'); ?>" method="post"
                data-ui-container="#contactTable tbody"
                data-add-form="contactForm"
                data-add-tpl="#contactTable tbody .oms-add-tpl-contact">
                <div class="portlet-head"><?= $this->getHtml('Contact', 'Admin', 'Backend'); ?></div>
                <div class="portlet-body">
                    <input type="hidden" id="iContactRef" name="account" value="<?= $this->refId; ?>" disabled>

                    <div class="form-group">
                        <label for="iContactId"><?= $this->getHtml('ID', '0', '0'); ?></label>
                        <input type="text" id="iContactId" name="id" data-tpl-text="/id" data-tpl-value="/id" disabled>
                    </div>

                    <div class="form-group">
                        <label for="iContactName"><?= $this->getHtml('Name', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iContactName" name="name" data-tpl-text="/name" data-tpl-value="/name">
                    </div>

                    <div class="form-group">
                        <label for="iContactsType"><?= $this->getHtml('Type', 'Admin', 'Backend'); ?></label>
                        <select id="iContactsType" name="type" data-tpl-text="/type" data-tpl-value="/type">
                            <?php
                            foreach ($types as $type) : ?>
                                <option value="<?= $type; ?>"><?= $this->getHtml(':contact' . $type); ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="iContactsSubtype"><?= $this->getHtml('Subtype', 'Admin', 'Backend'); ?></label>
                        <select id="iContactsSubtype" name="subtype" data-tpl-text="/subtype" data-tpl-value="/subtype">
                            <?php
                            foreach ($subtypes as $type) : ?>
                                <option value="<?= $type; ?>"><?= $this->getHtml(':address' . $type); ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="iContactContent"><?= $this->getHtml('Contact', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iContactContent" name="content" data-tpl-text="/content" data-tpl-value="/content">
                    </div>
                </div>
                <div class="portlet-foot">
                    <input id="bContactAdd" formmethod="put" type="submit" class="add-form" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                    <input id="bContactSave" formmethod="post" type="submit" class="save-form hidden button save" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                    <input id="bContactCancel" type="submit" class="cancel-form hidden button close" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
                </div>
            </form>
        </section>
    </div>

    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Contacts', 'Admin', 'Backend'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="contactTable" class="default sticky"
                data-tag="form"
                data-ui-element="tr"
                data-add-tpl=".oms-add-tpl-contact"
                data-update-form="contactForm">
                <thead>
                    <tr>
                        <td>
                        <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <td><?= $this->getHtml('Type', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Subtype', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Name', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Content', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                <tbody>
                    <template class="oms-add-tpl-contact">
                        <tr class="animated medium-duration greenCircleFade" data-id="" draggable="false">
                            <td>
                                <i class="g-icon btn update-form">settings</i>
                                <input id="contactTable-remove-0" type="checkbox" class="hidden">
                                <label for="contactTable-remove-0" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                <span class="checked-visibility">
                                    <label for="contactTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                    <label for="contactTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                </span>
                            <td data-tpl-text="/id" data-tpl-value="/id"></td>
                            <td data-tpl-text="/type" data-tpl-value="/type" data-value=""></td>
                            <td data-tpl-text="/subtype" data-tpl-value="/subtype" data-value=""></td>
                            <td data-tpl-text="/name" data-tpl-value="/name"></td>
                            <td data-tpl-text="/content" data-tpl-value="/content"></td>
                        </tr>
                    </template>
                    <?php $c = 0;
                    foreach ($contact as $key => $value) : ++$c; ?>
                        <tr data-id="<?= $value->id; ?>">
                            <td>
                                <i class="g-icon btn update-form">settings</i>
                                <input id="contactTable-remove-<?= $value->id; ?>" type="checkbox" class="hidden">
                                <label for="contactTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                <span class="checked-visibility">
                                    <label for="contactTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                    <label for="contactTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                </span>
                            <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                            <td data-tpl-text="/type" data-tpl-value="/type" data-value="<?= $value->type; ?>"><?= $this->getHtml(':contact' . $value->type, 'Admin', 'Backend'); ?>
                            <td data-tpl-text="/subtype" data-tpl-value="/subtype" data-value="<?= $value->subtype; ?>"><?= $this->getHtml(':address' . $value->subtype, 'Admin', 'Backend'); ?>
                            <td data-tpl-text="/name" data-tpl-value="/name"><?= $this->printHtml($value->title); ?>
                            <td data-tpl-text="/content" data-tpl-value="/content"><?= $this->printHtml($value->content); ?>
                    <?php endforeach; ?>
                    <?php if ($c === 0) : ?>
                    <tr><td colspan="11" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                    <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>
