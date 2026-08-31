<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Customer Services
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Check Customer Login
        |--------------------------------------------------------------------------
        */

        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }


        /*
        |--------------------------------------------------------------------------
        | Only CUSTOMER Can Access
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'CUSTOMER'
        ) {
            return redirect()->route('home');
        }


        /*
        |--------------------------------------------------------------------------
        | Get Active Services
        |--------------------------------------------------------------------------
        |
        | Query Builder is used instead of Eloquent ORM.
        |
        | This retrieves:
        |
        | - Service_ID
        | - Service_Name
        | - Price
        | - Duration
        | - Description
        | - Status
        |
        */

        $services = DB::table('service')
            ->where(
                'Status',
                'ACTIVE'
            )
            ->orderBy(
                'Service_Name'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Return Services View
        |--------------------------------------------------------------------------
        */

        return view(
            'services.index',
            compact('services')
        );
    }
}