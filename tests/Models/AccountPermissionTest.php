<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\AccountPermission;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Admin\Models\AccountPermission::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Admin\tests\Models\AccountPermissionTest: Account permission')]
final class AccountPermissionTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The account permission has the expected default values after initialization')]
    public function testDefault() : void
    {
        $account = new AccountPermission();
        self::assertEquals(0, $account->getAccount());
    }
}
