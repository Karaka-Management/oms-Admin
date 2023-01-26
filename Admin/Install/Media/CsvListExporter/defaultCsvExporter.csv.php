<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

$data = $this->getData('data') ?? [];

$out = \fopen('php://output', 'w');

foreach ($data as $row) {
    fputcsv($out, $row);
}

\fclose($out);
