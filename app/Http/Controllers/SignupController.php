<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerPhone;
use App\Models\Groomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        | CHECK EMAIL ALREADY EXISTS
        |--------------------------------------------------------------------------
        */

        $email = trim($request->email);

        if (User::where('Email', $email)->exists()) {

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


            DB::beginTransaction();

            try {

                /*
                |--------------------------------------------------------------------------
                | 1. CREATE USER
                |--------------------------------------------------------------------------
                */

                $user = new User();

                $user->Email = $email;

                $user->Password =
                    Hash::make(
                        $request->password
                    );

                $user->Role = 'CUSTOMER';

                $user->save();


                /*
                |--------------------------------------------------------------------------
                | 2. GET GENERATED USER ID
                |--------------------------------------------------------------------------
                */

                $customerId = $user->ID;


                if (!$customerId) {

                    throw new \Exception(
                        'User ID was not generated correctly.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 3. CREATE CUSTOMER
                |--------------------------------------------------------------------------
                */

                $customer = new Customer();

                $customer->ID = $customerId;

                $customer->First_name =
                    trim(
                        $request->first_name
                    );

                $customer->Last_name =
                    trim(
                        $request->last_name
                    );

                $customer->Address =
                    trim(
                        $request->address
                    );

                /*
                | New customers start with 0 loyalty points
                */

                $customer->Loyalty_Points = 0;

                $customer->save();


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
                    | Ignore empty phone fields
                    */

                    if ($phone === '') {
                        continue;
                    }


                    $customerPhone =
                        new CustomerPhone();

                    /*
                    | Customer_Phone table:
                    |
                    | Phone_ID      -> AUTO_INCREMENT
                    | Customer_ID   -> Customer ID
                    | Phone_Number  -> Phone number
                    */

                    $customerPhone->Customer_ID =
                        $customerId;

                    $customerPhone->Phone_Number =
                        $phone;

                    $customerPhone->save();
                }


                /*
                |--------------------------------------------------------------------------
                | 5. COMMIT TRANSACTION
                |--------------------------------------------------------------------------
                */

                DB::commit();


                /*
                |--------------------------------------------------------------------------
                | 6. SUCCESS MESSAGE
                |--------------------------------------------------------------------------
                |
                | Stay on signup page so the message is visible.
                |
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
                | ROLLBACK
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


            DB::beginTransaction();

            try {

                /*
                |--------------------------------------------------------------------------
                | 1. CREATE USER
                |--------------------------------------------------------------------------
                */

                $user = new User();

                $user->Email = $email;

                $user->Password =
                    Hash::make(
                        $request->password
                    );

                $user->Role = 'GROOMER';

                $user->save();


                /*
                |--------------------------------------------------------------------------
                | 2. GET GENERATED USER ID
                |--------------------------------------------------------------------------
                */

                $groomerId = $user->ID;


                if (!$groomerId) {

                    throw new \Exception(
                        'User ID was not generated correctly.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 3. CREATE GROOMER
                |--------------------------------------------------------------------------
                */

                $groomer = new Groomer();

                $groomer->ID = $groomerId;

                $groomer->Name =
                    trim(
                        $request->groomer_name
                    );

                $groomer->Phone =
                    trim(
                        $request->phone
                    );

                $groomer->Experience =
                    $request->experience !== null
                    ? (float) $request->experience
                    : 0;

                $groomer->Specialization =
                    trim(
                        $request->specialization ?? ''
                    );

                $groomer->save();


                /*
                |--------------------------------------------------------------------------
                | 4. COMMIT TRANSACTION
                |--------------------------------------------------------------------------
                */

                DB::commit();


                /*
                |--------------------------------------------------------------------------
                | 5. SUCCESS MESSAGE
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
                | ROLLBACK
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
        | INVALID ROLE
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