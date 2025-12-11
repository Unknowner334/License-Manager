<?php

namespace App\Helpers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserHelper
{
    static function userGenerate($request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        try {
            manager_limit($request->input('role'));
            $username = $request->input('username');
            
            User::create([
                'name'        => $request->input('name'),
                'username'    => $request->input('username'),
                'password'    => $request->input('password'),
                'status'      => $request->input('status'),
                'role'        => $request->input('role'),
                'registrar'   => auth()->user()->user_id,
            ]);

            $msg = str_replace(':flag', "<strong>User</strong> $username", $successMessage);
            return response()->json([
                'status' => 0,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 202', $errorMessage),
            ]);
        }
    }

    static function userEdit($request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        try {
            $username = $request->input('username');
            $user = User::where('user_id', $request->input('user_id'))->first();

            $request->validate([
                'username' => 'required|string|min:8|max:50',Rule::unique('users', 'username')->ignore($user->user_id, 'user_id'),
            ]);

            if (empty($user)) {
                return back()->withErrors(['name' => str_replace(':info', 'Error Code 202', $errorMessage),])->onlyInput('name');
            }

            manager_limit($user->role);
            psueAction($user);

            if ($request->has('new_password')) {
                $request->validate([
                    'password' => 'required|string|confirmed|min:8|max:50',
                ]);

                $user->update([
                    'name'        => $request->input('name'),
                    'username'    => $request->input('username'),
                    'password'    => $request->input('password'),
                    'status'      => $request->input('status'),
                    'permissions' => $request->input('role'),
                ]);
            } else {
                $user->update([
                    'name'        => $request->input('name'),
                    'username'    => $request->input('username'),
                    'status'      => $request->input('status'),
                    'permissions' => $request->input('role'),
                ]);
            }

            $msg = str_replace(':flag', "<strong>User</strong> $username", $successMessage);
            return response()->json([
                'status' => 0,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
    }
}