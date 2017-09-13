<?php
declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Slim\Http\{
    Response,
    Request
};
use App\Contracts\VehiclesInterface;
use GuzzleHttp\Client as GuzzleClient;

class VehicleController implements VehiclesInterface
{
    /**
     * The year of model MUST be an INTEGER
     * @var integer
     */    
    private $modelYear = null;
    /**
     * The manufacturer should be a string
     * @var String
     */
    private $manufacturer = null;
    /**
     * The model should be a string
     * @var string
     */
    private $model = null;
    /**
     * The argument to get with rating
     * @var bool
     */
    private $withRating = false;
    /**
     * The count of results found MUST be an INTEGER
     * @var integer
     */
    private $count = 0;
    /**
     * The results MUST be an array
     * @var array
     */
    private $results = [];
    /**
     * The possible values for CrashRating
     * @var array
     */
    const VALID_CRASH_RATING = ["Not Rated", "0", "1", "2", "3", "4", "5"];
    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * VehicleController constructor.
     */
    public function __construct()
    {
        $this->client = new GuzzleClient();
    }

    /**
     * Find the vehicles match
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function find(Request $request, Response $response, $args) : Response
    {
        // Set the values on speficic variables
        $this->setValues($request);
        // Check if the data is valid, if not return with json without error
        if ($this->validate() === false) return $this->formatReturn($response);
        // Do a call for a external API to get information
        $res = $this->callExtAPI();
        // Reading the result and creating response
        $body = json_decode((string)$res->getBody());
        $this->count = ($body->Count > 0) ? $body->Count : 0;
        // cheking if there are some result
        if ($this->count > 0) $this->readAllVehiclesData($body);

        return $this->formatReturn($response);
    }

    /**
     * Deletes a new object
     * @param $id
     * @return mixed
     */
    private function formatReturn(Response $response): Response
    {
        // Returning response with json data formatted
        return $response->withJson([
            'Count' => $this->count,
            'Results' => $this->results
        ],200);
    }

    /**
     * @param null $vehicleId
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function callExtAPI($vehicleId = null): ResponseInterface {
        // Reading NHTSA API to get information
        if (is_null($vehicleId)) {
            return $this->client->request(
                'GET',
                "https://one.nhtsa.gov/webapi/api/SafetyRatings/modelyear/$this->modelYear/make/$this->manufacturer/model/$this->model?format=json"
            );
        }
        // Reading NHTSA API to get vehicle information
        return $this->client->request(
            'GET',
            "https://one.nhtsa.gov/webapi/api/SafetyRatings/VehicleId/$vehicleId?format=json"
        );
    }

    /**
     * Set values of variables
     * @param Request $request
     */
    private function setValues(Request $request)
    {
        // Checking if the method is post and reading the json data
        if ($request->isPost() === true) {
            $data = json_decode((string)$request->getBody(), true);
            // If POST parameters exists set on variables
            $this->modelYear = filter_var($data['modelYear'], FILTER_SANITIZE_NUMBER_INT) ?? null;
            $this->manufacturer = filter_var($data['manufacturer'], FILTER_SANITIZE_STRING) ??  null;
            $this->model = filter_var($data['model'], FILTER_SANITIZE_STRING) ??  null;
        } else {
            // If GET parameters exists set on variables
            $this->modelYear = filter_var($request->getAttribute('modelYear'), FILTER_SANITIZE_NUMBER_INT) ?? null;
            $this->manufacturer = filter_var($request->getAttribute('manufacturer'), FILTER_SANITIZE_STRING) ?? null;
            $this->model = filter_var($request->getAttribute('model'), FILTER_SANITIZE_STRING) ?? null;

            // Checking if withRating parameter is true and setting $withRating
            $this->withRating = (filter_var($request->getQueryParam('withRating'), FILTER_SANITIZE_STRING) === 'true') ? true : false;
        }
       }
    /**
     * Read all vehicles data and set $this->results variable
     * @param $body
     */
    private function readAllVehiclesData($body) {
        // Loop to read all vehicles results
        foreach ($body->Results as $vehicle) {
            $res = null;
            // check if is need to show Crash Rating
            if ($this->withRating === true) {
                // Do a call for a external API to get information about CrashRating of vehicle selected
                $resRating = $this->callExtAPI($vehicle->VehicleId);
                $bodyRating = json_decode((string)$resRating->getBody());
                // Setting Crash Rating value on return variable
                // Handling valid values
                $res['CrashRating'] = (in_array($bodyRating->Results[0]->OverallRating, self::VALID_CRASH_RATING)) ? $bodyRating->Results[0]->OverallRating : "Not Rated";
            }
            $res['Description'] = $vehicle->VehicleDescription;
            $res['VehicleId'] = $vehicle->VehicleId;
            // Push on array the new record
            array_push($this->results, $res);
        }
        unset($res);        
    }

    /**
     * Validate if Request params is fine
     * @return bool
     */
    private function validate(): bool
    {
        // Checking if modelYear param is valid
        if (is_numeric($this->modelYear) === false)  return false;

        return true;
    }
}