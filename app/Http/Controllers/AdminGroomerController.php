<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Groomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminGroomerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Groomers
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

        // Get all groomers with their User information
        $groomers = Groomer::with('user')
            ->orderBy('ID', 'desc')
            ->get();

        return view('admin.manage_groomers', compact('groomers'));
    }


    /*
    |--------------------------------------------------------------------------
    | Add New Groomer
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Check whether user is logged in
        if (!$request->session()->has('user_id')) {
            return redirect('/login');
        }

        // Only ADMIN can add groomers
        if (strtoupper($request->session()->get('role')) !== 'ADMIN') {
            return redirect('/login');
        }

        // Validate form
        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'experience' => 'required|numeric|min:0',
            'phone' => 'required|string|max:50',
        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Create User Account
            |--------------------------------------------------------------------------
            */

            $fullName = trim($request->name);

            // Generate email similar to the old PHP system
            $cleanName = preg_replace(
                '/[^a-zA-Z0-9]/',
                '',
                strtolower($fullName)
            );

            $email = $cleanName . rand(100, 999) . '@petcare.com';

            // Create User
            $user = new User();

            $user->Email = $email;
            $user->Password = Hash::make('Groomer123!');
            $user->Role = 'GROOMER';

            $user->save();


            /*
            |--------------------------------------------------------------------------
            | Create Groomer Record
            |--------------------------------------------------------------------------
            */

            $groomer = new Groomer();

            // User ID and Groomer ID are the same
            $groomer->ID = $user->ID;

            $groomer->Name = $fullName;
            $groomer->Phone = $request->phone;
            $groomer->Experience = $request->experience;
            $groomer->Specialization = $request->specialization;

            $groomer->save();


            DB::commit();

            return redirect()
                ->route('admin.groomers')
                ->with(
                    'success',
                    "Groomer added successfully! (Assigned ID: #{$user->ID})"
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->route('admin.groomers')
                ->with(
                    'error',
                    'Database Error: ' . $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Groomer
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request, $id)
    {
        // Check whether user is logged in
        if (!$request->session()->has('user_id')) {
            return redirect('/login');
        }

        // Only ADMIN can delete groomers
        if (strtoupper($request->session()->get('role')) !== 'ADMIN') {
            return redirect('/login');
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Delete User
            |--------------------------------------------------------------------------
            |
            | The User record is deleted.
            | If your database foreign key uses ON DELETE CASCADE,
            | the related Groomer record will also be deleted.
            |
            */

            $user = User::where('ID', $id)
                ->whereRaw('UPPER(Role) = ?', ['GROOMER'])
                ->firstOrFail();

            $user->delete();

            return redirect()
                ->route('admin.groomers')
                ->with(
                    'success',
                    "Groomer #{$id} removed successfully."
                );

        } catch (\Exception $e) {

            return redirect()
                ->route('admin.groomers')
                ->with(
                    'error',
                    'Database Error: ' . $e->getMessage()
                );
        }
    }
}