<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PetController extends Controller
{
    /*
    |-------------------------------------------------------------------------
    | Show Customer's Pets
    |-------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |-------------------------------------------------------------------------
        | Check Login
        |-------------------------------------------------------------------------
        */

        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        /*
        |-------------------------------------------------------------------------
        | Customer Only
        |-------------------------------------------------------------------------
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
        | Customer ID
        |--------------------------------------------------------------------------
        */

        $customerId = $request->session()->get('user_id');

        /*
        |--------------------------------------------------------------------------
        | Get Customer's Pets
        |--------------------------------------------------------------------------
        |
        | Query Builder is used.
        | No Eloquent ORM is used.
        |
        */

        $pets = DB::table('pet')
            ->where(
                'Customer_ID',
                $customerId
            )
            ->orderBy(
                'Pet_ID',
                'desc'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Calculate Age
        |--------------------------------------------------------------------------
        */

        foreach ($pets as $pet) {

            if (!empty($pet->DOB)) {

                $pet->calculated_age =
                    Carbon::parse($pet->DOB)->age;

            } else {

                $pet->calculated_age = 'N/A';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Return Pets Page
        |--------------------------------------------------------------------------
        */

        return view(
            'pets',
            compact('pets')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Add Pet Form
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
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
        | Customer Only
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
        | Return Add Pet View
        |--------------------------------------------------------------------------
        */

        return view('add_pet');
    }


    /*
    |--------------------------------------------------------------------------
    | Store New Pet
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
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
        | Customer Only
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
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'Name' => [
                'required',
                'string',
                'max:255'
            ],

            'Breed' => [
                'required',
                'string',
                'max:255'
            ],

            'DOB' => [
                'required',
                'date',
                'before_or_equal:today'
            ],

            'Gender' => [
                'required',
                'string',
                'max:50'
            ],

            'Weight' => [
                'required',
                'numeric',
                'min:0'
            ],

            'Allergies' => [
                'nullable',
                'string',
                'max:500'
            ],

            'Vaccination_Status' => [
                'required',
                'string',
                'max:255'
            ],

        ]);

        try {

            /*
            |--------------------------------------------------------------------------
            | Insert Pet
            |--------------------------------------------------------------------------
            |
            | DB::table()->insert() = Query Builder.
            | No Eloquent ORM is used.
            |
            */

            DB::table('pet')->insert([

                'Customer_ID' =>
                    $request->session()->get('user_id'),

                'Name' =>
                    trim($request->Name),

                'Allergies' =>
                    trim($request->Allergies ?? ''),

                'Gender' =>
                    trim($request->Gender),

                'Vaccination_Status' =>
                    trim($request->Vaccination_Status),

                'Breed' =>
                    trim($request->Breed),

                'DOB' =>
                    $request->DOB,

                'Weight' =>
                    $request->Weight,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('pets.index')
                ->with(
                    'success',
                    'Pet profile created successfully!'
                );

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Failed to create pet: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Show Edit Pet Form
    |--------------------------------------------------------------------------
    */

    public function edit(
        Request $request,
        $id
    ) {

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
        | Customer Only
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
        | Customer ID
        |--------------------------------------------------------------------------
        */

        $customerId =
            $request->session()->get('user_id');

        /*
        |--------------------------------------------------------------------------
        | Find Customer's Pet
        |--------------------------------------------------------------------------
        */

        $pet = DB::table('pet')
            ->where(
                'Pet_ID',
                $id
            )
            ->where(
                'Customer_ID',
                $customerId
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Pet Not Found
        |--------------------------------------------------------------------------
        */

        if (!$pet) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Return Edit View
        |--------------------------------------------------------------------------
        */

        return view(
            'edit_pet',
            compact('pet')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Pet
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

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
        | Customer Only
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
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'Name' => [
                'required',
                'string',
                'max:255'
            ],

            'Breed' => [
                'required',
                'string',
                'max:255'
            ],

            'DOB' => [
                'required',
                'date',
                'before_or_equal:today'
            ],

            'Gender' => [
                'required',
                'string',
                'max:50'
            ],

            'Weight' => [
                'required',
                'numeric',
                'min:0'
            ],

            'Allergies' => [
                'nullable',
                'string',
                'max:500'
            ],

            'Vaccination_Status' => [
                'required',
                'string',
                'max:255'
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Customer ID
        |--------------------------------------------------------------------------
        */

        $customerId =
            $request->session()->get('user_id');

        /*
        |--------------------------------------------------------------------------
        | Check Customer Owns Pet
        |--------------------------------------------------------------------------
        */

        $pet = DB::table('pet')
            ->where(
                'Pet_ID',
                $id
            )
            ->where(
                'Customer_ID',
                $customerId
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Pet Not Found
        |--------------------------------------------------------------------------
        */

        if (!$pet) {
            abort(404);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Update Pet
            |--------------------------------------------------------------------------
            */

            DB::table('pet')
                ->where(
                    'Pet_ID',
                    $id
                )
                ->where(
                    'Customer_ID',
                    $customerId
                )
                ->update([

                    'Name' =>
                        trim($request->Name),

                    'Allergies' =>
                        trim(
                            $request->Allergies ?? ''
                        ),

                    'Gender' =>
                        trim($request->Gender),

                    'Vaccination_Status' =>
                        trim(
                            $request->Vaccination_Status
                        ),

                    'Breed' =>
                        trim($request->Breed),

                    'DOB' =>
                        $request->DOB,

                    'Weight' =>
                        $request->Weight,

                ]);

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('pets.index')
                ->with(
                    'success',
                    'Pet profile updated successfully!'
                );

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Failed to update pet: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Pet
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        $id
    ) {

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
        | Customer Only
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
        | Customer ID
        |--------------------------------------------------------------------------
        */

        $customerId =
            $request->session()->get('user_id');

        /*
        |--------------------------------------------------------------------------
        | Check Customer Owns Pet
        |--------------------------------------------------------------------------
        */

        $pet = DB::table('pet')
            ->where(
                'Pet_ID',
                $id
            )
            ->where(
                'Customer_ID',
                $customerId
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Pet Not Found
        |--------------------------------------------------------------------------
        */

        if (!$pet) {

            return redirect()
                ->route('pets.index')
                ->with(
                    'error',
                    'Pet not found.'
                );
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Delete Pet
            |--------------------------------------------------------------------------
            */

            DB::table('pet')
                ->where(
                    'Pet_ID',
                    $id
                )
                ->where(
                    'Customer_ID',
                    $customerId
                )
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('pets.index')
                ->with(
                    'success',
                    'Pet profile deleted successfully!'
                );

        } catch (\Exception $e) {

            return redirect()
                ->route('pets.index')
                ->with(
                    'error',
                    'Failed to delete pet: ' .
                    $e->getMessage()
                );
        }
    }
}
