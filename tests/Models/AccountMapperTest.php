<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Models;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\AccountMapper;
use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Auth\LoginReturnType;
use phpOMS\Utils\TestUtils;

/**
 * @testdox Modules\Admin\tests\Models\AccountMapperTest: Account database mapper
 *
 * @internal
 */
class AccountMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The model can be created and read from the database
     * @covers Modules\Admin\Models\AccountMapper
     * @group module
     */
    public function testCR() : void
    {
        $account = new Account();

        $account->login = 'TestLogin';
        $account->name1 = 'Donald';
        $account->name2 = 'Fauntleroy';
        $account->name3 = 'Duck';
        $account->tries = 0;
        $account->setEmail('d.duck@duckburg.com');
        $account->setStatus(AccountStatus::ACTIVE);
        $account->setType(AccountType::USER);

        $id = AccountMapper::create($account);
        self::assertGreaterThan(0, $account->getId());
        self::assertEquals($id, $account->getId());

        $accountR = AccountMapper::get($account->getId());
        self::assertEquals($account->createdAt->format('Y-m-d'), $accountR->createdAt->format('Y-m-d'));
        self::assertEquals($account->login, $accountR->login);
        self::assertEquals($account->name1, $accountR->name1);
        self::assertEquals($account->name2, $accountR->name2);
        self::assertEquals($account->name3, $accountR->name3);
        self::assertEquals($account->getStatus(), $accountR->getStatus());
        self::assertEquals($account->getType(), $accountR->getType());
        self::assertEquals($account->getEmail(), $accountR->getEmail());
        self::assertEquals($account->tries, $accountR->tries);
    }

    /**
     * @testdox A empty user password results in a failed login
     * @covers Modules\Admin\Models\AccountMapper
     * @group module
     */
    public function testEmptyPasswordLogin() : void
    {
        self::assertEquals(LoginReturnType::WRONG_PASSWORD, AccountMapper::login('admin', ''));
    }

    /**
     * @testdox A invalid user password results in a failed login
     * @covers Modules\Admin\Models\AccountMapper
     * @group module
     */
    public function testInvalidPasswordLogin() : void
    {
        self::assertEquals(LoginReturnType::WRONG_PASSWORD, AccountMapper::login('admin', 'invalid'));
    }

    /**
     * @testdox A invalid user name results in a failed login
     * @covers Modules\Admin\Models\AccountMapper
     * @group module
     */
    public function testInvalidUsernameLogin() : void
    {
        self::assertEquals(LoginReturnType::WRONG_USERNAME, AccountMapper::login('zzzzInvalidTestzzz', 'orange'));
    }

    /**
     * @testdox A valid user name and password results in a successful login
     * @covers Modules\Admin\Models\AccountMapper
     * @group module
     */
    public function testValidLogin() : void
    {
        self::assertGreaterThan(0, AccountMapper::login('admin', 'orange'));
    }

    /**
     * @covers Modules\Admin\Models\AccountMapper
     * @group module
     */
    public function testInvalidLoginTries() : void
    {
        $accountR        = AccountMapper::get(1);
        $accountR->tries = 10;
        AccountMapper::update($accountR);

        self::assertEquals(LoginReturnType::WRONG_INPUT_EXCEEDED, AccountMapper::login($accountR->login, 'orange'));

        $accountR->tries = 0;
        AccountMapper::update($accountR);
    }

    /**
     * @covers Modules\Admin\Models\AccountMapper
     * @group module
     */
    public function testInvalidLoginAccountStatus() : void
    {
        $accountR = AccountMapper::get(1);
        $accountR->setStatus(AccountStatus::BANNED);
        AccountMapper::update($accountR);

        self::assertEquals(LoginReturnType::INACTIVE, AccountMapper::login($accountR->login, 'orange'));

        $accountR->setStatus(AccountStatus::ACTIVE);
        AccountMapper::update($accountR);
    }

    /**
     * @covers Modules\Admin\Models\AccountMapper
     * @group module
     */
    public function testEmptyLoginPassword() : void
    {
        $accountR = AccountMapper::get(1);
        TestUtils::setMember($accountR, 'password', '');
        AccountMapper::update($accountR);

        self::assertEquals(LoginReturnType::EMPTY_PASSWORD, AccountMapper::login($accountR->login, 'orange'));

        $accountR->generatePassword('orange');
        AccountMapper::update($accountR);
    }

    /**
     * @covers Modules\Admin\Models\AccountMapper
     * @group module
     */
    public function testGetWithPermission() : void
    {
        $accountR = AccountMapper::getWithPermissions(1);
        self::assertEquals('admin', $accountR->login);
        self::assertGreaterThan(0, $accountR->getPermissions());
    }
}
