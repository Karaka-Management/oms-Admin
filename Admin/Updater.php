<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Admin\Admin;

use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\Module\ModuleInfo;
use phpOMS\Module\UpdaterAbstract;
use phpOMS\System\File\Local\Directory;

/**
 * Update class.
 *
 * @package Modules\Admin\Admin
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class Updater extends UpdaterAbstract
{
    /**
     * {@inheritdoc}
     */
    public static function update(DatabasePool $dbPool, ModuleInfo $info) : void
    {
        Directory::delete(__DIR__ . '/Update');
        \mkdir('Update');
        parent::update($dbPool, $info);
    }
}
