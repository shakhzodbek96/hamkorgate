<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\Helpers\Response;

class TestMethods extends Response
{
    public function success(array $params)
    {
        $this->validate($params, [
            'name' => 'required|string|max:255',
        ]);

        return self::successResponse(
            [
                'message' => "Hello, {$params['name']}! This is a success response example.",
                'partner' => $params,
            ]
        );
    }

    public function fail(array $params)
    {
        $this->validate($params, [
            'message' => 'required|string|max:255',
        ]);

        return self::errorResponse("This is a failure response example: {$params['message']}");
    }
}
