<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponses
{
    /**
     * Success Response.
     *
     * @param  mixed  $data
     * @param  int  $statusCode
     * @return JsonResponse
     */
    public function successResponse(mixed $data, string $message = '',int $statusCode = Response::HTTP_OK): JsonResponse
    {
        if (!$message) {
            $message = Response::$statusTexts[$statusCode];
          }

        $data = [
            'status' => true,
            'message' => $message,
            'data' => $data,
        ];

        return new JsonResponse($data, $statusCode);
    }

    /**
     * Error Response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $statusCode
     * @return JsonResponse
     */
    public function errorResponse(string $message = '', int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        if (!$message) {
            $message = Response::$statusTexts[$statusCode];
        }

        $data = [
            'status' => false,
            'message' => $message,
        ];

        return new JsonResponse($data, $statusCode);
    }

    /**
     * Response with status code 200.
     *
     * @param  mixed  $data
     * @return JsonResponse
     */
    public function okResponse(mixed $message): JsonResponse
    {

      $message = [
        'status' => true,
        'message' => $message,
      ];

      return new JsonResponse($message);
    }

    /**
     * Response with status code 201.
     *
     * @param  mixed  $data
     * @return JsonResponse
     */
    public function createdResponse(mixed $data): JsonResponse
    {
        return $this->successResponse($data, Response::HTTP_CREATED);
    }

    /**
     * Response with status code 204.
     *
     * @return JsonResponse
     */
    public function noContentResponse(): JsonResponse
    {
        return $this->successResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Response with status code 400.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function badRequestResponse(string $message = ''): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Response with status code 401.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function unauthorizedResponse(string $message = ''): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Response with status code 403.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function forbiddenResponse(string $message = ''): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Response with status code 404.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function notFoundResponse(string $message = ''): JsonResponse
    {
        return $this->errorResponse( $message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Response with status code 409.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function conflictResponse(string $message = ''): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_CONFLICT);
    }

    /**
     * Response with status code 422.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function unprocessableResponse(string $message = ''): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    public function respondWithToken($token, string $guard , $message = Null) {
        $data["token"] = $token;
        $data["token_type"] = "bearer";
        $data["expires_in"] = auth($guard)->factory()->getTTL() * 60;
        return $this->successResponse($data , $message);
    }
}
