<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $rules = [
            'username' => ['required', 'min:2'],
            'password' => ['required', 'min:5'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = User::query()
            ->where('username', $validator->safe()->only('username'))
            ->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                if ($token = auth()->attempt($validator->validated())) {

                    return $this->respondWithToken($token, 'User login successfully');
                }
            } else {

                return response()->json([
                    'status' => false,
                    'message' => 'Username or password invalid'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {

            return response()->json([
                'status' => false,
                'message' => 'User not registered'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unauthorized'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out'
        ], Response::HTTP_OK);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh(), 'token updated successfully');
    }

    public function register(Request $request)
    {
        $rules = [
            'fullname' => ['required', 'min:2'],
            'username' => ['required', 'min:2'],
            'password' => ['required', 'confirmed', 'min:5'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = User::query()
            ->where('username', $validator->safe()->only('username'))
            ->first();

        if (!$user) {
            User::create($validator->validated());

            if ($token = auth()->attempt($validator->validated())) {

                return $this->respondWithToken($token, 'The user is successfully registered and logged in');
            }
        } else {

            return response()->json([
                'status' => false,
                'message' => 'User already exists'
            ], Response::HTTP_CONFLICT);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param  string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $message)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'message' => $message
        ], Response::HTTP_OK);
    }
}
