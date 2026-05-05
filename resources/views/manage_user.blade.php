@section('title', 'HVF | Documents')
@extends('layouts.master')

@section('content')
    @include('layouts.header')
    @include('layouts.sidebar')

    <main id="main" class="main">
        <!-- Lightbox Container -->
        <div id="lightbox" class="lightbox" style="display: none;">
            <span id="closeLightbox" class="close">&times;</span>
            <img id="lightboxImage" class="lightbox-image" src="" alt="Expanded Image">
        </div>
        <div class="pagetitle">
            <h1>User</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item active">User </li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">

                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">


                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="card-title"><span id="filter">| Manage Users</span></h5>
                                        @if (session('users.level') == 1)
                                            {{-- ตรวจสอบว่าเป็น Admin --}}
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addUserModal">
                                                + Add User
                                            </button>
                                        @endif
                                    </div>

                                    <table class="table table-borderless table-hover user_dataTable" id="user_dataTable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">username</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">description</th>
                                                <th scope="col">level</th>
                                                <th scope="col">lastLogin</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        </div><!-- End Recent Sales -->

                    </div>
                </div><!-- End Left side columns -->



            </div>
            <!-- Add/Edit User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form id="save_user" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="user_id">

                            <div class="modal-header">
                                <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-control"
                                        placeholder="Enter username" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Enter password">
                                    <small class="text-muted">* เวลามีการแก้ไข user ถ้าไม่ใส่ password
                                        ระบบจะไม่เปลี่ยนรหัสผ่านเดิม</small>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Enter full name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control" placeholder="Enter description"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="level" class="form-label">Level</label>
                                    <select name="level" id="level" class="form-select" required>
                                        <option value="">-- Select Level --</option>
                                        <option value="1">Admin</option>
                                        <option value="2">Staff</option>
                                        <option value="3">Viewer</option>
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" id="modalSubmitButton" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>




        </section>

    </main>
    @include('layouts.footer')


    <!-- End #main -->

    {{-- end กระพิบ --}}



    <script>
        $(document).ready(function() {
            var user_dataTable = $('#user_dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('manage_user.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'level',
                        name: 'level'
                    },
                    {
                        data: 'last_login',
                        name: 'last_login'
                    },

                    {
                        data: 'Detail',
                        name: 'Detail',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // ---- Add / Edit ----
            $('#save_user').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('manage_user.store') }}", // ✅ ใช้ route users.store ตามที่กำหนดใน web.php
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#addUserModal').modal('hide'); // ✅ ปิด modal ผู้ใช้
                        user_dataTable.ajax.reload(); // ✅ โหลด datatable ใหม่
                        Swal.fire('Success', 'User saved successfully!', 'success');

                        // ✅ เคลียร์ฟอร์ม
                        $('#user_id').val('');
                        $('#save_user')[0].reset();
                        $('#modalSubmitButton').text('Save');
                        $('#addUserModalLabel').text('Add User');
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to save user!', 'error');
                    }
                });
            });

            // ---- Edit Button ----
            $(document).on('click', '.edit-user', function() {
                var id = $(this).data('id');

                $.get("{{ url('manage_user/get') }}/" + id, function(data) {
                    if (!data || data.success === false) {
                        Swal.fire('Error', data.message || 'User not found', 'error');
                        return;
                    }

                    $('#user_id').val(data.id);
                    $('#username').val(data.username);
                    $('#name').val(data.name);
                    $('#description').val(data.description);
                    $('#level').val(data.level);

                    $('#addUserModalLabel').text('Edit User');
                    $('#modalSubmitButton').text('Save Changes');
                    $('#addUserModal').modal('show');
                });

            });

            // ---- Delete Button ----
            $(document).on('click', '.delete-user', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Confirm Delete',
                    text: 'Are you sure you want to delete this document?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('manage_user/delete') }}/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                user_dataTable.ajax.reload();
                                Swal.fire('Deleted!', 'Document has been deleted.',
                                    'success');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
