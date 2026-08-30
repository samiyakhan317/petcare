<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
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
            ORDER BY c.ID DESC
        ");

        return view('customers.index', [
            'customers' => $customers
        ]);
    }
}