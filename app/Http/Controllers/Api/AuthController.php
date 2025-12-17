<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Helpers\ApiFormatter;
use App\Models\User;

use Carbon\Carbon;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $params = $request->all();

        $validator = Validator::make($params, [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all()), 400);
        }

        $user = User::where('email', $params['email'])->first();
        if (!$user) return response()->json(ApiFormatter::createJson(404, 'Account not found'), 404);

        if (!Hash::check($params['password'], $user->password)) {
            return response()->json(ApiFormatter::createJson(401, 'Password does not match'), 401);
        }

        $token = JWTAuth::fromUser($user);
        $expiration = Carbon::now()->addMinutes(JWTAuth::factory()->getTTL());

        return response()->json(ApiFormatter::createJson(200, 'Login successful', [
            'type'    => 'Bearer',
            'token'   => $token,
            'expires' => $expiration->format('Y-m-d H:i:s')
        ]));
    }

    // Get current user info (cek token blacklist juga)
    public function me()
    {
        try {
            JWTAuth::factory()->setBlacklistEnabled(true);
            $user = JWTAuth::parseToken()->authenticate();
            $payload = JWTAuth::getPayload(JWTAuth::getToken());
            $expiration = date('Y-m-d H:i:s', $payload->get('exp'));

            return response()->json(ApiFormatter::createJson(200, 'Logged in User', [
                'name'  => $user->name,
                'email' => $user->email,
                'exp'   => $expiration
            ]));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(401, 'Token invalid or expired'), 401);
        }
    }

    // Refresh token
    public function refresh()
    {
        try {
            // Ambil token lama
            $oldToken = JWTAuth::getToken();

            // Generate token baru dan otomatis blacklist token lama
            $newToken = JWTAuth::refresh($oldToken, true);

            $expirationDateTime = Carbon::now()->addMinutes(JWTAuth::factory()->getTTL());

            $info = [
                'type'    => 'Bearer',
                'token'   => $newToken,
                'expires' => $expirationDateTime->format('Y-m-d H:i:s'),
            ];

            return response()->json(ApiFormatter::createJson(200, 'Successfully refreshed', $info), 200);

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(401, 'Failed to refresh token', $e->getMessage()), 401);
        }
    }

    // Logout
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken()); // token masuk blacklist
            return response()->json(ApiFormatter::createJson(200, 'Successfully logged out'));

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, 'Failed to logout', $e->getMessage()));
        }
    }
}
