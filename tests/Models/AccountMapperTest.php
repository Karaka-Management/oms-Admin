<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\NullAccount;
use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Auth\LoginReturnType;
use phpOMS\Utils\TestUtils;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Admin\Models\AccountMapper::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Admin\tests\Models\AccountMapperTest: Account database mapper')]
final class AccountMapperTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The model can be created and read from the database')]
    public function testCR() : void
    {
        $account = new Account();

        $account->login = 'TestLogin';
        $account->name1 = 'Donald';
        $account->name2 = 'Fauntleroy';
        $account->name3 = 'Duck';
        $account->tries = 0;
        $account->setEmail('d.duck@duckburg.com');
        $account->status = AccountStatus::ACTIVE;
        $account->type   = AccountType::USER;

        $id = AccountMapper::create()->execute($account);
        self::assertGreaterThan(0, $account->id);
        self::assertEquals($id, $account->id);

        $accountR = AccountMapper::get()->where('id', $account->id)->execute();
        self::assertEquals($account->createdAt->format('Y-m-d'), $accountR->createdAt->format('Y-m-d'));
        self::assertEquals($account->login, $accountR->login);
        self::assertEquals($account->name1, $accountR->name1);
        self::assertEquals($account->name2, $accountR->name2);
        self::assertEquals($account->name3, $accountR->name3);
        self::assertEquals($account->status, $accountR->status);
        self::assertEquals($account->type, $accountR->type);
        self::assertEquals($account->getEmail(), $accountR->getEmail());
        self::assertEquals($account->tries, $accountR->tries);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A empty user password results in a failed login')]
    public function testEmptyPasswordLogin() : void
    {
        self::assertEquals(LoginReturnType::WRONG_PASSWORD, AccountMapper::login('admin', ''));
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A invalid user password results in a failed login')]
    public function testInvalidPasswordLogin() : void
    {
        self::assertEquals(LoginReturnType::WRONG_PASSWORD, AccountMapper::login('admin', 'invalid'));
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A invalid user name results in a failed login')]
    public function testInvalidUsernameLogin() : void
    {
        self::assertEquals(LoginReturnType::WRONG_USERNAME, AccountMapper::login('zzzzInvalidTestzzz', 'orange'));
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A valid user name and password results in a successful login')]
    public function testValidLogin() : void
    {
        self::assertGreaterThan(0, AccountMapper::login('admin', 'orange'));
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testInvalidLoginTries() : void
    {
        $accountR        = AccountMapper::get()->where('id', 1)->execute();
        $accountR->tries = 10;
        AccountMapper::update()->execute($accountR);

        self::assertEquals(LoginReturnType::WRONG_INPUT_EXCEEDED, AccountMapper::login($accountR->login, 'orange'));

        $accountR->tries = 0;
        AccountMapper::update()->execute($accountR);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testInvalidLoginAccountStatus() : void
    {
        /** @var Account $accountR */
        $accountR         = AccountMapper::get()->where('id', 1)->execute();
        $accountR->status = AccountStatus::BANNED;
        AccountMapper::update()->execute($accountR);

        self::assertEquals(LoginReturnType::INACTIVE, AccountMapper::login($accountR->login, 'orange'));

        $accountR->status = AccountStatus::ACTIVE;
        AccountMapper::update()->execute($accountR);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testEmptyLoginPassword() : void
    {
        /** @var Account $accountR */
        $accountR = AccountMapper::get()->where('id', 1)->execute();
        TestUtils::setMember($accountR, 'password', '');
        AccountMapper::update()->with('password')->execute($accountR);

        self::assertEquals(LoginReturnType::WRONG_PASSWORD, AccountMapper::login($accountR->login, 'invalidPassword'));

        $accountR->generatePassword('orange');
        AccountMapper::update()->with('password')->execute($accountR);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testGetWithPermission() : void
    {
        $accountR = AccountMapper::getWithPermissions(1);
        self::assertEquals('admin', $accountR->login);
        self::assertGreaterThan(0, $accountR->getPermissions());
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testGetWithPermissionInvalidId() : void
    {
        $accountR = AccountMapper::getWithPermissions(0);

        self::assertInstanceOf(NullAccount::class, $accountR);
    }
}
