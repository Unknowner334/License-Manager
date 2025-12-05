<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\App;
use App\Models\AppHistory;
use Illuminate\Validation\Rule;

class AppController extends Controller
{
    public function applist(Request $request) {
        return view('App.list');
    }

    public function appdata() {
        $apps = App::get();

        $data = $apps->map(function ($app) {
            $currency = Config::get('messages.settings.currency');
            $created = Controller::timeElapsed($app->created_at);
            $price = number_format($app->price);
            $appStatus = Controller::statusColor($app->status);
            $licenses = 0;

            $ids = [$app->edit_id, $app->app_id, $app->name];

            foreach ($app->licenses as $license) {
                $licenses += 1;
            }

            return [
                'id'        => $app->id,
                'ids'       => $ids,
                'name'      => "<span class='align-middle badge fw-normal text-$appStatus fs-6 px-3'>$app->name</span>",
                'licenses'  => "$licenses License",
                'registrar' => Controller::userUsername($app->registrar),
                'created'   => "<i class='align-middle badge fw-normal text-dark fs-6'>$created</i>",
                'price'     => "$price$currency",
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $data
        ]);
    }

    public function appgenerate() {
        $errorMessage = Config::get('messages.error.validation');

        parent::require_ownership(1);

        return view('App.generate');
    }

    public function appgenerate_action(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        parent::require_ownership(1);

        $request->validate([
            'name'    => 'required|string|unique:apps,name|min:6|max:50',
            'price'   => 'required|integer|min:1|max:300000',
            'status'  => 'required|in:Active,Inactive',
        ]);

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

    public function appedit($id) {
        $errorMessage = Config::get('messages.error.validation');
        $app = App::where('edit_id', $id)->first();

        if (empty($app)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }

        return view('App.edit', compact('app'));
    }

    public function appedit_action(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        parent::require_ownership(1, 1, 1);

        $request->validate([
            'edit_id' => 'required|string|min:10|max:36',
        ]);

        $app = App::where('edit_id', $request->input('edit_id'))->first();

        if (empty($app)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'id'      => [
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
            'price'   => 'required|integer|min:250|max:300000',
            'status'  => 'required|in:Active,Inactive',
        ]);

        try {
            $app->update([
                'app_id'      => $request->input('id'),
                'name'        => $request->input('name'),
                'price'       => $request->input('price'),
                'ppd_basic'   => $request->input('basic'),
                'ppd_premium' => $request->input('premium'),
                'status'      => $request->input('status'),
            ]);

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
                'message' => str_replace(':info', 'Error Code 202', $errorMessage),
            ]);
        }
    }

    public function appdelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        parent::require_ownership(1, 1, 1);

        $request->validate([
            'edit_id' => 'required|string|min:10|max:36',
        ]);

        try {
            $app = App::where('edit_id', $request->input('edit_id'))->firstOrFail();
            $name = $app->name;
            $app->delete();

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', "<b>App</b> " . $name, $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
    }

    public function appdeletelicenses(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        parent::require_ownership(1, 1, 1);

        $request->validate([
            'edit_id' => 'required|string|min:10|max:36',
        ]);

        try {
            $app = App::where('edit_id', $request->input('edit_id'))->firstOrFail();
            $name = $app->name;
            $licenses = $app->licenses()->get();

            foreach ($licenses as $license) {
                $license->delete();
            }

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', "<b>App</b> " . $name . "<b>'s Licenses</b>", $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
    }

    public function appdeletelicensesme(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'edit_id' => 'required|string|min:10|max:36',
        ]);

        try {
            $app = App::where('edit_id', $request->input('edit_id'))->firstOrFail();
            $name = $app->name;
            $licenses = $app->licenses()->where('registrar', auth()->user()->user_id)->get();

            foreach ($licenses as $license) {
                $license->delete();
            }

            return response()->json([
                'status' => 0,
                'message' => str_replace(':flag', "<b>App</b> " . $name . "<b>'s Licenses</b>", $successMessage),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'message' => str_replace(':info', 'Error Code 201', $errorMessage),
            ]);
        }
    }
}