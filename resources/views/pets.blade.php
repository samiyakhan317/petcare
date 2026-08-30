@extends('layouts.app')

@section('title', 'My Pets - PetCare')

@section('content')

<style>
    .pets-container {
        width: 85%;
        max-width: 1100px;
        margin: 40px auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        gap: 20px;
    }

    .page-header h1 {
        color: #285b94;
        font-size: 32px;
        margin-bottom: 8px;
    }

    .subtitle {
        color: #666;
        font-size: 16px;
    }

    /* =========================
       MESSAGES
       ========================= */

    .success-message {
        background: #e8f7ee;
        color: #187a3d;
        border: 1px solid #b7e4c7;
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 25px;
        font-weight: bold;
    }

    .error-message {
        background: #ffe8e8;
        color: #b00000;
        border: 1px solid #f3b5b5;
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 25px;
        font-weight: bold;
    }

    .error-message ul {
        margin-top: 8px;
        padding-left: 20px;
    }

    /* =========================
       ADD PET BUTTON
       ========================= */

    .add-button {
        display: inline-block;
        background-color: #3478c9;
        color: white;
        text-decoration: none;
        padding: 12px 22px;
        border-radius: 8px;
        font-weight: bold;
        transition: 0.2s;
        white-space: nowrap;
    }

    .add-button:hover {
        background-color: #285b94;
        transform: translateY(-2px);
    }

    /* =========================
       PET GRID
       ========================= */

    .pet-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }

    .pet-card {
        background-color: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);

        display: flex;
        flex-direction: column;
        justify-content: space-between;

        transition: 0.2s;
    }

    .pet-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.10);
    }

    .pet-name {
        color: #285b94;
        font-size: 23px;
        margin-bottom: 18px;

        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pet-info p {
        margin: 9px 0;
        color: #555;
        font-size: 14px;
        line-height: 1.5;
    }

    .pet-info strong {
        color: #333;
    }

    /* =========================
       ACTIONS
       ========================= */

    .buttons {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;

        display: flex;
        align-items: center;
        gap: 20px;
    }

    .edit-button {
        color: #3478c9;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
    }

    .edit-button:hover {
        text-decoration: underline;
    }

    .delete-button {
        color: #d9534f;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;

        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    .delete-button:hover {
        text-decoration: underline;
    }

    /* =========================
       NO PETS
       ========================= */

    .no-pets {
        background-color: white;
        padding: 50px 30px;
        border-radius: 15px;
        text-align: center;
        color: #777;

        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .no-pets-icon {
        font-size: 50px;
        margin-bottom: 15px;
    }

    .no-pets h2 {
        color: #555;
        margin-bottom: 10px;
    }

    .no-pets p {
        margin-bottom: 0;
    }

    /* =========================
       MOBILE
       ========================= */

    @media (max-width: 750px) {

        .pets-container {
            width: 92%;
            margin: 30px auto;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .add-button {
            width: 100%;
            text-align: center;
        }

        .pet-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


<div class="pets-container">

    <!-- =========================
         PAGE HEADER
         ========================= -->

    <div class="page-header">

        <div>
            <h1>My Pets</h1>

            <p class="subtitle">
                Manage your pet profiles and health information.
            </p>
        </div>

        <!-- ONLY ONE ADD PET BUTTON -->

        <a
            href="{{ route('pets.create') }}"
            class="add-button"
        >
            + Add Pet
        </a>

    </div>


    <!-- =========================
         SUCCESS MESSAGE
         ========================= -->

    @if(session('success'))

        <div class="success-message">
            ✅ {{ session('success') }}
        </div>

    @endif


    <!-- =========================
         ERROR MESSAGE
         ========================= -->

    @if(session('error'))

        <div class="error-message">
            ❌ {{ session('error') }}
        </div>

    @endif


    <!-- =========================
         VALIDATION ERRORS
         ========================= -->

    @if($errors->any())

        <div class="error-message">

            <strong>Please fix the following:</strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <!-- =========================
         PET LIST
         ========================= -->

    @if($pets->count() > 0)

        <div class="pet-grid">

            @foreach($pets as $pet)

                <div class="pet-card">

                    <div class="pet-info">

                        <h2 class="pet-name">
                            🐾 {{ $pet->Name }}
                        </h2>

                        <p>
                            <strong>Pet ID:</strong>
                            {{ $pet->Pet_ID }}
                        </p>

                        <p>
                            <strong>Breed:</strong>
                            {{ $pet->Breed ?: 'N/A' }}
                        </p>

                        <p>
                            <strong>Date of Birth:</strong>
                            {{ $pet->DOB ?: 'N/A' }}
                        </p>

                        <p>
                            <strong>Age:</strong>
                            {{ $pet->age?? 'N/A' }}
                        </p>

                        <p>
                            <strong>Weight:</strong>

                            @if($pet->Weight !== null)
                                {{ $pet->Weight }} kg
                            @else
                                N/A
                            @endif

                        </p>

                        <p>
                            <strong>Gender:</strong>
                            {{ $pet->Gender ?: 'N/A' }}
                        </p>

                        <p>
                            <strong>Allergies:</strong>
                            {{ $pet->Allergies ?: 'None' }}
                        </p>

                        <p>
                            <strong>Vaccination Status:</strong>
                            {{ $pet->Vaccination_Status ?: 'N/A' }}
                        </p>

                    </div>


                    <!-- =========================
                         EDIT / DELETE
                         ========================= -->

                    <div class="buttons">

                        <a
                            href="{{ route('pets.edit', $pet->Pet_ID) }}"
                            class="edit-button"
                        >
                            Edit
                        </a>


                        <form
                            method="POST"
                            action="{{ route('pets.destroy', $pet->Pet_ID) }}"
                            onsubmit="return confirm('Are you sure you want to delete this pet profile?');"
                            style="display: inline;"
                        >

                            @csrf

                            @method('DELETE')

                            <button
                                type="submit"
                                class="delete-button"
                            >
                                Delete
                            </button>

                        </form>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <!-- =========================
             NO PETS
             ========================= -->

        <div class="no-pets">

            <div class="no-pets-icon">
                🐾
            </div>

            <h2>No Pets Added Yet</h2>

            <p>
                You have not added any pets yet.
                Click <strong>+ Add Pet</strong> above to create your first pet profile.
            </p>

        </div>

    @endif

</div>

@endsection