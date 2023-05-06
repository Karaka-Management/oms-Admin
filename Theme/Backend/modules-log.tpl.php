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

use phpOMS\Message\Http\HttpHeader;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */
$audits = $this->getData('auditlogs') ?? [];

$previous = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/module/settings?id={?id}#{\#}' : 'admin/module/settings?{?}&audit=' . \reset($audits)->id . '&ptype=p#{\#}';
$next     = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/module/settings?id={?id}#{\#}' : 'admin/module/settings?{?}&audit=' . \end($audits)->id . '&ptype=n#{\#}';

echo $this->getData('nav')->render();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Audits', 'Auditor'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <div class="slider">
            <table class="default">
                <colgroup>
                    <col style="width: 75px">
                    <col style="width: 150px">
                    <col style="width: 100px">
                    <col>
                    <col>
                    <col style="width: 125px">
                    <col style="width: 75px">
                    <col style="width: 150px">
                </colgroup>
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                    <td><?= $this->getHtml('Module', 'Auditor'); ?>
                    <td><?= $this->getHtml('Type', 'Auditor'); ?>
                    <td><?= $this->getHtml('Trigger', 'Auditor'); ?>
                    <td><?= $this->getHtml('Content', 'Auditor'); ?>
                    <td><?= $this->getHtml('By', 'Auditor'); ?>
                    <td><?= $this->getHtml('Ref', 'Auditor'); ?>
                    <td><?= $this->getHtml('Date', 'Auditor'); ?>
                <tbody>
                <?php $count = 0; foreach ($audits as $key => $audit) : ++$count;
                $url         = UriFactory::build('{/base}/admin/audit/single?{?}&id=' . $audit->id); ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td><?= $audit->id; ?>
                        <td><?= $this->printHtml($audit->module); ?>
                        <td><?= $audit->type; ?>
                        <td><?= $this->printHtml($audit->trigger); ?>
                        <td><?= $this->printHtml((string) $audit->content); ?>
                        <td><?= $this->printHtml($audit->createdBy->login); ?>
                        <td><?= $this->printHtml((string) $audit->ref); ?>
                        <td><?= $audit->createdAt->format('Y-m-d H:i'); ?>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
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
