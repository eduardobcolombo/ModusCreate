<?php

namespace App\Contracts;

use Slim\Http\{
    Response,
    Request
};

/**
 *    Interface VehiclesInterface
 *
 */
interface VehiclesInterface
{
    /**
     * Get a vehicles
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function find(Request $request, Response $response, $args);

}