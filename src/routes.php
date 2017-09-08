<?php
// Routes


// Endpoint GET  http://localhost:8080/vehicles/<MODEL YEAR>/<MANUFACTURER>/<MODEL>
// Endpoing POST http://localhost:8080/vehicles
$app->map(['GET', 'POST'], '/vehicles[/{params:.*}]', function ($request, $response, $args) {

    // Getting parameters of query
    $params = explode('/', $request->getAttribute('params'));
    $modelYear = $params[0];
    $manufacturer = $params[1];
    $model = $params[2];
    $count = 0;

    // Checking if the method is post and reading the json data
    if ($request->isPost()) {
        $data = json_decode($request->getBody(), true);
        $modelYear = $data['modelYear'];
        $manufacturer = $data['manufacturer'];
        $model = $data['model'];
//        return $response->withJson($data);
    }

    // Reading NHTSA API to get information
    $client = new \GuzzleHttp\Client();
    $res = $client->request(
        'GET',
        "https://one.nhtsa.gov/webapi/api/SafetyRatings/modelyear/$modelYear/make/$manufacturer/model/$model?format=json"
    );
    // Reading the result and creating response
    $body = json_decode($res->getBody());
    $count = ($body->Count >  0) ? $body->Count : 0;
    $results = [];
    if ($count>0) {
        foreach($body->Results as $vehicle) {
            $res = null;
            $res['Description'] = $vehicle->VehicleDescription;
            $res['VehicleId'] = $vehicle->VehicleId;
            array_push($results,$res);
        }
    }
    // Formatting response
    $return = [
        'Count' => $count,
        'Results' => $results
    ];
    // Returning response with json data
    return $response->withJson($return);

});






