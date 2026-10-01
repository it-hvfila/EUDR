@section('title', 'HVF | Supplier Documents')

@extends('layouts.master')

@section('content')

@include('layouts.header')
@include('layouts.sidebar')

<main id="main" class="main">


    {{-- Page Title --}}
    <div class="pagetitle">

        <h1>Supplier Documents</h1>

        <nav>

            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item">
                    <a href="{{ url('/home') }}">
                        Home
                    </a>
                </li>

                <li class="breadcrumb-item">

                    <a href="{{ route('supplier_docs') }}">
                        Suppliers
                    </a>

                </li>

                <li class="breadcrumb-item active">

                    {{ $supplier->supplier_name }}

                </li>

            </ol>

        </nav>

    </div>


    <section class="section dashboard">

        <div class="row">

            <div class="col-lg-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">


                        {{-- Supplier Header --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <div>

                                <h5 class="card-title mb-1">

                                    {{ $supplier->supplier_name }}

                                </h5>

                                <div class="text-muted">

                                    Supplier Code:
                                    <strong>
                                        {{ $supplier->supplier_code }}
                                    </strong>

                                </div>

                            </div>


                            @if (session('users.level') == 1)

                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addDocumentModal">

                                    <i class="fa fa-plus"></i>

                                    Add Document

                                </button>

                            @endif

                        </div>


                        {{-- Documents Table --}}
                        <table
                            class="table table-borderless table-hover"
                            id="doc_dataTable">

                            <thead>

                                <tr>

                                    <th>#</th>

                                    <th>Title</th>

                                    <th>Category</th>

                                    <th>รายละเอียด</th>

                                    <th>วันที่</th>

                                    <th>Upload By</th>

                                    <th>Action</th>

                                </tr>

                            </thead>

                            <tbody>
                            </tbody>

                        </table>


                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
         Add / Edit Modal
    ====================================================== --}}

    <div
        class="modal fade"
        id="addDocumentModal"
        tabindex="-1"
        aria-labelledby="addDocumentModalLabel"
        aria-hidden="true">


        <div class="modal-dialog">

            <form
                id="uploadDocumentForm"
                enctype="multipart/form-data">

                @csrf


                {{-- Supplier ID --}}
                <input
                    type="hidden"
                    name="supplier_id"
                    value="{{ $supplier_id }}">


                {{-- Document ID --}}
                <input
                    type="hidden"
                    name="doc_id"
                    id="doc_id"
                    value="">


                <div class="modal-content">


                    <div class="modal-header">

                        <h5
                            class="modal-title"
                            id="addDocumentModalLabel">

                            Add Document

                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">

                        </button>

                    </div>


                    <div class="modal-body">


                        {{-- Title --}}
                        <div class="mb-3">

                            <label
                                for="doc_name"
                                class="form-label">

                                Title

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="doc_name"
                                id="doc_name"
                                required>

                        </div>


                        {{-- Category --}}
                        <div class="mb-3">

                            <label
                                for="category_id"
                                class="form-label">

                                Report Category

                            </label>

                            <select
                                class="form-select"
                                name="category_id"
                                id="category_id"
                                required>

                                <option value="">
                                    -- Select Category --
                                </option>


                                @foreach ($categories as $cat)

                                    <option value="{{ $cat->id }}">

                                        {{ $cat->report_section }}
                                        -
                                        {{ $cat->category_name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- File --}}
                        <div class="mb-3">

                            <label
                                for="file_input"
                                class="form-label">

                                File

                            </label>

                            <input
                                type="file"
                                class="form-control"
                                name="file_input"
                                id="file_input">


                            <small class="text-muted">

                                Leave blank to keep existing file when editing.

                            </small>

                        </div>


                        {{-- Description --}}
                        <div class="mb-3">

                            <label
                                for="description"
                                class="form-label">

                                Description

                            </label>

                            <textarea
                                class="form-control"
                                name="description"
                                id="description"
                                rows="3"></textarea>

                        </div>


                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Close

                        </button>


                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="modalSubmitButton">

                            Upload

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</main>


@include('layouts.footer')


<script>

$(document).ready(function() {


    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    var doc_dataTable = $('#doc_dataTable').DataTable({

        processing: true,

        serverSide: true,

        ajax: "{{ route('supplier_docs.list', $supplier_id) }}",

        columns: [

            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },

            {
                data: 'doc_name',
                name: 'sd.doc_name'
            },

            {
                data: 'category_name',
                name: 'dc.category_name'
            },

            {
                data: 'description',
                name: 'sd.description'
            },

            {
                data: 'created_at',
                name: 'sd.created_at'
            },

            {
                data: 'upload_by',
                name: 'sd.upload_by'
            },

            {
                data: 'Detail',
                name: 'Detail',
                orderable: false,
                searchable: false
            }

        ]

    });



    /*
    |--------------------------------------------------------------------------
    | Add / Edit Document
    |--------------------------------------------------------------------------
    */

    $('#uploadDocumentForm').submit(function(e) {

        e.preventDefault();


        var formData = new FormData(this);


        $.ajax({

            url: "{{ route('supplier_docs.save') }}",

            type: 'POST',

            data: formData,

            processData: false,

            contentType: false,


            success: function(response) {

                if (!response.success) {

                    Swal.fire(
                        'Error',
                        'Failed to save document!',
                        'error'
                    );

                    return;

                }


                $('#addDocumentModal').modal('hide');


                doc_dataTable.ajax.reload(null, false);


                Swal.fire(
                    'Success',
                    'Document saved successfully!',
                    'success'
                );


                resetDocumentForm();

            },


            error: function(xhr) {

                let message =
                    'Failed to save document!';


                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {

                    message =
                        xhr.responseJSON.message;

                }


                Swal.fire(
                    'Error',
                    message,
                    'error'
                );

            }

        });

    });



    /*
    |--------------------------------------------------------------------------
    | Edit Button
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.edit-document',
        function() {


            var id =
                $(this).data('id');


            $.get(

                "{{ route('supplier_docs.get', '') }}/" + id,

                function(data) {


                    if (
                        !data ||
                        data.success === false
                    ) {

                        Swal.fire(
                            'Error',
                            data.message ||
                            'Document not found',
                            'error'
                        );

                        return;

                    }


                    $('#doc_id')
                        .val(data.id);


                    $('#doc_name')
                        .val(data.doc_name);


                    $('#category_id')
                        .val(data.category_id);


                    $('#description')
                        .val(data.description);


                    $('#addDocumentModalLabel')
                        .text('Edit Document');


                    $('#modalSubmitButton')
                        .text('Save Changes');


                    $('#addDocumentModal')
                        .modal('show');

                }

            );

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Delete Button
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.delete-document',
        function() {


            var id =
                $(this).data('id');


            Swal.fire({

                title: 'Confirm Delete',

                text:
                    'Are you sure you want to delete this document?',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Delete',

                cancelButtonText: 'Cancel'

            }).then((result) => {


                if (result.isConfirmed) {


                    $.ajax({

                        url:
                            '{{ url('supplier_docs/delete') }}/'
                            + id,

                        type: 'DELETE',

                        data: {

                            _token:
                                '{{ csrf_token() }}'

                        },


                        success: function() {


                            doc_dataTable.ajax.reload(
                                null,
                                false
                            );


                            Swal.fire(
                                'Deleted!',
                                'Document has been deleted.',
                                'success'
                            );

                        },


                        error: function() {

                            Swal.fire(
                                'Error',
                                'Failed to delete document.',
                                'error'
                            );

                        }

                    });

                }

            });

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Reset Form
    |--------------------------------------------------------------------------
    */

    function resetDocumentForm()
    {

        $('#doc_id').val('');

        $('#uploadDocumentForm')[0].reset();

        $('#modalSubmitButton')
            .text('Upload');

        $('#addDocumentModalLabel')
            .text('Add Document');

    }



    /*
    |--------------------------------------------------------------------------
    | Modal Close
    |--------------------------------------------------------------------------
    */

    $('#addDocumentModal').on(
        'hidden.bs.modal',
        function() {

            resetDocumentForm();

        }
    );


});

</script>

@endsection