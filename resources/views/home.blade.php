@extends('layouts.app')

@section('title', 'PetCare & Grooming')

@section('content')

<style>

    /* =================================================
       HERO SECTION
    ================================================= */

    .hero {

        min-height: 430px;

        display: flex;

        align-items: center;

        justify-content: space-between;

        padding: 60px 10%;

        background:
            linear-gradient(
                135deg,
                #e8f4ff,
                #f7fbff
            );

    }


    .hero-text {

        max-width: 550px;

    }


    .welcome {

        font-size: 14px;

        color: #777;

        margin-bottom: 12px;

    }


    .hero-text h1 {

        font-size: 45px;

        color: #285b94;

        margin-bottom: 20px;

    }


    .hero-text h1 span {

        color: #3478c9;

    }


    .hero-text p {

        font-size: 18px;

        line-height: 1.7;

        color: #666;

        margin-bottom: 25px;

    }


    .hero-button {

        display: inline-block;

        background-color: #3478c9;

        color: white;

        text-decoration: none;

        padding: 14px 25px;

        border-radius: 10px;

        font-weight: bold;

        transition:
            background-color 0.2s,
            transform 0.2s;

    }


    .hero-button:hover {

        background-color: #285b94;

        transform: translateY(-2px);

    }


    /* =================================================
       PET IMAGE
    ================================================= */

    .pet-icon {

        width: 280px;

        height: 280px;

        background-color: white;

        border-radius: 50%;

        display: flex;

        align-items: center;

        justify-content: center;

        overflow: hidden;

        box-shadow:
            0 15px 40px
            rgba(0, 0, 0, 0.10);

    }


    .pet-icon img {

        width: 85%;

        height: 85%;

        object-fit: contain;

        border-radius: 50%;

    }


    /* =================================================
       MOBILE
    ================================================= */

    @media (max-width: 750px) {

        .hero {

            flex-direction: column;

            text-align: center;

            gap: 40px;

        }


        .hero-text h1 {

            font-size: 35px;

        }


        .pet-icon {

            width: 200px;

            height: 200px;

        }

    }

</style>

<!-- =====================================================
     HERO
====================================================== -->

<section class="hero">

```
<div class="hero-text">

    <!-- =================================================
         WELCOME
    ================================================== -->

    <div class="welcome">

        Welcome back,

        {{ $display_name }}

        🐾

    </div>


    <!-- =================================================
         CUSTOMER
    ================================================== -->

    @if ($role === 'CUSTOMER')

        <h1>

            Better Care for

            <span>
                Every Pet
            </span>

        </h1>


        <p>

            Manage your pets, book grooming
            appointments, keep track of their
            grooming history, and explore
            grooming services — all in one place.

        </p>


        <a
            href="{{ route('pets.index') }}"
            class="hero-button"
        >

            Manage My Pets &rarr;

        </a>


    <!-- =================================================
         GROOMER
    ================================================== -->

    @elseif ($role === 'GROOMER')

        <h1>

            Grooming Care for

            <span>
                Every Pet
            </span>

        </h1>


        <p>

            Manage your assigned
            appointments and create
            grooming reports for pets
            after their grooming sessions.

        </p>

    @endif

</div>


<!-- =================================================
     IMAGE
================================================= -->

<div class="pet-icon">

    <img
        src="{{ asset('pets.png') }}"
        alt="Pets"
    >

</div>
```

</section>

@endsection
