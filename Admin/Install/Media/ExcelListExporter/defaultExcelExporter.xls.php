<?php
/**
 * Karaka
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

require_once __DIR__ . '/../phpOMS/Autoloader.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use phpOMS\Autoloader;

Autoloader::addPath(__DIR__ . '/../Resources');

/** @var \phpOMS\Views\View $this */
$media = $this->getData('media');
$data  = $this->getData('data') ?? [];

include $media->getSourceByName('template.php')->getAbsolutePath();

$excel = new DefaultExcel();

foreach ($data as $i => $row) {
    foreach ($row as $j => $cell) {
        // @todo: fix 26 column overflow.
        $excel->getActiveSheet()->setCellValue(\chr(64 + $j + 1) . ($i + 1), $cell);
    }
}

$file = \tempnam(\sys_get_temp_dir(), 'oms_');

$writer = IOFactory::createWriter($excel, 'Xlsx');
$writer->save($file);

$content = \file_get_contents($file);
if ($content !== false) {
    echo $content;
}

\unlink($file);
