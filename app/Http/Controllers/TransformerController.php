<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Repositories\Facade;

class TransformerController extends Controller
{
    public function transform(Request $request)
    {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $method = $request->method();

        $message = $this->getRouteExploded([], $routeName);
        $message['method'] = $method;
        $message['request'] = $request;

        $message = array_merge($message, $this->getParameters($currentRoute, $request));

        $facade = new Facade($message);
        $result = $facade->execute();

        return response()->json($result['response'], $result['response']['statusCode'] ?? 200);
    }

    public function getRouteExploded($message, $routeName)
    {
        $exp_arr = explode(".", $routeName);
        if (count($exp_arr) === 2) {
            $message["facade"] = $exp_arr[0];
            $message["function"] = $exp_arr[1];
        }
        return $message;
    }

    private function getParameters($currentRoute, Request $request)
    {
        $parameters = [];

        $urlParameters = $currentRoute->parameters();
        if (count($urlParameters) > 0) {
            $parameters['urlParameters'] = $urlParameters;
        }

        $queryParameters = $request->query();
        if (count($queryParameters) > 0) {
            $parameters['queryParameters'] = $queryParameters;
        }

        if ($request->isMethod('POST')) {
            $bodyParameters = $request->all();
            if (count($bodyParameters) > 0) {
                $parameters['bodyParameters'] = $bodyParameters;
            }
        }

        return $parameters;
    }
}
