<!DOCTYPE html>
<html>
<head>
    <title>Customers</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

<h1>Customer List</h1>

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
                <td colspan="6">
                    No customers found.
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

</body>
</html>