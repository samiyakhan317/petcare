@extends('layouts.app')

@section('title', 'Manage Grooming Services')

@section('content')

<style>

    .services-page {
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

    .services-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .services-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        gap: 20px;
    }

    .services-header h1 {
        color: #285b94;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .services-header p {
        color: #666;
    }

    .add-button {
        display: inline-block;
        padding: 12px 20px;
        background:
            linear-gradient(
                135deg,
                #3478c9,
                #54a4df
            );
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
    }

    .add-button:hover {
        background: #285b94;
    }

    .message {
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .success {
        background: #e8f8ee;
        color: #218838;
        border: 1px solid #b8e6c8;
    }

    .error {
        background: #fdeaea;
        color: #c62828;
        border: 1px solid #f2b8b8;
    }

    .services-table-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.10);
        overflow-x: auto;
    }

    .services-table {
        width: 100%;
        border-collapse: collapse;
    }

    .services-table th {
        background: #f0f6fc;
        color: #285b94;
        padding: 14px;
        text-align: left;
    }

    .services-table td {
        padding: 14px;
        border-bottom: 1px solid #eee;
        color: #555;
    }

    .delete-button {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }

    .delete-button:hover {
        background: #b02a37;
    }

    .empty-message {
        text-align: center;
        padding: 40px 20px;
        color: #777;
    }

    @media (max-width: 700px) {

        .services-page {
            padding: 35px 20px;
        }

        .services-header {
            flex-direction: column;
            align-items: flex-start;
        }

    }

</style>


<div class="services-page">

    <div class="services-container">

        <!-- =========================
             HEADER
        ========================== -->

        <div class="services-header">

            <div>

                <h1>
                    Manage Grooming Services
                </h1>

                <p>
                    Add and manage pet grooming services.
                </p>

            </div>

            <a
                href="{{ route('admin.services.create') }}"
                class="add-button"
            >
                + Add New Service
            </a>

        </div>


        <!-- =========================
             SUCCESS MESSAGE
        ========================== -->

        @if(session('success'))

            <div class="message success">
                {{ session('success') }}
            </div>

        @endif


        <!-- =========================
             ERROR MESSAGE
        ========================== -->

        @if(session('error'))

            <div class="message error">
                {{ session('error') }}
            </div>

        @endif


        <!-- =========================
             SERVICES TABLE
        ========================== -->

        <div class="services-table-container">

            @if($services->count() > 0)

                <table class="services-table">

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Service Name
                            </th>

                            <th>
                                Duration
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($services as $service)

                            <tr>

                                <td>
                                    {{ $service->Service_ID }}
                                </td>

                                <td>
                                    {{ $service->Service_Name }}
                                </td>

                                <td>
                                    {{ $service->Duration }} minutes
                                </td>

                                <td>
                                    ৳{{ number_format($service->Price, 2) }}
                                </td>

                                <td>
                                    {{ $service->Description ?: 'No description' }}
                                </td>

                                <td>

                                    <form
                                        action="{{ route('admin.services.destroy', $service->Service_ID) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this service?');"
                                        style="display: inline;"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="delete-button"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <div class="empty-message">

                    <h2>
                        No Grooming Services
                    </h2>

                    <p>
                        No grooming services have been added yet.
                    </p>

                    <p style="margin-top: 10px;">
                        Click <strong>+ Add New Service</strong> to create your first service.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection