<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Address
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Localization\ISO639Enum;
use phpOMS\Stdlib\Base\AddressType;
use phpOMS\Uri\UriFactory;

$address   = $this->addresses;
$languages = ISO639Enum::getConstants();
$types     = AddressType::getConstants();
?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <form id="addressForm" action="<?= UriFactory::build('{api}account/address?csrf={$CSRF}'); ?>" method="post"
                data-ui-container="#addressTable tbody"
                data-add-form="addressForm"
                data-add-tpl="#addressTable tbody .oms-add-tpl-address">
                <div class="portlet-head"><?= $this->getHtml('Address', 'Admin', 'Backend'); ?></div>
                <div class="portlet-body">
                    <input type="hidden" id="iAddressRef" name="account" value="<?= $this->refId; ?>" disabled>

                    <div class="form-group">
                        <label for="iAddressId"><?= $this->getHtml('ID', '0', '0'); ?></label>
                        <input type="text" id="iAddressId" name="id" data-tpl-text="/id" data-tpl-value="/id" disabled>
                    </div>

                    <div class="form-group">
                        <label for="iAddressName"><?= $this->getHtml('Name', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iAddressName" name="name" data-tpl-text="/name" data-tpl-value="/name">
                    </div>

                    <div class="form-group">
                        <label for="iAddressFao"><?= $this->getHtml('FAO', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iAddressFao" name="fao" data-tpl-text="/fao" data-tpl-value="/fao">
                    </div>

                    <div class="form-group">
                        <label for="iAddressAddress"><?= $this->getHtml('Address', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iAddressAddress" name="address" data-tpl-text="/address" data-tpl-value="/address">
                    </div>

                    <div class="form-group">
                        <label for="iAddressAddition"><?= $this->getHtml('Addition', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iAddressAddition" name="addition" data-tpl-text="/addition" data-tpl-value="/addition">
                    </div>

                    <div class="form-group">
                        <label for="iAddressPostal"><?= $this->getHtml('Postal', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iAddressPostal" name="postal" data-tpl-text="/postal" data-tpl-value="/postal">
                    </div>

                    <div class="form-group">
                        <label for="iAddressCity"><?= $this->getHtml('City', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iAddressCity" name="city" data-tpl-text="/city" data-tpl-value="/city">
                    </div>

                    <div class="form-group">
                        <label for="iAddressState"><?= $this->getHtml('State', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iAddressState" name="state" data-tpl-text="/state" data-tpl-value="/state">
                    </div>

                    <div class="form-group">
                        <label for="iAddressCountry"><?= $this->getHtml('Country', 'Admin', 'Backend'); ?></label>
                        <input type="text" id="iAddressCountry" name="country" data-tpl-text="/country" data-tpl-value="/country">
                    </div>

                    <div class="form-group">
                        <label for="iAddressesType"><?= $this->getHtml('Type', 'Admin', 'Backend'); ?></label>
                        <select id="iAddressesType" name="type" data-tpl-text="/type" data-tpl-value="/type">
                        <?php
                            foreach ($types as $type) : ?>
                                <option value="<?= $type; ?>"><?= $this->getHtml(':address' . $type); ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="portlet-foot">
                    <input id="bAddressAdd" formmethod="put" type="submit" class="add-form" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                    <input id="bAddressSave" formmethod="post" type="submit" class="save-form vh button save" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                    <input id="bAddressCancel" type="submit" class="cancel-form vh button close" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
                </div>
            </form>
        </section>
    </div>

    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Addresses', 'Admin', 'Backend'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="addressTable" class="default sticky"
                data-tag="form"
                data-ui-element="tr"
                data-add-tpl=".oms-add-tpl-address"
                data-update-form="addressForm">
                <thead>
                    <tr>
                        <td>
                        <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <td><?= $this->getHtml('Type', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Name', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('FAO', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Address', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Addition', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Postal', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('City', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('State', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                        <td><?= $this->getHtml('Country', 'Admin', 'Backend'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                <tbody>
                    <template class="oms-add-tpl-address">
                        <tr class="animated medium-duration greenCircleFade" data-id="" draggable="false">
                            <td>
                                <i class="g-icon btn update-form">settings</i>
                                <input id="addressTable-remove-0" type="checkbox" class="vh">
                                <label for="addressTable-remove-0" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                <span class="checked-visibility">
                                    <label for="addressTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                    <label for="addressTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                </span>
                            <td data-tpl-text="/id" data-tpl-value="/id"></td>
                            <td data-tpl-text="/type" data-tpl-value="/type" data-value=""></td>
                            <td data-tpl-text="/name" data-tpl-value="/name"></td>
                            <td data-tpl-text="/fao" data-tpl-value="/fao"></td>
                            <td data-tpl-text="/address" data-tpl-value="/address"></td>
                            <td data-tpl-text="/addition" data-tpl-value="/addition"></td>
                            <td data-tpl-text="/postal" data-tpl-value="/postal"></td>
                            <td data-tpl-text="/city" data-tpl-value="/city"></td>
                            <td data-tpl-text="/state" data-tpl-value="/state"></td>
                            <td data-tpl-text="/country" data-tpl-value="/country"></td>
                        </tr>
                    </template>
                    <?php
                    $c = 0;
                    foreach ($address as $key => $value) :
                        ++$c;
                    ?>
                    <tr data-id="<?= $value->id; ?>">
                        <td>
                            <i class="g-icon btn update-form">settings</i>
                            <input id="addressTable-remove-<?= $value->id; ?>" type="checkbox" class="vh">
                            <label for="addressTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                            <span class="checked-visibility">
                                <label for="addressTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                <label for="addressTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                            </span>
                        <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                        <td data-tpl-text="/type" data-tpl-value="/type" data-value="<?= $value->type; ?>"><?= $this->getHtml(':address' . $value->type, 'Admin', 'Backend'); ?>
                        <td data-tpl-text="/name" data-tpl-value="/name"><?= $this->printHtml($value->name); ?>
                        <td data-tpl-text="/fao" data-tpl-value="/fao"><?= $this->printHtml($value->fao); ?>
                        <td data-tpl-text="/address" data-tpl-value="/address"><?= $this->printHtml($value->address); ?>
                        <td data-tpl-text="/addition" data-tpl-value="/addition"><?= $this->printHtml($value->addressAddition); ?>
                        <td data-tpl-text="/postal" data-tpl-value="/postal"><?= $this->printHtml($value->postal); ?>
                        <td data-tpl-text="/city" data-tpl-value="/city"><?= $this->printHtml($value->city); ?>
                        <td data-tpl-text="/state" data-tpl-value="/state"><?= $this->printHtml($value->state); ?>
                        <td data-tpl-text="/country" data-tpl-value="/country"><?= $this->printHtml($value->country); ?>
                    <?php endforeach; ?>
                    <?php if ($c === 0) : ?>
                    <tr>
                        <td colspan="11" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                    <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>
