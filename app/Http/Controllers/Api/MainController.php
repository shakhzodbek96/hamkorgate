<?php

namespace App\Http\Controllers\Api;

use App\Services\Helpers\Response;
use Illuminate\Http\Request;

class MainController extends Response
{
    // Request handler
    public function index(Request $request)
    {
        // Void validator function
        $this->validate($request->all(), [
            'method' => 'required',
            'params' => 'array|max:250'
        ]);

        #get version from request url
        $version = $request->segment(2) ?? 'v1';
        $params = $request->params ?? [];
        $params['partner_id'] = $request->get('partner')['id'] ?? null;
        $params['partner'] = $request->get('partner') ?? null;
        // Call method
        return $this->call_method($request->get('method'), $params, $version);
    }

    //define method and call it
    public function call_method(string $method = '', array $params = [], string $version = 'v1')
    {
        $original_method = $method;
        $point = strpos($method, '.');
        $controller = substr($method, 0, $point);
        $method = substr($method, $point ? ++$point : 0);
        $class = "\\" . __NAMESPACE__ . "\\$version\\" . ucfirst($controller) . 'Methods';
        $method = str_replace('.', '_', $method);

        // existing of method in this class
        if (method_exists($class, $method)) {
            try {
                return (new $class())->{$method}($params);
            } catch (\Exception $exception) {
                return self::errorResponse("Exception error! " . $exception->getMessage() . ". Line: " . $exception->getLine());
            }

        } else {
            return $this->errorMethodUndefined($original_method);
        }
    }
}
