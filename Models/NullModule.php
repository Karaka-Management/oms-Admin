<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

/**
 * Null model
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class NullModule extends Module
{
    /**
     * Constructor
     *
     * @param string $id Model id
     *
     * @since 1.0.0
     */
    public function __construct(string $id = '')
    {
        parent::__construct();
        $this->id = $id;
    }
}
