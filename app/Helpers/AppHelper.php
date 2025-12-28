<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use App\Models\App;
use App\Models\AppHistory;
use Illuminate\Validation\Rule;

class AppHelper
{
    static function appGenerate($request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        try {
            App::create([
                'name'        => $request->input('name'),
                'price'       => $request->input('price'),
                'status'      => $request->input('status'),
                'registrar'  => auth()->user()->user_id,
            ]);

            $app = App::where('name', $request->input('name'))->where('price', $request->input('price'))->first();

            AppHistory::create([
                'app_id' => $app->edit_id,
                'user'   => auth()->user()->user_id,
                'type'   => 'Create',
            ]);

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', "<b>App</b> " . $request->input('name'), $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
    }

    static function appEdit($request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        $app = App::where('edit_id', $request->input('edit_id'))->first();

        if (empty($app)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'app_id'  => [
                'required',
                'string',
                'min:10',
                'max:36',
                Rule::unique('apps', 'app_id')->ignore($app->edit_id, 'edit_id')
            ],
            'name'    => [
                'required',
                'string',
                'min:6',
                'max:50',
                Rule::unique('apps', 'name')->ignore($app->edit_id, 'edit_id')
            ],
        ]);

        try {
            $app->update([
                'app_id'      => $request->input('app_id'),
                'name'        => $request->input('name'),
                'price'       => $request->input('price'),
                'status'      => $request->input('status'),
            ]);

            AppHistory::create([
                'app_id' => $app->edit_id,
                'user'   => auth()->user()->user_id,
                'type'   => 'Update',
            ]);

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', "<b>App</b> " . $request->input('name'), $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 202', $errorMessage),
            ]);
        }
    }
}