<?php

namespace App\Http\Controllers;

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
        /*
        |--------------------------------------------------------------------------
        | Check Login
        |--------------------------------------------------------------------------
        */

        if (!$request->session()->has('user_id')) {
            return redirect('/login');
        }


        /*
        |--------------------------------------------------------------------------
        | Admin Only
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'ADMIN'
        ) {
            return redirect('/login');
        }


        /*
        |--------------------------------------------------------------------------
        | Get Groomers
        |--------------------------------------------------------------------------
        |
        | Query Builder only.
        | NO Eloquent ORM.
        |
        | User and Groomer are joined using their common ID.
        |
        */

        $groomers = DB::table('Groomer as g')
            ->join(
                'User as u',
                'g.ID',
                '=',
                'u.ID'
            )
            ->whereRaw(
                'UPPER(u.Role) = ?',
                ['GROOMER']
            )
            ->select(
                'g.ID',
                'g.Name',
                'g.Phone',
                'g.Experience',
                'g.Specialization',
                'u.Email'
            )
            ->orderBy(
                'g.ID',
                'desc'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Send Data To View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.manage_groomers',
            compact('groomers')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Add New Groomer
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
            return redirect('/login');
        }


        /*
        |--------------------------------------------------------------------------
        | Admin Only
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'ADMIN'
        ) {
            return redirect('/login');
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Form
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'specialization' => [
                'required',
                'string',
                'max:255'
            ],

            'experience' => [
                'required',
                'numeric',
                'min:0'
            ],

            'phone' => [
                'required',
                'string',
                'max:50'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Begin Database Transaction
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | STEP 1: Generate Groomer Information
            |--------------------------------------------------------------------------
            */

            $fullName = trim(
                $request->name
            );


            /*
            |--------------------------------------------------------------------------
            | Generate Email
            |--------------------------------------------------------------------------
            |
            | Same idea as your previous system.
            |
            */

            $cleanName = preg_replace(
                '/[^a-zA-Z0-9]/',
                '',
                strtolower($fullName)
            );


            $email = $cleanName
                . rand(100, 999)
                . '@petcare.com';


            /*
            |--------------------------------------------------------------------------
            | Make Sure Email Is Unique
            |--------------------------------------------------------------------------
            */

            while (
                DB::table('User')
                    ->where('Email', $email)
                    ->exists()
            ) {

                $email = $cleanName
                    . rand(100, 999)
                    . '@petcare.com';
            }


            /*
            |--------------------------------------------------------------------------
            | STEP 2: Insert Into User
            |--------------------------------------------------------------------------
            |
            | Query Builder.
            | NO User model.
            |
            */

            $groomerId = DB::table('User')
                ->insertGetId([

                    'Email' => $email,

                    'Password' => Hash::make(
                        'Groomer123!'
                    ),

                    'Role' => 'GROOMER',

                ]);


            /*
            |--------------------------------------------------------------------------
            | Safety Check
            |--------------------------------------------------------------------------
            */

            if (
                !$groomerId ||
                $groomerId <= 0
            ) {

                throw new \Exception(
                    'Groomer/User ID was not generated correctly.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | STEP 3: Insert Into Groomer
            |--------------------------------------------------------------------------
            |
            | Query Builder.
            | NO Groomer model.
            |
            */

            DB::table('Groomer')
                ->insert([

                    'ID' => $groomerId,

                    'Name' => $fullName,

                    'Phone' => trim(
                        $request->phone
                    ),

                    'Experience' =>
                        $request->experience,

                    'Specialization' =>
                        trim(
                            $request->specialization
                        ),

                ]);


            /*
            |--------------------------------------------------------------------------
            | STEP 4: Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | Success Message
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('admin.groomers')
                ->with(
                    'success',
                    "Groomer added successfully! (Assigned ID: #{$groomerId})"
                );


        } catch (\Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | Rollback
            |--------------------------------------------------------------------------
            */

            DB::rollBack();


            return redirect()
                ->route('admin.groomers')
                ->with(
                    'error',
                    'Database Error: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Groomer
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
            return redirect('/login');
        }


        /*
        |--------------------------------------------------------------------------
        | Admin Only
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'ADMIN'
        ) {
            return redirect('/login');
        }


        /*
        |--------------------------------------------------------------------------
        | Check Groomer Exists
        |--------------------------------------------------------------------------
        |
        | Query Builder only.
        |
        */

        $groomer = DB::table('User')
            ->where(
                'ID',
                $id
            )
            ->whereRaw(
                'UPPER(Role) = ?',
                ['GROOMER']
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Groomer Not Found
        |--------------------------------------------------------------------------
        */

        if (!$groomer) {

            return redirect()
                ->route('admin.groomers')
                ->with(
                    'error',
                    'Groomer not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Begin Transaction
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | STEP 1: Delete Groomer
            |--------------------------------------------------------------------------
            |
            | Explicitly delete the child record first.
            | This avoids depending on ON DELETE CASCADE.
            |
            */

            DB::table('Groomer')
                ->where(
                    'ID',
                    $id
                )
                ->delete();


            /*
            |--------------------------------------------------------------------------
            | STEP 2: Delete User
            |--------------------------------------------------------------------------
            */

            DB::table('User')
                ->where(
                    'ID',
                    $id
                )
                ->whereRaw(
                    'UPPER(Role) = ?',
                    ['GROOMER']
                )
                ->delete();


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();


            return redirect()
                ->route('admin.groomers')
                ->with(
                    'success',
                    "Groomer #{$id} removed successfully."
                );


        } catch (\Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | Rollback
            |--------------------------------------------------------------------------
            */

            DB::rollBack();


            return redirect()
                ->route('admin.groomers')
                ->with(
                    'error',
                    'Database Error: ' .
                    $e->getMessage()
                );
        }
    }
}