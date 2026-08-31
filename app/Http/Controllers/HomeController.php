<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Home Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
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
        | Get Session Information
        |--------------------------------------------------------------------------
        */

        $userId =
            $request->session()->get('user_id');

        $role = strtoupper(
            $request->session()->get('role', '')
        );


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        |
        | Admin does not use the normal customer/groomer home page.
        | Send admin directly to the admin dashboard.
        |
        */

        if ($role === 'ADMIN') {

            return redirect()
                ->route('admin.dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | Default Display Name
        |--------------------------------------------------------------------------
        |
        | If customer/groomer information cannot be found,
        | use the email stored in the session.
        |
        */

        $display_name =
            $request->session()->get('email');


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if ($role === 'CUSTOMER') {

            /*
            |--------------------------------------------------------------------------
            | Get Customer Information
            |--------------------------------------------------------------------------
            |
            | Query Builder only.
            | NO Eloquent ORM.
            |
            */

            $customer = DB::table('Customer')
                ->where(
                    'ID',
                    $userId
                )
                ->first();


            /*
            |--------------------------------------------------------------------------
            | Set Customer Name
            |--------------------------------------------------------------------------
            */

            if (
                $customer &&
                !empty($customer->First_name)
            ) {

                $display_name =
                    $customer->First_name;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GROOMER
        |--------------------------------------------------------------------------
        */

        elseif ($role === 'GROOMER') {

            /*
            |--------------------------------------------------------------------------
            | Get Groomer Information
            |--------------------------------------------------------------------------
            |
            | Query Builder only.
            | NO Eloquent ORM.
            |
            */

            $groomer = DB::table('Groomer')
                ->where(
                    'ID',
                    $userId
                )
                ->first();


            /*
            |--------------------------------------------------------------------------
            | Set Groomer Name
            |--------------------------------------------------------------------------
            */

            if (
                $groomer &&
                !empty($groomer->Name)
            ) {

                $display_name =
                    $groomer->Name;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Invalid Role
        |--------------------------------------------------------------------------
        */

        else {

            return redirect()
                ->route('login');
        }


        /*
        |--------------------------------------------------------------------------
        | Show Home Page
        |--------------------------------------------------------------------------
        */

        return view(
            'home',
            compact(
                'role',
                'display_name'
            )
        );
    }
}