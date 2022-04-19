<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests\Controller\Api;

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Uri\HttpUri;

trait ApiControllerApplicationTrait
{
    /**
     * @covers Modules\Admin\Controller\Apicontroller
     * @group module
     */
    public function testApiInvalidAppplicationPathInstall() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('appSrc', 'invalid');

        $this->module->apiInstallApplication($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }
}
