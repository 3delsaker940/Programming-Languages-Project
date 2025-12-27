@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">User Management</h2>
</div>

<div class="overflow-x-auto bg-white shadow rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($users as $user)
            <tr>
                <td class="px-6 py-4">{{ $user->id }}</td>
                <td class="px-6 py-4">{{ $user->first_name }}</td>
                <td class="px-6 py-4">{{ $user->last_name }}</td>
                <td class="px-6 py-4">{{ ucfirst($user->type) }}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $user->status == 'active' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $user->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $user->status == 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $user->status == 'frozen' ? 'bg-gray-200 text-gray-800' : '' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 space-x-2">
                    <button data-user-id="{{ $user->id }}" data-status="active" class="status-btn bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">Active</button>
                    <button data-user-id="{{ $user->id }}" data-status="pending" class="status-btn bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 text-xs">Pending</button>
                    <button data-user-id="{{ $user->id }}" data-status="rejected" class="status-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Rejected</button>
                    <button data-user-id="{{ $user->id }}" data-status="frozen" class="status-btn bg-gray-400 text-white px-3 py-1 rounded hover:bg-gray-500 text-xs">Frozen</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg hidden"></div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.status-btn');
    const toast = document.getElementById('toast');

    buttons.forEach(button => {
        button.addEventListener('click', async () => {
            const userId = button.dataset.userId;
            const status = button.dataset.status;

            try {
                const response = await fetch(`/api/users/${userId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });

                const data = await response.json();

                if (response.ok) {
                    const statusCell = button.closest('tr').querySelector('td:nth-child(5) span');
                    if (statusCell) {
                        statusCell.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        statusCell.className = `px-3 py-1 rounded-full text-xs font-semibold ${
                            status === 'active' ? 'bg-green-100 text-green-700' :
                            status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                            status === 'rejected' ? 'bg-red-100 text-red-700' :
                            'bg-gray-200 text-gray-800'
                        }`;
                    }

                    // إظهار Toast
                    toast.textContent = `Status updated to ${data.status}`;
                    toast.classList.remove('hidden');
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 2500);
                } else {
                    console.error('Error:', data.message);
                }
            } catch (err) {
                console.error('Something went wrong!', err);
            }
        });
    });
});
</script>
@endsection