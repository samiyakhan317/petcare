@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')

<style>

    .booking-page {
        max-width: 700px;
        margin: 0 auto;
    }

    .booking-header {
        margin-bottom: 30px;
    }

    .booking-header h1 {
        color: #333;
        margin-bottom: 10px;
    }

    .booking-header p {
        color: #777;
    }

    .form-card {
        background: white;
        padding: 30px;
        border-radius: 12px;

        box-shadow:
            0 4px 15px rgba(0,0,0,0.08);
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        color: #444;
        margin-bottom: 8px;
    }

    .form-group select,
    .form-group input {
        width: 100%;
        padding: 12px;

        border: 1px solid #ddd;
        border-radius: 7px;

        font-size: 15px;
        box-sizing: border-box;
        background: white;
    }

    .service-info {
        margin-top: 8px;
        padding: 12px;

        background: #f8f9fb;
        border-radius: 7px;

        color: #555;

        display: none;
    }

    .service-info strong {
        color: #285b94;
    }

    .groomer-info {
        margin-top: 8px;
        padding: 12px;

        background: #f8f9fb;
        border-radius: 7px;

        color: #555;

        display: none;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #e8f8ee;
        color: #218838;
        border: 1px solid #b8e6c8;
    }

    .alert-error {
        background: #fdecec;
        color: #c62828;
        border: 1px solid #f5b5b5;
    }

    .book-button {
        width: 100%;
        padding: 13px;

        border: none;
        border-radius: 7px;

        background: #ff6b81;
        color: white;

        font-size: 15px;
        font-weight: bold;

        cursor: pointer;
    }

    .book-button:hover {
        background: #ff4f68;
    }

    .no-pets {
        background: white;
        padding: 40px;
        border-radius: 12px;

        text-align: center;

        box-shadow:
            0 4px 15px rgba(0,0,0,0.08);
    }

    .no-pets h2 {
        margin-bottom: 10px;
    }

    .no-pets p {
        color: #777;
        margin-bottom: 20px;
    }

    .pet-button {
        display: inline-block;

        padding: 10px 18px;

        background: #ff6b81;
        color: white;

        text-decoration: none;

        border-radius: 7px;
        font-weight: bold;
    }

</style>

<div class="booking-page">

```
{{-- =========================
     HEADER
========================== --}}

<div class="booking-header">

    <h1>
        Book Appointment
    </h1>

    <p>
        Select your pet, grooming service, groomer, date and time.
    </p>

</div>


{{-- =========================
     SUCCESS MESSAGE
========================== --}}

@if(session('success'))

    <div class="alert alert-success">

        {{ session('success') }}

    </div>

@endif


{{-- =========================
     ERROR MESSAGE
========================== --}}

@if(session('error'))

    <div class="alert alert-error">

        {{ session('error') }}

    </div>

@endif


{{-- =========================
     VALIDATION ERRORS
========================== --}}

@if($errors->any())

    <div class="alert alert-error">

        @foreach($errors->all() as $error)

            <div>
                {{ $error }}
            </div>

        @endforeach

    </div>

@endif


@if($pets->count() > 0)

    <div class="form-card">

        <form
            method="POST"
            action="{{ route('appointments.store') }}"
        >

            @csrf


            {{-- =========================
                 SELECT PET
            ========================== --}}

            <div class="form-group">

                <label for="Pet_ID">
                    Select Pet
                </label>

                <select
                    name="Pet_ID"
                    id="Pet_ID"
                    required
                >

                    <option value="">
                        -- Select Your Pet --
                    </option>

                    @foreach($pets as $pet)

                        <option
                            value="{{ $pet->Pet_ID }}"
                            {{ old('Pet_ID') == $pet->Pet_ID ? 'selected' : '' }}
                        >

                            {{ $pet->Name }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- =========================
                 SELECT SERVICE
            ========================== --}}

            @php
                /*
                |--------------------------------------------------------------------------
                | Get Service ID From URL
                |--------------------------------------------------------------------------
                |
                | Example:
                | /appointments/create?service_id=3
                |
                | If validation fails, old('Service_ID')
                | gets priority.
                |
                */

                $requestServiceId = request('service_id');
            @endphp


            <div class="form-group">

                <label for="Service_ID">
                    Select Grooming Service
                </label>

                <select
                    name="Service_ID"
                    id="Service_ID"
                    required
                >

                    <option value="">
                        -- Select Grooming Service --
                    </option>

                    @foreach($services as $service)

                        <option
                            value="{{ $service->Service_ID }}"

                            data-duration="{{ $service->Duration }}"

                            data-price="{{ number_format(
                                $service->Price,
                                2
                            ) }}"

                            data-description="{{ $service->Description }}"

                            {{ old(
                                'Service_ID',
                                $requestServiceId
                            ) == $service->Service_ID
                                ? 'selected'
                                : ''
                            }}
                        >

                            {{ $service->Service_Name }}

                            -
                            ৳{{ number_format(
                                $service->Price,
                                2
                            ) }}

                        </option>

                    @endforeach

                </select>


                {{-- =========================
                     SERVICE INFORMATION
                ========================== --}}

                <div
                    id="serviceInfo"
                    class="service-info"
                >

                    <div>

                        <strong>
                            Duration:
                        </strong>

                        <span id="serviceDuration"></span>

                        minutes

                    </div>


                    <div style="margin-top: 5px;">

                        <strong>
                            Price:
                        </strong>

                        ৳<span id="servicePrice"></span>

                    </div>


                    <div
                        id="serviceDescriptionContainer"
                        style="margin-top: 5px;"
                    >

                        <strong>
                            Description:
                        </strong>

                        <span id="serviceDescription"></span>

                    </div>

                </div>

            </div>


            {{-- =========================
                 SELECT GROOMER
            ========================== --}}

            <div class="form-group">

                <label for="Groomer_ID">
                    Select Groomer
                </label>

                <select
                    name="Groomer_ID"
                    id="Groomer_ID"
                    required
                >

                    <option value="">
                        -- Select Groomer --
                    </option>

                    @foreach($groomers as $groomer)

                        <option
                            value="{{ $groomer->ID }}"

                            data-specialization="{{ $groomer->Specialization }}"

                            data-experience="{{ $groomer->Experience }}"

                            {{ old('Groomer_ID') == $groomer->ID
                                ? 'selected'
                                : '' }}
                        >

                            {{ $groomer->Name }}

                        </option>

                    @endforeach

                </select>


                {{-- =========================
                     GROOMER INFORMATION
                ========================== --}}

                <div
                    id="groomerInfo"
                    class="groomer-info"
                >

                    <div>

                        <strong>
                            Specialization:
                        </strong>

                        <span id="groomerSpecialization"></span>

                    </div>


                    <div style="margin-top: 5px;">

                        <strong>
                            Experience:
                        </strong>

                        <span id="groomerExperience"></span>

                        years

                    </div>

                </div>

            </div>


            {{-- =========================
                 APPOINTMENT DATE
            ========================== --}}

            <div class="form-group">

                <label for="Appointment_Date">
                    Appointment Date
                </label>

                <input
                    type="date"
                    name="Appointment_Date"
                    id="Appointment_Date"
                    required

                    min="{{ date('Y-m-d') }}"

                    value="{{ old('Appointment_Date') }}"
                >

            </div>


            {{-- =========================
                 APPOINTMENT TIME
            ========================== --}}

            <div class="form-group">

                <label for="Appointment_Time">
                    Appointment Time
                </label>

                <input
                    type="time"
                    name="Appointment_Time"
                    id="Appointment_Time"
                    required

                    value="{{ old('Appointment_Time') }}"
                >

            </div>


            {{-- =========================
                 BOOK BUTTON
            ========================== --}}

            <button
                type="submit"
                class="book-button"
            >

                Book Appointment

            </button>

        </form>

    </div>

@else

    {{-- =========================
         NO PETS
    ========================== --}}

    <div class="no-pets">

        <h2>
            No Pets Found
        </h2>

        <p>
            Please add a pet before booking an appointment.
        </p>

        <a
            href="{{ route('pets.index') }}"
            class="pet-button"
        >

            Go to My Pets

        </a>

    </div>

@endif
```

</div>

<script>

    /*
    |--------------------------------------------------------------------------
    | SERVICE INFORMATION
    |--------------------------------------------------------------------------
    */

    const serviceSelect =
        document.getElementById('Service_ID');

    const serviceInfo =
        document.getElementById('serviceInfo');

    const serviceDuration =
        document.getElementById('serviceDuration');

    const servicePrice =
        document.getElementById('servicePrice');

    const serviceDescription =
        document.getElementById('serviceDescription');


    function showServiceInfo() {

        if (!serviceSelect || !serviceInfo) {
            return;
        }

        const selected =
            serviceSelect.options[
                serviceSelect.selectedIndex
            ];


        if (!selected || !selected.value) {

            serviceInfo.style.display = 'none';

            return;
        }


        serviceDuration.textContent =
            selected.dataset.duration || 'N/A';


        servicePrice.textContent =
            selected.dataset.price || '0.00';


        serviceDescription.textContent =
            selected.dataset.description ||
            'No description';


        serviceInfo.style.display = 'block';

    }


    if (serviceSelect) {

        serviceSelect.addEventListener(
            'change',
            showServiceInfo
        );

    }


    /*
    |--------------------------------------------------------------------------
    | GROOMER INFORMATION
    |--------------------------------------------------------------------------
    */

    const groomerSelect =
        document.getElementById('Groomer_ID');

    const groomerInfo =
        document.getElementById('groomerInfo');

    const groomerSpecialization =
        document.getElementById('groomerSpecialization');

    const groomerExperience =
        document.getElementById('groomerExperience');


    function showGroomerInfo() {

        if (!groomerSelect || !groomerInfo) {
            return;
        }

        const selected =
            groomerSelect.options[
                groomerSelect.selectedIndex
            ];


        if (!selected || !selected.value) {

            groomerInfo.style.display = 'none';

            return;
        }


        groomerSpecialization.textContent =
            selected.dataset.specialization ||
            'Not specified';


        groomerExperience.textContent =
            selected.dataset.experience ||
            'Not specified';


        groomerInfo.style.display = 'block';

    }


    if (groomerSelect) {

        groomerSelect.addEventListener(
            'change',
            showGroomerInfo
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SHOW INFORMATION FOR AUTOMATICALLY SELECTED VALUES
    |--------------------------------------------------------------------------
    */

    showServiceInfo();

    showGroomerInfo();

</script>

@endsection
