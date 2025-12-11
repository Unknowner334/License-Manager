<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use App\Models\App;
use App\Models\AppHistory;
use App\Helpers\AppHelper;
use App\Http\Requests\AppGenerateRequest;
use App\Http\Requests\AppEditRequest;

class AppController extends Controller
{
    public function applist() {
        return view('App.list');
    }

    public function appdata() {
        $apps = App::get();

        $data = $apps->map(function ($app) {
            $currency = Config::get('messages.settings.currency');
            $cplace = Config::get('messages.settings.currency_place');
            $created = timeElapsed($app->created_at);
            $price = number_format($app->price);
            $appStatus = statusColor($app->status);
            $licenses = 0;

            if ($cplace == 0) {
                $price = $price . $currency;
            } else if ($cplace == 1) {
                $price = $currency . $price;
            } else {
                $price = $price . ' ' . $currency;
            }

            $ids = [$app->edit_id, $app->app_id, $app->name];

            foreach ($app->licenses as $license) {
                $licenses += 1;
            }

            return [
                'id'        => $app->id,
                'ids'       => $ids,
                'name'      => "<span class='align-middle badge fw-normal text-$appStatus fs-6 px-3'>$app->name</span>",
                'licenses'  => "$licenses License",
                'registrar' => userUsername($app->registrar),
                'created'   => "<i class='align-middle badge fw-normal text-dark fs-6'>$created</i>",
                'price'     => "$price",
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $data
        ]);
    }

    public function appgenerate() {
        $errorMessage = Config::get('messages.error.validation');

        require_ownership(1);

        return view('App.generate');
    }

    public function appgenerate_action(AppGenerateRequest $request) {
        require_ownership(1);

        $request->validated();

        return AppHelper::appGenerate($request);
    }

    public function appedit($id) {
        $errorMessage = Config::get('messages.error.validation');
        $app = App::where('edit_id', $id)->first();

        if (empty($app)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }

        return view('App.edit', compact('app'));
    }

    public function appedit_action(AppEditRequest $request) {
        require_ownership(1, 1, 1);

        $request->validated();

        return AppHelper::appEdit($request);
    }

    public function appdelete(AppEditRequest $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        require_ownership(1, 1, 1);

        $request->validated();

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

    public function appdeletelicenses(AppEditRequest $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        require_ownership(1, 1, 1);

        $request->validated();

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

    public function appdeletelicensesme(AppEditRequest $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        $request->validated();

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