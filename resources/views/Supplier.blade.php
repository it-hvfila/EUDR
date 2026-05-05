@section('title', 'HVF | Supplier')
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
            <h1>Supplier</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Supplier</li>
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
                                        <h5 class="card-title"><span id="filter">| Supplier</span></h5>
                                        @if (session('users.level') == 1)
                                            {{-- ตรวจสอบว่าเป็น Admin --}}
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addSupplierModal">
                                                + Add Supplier
                                            </button>
                                        @endif
                                    </div>

                                    <table class="table table-borderless table-hover supplier_dataTable"
                                        id="supplier_dataTable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Supplier_code</th>
                                                <th scope="col">Supplier_name</th>
                                                <th scope="col">address</th>
                                                <th scope="col">contact</th>
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

                <!-- Add Supplier Modal -->
                <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="addSupplierForm">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="supplier_code" class="form-label">Supplier Code</label>
                                        <input type="text" class="form-control" id="supplier_code" name="supplier_code"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="supplier_name" class="form-label">Supplier Name</label>
                                        <input type="text" class="form-control" id="supplier_name" name="supplier_name"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_name" class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" id="contact_name" name="contact_name">
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label">Contact Phone</label>
                                        <input type="text" class="form-control" id="contact_phone" name="contact_phone">
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Supplier</button>
                                </div>
                            </form>
                        </div>
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
            var table = $('#supplier_dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('supplier.dataTable') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'supplier_code',
                        name: 'supplier_code'
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'contact_name',
                        name: 'contact_name'
                    },
                    {
                        data: 'Detail',
                        name: 'Detail',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Add Supplier
            $('#addSupplierForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('suppliers.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            $('#addSupplierModal').modal('hide');
                            $('#addSupplierForm')[0].reset();
                            table.ajax.reload(null, false);
                            Swal.fire('Success', 'Supplier added successfully!', 'success');
                        } else {
                            Swal.fire('Error', res.message || 'Failed to add supplier',
                                'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Server error occurred', 'error');
                    }
                });
            });

            // Delete Supplier
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('suppliers') }}/' + id +
                            '/delete', // <-- ใช้ url() ของ Blade
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                if (res.success) {
                                    $('#supplier_dataTable').DataTable().ajax.reload(
                                        null, false);
                                    Swal.fire('Deleted!', 'Supplier has been deleted.',
                                        'success');
                                } else {
                                    Swal.fire('Error', res.message ||
                                        'Failed to delete supplier', 'error');
                                }
                            },
                            error: function(xhr) {
                                let message = xhr.responseJSON?.message ||
                                    'Server error occurred';
                                Swal.fire('Error', message, 'error');
                            }
                        });
                    }
                });
            });


        });
    </script>
@endsection
