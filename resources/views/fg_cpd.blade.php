@section('title', 'HVF | FG Map CPD')
@extends('layouts.master')

@section('content')
    @include('layouts.header')
    @include('layouts.sidebar')

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>FG Map CPD</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">CPD Lots</a></li>
                    <li class="breadcrumb-item active">FG Map</li>
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
                                    Lot: {{ $lot_number }} |
                                    CPD Lot: {{ $lot_cpd_no }}
                                </h2>

                                <div>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                                        ← Back
                                    </a>
                                    <button class="btn btn-primary btn-sm" id="addFgBtn">
                                        + Add FG Lot
                                    </button>
                                </div>
                            </div>

                            {{-- Table --}}
                            <table class="table table-borderless table-hover" id="fgTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>FG Lot No</th>
                                        <th>Remark</th>
                                        <th>Created At</th>
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

    {{-- Modal --}}
    <div class="modal fade" id="fgModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="fgModalTitle">Add FG Lot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="fg_id">

                    <div class="mb-2">
                        <label>FG Lot No</label>
                        <input type="text" id="fg_lot_no" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Remark</label>
                        <textarea id="remark" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" id="saveFgBtn">Save</button>
                </div>

            </div>
        </div>
    </div>

    @include('layouts.footer')

    <script>
        $(document).ready(function() {

            let cpd_id = "{{ $cpd_id }}";

            let table = $('#fgTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('fg-cpd') }}/" + cpd_id,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'fg_lot_no'
                    },
                    {
                        data: 'remak'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'created_by'
                    },
                    {
                        data: 'Detail',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Add
            $('#addFgBtn').click(function() {
                $('#fg_id').val('');
                $('#fg_lot_no').val('');
                $('#remark').val('');
                $('#fgModalTitle').text('Add FG Lot');
                $('#fgModal').modal('show');
            });

            // Save
            $('#saveFgBtn').click(function() {

                let id = $('#fg_id').val();
                let url = id ?
                    "{{ url('fg-cpd/update') }}/" + id :
                    "{{ url('fg-cpd/store') }}";

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        cpd_id: cpd_id,
                        fg_lot_no: $('#fg_lot_no').val(),
                        remak: $('#remark').val()
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#fgModal').modal('hide');
                            table.ajax.reload();
                            Swal.fire('Success', 'Saved successfully', 'success');
                        } else {
                            Swal.fire('Error', res.message || 'Failed', 'error');
                        }
                    }
                });
            });

            // Edit
            $(document).on('click', '.edit-fg', function() {
                let id = $(this).data('id');

                $.get("{{ url('fg-cpd/show') }}/" + id, function(res) {
                    $('#fg_id').val(res.id);
                    $('#fg_lot_no').val(res.fg_lot_no);
                    $('#remark').val(res.remak);
                    $('#fgModalTitle').text('Edit FG Lot');
                    $('#fgModal').modal('show');
                });
            });

            // Delete
            $(document).on('click', '.delete-fg', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('fg-cpd/delete') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.success) {
                                    table.ajax.reload();
                                    Swal.fire('Deleted', 'FG removed', 'success');
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
