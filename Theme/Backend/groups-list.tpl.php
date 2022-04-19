<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use phpOMS\Account\GroupStatus;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View            $this
 * @var \Modules\Admin\Models\Group[] $groups
 */
$groups      = $this->getData('groups') ?? [];
$memberCount = $this->getData('memberCount') ?? [];

$previous = empty($groups) ? '{/prefix}admin/group/list' : '{/prefix}admin/group/list?{?}&id=' . \reset($groups)->getId() . '&ptype=p';
$next     = empty($groups) ? '{/prefix}admin/group/list' : '{/prefix}admin/group/list?{?}&id=' . \end($groups)->getId() . '&ptype=n';

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Groups'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <div class="slider">
            <table id="groupList" class="default sticky">
                <thead>
                    <tr>
                        <td><?= $this->getHtml('ID', '0', '0'); ?>
                            <label for="groupList-r1-asc">
                                <input id="groupList-r1-asc" name="groupList-sort" type="radio">
                                <i class="sort-asc fa fa-chevron-up"></i>
                            </label>
                            <label for="groupList-r1-desc">
                                <input id="groupList-r1-desc" name="groupList-sort" type="radio">
                               <i class="sort-desc fa fa-chevron-down"></i>
                            </label>
                        <td><?= $this->getHtml('Status'); ?>
                            <label for="groupList-r2-asc">
                                <input id="groupList-r2-asc" name="groupList-sort" type="radio">
                                <i class="sort-asc fa fa-chevron-up"></i>
                            </label>
                            <label for="groupList-r2-desc">
                                <input id="groupList-r2-desc" name="groupList-sort" type="radio">
                               <i class="sort-desc fa fa-chevron-down"></i>
                            </label>
                        <td class="wf-100"><?= $this->getHtml('Name'); ?>
                            <label for="groupList-r3-asc">
                                <input id="groupList-r3-asc" name="groupList-sort" type="radio">
                                <i class="sort-asc fa fa-chevron-up"></i>
                            </label>
                            <label for="groupList-r3-desc">
                                <input id="groupList-r3-desc" name="groupList-sort" type="radio">
                               <i class="sort-desc fa fa-chevron-down"></i>
                            </label>
                        <td><?= $this->getHtml('Members'); ?>
                            <label for="groupList-r4-asc">
                                <input id="groupList-r4-asc" name="groupList-sort" type="radio">
                                <i class="sort-asc fa fa-chevron-up"></i>
                            </label>
                            <label for="groupList-r4-desc">
                                <input id="groupList-r4-desc" name="groupList-sort" type="radio">
                               <i class="sort-desc fa fa-chevron-down"></i>
                            </label>
                <tbody>
                    <?php $c = 0;
                        foreach ($groups as $key => $value) : ++$c;
                            $url = UriFactory::build('{/prefix}admin/group/settings?{?}&id=' . $value->getId());

                            $color                                                          = 'darkred';
                            if ($value->getStatus() === GroupStatus::ACTIVE) { $color       = 'green'; }
                            elseif ($value->getStatus() === GroupStatus::INACTIVE) { $color = 'darkblue'; }
                            elseif ($value->getStatus() === GroupStatus::HIDDEN) { $color   = 'purple'; }
                    ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $value->getId(); ?></a>
                        <td data-label="<?= $this->getHtml('Status'); ?>"><a href="<?= $url; ?>"><span class="tag <?= $color; ?>"><?= $this->getHtml('Status'. $value->getStatus()); ?></span></a>
                        <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                        <td data-label="<?= $this->getHtml('Members'); ?>"><?= $memberCount[$value->getId()] ?? 0; ?>
                    <?php endforeach; ?>
                    <?php if ($c === 0) : ?>
                    <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                    <?php endif; ?>
            </table>
            </div>
            <div class="portlet-foot">
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
        </div>
    </div>
</div>