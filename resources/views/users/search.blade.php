<!DOCTYPE html>
<html>
<head>
    <title>User Search - VULNERABLE</title>
</head>
<body>
    <h1>Search Users (VULNERABLE VERSION)</h1>

    <form method="GET" action="/vulnerable/search">
        <input type="text" name="search" placeholder="Search name...">
        <button type="submit">Search</button>
    </form>

    @if(isset($users))
        <h2>Results:</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
            </tr>
            @endforeach
        </table>
    @endif
</body>
</html>