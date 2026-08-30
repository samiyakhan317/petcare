@extends('layouts.app')

@section('title', 'Edit Pet - PetCare')

@section('content')

<style>
    .edit-container {
        width: 85%;
        max-width: 750px;
        margin: 40px auto;
    }

    .edit-card {
        background: white;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .edit-card h1 {
        color: #285b94;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .subtitle {
        color: #666;
        margin-bottom: 30px;
        font-size: 15px;
    }

    /* =========================
       MESSAGES
       ========================= */

    .error-message {
        background: #ffe8e8;
        color: #b00000;
        border: 1px solid #f3b5b5;
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .error-message ul {
        margin-top: 8px;
        padding-left: 20px;
    }

    /* =========================
       FORM
       ========================= */

    .row {
        display: flex;
        gap: 20px;
    }

    .field {
        flex: 1;
        margin-bottom: 20px;
    }

    label {
        display: block;
        color: #444;
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 7px;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #d5dce5;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        font-family: Arial, sans-serif;
    }

    textarea {
        resize: vertical;
        min-height: 90px;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #4389d1;
        box-shadow: 0 0 0 3px rgba(67, 137, 209, 0.12);
    }

    .required {
        color: #d9534f;
    }

    /* =========================
       BUTTONS
       ========================= */

    .actions {
        display: flex;
        gap: 15px;
        margin-top: 10px;
    }

    .update-button {
        flex: 1;
        padding: 13px;
        background: #3478c9;
        color: white;
        border: none;
        border-radius: 9px;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
    }

    .update-button:hover {
        background: #285b94;
    }

    .cancel-button {
        flex: 1;
        padding: 13px;
        background: #eee;
        color: #444;
        text-decoration: none;
        border-radius: 9px;
        text-align: center;
        font-size: 15px;
        font-weight: bold;
    }

    .cancel-button:hover {
        background: #ddd;
    }

    /* =========================
       MOBILE
       ========================= */

    @media (max-width: 650px) {

        .edit-container {
            width: 92%;
        }

        .edit-card {
            padding: 25px;
        }

        .row {
            flex-direction: column;
            gap: 0;
        }

        .actions {
            flex-direction: column;
        }
    }
</style>


<div class="edit-container">

    <div class="edit-card">

        <h1>Edit Pet 🐾</h1>

        <p class="subtitle">
            Update your pet's information and health details.
        </p>


        <!-- =========================
             VALIDATION ERRORS
             ========================= -->

        @if($errors->any())

            <div class="error-message">

                <strong>Please fix the following:</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        @endif


        <!-- =========================
             EDIT FORM
             ========================= -->

        <form
            method="POST"
            action="{{ route('pets.update', $pet->Pet_ID) }}"
        >

            @csrf

            @method('PUT')


            <!-- =========================
                 NAME + BREED
                 ========================= -->

            <div class="row">

                <div class="field">

                    <label for="Name">
                        Pet Name <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="Name"
                        name="Name"
                        value="{{ old('Name', $pet->Name) }}"
                        placeholder="Enter pet name"
                        required
                    >

                </div>


                <div class="field">

                    <label for="Breed">
                        Breed <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="Breed"
                        name="Breed"
                        value="{{ old('Breed', $pet->Breed) }}"
                        placeholder="Enter breed"
                        required
                    >

                </div>

            </div>


            <!-- =========================
                 DOB + GENDER
                 ========================= -->

            <div class="row">

                <div class="field">

                    <label for="DOB">
                        Date of Birth <span class="required">*</span>
                    </label>

                    <input
                        type="date"
                        id="DOB"
                        name="DOB"
                        value="{{ old('DOB', $pet->DOB) }}"
                        max="{{ date('Y-m-d') }}"
                        required
                    >

                </div>


                <div class="field">

                    <label for="Gender">
                        Gender <span class="required">*</span>
                    </label>

                    <select
                        id="Gender"
                        name="Gender"
                        required
                    >

                        <option value="">
                            Select Gender
                        </option>

                        <option
                            value="Male"
                            {{ old('Gender', $pet->Gender) == 'Male' ? 'selected' : '' }}
                        >
                            Male
                        </option>

                        <option
                            value="Female"
                            {{ old('Gender', $pet->Gender) == 'Female' ? 'selected' : '' }}
                        >
                            Female
                        </option>

                    </select>

                </div>

            </div>


            <!-- =========================
                 WEIGHT
                 ========================= -->

            <div class="field">

                <label for="Weight">
                    Weight (kg) <span class="required">*</span>
                </label>

                <input
                    type="number"
                    id="Weight"
                    name="Weight"
                    value="{{ old('Weight', $pet->Weight) }}"
                    placeholder="Enter Weight in kg"
                    step="0.01"
                    min="0"
                    required
                >

            </div>


            <!-- =========================
                 ALLERGIES
                 ========================= -->

            <div class="field">

                <label for="Allergies">
                    Allergies
                </label>

                <textarea
                    id="Allergies"
                    name="Allergies"
                    placeholder="Enter any known allergies or write None"
                >{{ old('Allergies', $pet->Allergies) }}</textarea>

            </div>


            <!-- =========================
                 VACCINATION STATUS
                 ========================= -->

            <div class="field">

                <label for="Vaccination_Status">
                    Vaccination Status <span class="required">*</span>
                </label>

                <select
                    id="Vaccination_Status"
                    name="Vaccination_Status"
                    required
                >

                    <option value="">
                        Select vaccination Status
                    </option>

                    <option
                        value="Fully Vaccinated"
                        {{ old('Vaccination_Status', $pet->Vaccination_status) == 'Fully Vaccinated' ? 'selected' : '' }}
                    >
                        Fully Vaccinated
                    </option>

                    <option
                        value="Partially Vaccinated"
                        {{ old('Vaccination_Status', $pet->Vaccination_Status) == 'Partially Vaccinated' ? 'selected' : '' }}
                    >
                        Partially Vaccinated
                    </option>

                    <option
                        value="Not Vaccinated"
                        {{ old('Vaccination_Status', $pet->Vaccination_Status) == 'Not Vaccinated' ? 'selected' : '' }}
                    >
                        Not Vaccinated
                    </option>

                </select>

            </div>


            <!-- =========================
                 BUTTONS
                 ========================= -->

            <div class="actions">

                <button
                    type="submit"
                    class="update-button"
                >
                    Update Pet
                </button>

                <a
                    href="{{ route('pets.index') }}"
                    class="cancel-button"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection