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
            <h1>Company Documents</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Company Documents</li>
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
                                        <h5 class="card-title"><span id="filter">| Company Documents</span></h5>
                                        @if (session('users.level') == 1)
                                            {{-- ตรวจสอบว่าเป็น Admin --}}
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addDocumentModal">
                                                + Add Document
                                            </button>
                                        @endif
                                    </div>

                                    <table class="table table-borderless table-hover doc_dataTable" id="doc_dataTable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Title</th>
                                                <th scope="col">token</th>
                                                <th scope="col">รายละเอียด</th>
                                                <th scope="col">วันที่</th>
                                                <th scope="col">upload_by</th>
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
            <!-- Add/Edit Document Modal -->
            <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form id="uploadDocumentForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="doc_id" id="doc_id" value="">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addDocumentModalLabel">Upload Document</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="doc_name" class="form-label">Title</label>
                                    <input type="text" class="form-control" name="doc_name" id="doc_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="file_input" class="form-label">File</label>
                                    <input type="file" class="form-control" name="file_input" id="file_input">
                                    <small class="text-muted">Leave blank to keep existing file when editing.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="description"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="modalSubmitButton">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


        </section>

    </main>
    @include('layouts.footer')


    <!-- End #main -->

    {{-- end กระพิบ --}}



    <script>
        $(document).ready(function() {
            var doc_dataTable = $('#doc_dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('company_docs.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'doc_name',
                        name: 'doc_name'
                    },
                    {
                        data: 'token',
                        name: 'token'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'upload_by',
                        name: 'upload_by'
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
            $('#uploadDocumentForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('company_docs.save') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#addDocumentModal').modal('hide');
                        doc_dataTable.ajax.reload();
                        Swal.fire('Success', 'Document saved successfully!', 'success');
                        $('#doc_id').val('');
                        $('#uploadDocumentForm')[0].reset();
                        $('#modalSubmitButton').text('Upload');
                        $('#addDocumentModalLabel').text('Add Document');
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to save document!', 'error');
                    }
                });
            });

            // ---- Edit Button ----
            $(document).on('click', '.edit-document', function() {
                var id = $(this).data('id');

                $.get("{{ route('company_docs.get', '') }}/" + id, function(data) {
                    if (!data || data.success === false) {
                        Swal.fire('Error', data.message || 'Document not found', 'error');
                        return;
                    }

                    // ใส่ข้อมูลเดิมใน modal
                    $('#doc_id').val(data.id);
                    $('#doc_name').val(data.doc_name);
                    $('#description').val(data.description);

                    // ปรับหัว modal และปุ่ม
                    $('#addDocumentModalLabel').text('Edit Document');
                    $('#modalSubmitButton').text('Save Changes');

                    // เปิด modal
                    $('#addDocumentModal').modal('show');
                });
            });

            // ---- Delete Button ----
            $(document).on('click', '.delete-document', function() {
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
                            url: '{{ url('company_docs/delete') }}/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                doc_dataTable.ajax.reload();
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
