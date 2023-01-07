<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Application\ApplicationStatus;
use phpOMS\Application\ApplicationType;

/**
 * App model.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class App
{
    /**
     * Id
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Name
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * Theme
     *
     * @var string
     * @since 1.0.0
     */
    public string $theme = '';

    /**
     * Status
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = ApplicationStatus::NORMAL;

    /**
     * Status
     *
     * @var int
     * @since 1.0.0
     */
    public int $type = ApplicationType::WEB;

    /**
     * Get id
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }
}
