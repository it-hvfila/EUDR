<div class="card bg_job">
    <div class="card-body">
        <!-- Multi Columns Form -->
        <div class="mt-3">
            <div class="row" id="Proceed">
                {{-- id --}}
                <input type="hidden" class="form-control text-center" id="id" name="id">
                <div class="col-md-9">
                    <label for="inputName5" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="หัวข้อข่าว">
                </div>
                <div class="col-md-12">
                    <label for="inputName5" class="form-label">Description</label>
                    <textarea class="form-control" style="height: 100px" placeholder="รายละเอียดเพิ่มเติม" id="description"
                        name="description"></textarea>
                </div>
                <div class="col-md-12">
                    <div class="text-center mt-2">
                        <label for="formFileSm" class="form-label">แนบไฟล์รูปประกอบ</label>
                        <input type="file" class="form-control form-control-sm" id="image" name="image[]"
                            accept="image/*" multiple>
                        <div id="imagePreview" class="mt-2">
                            <!-- ภาพตัวอย่างจะถูกเพิ่มที่นี่ -->
                        </div>
                        <button type="button" id="clearAll" class="btn btn-primary btn-sm">Clear All</button>
                        <!-- ปุ่มลบทั้งหมด -->
                    </div>
                </div>

                <div class="col-12 mt-4" id="update_news">
                    <label for="inputName5" class="form-label mx-2">Status</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status1" value="1">
                        <label class="form-check-label" for="status1">Show News</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status2" value="2">
                        <label class="form-check-label" for="status2">Not Show</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status3" value="3">
                        <label class="form-check-label" for="status3">Set time</label>
                    </div>
                </div>
                <!-- Input date-time (initially hidden) -->
                <div class="col-12 mt-4" id="datetime_container" style="display: none;">
                    <div class="row">
                        <div class="col-6">
                            <label for="datetime" class="form-label mx-2">Date Start</label>
                            <input type="datetime-local" id="start_of_news" name="start_of_news" class="form-control">
                        </div>
                        <div class="col-6">
                            <label for="datetime" class="form-label mx-2">Date End</label>
                            <input type="datetime-local" id="end_of_news" name="end_of_news" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Multi Columns Form -->
    </div>
</div>

<script>
    var department = @json(session('user.department'));
    var name = @json(session('user.name'));
    $(document).ready(function() {
        if (department == 'D005') {
            $('#add_news').show();
            $('#update_news').show();
        } else {
            $('#add_news').hide();
            $('#update_news').hide();
        }

        $('input[name="status"]').on('change', function() {
            if ($('#status3').is(':checked')) {
                $('#datetime_container').show(); // Show date-time input
            } else {
                $('#datetime_container').hide(); // Hide date-time input
                $('#start_of_news').val(''); // Clear the start date
                $('#end_of_news').val(''); // Clear the end date
            }
        });

    });

    // {{-- News & Updates --}}
    function loadLatestNews() {
        $.ajax({
            url: '{{ url('/latest-news') }}',
            method: 'GET',
            success: function(response) {
                let newsHtml = '';
                response.forEach(function(news) {
                    newsHtml += `
                        <div class="post-item clearfix news_it">
                            <img src="data:image/png;base64,${news.image}" alt="" class="me-3">
                            <h3><a href="#" id="open_modal" data-id="${news.id}">${news.title}</a></h3>
                            <p>- ${news.description}</p>
                        </div>
                    `;
                });
                document.querySelector('.news').innerHTML = newsHtml;
            }
        });
    }


    $('#image').on('change', function(event) {
        const files = event.target.files;
        const previewContainer = $('#imagePreview');
        // previewContainer.empty(); // Clear previous previews

        if (files.length > 0) {
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = $('<img>').attr('src', e.target.result).css({
                        'max-width': '100px', // Adjust size as needed
                        'margin': '5px'
                    });
                    previewContainer.append(img);
                };
                reader.readAsDataURL(file);
            });
        }
    });
    // Clear all images and reset file input
    $('#clearAll').on('click', function() {
        $('#imagePreview').empty(); // Remove image previews
        filesArray = []; // Clear file array

        // Clear the file input
        const dataTransfer = new DataTransfer();
        $('#image')[0].files = dataTransfer.files;
    });
    $(document).ready(function() {
        // Load the latest news on page load
        loadLatestNews();
        // Handle form submission
        $('#Form_News').on('submit', function(e) {
            e.preventDefault();

            // Serialize form data
            // var formData = $(this).serialize();
            var formData = new FormData(this); //image

            // รับ ID ของภาพที่ต้องการลบจาก checkbox
            const imagesToDelete = $('.delete-checkbox:checked').map(function() {
                return $(this).data('image-id');
            }).get();

            // เพิ่มข้อมูล ID ของภาพที่ต้องการลบลงใน FormData
            formData.append('images_to_delete', JSON.stringify(imagesToDelete));

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

            // Perform AJAX request to insert data into the database
            $.ajax({
                url: "{{ url('Form_News') }}", // Replace with the appropriate route
                type: 'POST',
                data: formData,
                processData: false, // Required for FormData
                contentType: false, // Required for FormData
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
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
                        })
                        loadLatestNews();
                        $('#editNewsModal').modal('hide');
                    } else {
                        Toast.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error',
                            backdrop: `
                                   url("https://media.tenor.com/9z8aTaVmPfwAAAAi/cats-sad.gif")
                                   right bottom
                                   no-repeat
                            `
                        });
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'An error occurred while inserting the record.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                    }
                    Toast.fire({
                        title: 'Error',
                        text: errorMessage,
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
    });

    // Open the modal for both insert and update
    $(document).on('click', '#open_modal', function() {
        var recordId = this.id;
        var dataId = this.getAttribute('data-id');
        // console.log(dataId)
        // Check if it's an update or insert action
        if (dataId !== null) {
            $.ajax({
                url: '{{ url('/news') }}/' + dataId + '/edit',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log(response);
                    fillFormFields(response);
                    setModalTitleAndDate(true, response); // Call the function for edit
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while fetching customer data.',
                        icon: 'error'
                    });
                }
            });
        } else {
            setModalTitleAndDate(false); // Call the function for insert
        }
    });

    //Modal show insert edit
    function setModalTitleAndDate(isEdit, response = null) {
        var now = new Date();
        var formattedNow = now.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: '2-digit'
        });

        if (isEdit) {
            var news_it = response.news_it; // Access the job_it object
            var status = news_it.status;
            var created_by = news_it.created_by ||
                'Unknown'; // Default to 'Unknown' if created_by is not available
            var createdAt = new Date(news_it.created_at);
            var proceed_when = new Date(news_it.proceed_when);
            var formattedDate = createdAt.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: '2-digit'
            });
            var formatted_proceed_when = new Date(proceed_when).toLocaleString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false // ใช้ 24-hour format
            });
            // console.log(response.created_by)
            $('#modal-title-text').text('News');
            $('#modal-title-date').text('(' + created_by + ') ' + formattedDate);
            if (formatted_proceed_when && formatted_proceed_when !== '01/01/70, 07:00') {
                $('#proceed_when').text('ดำเนินการเมื่อ (' + formatted_proceed_when + ') ').removeClass(
                    'text-danger').addClass('text-success');
            } else {
                $('#proceed_when').text('ยังไม่ได้ดำเนินการ').removeClass('text-success').addClass(
                    'text-danger');
            }
            $('#customer-code-group').show();
            $('#clearAll').hide();
            $('#it_staff').show();
            if (department == 'D005') {
                $('.saveButton').show();
                $('#saveButton').text('Update');
                $('#saveButton').removeClass('btn-primary').addClass('btn-warning');
            } else {
                $('.saveButton').hide();
            }
        } else {
            $('#modal-title-text').text('Add News');
            $('#modal-title-date').text(formattedNow);
            $('#customer-code-group').hide();
            $('#clearAll').text('clear image');
            $('#clearAll').show();
            $('#it_staff').hide();
            $('#clearAll').removeClass('btn-warning').addClass('btn-danger');
            $('#saveButton').text('Save');
            $('#saveButton').removeClass('btn-warning').addClass('btn-primary');
        }

        $('#editNewsModal').modal('show');
    }

    $('#editNewsModal').on('hidden.bs.modal', function(e) {
        // เคลียร์ค่าทุกครั้งเมื่อโมดัลถูกปิดลง
        console.log('Modal is hidden');
        $('#editNewsModal input[type="text"]').val('');
        $('#editNewsModal input[type="number"]').val('');
        $('#editNewsModal input[type="file"]').val('');
        $('#imagePreview').empty(); // Clear image previews
        $('#editNewsModal textarea').val('');
        $('#editNewsModal select').prop('selectedIndex', 0);
        $('#editNewsModal input[name="id"]').val('');
        $('#editNewsModal select').val(null).trigger('change');
    });

    function fillFormFields(data) {
        $('#id').val(data.news_it.id);
        $('#title').val(data.news_it.title.replace(/\\/g, ''));
        $('#description').val(data.news_it.description);
        $('input[name="status"][value="' + data.news_it.status + '"]').prop('checked', true);
        $('#status').val(data.news_it.status);
        $('#start_of_news').val(data.news_it.start_of_news);
        $('#end_of_news').val(data.news_it.end_of_news);
        $('#created_by').val(data.news_it.created_by);
        // ตรวจสอบสถานะของ radio button เมื่อโหลดหน้าเว็บ
        if ($('#status3').is(':checked')) {
            $('#datetime_container').show(); // แสดง input date-time ถ้าเลือก "Set time"
        } else {
            $('#datetime_container').hide(); // ซ่อน input date-time ถ้าไม่ได้เลือก "Set time"
            $('#start_of_news').val(''); // ล้างค่า start date
            $('#end_of_news').val(''); // ล้างค่า end date
        }

        // เติมข้อมูลรูปภาพ
        const imagePreviewContainer = $('#imagePreview');
        imagePreviewContainer.empty(); // เคลียร์รูปภาพที่แสดงอยู่ก่อนหน้า

        data.images.forEach(image => {
            const imgContainer = $('<div>').addClass('img-container');
            const img = $('<img>').attr('src', 'data:image/png;base64,' + image.image);

            // เพิ่ม checkbox สำหรับเลือกรูปภาพที่ต้องการลบ
            const checkbox = $('<input>').attr({
                type: 'checkbox',
                class: 'delete-checkbox',
                'data-image-id': image.id // เก็บข้อมูล ID ของภาพใน data attribute
            });
            // Ensure the checkbox is not checked by default
            checkbox.prop('checked', false);

            imgContainer.append(checkbox).append(img);
            imagePreviewContainer.append(imgContainer);
        });
    }
</script>
