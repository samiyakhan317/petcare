<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PetController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Customer's Pets
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        // Check login
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        // Customer only
        if (strtoupper($request->session()->get('role')) !== 'CUSTOMER') {
            return redirect()->route('home');
        }

        $customerId = $request->session()->get('user_id');

        // Get only pets belonging to logged-in customer
        $pets = Pet::where('Customer_ID', $customerId)
            ->orderBy('Pet_ID', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Calculate Age from Date of Birth
        |--------------------------------------------------------------------------
        */

        foreach ($pets as $pet) {

            if (!empty($pet->DOB)) {

                $pet->calculated_age = Carbon::parse($pet->DOB)
                    ->age;

            } else {

                $pet->calculated_age = 'N/A';

            }
        }

        return view('pets', compact('pets'));
    }


    /*
    |--------------------------------------------------------------------------
    | Show Add Pet Form
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        // Check login
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        // Customer only
        if (strtoupper($request->session()->get('role')) !== 'CUSTOMER') {
            return redirect()->route('home');
        }

        return view('add_pet');
    }


    /*
    |--------------------------------------------------------------------------
    | Store New Pet
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Check login
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        // Customer only
        if (strtoupper($request->session()->get('role')) !== 'CUSTOMER') {
            return redirect()->route('home');
        }

        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'Name' => 'required|string|max:255',

            'Breed' => 'required|string|max:255',

            'DOB' => 'required|date|before_or_equal:today',

            'Gender' => 'required|string|max:50',

            'Weight' => 'required|numeric|min:0',

            'Allergies' => 'nullable|string|max:500',

            'Vaccination_Status' => 'required|string|max:255',
        ]);


        try {

            /*
            |--------------------------------------------------------------------------
            | Create Pet
            |--------------------------------------------------------------------------
            */

            $pet = new Pet();

            // Logged-in customer becomes owner
            $pet->Customer_ID = $request->session()->get('user_id');

            $pet->Name = trim($request->Name);

            $pet->Allergies = trim(
                $request->Allergies ?? ''
            );

            $pet->Gender = trim(
                $request->Gender
            );

            $pet->Vaccination_Status = trim(
                $request->Vaccination_Status
            );

            $pet->Breed = trim(
                $request->Breed
            );

            $pet->DOB = $request->DOB;

            $pet->Weight = $request->Weight;

            $pet->save();


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
                    'Failed to create pet: ' . $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Show Edit Pet Form
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, $id)
    {
        // Check login
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        // Customer only
        if (strtoupper($request->session()->get('role')) !== 'CUSTOMER') {
            return redirect()->route('home');
        }

        /*
        |--------------------------------------------------------------------------
        | Get only the customer's own pet
        |--------------------------------------------------------------------------
        */

        $pet = Pet::where('Pet_ID', $id)
            ->where(
                'Customer_ID',
                $request->session()->get('user_id')
            )
            ->firstOrFail();

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

    public function update(Request $request, $id)
    {
        // Check login
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        // Customer only
        if (strtoupper($request->session()->get('role')) !== 'CUSTOMER') {
            return redirect()->route('home');
        }

        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'Name' => 'required|string|max:255',

            'Breed' => 'required|string|max:255',

            'DOB' => 'required|date|before_or_equal:today',

            'Gender' => 'required|string|max:50',

            'Weight' => 'required|numeric|min:0',

            'Allergies' => 'nullable|string|max:500',

            'Vaccination_Status' => 'required|string|max:255',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Find customer's own pet
        |--------------------------------------------------------------------------
        */

        $pet = Pet::where('Pet_ID', $id)
            ->where(
                'Customer_ID',
                $request->session()->get('user_id')
            )
            ->firstOrFail();


        try {

            /*
            |--------------------------------------------------------------------------
            | Update Pet
            |--------------------------------------------------------------------------
            */

            $pet->Name = trim(
                $request->Name
            );

            $pet->Allergies = trim(
                $request->Allergies ?? ''
            );

            $pet->Gender = trim(
                $request->Gender
            );

            $pet->Vaccination_Status = trim(
                $request->Vaccination_Status
            );

            $pet->Breed = trim(
                $request->Breed
            );

            $pet->DOB = $request->DOB;

            $pet->Weight = $request->Weight;

            $pet->save();


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
                    'Failed to update pet: ' . $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Pet
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request, $id)
    {
        // Check login
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        // Customer only
        if (strtoupper($request->session()->get('role')) !== 'CUSTOMER') {
            return redirect()->route('home');
        }

        /*
        |--------------------------------------------------------------------------
        | Find customer's own pet
        |--------------------------------------------------------------------------
        */

        $pet = Pet::where('Pet_ID', $id)
            ->where(
                'Customer_ID',
                $request->session()->get('user_id')
            )
            ->firstOrFail();


        try {

            $pet->delete();

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