<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Helpers\ApiFormatter;
use App\Models\LogModel;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $user = null;
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                $user = null;
            }

            $filteredRequest = ApiFormatter::filterSensitiveData($request->all());
            LogModel::create([
                'user_id'      => $user ? $user->id : null,
                'log_method'   => $request->method(),
                'log_url'      => $request->fullUrl(),
                'log_ip'       => $request->ip(),
                'log_request'  => json_encode($filteredRequest),
                'log_response' => json_encode(ApiFormatter::createJson(404, 'Not Found', 'Route not found')),
            ]);

            return response()->json(ApiFormatter::createJson(404, 'Not Found', 'Route not found'), 404);
        }

        return parent::render($request, $exception);
    }
}
