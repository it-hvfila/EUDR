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
            <h1>Documents</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Documents</li>
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
                                        <h5 class="card-title"><span id="filter">| Documents</span></h5>
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
                                                <th scope="col">รายละเอียด</th>
                                                {{-- <th scope="col">File</th> --}}
                                                <th scope="col">ประเภทไฟล์</th>
                                                <th scope="col">ขนาดไฟล์</th>

                                                <th scope="col">วันที่</th>
                                                <th scope="col">upload_by</th>
                                                {{-- <th scope="col">ผู้รับผิดชอบ</th> --}}
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
            <!-- Add Document Modal -->
            <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form id="uploadDocumentForm" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addDocumentModalLabel">Upload Document</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name_file" class="form-label">Title</label>
                                    <input type="text" class="form-control" name="name_file" required>
                                </div>
                                <div class="mb-3">
                                    <label for="file_input" class="form-label">File</label>
                                    <input type="file" class="form-control" name="file_input" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Upload</button>
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
                ajax: {
                    url: "{{ url('doc_dataTable') }}",
                    data: function(d) {

                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name_file',
                        name: 'name_file'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    // {
                    //     data: 'path_file',
                    //     name: 'path_file'
                    // },
                    {
                        data: 'type_file',
                        name: 'type_file'
                    },
                    {
                        data: 'size_mb',
                        name: 'size_mb',
                        render: function(data, type, row) {
                            return data + ' MB';
                        }
                    },

                    {
                        data: 'date_file',
                        name: 'date_file'
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
                    },
                ],
            });

            $('#uploadDocumentForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ url('/documents/upload') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#addDocumentModal').modal('hide');
                        $('#doc_dataTable').DataTable().ajax.reload();
                        alert('Upload successful!');
                    },
                    error: function(xhr) {
                        alert('Upload failed!');
                    }
                });
            });




        });
    </script>
@endsection
