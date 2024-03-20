<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\Account;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Admin\Models\Account::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Admin\tests\Models\AccountTest: Account model')]
final class AccountTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The account has the expected default values after initialization')]
    public function testDefault() : void
    {
        $account = new Account();
        self::assertEquals(0, $account->tries);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The login tries can be set and returned')]
    public function testLoginTriesInputOutput() : void
    {
        $account = new Account();

        $account->tries = 3;
        self::assertEquals(3, $account->tries);
    }
}
