<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="icon"
        href="https://cdn2.iconfinder.com/data/icons/seo-web-optomization-ultimate-set/512/customer_support-512.png"
        type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    {{-- font --}}
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    {{-- icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Vendor CSS Files -->
    <link href="{{ url('assets/vendor/bootstrap/css/bootstrap.min.css?v=1') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">




    {{-- aria-hidden-modal-fileinput --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>


    <!-- Template Main CSS File -->
    <link href="{{ url('assets/css/style.css?v=12') }}" rel="stylesheet">

    {{-- sweet alert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.6/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.6/dist/sweetalert2.min.js"></script>

    {{-- ajax --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- data Table --}}
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    {{-- file_input --}}
    <link rel="stylesheet" href="{{ url('assets/css/fileinput.css') }}">
    <script src="{{ url('assets/js/fileinput.js') }}"></script>
    {{-- daterange --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />



    <style>
        .bg_job {
            background-color: #e2f2e3;
            color: #155210;
            border-color: #87aa8a;
        }

        .bg_register {
            background-color: #e2f2f8;
            color: #003366;
            border-color: #87aaff;
        }

        .modal-dialog {
            margin-right: 0;
            margin-left: auto;
            margin-top: 0;
        }

        #open_modal {
            /* color: #4154f1; */
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            /* ป้องกันการตัดบรรทัด */
        }

        table.dataTable>thead>tr>th:not(.sorting_disabled),
        table.dataTable>thead>tr>td:not(.sorting_disabled) {
            padding-right: 6px;
        }

        .status-p {
            color: #fff;
            padding: 0px 20px 1px;
            border-radius: 20px;
            display: inline-block;
            text-transform: capitalize;
            vertical-align: middle;
        }

        /* เพิ่มเข้ามาาา  */
        .password-input {
            position: relative;
            width: 100%;
        }

        .password-input input {
            padding-right: 40px;
            /* เผื่อที่ไว้ให้ icon ด้านขวา */
        }

        .password-input div {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .password-input div:hover {
            color: #000;
        }


        /* -------------------- */

        /* #togglePassword1 {
            position: absolute;
            right: 35%;
            top: 57%;
            padding: 0 10px;
            background: transparent;
            border: none;
        } */

        /* #togglePassword2 {
            position: absolute;
            right: 35%;
            top: 70%;
            padding: 0 10px;
            background: transparent;
            border: none;
        } */

        #togglePassword3 {
            position: absolute;
            right: 12%;
            top: 58%;
            padding: 0 10px;
            background: transparent;
            border: none;
        }

        .select2-container {
            width: 100% !important;
        }

        .progress {
            width: 70px;
        }

        .description_it {
            margin-bottom: 0 !important;
        }

        .size_it_woc {
            font-size: 10px !important;
        }

        .text-color-open-job {
            color: rgb(255 154 6) !important;
        }

        #imagePreview {
            display: flex;
            flex-wrap: wrap;
            /* Allow images to wrap to the next line */
            gap: 10px;
            /* Space between images */
        }

        .img-container {
            position: relative;
            width: 120px;
            /* Adjust size as needed */
            height: 120px;
            /* Adjust size as needed */
        }

        .img-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            /* Ensure the image covers the container */
        }

        .delete-checkbox {
            position: absolute;
            top: 5px;
            /* Space from the top */
            left: 5px;
            /* Space from the left */
            z-index: 10;
        }

        .lightbox {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .lightbox-image {
            max-width: 90%;
            max-height: 80%;
            border: 2px solid white;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 30px;
            color: white;
            cursor: pointer;
            z-index: 2001;
        }

        .dataTables_wrapper {
            margin-bottom: 1.5rem !important;
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
            max-height: 300px;
            /* min-height: 300px; */
            margin: 10px;
        }

        #type1:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        #type2:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        #type3:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
</head>

<body>

    @yield('content')

    @yield('footer')

    <!-- Vendor JS Files -->
    <script src="{{ url('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    {{-- <script src="{{ url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
    <script src="{{ url('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ url('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ url('assets/vendor/quill/quill.js') }}"></script>
    <script src="{{ url('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ url('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ url('assets/vendor/php-email-form/validate.js') }}"></script>

    {{-- daterangepicker --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- Template Main JS File -->
    <script src="{{ url('assets/js/main.js') }}"></script>

    <script>
        //onclick show image // Home , Job
        $(document).ready(function() {
            // Show lightbox with clicked image
            $(document).on('click', '#imagePreview img, .news_it img', function() {
                const src = $(this).attr('src') || '';
                // alert('test');
                if (src) {
                    $('#lightboxImage').attr('src', src);
                    $('#lightbox').show();
                }
            });


            // Hide lightbox when clicking the close button
            $('#closeLightbox').on('click', function() {
                $('#lightbox').hide();
            });

            // Hide lightbox when clicking outside the image
            $('#lightbox').on('click', function(event) {
                if (event.target === this) {
                    $('#lightbox').hide();
                }
            });
        });



        $(document).ready(function() {
            $.ajax({
                url: '{{ url('department') }}',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // ตรวจสอบข้อมูลที่ได้รับ
                    // console.log('Raw data:', data);

                    var select = $('[data-role="department"]');
                    select.empty();
                    select.append(
                        '<option class="text-center" selected disabled>-- Department --</option>');

                    // ใช้ข้อมูลจากอาร์เรย์ภายในอาร์เรย์
                    var departments = data.departments || []; // ดึงข้อมูลจากคีย์ที่ถูกต้อง

                    $.each(departments, function(index, item) {
                        var departmentsValue = item.department_name.trim();
                        var departmentsid = item.did.trim();
                        select.append('<option value="' + departmentsid + '">' + departmentsid +
                            ' - ' +
                            departmentsValue + '</option>');
                    });
                },
                error: function(error) {
                    console.log('Error:', error);
                }
            });
            // Status
            $.ajax({
                url: '{{ url('status') }}',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // ตรวจสอบข้อมูลที่ได้รับ
                    // console.log('Raw data:', data);

                    var select = $('[data-role="filter_status"]');
                    select.empty();
                    select.append(
                        '<option class="text-center" selected disabled>-- status --</option>');

                    // ใช้ข้อมูลจากอาร์เรย์ภายในอาร์เรย์
                    var statuss = data.statuss || []; // ดึงข้อมูลจากคีย์ที่ถูกต้อง

                    $.each(statuss, function(index, item) {
                        var statusValue = item.name.trim();
                        // console.log(statusValue)
                        var statusid = item.status.trim();
                        select.append('<option value="' + statusid + '">' + statusValue +
                            '</option>');
                    });
                },
                error: function(error) {
                    console.log('Error:', error);
                }
            });
        });

        // การกำหนดวันที่สำหรับ date range picker
        var start = moment().subtract(30, 'days');
        var end = moment().add(1, 'days');


        // ฟังก์ชัน callback สำหรับแสดงวันที่
        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
        const currentYear = moment().year();
        // การตั้งค่า date range picker
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            showDropdowns: true,
            minYear: 2022, // กำหนดปีต่ำสุดที่สามารถเลือกได้
            maxYear: currentYear, // กำหนดปีสูงสุดเป็นปีปัจจุบัน
            maxDate: moment().endOf('year'),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [
                    moment().subtract(1, 'month').startOf('month'),
                    moment().subtract(1, 'month').endOf('month')
                ],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment()
                    .subtract(1, 'year')
                    .endOf('year')
                ]
            }
        }, cb);

        // เรียกใช้งานฟังก์ชัน callback เพื่อแสดงวันที่เริ่มต้นและสิ้นสุดในครั้งแรก
        cb(start, end);



        // var timeout;
        // var timeoutDuration = 900000; // 15 นาที

        // function resetTimer() {
        //     clearTimeout(timeout);
        //     timeout = setTimeout(function() {
        //         window.location.href = "{{ url('/') }}"; // ไปที่หน้า login
        //     }, timeoutDuration);
        // }

        // // เรียกใช้เมื่อผู้ใช้เคลื่อนไหว
        // window.onload = resetTimer;
        // document.onmousemove = resetTimer;
        // document.onkeypress = resetTimer;
    </script>
</body>

</html>

</html>
