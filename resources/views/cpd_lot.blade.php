@section('title', 'HVF | CPD Lots')
@extends('layouts.master')

@section('content')
    @include('layouts.header')
    @include('layouts.sidebar')

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>CPD Lots</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/supplier') }}">Supplier</a></li>
                    <li class="breadcrumb-item active">CPD Map Lots</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="row">
                <div class="col-12">
                    <div class="card overflow-auto">
                        <div class="card-body">

                            {{-- Header --}}
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h2 class="card-title mb-0">
                                    Supplier: {{ $supplier_name }} |
                                    Lot: {{ $lot_number }}
                                </h2>

                                <div>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                                        ← Back
                                    </a>
                                    <button class="btn btn-primary btn-sm" id="addCpdBtn">
                                        + Add CPD Map Lot
                                    </button>
                                </div>
                            </div>

                            {{-- Table --}}
                            <table class="table table-borderless table-hover" id="cpdLotTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>CPD Lot No</th>
                                        <th>Date</th>
                                        <th>Remark</th>
                                        <th>Created By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Modal: Add / Edit CPD --}}
    <div class="modal fade" id="cpdModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="cpdModalTitle">Add CPD Map Lot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="cpd_id">

                    <div class="mb-2">
                        <label>CPD Lot No</label>
                        <input type="text" id="lot_cpd_no" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Remark</label>
                        <textarea id="remark" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" id="saveCpdBtn">Save</button>
                </div>

            </div>
        </div>
    </div>

    @include('layouts.footer')

    {{-- Scripts --}}
    <script>
        $(document).ready(function() {

            let lot_id = "{{ $lot_id }}";

            // DataTable
            let table = $('#cpdLotTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('cpd-lots') }}/" + lot_id,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'lot_cpd_no',
                        name: 'lot_cpd_no'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'remak',
                        name: 'remak'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'Detail',
                        name: 'Detail',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Add
            $('#addCpdBtn').click(function() {
                $('#cpdModalTitle').text('Add CPD Map Lot');
                $('#cpd_id').val('');
                $('#lot_cpd_no').val('');
                $('#cpd_date').val('');
                $('#remark').val('');
                $('#cpdModal').modal('show');
            });

            // Save
            $('#saveCpdBtn').click(function() {

                let id = $('#cpd_id').val();
                let url = id ?
                    "{{ url('cpd-lots/update') }}/" + id :
                    "{{ url('cpd-lots/store') }}";

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        lot_id: lot_id,
                        lot_cpd_no: $('#lot_cpd_no').val(),
                        date: $('#cpd_date').val(),
                        remak: $('#remark').val()
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#cpdModal').modal('hide');
                            table.ajax.reload();
                            Swal.fire('Success', 'Saved successfully', 'success');
                        } else {
                            Swal.fire('Error', res.message || 'Failed', 'error');
                        }
                    }
                });
            });

            // Edit
            $(document).on('click', '.edit-cpd-lot', function() {
                let id = $(this).data('id');

                $.get("{{ url('cpd-lots/show') }}/" + id, function(res) {
                    $('#cpd_id').val(res.id);
                    $('#lot_cpd_no').val(res.lot_cpd_no);
                    $('#cpd_date').val(res.date);
                    $('#remark').val(res.remak);
                    $('#cpdModalTitle').text('Edit CPD');
                    $('#cpdModal').modal('show');
                });
            });

            // Delete
            $(document).on('click', '.delete-cpd-lot', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('cpd-lots/delete') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.success) {
                                    table.ajax.reload();
                                    Swal.fire('Deleted', 'CPD removed', 'success');
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
