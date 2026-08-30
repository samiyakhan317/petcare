<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PetCare - Manage Customers</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background: linear-gradient(
                135deg,
                #e8f4ff,
                #f7fbff,
                #dff3f0
            );
            padding: 40px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1 {
            color: #285b94;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 20px;
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

        /* =========================
           ADD CUSTOMER FORM
        ========================== */

        .form-container {
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
            gap: 15px;
        }

        .form-group {
            width: 100%;
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

        /* =========================
           PHONE SECTION
        ========================== */

        .phone-section {
            grid-column: 1 / -1;
            margin-top: 5px;
        }

        .phone-section label {
            display: block;
            margin-bottom: 8px;
            color: #444;
            font-weight: bold;
        }

        .phone-note {
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 10px;
        }

        .phone-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .phone-row input {
            flex: 1;
        }

        .remove-phone {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .remove-phone:hover {
            background: #c0392b;
        }

        .add-phone {
            background: #6c757d;
            color: white;
            border: none;
            padding: 9px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 3px;
        }

        .add-phone:hover {
            background: #545b62;
        }

        /* =========================
           ADD BUTTON
        ========================== */

        .add-button-container {
            grid-column: 1 / -1;
        }

        .btn-add {
            background: #3478c9;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-add:hover {
            background: #285b94;
        }

        /* =========================
           CUSTOMER TABLE
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

        .table-title {
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
           PHONE DISPLAY
        ========================== */

        .phone-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .phone-item {
            background: #f1f3f5;
            padding: 5px 8px;
            border-radius: 5px;
            width: fit-content;
        }

        .no-phone {
            color: #999;
            font-style: italic;
        }

        /* =========================
           DELETE BUTTON
        ========================== */

        .btn-delete {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        /* =========================
           BACK BUTTON
        ========================== */

        .back {
            display: inline-block;
            margin-top: 25px;
            padding: 11px 20px;
            background: #3478c9;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .back:hover {
            background: #285b94;
        }

        /* =========================
           EMPTY
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

            body {
                padding: 20px;
            }

            .add-form {
                grid-template-columns: 1fr;
            }

            .phone-row {
                flex-direction: column;
            }

            .remove-phone {
                padding: 8px;
            }

            table {
                font-size: 13px;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <h1>Manage Customers</h1>

    <p class="subtitle">
        View and manage registered PetCare customers.
    </p>


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


    {{-- =========================
         ADD NEW CUSTOMER
    ========================== --}}

    <div class="form-container">

        <h2>Add New Customer</h2>

        <form
            method="POST"
            action="{{ route('admin.customers.store') }}"
            class="add-form"
        >

            @csrf


            {{-- First Name --}}

            <div class="form-group">

                <label>
                    First Name
                </label>

                <input
                    type="text"
                    name="first_name"
                    placeholder="Enter first name"
                    value="{{ old('first_name') }}"
                    required
                >

            </div>


            {{-- Last Name --}}

            <div class="form-group">

                <label>
                    Last Name
                </label>

                <input
                    type="text"
                    name="last_name"
                    placeholder="Enter last name"
                    value="{{ old('last_name') }}"
                    required
                >

            </div>


            {{-- Email --}}

            <div class="form-group">

                <label>
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter email"
                    value="{{ old('email') }}"
                    required
                >

            </div>


            {{-- Address --}}

            <div class="form-group">

                <label>
                    Address
                </label>

                <input
                    type="text"
                    name="address"
                    placeholder="Enter address"
                    value="{{ old('address') }}"
                    required
                >

            </div>


            {{-- Password --}}

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

            </div>


            {{-- =========================
                 PHONE NUMBERS
            ========================== --}}

            <div class="phone-section">

                <label>
                    Phone Numbers
                </label>

                <div class="phone-note">
                    At least one phone number is required. You can add multiple phone numbers.
                </div>


                <div id="phone-container">

                    {{-- First Phone Number --}}

                    <div class="phone-row">

                        <input
                            type="text"
                            name="phone_numbers[]"
                            placeholder="Enter phone number"
                            value="{{ old('phone_numbers.0') }}"
                            required
                        >

                    </div>

                    {{-- If validation returns multiple phones, show them --}}

                    @if(old('phone_numbers'))

                        @foreach(old('phone_numbers') as $index => $oldPhone)

                            @if($index > 0)

                                <div class="phone-row">

                                    <input
                                        type="text"
                                        name="phone_numbers[]"
                                        placeholder="Enter phone number"
                                        value="{{ $oldPhone }}"
                                    >

                                    <button
                                        type="button"
                                        class="remove-phone"
                                        onclick="removePhone(this)"
                                    >
                                        Remove
                                    </button>

                                </div>

                            @endif

                        @endforeach

                    @endif

                </div>


                <button
                    type="button"
                    class="add-phone"
                    onclick="addPhone()"
                >
                    + Add Another Phone
                </button>

            </div>


            {{-- =========================
                 ADD CUSTOMER BUTTON
            ========================== --}}

            <div class="add-button-container">

                <button
                    type="submit"
                    class="btn-add"
                >
                    Add Customer
                </button>

            </div>

        </form>

    </div>


    {{-- =========================
         CUSTOMER LIST
    ========================== --}}

    <div class="table-container">

        <h2 class="table-title">
            Customer List
        </h2>


        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>First Name</th>

                    <th>Last Name</th>

                    <th>Email</th>

                    <th>Address</th>

                    <th>Phone Numbers</th>

                    <th>Registered Pets</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                @forelse($customers as $customer)

                    <tr>

                        {{-- ID --}}

                        <td>
                            {{ $customer->ID }}
                        </td>


                        {{-- First Name --}}

                        <td>
                            {{ $customer->customer->First_name ?? 'N/A' }}
                        </td>


                        {{-- Last Name --}}

                        <td>
                            {{ $customer->customer->Last_name ?? 'N/A' }}
                        </td>


                        {{-- Email --}}

                        <td>
                            {{ $customer->Email }}
                        </td>


                        {{-- Address --}}

                        <td>
                            {{ $customer->customer->Address ?? 'N/A' }}
                        </td>


                        {{-- Phone Numbers --}}

                        <td>

                            @if(
                                $customer->customer &&
                                $customer->customer->phoneNumbers &&
                                $customer->customer->phoneNumbers->count() > 0
                            )

                                <div class="phone-list">

                                    @foreach(
                                        $customer->customer->phoneNumbers
                                        as $phone
                                    )

                                        <span class="phone-item">
                                            {{ $phone->Phone_Number }}
                                        </span>

                                    @endforeach

                                </div>

                            @else

                                <span class="no-phone">
                                    No phone number
                                </span>

                            @endif

                        </td>


                        {{-- Registered Pets --}}

                        <td>

                            <strong>
                                {{ $customer->pets_count }}
                            </strong>

                        </td>


                        {{-- Delete --}}

                        <td>

                            <form
                                method="POST"
                                action="{{ route('admin.customers.destroy', $customer->ID) }}"
                                onsubmit="return confirm('Are you sure you want to delete this customer?');"
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
                            colspan="8"
                            class="empty"
                        >
                            No customers found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =========================
         BACK TO DASHBOARD
    ========================== --}}

    <a
        href="{{ route('admin.dashboard') }}"
        class="back"
    >
        ← Back to Dashboard
    </a>

</div>


{{-- =========================
     JAVASCRIPT
========================== --}}

<script>

    function addPhone() {

        const container =
            document.getElementById('phone-container');

        const phoneRow =
            document.createElement('div');

        phoneRow.className = 'phone-row';

        phoneRow.innerHTML = `
            <input
                type="text"
                name="phone_numbers[]"
                placeholder="Enter phone number"
            >

            <button
                type="button"
                class="remove-phone"
                onclick="removePhone(this)"
            >
                Remove
            </button>
        `;

        container.appendChild(phoneRow);
    }


    function removePhone(button) {

        const row = button.parentElement;

        row.remove();

    }

</script>

</body>

</html>