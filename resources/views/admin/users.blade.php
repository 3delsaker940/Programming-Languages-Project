@extends('admin.layout')

@section('content')

    <div class="flex justify-between items-center mb-6 ">
        <h2 class="text-2xl font-bold">User Management</h2>

        <div class="flex items-center gap-4">
            <div
                class="bg-green-100 text-green-800 px-4 py-2 rounded-full font-semibold shadow flex items-center gap-2 transition transform hover:scale-110">
                <span class="text-lg">🏡</span>
                <span>{{ $apartments->count() }} Properties</span>
            </div>

            <div
                class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full font-semibold shadow flex items-center gap-2 transition transform hover:scale-110">
                <span class="text-lg">👤</span>
                <span>{{ $users->count() }} Users</span>
            </div>
        </div>
    </div>

    <!-- ========================================== -->

    <div class="mb-4 flex justify-center">
        <div class="relative w-full max-w-md">
            <input type="text" id="search" placeholder="Search users..."
                class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 shadow-m focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-colors duration-200">
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-200">
                🔍
            </span>
        </div>
    </div>

    <!-- ========================================= -->

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($users as $user)
                    <tr data-user-id="{{ $user->id }}"
                        class="hover:bg-gray-100 cursor-pointer transition-transform transform hover:-translate-y-1 hover:shadow-lg duration-200">
                        <td class="px-6 py-4">{{ $user->id }}</td>
                        <td class="px-6 py-4">{{ $user->first_name }}</td>
                        <td class="px-6 py-4">{{ $user->last_name }}</td>
                        <td class="px-6 py-4">{{ ucfirst($user->type) }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $user->status == 'active' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $user->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $user->status == 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $user->status == 'frozen' ? 'bg-gray-200 text-gray-800' : '' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <button data-user-id="{{ $user->id }}" data-status="active"
                                class="status-btn bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs active:translate-y-1">Active</button>
                            <button data-user-id="{{ $user->id }}" data-status="pending"
                                class="status-btn bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 text-xs active:translate-y-1">Pending</button>
                            <button data-user-id="{{ $user->id }}" data-status="rejected"
                                class="status-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs active:translate-y-1">Rejected</button>
                            <button data-user-id="{{ $user->id }}" data-status="frozen"
                                class="status-btn bg-gray-400 text-white px-3 py-1 rounded hover:bg-gray-500 text-xs active:translate-y-1">Frozen</button>
                            <button data-user-id="{{ $user->id }}"
                                class="delete-user-btn bg-red-400 text-white font-bold px-3 py-1 ml-8 rounded-full hover:bg-red-800 text-xs transition transform hover:scale-105 shadow-md"
                                title="Delete User">Delete🗑</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ==================================== -->

    <div id="userModal"
        class="fixed inset-0 bg-black/70 backdrop-blur-sm bg-opacity-50 hidden items-center justify-center z-50">
        <div
            class="bg-white rounded-lg w-11/12 md:w-3/4 lg:w-1/2 p-6 relative max-h-[90vh] overflow-y-auto transform transition-transform duration-300 scale-95">
            
            <button id="closeModal"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>

            
            <h2 class="text-2xl font-bold mb-4" id="modalName">User Details</h2>

            
            <div class="flex border-b mb-4">
                <button
                    class="tab-btn px-4 py-2 text-blue-600 font-semibold border-b-2 border-blue-600 focus:outline-none transition-transform transform hover:-translate-y-1 hover:shadow-lg duration-200 cursor-pointer"
                    data-tab="general">General</button>
                <button
                    class="tab-btn px-4 py-2 text-gray-500 font-semibold border-b-2 border-transparent focus:outline-none transition-transform transform hover:-translate-y-1 hover:shadow-lg duration-200 cursor-pointer"
                    data-tab="apartments">Apartments</button>
                <button
                    class="tab-btn px-4 py-2 text-gray-500 font-semibold border-b-2 border-transparent focus:outline-none transition-transform transform hover:-translate-y-1 hover:shadow-lg duration-200 cursor-pointer"
                    data-tab="reservations">Reservations</button>
            </div>

            
            <div id="tabContents">
                <div id="general" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded shadow space-y-2">
                            <p><strong>First Name:</strong> <span id="modalFirstName"></span></p>
                            <p><strong>Last Name:</strong> <span id="modalLastName"></span></p>
                            <p><strong>Birthdate:</strong> <span id="modalBirthdate"></span></p>
                            <p><strong>Number:</strong> <span id="modalNumber"></span></p>
                            <p><strong>Status:</strong> <span id="modalStatus" class="px-2 py-1 rounded text-white"></span>
                            </p>
                            <p><strong>Type:</strong> <span id="modalType"></span></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded shadow space-y-2">
                            <p class="font-semibold">ID Photos:</p>
                            <img id="modalIdFront"
                                class="border rounded cursor-pointer w-full max-w-xs hover:scale-105 transition-transform"
                                src="placeholder.jpg" alt="ID Front">
                            <img id="modalIdBack"
                                class="border rounded cursor-pointer w-full max-w-xs hover:scale-105 transition-transform"
                                src="placeholder.jpg" alt="ID Back">
                        </div>
                    </div>
                </div>

                <div id="apartments" class="tab-content hidden">
                    <div id="modalApartments" class="space-y-4"></div>
                </div>

                <div id="reservations" class="tab-content hidden">
                    <div id="modalReservation" class="list-disc pl-5 space-y-1"></div>
                </div>
            </div>
        </div>
    </div>

    
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
        <div class="relative p-2 max-w-xl max-h-[90vh]">
            <img id="lightboxImage" class="rounded shadow-lg max-h-[90vh] w-auto" src="" alt="User ID">
            <button id="closeImageModal" class="absolute top-2 right-2 text-white text-2xl font-bold">&times;</button>
        </div>
    </div>

    <!-- ========================= -->
    <div id="toast" class="fixed top-23 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg hidden"></div>
    <!-- ======================== -->

@endsection

@section('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            // ========================== search =========================

            const searchInput = document.getElementById('search');
            const tableRows = document.querySelectorAll('tbody tr');

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase();

                tableRows.forEach(row => {
                    const id = row.children[0].textContent.toLowerCase();
                    const firstName = row.children[1].textContent.toLowerCase();
                    const lastName = row.children[2].textContent.toLowerCase();
                    const type = row.children[3].textContent.toLowerCase();
                    const status = row.children[4].textContent.toLowerCase();

                    if (
                        id.includes(query) ||
                        firstName.includes(query) ||
                        lastName.includes(query) ||
                        type.includes(query) ||
                        status.includes(query)
                    ) {
                        row.style.display = ''; 
                    } else {
                        row.style.display = 'none'; 
                    }
                });
            });

            // ======================= table ======================

            const buttons = document.querySelectorAll('.status-btn');
            const toast = document.getElementById('toast');
            const deleteButton = document.querySelectorAll('.delete-user-btn');


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
                                statusCell.className = `px-3 py-1 rounded-full text-xs font-semibold ${status === 'active' ? 'bg-green-100 text-green-700' :
                                    status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                        status === 'rejected' ? 'bg-red-100 text-red-700' :
                                            'bg-gray-200 text-gray-800'
                                    }`;
                            }

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

            // ================== modal ============================

            const modal = document.getElementById('userModal');
            const closeModal = document.getElementById('closeModal');

            const modalName = document.getElementById('modalName');
            const modalFirstName = document.getElementById('modalFirstName');
            const modalLastName = document.getElementById('modalLastName');
            const modalBirthdate = document.getElementById('modalBirthdate');
            const modalNumber = document.getElementById('modalNumber');
            const modalStatus = document.getElementById('modalStatus');
            const modalType = document.getElementById('modalType');
            const modalIdFront = document.getElementById('modalIdFront');
            const modalIdBack = document.getElementById('modalIdBack');
            const modalApartments = document.getElementById('modalApartments');
            const modalReservation = document.getElementById('modalReservation');

            const imageModal = document.getElementById('imageModal');
            const lightboxImage = document.getElementById('lightboxImage');
            const closeImageModal = document.getElementById('closeImageModal');

            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = btn.dataset.tab;
                    tabContents.forEach(tc => tc.classList.add('hidden'));
                    tabContents.forEach(tc => {
                        if (tc.id === target) tc.classList.remove('hidden');
                    });
                    tabButtons.forEach(b => b.classList.remove('text-blue-600', 'border-blue-600'));
                    tabButtons.forEach(b => b.classList.add('text-gray-500', 'border-transparent'));
                    btn.classList.add('text-blue-600', 'border-blue-600');
                    btn.classList.remove('text-gray-500', 'border-transparent');
                });
            });

            document.querySelectorAll('tbody tr').forEach(row => {
                row.addEventListener('click', async (e) => {
                    if (e.target.closest('button')) return;

                    const userId = row.dataset.userId;
                    if (!userId) return;

                    try {
                        const response = await fetch(`/api/users/${userId}`, { headers: { 'Accept': 'application/json' } });
                        if (!response.ok) throw new Error('Failed to load user');
                        const user = await response.json();

                        modalName.textContent = `${user.first_name} ${user.last_name}`;
                        modalFirstName.textContent = user.first_name ?? '-';
                        modalLastName.textContent = user.last_name ?? '-';
                        modalBirthdate.textContent = user.birthdate ?? '-';
                        modalNumber.textContent = user.number ?? '-';
                        modalStatus.textContent = user.status ?? '-';
                        modalType.textContent = user.type ?? '-';

            
                        modalStatus.className = `px-2 py-1 rounded text-white 
                            ${user.status === 'active' ? 'bg-green-500' :
                                user.status === 'pending' ? 'bg-yellow-500' :
                                    user.status === 'rejected' ? 'bg-red-500' :
                                        'bg-gray-500'}`;

                        // ===================== Photo identy ============================

                        modalIdFront.src = user.id_photo_front ?? 'placeholder.jpg';
                        modalIdFront.dataset.src = user.id_photo_front ?? '';
                        modalIdBack.src = user.id_photo_back ?? 'placeholder.jpg';
                        modalIdBack.dataset.src = user.id_photo_back ?? '';

                        // ================= Apartments =================

                        modalApartments.innerHTML = '';

                        if (user.apartments && user.apartments.length > 0) {
                            user.apartments.forEach(apartment => {

                                const card = document.createElement('div');
                                card.className = 'border rounded-lg p-4 bg-gray-50 shadow flex justify-between items-start transition transform hover:scale-105';

                                card.innerHTML = `
                                                        <div class="space-y-1 ">
                                                            <p><strong>Title:</strong> ${apartment.title ?? '-'}</p>
                                                            <p><strong>Price:</strong> ${apartment.price ?? '-'}</p>
                                                            <p><strong>Area:</strong> ${apartment.area ?? '-'} m²</p>
                                                            <p><strong>Rooms:</strong> ${apartment.rooms ?? '-'}</p>
                                                            <p><strong>City:</strong> ${apartment.city ?? '-'}</p>
                                                            <p><strong>Type:</strong> ${apartment.type ?? '-'}</p>
                                                        </div>

                                                        <button
                                                            class="delete-apartment-btn  bg-red-400 text-white font-bold px-3 py-1 ml-8 rounded-full hover:bg-red-800 text-xs transition transform hover:scale-105 shadow-md"
                                                            data-apartment-id="${apartment.id}"
                                                            title="Delete Apartment"
                                                        >
                                                            Delete🗑
                                                        </button>
                                                    `;

                                modalApartments.appendChild(card);
                            });
                        } else {
                            modalApartments.innerHTML = '<p class="text-gray-500">No apartments</p>';
                        }

                        // ====================== Reservations =========================

                        modalReservation.innerHTML = '';

                        if (user.reservations && user.reservations.length > 0) {
                            user.reservations.forEach(reservation => {

                                const card = document.createElement('div');
                                card.className = 'border rounded-lg p-4 bg-gray-50 shadow flex justify-between items-start transition transform hover:scale-105 mr-5';

                                card.innerHTML = `
                                                        <div class="space-y-1 ">
                                                            <p><strong>Apartment ID:</strong> ${reservation.apartment_id ?? '-'}</p>
                                                            <p><strong>Start Date:</strong> ${reservation.start_date ?? '-'}</p>
                                                            <p><strong>End Date:</strong> ${reservation.end_date ?? '-'}</p>
                                                        </div>
                                                    `;

                                modalReservation.appendChild(card);
                            });
                        } else {
                            modalReservation.innerHTML = '<p class="text-gray-500">No Reservations</p>';
                        }

                        modal.classList.remove('hidden');
                        modal.classList.add('flex');

                    } catch (err) {
                        console.error(err);
                        alert('Failed to load user details');
                    }
                });
            });

            // ===================== modal images ==================================
            [modalIdFront, modalIdBack].forEach(imgEl => {
                imgEl.addEventListener('click', () => {
                    const src = imgEl.dataset.src;
                    if (src) {
                        lightboxImage.src = src;
                        imageModal.classList.remove('hidden');
                        imageModal.classList.add('flex');
                    }
                });
            });

            closeImageModal.addEventListener('click', () => {
                imageModal.classList.add('hidden');
                imageModal.classList.remove('flex');
            });

            imageModal.addEventListener('click', e => {
                if (e.target === imageModal) {
                    imageModal.classList.add('hidden');
                    imageModal.classList.remove('flex');
                }
            });

            
            closeModal.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });

            // ================= DELETE USER =================

            deleteButton.forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation(); // 

                    const userId = btn.dataset.userId;
                    if (!userId) return;

                    const confirmed = confirm('Are you sure you want to delete this user?');
                    if (!confirmed) return;

                    try {
                        const response = await fetch(`/api/user/delete/${userId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Delete failed');
                        }

                        btn.closest('tr').remove();

                        if (toast) {
                            toast.textContent = 'User deleted successfully';
                            toast.classList.remove('hidden');
                            setTimeout(() => toast.classList.add('hidden'), 2500);
                        }

                    } catch (err) {
                        console.error(err);
                        alert('Failed to delete user');
                    }
                });
            });

            // ================= DELETE APARTMENT =================

            document.addEventListener('click', async function (e) {

                const btn = e.target.closest('.delete-apartment-btn');
                if (!btn) return;

                e.stopPropagation();

                const apartmentId = btn.dataset.apartmentId;
                if (!apartmentId) return;

                const confirmed = confirm('Are you sure you want to delete this apartment?');
                if (!confirmed) return;

                try {
                    const response = await fetch(`/api/apartments/${apartmentId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json'
                            // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Delete failed');
                    }

                    btn.closest('.border').remove();

                    if (toast) {
                        toast.textContent = 'Apartment deleted successfully';
                        toast.classList.remove('hidden');
                        setTimeout(() => toast.classList.add('hidden'), 2500);
                    }

                } catch (err) {
                    console.error(err);
                    alert('Failed to delete apartment');
                }
            });

        });

    </script>
@endsection