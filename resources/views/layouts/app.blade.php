<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
    @yield('title', 'PetCare & Grooming')
</title>

<style>

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #dbdcf1;
        color: #070707;
    }

    /* =========================
       NAVBAR
    ========================= */

    .navbar {
        width: 100%;
        background: #b0cecc;
        padding: 10px 50px;

        display: flex;
        align-items: center;
        justify-content: space-between;

        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);

        position: sticky;
        top: 0;
        z-index: 1000;
    }

    /* =========================
       LOGO
    ========================= */

    .logo {
        text-decoration: none;
        color: #333;
        font-size: 24px;
        font-weight: bold;
        white-space: nowrap;
    }

    .logo span {
        color: #070707;
    }

    /* =========================
       NAV LINKS
    ========================= */

    .nav-links {
        display: flex;
        align-items: center;
        gap: 30px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .nav-links a {
        text-decoration: none;
        color: #0b0b0b;
        font-size: 15px;
        font-weight: 600;
        transition: 0.2s;
    }

    .nav-links a:hover {
        color: #060a75;
    }

    .nav-links a.active {
        color: #1217a8;
    }

    /* =========================
       LOYALTY POINTS
    ========================= */

    .loyalty-points {
        background: #f4e4af;
        color: #302504 !important;
        padding: 4px 9px;
        border-radius: 10px;
        font-size: 14px !important;
        font-weight: bold !important;
        white-space: nowrap;
    }

    .loyalty-points:hover {
        background: #f4e4af;
        color: #302504 !important;
    }

    /* =========================
       ROLE BADGE
    ========================= */

    .role-badge {
        padding: 6px 10px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: bold;
        white-space: nowrap;
    }

    .customer-badge {
        background: #f4e4af;
        color: #382c08;
    }

    .groomer-badge {
        background: #f0e8ff;
        color: #7040a0;
    }

    .admin-badge {
        background: #ffe8ed;
        color: #1a631a;
    }

    /* =========================
       LOGOUT
    ========================= */

    .logout-btn {
        background: #990e23;
        color: white !important;
        padding: 10px 18px;
        border-radius: 8px;
        white-space: nowrap;
    }

    .logout-btn:hover {
        background: #990e23;
        color: white !important;
    }

    /* =========================
       PAGE CONTENT
    ========================= */

    .page-container {
        width: 95%;
        max-width: 1200px;
        margin: 20px auto;
    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 1100px) {

        .navbar {
            padding: 15px 25px;
        }

        .nav-links {
            gap: 15px;
        }

    }

    @media (max-width: 900px) {

        .navbar {
            flex-direction: column;
            gap: 15px;
        }

        .nav-links {
            justify-content: center;
            gap: 15px;
        }

    }

    @media (max-width: 600px) {

        .nav-links {
            flex-direction: column;
            width: 100%;
        }

        .nav-links a {
            text-align: center;
        }

    }

</style>

@yield('styles')


</head>

<body>

@php


$userRole = strtoupper((string) session('role', ''));

$loyaltyPoints = 0;

if ($userRole === 'CUSTOMER' && session()->has('user_id')) {

    $loyaltyPoints = DB::table('Customer')
        ->where('ID', session('user_id'))
        ->value('Loyalty_Points');

    if ($loyaltyPoints === null) {
        $loyaltyPoints = 0;
    }

}


@endphp

<!-- =========================
     NAVBAR
========================= -->

<nav class="navbar">


<!-- =========================
     LOGO
========================= -->

@if($userRole === 'ADMIN')

    <a
        href="{{ route('admin.dashboard') }}"
        class="logo"
    >
        🐾 <span>PetCare</span>
    </a>

@else

    <a
        href="{{ route('home') }}"
        class="logo"
    >
        🐾 <span>PetCare</span>
    </a>

@endif


<div class="nav-links">


    <!-- =========================
         CUSTOMER NAVIGATION
    ========================= -->

    @if($userRole === 'CUSTOMER')


        <!-- HOME -->

        <a
            href="{{ route('home') }}"
            class="{{ request()->routeIs('home') ? 'active' : '' }}"
        >
            Home
        </a>


        <!-- MY PETS -->

        <a
            href="{{ route('pets.index') }}"
            class="{{ request()->routeIs('pets.*') ? 'active' : '' }}"
        >
            My Pets
        </a>


        <!-- MY APPOINTMENTS -->

        <a
            href="{{ route('appointments.index') }}"
            class="{{ request()->routeIs('appointments.index') ? 'active' : '' }}"
        >
            My Appointments
        </a>


        <!-- BOOK APPOINTMENT -->

        <a
            href="{{ route('appointments.create') }}"
            class="{{ request()->routeIs('appointments.create') ? 'active' : '' }}"
        >
            Book Appointment
        </a>


        <!-- SERVICES -->

        <a
            href="{{ route('services.index') }}"
            class="{{ request()->routeIs('services.*') ? 'active' : '' }}"
        >
            Services
        </a>


        <!-- GROOMING REPORTS -->

        <a
            href="{{ route('grooming-reports.index') }}"
            class="{{ request()->routeIs('grooming-reports.*') ? 'active' : '' }}"
        >
            Grooming Reports
        </a>


        <!-- LOYALTY POINTS -->

        <span class="loyalty-points">
            🪙 {{ $loyaltyPoints }} Points
        </span>


        <!-- CUSTOMER ROLE -->

        <span class="role-badge customer-badge">
            CUSTOMER
        </span>


    <!-- =========================
         GROOMER NAVIGATION
    ========================= -->

    @elseif($userRole === 'GROOMER')


        <!-- HOME -->

        <a
            href="{{ route('home') }}"
            class="{{ request()->routeIs('home') ? 'active' : '' }}"
        >
            Home
        </a>


        <!-- MY APPOINTMENTS -->

        <a
            href="{{ route('appointments.index') }}"
            class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}"
        >
            My Appointments
        </a>


        <!-- GROOMING REPORTS -->

        <a
            href="{{ route('grooming-reports.index') }}"
            class="{{ request()->routeIs('grooming-reports.*') ? 'active' : '' }}"
        >
            Grooming Reports
        </a>


        <!-- GROOMER ROLE -->

        <span class="role-badge groomer-badge">
            😎 GROOMER
        </span>


    <!-- =========================
         ADMIN NAVIGATION
    ========================= -->

    @elseif($userRole === 'ADMIN')


        <!-- DASHBOARD -->

        <a
            href="{{ route('admin.dashboard') }}"
            class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        >
            Dashboard
        </a>


        <!-- CUSTOMERS -->

        <a
            href="{{ route('admin.customers') }}"
            class="{{ request()->routeIs('admin.customers*') ? 'active' : '' }}"
        >
            Customers
        </a>


        <!-- GROOMERS -->

        <a
            href="{{ route('admin.groomers') }}"
            class="{{ request()->routeIs('admin.groomers*') ? 'active' : '' }}"
        >
            Groomers
        </a>


        <!-- SERVICES -->

        <a
            href="{{ route('admin.services.index') }}"
            class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}"
        >
            Services
        </a>


        <!-- PAYMENTS -->

        <a
            href="{{ route('admin.payments') }}"
            class="{{ request()->routeIs('admin.payments*') ? 'active' : '' }}"
        >
            💳 Payments
        </a>


        <!-- ADMIN ROLE -->

        <span class="role-badge admin-badge">
            😴 ADMIN
        </span>


    @endif


    <!-- =========================
         LOGOUT
    ========================= -->

    @if(session()->has('user_id'))

        <a
            href="{{ route('logout') }}"
            class="logout-btn"
        >
            Logout
        </a>

    @endif


</div>


</nav>

<!-- =========================
     PAGE CONTENT
========================= -->

<main class="page-container">


@yield('content')


</main>

@yield('scripts')

</body>

</html>
