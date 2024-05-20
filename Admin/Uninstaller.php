<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Admin;

use phpOMS\Module\UninstallerAbstract;

/**
 * Uninstall class.
 *
 * @package Modules\Admin\Admin
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Uninstaller extends UninstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;
}
