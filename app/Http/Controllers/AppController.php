<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\App;
use Illuminate\Validation\Rule;

class AppController extends Controller
{
    public function AppListView(Request $request) {
        if ($request->get('search')) {
            $apps = App::where('name', $request->get('search'))->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $apps = App::orderBy('created_at', 'desc')->paginate(10);
        }
        $currency = Config::get('messages.settings.currency');

        return view('App.list', compact('apps', 'currency'));
    }

    public function AppGenerateView() {
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Admin") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        return view('App.generate');
    }

    public function AppGeneratePost(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Admin") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'name'    => 'required|string|unique:apps,name|min:6|max:50',
            'basic'   => 'required|integer|min:1|max:300000',
            'premium' => 'required|integer|min:1|max:300000',
            'status'  => 'required|in:Active,Inactive',
        ]);

        try {
            App::create([
                'name'        => $request->input('name'),
                'ppd_basic'   => $request->input('basic'),
                'ppd_premium' => $request->input('premium'),
                'status'      => $request->input('status'),
            ]);

            return redirect()->route('apps.generate')->with('msgSuccess', str_replace(':flag', "App " . $request->input('name'), $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }
    }

    public function AppEditView($id) {
        $errorMessage = Config::get('messages.error.validation');
        $app = App::where('edit_id', $id)->first();

        if (empty($app)) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }

        return view('App.edit', compact('app'));
    }

    public function AppEditPost(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Admin") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

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
            'basic'   => 'required|integer|min:250|max:300000',
            'premium' => 'required|integer|min:250|max:300000',
            'status'  => 'required|in:Active,Inactive',
        ]);

        try {
            $app->update([
                'app_id'      => $request->input('id'),
                'name'        => $request->input('name'),
                'ppd_basic'   => $request->input('basic'),
                'ppd_premium' => $request->input('premium'),
                'status'      => $request->input('status'),
            ]);

            return redirect()->route('apps.edit', $request->input('edit_id'))->with('msgSuccess', str_replace(':flag', "App " . $request->input('name'), $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }
    }

    public function AppDelete(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Admin") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'edit_id' => 'required|string|min:10|max:36',
        ]);

        try {
            $app = App::where('edit_id', $request->input('edit_id'))->firstOrFail();
            $name = $app->name;
            $app->delete();

            return redirect()->route('apps')->with('msgSuccess', str_replace(':flag', "App " . $name, $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }
    }

    public function AppDeleteKeys(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        if (auth()->user()->permissions == "Admin") {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 202, Access Forbidden', $errorMessage),])->onlyInput('name');
        }

        $request->validate([
            'edit_id' => 'required|string|min:10|max:36',
        ]);

        try {
            $app = App::where('edit_id', $request->input('edit_id'))->firstOrFail();
            $name = $app->name;
            $app->keys()->delete();

            return redirect()->route('apps')->with('msgSuccess', str_replace(':flag', "App " . $name . "'s Keys", $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }
    }

    public function AppDeleteKeysMe(Request $request) {
        $successMessage = Config::get('messages.success.deleted');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'edit_id' => 'required|string|min:10|max:36',
        ]);

        try {
            $app = App::where('edit_id', $request->input('edit_id'))->firstOrFail();
            $name = $app->name;
            $app->keys()->where('created_by', auth()->user()->username)->delete();

            return redirect()->route('apps')->with('msgSuccess', str_replace(':flag', "App " . $name . "'s Keys", $successMessage));
        } catch (\Exception $e) {
            return back()->withErrors(['name' => str_replace(':info', 'Error Code 201', $errorMessage),])->onlyInput('name');
        }
    }
}