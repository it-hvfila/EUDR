@section('title', 'HVF | Supplier Documents')

@extends('layouts.master')

@section('content')

@include('layouts.header')
@include('layouts.sidebar')

<main id="main" class="main">

    <div class="pagetitle">

        <h1>Supplier Documents</h1>

        <nav>

            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item">
                    <a href="{{ url('/home') }}">
                        Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Supplier Documents
                </li>

            </ol>

        </nav>

    </div>


    <section class="section dashboard">

        <div class="row">

            <div class="col-lg-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">

                        <h5 class="card-title">
                            <span>
                                | Select Supplier
                            </span>
                        </h5>


                        <table
                            class="table table-borderless table-hover"
                            id="supplier_dataTable">

                            <thead>

                                <tr>

                                    <th>#</th>

                                    <th>Supplier Code</th>

                                    <th>Supplier Name</th>

                                    <th>Documents</th>

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

</main>


@include('layouts.footer')


<script>

$(document).ready(function() {

    $('#supplier_dataTable').DataTable({

        processing: true,

        serverSide: true,

        ajax: "{{ route('supplier_docs.data') }}",

        columns: [

            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
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
                data: 'document_count',
                name: 'document_count',
                searchable: false
            },

            {
                data: 'Detail',
                name: 'Detail',
                orderable: false,
                searchable: false
            }

        ]

    });

});

</script>

@endsection