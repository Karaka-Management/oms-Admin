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

require_once __DIR__ . '/../phpOMS/Autoloader.php';

use phpOMS\Autoloader;

Autoloader::addPath(__DIR__ . '/../Resources');

/** @var \phpOMS\Views\View $this */
/** @var \Modules\Media\Models\Collection $media */
$media = $this->data['media'];

/** @var array<array> $data */
$data = $this->data['data'] ?? [];

include $media->getSourceByName('template.php')->getAbsolutePath();

$word    = new DefaultWord();
$section = $word->createFirstPage();

$tbl = '<table border="1" cellpadding="0" cellspacing="0">';
foreach ($data as $i => $row) {
    if ($i === 0) {
        $tbl = '<thead><tr>';

        foreach ($row as $j => $cell) {
            $tbl .= '<td>' . $cell . '</td>';
        }

        $tbl .= '</tr></thead>';
    } else {
        $tbl .= '<tr>';
        foreach ($row as $j => $cell) {
            $tbl .= '<td>' . $cell . '</td>';
        }
        $tbl .= '</tr>';
    }
}
$tbl .= '</table>';

\PhpOffice\PhpWord\Shared\Html::addHtml($section, $tbl, false, false);

$file = \tempnam(\sys_get_temp_dir(), 'oms_');
if ($file === false) {
    return '';
}

$writer = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
$writer->save($file);

echo \file_get_contents($file);

\unlink($file);
