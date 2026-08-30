<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PetCare - Manage Groomers</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f4ff, #f7fbff, #dff3f0);
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

        /* Add Groomer */

        .form-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            color: #285b94;
            margin-bottom: 15px;
        }

        .add-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
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

        /* Groomer Table */

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            color: #444;
        }

        tr:hover {
            background: #f7fbff;
        }

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

        /* Back Button - Same as Manage Customers */

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

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        @media (max-width: 700px) {

            .add-form {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <h1>Manage Groomers</h1>

    <p class="subtitle">
        View and manage registered PetCare groomers.
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
         ADD GROOMER
    ========================== --}}

    <div class="form-container">

        <h2>Add New Groomer</h2>

        <form
            method="POST"
            action="{{ route('admin.groomers.store') }}"
            class="add-form"
        >

            @csrf

            <input
                type="text"
                name="name"
                placeholder="Groomer Name"
                value="{{ old('name') }}"
                required
            >

            <input
                type="text"
                name="phone"
                placeholder="Phone"
                value="{{ old('phone') }}"
                required
            >

            <input
                type="number"
                name="experience"
                placeholder="Experience (years)"
                value="{{ old('experience') }}"
                min="0"
                step="0.1"
                required
            >

            <input
                type="text"
                name="specialization"
                placeholder="Specialization"
                value="{{ old('specialization') }}"
                required
            >

            <button
                type="submit"
                class="btn-add"
            >
                Add Groomer
            </button>

        </form>

    </div>


    {{-- =========================
         GROOMER TABLE
    ========================== --}}

    <div class="table-container">

        <h2>Groomer List</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Phone</th>

                    <th>Experience</th>

                    <th>Specialization</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                @forelse($groomers as $groomer)

                    <tr>

                        {{-- Groomer ID --}}

                        <td>
                            {{ $groomer->ID }}
                        </td>


                        {{-- Name --}}

                        <td>
                            {{ $groomer->Name }}
                        </td>


                        {{-- Email --}}

                        <td>
                            {{ $groomer->user->Email ?? 'N/A' }}
                        </td>


                        {{-- Phone --}}

                        <td>
                            {{ $groomer->Phone }}
                        </td>


                        {{-- Experience --}}

                        <td>
                            {{ $groomer->Experience }}
                        </td>


                        {{-- Specialization --}}

                        <td>
                            {{ $groomer->Specialization }}
                        </td>


                        {{-- Delete Action --}}

                        <td>

                            <form
                                method="POST"
                                action="{{ route('admin.groomers.destroy', $groomer->ID) }}"
                                onsubmit="return confirm('Are you sure you want to delete this groomer?');"
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

                        <td colspan="7" class="empty">

                            No groomers found.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =========================
         BACK TO ADMIN DASHBOARD
    ========================== --}}

    <a
        href="{{ route('admin.dashboard') }}"
        class="back"
    >
        ← Back to Dashboard
    </a>

</div>

</body>

</html>