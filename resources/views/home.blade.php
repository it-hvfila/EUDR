@section('title', 'HVF | IT WORK')
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
            <h1>News</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item active">News</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <!-- Right side columns -->
                <div class="col-lg-12">
                    <!-- News & Updates Traffic -->
                    <div class="col-12">
                        <div class="card">

                            <div class="filter" id="add_news">
                                <button type="button" class="btn btn-outline-primary me-2" id="open_modal">+ Add
                                    News</button>
                                {{-- <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>News Updates</h6>
                                    </li>
                                    <li class="dropdown-item " id="open_modal">Add News</li>
                                </ul> --}}
                            </div>

                            <div class="card-body pb-0">
                                <h5 class="card-title">News &amp; Updates <span>| Latest news</span></h5>

                                <div class="news overflow-auto  mb-3" style="max-height: 600px;">
                                    <!-- Latest news will be loaded here via AJAX -->
                                </div><!-- End sidebar recent posts-->

                            </div>
                        </div>
                    </div>
                    <!-- End News & Updates -->
                </div><!-- End Right side columns -->

            </div>
        </section>

    </main><!-- End #main -->
    @include('layouts.footer')
    <!-- Modal -->
    <div class="modal fade add_job" id="editNewsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <form id="Form_News" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        {{-- <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Customer</h1> --}}
                        <h5 class="modal-title d-flex justify-content-between w-100">
                            <span id="modal-title-text"></span>
                            <span id="modal-title-date"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('home.form_home')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="saveButton" class="btn btn-primary saveButton"></button>
                    </div>
                </div>
            </form>
        </div>
    </div>



@endsection
