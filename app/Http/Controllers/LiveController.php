<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiErrorException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InstagramAPI\Instagram;
use Throwable;

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

        /** @var Instagram $instagram */
        $instagram = app(Instagram::class);

        try {
            $loginResponse = $instagram->login(
                $request->input('username'),
                $request->input('password')
            );
        } catch (Throwable $e) {
            throw new ApiErrorException($e->getMessage(), 401);
        }

        if ($loginResponse != null && $loginResponse->isTwoFactorRequired()) {
            throw new ApiErrorException('Two factor auth is not supported.', 401);
        }

        $creationResponse = $instagram->live->create();
        if ($creationResponse->isOk() == false) {
            throw new ApiErrorException($creationResponse->getMessage(), 400);
        }

        $startingResponse = $instagram->live->start($creationResponse->getBroadcastId());
        if ($startingResponse->isOk() == false) {
            throw new ApiErrorException($startingResponse->getMessage(), 400);
        }

        return new JsonResponse([
            'broadcast_id' => $creationResponse->getBroadcastId(),
            'upload_url' => $creationResponse->getUploadUrl(),
            'info' => $instagram->live->getInfo($creationResponse->getBroadcastId()),
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function stop(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
            'broadcast_id' => 'required',
        ]);

        /** @var Instagram $instagram */
        $instagram = app(Instagram::class);

        try {
            $loginResponse = $instagram->login(
                $request->input('username'),
                $request->input('password')
            );
        } catch (Throwable $e) {
            throw new ApiErrorException($e->getMessage(), 401);
        }

        if ($loginResponse != null && $loginResponse->isTwoFactorRequired()) {
            throw new ApiErrorException('Two factor auth is not supported.', 401);
        }

        try {
            $response = $instagram->live->end($request->input('broadcast_id'));
        } catch (Throwable $e) {
            throw new ApiErrorException($e->getMessage(), 400);
        }

        return new JsonResponse([
            'status' => $response->getStatus(),
            'message' => $response->getMessage(),
        ], 200);
    }
}
