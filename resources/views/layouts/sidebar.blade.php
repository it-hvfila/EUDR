<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{ Request::is('home') ? '' : ' collapsed' }}" href="{{ url('/home') }}">
                <i class="bi bi-house-door"></i>
                <span>Home</span>
            </a>
        </li>

        @if (session('users.level') == 1)
            <!-- ===== EUDR / Traceability ===== -->
            <li class="nav-heading">EUDR / Traceability</li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('shipment-report') ? 'active' : 'collapsed' }}"
                    href="{{ url('/invoice') }}">
                    <i class="bi bi-diagram-3"></i>
                    <span>EUDR invoice</span>
                </a>
            </li>

            <!-- ===== Supplier & Source ===== -->
            <li class="nav-heading">Supplier & Source</li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('supplier') ? 'active' : 'collapsed' }}"
                    href="{{ url('/supplier') }}">
                    <i class="bi bi-map"></i>
                    <span>Map น้ำยาง</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('supplier_docs') ? 'active' : 'collapsed' }}"
                    href="{{ url('/supplier_docs') }}">
                    <i class="bi bi-file-earmark-person"></i>
                    <span>เอกสาร Supplier</span>
                </a>
            </li>

            <!-- ===== Company ===== -->
            <li class="nav-heading">Company</li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('company_docs') ? 'active' : 'collapsed' }}"
                    href="{{ url('/company_docs') }}">
                    <i class="bi bi-building"></i>
                    <span>เอกสารบริษัท</span>
                </a>
            </li>

            <!-- ===== Administration ===== -->
            <li class="nav-heading">Administration</li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('manage_user') ? 'active' : 'collapsed' }}"
                    href="{{ url('/manage_user') }}">
                    <i class="bi bi-people-gear"></i>
                    <span>Manage Users</span>
                </a>
            </li>
        @endif
    </ul>

</aside>
