<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminServiceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Check Admin Login
    |--------------------------------------------------------------------------
    */

    private function checkAdmin(Request $request)
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
        | Check Admin Role
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'ADMIN'
        ) {

            return redirect()
                ->route('home');
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Show Services
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Access Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Get All Services
        |--------------------------------------------------------------------------
        |
        | Query Builder only.
        | NO Eloquent ORM.
        |
        */

        $services = DB::table('Service')
            ->select(
                'Service_ID',
                'Service_Name',
                'Duration',
                'Price',
                'Description',
                'Status'
            )
            ->orderBy(
                'Service_ID',
                'desc'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.services.index',
            compact('services')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Create Service Form
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Access Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Return Create View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.services.create'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store New Service
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Access Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Form
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'Service_Name' => [
                'required',
                'string',
                'max:255'
            ],

            'Duration' => [
                'required',
                'integer',
                'min:1'
            ],

            'Price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'Description' => [
                'nullable',
                'string'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Insert Service
        |--------------------------------------------------------------------------
        |
        | Query Builder only.
        | NO Service model.
        |
        */

        DB::table('Service')
            ->insert([

                'Service_Name' =>
                    trim(
                        $request->Service_Name
                    ),

                'Duration' =>
                    $request->Duration,

                'Price' =>
                    $request->Price,

                'Description' =>
                    $request->Description,

                'Status' =>
                    'Active',

            ]);


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.services.index'
            )
            ->with(
                'success',
                'Grooming service added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Service
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | Admin Access Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Check Service Exists
        |--------------------------------------------------------------------------
        |
        | Query Builder only.
        | NO Eloquent ORM.
        |
        */

        $service = DB::table('Service')
            ->where(
                'Service_ID',
                $id
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Service Not Found
        |--------------------------------------------------------------------------
        */

        if (!$service) {

            return redirect()
                ->route(
                    'admin.services.index'
                )
                ->with(
                    'error',
                    'Service not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Whether Service Is Used
        |--------------------------------------------------------------------------
        |
        | appointment_service contains the relationship
        | between Appointment and Service.
        |
        */

        $used = DB::table('Appointment_Service')
            ->where(
                'Service_ID',
                $id
            )
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | Cannot Delete Used Service
        |--------------------------------------------------------------------------
        */

        if ($used) {

            return redirect()
                ->route(
                    'admin.services.index'
                )
                ->with(
                    'error',
                    'This service cannot be deleted because it is already used in an appointment.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Service
        |--------------------------------------------------------------------------
        |
        | Query Builder only.
        | NO Eloquent ORM.
        |
        */

        DB::table('Service')
            ->where(
                'Service_ID',
                $id
            )
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.services.index'
            )
            ->with(
                'success',
                'Grooming service deleted successfully.'
            );
    }
}