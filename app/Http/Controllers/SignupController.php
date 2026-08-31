<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignupController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Signup Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('signup');
    }


    /*
    |--------------------------------------------------------------------------
    | Register User
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Common Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'Role' => 'required|in:CUSTOMER,GROOMER',

            'email' => 'required|email|max:255',

            // EXACTLY 8 characters
            'password' => 'required|string|size:8',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Get Email
        |--------------------------------------------------------------------------
        */

        $email = trim($request->email);


        /*
        |--------------------------------------------------------------------------
        | Check Email Already Exists
        |--------------------------------------------------------------------------
        |
        | Query Builder only.
        |
        */

        $existingUser = DB::table('User')
            ->where('Email', $email)
            ->first();


        if ($existingUser) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Email already exists.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER REGISTRATION
        |--------------------------------------------------------------------------
        */

        if ($request->Role === 'CUSTOMER') {

            /*
            |--------------------------------------------------------------------------
            | Customer Validation
            |--------------------------------------------------------------------------
            */

            $request->validate([

                'first_name' =>
                    'required|string|max:255',

                'last_name' =>
                    'required|string|max:255',

                'address' =>
                    'required|string|max:500',

                'customer_phone' =>
                    'required|array|min:1',

                'customer_phone.*' =>
                    'nullable|string|max:30',
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
                | 1. CREATE USER
                |--------------------------------------------------------------------------
                */

                $userId = DB::table('User')
                    ->insertGetId([

                        'Email' =>
                            $email,

                        'Password' =>
                            password_hash(
                                $request->password,
                                PASSWORD_DEFAULT
                            ),

                        'Role' =>
                            'CUSTOMER',

                    ]);


                /*
                |--------------------------------------------------------------------------
                | 2. Check Generated User ID
                |--------------------------------------------------------------------------
                */

                if (!$userId) {

                    throw new \Exception(
                        'User ID was not generated correctly.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 3. CREATE CUSTOMER
                |--------------------------------------------------------------------------
                */

                DB::table('Customer')
                    ->insert([

                        'ID' =>
                            $userId,

                        'First_name' =>
                            trim(
                                $request->first_name
                            ),

                        'Last_name' =>
                            trim(
                                $request->last_name
                            ),

                        'Address' =>
                            trim(
                                $request->address
                            ),

                        /*
                        | New customers start with 0 loyalty points.
                        */

                        'Loyalty_Points' =>
                            0,

                    ]);


                /*
                |--------------------------------------------------------------------------
                | 4. CREATE CUSTOMER PHONE NUMBERS
                |--------------------------------------------------------------------------
                */

                foreach (
                    $request->customer_phone
                    as $phone
                ) {

                    $phone = trim($phone);


                    /*
                    | Ignore empty phone fields.
                    */

                    if ($phone === '') {
                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Customer_Phone table
                    |--------------------------------------------------------------------------
                    |
                    | Phone_ID
                    | Customer_ID
                    | Phone_Number
                    |
                    */

                    DB::table('Customer_Phone')
                        ->insert([

                            'Customer_ID' =>
                                $userId,

                            'Phone_Number' =>
                                $phone,

                        ]);
                }


                /*
                |--------------------------------------------------------------------------
                | 5. Commit Transaction
                |--------------------------------------------------------------------------
                */

                DB::commit();


                /*
                |--------------------------------------------------------------------------
                | 6. Success Message
                |--------------------------------------------------------------------------
                */

                return redirect()
                    ->route('signup')
                    ->with(
                        'success',
                        'Customer account created successfully!'
                    );


            } catch (\Exception $e) {

                /*
                |--------------------------------------------------------------------------
                | Rollback
                |--------------------------------------------------------------------------
                */

                DB::rollBack();


                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Account creation failed: ' .
                        $e->getMessage()
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GROOMER REGISTRATION
        |--------------------------------------------------------------------------
        */

        if ($request->Role === 'GROOMER') {

            /*
            |--------------------------------------------------------------------------
            | Groomer Validation
            |--------------------------------------------------------------------------
            */

            $request->validate([

                'groomer_name' =>
                    'required|string|max:255',

                'phone' =>
                    'required|string|max:30',

                'experience' =>
                    'nullable|numeric|min:0',

                'specialization' =>
                    'nullable|string|max:255',
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
                | 1. CREATE USER
                |--------------------------------------------------------------------------
                */

                $userId = DB::table('User')
                    ->insertGetId([

                        'Email' =>
                            $email,

                        'Password' =>
                            password_hash(
                                $request->password,
                                PASSWORD_DEFAULT
                            ),

                        'Role' =>
                            'GROOMER',

                    ]);


                /*
                |--------------------------------------------------------------------------
                | 2. Check Generated User ID
                |--------------------------------------------------------------------------
                */

                if (!$userId) {

                    throw new \Exception(
                        'User ID was not generated correctly.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 3. CREATE GROOMER
                |--------------------------------------------------------------------------
                */

                DB::table('Groomer')
                    ->insert([

                        'ID' =>
                            $userId,

                        'Name' =>
                            trim(
                                $request->groomer_name
                            ),

                        'Phone' =>
                            trim(
                                $request->phone
                            ),

                        'Experience' =>
                            $request->experience !== null
                                ? (float) $request->experience
                                : 0,

                        'Specialization' =>
                            trim(
                                $request->specialization ?? ''
                            ),

                    ]);


                /*
                |--------------------------------------------------------------------------
                | 4. Commit Transaction
                |--------------------------------------------------------------------------
                */

                DB::commit();


                /*
                |--------------------------------------------------------------------------
                | 5. Success Message
                |--------------------------------------------------------------------------
                */

                return redirect()
                    ->route('signup')
                    ->with(
                        'success',
                        'Groomer account created successfully!'
                    );


            } catch (\Exception $e) {

                /*
                |--------------------------------------------------------------------------
                | Rollback
                |--------------------------------------------------------------------------
                */

                DB::rollBack();


                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Account creation failed: ' .
                        $e->getMessage()
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Invalid Role
        |--------------------------------------------------------------------------
        */

        return back()
            ->withInput()
            ->with(
                'error',
                'Invalid account type.'
            );
    }
}