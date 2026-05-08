@section('title', 'HVF | Customer Documents Portal')
@extends('layouts.master') {{-- ตรวจสอบว่า layouts.master มี jquery และ sweetalert แล้ว --}}

@section('content')
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="" class="logo d-flex align-items-center w-auto">
                                    <span class="d-none d-lg-block">Customer EURD Portal</span>
                                </a>
                            </div>

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Access Documents</h5>
                                        <p class="text-center small">Enter your temporary username & password</p>
                                    </div>

                                    {{-- id="customer_login_form" --}}
                                    <form class="row g-3 needs-validation" novalidate id="customer_login_form">
                                        @csrf
                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Username</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text">@</span>
                                                <input type="text" name="username" class="form-control" id="yourUsername"
                                                    required>
                                                <div class="invalid-feedback">Please enter your username.</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <div class="input-group">
                                                <input type="password" name="password" class="form-control"
                                                    id="passwordlogin" required>
                                                <span class="input-group-text" id="togglePassword3" style="cursor: pointer;"
                                                    onclick="togglePasswordLogin()">
                                                    <i id="passwordIconLogin" class="fa fa-eye"></i>
                                                </span>
                                                <div class="invalid-feedback">Please enter your password!</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-success w-100" type="submit">Access Files</button>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <p class="text-muted small text-center">Note: This access is temporary (15-30
                                                days).</p>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="credits">
                                Designed by <a href="">HVF IT Department</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        // Toggle Password Visibility
        function togglePasswordLogin() {
            var x = document.getElementById("passwordlogin");
            var icon = document.getElementById("passwordIconLogin");
            if (x.type === "password") {
                x.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                x.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // AJAX Login Logic
        $('#customer_login_form').on('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: '{{ url('/portal/login') }}', // เปลี่ยนเป็น URL ของ Portal ลูกค้า
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    // สามารถใส่ Loading ตรงนี้ได้
                },
                success: function(data) {
                    if (data.status == 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // ส่งลูกค้าไปที่หน้าดาวน์โหลด หรือ URL ที่เขาตั้งใจจะไปตอนแรก (Intended)
                            window.location.href = '{{ url('/portal/download-list') }}';
                        });
                    } else {
                        Swal.fire({
                            title: 'Access Denied',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(jqXHR) {
                    if (jqXHR.status === 419) {
                        Swal.fire({
                            title: 'Session Expired',
                            text: 'Please refresh the page.',
                            icon: 'warning'
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                            icon: 'error'
                        });
                    }
                }
            });
        });
    </script>

    {{-- เช็ค Error จาก Middleware --}}
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: '{{ session('error') }}'
            });
        </script>
    @endif
@endsection
