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

namespace Modules\Admin\tests\Controller\Api;

use phpOMS\Account\GroupStatus;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;

trait ApiControllerGroupTrait
{
    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user group can be returned')]
    public function testApiGroupGet() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('id', '3');

        $this->module->apiGroupGet($request, $response);

        self::assertEquals('admin', $response->getDataArray('')['response']->name);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user group can be updated')]
    public function testApiGroupSet() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('id', '3');
        $request->setData('name', 'root');

        $this->module->apiGroupUpdate($request, $response);
        $this->module->apiGroupGet($request, $response);

        self::assertEquals('root', $response->getDataArray('')['response']->name);

        $request->setData('name', 'admin', true);
        $this->module->apiGroupUpdate($request, $response);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user group can be found by name')]
    public function testApiGroupFind() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('search', 'admin');

        $this->module->apiGroupFind($request, $response);
        self::assertCount(1, $response->getData(''));
        self::assertEquals('admin', $response->getData('')[0]->name);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user group can be created and deleted')]
    public function testApiGroupCreateDelete() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('name', 'test');
        $request->setData('status', GroupStatus::INACTIVE);
        $request->setData('description', 'test description');

        $this->module->apiGroupCreate($request, $response);

        self::assertEquals('test', $response->getDataArray('')['response']->name);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);

        // test delete
        $request->setData('id', $response->getDataArray('')['response']->id);
        $this->module->apiGroupDelete($request, $response);

        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user group can be created and deleted')]
    public function testApiGroupDeleteAdminInvalid() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;

        $request->setData('id', '3');
        $this->module->apiGroupDelete($request, $response);
        self::assertEquals('warning', $response->getData('')['status']);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A invalid user group cannot be created')]
    public function testApiGroupCreateInvalid() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('status', 999);
        $request->setData('description', 'test description');

        $this->module->apiGroupCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user can be added to a user group')]
    public function testApiAddRemoveAccountToGroup() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('group', 1);
        $request->setData('group-list', '1');

        $this->module->apiAddAccountToGroup($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);

        // remove
        $response = new HttpResponse();

        $this->module->apiDeleteAccountFromGroup($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiRemoveAdminAccountFromAdminGroup() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('group', 3);
        $request->setData('group-list', '1');

        $this->module->apiDeleteAccountFromGroup($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A user and user group can be found by name')]
    public function testApiAccountGroupFind() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('search', 'admin');

        $this->module->apiAccountGroupFind($request, $response);
        self::assertCount(2, $response->getData(''));
        self::assertEquals('admin', $response->getData('')[0]['name'][0] ?? '');
        self::assertEquals('admin', $response->getData('')[1]['name'][0] ?? '');
    }
}
