<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Customer Services
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        // Check customer login
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        // Only CUSTOMER can access
        if (
            strtoupper($request->session()->get('role')) !== 'CUSTOMER'
        ) {
            return redirect()->route('home');
        }

        /*
        |--------------------------------------------------------------------------
        | Get Active Services
        |--------------------------------------------------------------------------
        |
        | Duration is included automatically because we retrieve
        | the complete Service records.
        |
        */

        $services = Service::where('Status', 'Active')
            ->orderBy('Service_Name')
            ->get();

        return view(
            'services.index',
            compact('services')
        );
    }
}