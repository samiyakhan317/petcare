@extends('layouts.app')

@section('title', 'Edit Grooming Report')

@section('content')

<style>

    .report-page {
        min-height: calc(100vh - 70px);
        background:
            linear-gradient(
                135deg,
                #e8f4ff,
                #f7fbff,
                #dff3f0
            );
        padding: 50px 40px;
    }

    .report-container {
        max-width: 750px;
        margin: 0 auto;
    }

    .report-card {
        background: white;
        padding: 35px;
        border-radius: 15px;
        box-shadow:
            0 10px 30px
            rgba(0, 0, 0, 0.10);
    }

    .report-card h1 {
        color: #285b94;
        margin-bottom: 8px;
    }

    .subtitle {
        color: #777;
        margin-bottom: 30px;
    }

    .appointment-info {
        background: #f0f6fc;
        padding: 18px;
        border-radius: 10px;
        margin-bottom: 25px;
    }

    .appointment-info p {
        margin: 6px 0;
        color: #555;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        color: #444;
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 15px;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3478c9;
    }

    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    .error {
        color: #c62828;
        font-size: 14px;
        margin-top: 5px;
    }

    .buttons {
        display: flex;
        gap: 12px;
        margin-top: 25px;
    }

    .button {
        padding: 12px 22px;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
        font-size: 15px;
    }

    .save-button {
        background:
            linear-gradient(
                135deg,
                #3478c9,
                #54a4df
            );
        color: white;
    }

    .save-button:hover {
        background: #285b94;
    }

    .back-button {
        background: #eee;
        color: #555;
    }

    .back-button:hover {
        background: #ddd;
    }

    .alert {
        padding: 12px 15px;
        border-radius: 7px;
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

    @media (max-width: 700px) {

        .report-page {
            padding: 30px 20px;
        }

        .report-card {
            padding: 25px;
        }

        .buttons {
            flex-direction: column;
        }

    }

</style>

<div class="report-page">

```
<div class="report-container">

    <div class="report-card">

        <h1>
            Edit Grooming Report
        </h1>

        <p class="subtitle">
            Update the grooming details for this appointment.
        </p>


        {{-- =====================================================
             SUCCESS MESSAGE
        ====================================================== --}}

        @if(session('success'))

            <div class="alert alert-success">

                {{ session('success') }}

            </div>

        @endif


        {{-- =====================================================
             ERROR MESSAGE
        ====================================================== --}}

        @if(session('error'))

            <div class="alert alert-error">

                {{ session('error') }}

            </div>

        @endif


        {{-- =====================================================
             VALIDATION ERRORS
        ====================================================== --}}

        @if($errors->any())

            <div class="alert alert-error">

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif


        {{-- =====================================================
             APPOINTMENT INFORMATION
             
             NO ORM:
             Pet_Name is directly supplied by the controller
             from a SQL JOIN.
        ====================================================== --}}

        @if(isset($appointment))

            <div class="appointment-info">

                <strong style="color: #285b94;">
                    Appointment Information
                </strong>


                <p>

                    <strong>
                        Appointment ID:
                    </strong>

                    {{ $appointment->Appointment_ID }}

                </p>


                <p>

                    <strong>
                        Pet:
                    </strong>

                    {{ $appointment->Pet_Name ?? 'N/A' }}

                </p>


                <p>

                    <strong>
                        Date:
                    </strong>

                    @if(!empty($appointment->Appointment_Date))

                        {{ date(
                            'd M Y',
                            strtotime(
                                $appointment->Appointment_Date
                            )
                        ) }}

                    @else

                        N/A

                    @endif

                </p>


                <p>

                    <strong>
                        Time:
                    </strong>

                    @if(!empty($appointment->Appointment_Time))

                        {{ date(
                            'h:i A',
                            strtotime(
                                $appointment->Appointment_Time
                            )
                        ) }}

                    @else

                        N/A

                    @endif

                </p>

            </div>

        @endif


        {{-- =====================================================
             EDIT REPORT FORM
        ====================================================== --}}

        @if(isset($appointment) && isset($report))

            <form
                method="POST"
                action="{{ route(
                    'grooming-reports.update',
                    $appointment->Appointment_ID
                ) }}"
            >

                @csrf

                @method('PUT')


                {{-- =================================================
                     COAT CONDITION
                ================================================== --}}

                <div class="form-group">

                    <label for="Coat_Condition">
                        Coat Condition
                    </label>

                    <input
                        type="text"
                        id="Coat_Condition"
                        name="Coat_Condition"
                        value="{{ old(
                            'Coat_Condition',
                            $report->Coat_Condition
                        ) }}"
                        placeholder="Example: Clean and healthy"
                    >

                    @error('Coat_Condition')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =================================================
                     SKIN CONDITION
                ================================================== --}}

                <div class="form-group">

                    <label for="Skin_Condition">
                        Skin Condition
                    </label>

                    <input
                        type="text"
                        id="Skin_Condition"
                        name="Skin_Condition"
                        value="{{ old(
                            'Skin_Condition',
                            $report->Skin_Condition
                        ) }}"
                        placeholder="Example: Healthy, no irritation"
                    >

                    @error('Skin_Condition')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =================================================
                     EAR CLEANING
                ================================================== --}}

                <div class="form-group">

                    <label for="Ear_Cleaning">
                        Ear Cleaning
                    </label>

                    <input
                        type="text"
                        id="Ear_Cleaning"
                        name="Ear_Cleaning"
                        value="{{ old(
                            'Ear_Cleaning',
                            $report->Ear_Cleaning
                        ) }}"
                        placeholder="Example: Completed"
                    >

                    @error('Ear_Cleaning')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =================================================
                     NAIL TRIMMING
                ================================================== --}}

                <div class="form-group">

                    <label for="Nail_Trimming">
                        Nail Trimming
                    </label>

                    <input
                        type="text"
                        id="Nail_Trimming"
                        name="Nail_Trimming"
                        value="{{ old(
                            'Nail_Trimming',
                            $report->Nail_Trimming
                        ) }}"
                        placeholder="Example: Completed"
                    >

                    @error('Nail_Trimming')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =================================================
                     RECOMMENDATION
                ================================================== --}}

                <div class="form-group">

                    <label for="Recommendation">
                        Recommendation
                    </label>

                    <textarea
                        id="Recommendation"
                        name="Recommendation"
                        placeholder="Enter recommendations for the pet owner..."
                    >{{ old(
                        'Recommendation',
                        $report->Recommendation
                    ) }}</textarea>

                    @error('Recommendation')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =================================================
                     GROOMER NOTES
                ================================================== --}}

                <div class="form-group">

                    <label for="Groomer_Notes">
                        Grooming Notes
                    </label>

                    <textarea
                        id="Groomer_Notes"
                        name="Groomer_Notes"
                        placeholder="Enter additional grooming notes..."
                    >{{ old(
                        'Groomer_Notes',
                        $report->Groomer_Notes
                    ) }}</textarea>

                    @error('Groomer_Notes')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =================================================
                     BUTTONS
                ================================================== --}}

                <div class="buttons">

                    <button
                        type="submit"
                        class="button save-button"
                    >
                        Update Grooming Report
                    </button>


                    <a
                        href="{{ route(
                            'grooming-reports.index'
                        ) }}"
                        class="button back-button"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        @else

            <div class="alert alert-error">

                Grooming report information could not be loaded.

            </div>

        @endif

    </div>

</div>
```

</div>

@endsection
