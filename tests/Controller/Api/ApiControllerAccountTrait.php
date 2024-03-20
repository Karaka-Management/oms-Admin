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

namespace Modules\Admin\tests\Controller\Api;

use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;

trait ApiControllerAccountTrait
{
    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user can be returned')]
    public function testApiAccountGet() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('id', '1');

        $this->module->apiAccountGet($request, $response);

        self::assertEquals('admin', $response->getDataArray('')['response']->login);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user can be updated')]
    public function testApiAccountUpdate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('id', 1);
        $request->setData('email', 'oms@karaka.de');
        $request->setData('password', 'orange');

        $this->module->apiAccountUpdate($request, $response);
        $this->module->apiAccountGet($request, $response);

        self::assertEquals('oms@karaka.de', $response->getDataArray('')['response']->getEmail());
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user can be found by name')]
    public function testApiAccountFind() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('search', 'admin');

        $this->module->apiAccountFind($request, $response);
        self::assertCount(1, $response->getData(''));
        self::assertEquals('admin', $response->getData('')[0]->name1);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user and profile for the user can be created')]
    public function testApiAccountAndProfileCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('user', 'guest');
        $request->setData('password', 'guest');
        $request->setData('name1', 'Guest');
        $request->setData('email', 'test@email.com');
        $request->setData('type', AccountType::USER);
        $request->setData('status', AccountStatus::INACTIVE);

        $this->module->apiAccountCreate($request, $response);

        self::assertEquals('guest', $response->getDataArray('')['response']->login);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiAccountCreateWithCustomLocale() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('user', 'guest2');
        $request->setData('password', 'guest2');
        $request->setData('name1', 'Guest2');
        $request->setData('email', 'guest2@email.com');
        $request->setData('type', AccountType::USER);
        $request->setData('status', AccountStatus::INACTIVE);
        $request->setData('locale', 'de_DE');

        $this->module->apiAccountCreate($request, $response);

        self::assertEquals('guest2', $response->getDataArray('')['response']->login);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user can be deleted')]
    public function testApiAccountDelete() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        // mustn't create a profile otherwise it will not be possible to delete the account because of FK constraints
        $request->setData('name1', 'Guest');
        $request->setData('email', 'test@email.com');
        $request->setData('type', AccountType::USER);
        $request->setData('status', AccountStatus::INACTIVE);

        $this->module->apiAccountCreate($request, $response);
        $request->setData('id', $response->getDataArray('')['response']->id);
        $this->module->apiAccountDelete($request, $response);

        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A invalid user cannot be created')]
    public function testApiAccountCreateInvalid() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('status', 999);
        $request->setData('description', 'test description');

        $this->module->apiAccountCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user group can be added to a user')]
    public function testApiAddRemoveGroupToAccount() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('account', 1);
        $request->setData('account-list', '1');

        $this->module->apiAddGroupToAccount($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);

        // remove
        $response = new HttpResponse();

        $this->module->apiDeleteGroupFromAccount($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiRemoveAdminGroupFromOneselfAccount() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('account', 1);
        $request->setData('account-list', '3');

        $this->module->apiDeleteGroupFromAccount($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiAccountLogin() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('user', 'admin');
        $request->setData('pass', 'orange');

        $this->module->apiLogin($request, $response);
        self::assertInstanceOf('\phpOMS\Model\Message\Reload', $response->getData(''));
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiAccountLoginInvalid() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('user', 'admin');
        $request->setData('pass', 'invalid');

        $this->module->apiLogin($request, $response);
        self::assertEquals('warning', $response->getData('')['status']);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiAccountLogout() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('user', 'admin');
        $request->setData('pass', 'invalid');

        $this->module->apiLogout($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);
    }
}
