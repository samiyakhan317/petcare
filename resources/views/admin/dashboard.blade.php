@extends('layouts.app')

@section('title', 'PetCare - Admin Dashboard')

@section('content')

<style>

    .admin-page {
        min-height: calc(100vh - 70px);

        background:
            linear-gradient(
                135deg,
                #e8f4ff,
                #f7fbff,
                #dff3f0
            );

        padding: 0;
    }


    /* =========================
       MAIN WELCOME SECTION
    ========================== */

    .admin-welcome {
        min-height: calc(100vh - 70px);

        display: flex;

        align-items: center;

        justify-content: center;

        padding: 40px;
    }


    .welcome-container {
        max-width: 1100px;

        width: 100%;

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 50px;

        background: white;

        padding: 45px;

        border-radius: 24px;

        box-shadow:
            0 15px 40px rgba(0, 0, 0, 0.10);
    }


    /* =========================
       TEXT SECTION
    ========================== */

    .welcome-text {
        flex: 1;

        padding: 10px;
    }


    .admin-label {
        display: inline-block;

        padding: 8px 18px;

        background: #e8f4ff;

        color: #285b94;

        border-radius: 25px;

        font-size: 14px;

        font-weight: bold;

        margin-bottom: 18px;
    }


    .welcome-text h1 {
        color: #285b94;

        font-size: 42px;

        line-height: 1.2;

        margin-bottom: 18px;
    }


    .welcome-text h1 span {
        color: #3478c9;
    }


    .welcome-text p {
        color: #666;

        font-size: 17px;

        line-height: 1.8;

        margin-bottom: 15px;
    }


    /* =========================
       MESSAGE BOX
    ========================== */

    .petcare-message {
        margin-top: 22px;

        padding: 18px 22px;

        background: #f2f9ff;

        border-left: 5px solid #3478c9;

        border-radius: 8px;

        color: #555;

        line-height: 1.6;

        max-width: 600px;
    }


    /* =========================
       IMAGE SECTION
    ========================== */

    .welcome-image {
        flex: 0 0 350px;

        display: flex;

        align-items: center;

        justify-content: center;
    }


    .welcome-image img {
        width: 320px;

        height: auto;

        max-height: 260px;

        object-fit: contain;

        border-radius: 15px;
    }


    /* =========================
       RESPONSIVE
    ========================== */

    @media (max-width: 850px) {

        .welcome-container {
            flex-direction: column;

            text-align: center;

            gap: 25px;

            padding: 35px;
        }


        .welcome-image {
            order: -1;

            flex: none;
        }


        .welcome-image img {
            width: 260px;

            max-height: 220px;
        }


        .petcare-message {
            text-align: left;

            margin-left: auto;

            margin-right: auto;
        }


        .welcome-text h1 {
            font-size: 34px;
        }
    }


    @media (max-width: 600px) {

        .admin-welcome {
            padding: 20px 15px;
        }


        .welcome-container {
            padding: 25px 20px;

            border-radius: 18px;
        }


        .welcome-image img {
            width: 220px;

            max-height: 190px;
        }


        .welcome-text h1 {
            font-size: 29px;
        }


        .welcome-text p {
            font-size: 15px;
        }
    }

</style>

<div class="admin-page">

```
<section class="admin-welcome">

    <div class="welcome-container">


        <!-- =========================
             WELCOME TEXT
        ========================== -->

        <div class="welcome-text">

            <div class="admin-label">
                🐾 PETCARE ADMIN
            </div>

            <h1>
                Welcome to
                <span>PetCare</span>
            </h1>

            <div class="petcare-message">

                <strong>
                    Every pet deserves the best care.
                </strong>

                <br>

                Thank you for helping us provide a safe,
                friendly and comfortable grooming experience
                for every pet.

            </div>

        </div>


        <!-- =========================
             PET IMAGE
        ========================== -->

        <div class="welcome-image">

            <img
                src="{{ asset('pets.png') }}"
                alt="Cute pets"
            >

        </div>


    </div>

</section>
```

</div>

@endsection
