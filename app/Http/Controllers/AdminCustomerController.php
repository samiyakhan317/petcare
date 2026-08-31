<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminCustomerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Customers
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        // Check whether user is logged in
        if (!$request->session()->has('user_id')) {
            return redirect('/login');
        }

        // Only ADMIN can access this page
        if (strtoupper($request->session()->get('role', '')) !== 'ADMIN') {
            return redirect('/login');
        }

        /*
        |--------------------------------------------------------------------------
        | Get Customers
        |--------------------------------------------------------------------------
        |
        | No Eloquent ORM is used here.
        | Data is retrieved using DB::table().
        |
        */

        $customers = DB::table('User as u')
            ->leftJoin(
                'Customer as c',
                'u.ID',
                '=',
                'c.ID'
            )
            ->whereRaw(
                'UPPER(u.Role) = ?',
                ['CUSTOMER']
            )
            ->select(
                'u.ID',
                'u.Email',
                'c.First_name',
                'c.Last_name',
                'c.Address',
                'c.Loyalty_Points'
            )
            ->orderBy(
                'u.ID',
                'desc'
            )
            ->get();


        /*
        |-------------------------------------------------------------------------
        | Add Phone Numbers and Pet Count
        |-------------------------------------------------------------------------
        */

        foreach ($customers as $customer) {

            /*
            |-------------------------------------------------------------------------
            | Get Phone Numbers
            |-------------------------------------------------------------------------
            */

            $customer->phoneNumbers = DB::table(
                'Customer_Phone'
            )
                ->where(
                    'Customer_ID',
                    $customer->ID
                )
                ->select(
                    'Phone_Number'
                )
                ->get();


            /*
            |--------------------------------------------------------------------------
            | Get Registered Pet Count
            |--------------------------------------------------------------------------
            */

            $customer->pets_count = DB::table(
                'Pet'
            )
                ->where(
                    'Customer_ID',
                    $customer->ID
                )
                ->count();
        }


        return view(
            'admin.manage_customers',
            compact('customers')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Add New Customer
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Check whether user is logged in
        if (!$request->session()->has('user_id')) {
            return redirect('/login');
        }

        // Only ADMIN can add customers
        if (strtoupper($request->session()->get('role', '')) !== 'ADMIN') {
            return redirect('/login');
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Form
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'first_name' => 'required|string|max:255',

            'last_name' => 'required|string|max:255',

            'address' => 'required|string|max:500',

            'email' => 'required|email|max:255|unique:User,Email',

            'password' => 'required|string|size:8',

            'phone_numbers' => 'required|array|min:1',

            'phone_numbers.*' => 'nullable|string|max:30',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Begin Transaction
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | STEP 1: Insert User
            |--------------------------------------------------------------------------
            */

            $customerId = DB::table('User')->insertGetId([

                'Email' => trim($request->email),

                'Password' => Hash::make(
                    $request->password
                ),

                'Role' => 'CUSTOMER',

            ]);


            /*
            |--------------------------------------------------------------------------
            | Safety Check
            |--------------------------------------------------------------------------
            */

            if (!$customerId || $customerId <= 0) {

                throw new \Exception(
                    'Customer/User ID was not generated correctly.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | STEP 2: Insert Customer
            |--------------------------------------------------------------------------
            */

            DB::table('Customer')->insert([

                'ID' => $customerId,

                'First_name' => trim(
                    $request->first_name
                ),

                'Last_name' => trim(
                    $request->last_name
                ),

                'Address' => trim(
                    $request->address
                ),

                'Loyalty_Points' => 0,

            ]);


            /*
            |--------------------------------------------------------------------------
            | STEP 3: Insert Phone Numbers
            |--------------------------------------------------------------------------
            */

            foreach ($request->phone_numbers as $phoneNumber) {

                $phoneNumber = trim($phoneNumber);


                // Skip empty additional phone fields
                if ($phoneNumber === '') {
                    continue;
                }


                DB::table('Customer_Phone')->insert([

                    'Customer_ID' => $customerId,

                    'Phone_Number' => $phoneNumber,

                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | STEP 4: Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();


            return redirect()
                ->route('admin.customers')
                ->with(
                    'success',
                    "Customer added successfully! (Assigned ID: #{$customerId})"
                );


        } catch (\Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | Rollback
            |--------------------------------------------------------------------------
            */

            DB::rollBack();


            return redirect()
                ->route('admin.customers')
                ->with(
                    'error',
                    'Database Error: ' . $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Customer
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        $id
    ) {

        // Check whether user is logged in
        if (!$request->session()->has('user_id')) {
            return redirect('/login');
        }

        // Only ADMIN can delete customers
        if (strtoupper($request->session()->get('role', '')) !== 'ADMIN') {
            return redirect('/login');
        }


        /*
        |--------------------------------------------------------------------------
        | Check Customer Exists
        |--------------------------------------------------------------------------
        */

        $customer = DB::table('User')
            ->where(
                'ID',
                $id
            )
            ->whereRaw(
                'UPPER(Role) = ?',
                ['CUSTOMER']
            )
            ->first();


        if (!$customer) {

            return redirect()
                ->route('admin.customers')
                ->with(
                    'error',
                    'Customer not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Customer
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Delete Phone Numbers
            |--------------------------------------------------------------------------
            |
            | Explicitly delete these instead of depending
            | on ON DELETE CASCADE.
            |
            */

            DB::table('Customer_Phone')
                ->where(
                    'Customer_ID',
                    $id
                )
                ->delete();


            /*
            |--------------------------------------------------------------------------
            | Delete Customer Record
            |--------------------------------------------------------------------------
            */

            DB::table('Customer')
                ->where(
                    'ID',
                    $id
                )
                ->delete();


            /*
            |--------------------------------------------------------------------------
            | Delete User Record
            |--------------------------------------------------------------------------
            */

            DB::table('User')
                ->where(
                    'ID',
                    $id
                )
                ->delete();


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();


            return redirect()
                ->route('admin.customers')
                ->with(
                    'success',
                    "Customer ID #{$id} deleted successfully."
                );


        } catch (\Exception $e) {

            DB::rollBack();


            return redirect()
                ->route('admin.customers')
                ->with(
                    'error',
                    'Database Error: ' . $e->getMessage()
                );
        }
    }
}
