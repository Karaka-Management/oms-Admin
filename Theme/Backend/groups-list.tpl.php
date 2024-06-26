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

use phpOMS\Account\GroupStatus;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View            $this
 * @var \Modules\Admin\Models\Group[] $groups
 */
$groups      = $this->data['groups'] ?? [];
$memberCount = $this->data['memberCount'] ?? [];

$tableView            = $this->data['tableView'];
$tableView->id        = 'groupsList';
$tableView->baseUri   = 'admin/group/list';
$tableView->exportUri = '{/api}admin/group/list/export?csrf={$CSRF}';
$tableView->setObjects($groups);

$previous = $tableView->getPreviousLink(
    $this->request,
    empty($this->objects) || !$this->getData('hasPrevious') ? null : \reset($this->objects)
);

$next = $tableView->getNextLink(
    $this->request,
    empty($this->objects) ? null : \end($this->objects),
    $this->getData('hasNext') ?? false
);

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head">
                <?= $tableView->renderTitle(
                    $this->getHtml('Groups')
                ); ?>
            </div>
            <div class="slider">
            <table id="<?= $tableView->id; ?>" class="default sticky">
                <thead>
                    <tr>
                        <td><?= $tableView->renderHeaderElement(
                            'id',
                            $this->getHtml('ID', '0', '0'),
                            'number'
                        ); ?>
                        <td><?= $tableView->renderHeaderElement(
                                'action',
                                $this->getHtml('Status'),
                                'select',
                                [
                                    'active'   => $this->getHtml('Active'),
                                    'inactive' => $this->getHtml('Inactive'),
                                ],
                                false // don't render sort
                            ); ?>
                        <td class="wf-100"><?= $tableView->renderHeaderElement(
                                'module',
                                $this->getHtml('Name'),
                                'text'
                            ); ?>
                        <td><?= $tableView->renderHeaderElement(
                                'module',
                                $this->getHtml('Members'),
                                'number',
                                [],
                                true,
                                false,
                                false
                            ); ?>
                <tbody>
                    <?php $c = 0;
                        foreach ($groups as $key => $value) : ++$c;
                            $url = UriFactory::build('{/base}/admin/group/view?{?}&id=' . $value->id);

                            $color = 'darkred';
                            if ($value->status === GroupStatus::ACTIVE) { $color = 'green'; }
                            elseif ($value->status === GroupStatus::INACTIVE) { $color = 'darkblue'; }
                            elseif ($value->status === GroupStatus::HIDDEN) { $color = 'purple'; }
                    ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $value->id; ?></a>
                        <td data-label="<?= $this->getHtml('Status'); ?>"><a href="<?= $url; ?>"><span class="tag <?= $color; ?>"><?= $this->getHtml('Status' . $value->status); ?></span></a>
                        <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                        <td data-label="<?= $this->getHtml('Members'); ?>"><?= $memberCount[$value->id] ?? 0; ?>
                    <?php endforeach; ?>
                    <?php if ($c === 0) : ?>
                    <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                    <?php endif; ?>
            </table>
            </div>
            <!--
            <?php if ($this->getData('hasPrevious') || $this->getData('hasNext')) : ?>
            <div class="portlet-foot">
                <?php if ($this->getData('hasPrevious')) : ?>
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><i class="g-icon">chevron_left</i></a>
                <?php endif; ?>
                <?php if ($this->getData('hasNext')) : ?>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><i class="g-icon">chevron_right</i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            -->
        </section>
    </div>
</div>