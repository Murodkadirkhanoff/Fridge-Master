<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Api Documentation",
 *    version="1.0.0",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success($data, $code = 200)
    {
        return response()->json(['result' => $data, 'code' => $code], $code);
    }

    protected function error($message, $code)
    {
        if (!is_array($message)) {
            return response()->json(['error' => [$message], 'code' => $code], $code);
        }
        return response()->json(['error' => $message, 'code' => $code], $code);
    }
}
