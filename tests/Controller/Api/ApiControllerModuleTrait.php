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

use Modules\Admin\Models\ModuleStatusUpdateType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;

trait ApiControllerModuleTrait
{
    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The status of a module can be updated')]
    public function testApiModuleStatusUpdate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('module', 'TestModule');

        $request->setData('status', ModuleStatusUpdateType::INSTALL);
        $this->module->apiModuleStatusUpdate($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);

        $request->setData('status', ModuleStatusUpdateType::ACTIVATE, true);
        $this->module->apiModuleStatusUpdate($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);

        $request->setData('status', ModuleStatusUpdateType::DEACTIVATE, true);
        $this->module->apiModuleStatusUpdate($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);

        $request->setData('status', ModuleStatusUpdateType::UNINSTALL, true);
        $this->module->apiModuleStatusUpdate($request, $response);
        self::assertEquals('ok', $response->getData('')['status']);

        $this->module->apiReInit($request, $response);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A missing module cannot be updated')]
    public function testApiModuleStatusUpdateEmptyModule() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;

        $request->setData('status', ModuleStatusUpdateType::INSTALL);
        $this->module->apiModuleStatusUpdate($request, $response);

        self::assertEquals(RequestStatusCode::R_403, $response->header->status);
        self::assertNull($response->getData('module_status_update'));
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A invalid module status cannot update a module')]
    public function testApiModuleStatusUpdateInvalidStatus() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('module', 'TestModule');
        $request->setData('status', 99);

        $this->module->apiModuleStatusUpdate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A invalid module cannot be updated')]
    public function testApiModuleStatusUpdateInvalidModule() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('module', 'invalid');
        $request->setData('status', ModuleStatusUpdateType::INSTALL);

        $this->module->apiModuleStatusUpdate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers \Modules\Admin\Controller\ApiController
     */
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('A module can be re-initialized')]
    public function testApiReInit() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;

        $routes = include __DIR__ . '/../../../../../Web/Api/Routes.php';
        $hooks  = include __DIR__ . '/../../../../../Web/Api/Hooks.php';

        $this->module->apiReInit($request, $response);

        $routes2 = include __DIR__ . '/../../../../../Web/Api/Routes.php';
        $hooks2  = include __DIR__ . '/../../../../../Web/Api/Hooks.php';

        self::assertEquals($routes, $routes2);
        self::assertEquals($hooks, $hooks2);
    }
}
