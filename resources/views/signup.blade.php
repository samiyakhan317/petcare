<!DOCTYPE html>
<html>
<head>
    <title>PetCare - Create Account</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;

            background:
                linear-gradient(
                    135deg,
                    #e8f4ff,
                    #f7fbff,
                    #dff3f0
                );

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 30px;
        }


        /* =========================
           MAIN CONTAINER
        ========================= */

        .container {
            width: 900px;
            min-height: 600px;

            background: white;

            border-radius: 25px;
            overflow: hidden;

            display: flex;

            box-shadow:
                0 20px 50px
                rgba(0, 0, 0, 0.15);
        }


        /* =========================
           LEFT SIDE
        ========================= */

        .left {
            width: 42%;

            background:
                linear-gradient(
                    145deg,
                    #3478c9,
                    #5ca9e6
                );

            color: white;

            padding: 50px 40px;

            display: flex;
            flex-direction: column;

            justify-content: center;

            text-align: center;
        }

        .paw {
            font-size: 70px;
            margin-bottom: 20px;
        }

        .left h1 {
            font-size: 35px;
            margin-bottom: 15px;
        }

        .left p {
            font-size: 16px;
            line-height: 1.7;
            opacity: 0.95;
        }

        .features {
            margin-top: 35px;
            text-align: left;
        }

        .features p {
            margin: 15px 0;
            font-size: 14px;
        }


        /* =========================
           RIGHT SIDE
        ========================= */

        .right {
            width: 58%;
            padding: 45px 55px;
        }

        .right h2 {
            color: #285b94;
            font-size: 30px;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 25px;
            font-size: 14px;
        }


        /* =========================
           MESSAGE
        ========================= */

        .message {
            padding: 12px 15px;

            margin-bottom: 20px;

            border-radius: 8px;

            text-align: center;

            font-size: 14px;

            line-height: 1.5;
        }

        .message.success {
            background: #eaf8ef;
            color: #218838;

            border: 1px solid #b8e6c5;
        }

        .message.error {
            background: #fdeaea;
            color: #c0392b;

            border: 1px solid #f2b8b5;
        }

        .message ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .message li {
            margin: 3px 0;
        }


        /* =========================
           FORM
        ========================= */

        .row {
            display: flex;
            gap: 15px;
        }

        .field {
            flex: 1;
            margin-bottom: 17px;
        }

        label {
            display: block;

            color: #444;

            font-size: 14px;

            font-weight: bold;

            margin-bottom: 7px;
        }

        input,
        select {
            width: 100%;

            padding: 12px 14px;

            border: 1px solid #d5dce5;

            border-radius: 9px;

            font-size: 14px;

            outline: none;

            background: white;
        }

        input:focus,
        select:focus {
            border-color: #4389d1;

            box-shadow:
                0 0 0 3px
                rgba(67, 137, 209, 0.12);
        }


        /* =========================
           PASSWORD
        ========================= */

        .password-container {
            position: relative;
        }

        .password-container input {
            padding-right: 45px;
        }

        .show-password {
            position: absolute;

            right: 12px;
            top: 50%;

            transform: translateY(-50%);

            cursor: pointer;

            font-size: 18px;

            color: #777;

            user-select: none;
        }

        .password-help {
            font-size: 12px;

            color: #888;

            margin-top: 5px;
        }


        /* =========================
           PHONE SECTION
        ========================= */

        .phone-row {
            display: flex;

            gap: 10px;

            margin-bottom: 10px;
        }

        .phone-row input {
            flex: 1;
        }

        .remove-phone {
            width: 45px;

            padding: 0;

            margin: 0;

            background: #e74c3c;

            color: white;

            border: none;

            border-radius: 8px;

            cursor: pointer;

            font-size: 18px;
        }

        .remove-phone:hover {
            background: #c0392b;
        }

        .add-phone {
            width: auto;

            padding: 8px 15px;

            margin-top: 0;

            margin-bottom: 15px;

            background: #5ca9e6;

            color: white;

            border: none;

            border-radius: 8px;

            font-size: 13px;

            cursor: pointer;
        }

        .add-phone:hover {
            background: #438fce;
        }


        /* =========================
           SUBMIT BUTTON
        ========================= */

        .submit-button {
            width: 100%;

            padding: 14px;

            margin-top: 8px;

            border: none;

            border-radius: 10px;

            background:
                linear-gradient(
                    135deg,
                    #3478c9,
                    #54a4df
                );

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.2s;
        }

        .submit-button:hover {
            transform: translateY(-2px);

            box-shadow:
                0 7px 18px
                rgba(52, 120, 201, 0.3);
        }


        /* =========================
           LOGIN
        ========================= */

        .login {
            text-align: center;

            margin-top: 22px;

            color: #777;

            font-size: 14px;
        }

        .login a {
            color: #3478c9;

            text-decoration: none;

            font-weight: bold;
        }

        .login a:hover {
            text-decoration: underline;
        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 750px) {

            .container {
                width: 100%;
            }

            .left {
                display: none;
            }

            .right {
                width: 100%;

                padding: 35px 30px;
            }

            .row {
                flex-direction: column;

                gap: 0;
            }
        }
    </style>
</head>


<body>

<div class="container">


    <!-- =====================================================
         LEFT SIDE
    ====================================================== -->

    <div class="left">

        <div class="paw">
            🐾
        </div>

        <h1>
            PetCare
        </h1>

        <p>
            Everything your pet needs,
            all in one place.
        </p>

        <div class="features">

            <p>
                - Manage your pet profiles
            </p>

            <p>
                - Keep track of grooming
            </p>

            <p>
                - Keep your pet's information safe
            </p>

        </div>

    </div>


    <!-- =====================================================
         RIGHT SIDE
    ====================================================== -->

    <div class="right">

        <h2>
            Create Account
        </h2>

        <p class="subtitle">
            Create your PetCare account to get started.
        </p>


        <!-- =================================================
             SUCCESS MESSAGE
        ================================================== -->

        @if (session('success'))

            <div class="message success">
                {{ session('success') }}
            </div>

        @endif


        <!-- =================================================
             ERROR MESSAGE
        ================================================== -->

        @if (session('error'))

            <div class="message error">
                {{ session('error') }}
            </div>

        @endif


        <!-- =================================================
             VALIDATION ERRORS
        ================================================== -->

        @if ($errors->any())

            <div class="message error">

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <!-- =================================================
             SIGNUP FORM
        ================================================== -->

        <form
            method="POST"
            action="{{ route('signup.store') }}"
        >

            @csrf


            <!-- =================================================
                 ACCOUNT TYPE
            ================================================== -->

            <div class="field">

                <label>
                    Account Type
                </label>

                <select
                    name="Role"
                    id="roleSelect"
                    required
                >

                    <option value="">
                        Select an option
                    </option>

                    <option
                        value="CUSTOMER"
                        {{ old('Role') === 'CUSTOMER' ? 'selected' : '' }}
                    >
                        Customer
                    </option>

                    <option
                        value="GROOMER"
                        {{ old('Role') === 'GROOMER' ? 'selected' : '' }}
                    >
                        Groomer
                    </option>

                </select>

            </div>


            <!-- =================================================
                 CUSTOMER FIELDS
            ================================================== -->

            <div
                id="customerFields"
                style="display: block;"
            >

                <!-- FIRST / LAST NAME -->

                <div class="row">

                    <div class="field">

                        <label>
                            First Name
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            placeholder="First name"
                        >

                    </div>


                    <div class="field">

                        <label>
                            Last Name
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            placeholder="Last name"
                        >

                    </div>

                </div>


                <!-- CUSTOMER PHONE NUMBERS -->

                <div class="field">

                    <label>
                        Phone Number(s)
                    </label>


                    <div id="phoneContainer">

                        @if (old('customer_phone'))

                            @foreach (old('customer_phone') as $phone)

                                <div class="phone-row">

                                    <input
                                        type="tel"
                                        name="customer_phone[]"
                                        value="{{ $phone }}"
                                        placeholder="Enter phone number"
                                    >

                                    @if (!$loop->first)

                                        <button
                                            type="button"
                                            class="remove-phone"
                                            onclick="removePhone(this)"
                                        >
                                            ×
                                        </button>

                                    @endif

                                </div>

                            @endforeach

                        @else

                            <div class="phone-row">

                                <input
                                    type="tel"
                                    name="customer_phone[]"
                                    placeholder="Enter phone number"
                                >

                            </div>

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

            </div>


            <!-- =================================================
                 GROOMER FIELDS
            ================================================== -->

            <div
                id="groomerFields"
                style="display: none;"
            >

                <!-- GROOMER NAME -->

                <div class="field">

                    <label>
                        Name
                    </label>

                    <input
                        type="text"
                        name="groomer_name"
                        value="{{ old('groomer_name') }}"
                        placeholder="Enter your name"
                    >

                </div>


                <!-- GROOMER PHONE -->

                <div class="field">

                    <label>
                        Phone Number
                    </label>

                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="Enter phone number"
                    >

                </div>


                <!-- EXPERIENCE -->

                <div class="field">

                    <label>
                        Experience (Years)
                    </label>

                    <input
                        type="number"
                        name="experience"
                        value="{{ old('experience') }}"
                        placeholder="e.g. 3.5"
                        step="0.1"
                        min="0"
                    >

                </div>


                <!-- SPECIALIZATION -->

                <div class="field">

                    <label>
                        Specialization
                    </label>

                    <input
                        type="text"
                        name="specialization"
                        value="{{ old('specialization') }}"
                        placeholder="e.g. Dog Grooming"
                    >

                </div>

            </div>


            <!-- =================================================
                 EMAIL
            ================================================== -->

            <div class="field">

                <label>
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="example@email.com"
                    required
                >

            </div>


            <!-- =================================================
                 PASSWORD
            ================================================== -->

            <div class="field">

                <label>
                    Password
                </label>

                <div class="password-container">

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter exactly 8 characters"
                        minlength="8"
                        maxlength="8"
                        required
                    >

                    <span
                        class="show-password"
                        onclick="togglePassword()"
                    >
                        👁
                    </span>

                </div>

                <p class="password-help">
                    Password must be exactly 8 characters.
                </p>

            </div>


            <!-- =================================================
                 CUSTOMER ADDRESS
            ================================================== -->

            <div
                class="field"
                id="addressField"
            >

                <label>
                    Address
                </label>

                <input
                    type="text"
                    name="address"
                    value="{{ old('address') }}"
                    placeholder="Enter your address"
                >

            </div>


            <!-- =================================================
                 SUBMIT
            ================================================== -->

            <button
                type="submit"
                class="submit-button"
            >
                Create Account
            </button>

        </form>


        <!-- =================================================
             LOGIN
        ================================================== -->

        <div class="login">

            Already have an account?

            <a href="{{ route('login') }}">
                Login here
            </a>

        </div>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| SHOW / HIDE PASSWORD
|--------------------------------------------------------------------------
*/

function togglePassword()
{
    const password =
        document.getElementById("password");

    const icon =
        document.querySelector(".show-password");


    if (password.type === "password")
    {
        password.type = "text";

        icon.textContent = "👀";
    }
    else
    {
        password.type = "password";

        icon.textContent = "👁";
    }
}


/*
|--------------------------------------------------------------------------
| GET FORM ELEMENTS
|--------------------------------------------------------------------------
*/

const roleSelect =
    document.getElementById("roleSelect");

const customerFields =
    document.getElementById("customerFields");

const groomerFields =
    document.getElementById("groomerFields");

const addressField =
    document.getElementById("addressField");


/*
|--------------------------------------------------------------------------
| CUSTOMER / GROOMER SWITCHING
|--------------------------------------------------------------------------
*/

function updateRoleFields()
{
    if (roleSelect.value === "GROOMER")
    {
        customerFields.style.display =
            "none";

        groomerFields.style.display =
            "block";

        addressField.style.display =
            "none";
    }
    else
    {
        customerFields.style.display =
            "block";

        groomerFields.style.display =
            "none";

        addressField.style.display =
            "block";
    }
}


/*
|--------------------------------------------------------------------------
| ROLE CHANGE
|--------------------------------------------------------------------------
*/

roleSelect.addEventListener(
    "change",
    updateRoleFields
);


/*
|--------------------------------------------------------------------------
| ADD CUSTOMER PHONE NUMBER
|--------------------------------------------------------------------------
*/

function addPhone()
{
    const container =
        document.getElementById(
            "phoneContainer"
        );


    const phoneRow =
        document.createElement("div");


    phoneRow.className =
        "phone-row";


    phoneRow.innerHTML = `
        <input
            type="tel"
            name="customer_phone[]"
            placeholder="Enter phone number"
        >

        <button
            type="button"
            class="remove-phone"
            onclick="removePhone(this)"
        >
            ×
        </button>
    `;


    container.appendChild(phoneRow);
}


/*
|--------------------------------------------------------------------------
| REMOVE CUSTOMER PHONE NUMBER
|--------------------------------------------------------------------------
*/

function removePhone(button)
{
    button.parentElement.remove();
}


/*
|--------------------------------------------------------------------------
| RESTORE ROLE AFTER VALIDATION ERROR
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function()
    {
        updateRoleFields();
    }
);

</script>

</body>
</html>