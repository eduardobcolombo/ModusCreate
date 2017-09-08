<?php

namespace Tests\Functional;

use Slim\Http\Environment;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;
use Slim\Http\Headers;
use Slim\Http\RequestBody;


class RequirementsTest extends BaseTestCase
{

    /**
     * @test
     * Test if the GET request with URL full return fine to Requirement01
     */
    public function testGetVehiclesWithGETAndAllParamsRequirement1_1()
    {
        $response = $this->runApp('GET', '/vehicles/2015/Audi/A3');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody());
        $this->assertEquals(4, $body->Count);
        $this->assertEquals(4, count($body->Results));
        $body = json_decode($response->getBody(),true);
        $this->assertFalse(array_key_exists('CrashRating', $body['Results'][0]));
        $this->assertTrue(array_key_exists('Description', $body['Results'][0]));
        $this->assertTrue(array_key_exists('VehicleId', $body['Results'][0]));
    }

    /**
     * @test
     * Test if the GET request with URL full return fine to Requirement01
     */
    public function testGetVehiclesWithGETAndAllParamsRequirement1_2()
    {
        $response = $this->runApp('GET', '/vehicles/2015/Toyota/Yaris');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody());
        $this->assertEquals(2, $body->Count);
        $this->assertEquals(2, count($body->Results));
        $body = json_decode($response->getBody(),true);
        $this->assertFalse(array_key_exists('CrashRating', $body['Results'][0]));
        $this->assertTrue(array_key_exists('Description', $body['Results'][0]));
        $this->assertTrue(array_key_exists('VehicleId', $body['Results'][0]));

    }

    /**
     * @test
     * Test if the GET request with URL full return fine to Requirement01 with empty result
     */
    public function testGetVehiclesWithGETAndAllParamsRequirement1_3()
    {
        $response = $this->runApp('GET', '/vehicles/2015/Ford/Crown Victoria');
        $this->assertEquals(200, $response->getStatusCode());
        $bodyExpected = json_encode(["Count"=>0, "Results"=>[]]);
        $this->assertEquals($bodyExpected, $response->getBody());
        $body = json_decode($response->getBody());

    }

    /**
     * @test
     * Test if the GET request with URL full return fine to Requirement01 with empty result without Year param
     */
    public function testGetVehiclesWithGETAndWithoutYearParam1_4()
    {
        $response = $this->runApp('GET', '/vehicles/undefined/Ford/Fusion');
        $this->assertEquals(200, $response->getStatusCode());
        $bodyExpected = json_encode(["Count"=>0, "Results"=>[]]);
        $this->assertEquals($bodyExpected, $response->getBody());

    }
    /**
     * @test
     * Test if the POST request with URL vehicles with JSON request
     */
    public function testPostVehiclesWithJsonData2_1()
    {
        $data = (object) json_encode(["manufacturer"=>"Honda","model"=> "Accord"]);
        $response = $this->runApp('POST', '/vehicles', $data);
        $this->assertEquals(200, $response->getStatusCode());
        $bodyExpected = json_encode(["Count"=>0, "Results"=>[]]);
        $this->assertEquals($bodyExpected, $response->getBody());

    }

    /**
     * @test
     * Test if the GET request with URL full return fine to Requirement03
     * Test if return with CrashRating true
     */
    public function testGetVehiclesWithGETAndAllParamsRequirement3_1()
    {
        $response = $this->runApp('GET', '/vehicles/2015/Audi/A3?withRating=true');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody());
        $this->assertEquals(4, $body->Count);
        $this->assertEquals(4, count($body->Results));
        $body = json_decode($response->getBody(),true);
        $this->assertTrue(array_key_exists('CrashRating', $body['Results'][0]));
        $this->assertTrue(array_key_exists('Description', $body['Results'][0]));
        $this->assertTrue(array_key_exists('VehicleId', $body['Results'][0]));
    }

    /**
     * @test
     * Test if the GET request with URL full return fine to Requirement03
     * Test if return with CrashRating false
     */
    public function testGetVehiclesWithGETAndAllParamsRequirement3_2()
    {
        $response = $this->runApp('GET', '/vehicles/2015/Audi/A3?withRating=false');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody());
        $this->assertEquals(4, $body->Count);
        $this->assertEquals(4, count($body->Results));
        $body = json_decode($response->getBody(),true);
        $this->assertFalse(array_key_exists('CrashRating', $body['Results'][0]));
        $this->assertTrue(array_key_exists('Description', $body['Results'][0]));
        $this->assertTrue(array_key_exists('VehicleId', $body['Results'][0]));
    }

    /**
     * @test
     * Test if the GET request with URL full return fine to Requirement03
     * Test if return with CrashRating bananas
     */
    public function testGetVehiclesWithGETAndAllParamsRequirement3_3()
    {
        $response = $this->runApp('GET', '/vehicles/2015/Audi/A3?withRating=bananas');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody());
        $this->assertEquals(4, $body->Count);
        $this->assertEquals(4, count($body->Results));
        $body = json_decode($response->getBody(),true);
        $this->assertFalse(array_key_exists('CrashRating', $body['Results'][0]));
        $this->assertTrue(array_key_exists('Description', $body['Results'][0]));
        $this->assertTrue(array_key_exists('VehicleId', $body['Results'][0]));
    }


}