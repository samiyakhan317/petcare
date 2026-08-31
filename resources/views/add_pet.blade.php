@extends('layouts.app')

@section('title', 'Add Pet - PetCare')

@section('content')

<style>

    .pet-container {
        width: 90%;
        max-width: 800px;
        margin: 40px auto;
    }

    .form-card {
        background: white;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
    }

    .form-card h1 {
        color: #285b94;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .subtitle {
        color: #666;
        margin-bottom: 30px;
        font-size: 15px;
    }

    .success-message {
        background: #e8f7ed;
        color: #217a3a;
        padding: 13px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }

    .error-message {
        background: #ffe8e8;
        color: #b00000;
        padding: 13px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #d9534f;
    }

    .error-message ul {
        margin: 8px 0 0 20px;
    }

    .error-message li {
        margin-bottom: 4px;
    }

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
        background: white;
        font-family: Arial, sans-serif;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #4389d1;
        box-shadow: 0 0 0 3px rgba(67, 137, 209, 0.12);
    }

    textarea {
        min-height: 90px;
        resize: vertical;
    }

    .help {
        color: #888;
        font-size: 12px;
        margin-top: 5px;
    }

    .buttons {
        display: flex;
        gap: 15px;
        margin-top: 10px;
    }

    .submit-button {
        flex: 1;
        padding: 13px;
        border: none;
        border-radius: 9px;
        background: linear-gradient(135deg, #3478c9, #54a4df);
        color: white;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
    }

    .submit-button:hover {
        background: #285b94;
    }

    .cancel-button {
        flex: 1;
        padding: 13px;
        border-radius: 9px;
        background: #eee;
        color: #444;
        text-decoration: none;
        text-align: center;
        font-size: 15px;
        font-weight: bold;
    }

    .cancel-button:hover {
        background: #ddd;
    }

    @media (max-width: 750px) {

        .pet-container {
            width: 95%;
        }

        .form-card {
            padding: 25px;
        }

        .row {
            flex-direction: column;
            gap: 0;
        }

        .buttons {
            flex-direction: column;
        }

    }

</style>

<div class="pet-container">

```
<div class="form-card">

    <h1>
        Add New Pet
    </h1>

    <p class="subtitle">
        Add your pet's information to create a pet profile.
    </p>


    {{-- SUCCESS MESSAGE --}}

    @if(session('success'))

        <div class="success-message">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR MESSAGE --}}

    @if(session('error'))

        <div class="error-message">
            {{ session('error') }}
        </div>

    @endif


    {{-- VALIDATION ERRORS --}}

    @if($errors->any())

        <div class="error-message">

            <strong>
                Please fix the following:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ADD PET FORM --}}

    <form
        method="POST"
        action="{{ route('pets.store') }}"
    >

        @csrf


        {{-- PET NAME --}}

        <div class="field">

            <label for="Name">
                Pet Name
            </label>

            <input
                type="text"
                id="Name"
                name="Name"
                value="{{ old('Name') }}"
                placeholder="Enter your pet's name"
                required
            >

        </div>


        {{-- BREED --}}

        <div class="field">

            <label for="Breed">
                Breed
            </label>

            <input
                type="text"
                id="Breed"
                name="Breed"
                value="{{ old('Breed') }}"
                placeholder="e.g. Golden Retriever"
                required
            >

        </div>


        {{-- DOB + GENDER --}}

        <div class="row">

            {{-- DATE OF BIRTH --}}

            <div class="field">

                <label for="DOB">
                    Date of Birth
                </label>

                <input
                    type="date"
                    id="DOB"
                    name="DOB"
                    value="{{ old('DOB') }}"
                    max="{{ date('Y-m-d') }}"
                    required
                >

                <p class="help">
                    Age will be calculated automatically from the date of birth.
                </p>

            </div>


            {{-- GENDER --}}

            <div class="field">

                <label for="Gender">
                    Gender
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
                        {{ old('Gender') == 'Male' ? 'selected' : '' }}
                    >
                        Male
                    </option>

                    <option
                        value="Female"
                        {{ old('Gender') == 'Female' ? 'selected' : '' }}
                    >
                        Female
                    </option>

                </select>

            </div>

        </div>


        {{-- WEIGHT --}}

        <div class="field">

            <label for="Weight">
                Weight (kg)
            </label>

            <input
                type="number"
                id="Weight"
                name="Weight"
                value="{{ old('Weight') }}"
                placeholder="e.g. 12.5"
                min="0"
                step="0.1"
                required
            >

        </div>


        {{-- ALLERGIES --}}

        <div class="field">

            <label for="Allergies">
                Allergies
            </label>

            <textarea
                id="Allergies"
                name="Allergies"
                placeholder="Enter any known allergies, or write None"
            >{{ old('Allergies') }}</textarea>

        </div>


        {{-- VACCINATION STATUS --}}

        <div class="field">

            <label for="Vaccination_Status">
                Vaccination Status
            </label>

            <select
                id="Vaccination_Status"
                name="Vaccination_Status"
                required
            >

                <option value="">
                    Select Vaccination Status
                </option>

                <option
                    value="Fully Vaccinated"
                    {{ old('Vaccination_Status') == 'Fully Vaccinated' ? 'selected' : '' }}
                >
                    Fully Vaccinated
                </option>

                <option
                    value="Partially Vaccinated"
                    {{ old('Vaccination_Status') == 'Partially Vaccinated' ? 'selected' : '' }}
                >
                    Partially Vaccinated
                </option>

                <option
                    value="Not Vaccinated"
                    {{ old('Vaccination_Status') == 'Not Vaccinated' ? 'selected' : '' }}
                >
                    Not Vaccinated
                </option>

            </select>

        </div>


        {{-- BUTTONS --}}

        <div class="buttons">

            <a
                href="{{ route('pets.index') }}"
                class="cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="submit-button"
            >
                Add Pet
            </button>

        </div>

    </form>

</div>
```

</div>

@endsection
