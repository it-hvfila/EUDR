  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">
          <li class="nav-item">
              <a class="nav-link {{ Request::is('home') ? '' : ' collapsed' }}" href="{{ url('/home') }}">
                  <i class="bi bi-grid"></i>
                  <span>Home</span>
              </a>
          </li>
          <li class="nav-item ">
              <a class="nav-link {{ Request::is('job') ? '' : ' collapsed' }}" href="{{ url('/job') }}">
                  <i class="bi bi-journal-text"></i>
                  <span>Job</span>
              </a>
          </li>>

          <li class="nav-item ">
              <a class="nav-link {{ Request::is('manage_user') ? '' : ' collapsed' }}" href="{{ url('/manage_user') }}">
                  <i class="bi bi-person"></i>
                  <span>Manage Users</span>
              </a>
          </li>

          <!-- End Components Nav -->


      </ul>

  </aside><!-- End Sidebar-->