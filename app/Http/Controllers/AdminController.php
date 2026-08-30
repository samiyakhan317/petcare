<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Check Login
        |--------------------------------------------------------------------------
        */

        if (!$request->session()->has('user_id')) {
            return redirect()
                ->route('login');
        }


        /*
        |--------------------------------------------------------------------------
        | Check Admin Role
        |--------------------------------------------------------------------------
        */

        $role = strtoupper(
            $request->session()->get('role', '')
        );

        if ($role !== 'ADMIN') {
            return redirect()
                ->route('home');
        }


        /*
        |--------------------------------------------------------------------------
        | Show Admin Dashboard
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard');
    }
}