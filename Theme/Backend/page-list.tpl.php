<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Account\AccountStatus;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View           $this
 * @var \Modules\Admin\Models\Page[] $pages
 */
$pages = $this->data['pages'] ?? [];

$previous = empty($pages) ? 'admin/page/list' : '{/base}/admin/page/list?{?}&offset=' . \reset($pages)->id . '&ptype=p';
$next     = empty($pages) ? 'admin/page/list' : '{/base}/admin/page/list?{?}&offset=' . \end($pages)->id . '&ptype=n';

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head">
                <?= $this->getHtml('Pages'); ?>
            </div>
            <div class="slider">
            <table id="accountList" class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <label for="accountList-r1-asc">
                            <input id="accountList-r1-asc" name="accountList-sort" type="radio">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="accountList-r1-desc">
                            <input id="accountList-r1-desc" name="accountList-sort" type="radio">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                    <td><?= $this->getHtml('Status'); ?>
                        <label for="accountList-r2-asc">
                            <input id="accountList-r2-asc" name="accountList-sort" type="radio">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="accountList-r2-desc">
                            <input id="accountList-r2-desc" name="accountList-sort" type="radio">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <label for="accountList-r3-asc">
                            <input id="accountList-r3-asc" name="accountList-sort" type="radio">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="accountList-r3-desc">
                            <input id="accountList-r3-desc" name="accountList-sort" type="radio">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <?php include __DIR__ . '/../../../../Web/Backend/Themes/popup-filter-table.tpl.php'; ?>
                    <td><?= $this->getHtml('Activity'); ?>
                        <label for="accountList-r4-asc">
                            <input id="accountList-r4-asc" name="accountList-sort" type="radio">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="accountList-r4-desc">
                            <input id="accountList-r4-desc" name="accountList-sort" type="radio">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Created'); ?>
                        <label for="accountList-r5-asc">
                            <input id="accountList-r5-asc" name="accountList-sort" type="radio">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="accountList-r5-desc">
                            <input id="accountList-r5-desc" name="accountList-sort" type="radio">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <tbody>
                        <?php $c = 0; foreach ($accounts as $key => $value) : ++$c;
                        $url     = UriFactory::build('{/base}/admin/account/settings?{?}&id=' . $value->id);
                        $color   = 'darkred';
                        if ($value->status === AccountStatus::ACTIVE) { $color = 'green'; }
                        elseif ($value->status === AccountStatus::INACTIVE) { $color = 'darkblue'; }
                        elseif ($value->status === AccountStatus::TIMEOUT) { $color = 'purple'; }
                        elseif ($value->status === AccountStatus::BANNED) { $color = 'red'; } ?>
                <tr tabindex="0" data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $value->id; ?></a>
                    <td data-label="<?= $this->getHtml('Status'); ?>"><a href="<?= $url; ?>"><span class="tag <?= $color; ?>"><?= $this->getHtml('Status' . $value->status); ?></span></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($this->renderUserName('%3$s %2$s %1$s', [$value->name1, $value->name2, $value->name3, $value->login])); ?></a>
                    <td data-label="<?= $this->getHtml('Activity'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getLastActive()->format('Y-m-d H:i:s')); ?></a>
                    <td data-label="<?= $this->getHtml('Created'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->createdAt->format('Y-m-d H:i:s')); ?></a>
                        <?php endforeach; ?>
                        <?php if ($c === 0) : ?>
                            <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                        <?php endif; ?>
            </table>
            </div>
            <!--
            <div class="portlet-foot">
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
            -->
        </section>
    </div>
</div>
