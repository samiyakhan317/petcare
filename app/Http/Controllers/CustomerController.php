<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Customers
    |--------------------------------------------------------------------------
    |
    | Query Builder / Raw SQL only.
    | NO Eloquent ORM is used.
    |
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
        | Get Customers
        |--------------------------------------------------------------------------
        |
        | DB::select() executes raw SQL directly.
        | This does NOT use Eloquent ORM.
        |
        */

        $customers = DB::select("
            SELECT
                c.ID,
                c.First_name,
                c.Last_name,
                c.Address,
                c.Loyalty_Points,
                u.Email
            FROM Customer c
            INNER JOIN User u
                ON c.ID = u.ID
            WHERE UPPER(u.Role) = ?
            ORDER BY c.ID DESC
        ", [
            'CUSTOMER'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Return Customer View
        |--------------------------------------------------------------------------
        */

        return view(
            'customers.index',
            [
                'customers' => $customers
            ]
        );
    }
}