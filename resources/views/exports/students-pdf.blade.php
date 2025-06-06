<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Students PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h2>Student List</h2>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name (TH)</th>
                <th>Surname</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->student_id }}</td>
                    <td>{{ $student->name_th }}</td>
                    <td>{{ $student->surname_th }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->phone }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
