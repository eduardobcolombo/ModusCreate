<?php
// Routes

// Endpoing POST http://localhost:8080/vehicles
// Endpoint GET  http://localhost:8080/vehicles/<MODEL YEAR>/<MANUFACTURER>/<MODEL>
// Endpoing GET  http://localhost:8080/vehicles/<MODEL YEAR>/<MANUFACTURER>/<MODEL>?withRating=true
$app->map(['GET', 'POST'], '/vehicles[/{params:.*}]', function ($request, $response, $args) {

    // Getting parameters of query
    $params = explode('/', $request->getAttribute('params'));
    // If GET parameters exists set on variables
    $modelYear = (isset($params[0])) ? $params[0] : null;
    $manufacturer = (isset($params[1])) ? $params[1] : null;
    $model = (isset($params[2])) ? $params[2] : null;
    // Initializing variables for return
    $count = 0;
    $results = [];
    // possible values for CrashRating
    $validCrashRating = ["Not Rated", "0", "1", "2", "3", "4", "5"];
    // Checking if withRating parameter is true and setting $withRating
    $withRating = ($request->getQueryParam('withRating') == 'true') ? true : false;

    // Checking if the method is post and reading the json data
    if ($request->isPost()) {
        $data = json_decode($request->getBody(), true);
        // If POST parameters exists set on variables
        $modelYear = (isset($data['modelYear'])) ? (integer) $data['modelYear'] : null;
        $manufacturer = (isset($data['manufacturer'])) ? $data['manufacturer'] : null;
        $model = (isset($data['model'])) ? $data['model'] : null;
    }
    // Checking if modelYear param is valid
    if (is_numeric($modelYear)) {
        // Reading NHTSA API to get information
        $client = new \GuzzleHttp\Client();
        $res = $client->request(
            'GET',
            "https://one.nhtsa.gov/webapi/api/SafetyRatings/modelyear/$modelYear/make/$manufacturer/model/$model?format=json"
        );
        // Reading the result and creating response
        $body = json_decode($res->getBody());
        $count = ($body->Count > 0) ? $body->Count : 0;
        // cheking if there are some result
        if ($count > 0) {
            // Loop to read all vehicles results
            foreach ($body->Results as $vehicle) {
                $res = null;
                // checkinf if is need to show Crash Rating
                if ($withRating === true) {
                    $resRating = $client->request(
                        'GET',
                        "https://one.nhtsa.gov/webapi/api/SafetyRatings/VehicleId/$vehicle->VehicleId?format=json"
                    );
                    $bodyRating = json_decode($resRating->getBody());
                    // Setting Crash Rating value on return variable
                    // Handling valid values
                    $res['CrashRating'] = (in_array($bodyRating->Results[0]->OverallRating, $validCrashRating)) ? $bodyRating->Results[0]->OverallRating : "Not Rated";
                }
                $res['Description'] = $vehicle->VehicleDescription;
                $res['VehicleId'] = $vehicle->VehicleId;

                array_push($results, $res);
            }
        }
    }
    // Formatting response
    $return = [
        'Count' => $count,
        'Results' => $results
    ];
    // Returning response with json data
    return $response->withJson($return,200);

});
