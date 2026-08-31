<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Login Page
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('login');
    }


    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Get Input
        |--------------------------------------------------------------------------
        */

        $email = trim(
            $request->input('email', '')
        );

        $password =
            $request->input('password', '');


        /*
        |--------------------------------------------------------------------------
        | Check Empty Fields
        |--------------------------------------------------------------------------
        */

        if (
            $email === '' ||
            $password === ''
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Please enter email and password.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Get User From Database
        |--------------------------------------------------------------------------
        |
        | Query Builder / DB only.
        |
        | NO Eloquent Model.
        | NO User::where().
        |
        */

        $user = DB::table('User')
            ->where(
                'Email',
                $email
            )
            ->select(
                'ID',
                'Email',
                'Password',
                'Role'
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | User Not Found
        |--------------------------------------------------------------------------
        */

        if (!$user) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid email or password.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Password
        |--------------------------------------------------------------------------
        |
        | Passwords are stored using Hash::make()
        | during signup/admin creation.
        |
        | password_verify() correctly checks
        | those hashed passwords.
        |
        */

        if (
            !password_verify(
                $password,
                $user->Password
            )
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid email or password.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check User Role
        |--------------------------------------------------------------------------
        */

        $role = strtoupper(
            trim($user->Role ?? '')
        );


        /*
        |--------------------------------------------------------------------------
        | Validate Role
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $role,
                [
                    'ADMIN',
                    'CUSTOMER',
                    'GROOMER'
                ]
            )
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid user role.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        |
        | Prevents session fixation after login.
        |
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Store Login Information In Session
        |--------------------------------------------------------------------------
        */

        $request->session()->put([
            'user_id' => $user->ID,
            'email'   => $user->Email,
            'role'    => $role
        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirect Based On Role
        |--------------------------------------------------------------------------
        */

        if ($role === 'ADMIN') {

            return redirect()
                ->route('admin.dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER / GROOMER
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('home');
    }


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Remove Session Data
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();


        /*
        |--------------------------------------------------------------------------
        | Regenerate CSRF Token
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerateToken();


        /*
        |--------------------------------------------------------------------------
        | Redirect To Login
        |--------------------------------------------------------------------------
        */

        return redirect('/login');
    }
}