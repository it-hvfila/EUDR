@section('title', 'HVF | Documents EURD')
@extends('layouts.master')

@section('content')
    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="" class="logo d-flex align-items-center w-auto">
                                    <img src="assets/img/logo.png" alt="">
                                    <span class="d-none d-lg-block">Documents EURD</span>
                                </a>
                            </div>
                            <!-- End Logo -->

                            <div class="card mb-3">

                                <div class="card-body">

                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                                        <p class="text-center small">Enter your username & password to login</p>
                                    </div>

                                    <form class="row g-3 needs-validation" novalidate id="login_form">
                                        @csrf
                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Username</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                <input type="text" name="username" class="form-control" id="yourUsername"
                                                    required>
                                                <div class="invalid-feedback">Please enter your username.</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control" id="passwordlogin"
                                                required>
                                            <div type="button" id="togglePassword3" onclick="togglePasswordLogin()">
                                                <i id="passwordIconLogin" class="fa fa-eye"></i>
                                            </div>
                                            <div class="invalid-feedback">Please enter your password!</div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember"
                                                    value="true" id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Login</button>
                                        </div>
                                    </form>

                                </div>
                            </div>

                            <div class="credits">
                                Designed by <a href="">HVF</a>
                            </div>

                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main>
    <!-- End #main -->
    <!-- Modal -->
    <div class="modal fade register" id="RegisterModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <!-- Registration form goes here -->
            <form id="registerForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('login.form_register')
                    </div>
                    <div class="modal-footer mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Open the modal for both insert and update
        $(document).on('click', '#open_modal', function() {
            setModalTitleAndDate(false); // Call the function for insert
        });
        //Modal show insert edit
        function setModalTitleAndDate(isEdit, response = null) {
            var now = new Date();
            var formattedNow = now.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: '2-digit'
            });
            $('#modal-title-text').text('Add Job');
            $('#modal-title-date').text(formattedNow);
            $('#customer-code-group').hide();
            $('#saveButton').text('Save');
            $('#saveButton').removeClass('btn-warning').addClass('btn-primary');
            $('#RegisterModal').modal('show');
            $('#status_staff_it').hide();

        }

        //login
        $('#login_form').on('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: '{{ url('/login') }}', // URL ที่จะทำการ POST
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.status == 'success') {
                        Swal.fire({
                            title: 'Success',
                            html: data.message.replace(/\n/g, '<br>'),
                            icon: 'success',
                            backdrop: `
                        rgba(0,123,39,0.4)
                        url("https://media.tenor.com/ek214PnxJhEAAAAi/jagyasini-singh-cute-cat.gif")
                        left top
                        no-repeat
                    `
                        }).then(() => {
                            window.location.href = '{{ url('/home') }}';
                        });
                    } else {
                        Toast.fire({
                            title: 'Error',
                            html: data.message,
                            icon: 'error',
                            backdrop: `
                       url("https://media.tenor.com/9z8aTaVmPfwAAAAi/cats-sad.gif")
                       right bottom
                       no-repeat
                    `
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status === 419) {
                        // Handle CSRF token expiration (Error 419)
                        Swal.fire({
                            title: 'Session Expired',
                            text: 'Your session has expired. Please refresh the page and try again.',
                            icon: 'warning',
                            confirmButtonText: 'Refresh Page',
                            backdrop: `
                        url("https://media.tenor.com/9z8aTaVmPfwAAAAi/cats-sad.gif")
                        right bottom
                        no-repeat
                    `
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        // Handle other errors
                        Toast.fire({
                            title: 'Error',
                            html: 'An error occurred. Please try again.',
                            icon: 'error',
                            backdrop: `
                        url("https://media.tenor.com/9z8aTaVmPfwAAAAi/cats-sad.gif")
                        right bottom
                        no-repeat
                    `
                        });
                    }
                }
            });
        });
    </script>

    {{-- middelwareLogin //app/http/middleware/usernameSession --}}
    @error('middelwareLogin')
        <script>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: '{{ $message }}'
            });
        </script>
    @enderror
@endsection
