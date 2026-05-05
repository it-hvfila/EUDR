    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ url('/home') }}" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="">
                <span class="d-none d-lg-block">Documents</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>
        <!-- End Logo -->
        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item dropdown">

                    <!-- Notification Icon -->
                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="badge bg-primary badge-number">0</span>
                    </a>

                    <!-- Dropdown Notification Items -->
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications overflow-auto"
                        style="max-height: 500px;">
                        <li class="dropdown-header">
                            You have 0 new notifications
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <!-- Notification Items will be dynamically inserted here by AJAX -->
                    </ul>
                </li>
                <!-- End Notification Nav -->

                <!-- End Messages Nav -->

                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                        data-bs-toggle="dropdown">
                        <img src="assets/img/user-profile-icon-free-vector.jpg" alt="Profile" class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2">{{ session('user.1surname') }}
                            {{ session('users.name') }}</span>
                        {{-- <span class="d-none d-md-block dropdown-toggle ps-2">K. Anderson</span> --}}
                    </a>
                    <!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ session('users.name') }}
                            </h6>
                            <span>{{ session('users.username') }}</span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('/need_help') }}">
                                <i class="bi bi-question-circle"></i>
                                <span>Need Help?</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->



    <script>
        $(document).ready(function() {
            function loadNotifications() {
                $.ajax({
                    url: '{{ url('notifications') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Clear existing notifications
                        $('.notifications').empty();

                        // Update badge number
                        $('.badge-number').text(data.count + data.count_news);

                        if (data.count_news !== 0) {
                            // Update notification header_news
                            let header_news = `
                                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                                            <div>
                                                You have ${data.count_news} News notifications
                                            </div>
                                            <span class="ms-auto">
                                                <a href="{{ url('home') }}" class="text-end">
                                                    <span class="badge rounded-pill bg-primary p-2 ms-2">View all</span>
                                                </a>
                                            </span>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                    `;
                            $('.notifications').append(header_news);
                            // Append each notificationItem_news
                            data.notifications_news.forEach(function(notifications_news) {
                                let notificationItem_news = `
                                                    <li class="notification-item">
                                                        <i class="bi ${notifications_news.icon} ${notifications_news.iconColor}"></i>
                                                        <div>
                                                            <h4>${notifications_news.title}</h4>
                                                            <p>${notifications_news.description}</p>
                                                            <p>${notifications_news.time_ago}</p>
                                                        </div>
                                                    </li>
                                        <li><hr class="dropdown-divider"></li>
                                                `;
                                $('.notifications').append(notificationItem_news);
                            });
                        }
                        if (data.count !== 0) {
                            let header = `
                                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                                            <div>
                                                You have ${data.count} Job notifications
                                            </div>
                                            <span class="ms-auto">
                                        <a href="{{ url('job') }}"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                                            </span>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                    `;
                            $('.notifications').append(header);

                            // Append each notification
                            data.notifications.forEach(function(notification) {
                                let notificationItem = `
                                                    <li class="notification-item">
                                                        <i class="bi ${notification.icon} ${notification.iconColor}"></i>
                                                        <div>
                                                            <h4>${notification.job_code}</h4>
                                                            <p>${notification.title}</p>
                                                            <p>${notification.time_ago}</p>
                                                        </div>
                                                    </li>
                                                `;
                                $('.notifications').append(notificationItem);
                            });
                        }
                    }
                });
            }

            // Call function on page load
            loadNotifications();

            // Optionally, refresh notifications every minute
            setInterval(loadNotifications, 60000);


        });
    </script>


    <script type="text/javascript">
        var timeout;
        var timeoutDuration = 900000; // 900,000 มิลลิวินาที = 15 นาที
        // var timeoutDuration = 5000;// 900,000 มิลลิวินาที = 15 นาที

        function resetTimer() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                // แสดง SweetAlert แจ้งเตือน
                Swal.fire({
                    icon: 'error',
                    title: 'Timeout',
                    text: 'กรุณาล็อคอินใหม่',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false, // ห้ามกดนอก popup แล้วปิด
                    allowEscapeKey: false, // ห้ามกด ESC เพื่อปิด
                }).then((result) => {
                    // เมื่อกด OK จะรีไดเรกไปที่หน้า logout
                    if (result.isConfirmed) {
                        window.location.href = "{{ url('/logout') }}"; // ไปที่หน้า login
                    }
                });
            }, timeoutDuration);
        }

        // เรียกใช้เมื่อผู้ใช้เคลื่อนไหว
        // เรียกใช้เมื่อผู้ใช้เคลื่อนไหวครั้งแรก
        window.onload = resetTimer;
        document.onmousemove = function() {
            resetTimer(); // เรียกใช้การตั้งเวลาใหม่เมื่อเคลื่อนไหว
            // console.log('test')
        };
        document.onkeypress = function() {
            resetTimer(); // เรียกใช้การตั้งเวลาใหม่เมื่อกดปุ่ม
            // console.log('test')
        };
    </script>
