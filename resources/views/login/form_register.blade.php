<div class="card bg_register">
    <div class="card-body">
        <!-- Multi Columns Form -->
        <div class="col-md-12 mt-2">
            <div class="row">
                <div class="col-6">
                    {{-- id --}}
                    <input type="hidden" class="form-control text-center" id="id" name="id">
                    <div class="form-group">
                        <label for="number">รหัสพนักงาน</label>
                        <input type="number" class="form-control" id="empno" name="empno"
                            placeholder="รหัสพนักงาน..">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group mt-3 ">
                        <label for="number">แผนก</label>
                        <select class="form-select w-100" aria-label="Default select example" name="department"
                            id="department" data-role="department">
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mt-3">
                        <label for="name">ชื่อ</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="ชื่อ..">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mt-3">
                        <label for="name">นามสกุล</label>
                        <input type="text" class="form-control" id="surname" name="surname"
                            placeholder="นามสกุล..">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-7">
                    <div class="form-group mt-3">
                        <label for="name">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="ชื่อผู้ใช้..">
                    </div>
                    <div class="form-group mt-3">
                        <label for="password">Password</label>
                        <div class="password-input">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="รหัสผ่าน.." required>
                            <div type="button" id="togglePassword1" onclick="togglePasswordVisibility()">
                                <i id="passwordIcon" class="fa fa-eye"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="password">Confirm password</label>
                        <div class="password-input">
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" placeholder="ยืนยันรหัสผ่าน.." required>
                            <div type="button" id="togglePassword2" onclick="togglePasswordConfirm()">
                                <i id="passwordIcon_confirmation" class="fa fa-eye"></i>
                            </div>
                        </div>

                    </div>
                    <div class="form-group mt-3">
                        <label for="name">Description</label>
                        <textarea class="form-control" placeholder="รายละเอียด.." id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-5">
                    <div class="form-group mt-3">
                        <div class="mt-3">
                            <img id="preview" src="assets/img/user.png" alt="Preview Image" width="90%"
                                class="rounded shadow">
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="image">รูปภาพ</label>
                        <input type="file" class="form-control" id="img_user" name="img_user" accept="image/*"
                            onchange="previewImage(event)">
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4" id="status_staff_it">
                <label for="inputName5" class="form-label mx-2">Status Staff IT</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status_it" id="status_it0" value="0">
                    <label class="form-check-label" for="status_it0">offline</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status_it" id="status_it1" value="1">
                    <label class="form-check-label" for="status_it1">online</label>
                </div>
            </div>


        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        var input = event.target;
        var reader = new FileReader();

        reader.onload = function() {
            var preview = document.getElementById('preview');
            preview.src = reader.result;
        };

        if (input.files[0]) {
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePasswordVisibility() {
        var passwordInput = document.getElementById("password");
        var passwordIcon = document.getElementById("passwordIcon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordIcon.classList.remove("fa-eye");
            passwordIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            passwordIcon.classList.remove("fa-eye-slash");
            passwordIcon.classList.add("fa-eye");
        }
    }

    function togglePasswordConfirm() {
        var passwordInput = document.getElementById("password_confirmation");
        var passwordIcon = document.getElementById("passwordIcon_confirmation");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordIcon.classList.remove("fa-eye");
            passwordIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            passwordIcon.classList.remove("fa-eye-slash");
            passwordIcon.classList.add("fa-eye");
        }
    }

    function togglePasswordLogin() {
        var passwordInput = document.getElementById("passwordlogin");
        var passwordIcon = document.getElementById("passwordIconLogin");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordIcon.classList.remove("fa-eye");
            passwordIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            passwordIcon.classList.remove("fa-eye-slash");
            passwordIcon.classList.add("fa-eye");
        }
    }

    function initializeSelect2(elementId) {
        $(elementId).select2({
            placeholder: "Choose",
            allowClear: true,
            dropdownParent: $(elementId).closest('.col-12')
        });
    }
    // Call the function for multiple IDs
    initializeSelect2('#department');


    //sweetalert error
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        // timer: 3000,
        showCloseButton: true, // เพิ่มปุ่มปิด
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    // Register form submission
    $('#registerForm').on('submit', function(event) {
        event.preventDefault();

        // Collect form data
        // var formData = $(this).serialize();
        var formData = new FormData(this);

        // Additional validation for password confirmation
        var password = $('#password').val();
        var passwordConfirmation = $('#password_confirmation').val();
        if (password != passwordConfirmation) {
            Toast.fire({
                title: 'Error',
                html: 'รหัสผ่านไม่ตรงกัน กรุณากรอกใหม่',
                icon: 'error',
                backdrop: `
                            url("https://media.tenor.com/9z8aTaVmPfwAAAAi/cats-sad.gif")
                            right bottom
                            no-repeat
                            `
            });
            return;
        }
        $.ajax({
            url: "{{ url('/register') }}",
            method: "POST",
            data: formData,
            contentType: false, // ❗ สำคัญ
            processData: false, // ❗ สำคัญ
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    html: response.message.replace(/\n/g,
                        '<br>'), // Replace \n with <br> for line breaks
                    icon: 'success',
                    backdrop: `
                                    rgba(0,123,39,0.4)
                                    url("https://media.tenor.com/ek214PnxJhEAAAAi/jagyasini-singh-cute-cat.gif")
                                    left top
                                    no-repeat
                                    `
                }).then((result) => {
                    location.reload();
                });
                $('#registerModal').modal('hide'); // Close the modal

            },
            error: function(xhr) {
                var errorMessage = 'An error occurred while inserting the record.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                }
                Toast.fire({
                    title: 'Error',
                    html: errorMessage,
                    icon: 'error',
                    backdrop: `
                                   url("https://media.tenor.com/9z8aTaVmPfwAAAAi/cats-sad.gif")
                                   right bottom
                                   no-repeat
                                    `
                });
            }
        });
    });


    $(document).ready(function() {
        let currentPath = window.location.pathname;

        // ตรวจสอบว่าอยู่ในหน้า "/manage_user"
        if (currentPath === "/it_work_notification/public/manage_user") {
            $("#username").attr("readonly", true);
        }
    });
</script>
