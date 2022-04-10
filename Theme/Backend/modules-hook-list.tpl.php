<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use phpOMS\Message\Http\HttpHeader;
use phpOMS\Router\RouteVerb;

/**
 * @var \phpOMS\Views\View $this
 */
$hooks = $this->getData('hooks') ?? [];
$module = $this->getData('module') ?? '';

echo $this->getData('nav')->render();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Hooks'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <div class="slider">
            <table id="navElements" class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('Active'); ?>
                    <td><?= $this->getHtml('App'); ?>
                        <label for="navElements-sort-1">
                            <input type="radio" name="navElements-sort" id="navElements-sort-1">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="navElements-sort-2">
                            <input type="radio" name="navElements-sort" id="navElements-sort-2">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Verb'); ?>
                        <label for="navElements-sort-3">
                            <input type="radio" name="navElements-sort" id="navElements-sort-3">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="navElements-sort-4">
                            <input type="radio" name="navElements-sort" id="navElements-sort-4">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Route'); ?>
                        <label for="navElements-sort-5">
                            <input type="radio" name="navElements-sort" id="navElements-sort-5">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="navElements-sort-6">
                            <input type="radio" name="navElements-sort" id="navElements-sort-6">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Destination'); ?>
                        <label for="navElements-sort-7">
                            <input type="radio" name="navElements-sort" id="navElements-sort-7">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="navElements-sort-8">
                            <input type="radio" name="navElements-sort" id="navElements-sort-8">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                </thead>
                <tbody>
                    <?php $c = 0;
                        foreach ($hooks as $app => $appHooks) :
                        foreach ($appHooks as $uri => $destinations) :
                        foreach ($destinations as $route) :
                            if (\stripos($route['dest'], '\Modules\\' . $module . '\Controller') === false) {
                                continue;
                            }

                            ++$c;
                    ?>
                    <tr>
                        <td><label class="checkbox" for="iActive-<?= $c ?>">
                                <input id="iActive-<?= $c ?>" type="checkbox" name="active_route" value="<?= $this->printHtml($uri); ?>"<?= true ? ' checked' : ''; ?>>
                                <span class="checkmark"></span>
                            </label>
                        <td><?= $app; ?>
                        <td><?= RouteVerb::getName((string) $route['verb']); ?>
                        <td><?= $uri; ?>
                        <td><?= $route['dest']; ?>
                    <?php endforeach; endforeach; endforeach; ?>
            </table>
        </div>
    </div>
</div>