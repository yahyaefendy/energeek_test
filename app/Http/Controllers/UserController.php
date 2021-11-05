<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;

class UserController extends Controller
{
    public function login(Request $request) {
        $cridentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($cridentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
            User::where('email', $request->email)->update(['api_token' => $token]);

            return response()->json(compact('token'));
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'phone'     => 'required|string|max:12',
            'address'   => 'string|max:255',
            'role'      => 'required|string',
            'api_token' => 'string|max:100',
            'created_by'=> 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'address'   => $request->address,
            'role'      => $request->role,
            'api_token' => $request->api_token,
            'created_by'=> $request->created_by
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function getAuthenticatedUser() {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

    public function index() {
        $users = User::all();

        return response()->json(compact('users'));
    }

    public function show($id) {
        $user = User::findOrFail($id);

        return response()->json(compact('user'));
    }

    public function update(Request $request, $id) {
        try {
            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|max:255',
                'username'  => 'required|string|max:255',
                'email'     => 'required|string|email|max:255|unique:users,id,'.$request->email,
                'phone'     => 'required|string|max:12',
                'address'   => 'string|max:255',
                'role'      => 'required|string',
                'updated_by'=> 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user = User::findOrFail($id);
            $user->name         = $request->name;
            $user->username     = $request->username;
            $user->email        = $request->email;
            $user->phone        = $request->phone;
            $user->address      = $request->address;
            $user->role         = $request->role;
            $user->updated_by   = $request->updated_by;
            $user->updated_at   = Carbon::now();
            $user->update();

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user'), 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error', 
                'message' => $ex->getMessage()], 
                500
            );
        }
    }

    public function delete(Request $request, $id) {
        try {
            $validator = Validator::make($request->all(), [
                'user_delete' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            
            $user = User::findOrFail($id);
            $user->deleted_at = Carbon::now();
            $user->deleted_by = $request->user_delete;
            $user->update();

            return response()->json([
                'status' => 'Success', 
                'message' => sprintf('Deleted user %s', $user->name)
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error', 
                'message' => $ex->getMessage()], 
                500
            );
        }
    }
}
