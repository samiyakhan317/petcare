<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            width: 380px;
            background: white;

            padding: 35px;

            border-radius: 10px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.15);
        }

        h1 {
            text-align: center;
            color: #285b94;

            margin-bottom: 25px;
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

        input:focus {
            outline: none;

            border-color: #3478c9;
        }

        button {
            width: 100%;

            padding: 12px;

            border: none;

            border-radius: 5px;

            background: #3478c9;

            color: white;

            font-size: 16px;

            cursor: pointer;
        }

        button:hover {
            background: #285b94;
        }

        .error {
            background: #ffe0e0;

            color: #b00000;

            padding: 10px;

            margin-bottom: 20px;

            border-radius: 5px;

            text-align: center;

            font-size: 14px;
        }

        .success {
            background: #e0f7e9;

            color: #187a3d;

            padding: 10px;

            margin-bottom: 20px;

            border-radius: 5px;

            text-align: center;

            font-size: 14px;
        }

        .signup {
            text-align: center;

            margin-top: 20px;

            color: #777;

            font-size: 14px;
        }

        .signup a {
            color: #3478c9;

            text-decoration: none;

            font-weight: bold;
        }

        .signup a:hover {
            text-decoration: underline;
        }

    </style>

</head>


<body>

<div class="login-box">

    <h1>PetCare Login</h1>


    @if (session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

    @endif


    @if (session('error'))

        <div class="error">
            {{ session('error') }}
        </div>

    @endif


    @if ($errors->any())

        <div class="error">
            {{ $errors->first() }}
        </div>

    @endif


    <form
        method="POST"
        action="{{ route('login') }}"
    >

        @csrf


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


        <button type="submit">
            Login
        </button>

    </form>


    <div class="signup">

        Don't have an account?

        <a href="{{ route('signup') }}">
            Create Account
        </a>

    </div>

</div>

</body>

</html>