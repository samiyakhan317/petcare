@extends('layouts.app')

@section('title', 'Manage Groomers')

@section('content')

<style>

    .groomers-page {
        padding: 30px 0;
    }

    .groomers-container {
        width: 100%;
    }

    /* =========================
       PAGE HEADER
    ========================== */

    .groomers-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .groomers-header h1 {
        color: #285b94;
        margin-bottom: 8px;
        font-size: 28px;
    }

    .subtitle {
        color: #777;
        margin: 0;
    }

    /* =========================
       ADD BUTTON
    ========================== */

    .btn-show-form {
        background: #3478c9;
        color: white;
        border: none;
        padding: 11px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        font-size: 15px;
    }

    .btn-show-form:hover {
        background: #285b94;
    }

    /* =========================
       ALERTS
    ========================== */

    .alert {
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert ul {
        margin-top: 8px;
        padding-left: 20px;
    }

    /* =========================
       ADD GROOMER FORM
    ========================== */

    .form-container {
        display: none;

        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;

        box-shadow:
            0 10px 30px
            rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
        color: #285b94;
        margin-bottom: 20px;
    }

    .add-form {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .form-group {
        width: 100%;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #444;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .add-form input {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 0.95rem;
    }

    .add-form input:focus {
        outline: none;
        border-color: #3478c9;
    }

    .password-note {
        display: block;
        margin-top: 5px;
        color: #777;
        font-size: 13px;
    }

    /* =========================
       FORM BUTTONS
    ========================== */

    .form-actions {
        grid-column: 1 / -1;

        display: flex;
        gap: 10px;

        margin-top: 5px;
    }

    .btn-add {
        background: #1a631a;
        color: white;
        border: none;
        padding: 11px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-add:hover {
        background: #145014;
    }

    .btn-cancel {
        background: #777;
        color: white;
        border: none;
        padding: 11px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-cancel:hover {
        background: #555;
    }

    /* =========================
       GROOMER TABLE
    ========================== */

    .table-container {
        background: white;
        border-radius: 15px;
        padding: 20px;

        box-shadow:
            0 10px 30px
            rgba(0, 0, 0, 0.1);

        overflow-x: auto;
    }

    .table-container h2 {
        color: #285b94;
        margin-bottom: 15px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #3478c9;
        color: white;
        padding: 14px;
        text-align: left;
        white-space: nowrap;
    }

    td {
        padding: 14px;
        border-bottom: 1px solid #eee;
        color: #444;
        vertical-align: top;
    }

    tr:hover {
        background: #f7fbff;
    }

    /* =========================
       DELETE BUTTON
    ========================== */

    .btn-delete {
        background: #e74c3c;
        color: white;
        border: none;
        padding: 7px 12px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-delete:hover {
        background: #c0392b;
    }

    /* =========================
       EMPTY TABLE
    ========================== */

    .empty {
        text-align: center;
        padding: 30px;
        color: #777;
    }

    /* =========================
       RESPONSIVE
    ========================== */

    @media (max-width: 700px) {

        .groomers-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .add-form {
            grid-template-columns: 1fr;
        }

        .form-group.full {
            grid-column: 1;
        }

        .form-actions {
            grid-column: 1;
        }

        table {
            font-size: 13px;
        }

    }

</style>

<div class="groomers-page">

```
<div class="groomers-container">


    {{-- =========================
         PAGE HEADER
    ========================== --}}

    <div class="groomers-header">

        <div>

            <h1>
                Manage Groomers
            </h1>

            <p class="subtitle">
                View and manage registered PetCare groomers.
            </p>

        </div>


        <button
            type="button"
            class="btn-show-form"
            onclick="showGroomerForm()"
        >
            + Add Groomer
        </button>

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


    {{-- =========================
         ADD NEW GROOMER FORM
    ========================== --}}

    <div
        class="form-container"
        id="groomerForm"
    >

        <h2>
            Add New Groomer
        </h2>


        <form
            method="POST"
            action="{{ route('admin.groomers.store') }}"
            class="add-form"
        >

            @csrf


            {{-- GROOMER NAME --}}

            <div class="form-group">

                <label>
                    Groomer Name
                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter groomer name"
                    value="{{ old('name') }}"
                    required
                >

            </div>


            {{-- PHONE --}}

            <div class="form-group">

                <label>
                    Phone
                </label>

                <input
                    type="text"
                    name="phone"
                    placeholder="Enter phone number"
                    value="{{ old('phone') }}"
                    required
                >

            </div>


            {{-- EMAIL --}}

            <div class="form-group">

                <label>
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter email address"
                    value="{{ old('email') }}"
                >

            </div>


            {{-- PASSWORD --}}

            <div class="form-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Exactly 8 characters"
                    minlength="8"
                    maxlength="8"
                    required
                >

                <small class="password-note">
                    Password must be exactly 8 characters.
                </small>

            </div>


            {{-- EXPERIENCE --}}

            <div class="form-group">

                <label>
                    Experience (years)
                </label>

                <input
                    type="number"
                    name="experience"
                    placeholder="Experience in years"
                    value="{{ old('experience') }}"
                    min="0"
                    step="0.1"
                    required
                >

            </div>


            {{-- SPECIALIZATION --}}

            <div class="form-group">

                <label>
                    Specialization
                </label>

                <input
                    type="text"
                    name="specialization"
                    placeholder="Enter specialization"
                    value="{{ old('specialization') }}"
                    required
                >

            </div>


            {{-- =========================
                 FORM BUTTONS
            ========================== --}}

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-add"
                >
                    Add Groomer
                </button>

                <button
                    type="button"
                    class="btn-cancel"
                    onclick="hideGroomerForm()"
                >
                    Cancel
                </button>

            </div>


        </form>

    </div>


    {{-- =========================
         GROOMER LIST
    ========================== --}}

    <div class="table-container">

        <h2>
            Groomer List
        </h2>


        <table>

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Name
                    </th>

                    <th>
                        Email
                    </th>

                    <th>
                        Phone
                    </th>

                    <th>
                        Experience
                    </th>

                    <th>
                        Specialization
                    </th>

                    <th>
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>


                @forelse($groomers as $groomer)

                    <tr>

                        <td>
                            {{ $groomer->ID }}
                        </td>

                        <td>
                            {{ $groomer->Name }}
                        </td>

                        <td>
                            {{ $groomer->Email ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $groomer->Phone }}
                        </td>

                        <td>
                            {{ $groomer->Experience }}
                        </td>

                        <td>
                            {{ $groomer->Specialization }}
                        </td>

                        <td>

                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.groomers.destroy',
                                    $groomer->ID
                                ) }}"
                                onsubmit="return confirm(
                                    'Are you sure you want to delete this groomer?'
                                );"
                                style="display: inline;"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn-delete"
                                >
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="empty"
                        >

                            No groomers found.

                        </td>

                    </tr>

                @endforelse


            </tbody>

        </table>

    </div>

</div>
```

</div>

<script>

    function showGroomerForm() {

        document.getElementById('groomerForm').style.display = 'block';

        document.getElementById('groomerForm').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    }


    function hideGroomerForm() {

        document.getElementById('groomerForm').style.display = 'none';

    }

</script>

@endsection
