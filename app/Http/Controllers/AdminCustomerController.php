<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        if (strtoupper($request->session()->get('role')) !== 'ADMIN') {
            return redirect('/login');
        }

        /*
        |--------------------------------------------------------------------------
        | Get all customers
        |--------------------------------------------------------------------------
        */

        $customers = User::whereRaw('UPPER(Role) = ?', ['CUSTOMER'])
            ->with([
                'customer.phoneNumbers'
            ])
            ->withCount('pets')
            ->orderBy('ID', 'desc')
            ->get();

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
        if (strtoupper($request->session()->get('role')) !== 'ADMIN') {
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

            // At least one phone number
            'phone_numbers' => 'required|array|min:1',

            'phone_numbers.*' => 'nullable|string|max:30',

        ]);


        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | STEP 1: Create User
            |--------------------------------------------------------------------------
            */

            $user = new User();

            $user->Email = trim($request->email);

            $user->Password = Hash::make(
                $request->password
            );

            $user->Role = 'CUSTOMER';

            $user->save();


            /*
            |--------------------------------------------------------------------------
            | STEP 2: Get Generated User ID
            |--------------------------------------------------------------------------
            */

            $customerId = $user->ID;


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
            | STEP 3: Create Customer
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
            | STEP 4: Save Multiple Phone Numbers
            |--------------------------------------------------------------------------
            */

            foreach ($request->phone_numbers as $phoneNumber) {

                $phoneNumber = trim($phoneNumber);

                /*
                | Skip empty additional phone fields
                */

                if ($phoneNumber === '') {
                    continue;
                }


                DB::table('Customer_Phone')->insert([

                    /*
                    | IMPORTANT:
                    | Use the actual generated Customer ID.
                    */

                    'Customer_ID' => $customerId,

                    'Phone_Number' => $phoneNumber,

                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | STEP 5: Commit Transaction
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
            | Rollback if anything goes wrong
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

    public function destroy(Request $request, $id)
    {
        // Check whether user is logged in
        if (!$request->session()->has('user_id')) {
            return redirect('/login');
        }

        // Only ADMIN can delete customers
        if (strtoupper($request->session()->get('role')) !== 'ADMIN') {
            return redirect('/login');
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | Find Customer User
            |--------------------------------------------------------------------------
            */

            $user = User::where('ID', $id)
                ->whereRaw(
                    'UPPER(Role) = ?',
                    ['CUSTOMER']
                )
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Delete User
            |--------------------------------------------------------------------------
            |
            | Because your Customer_Phone foreign key has:
            |
            | ON DELETE CASCADE
            |
            | the customer's phone numbers will automatically
            | be deleted when the Customer record is deleted.
            |
            */

            $user->delete();


            return redirect()
                ->route('admin.customers')
                ->with(
                    'success',
                    "Customer ID #{$id} deleted successfully."
                );

        } catch (\Exception $e) {

            return redirect()
                ->route('admin.customers')
                ->with(
                    'error',
                    'Database Error: ' . $e->getMessage()
                );
        }
    }
}