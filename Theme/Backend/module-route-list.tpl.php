<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use phpOMS\Message\Http\HttpHeader;

/**
 * @var \phpOMS\Views\View $this
 */
$audits = $this->getData('auditlogs') ?? [];

$previous = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? '{/prefix}admin/module/settings?id={?id}#{\#}' : '{/prefix}admin/module/settings?{?}&audit=' . \reset($audits)->getId() . '&ptype=p#{\#}';
$next     = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? '{/prefix}admin/module/settings?id={?id}#{\#}' : '{/prefix}admin/module/settings?{?}&audit=' . \end($audits)->getId() . '&ptype=n#{\#}';

echo $this->getData('nav')->render();
