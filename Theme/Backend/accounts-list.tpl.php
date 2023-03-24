<?php
/**
 * Karaka
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
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View              $this
 * @var \Modules\Admin\Models\Account[] $accounts
 */
$accounts = $this->getData('accounts') ?? [];

$tableView            = $this->getData('tableView');
$tableView->id        = 'accountsList';
$tableView->baseUri   = 'admin/account/list';
$tableView->exportUri = '{/api}admin/account/list/export';
$tableView->setObjects($accounts);

$previous = $tableView->getPreviousLink(
    $this->request,
    empty($this->objects) || !$this->getData('hasPrevious') ? null : \reset($this->objects)
);

$next = $tableView->getNextLink(
    $this->request,
    empty($this->objects) ? null : \end($this->objects),
    $this->getData('hasNext') ?? false
);

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head">
                <?= $tableView->renderTitle(
                    $this->getHtml('Accounts')
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
                        'lastActive',
                        $this->getHtml('Activity'),
                        'date'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'createdAt',
                        $this->getHtml('Created'),
                        'date'
                    ); ?>
                    <tbody>
                        <?php
                        $c = 0;
                        foreach ($accounts as $key => $value) : ++$c;
                            $url   = UriFactory::build('{/base}/admin/account/settings?{?}&id=' . $value->getId());
                            $color = 'darkred';

                            if ($value->getStatus() === AccountStatus::ACTIVE) { $color       = 'green'; }
                            elseif ($value->getStatus() === AccountStatus::INACTIVE) { $color = 'darkblue'; }
                            elseif ($value->getStatus() === AccountStatus::TIMEOUT) { $color  = 'purple'; }
                            elseif ($value->getStatus() === AccountStatus::BANNED) { $color   = 'red'; }
                        ?>
                <tr tabindex="0" data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $value->getId(); ?></a>
                    <td data-label="<?= $this->getHtml('Status'); ?>"><a href="<?= $url; ?>"><span class="tag <?= $color; ?>"><?= $this->getHtml('Status'. $value->getStatus()); ?></span></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($this->renderUserName('%3$s %2$s %1$s', [$value->name1, $value->name2, $value->name3, $value->login])); ?></a>
                    <td data-label="<?= $this->getHtml('Activity'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getLastActive()->format('Y-m-d H:i:s')); ?></a>
                    <td data-label="<?= $this->getHtml('Created'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->createdAt->format('Y-m-d H:i:s')); ?></a>
                        <?php endforeach; ?>
                        <?php if ($c === 0) : ?>
                            <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                        <?php endif; ?>
            </table>
            </div>
            <?php if ($this->getData('hasPrevious') || $this->getData('hasNext')) : ?>
            <div class="portlet-foot">
                <?php if ($this->getData('hasPrevious')) : ?>
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><i class="fa fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php if ($this->getData('hasNext')) : ?>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><i class="fa fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
