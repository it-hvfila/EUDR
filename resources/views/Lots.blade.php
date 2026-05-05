@section('title', 'HVF | Lots')
@extends('layouts.master')

@section('content')
    @include('layouts.header')
    @include('layouts.sidebar')

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Lots</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ url('/supplier') }}">Supplier</a></li>
                    <li class="breadcrumb-item active">Lots</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h2 class="card-title">
                                    <span id="filter">| Lots — Supplier: {{ $supplier_name }} -
                                        {{ $supplier_code }}</span>
                                </h2>
                                @if (session('users.level') == 1)
                                    <button class="btn btn-primary" id="addLotBtn">+ Add Lot</button>
                                @endif
                            </div>

                            <table class="table table-borderless table-hover lots_dataTable" id="lots_dataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Lots_number</th>
                                        <th>lot_date</th>
                                        <th>description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="lotModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="lotModalTitle"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="lot_id">
                                <div class="mb-2">
                                    <label>Lot Number</label>
                                    <input type="text" id="lot_number" class="form-control">
                                </div>
                                <div class="mb-2">
                                    <label>Lot Date</label>
                                    <input type="date" id="lot_date" class="form-control">
                                </div>
                                <div class="mb-2">
                                    <label>Description</label>
                                    <textarea id="description" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button class="btn btn-primary" id="saveLotBtn">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal: Lot Files -->
                <div class="modal fade" id="edit_Lot_FileModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title d-flex justify-content-between w-100">
                                    <span id="modal-title-text-lot">Lot Files</span>
                                    <span id="modal-title-date-lot">{{ date('d/m/Y') }}</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="lot_id_modal" name="lot_id_modal">
                                <div class="container">
                                    <div class="form-control">
                                        <div class="file-loading">
                                            <input id="file_upload_lot" name="file" type="file" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->
                {{-- <div class="modal fade" id="mapCpdModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Map Latex Lot → CPD
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" id="map_lot_id">

                                <div class="mb-2">
                                    <label>Latex Lot</label>
                                    <input type="text" id="map_lot_number" class="form-control" readonly>
                                </div>

                                <div class="mb-2">
                                    <label>CPD Lot (Epicor)</label>
                                    <input type="text" id="lot_cpd_no" class="form-control"
                                        placeholder="เช่น CPD-2025-001">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button class="btn btn-success" id="saveCpdMapBtn">Save</button>
                            </div>
                        </div>
                    </div>
                </div> --}}


            </div>
        </section>
    </main>
    @include('layouts.footer')

    <script>
        $(document).ready(function() {
            var supplier_id = "{{ $supplier_id }}";

            // DataTable
            $('#lots_dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('supplier') }}/" + supplier_id + "/lots",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'lot_number',
                        name: 'lot_number'
                    },
                    {
                        data: 'lot_date',
                        name: 'lot_date'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'Detail',
                        name: 'Detail',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });

        // Add Lot
        $('#addLotBtn').click(function() {
            $('#lotModalTitle').text('Add Lot');
            $('#lot_id').val('');
            $('#lot_number').val('');
            $('#lot_date').val('');
            $('#description').val('');
            $('#lotModal').modal('show');
        });

        // Save (Add / Update)
        $('#saveLotBtn').click(function() {
            let id = $('#lot_id').val();
            let url = id ? `{{ url('lots/update') }}/${id}` : `{{ url('lots/store') }}`;

            $.ajax({
                url: url,
                method: 'POST', // ใช้ POST ทั้งคู่
                data: {
                    _token: '{{ csrf_token() }}',
                    lot_number: $('#lot_number').val(),
                    lot_date: $('#lot_date').val(),
                    description: $('#description').val(),
                    supplier_id: '{{ $supplier_id }}'
                },
                success: function(res) {
                    if (res.success) {
                        $('#lotModal').modal('hide');
                        $('#lots_dataTable').DataTable().ajax.reload();
                        Swal.fire('Success', 'Lot saved successfully!', 'success');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to save lot.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseText || 'Something went wrong.', 'error');
                }
            });
        });

        // Edit Lot
        $(document).on('click', '.edit-lot', function() {
            let lot_id = $(this).data('id');
            $('#lot_id').val(lot_id);

            $.get("{{ url('lots') }}/" + lot_id, function(res) {
                if (res.success === false) {
                    alert(res.message);
                    return;
                }
                $('#lot_number').val(res.lot_number);
                $('#lot_date').val(res.lot_date);
                $('#description').val(res.description);
            });

            $('#lotModal').modal('show');
        });

        // Delete Lot with SweetAlert2
        $(document).on('click', '.delete-lot', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url('lots/delete') }}/' +
                            id, // ใช้ url() ให้ Laravel สร้าง path ถูกต้อง
                        type: 'DELETE', // ต้องตรงกับ route Laravel
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.success) {
                                $('#lots_dataTable').DataTable().ajax.reload();
                                Swal.fire(
                                    'Deleted!',
                                    'The lot has been deleted.',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error!',
                                    res.message || 'There was a problem deleting this lot.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                xhr.responseJSON?.message ||
                                'There was a problem deleting this lot.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Open Modal
        $(document).on('click', '#file_input_modal', function() {
            let lot_id = $(this).data('id');
            $('#lot_id_modal').val(lot_id);

            $('#modal-title-text-lot').text('Lot Files');
            $('#modal-title-date-lot').text(new Date().toLocaleDateString());

            loadLotFiles(lot_id);
            $('#edit_Lot_FileModal').modal('show');
        });

        // $(document).on('click', '#map-cpd', function() {
        //     console.log('map-cpd clicked');
        //     $('#map_lot_id').val($(this).data('id'));
        //     $('#map_lot_number').val($(this).data('lot'));
        //     $('#lot_cpd_no').val('');
        //     $('#used_weight').val('');

        //     $('#mapCpdModal').modal('show');
        // });

        // $('#saveCpdMapBtn').click(function() {
        //     $.ajax({
        //         url: "{{ url('/lots/map-cpd') }}",
        //         method: "POST",
        //         data: {
        //             _token: "{{ csrf_token() }}",
        //             lot_id: $('#map_lot_id').val(),
        //             lot_cpd_no: $('#lot_cpd_no').val(),
        //             used_weight: $('#used_weight').val()
        //         },
        //         success: function(res) {
        //             if (res.success) {
        //                 Swal.fire('Success', 'Mapped successfully', 'success');
        //                 $('#mapCpdModal').modal('hide');
        //             } else {
        //                 Swal.fire('Error', res.message, 'error');
        //             }
        //         }
        //     });
        // });

        // Load files
        function loadLotFiles(lot_id) {
            $.get('{{ url('/lots') }}/' + lot_id + '/files', function(files) {
                var previewUrls = [];
                var previewConfigs = [];

                files.forEach(file => {
                    var fileUrl = "{{ url('uploads/lots') }}/" + file.file_name;
                    previewUrls.push(fileUrl);

                    previewConfigs.push({
                        caption: file.file_name,
                        size: file.file_size * 1024 * 1024, // MB -> bytes
                        url: "{{ url('/lots/file') }}/" + file.id, // DELETE URL
                        key: file.id,
                        downloadUrl: fileUrl,
                        type: file.file_name.split('.').pop().toLowerCase()
                    });
                });

                initializeLotFileInput('#file_upload_lot', previewUrls, previewConfigs);
            });
        }

        initializeLotFileInput('#file_upload_lot', previewUrls, previewConfigs);

        function initializeLotFileInput(selector, previewUrls, previewConfigs) {
            $(selector).fileinput('destroy');
            $(selector).fileinput({
                theme: 'fa5',
                uploadUrl: "{{ url('/lots') }}/" + $('#lot_id_modal').val() + "/upload",
                uploadExtraData: function() {
                    return {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };
                },
                overwriteInitial: false,
                initialPreviewAsData: true,
                allowedFileExtensions: ['pdf', 'geojson', 'json'],
                maxFileSize: 10240,
                initialPreview: previewUrls,
                initialPreviewConfig: previewConfigs,
                showUpload: true,
                showRemove: true,
                fileActionSettings: {
                    showUpload: false,
                    showRemove: true
                },

                // แก้ตรงนี้
                deleteUrl: function(config) {
                    return "{{ url('/lots/file') }}/" + config.key;
                },
                deleteExtraData: function(config) {
                    return {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE' // สำคัญ ให้ Laravel รับเป็น DELETE
                    };
                }
            }).on('filedeleted', function() {
                loadLotFiles($('#lot_id_modal').val());
            }).on('fileuploaded', function() {
                loadLotFiles($('#lot_id_modal').val());
            });
        }
    </script>
@endsection
