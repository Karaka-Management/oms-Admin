<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Admin\Template\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

/**
 * @var \phpOMS\Views\View $this
 */
$settings = $this->data['settings'] ?? [];

echo $this->data['nav']->render();

if ($this->hasData('settingsTpl')
    && \is_file($this->getData('settingsTpl'))
) :
    include $this->data['settingsTpl'];
else :
    include __DIR__ . '/settings.tpl.php';
endif;
