<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\App;
use Illuminate\Validation\Rule;

class AppController extends Controller
{
    public function AppListView() {
        $apps = App::paginate(10);

        return view('App.list', compact('apps'));
    }

    public function AppGenerateView() {
        return view('App.generate');
    }

    public function AppGeneratePost(Request $request) {
        $successMessage = Config::get('messages.success.created');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'name' => 'required|string|unique:apps,name|min:6|max:50',
            'basic' => 'required|integer|min:1|max:300000',
            'premium' => 'required|integer|min:1|max:300000',
            'status' => 'required|in:Active,Inactive',
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
        $app = App::where('edit_id', $id)->firstOrFail();

        return view('App.edit', compact('app'));
    }

    public function AppEditPost(Request $request) {
        $successMessage = Config::get('messages.success.updated');
        $errorMessage = Config::get('messages.error.validation');

        $request->validate([
            'edit_id' => 'required|string|min:10|max:36',
        ]);

        $app = App::where('edit_id', $request->input('edit_id'))->firstOrFail();

        $request->validate([
            'id' => [
                'required',
                'string',
                'min:10',
                'max:36',
                Rule::unique('apps', 'app_id')->ignore($app->edit_id, 'edit_id')
            ],
            'name' => [
                'required',
                'string',
                'min:6',
                'max:50',
                Rule::unique('apps', 'name')->ignore($app->edit_id, 'edit_id')
            ],
            'basic' => 'required|integer|min:250|max:300000',
            'premium' => 'required|integer|min:250|max:300000',
            'status' => 'required|in:Active,Inactive',
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
}