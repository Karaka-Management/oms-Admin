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

use Modules\Admin\Models\SettingsEnum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Uri\HttpUri;

trait ApiControllerSettingsTrait
{
    /**
     * @testdox Application settings can be read from the database
     * @covers Modules\Admin\Controller\ApiController
     * @group module
     */
    public function testApiSettingsGet() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('name', SettingsEnum::PASSWORD_INTERVAL);

        $this->module->apiSettingsGet($request, $response);
        self::assertEquals('90', $response->get('')['response']->content);
    }

    /**
     * @testdox Application settings can be set in the database
     * @covers Modules\Admin\Controller\ApiController
     * @group module
     */
    public function testApiSettingsSet() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('settings', \json_encode([['name' => SettingsEnum::PASSWORD_INTERVAL, 'content' => '60']]));
        $this->module->apiSettingsSet($request, $response);

        $request->setData('name', SettingsEnum::PASSWORD_INTERVAL);
        $this->module->apiSettingsGet($request, $response);
        self::assertEquals('60', $response->get('')['response']->content);

        $request->setData('settings', \json_encode([['name' => SettingsEnum::PASSWORD_INTERVAL, 'content' => '90']]), true);
        $this->module->apiSettingsSet($request, $response);
    }

    /**
     * @covers Modules\Admin\Controller\ApiController
     * @group module
     */
    public function testApiAccountLocalizationLoadSet() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('account_id', 1);
        $request->setData('load', true);
        $request->setData('localization_load', 'de_DE');
        $this->module->apiSettingsAccountLocalizationSet($request, $response);

        $l11n = $response->get('')['response'];
        self::assertEquals($l11n->language, 'de');

        $request->setData('localization_load', 'en_US', true);
        $this->module->apiSettingsAccountLocalizationSet($request, $response);

        $l11n = $response->get('')['response'];
        self::assertEquals($l11n->language, 'en');
    }

    /**
     * @covers Modules\Admin\Controller\ApiController
     * @group module
     */
    public function testApiAccountLocalizationSet() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('account_id', 1);

        $data = \json_decode('{"settings_country":"US","settings_language":"en","settings_temperature":"celsius","settings_timezone":"America\/New_York","settings_timeformat_vs":"d.m","settings_timeformat_s":"m.y","settings_timeformat_m":"Y.m.d","settings_timeformat_l":"Y.m.d h:i","settings_timeformat_vl":"Y.m.d h:i:s","settings_currency":"EUR","settings_currencyformat":"0","settings_decimal":".","settings_thousands":",","settings_precision_vs":"0","settings_precision_s":"1","settings_precision_m":"2","settings_precision_l":"3","settings_precision_vl":"5","settings_weight_vl":"mg","settings_weight_l":"g","settings_weight_m":"kg","settings_weight_h":"t","settings_weight_vh":"t","settings_speed_vs":"mps","settings_speed_s":"ms","settings_speed_m":"kph","settings_speed_f":"kph","settings_speed_vf":"mach","settings_speed_sea":"mpd","settings_length_vs":"micron","settings_length_s":"mm","settings_length_m":"cm","settings_length_l":"m","settings_length_vl":"km","settings_length_sea":"mi","settings_area_vs":"micron","settings_area_s":"mm","settings_area_m":"cm","settings_area_l":"m","settings_area_vl":"km","settings_volume_vs":"mul","settings_volume_s":"ml","settings_volume_m":"l","settings_volume_l":"cm","settings_volume_vl":"m","settings_volume_teaspoon":"Metric tsp","settings_volume_tablespoon":"Metric tblsp","settings_volume_glass":"Metric cup"}', true);

        foreach ($data as $key => $value) {
            $request->setData($key, $value);
        }

        $this->module->apiSettingsAccountLocalizationSet($request, $response);

        $l11n = $response->get('')['response'];
        self::assertEquals($l11n->getCurrency(), 'EUR');
    }

    /**
     * @covers Modules\Admin\Controller\ApiController
     * @group module
     */
    public function testInvalidPermissionAccountLocalizationSet() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 2;
        $request->setData('account_id', 1);
        $this->module->apiSettingsAccountLocalizationSet($request, $response);

        self::assertEquals(RequestStatusCode::R_403, $response->header->status);
    }
}
