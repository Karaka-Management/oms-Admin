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

use phpOMS\Account\GroupStatus;
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <form id="fGroupCreate"
                action="<?= UriFactory::build('{/api}admin/group?csrf={$CSRF}'); ?>"
                method="put"
                data-redirect="<?= UriFactory::build('{/base}/admin/group/view'); ?>?id={/0/response/id}"
                autocomplete="off">
                <div class="portlet-head"><?= $this->getHtml('Group'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <select id="iStatus" name="status">
                            <option value="<?= GroupStatus::ACTIVE; ?>" selected><?= $this->getHtml('Active'); ?>
                            <option value="<?= GroupStatus::INACTIVE; ?>"><?= $this->getHtml('Inactive'); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="iGname"><?= $this->getHtml('Name'); ?></label>
                        <input id="iGname" name="name" type="text" spellcheck="false" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <?= $this->data['editor']->render('group-editor'); ?>
                    </div>

                    <?= $this->data['editor']->getData('text')->render('group-editor', 'description', 'fGroupCreate'); ?>
                </div>
                <div class="portlet-foot">
                    <input type="submit" id="iCreateGroup" name="create" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                </div>
            </form>
        </section>
    </div>
</div>
