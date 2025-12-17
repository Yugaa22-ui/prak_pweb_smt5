<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ApiFormatter;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class JwtAuthenticate
{
    public function handle($request, Closure $next)
{
    try {
        $token = JWTAuth::parseToken()->getToken();      // ambil token
        JWTAuth::factory()->setBlacklistEnabled(true);   // aktifkan blacklist
        JWTAuth::parseToken()->authenticate();           // cek user + token valid

    } catch (TokenExpiredException $e) {
        return response()->json(ApiFormatter::createJson(401, 'Token has expired'), 401);

    } catch (TokenInvalidException $e) {
        return response()->json(ApiFormatter::createJson(401, 'Token is invalid'), 401);

    } catch (TokenBlacklistException $e) {
        return response()->json(ApiFormatter::createJson(401, 'Token has been blacklisted'), 401);

    } catch (JWTException $e) {
        return response()->json(ApiFormatter::createJson(401, 'Authorization Token missing or invalid'), 401);
    }

    return $next($request);
}
}
