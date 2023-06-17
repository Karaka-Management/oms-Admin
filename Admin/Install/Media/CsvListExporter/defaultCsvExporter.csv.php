<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Media
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

/** @var \phpOMS\Views\View $this */
$data = $this->data['data'] ?? [];

$out = \fopen('php://output', 'w');
if ($out !== false) {
    foreach ($data as $row) {
        \fputcsv($out, $row);
    }

    \fclose($out);
}
