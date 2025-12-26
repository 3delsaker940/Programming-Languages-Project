@extends('admin.layout')

@section('content')
<h2 class="text-2xl font-bold mb-4">Test Users Table</h2>

<table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
    <thead class="bg-gray-800 text-white">
        <tr>
            <th class="py-2 px-4">ID</th>
            <th class="py-2 px-4">First Name</th>
            <th class="py-2 px-4">Last Name</th>
            <th class="py-2 px-4">Number</th>
            <th class="py-2 px-4">Type</th>
            <th class="py-2 px-4">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr class="border-b">
            <td class="py-2 px-4">{{ $user->id }}</td>
            <td class="py-2 px-4">{{ $user->first_name }}</td>
            <td class="py-2 px-4">{{ $user->last_name }}</td>
            <td class="py-2 px-4">{{ $user->number }}</td>
            <td class="py-2 px-4">{{ $user->type }}</td>
            <td class="py-2 px-4">{{ $user->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection