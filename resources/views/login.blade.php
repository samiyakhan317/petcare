<!DOCTYPE html>
<html>
<head>

    <title>PetCare Login</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;

            display: flex;
            justify-content: center;
            align-items: center;

            min-height: 100vh;
        }

        .login-box {
            width: 400px;
            background: white;

            padding: 35px;

            border-radius: 10px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.15);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;

            margin-bottom: 18px;

            border: 1px solid #ccc;
            border-radius: 5px;

            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;

            border: none;
            border-radius: 5px;

            background: #333;
            color: white;

            font-size: 16px;

            cursor: pointer;
        }

        button:hover {
            opacity: 0.85;
        }

        /* ERROR MESSAGE */

        .error {
            background: #ffe0e0;
            color: #b00000;

            padding: 10px;
            margin-bottom: 20px;

            border-radius: 5px;

            text-align: center;
            font-size: 14px;
        }

        /* SUCCESS MESSAGE */

        .success {
            background: #e0f7e9;
            color: #187a3d;

            padding: 10px;
            margin-bottom: 20px;

            border-radius: 5px;

            text-align: center;
            font-size: 14px;
        }

        /* SIGNUP LINK */

        .signup-link {
            text-align: center;

            margin-top: 20px;

            color: #777;

            font-size: 14px;
        }

        .signup-link a {
            color: #3478c9;

            text-decoration: none;

            font-weight: bold;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

<div class="login-box">

    <h1>PetCare Login</h1>


    <!-- =========================================
         SUCCESS MESSAGE
    ========================================== -->

    @if(session('success'))

        <div class="success">

            {{ session('success') }}

        </div>

    @endif


    <!-- =========================================
         ERROR MESSAGE
    ========================================== -->

    @if(session('error'))

        <div class="error">

            {{ session('error') }}

        </div>

    @endif


    <!-- =========================================
         VALIDATION ERRORS
    ========================================== -->

    @if($errors->any())

        <div class="error">

            {{ $errors->first() }}

        </div>

    @endif


    <!-- =========================================
         LOGIN FORM
    ========================================== -->

    <form
        method="POST"
        action="{{ route('login') }}"
    >

        @csrf


        <!-- EMAIL -->

        <label for="email">
            Email
        </label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            value="{{ old('email') }}"
            required
        >


        <!-- PASSWORD -->

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >


        <!-- LOGIN BUTTON -->

        <button type="submit">
            Login
        </button>

    </form>


    <!-- =========================================
         CREATE ACCOUNT
    ========================================== -->

    <div class="signup-link">

        Don't have an account?

        <a href="{{ route('signup') }}">
            Create a new account
        </a>

    </div>

</div>

</body>
</html>