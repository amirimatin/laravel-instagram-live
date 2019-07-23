<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Class LiveController
 *
 * @package App\Http\Controllers
 */
class LiveController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function start(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $script = config('live.script');
        $username = $request->input('username');
        $password = $request->input('password');
        $output = [];
        $returnVar = null;

        exec("php $script 1 $username $password", $output, $returnVar);

        if (Str::startsWith($output[0], 'Error While Logging in to Instagram')) {
            return new JsonResponse([
                'error' => 'Authorization failed.'
            ], 401);
        } elseif (Str::startsWith($output[0], '*') == false) {
            return new JsonResponse([
                'error' => $output[0]
            ], 500);
        }

        $response = explode('*', $output[0]);
        $liveId = $response[1];
        $rtmpUrl = $response[2];
        $streamKey = $response[3];
        $httpStatus = 200;

        return new JsonResponse([
            'live_id' => $liveId,
            'stream_key' => $streamKey,
            'rtmp_url' => $rtmpUrl,
        ], $httpStatus);
    }
}
