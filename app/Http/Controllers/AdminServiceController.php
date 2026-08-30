<?php

namespace App\Http\Controllers;

use App\Models\Service;
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
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (
            strtoupper($request->session()->get('role')) !== 'ADMIN'
        ) {
            return redirect()->route('home');
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
        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }

        $services = Service::orderBy(
            'Service_ID',
            'desc'
        )->get();

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
        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }

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

            'Service_Name' =>
                'required|string|max:255',

            'Duration' =>
                'required|integer|min:1',

            'Price' =>
                'required|numeric|min:0',

            'Description' =>
                'nullable|string',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Create Service
        |--------------------------------------------------------------------------
        |
        | New services are automatically Active.
        |
        */

        Service::create([

            'Service_Name' =>
                $request->Service_Name,

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

    public function destroy(Request $request, $id)
    {
        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }

        $service = Service::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Check if Service is Used in an Appointment
        |--------------------------------------------------------------------------
        */

        $used = DB::table('appointment_service')
            ->where('Service_ID', $id)
            ->exists();

        if ($used) {
            return redirect()
                ->route('admin.services.index')
                ->with(
                    'error',
                    'This service cannot be deleted because it is already used in an appointment.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Service
        |--------------------------------------------------------------------------
        */

        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with(
                'success',
                'Grooming service deleted successfully.'
            );
    }
}