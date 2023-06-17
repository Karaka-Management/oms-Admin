<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin
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
$media = $this->data['media'];
$data  = $this->data['data'] ?? [];

include $media->getSourceByName('template.php')->getAbsolutePath();

$word    = new DefaultWord();
$section = $word->createFirstPage();

$file = \tempnam(\sys_get_temp_dir(), 'oms_');
$writer->save($file);

if ($file !== false) {
    $content = \file_get_contents($file);
    if ($content !== false) {
        echo $content;
    }

    \unlink($file);
}
