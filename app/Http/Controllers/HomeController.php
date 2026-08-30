<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Groomer;
use Illuminate\Http\Request;

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
            return redirect()->route('login');
        }


        /*
        |--------------------------------------------------------------------------
        | Get Session Information
        |--------------------------------------------------------------------------
        */

        $userId = $request->session()->get('user_id');

        $role = strtoupper(
            $request->session()->get('role')
        );


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        |
        | Admin should not use customer/groomer home.
        | Send admin directly to dashboard.
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
        */

        $display_name =
            $request->session()->get('email');


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER DISPLAY NAME
        |--------------------------------------------------------------------------
        */

        if ($role === 'CUSTOMER') {

            $customer = Customer::where(
                'ID',
                $userId
            )->first();


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
        | GROOMER DISPLAY NAME
        |--------------------------------------------------------------------------
        */

        elseif ($role === 'GROOMER') {

            $groomer = Groomer::where(
                'ID',
                $userId
            )->first();


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

            return redirect()->route('login');

        }


        /*
        |--------------------------------------------------------------------------
        | Show Home
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