@section('title', 'HVF | IT WORK | Need Help?')
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
            <h1>Need Help</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Need Help</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <!-- Right side columns -->
                <div class="col-lg-12" id="imagePreview">
                    <!-- News & Updates Traffic -->
                    <div class="col-12" >
                        <div class="card">
                            <div class="filter" id="add_news">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Work Flow แจ้งงาน IT</h6>
                                    </li>
                                    <li class="dropdown-item " id="open_modal">Add News</li>
                                </ul>
                            </div>

                            <div class="card-body pb-0">
                                <h5 class="card-title">Work Flow แจ้งงาน IT <span>| FlowChart</span></h5>
                                <div class="row">
                                    <div class="col-8 text-center">
                                        <img src="assets/img/needhelp/flowchart.jpg" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-4">
                                        <h5 class="card-title">คำอธิบาย <span>| FlowChart</span></h5>
                                        <h6>1. ผู้ใช้งานเปิด Case (Telegrame Notify)</h6>
                                        <h6>2. Staff IT ประเมินงาน และออกเลข IT-WOC (lineNotify)</h6>
                                        <h6>3. ดำเนินการ</h6>
                                        <h6>4. Staff IT ปิดงาน (Telegrame Notify and NiceSupportNotify)</h6>
                                        <h6>4. ผู้ใช้งานปิดเคส</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Work Flow-->

                        <!-- Start Register and Manage User-->
                        <div class="card">
                            <div class="card-body pb-3">
                                <h5 class="card-title">Register and Manage User <span>| user</span></h5>
                                <div class="row">
                                    <div class="col-5 text-center">
                                        <h6>1.Register</h6>
                                        <img src="assets/img/needhelp/register.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-1">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>2.กรอกข้อมูล (แนะนำให้เป็น ภาษาอังกฤษ)</h6>
                                        <img src="assets/img/needhelp/register_1.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-5 text-center">
                                        <h6>3.Approve User แจ้งหัวหน้าแผนกของท่าน เพื่อ Approve จาก Preuser เป็น user
                                            (ต้องมี Level Manager,Director ขึ้นไป)</h6>
                                        <img src="assets/img/needhelp/Approve_user.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-1">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>4.หากต้องการแก้ไขข้อมูล User ให้ถอย level User ท่านนั้นเป็น Preuser
                                            และทำการแก้ไข</h6>
                                        <img src="assets/img/needhelp/edit_user.png" alt="" class="img-fluid">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- End Register and Manage User-->
                        <!-- End ADD JOB-->
                        <div class="card">
                            <div class="card-body pb-3">
                                <h5 class="card-title">ADD JOB <span>| job</span></h5>
                                <div class="row">
                                    <div class="col-5 text-center">
                                        <h6>1.เปิดงาน</h6>
                                        <img src="assets/img/needhelp/add_job.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-1">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>2.Popup Add Job</h6>
                                        <img src="assets/img/needhelp/job_add_modal.png?v=1" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-5 text-center">
                                        <h6>3.แนบไฟล์</h6>
                                        <img src="assets/img/needhelp/job_add_image.png?v=1" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-1">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>4.เลือกไฟล์ที่จะแนบ (แนบได้ที่ status เปิดงานเท่านั้น)</h6>
                                        <img src="assets/img/needhelp/job_edit_modal.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-5 text-center">
                                        <h6>5.Click Detail</h6>
                                        <img src="assets/img/needhelp/click_detail.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-1">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>6.Detail</h6>
                                        <img src="assets/img/needhelp/Detail.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-5 text-center">
                                        <h6>7.สารมารถ Cancel รายการได้ที่ Status เปิดงานเท่านั้น</h6>
                                        <img src="assets/img/needhelp/cancel.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-1">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>8.กรุณาปิดงานมือถึง status รอปิดงาน</h6>
                                        <img src="assets/img/needhelp/job_close.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-12  text-center">
                                        <h6 class="text-bg-danger">9.หมายเหตุ งานที่รอปิด จะปิดเองภายใน 3 วัน</h6>
                                        {{-- <img src="assets/img/needhelp/job_alert_close.png" alt="" class="img-fluid"> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End ADD JOB-->

                        <!-- Start Notify-->
                        <div class="card">
                            <div class="card-body pb-3">
                                <h5 class="card-title">Notify <span>| Line and NiceSupport</span></h5>
                                <div class="row">
                                    <div class="col-5 text-center">
                                        <h6>1.Breaking News สามารถคลิ้ก view all ไปดูเต็มได้ที่หน้า Home</h6>
                                        <img src="assets/img/needhelp/Breaking_News.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-1">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>2.สามารถติดตามงานของท่านได้ เริ่ม Status ประเมินงาน ถึงรอปิดงาน </h6>
                                        <img src="assets/img/needhelp/NotiNiceSup.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-5 text-center">
                                        <h6>3.Telegrame Notify News IT</h6>
                                        <img src="assets/img/needhelp/LineNotify_News.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-1">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>4.Telegrame Notify เปิดงาน</h6>
                                        <img src="assets/img/needhelp/LineNotify_Openjob.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-5 text-center">
                                        <h6>5.Telegrame Notify ประเมินงาน</h6>
                                        <img src="assets/img/needhelp/LineNotify_estimate.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-1">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>6.Telegrame Notify ดำเนินงานเสร็จ รอปิดงาน</h6>
                                        <img src="assets/img/needhelp/LineNotify_waiting_for_closing.png" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-5  text-center">
                                        <h6>7.QR Telegrame Notify(Dowload ได้ที่ PlayStore หรือ AppStore) <a href="https://t.me/+L2vHugT4kGFiODZl" target="_blank">https://t.me/+L2vHugT4kGFiODZl</a></h6>
                                        <img src="assets/img/needhelp/QRCODE.jpg" alt="" class="img-fluid">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- End Notify-->

                    </div>
                </div><!-- End Right side columns -->

            </div>
        </section>

    </main><!-- End #main -->
    @include('layouts.footer')
@endsection
