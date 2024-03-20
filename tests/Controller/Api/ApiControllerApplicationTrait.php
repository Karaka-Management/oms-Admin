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

namespace Modules\Admin\tests\Controller\Api;

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;

trait ApiControllerApplicationTrait
{
    /**
     * @covers \Modules\Admin\Controller\Apicontroller
     * @group module
     */
    public function testApiInvalidAppplicationPathInstall() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('appSrc', 'invalid');

        $this->module->apiInstallApplication($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }
}
