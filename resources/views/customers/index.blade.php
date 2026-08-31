@extends('layouts.app')

@section('title', 'Manage Customers')

@section('content')

<style>
    .customers-page {
        min-height: calc(100vh - 70px);
        padding: 40px;
        background: #f4f6f8;
    }

    .customers-container {
        max-width: 1200px;
        margin: auto;
    }

    .customers-container h1 {
        margin-bottom: 25px;
        color: #333;
    }

    .table-box {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border-bottom: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    th {
        background: #333;
        color: white;
    }

    tr:hover {
        background: #f8f8f8;
    }

    .empty-message {
        text-align: center;
        color: #777;
        padding: 20px;
    }
</style>

<div class="customers-page">

```
<div class="customers-container">

    <h1>Customer List</h1>

    <div class="table-box">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Loyalty Points</th>
                </tr>
            </thead>

            <tbody>

                @forelse($customers as $customer)

                    <tr>
                        <td>{{ $customer->ID }}</td>

                        <td>{{ $customer->First_name }}</td>

                        <td>{{ $customer->Last_name }}</td>

                        <td>{{ $customer->Email }}</td>

                        <td>{{ $customer->Address }}</td>

                        <td>{{ $customer->Loyalty_Points }}</td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="empty-message">
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

@endsection
