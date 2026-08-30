<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $email = trim($request->input('email'));
        $password = $request->input('password');

        if ($email === '' || $password === '') {
            return back()->with(
                'error',
                'Please enter email and password.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get user from database using RAW SQL
        |--------------------------------------------------------------------------
        */

        $users = DB::select(
            "SELECT ID, Email, Password, Role
             FROM User
             WHERE Email = ?
             LIMIT 1",
            [$email]
        );

        /*
        |--------------------------------------------------------------------------
        | Check if user exists
        |--------------------------------------------------------------------------
        */

        if (empty($users)) {
            return back()->with(
                'error',
                'Invalid email or password.'
            );
        }

        $user = $users[0];

        /*
        |--------------------------------------------------------------------------
        | Check password
        |--------------------------------------------------------------------------
        */

        if (!password_verify($password, $user->Password)) {
            return back()->with(
                'error',
                'Invalid email or password.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create login session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        session([
            'user_id' => $user->ID,
            'email'   => $user->Email,
            'role'    => $user->Role
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect based on role
        |--------------------------------------------------------------------------
        */

        if ($user->Role === 'ADMIN') {
            return redirect()->route('admin.dashboard');
        }

        /*if ($user->Role === 'GROOMER') {
            return redirect('/groomer-dashboard');
        }*/

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/login');
    }
}