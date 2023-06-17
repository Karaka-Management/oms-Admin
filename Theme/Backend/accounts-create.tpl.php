<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="fAccount" action="<?= UriFactory::build('{/api}admin/account'); ?>" method="put">
                <div class="portlet-head"><?= $this->getHtml('Account'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iType"><?= $this->getHtml('Type'); ?></label>
                        <select id="Type" name="type">
                                    <option value="<?= $this->printHtml((string) AccountType::USER); ?>"><?= $this->getHtml('Person'); ?>
                                    <option value="<?= $this->printHtml((string) AccountType::GROUP); ?>"><?= $this->getHtml('Organization'); ?>
                                </select>
                    </div>
                    <div class="form-group">
                        <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <select id="iStatus" name="status">
                                    <option value="<?= $this->printHtml((string) AccountStatus::ACTIVE); ?>"><?= $this->getHtml('Active'); ?>
                                    <option value="<?= $this->printHtml((string) AccountStatus::INACTIVE); ?>"><?= $this->getHtml('Inactive'); ?>
                                    <option value="<?= $this->printHtml((string) AccountStatus::TIMEOUT); ?>"><?= $this->getHtml('Timeout'); ?>
                                    <option value="<?= $this->printHtml((string) AccountStatus::BANNED); ?>"><?= $this->getHtml('Banned'); ?>
                                </select>
                    </div>
                    <div class="form-group">
                        <label for="iUsername"><?= $this->getHtml('Username'); ?></label>
                        <span class="input">
                                    <button class="inactive" type="button"><i class="fa fa-user"></i></button>
                                    <input id="iUsername" name="name" type="text" autocomplete="off" spellcheck="false">
                                </span>
                    </div>
                    <div class="form-group">
                        <label for="iName1"><?= $this->getHtml('Name1'); ?></label>
                        <span class="input">
                                    <button class="inactive" type="button"><i class="fa fa-user"></i></button>
                                    <input id="iName1" name="name1" type="text" autocomplete="off" spellcheck="false" required>
                                </span>
                    </div>
                    <div class="form-group">
                        <label for="iName2"><?= $this->getHtml('Name2'); ?></label>
                        <span class="input">
                                    <button class="inactive" type="button"><i class="fa fa-user"></i></button>
                                    <input id="iName2" name="name2" type="text" autocomplete="off" spellcheck="false">
                                </span>
                    </div>
                    <div class="form-group">
                        <label for="iName3"><?= $this->getHtml('Name3'); ?></label>
                        <span class="input">
                                    <button class="inactive" type="button"><i class="fa fa-user"></i></button>
                                    <input id="iName3" name="name3" type="text" autocomplete="off" spellcheck="false">
                                </span>
                    </div>
                    <div class="form-group">
                        <label for="iEmail"><?= $this->getHtml('Email'); ?></label>
                        <span class="input">
                                    <button class="inactive" type="button"><i class="fa fa-envelope-o"></i></button>
                                    <input id="iEmail" name="email" type="email" autocomplete="off" spellcheck="false">
                                </span>
                    </div>
                    <div class="form-group">
                        <label for="iPassword"><?= $this->getHtml('Password'); ?></label>
                        <span class="input">
                                    <button class="inactive" type="button"><i class="fa fa-lock"></i></button>
                                    <input id="iPassword" name="password" type="password">
                                </span>
                    </div>
                </div>
                <div class="portlet-foot">
                    <input id="account-create-submit" name="createSubmit" type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>
