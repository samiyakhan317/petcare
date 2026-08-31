@extends('layouts.app')

@section('title', 'Manage Customers')

@section('content')

<style>

    .customers-page {
        padding: 30px 0;
    }

    .customers-container {
        width: 100%;
    }

    /* =========================
       HEADER
    ========================= */

    .customers-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .customers-header h1 {
        color: #222;
        font-size: 28px;
    }

    .add-btn {
        background: #1a631a;
        color: white;
        padding: 10px 18px;
        border: none;
        border-radius: 7px;
        cursor: pointer;
        font-size: 15px;
        font-weight: bold;
    }

    .add-btn:hover {
        background: #145014;
    }


    /* =========================
       FORM
    ========================= */

    .form-box {
        display: none;
        background: white;
        padding: 25px;
        margin-bottom: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    }

    .form-box h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full {
        grid-column: 1 / 3;
    }

    .form-group label {
        margin-bottom: 7px;
        font-weight: bold;
        color: #333;
    }

    .form-group input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        width: 100%;
    }

    .form-group input:focus {
        outline: none;
        border-color: #285b94;
    }


    /* =========================
       PHONE NUMBERS
    ========================= */

    .phone-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        width: 100%;
    }

    .phone-row input {
        flex: 1;
    }

    .add-phone {
        margin-top: 8px;
        background: #285b94;
        color: white;
        border: none;
        padding: 9px 15px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }

    .add-phone:hover {
        background: #1d426b;
    }

    .remove-phone {
        background: #990e23;
        color: white;
        border: none;
        padding: 9px 12px;
        border-radius: 6px;
        cursor: pointer;
        white-space: nowrap;
    }

    .remove-phone:hover {
        background: #750b1a;
    }


    /* =========================
       FORM BUTTONS
    ========================= */

    .form-actions {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }

    .save-btn {
        background: #1a631a;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }

    .save-btn:hover {
        background: #145014;
    }

    .cancel-btn {
        background: #777;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }

    .cancel-btn:hover {
        background: #555;
    }


    /* =========================
       TABLE
    ========================= */

    .table-box {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #333;
        color: white;
        padding: 13px;
        text-align: left;
        white-space: nowrap;
    }

    td {
        padding: 13px;
        border-bottom: 1px solid #ddd;
        color: #333;
        vertical-align: top;
    }

    tr:hover {
        background: #f7f7f7;
    }

    .empty-message {
        text-align: center;
        padding: 25px;
        color: #777;
    }


    /* =========================
       MESSAGES
    ========================= */

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 12px 15px;
        border-radius: 7px;
        margin-bottom: 20px;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 12px 15px;
        border-radius: 7px;
        margin-bottom: 20px;
    }

    .validation-errors {
        background: #f8d7da;
        color: #721c24;
        padding: 15px 20px;
        border-radius: 7px;
        margin-bottom: 20px;
    }

    .validation-errors ul {
        margin-top: 8px;
        padding-left: 20px;
    }


    /* =========================
       DELETE BUTTON
    ========================= */

    .delete-btn {
        background: #990e23;
        color: white;
        border: none;
        padding: 7px 12px;
        border-radius: 6px;
        cursor: pointer;
    }

    .delete-btn:hover {
        background: #750b1a;
    }


    /* =========================
       PHONE DISPLAY
    ========================= */

    .phone-display {
        margin-bottom: 4px;
    }


    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 700px) {

        .customers-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full {
            grid-column: 1;
        }

        .phone-row {
            flex-direction: column;
        }

        .remove-phone {
            width: fit-content;
        }

    }

</style>

<div class="customers-page">

```
<div class="customers-container">


    <!-- =========================
         HEADER
    ========================== -->

    <div class="customers-header">

        <h1>
            Customer List
        </h1>

        <button
            type="button"
            class="add-btn"
            onclick="showCustomerForm()"
        >
            + Add Customer
        </button>

    </div>


    <!-- =========================
         SUCCESS MESSAGE
    ========================== -->

    @if(session('success'))

        <div class="success-message">
            {{ session('success') }}
        </div>

    @endif


    <!-- =========================
         ERROR MESSAGE
    ========================== -->

    @if(session('error'))

        <div class="error-message">
            {{ session('error') }}
        </div>

    @endif


    <!-- =========================
         VALIDATION ERRORS
    ========================== -->

    @if($errors->any())

        <div class="validation-errors">

            <strong>
                Please fix the following errors:
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


    <!-- =========================
         ADD CUSTOMER FORM
    ========================== -->

    <div
        class="form-box"
        id="customerForm"
    >

        <h2>
            Add New Customer
        </h2>


        <form
            action="{{ route('admin.customers.store') }}"
            method="POST"
        >

            @csrf


            <div class="form-grid">


                <!-- FIRST NAME -->

                <div class="form-group">

                    <label>
                        First Name
                    </label>

                    <input
                        type="text"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        placeholder="Enter first name"
                        required
                    >

                </div>


                <!-- LAST NAME -->

                <div class="form-group">

                    <label>
                        Last Name
                    </label>

                    <input
                        type="text"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        placeholder="Enter last name"
                        required
                    >

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter email"
                        required
                    >

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        minlength="8"
                        maxlength="8"
                        placeholder="Exactly 8 characters"
                        required
                    >

                    <small style="margin-top:5px;color:#777;">
                        Password must be exactly 8 characters.
                    </small>

                </div>


                <!-- ADDRESS -->

                <div class="form-group full">

                    <label>
                        Address
                    </label>

                    <input
                        type="text"
                        name="address"
                        value="{{ old('address') }}"
                        placeholder="Enter address"
                        required
                    >

                </div>


                <!-- =========================
                     PHONE NUMBERS
                ========================== -->

                <div class="form-group full">

                    <label>
                        Phone Numbers
                    </label>


                    <div id="phoneContainer">


                        <!-- FIRST PHONE -->

                        <div class="phone-row">

                            <input
                                type="text"
                                name="phone_numbers[]"
                                placeholder="Enter phone number"
                                required
                            >

                            <button
                                type="button"
                                class="remove-phone"
                                onclick="removePhone(this)"
                            >
                                Remove
                            </button>

                        </div>


                    </div>


                    <button
                        type="button"
                        class="add-phone"
                        onclick="addPhone()"
                    >
                        + Add Another Phone
                    </button>

                </div>


            </div>


            <!-- =========================
                 FORM BUTTONS
            ========================== -->

            <div class="form-actions">

                <button
                    type="submit"
                    class="save-btn"
                >
                    Add Customer
                </button>

                <button
                    type="button"
                    class="cancel-btn"
                    onclick="hideCustomerForm()"
                >
                    Cancel
                </button>

            </div>


        </form>

    </div>


    <!-- =========================
         CUSTOMER TABLE
    ========================== -->

    <div class="table-box">

        <table>

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        First Name
                    </th>

                    <th>
                        Last Name
                    </th>

                    <th>
                        Email
                    </th>

                    <th>
                        Address
                    </th>

                    <th>
                        Phone Numbers
                    </th>

                    <th>
                        Pets
                    </th>

                    <th>
                        Loyalty Points
                    </th>

                    <th>
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>


                @forelse($customers as $customer)

                    <tr>


                        <!-- ID -->

                        <td>
                            {{ $customer->ID }}
                        </td>


                        <!-- FIRST NAME -->

                        <td>
                            {{ $customer->First_name }}
                        </td>


                        <!-- LAST NAME -->

                        <td>
                            {{ $customer->Last_name }}
                        </td>


                        <!-- EMAIL -->

                        <td>
                            {{ $customer->Email }}
                        </td>


                        <!-- ADDRESS -->

                        <td>
                            {{ $customer->Address }}
                        </td>


                        <!-- PHONE NUMBERS -->

                        <td>

                            @if($customer->phoneNumbers->count() > 0)

                                @foreach($customer->phoneNumbers as $phone)

                                    <div class="phone-display">
                                        {{ $phone->Phone_Number }}
                                    </div>

                                @endforeach

                            @else

                                <span>
                                    No phone
                                </span>

                            @endif

                        </td>


                        <!-- PET COUNT -->

                        <td>
                            {{ $customer->pets_count }}
                        </td>


                        <!-- LOYALTY POINTS -->

                        <td>
                            {{ $customer->Loyalty_Points }}
                        </td>


                        <!-- DELETE -->

                        <td>

                            <form
                                action="{{ route('admin.customers.destroy', $customer->ID) }}"
                                method="POST"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this customer?')"
                                >
                                    Delete
                                </button>

                            </form>

                        </td>


                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="empty-message"
                        >
                            No customers found.
                        </td>

                    </tr>

                @endforelse


            </tbody>

        </table>

    </div>


</div>
```

</div>

<!-- =========================
     JAVASCRIPT
========================= -->

<script>

    function showCustomerForm() {

        const form =
            document.getElementById('customerForm');

        form.style.display = 'block';

        form.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    }


    function hideCustomerForm() {

        const form =
            document.getElementById('customerForm');

        form.style.display = 'none';

    }


    function addPhone() {

        const container =
            document.getElementById('phoneContainer');


        const row =
            document.createElement('div');

        row.className = 'phone-row';


        row.innerHTML = `

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


        container.appendChild(row);

    }


    function removePhone(button) {

        const container =
            document.getElementById('phoneContainer');

        const rows =
            container.querySelectorAll('.phone-row');


        /*
         * Always keep at least
         * one phone number field.
         */

        if (rows.length > 1) {

            button.parentElement.remove();

        }

    }

</script>

@endsection
