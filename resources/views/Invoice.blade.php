@section('title', 'HVF | EUDR Invoice')
@extends('layouts.master')

@section('content')
    @php $customerPortal = $customerPortal ?? false; @endphp
    @unless ($customerPortal)
        @include('layouts.header')
        @include('layouts.sidebar')
    @endunless

    <style>
        /* Invoice controls: scoped separately from the printable report. */
        .invoice-search-panel,
        .invoice-account-panel {
            border: 1px solid #e3eaf3;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 6px 24px rgba(28, 55, 90, .05);
            padding: 24px;
            margin-bottom: 20px;
        }
        .invoice-panel-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .invoice-panel-icon { display: grid; place-items: center; flex-shrink: 0; width: 44px; height: 44px; border-radius: 12px; background: #edf4ff; color: #0d6efd; font-size: 21px; }
        .invoice-panel-heading h5 { margin: 0 0 4px; color: #17345a; font-weight: 700; }
        .invoice-panel-heading p { margin: 0; color: #718096; font-size: 13px; }
        .invoice-search-fields { display: flex; align-items: flex-end; gap: 12px; }
        .invoice-search-field { flex: 1; min-width: 0; }
        .invoice-search-fields .form-control { min-height: 48px; background: #f8fafd; border-color: #dce4ee; font-size: 16px; border-radius: 10px; }
        .invoice-search-fields .btn { min-height: 48px; padding: 10px 30px; border-radius: 10px; font-weight: 600; }
        .invoice-account-heading { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; }
        .invoice-account-heading .invoice-panel-icon { background: #eaf7f0; color: #23845c; }
        .invoice-account-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .invoice-print-button { border-radius: 10px; padding: 10px 18px; font-weight: 600; }
        .invoice-status { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; background: #eaf7f0; color: #23734f; }
        .invoice-status.is-expired { background: #fff0ef; color: #b33b35; }
        .invoice-account-details { display: grid; grid-template-columns: .65fr 1.4fr 1.3fr 1fr; gap: 20px; padding: 18px 20px; margin: 0 0 20px; background: #f8fafd; border: 1px solid #edf1f6; border-radius: 12px; }
        .invoice-account-details dt { color: #718096; font-size: 12px; font-weight: 500; margin-bottom: 6px; }
        .invoice-account-details dd { color: #233b58; font-size: 14px; font-weight: 600; margin: 0; overflow-wrap: anywhere; }
        .invoice-account-footer { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
        .invoice-portal-link { min-width: 0; }
        .invoice-portal-link span { display: block; color: #718096; font-size: 12px; margin-bottom: 4px; }
        .invoice-portal-link a { font-size: 14px; overflow-wrap: anywhere; }
        .invoice-account-footer .btn { border-radius: 9px; font-size: 13px; padding: 10px 16px; }
        @media (max-width: 991px) {
            .invoice-account-details { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 575px) {
            .invoice-search-panel, .invoice-account-panel { padding: 18px; }
            .invoice-search-fields { flex-direction: column; align-items: stretch; }
            .invoice-account-details { grid-template-columns: 1fr; gap: 14px; padding: 16px; }
            .invoice-account-footer, .invoice-account-footer form, .invoice-account-footer .btn { width: 100%; }
        }

        .a4-wrapper {
            background: #f0f0f0;
            padding: 20px 0;
            display: flex;
            justify-content: center;
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm;
            background: #fff;
            color: #000;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: "Cordia New", "Arial", sans-serif;
            line-height: 1.15;
            font-size: 18px;
        }

        /* ตารางหลัก */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            table-layout: fixed;
            border: 1px solid #000;
        }

        .info-table th,
        .info-table td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            padding: 3px 8px;
            vertical-align: top;
            font-size: 16px;
            word-wrap: break-word;
            border-bottom: none;
        }

        /* การจัดการแถบสีหัวข้อ */
        .main-header {
            background: #2d5a27 !important;
            color: #fff !important;
            /* บังคับสีฟอนต์ขาว */
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #000 !important;
            font-size: 18px;
        }

        .sub-header {
            background: #d9d9d9 !important;
            color: #000 !important;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #000 !important;
        }

        .section-bar {
            background: #1a4d5e !important;
            color: #fff !important;
            font-weight: bold;
            font-size: 16px;
            padding: 4px 10px;
            border-bottom: 1px solid #000 !important;
        }

        .compliance-bar {
            background: #aad0e6 !important;
            color: #000 !important;
            font-weight: bold;
            font-size: 16px;
            padding: 4px 10px;
            border-bottom: 1px solid #000 !important;
        }

        /* ระบบ Indent */
        .level-1 {
            padding-left: 15px !important;
        }

        .level-2 {
            padding-left: 35px !important;
        }

        .level-3 {
            padding-left: 55px !important;
        }

        .border-bottom-row {
            border-bottom: 1px solid #000 !important;
        }

        .red-bold {
            color: red;
            font-weight: bold;
        }

        .link-text {
            text-decoration: underline;
            color: #31708f;
        }

        .customer-box {
            border: 1px solid #4a90e2;
            padding: 10px;
            margin-bottom: 10px;
            background: #f9fcff;
            font-size: 15px;
            line-height: 1.2;
        }

        @media print {

            /* บังคับให้เบราว์เซอร์พิมพ์สีพื้นหลัง */
            * {
                -webkit-print-color-adjust: exact !important;
                /* Chrome, Safari, Edge */
                print-color-adjust: exact !important;
                /* Firefox */
            }

            /* ซ่อนส่วนประกอบที่ไม่ใช่เอกสาร */
            header,
            footer,
            .sidebar,
            .navbar,
            #header,
            #footer,
            .no-print,
            .search-container,
            form,
            button,
            .btn {
                display: none !important;
            }

            @page {
                margin: 0;
            }

            .a4-wrapper {
                padding: 0;
                background: none;
            }

            .a4-page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                padding: 10mm;
            }

            main#main {
                margin-top: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
    @if (!empty($searched) && empty($hasGeoJson))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่พบข้อมูล',
                    text: 'ไม่พบไฟล์ GeoJSON สำหรับ FG นี้'
                });
            });
        </script>
    @endif

    <main id="main" class="main" @if ($customerPortal) style="margin-left: 0; margin-top: 0;" @endif>
        @if ($customerPortal)
            <div class="d-flex justify-content-between align-items-center no-print mb-3 flex-wrap gap-2">
                <div>
                    <a href="{{ route('customer.dashboard') }}">Your Reports</a>
                    <h1 class="h4 mt-2">Invoice {{ $report->invoice_no }} / Pack ID {{ $report->pack_id }}</h1>
                    <p class="mb-0">Access expires: {{ $report->expired_at }}</p>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    @if (isset($report) && isset($header))
                        <button type="button" onclick="window.print()" class="btn btn-success invoice-print-button"><i class="bi bi-printer me-2" aria-hidden="true"></i>Print / PDF (A4)</button>
                    @endif
                    <form method="POST" action="{{ route('customer.logout') }}">
                        @csrf
                        <button class="btn btn-outline-secondary" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        @endif
        @unless ($customerPortal)
        <div class="no-print">
            <div>
                <div>
                    <form id="searchForm" method="POST" action="{{ route('invoice.search') }}" class="invoice-search-panel">
                        @csrf
                        <div class="invoice-panel-heading">
                            <span class="invoice-panel-icon" aria-hidden="true"><i class="bi bi-search"></i></span>
                            <div>
                                <h5>ค้นหา Invoice</h5>
                                <p>ค้นหาข้อมูลรายงาน EUDR และจัดการสิทธิ์เข้าใช้งานของลูกค้า</p>
                            </div>
                        </div>
                        <div class="invoice-search-fields">
                            <div class="invoice-search-field">
                                <label for="invoice-pack-id" class="form-label fw-semibold">Pack ID</label>
                                <input id="invoice-pack-id" type="text" name="pack_id" class="form-control"
                                    placeholder="กรอก Pack ID เช่น 783" value="{{ $packId ?? '' }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-2" aria-hidden="true"></i>ค้นหา Invoice</button>
                        </div>
                    </form>
                    <!-- Loading Overlay -->
                    <div id="loadingOverlay"
                        style="display:none;
            position:fixed;
            top:0; left:0;
            width:100%;
            height:100%;
            background:rgba(255,255,255,0.7);
            z-index:9999;
            justify-content:center;
            align-items:center;">

                        <div class="text-center">
                            <div class="spinner-border text-primary" style="width:3rem; height:3rem;" role="status"></div>
                            <div class="mt-3 fw-bold">กำลังค้นหา...</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        @endunless

        @php
            $appx = 1;
        @endphp

        @if ($errors->any())
            <div class="alert alert-danger no-print">{{ $errors->first() }}</div>
        @endif
        @if (!$customerPortal && isset($header))
            <div class="invoice-account-panel no-print">
                @if (isset($report))
                    @php $reportAccessActive = $report->is_active && \Carbon\Carbon::parse($report->expired_at)->isFuture(); @endphp
                    <div class="invoice-account-heading">
                        <div class="invoice-panel-heading">
                            <span class="invoice-panel-icon" aria-hidden="true"><i class="bi bi-person-check"></i></span>
                            <div>
                                <h5>บัญชีลูกค้าสำหรับรายงานนี้</h5>
                                <p>สร้างบัญชีแล้ว สามารถจัดการข้อมูลเข้าใช้งานได้ด้านล่าง</p>
                            </div>
                        </div>
                        <div class="invoice-account-actions">
                        <span class="invoice-status {{ $reportAccessActive ? '' : 'is-expired' }}">
                            <i class="bi {{ $reportAccessActive ? 'bi-check-circle' : 'bi-exclamation-circle' }}" aria-hidden="true"></i>
                            {{ $reportAccessActive ? 'สิทธิ์ใช้งานอยู่' : 'หมดอายุ / ยกเลิก' }}
                        </span>
                        <button type="button" onclick="window.print()" class="btn btn-success invoice-print-button">
                            <i class="bi bi-printer me-2" aria-hidden="true"></i>Print / PDF (A4)
                        </button>
                        </div>
                    </div>
                    @if (!empty($missingEvidence))
                        <div class="alert alert-danger">ไม่พบไฟล์หลักฐานบนเซิร์ฟเวอร์: {{ implode(', ', $missingEvidence) }} กรุณาตรวจสอบก่อนส่งรายงาน</div>
                    @endif
                    <dl class="invoice-account-details">
                        <div><dt>Pack ID</dt><dd>{{ $report->pack_id }}</dd></div>
                        <div><dt>ลูกค้า / Customer</dt><dd>{{ $report->customer_name }}</dd></div>
                        <div><dt>ชื่อผู้ใช้ / Username</dt><dd>{{ $reportAccount->username }}</dd></div>
                        <div><dt>วันหมดอายุ</dt><dd>{{ \Carbon\Carbon::parse($report->expired_at)->format('d/m/Y H:i') }}</dd></div>
                    </dl>
                    @if (!empty($newPassword))
                        <div class="alert alert-warning">Password: <strong>{{ $newPassword }}</strong><br>
                        แสดงรหัสผ่านครั้งนี้เท่านั้น กรุณาส่งให้ลูกค้าแยกจาก PDF</div>
                    @endif
                    <div class="invoice-account-footer">
                        <div class="invoice-portal-link">
                            <span>ลิงก์เข้าใช้งานสำหรับลูกค้า</span>
                            <a href="{{ route('customer.login') }}"><i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>{{ route('customer.login') }}</a>
                        </div>
                    @if ($reportAccessActive)
                        <form method="POST" action="{{ route('invoice.report.reset', $report->token) }}" onsubmit="return confirm('สร้างรหัสผ่านใหม่? รหัสผ่านและ session เดิมจะใช้ไม่ได้');">
                            @csrf
                            <button class="btn btn-outline-warning" type="submit">รีเซ็ตรหัสผ่านลูกค้า</button>
                        </form>
                    @else
                        <div class="alert alert-danger">รายงานหมดอายุหรือถูกยกเลิก ลูกค้าไม่สามารถดาวน์โหลดได้</div>
                    @endif
                    </div>
                @else
                    <div class="invoice-panel-heading">
                        <span class="invoice-panel-icon" aria-hidden="true"><i class="bi bi-person-plus"></i></span>
                        <div><h5>สร้างบัญชีลูกค้า</h5><p>เปิดสิทธิ์ให้ลูกค้าเข้าถึงรายงาน EUDR</p></div>
                    </div>
                    <p>ระบบจะสร้างบัญชีสำหรับ Pack ID นี้ และเปิดสิทธิ์ดาวน์โหลด {{ config('customer_reports.access_days') }} วัน</p>
                    <p class="text-muted small">สร้างรายงานสำหรับลูกค้าก่อนบันทึก PDF เพื่อเปิดใช้ลิงก์หลักฐาน</p>
                    <form method="POST" action="{{ route('invoice.report.create') }}">
                        @csrf
                        <input type="hidden" name="context_token" value="{{ $contextToken }}">
                        <button class="btn btn-primary" type="submit">สร้างรายงานสำหรับลูกค้า</button>
                    </form>
                @endif
            </div>
        @endif

        @if (isset($header))
            <div class="a4-wrapper">
                <div class="a4-page">
                    <div class="report-header" style="text-align: center; margin-bottom: 15px;">
                        <img src="{{ asset('assets/img/logo-hvfilla.png') }}" style="max-width: 250px;">
                        <br>
                        <div
                            style="font-size: 20px; font-weight: bold; border-bottom: 1px solid #000; display: inline-block; margin-top: 5px;">
                            EUDR Data Sheet (EUDR Compliant)</div>
                    </div>

                    <div class="customer-box">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($header['shipment_date'])->format('d/m/Y') }}<br>
                        <strong>Customer Name:</strong> {{ $header['customer'] ?? '' }}<br>
                        <strong>Address:</strong> {{ $header['address'] ?? '' }}<br>
                        <strong>Contact Detail:</strong> {{ $header['province'] ?? '' }} {{ $header['province_zip'] ?? '' }}
                    </div>

                    <table class="info-table">
                        <thead>
                            <tr class="main-header">
                                <th colspan="3">INFORMATION</th>
                            </tr>
                            <tr class="sub-header">
                                <th style="width: 35%;">Items</th>
                                <th style="width: 40%;">Descriptions/Applicable laws</th>
                                <th style="width: 25%;">Document Evidence</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- 1. Shipment --}}
                            <tr class="section-bar">
                                <td colspan="3">Shipment</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Date</td>
                                <td>{{ \Carbon\Carbon::parse($header['shipment_date'])->format('d/m/Y') }}</td>
                                <td rowspan="{{ 5 + count($details) * 3 }}"
                                    style="text-align: center; vertical-align: middle; border-bottom: 1px solid #000;">Ref.
                                    to E-mail</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Invoice No. : </td>
                                <td>{{ $header['invoice_no'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Trade Name. : </td>
                                <td>Rubber Thread</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Species (Scientific Name). : </td>
                                <td>Hevea brasiliensis</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Commodities content. : </td>
                                <td>Rubber Thread</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Harmonized System Code (HS Code). : </td>
                                <td>4007 Vulcanised rubber thread and cord</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Country of harvesting. : </td>
                                <td>Thailand(TH)</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Country of production. : </td>
                                <td>Thailand(TH)</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Packing list No.</td>
                                <td>{{ $header['pack_id'] }}</td>
                            </tr>
                            @foreach ($details as $index => $d)
                                <tr>
                                    <td class="level-2">- Product {{ $index + 1 }}</td>
                                    <td>{{ $d['description'] }}</td>
                                </tr>
                                <tr>
                                    <td class="level-2">- Quantity (Net Weight)</td>
                                    <td>{{ number_format($d['net_weight'], 2) }}</td>
                                </tr>
                                <tr class="border-bottom-row">
                                    <td class="level-2">- Unit</td>
                                    <td>KG</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="level-1">- Net Weight</td>
                                <td>{{ number_format($details->sum('net_weight'), 2) }} KG</td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-1">- Gross Weight</td>
                                <td>{{ number_format($details->sum('gross_weight'), 2) }} KG</td>
                            </tr>
                            {{-- 2. Company --}}
                            <tr class="section-bar">
                                <td colspan="3">Company</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Company Name</td>
                                <td>H.V.FILA Co., LTD</td>
                                <td class="" style="text-align: center;"></td>
                            </tr>
                            <tr class="">
                                <td class="level-1">- Address/td>
                                <td>119/9 Moo 1, Sethakit1 Rd., Tambol Baan-Koh, Amphur Muang, Samutsakorn (Thailand) 74000
                                </td>
                                <td style="text-align: center;"></td>
                            </tr>
                            <tr>
                                <td class="level-1">- Contact Person</td>
                                <td>Sureeporn Temsittichok</td>
                                <td class="" style="text-align: center;">@if (!empty($companyUrls['ccd7458cb2']))<a href="{{ $companyUrls['ccd7458cb2'] }}" target="_blank"
                                        class="link-text">
                                        Appx #{{ $appx++ }} H.V.FILA CO., LTD Business Registration Certificate
                                    </a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Contract details</td>
                                <td>+66 (0) 34 468 441, +66 (0) 34 468 666</td>
                                <td class="" style="text-align: center;"></td>
                            </tr>
                            <tr>
                                <td class="level-1">- Email address</td>
                                <td>info@hvfila.co.th</td>
                                <td class="" style="text-align: center;"></td>
                            </tr>
                            <tr>
                                <td class="level-1">- Company registration/TAX No.</td>
                                <td>0105546013248</td>
                                <td class="" style="text-align: center;"></td>
                            </tr>


                            {{-- 2. Supply Chain --}}
                            <tr class="section-bar">
                                <td colspan="3">Supply Chain</td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-1">- Supply chain mapping</td>
                                <td></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['99beb081ee']))<a href="{{ $companyUrls['99beb081ee'] }}"
                                        target="_blank">Appx #{{ $appx++ }} H.V. FILA Supply Chain Mapping</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>

                                                        {{-- 3. Geodata --}}
                            <tr class="section-bar">
                                <td colspan="3">Geodata and Traceability</td>
                            </tr>
                            @foreach ($details as $d)
                                <tr>
                                    <td class="level-1">- Batch No.</td>
                                    <td>{{ $d['lots'] }}</td>

                                    <td rowspan="4" class="border-bottom-row"
                                        style="text-align: center; vertical-align: middle;">

                                        @if (!empty($d['geojson']) && count($d['geojson']) > 0)
                                            @foreach ($d['geojson'] as $file)
                                                {{-- ใส่ลิงก์ดาวน์โหลดตรงนี้ --}}
                                                @if (!empty($evidenceUrls['lot_file_id:'.$file['id']]))<a href="{{ $evidenceUrls['lot_file_id:'.$file['id']] }}" target="_blank" class="link-text">
                                                    Appx #{{ $appx++ }} GeoJSON file
                                                </a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif<br>
                                            @endforeach
                                        @else
                                            -
                                        @endif

                                    </td>
                                </tr>

                                <tr>
                                    <td class="level-2">- Quantity (KG)</td>
                                    <td>{{ number_format($d['qty'], 2) }} KG</td>
                                </tr>

                                <tr>
                                    <td class="level-2">- Date of production</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($d['date_product'] ?? $header['shipment_date'])->format('d/m/Y') }}
                                    </td>
                                </tr>

                                <tr class="border-bottom-row">
                                    <td class="level-2">- Conversion Factor (CF)</td>
                                    <td>{{ number_format($d['cf_qty'], 2) }}</td>
                                </tr>
                            @endforeach



                            {{-- 4. Deforestation --}}
                            <tr class="section-bar">
                                <td colspan="3">Deforestation – free Verification</td>
                            </tr>
                            <tr>
                                <td class="level-1">- Rubber plantations Demonstration</td>
                                <td>Demonstrated all the plantation plots with forest, conservation areas, etc.</td>
                                <td class="border-bottom-row" style="text-align: center;">@if (!empty($companyUrls['ea226c80bc']))<a href="{{ $companyUrls['ea226c80bc'] }}">Appx
                                        #{{ $appx++ }} -
                                        Rubber plantations
                                        Deforestation – free Demonstration</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif </td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-1">- GISDA & RFD Map</td>
                                <td>Technology for Deforestation – free Demonstration</td>
                                <td style="text-align: center;"><a href="https://change.forest.go.th" target="_blank"
                                        class="link-text">https://change.forest.go.th</a></td>
                            </tr>

                                                       {{-- 5. Legal Compliance --}}
                            <tr class="compliance-bar">
                                <td colspan="3">Compliant with the laws of the country of production (Legal Compliance
                                    Verification)</td>
                            </tr>

                            @php $cocNumber = 1; @endphp

                            @if (!empty($supplierData))
                                @foreach ($supplierData as $supItem)
                                    @php
                                        $sup = $supItem['supplier'];
                                        $docs = $supItem['docs'];
                                    @endphp
                                    <tr>
                                        <td class="level-1"><strong>- Production (Chain of Custody #{{ $cocNumber++ }}): </strong></td>
                                        <td><span class="red-bold">{{ $sup->supplier_name }} ({{ $sup->supplier_code }})</span></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="level-2"><strong>- Operation regulations</strong></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Business registration</td>
                                        <td class="link-text"><a href="https://faolex.fao.org/docs/pdf/tha200357.pdf" target="_blank">In
                                                accordance with Section 1097³ of the Civil Code</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[1])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[1]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[1]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[1]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Factory license</td>
                                        <td class="link-text"><a
                                                href="http://reg3.diw.go.th/legal/wp-content/uploads/2017/05/fac-en.pdf" target="_blank">In
                                                accordance with Section 12 of the Factory Act B.E. 2535 (1992)</a>
                                        </td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[2])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[2]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[2]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[2]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-2"><strong>- Environment regulations</strong></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Wastewater Treatment Plant</td>
                                        <td class="link-text"><a href="https://faolex.fao.org/docs/pdf/tha19415.pdf" target="_blank">In accordance
                                                with Section 70 of the Enhancement and Conservation of
                                                National Environmental Quality Act, B.E. 2535 (1992)</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[3])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[3]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[3]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[3]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Water Pollution</td>
                                        <td class="link-text"><a
                                                href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act" target="_blank">In
                                                accordance with Section 68 Enhancement and Conservation of
                                                National
                                                Environmental Quality Act B.E. 2535</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[4])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[4]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[4]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[4]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Air Pollution</td>
                                        <td class="link-text"><a
                                                href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act" target="_blank">In
                                                accordance with Section 68 Enhancement and Conservation of
                                                National
                                                Environmental Quality Act B.E. 2535</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[5])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[5]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[5]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[5]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-2"><strong>- Labor right regulations</strong></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Employer Registration for Social Security</td>
                                        <td class="link-text"><a
                                                href="https://www.mol.go.th/wp-content/uploads/sites/2/2019/07/social_security_act_2533_sso_1.pdf" target="_blank">In
                                                accordance with Section 36 of the Social
                                                Security Act B.E. 2533"
                                                (1990)</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[6])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[6]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[6]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[6]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Employment and Working Conditions Declaration Form (KR 11)</td>
                                        <td class="link-text"><a
                                                href="https://data.thailand.opendevelopmentmekong.net/th/laws_record/labour-protection-act-b-e-2541-2008-with-updates-as-of-2017/resource/6169fd03-eae9-49b0-920c-b6ca109a9e0a" target="_blank">In
                                                accordance with Section 155/1 of the Labour Protection Act, B.E.
                                                2541 (1998)</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[7])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[7]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[7]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[7]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom-row">
                                        <td class="level-3">- Foreigner Workers</td>
                                        <td class="link-text"><a
                                                href="https://www.doe.go.th/prd/chiangmai/news/param/site/111/cat/7/sub/0/pull/detail/view/detail/object_id/72769" target="_blank">Registration
                                                of Foreign Workers 2023 According to the Cabinet
                                                Resolution of July 5, 2023</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[8])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[8]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[8]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[8]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-2"><strong>- Occupational Health and Safety regulations</strong></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- License for Operating Health Hazardous Activities.</td>
                                        <td class="link-text"><a href="https://faolex.fao.org/docs/pdf/tha209339.pdf"
                                                target="_blank">In accordance with Section 32 of the Public Health Act B.E. 2535
                                                (1992) and Amendments</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[9])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[9]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[9]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[9]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Appointment of the Occupational Health and Safety Committee</td>
                                        <td class="link-text"><a
                                                href="https://www.tosh.or.th/images/file/2020/k2-816.pdf?_t=1602038342"
                                                target="_blank">In accordance with Section 2 of the Ministerial Regulation on
                                                Standards for the Administration and Management of Occupational Safety, Health, and
                                                Environmental Conditions in the Workplace B.E. 2549 (2006)</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[10])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[10]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[10]->id] }}" target="_blank">Appx #{{ $appx++ }} {{ $docs[10]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Fire Fighting and Evacuation</td>
                                        <td class="link-text"><a
                                                href="https://www.ratchakitcha.soc.go.th/DATA/PDF/2556/A/002/24.PDF"
                                                target="_blank">In accordance with Section 30 of the Ministry Regulation on
                                                Standards for the Administration and Management of Occupational Safety, Health, and
                                                Environmental Conditions in the Workplace B.E. 2556 (2013)</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[11])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[11]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[11]->id] }}" target="_blank">Annex #{{ $appx++ }} {{ $docs[11]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom-row">
                                        <td class="level-3">- Hazardous Material Possession License</td>
                                        <td class="link-text"><a
                                                href="https://www.diw.go.th/webdiw/wp-content/uploads/2021/07/law-haz-29032535-eng.pdf"
                                                target="_blank">In accordance with Section 18 of the Hazardous Substance Act B.E.
                                                2535 (1992)</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[12])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[12]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[12]->id] }}" target="_blank">Annex #{{ $appx++ }} {{ $docs[12]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-2"><strong>- Trade, tax and customs regulations</strong></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Calibration of Weighing and Measuring Instruments</td>
                                        <td class="link-text"><a
                                                href="https://law.dit.go.th/Upload/Document/d24f4df6-9cad-4644-871b-a627c881970e.pdf"
                                                target="_blank">In
                                                accordance with the Measurement Act B.E. 2542 (1999)</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[13])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[13]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[13]->id] }}" target="_blank">Annex #{{ $appx++ }} {{ $docs[13]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Certificate of Value Added Tax Registration</td>
                                        <td class="link-text"><a href="https://www.rd.go.th/english/37718.html"
                                                target="_blank">In accordance with Revenue Code Section 4 Value Added Tax</a></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[14])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[14]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[14]->id] }}" target="_blank">Annex #{{ $appx++ }} {{ $docs[14]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom-row">
                                        <td class="level-3">- Natural Rubber Trading License</td>
                                        <td class="link-text"><a
                                                href="https://www.doa.go.th/th/wp-content/uploads/2020/11/%E0%B8%9E%E0%B8%A3%E0%B8%B0%E0%B8%A3%E0%B8%B2%E0%B8%8A%E0%B8%9A%E0%B8%B1%E0%B8%8D%E0%B8%8D%E0%B8%B1%E0%B8%95%E0%B8%B4%E0%B8%84%E0%B8%A7%E0%B8%9A%E0%B8%84%E0%B8%B8%E0%B8%A1%E0%B8%A2%E0%B8%B2%E0%B8%87-%E0%B8%9E.%E0%B8%A8.-2542-%E0%B8%89%E0%B8%9A%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%9B%E0%B8%A5%E0%B9%80%E0%B8%9B%E0%B9%87%E0%B8%99%E0%B8%A0%E0%B8%B2%E0%B8%A9%E0%B8%B2%E0%B8%AD%E0%B8%B1%E0%B8%87%E0%B8%81%E0%B8%A4%E0%B8%A9.pdf"
                                                target="_blank">In accordance with Section 22 the Rubber Control Act B.E. 2542
                                                (1999)</a>
                                        </td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[15])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[15]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[15]->id] }}" target="_blank">Annex #{{ $appx++ }} {{ $docs[15]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-2"><strong>- Legal Compliance at plots level</strong></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="border-bottom-row">
                                        <td class="level-3">- Legal compliance verification for each rubber plantation used for
                                            production</td>
                                        <td class="link-text">The legal complaints at the plot level have been verified, and the
                                            results are described in a GeoJSON file</td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[16])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[16]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[16]->id] }}" target="_blank">Annex #{{ $appx++ }} {{ $docs[16]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-2"><strong>- Others</strong></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- ISO 9001 Certificate</td>
                                        <td class="link-text"></td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[17])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[17]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[17]->id] }}" target="_blank">Annex #{{ $appx++ }} {{ $docs[17]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="level-3">- Forest Certificate</td>
                                        <td class="link-text">FSC Forest Management</td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[18])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[18]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[18]->id] }}" target="_blank">Annex #{{ $appx++ }} {{ $docs[18]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom-row">
                                        <td class="level-3">- Forest Certificate</td>
                                        <td class="link-text">FSC Chain of Custody</td>
                                        <td style="text-align: center;">
                                            @if(isset($docs[19])) @if (!empty($evidenceUrls['supplier_document_id:'.$docs[19]->id]))<a href="{{ $evidenceUrls['supplier_document_id:'.$docs[19]->id] }}" target="_blank">Annex #{{ $appx++ }} {{ $docs[19]->doc_name }}</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif @else - @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif


                            <tr>
                                <td class="level-1"><strong>- Production (Chain of Custody #3): </strong></td>
                                <td><span class="red-bold">H.V. Fila Co.,Ltd </span></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="level-2"><strong>- Operation regulations</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Business registration</td>
                                <td class="link-text"><a href="https://faolex.fao.org/docs/pdf/tha200357.pdf">In
                                        accordance with Section 1097³ of the Civil Code</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['ccd7458cb2']))<a href="{{ $companyUrls['ccd7458cb2'] }}">Annex #{{ $appx++ }}
                                        Business
                                        Registration Certificate</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr>
                                <td class="level-3">- Factory license</td>
                                <td class="link-text"><a
                                        href="http://reg3.diw.go.th/legal/wp-content/uploads/2017/05/fac-en.pdf">In
                                        accordance with Section 12 of the Factory Act B.E. 2535 (1992)</a>
                                </td>
                                <td style="text-align: center;">@if (!empty($companyUrls['53cf4e48cf']))<a href="{{ $companyUrls['53cf4e48cf'] }}">Annex #{{ $appx++ }}
                                        Factory
                                        license</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr>
                                <td class="level-2"><strong>- Environment regulations</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Wastewater Treatment Plant</td>
                                <td class="link-text"><a href="https://faolex.fao.org/docs/pdf/tha19415.pdf">In accordance
                                        with Section 70 of the Enhancement and Conservation of
                                        National Environmental Quality Act, B.E. 2535 (1992)</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['b7e53d08c4']))<a href="{{ $companyUrls['b7e53d08c4'] }}">Annex #{{ $appx++ }}
                                        Wastewater
                                        Treatment Plant</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr>
                                <td class="level-3">- Water Pollution</td>
                                <td class="link-text"><a
                                        href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act">In
                                        accordance with Section 68 Enhancement and Conservation of
                                        National
                                        Environmental Quality Act B.E. 2535</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['a9b90478e3']))<a href="{{ $companyUrls['a9b90478e3'] }}">Annex #{{ $appx++ }}
                                        Water
                                        Pollution Discharge Report (Form Ror Wor 2)</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Air Pollution</td>
                                <td class="link-text"><a
                                        href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act">In
                                        accordance with Section 68 Enhancement and Conservation of
                                        National
                                        Environmental Quality Act B.E. 2535</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['3a356e41a0']))<a href="{{ $companyUrls['3a356e41a0'] }}">Annex #{{ $appx++ }}
                                        Air
                                        Pollution Emission Report (Form Ror Wor 3)</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2"><strong>- Labor right regulations</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Employer Registration for Social Security</td>
                                <td class="link-text"><a
                                        href="https://www.mol.go.th/wp-content/uploads/sites/2/2019/07/social_security_act_2533_sso_1.pdf">In
                                        accordance with Section 36 of the Social Security Act B.E. 2533"
                                        (1990)</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['07d6d1e923']))<a href="{{ $companyUrls['07d6d1e923'] }}">Annex #{{ $appx++ }}
                                        Social
                                        Security Certificate of Registration</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr>
                                <td class="level-3">- Employment and Working Conditions Declaration Form (KR 11)</td>
                                <td class="link-text"><a
                                        href="https://data.thailand.opendevelopmentmekong.net/th/laws_record/labour-protection-act-b-e-2541-2008-with-updates-as-of-2017/resource/6169fd03-eae9-49b0-920c-b6ca109a9e0a">In
                                        accordance with Section 155/1 of the Labour Protection Act, B.E.
                                        2541 (1998)</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['591ce55991']))<a href="{{ $companyUrls['591ce55991'] }}">Annex #{{ $appx++ }}
                                        -
                                        KR 11</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Foreigner Workers</td>
                                <td class="link-text"><a
                                        href="https://www.doe.go.th/prd/chiangmai/news/param/site/111/cat/7/sub/0/pull/detail/view/detail/object_id/72769">Registration
                                        of Foreign Workers 2023 According to the Cabinet
                                        Resolution of July 5, 2023</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['f3ce181606']))<a href="{{ $companyUrls['f3ce181606'] }}">Annex #{{ $appx++ }}
                                        Foreigner
                                        Workers List</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Anti-Bribery and Corruption Policy</td>
                                <td class="link-text"><a
                                        href="https://nacc.go.th/files/article/attachments/main_old_article_20190614144832.pdf?csrt=6294656410101426070"
                                        target="_blank">In accordance with Section 176 of the Organic Act On
                                        Anti-Corruption
                                        B.E.2561 (2018)</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['591ce55991']))<a href="{{ $companyUrls['591ce55991'] }}">Annex #{{ $appx++ }}
                                        Anti-Bribery
                                        and Corruption Policy</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr>
                                <td class="level-2"><strong>- Occupational Health and Safety regulations</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="level-3">- License for Operating Health Hazardous Activities.</td>
                                <td class="link-text"><a href="https://faolex.fao.org/docs/pdf/tha209339.pdf"
                                        target="_blank">In accordance with Section 32 of the Public Health Act B.E. 2535
                                        (1992) and Amendments</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['6aa4254141']))<a href="{{ $companyUrls['6aa4254141'] }}">Annex #{{ $appx++ }}
                                        License for
                                        Operating Health Hazardous Activities.</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Appointment of the Occupational Health and Safety Committee</td>
                                <td class="link-text"><a
                                        href="https://www.tosh.or.th/images/file/2020/k2-816.pdf?_t=1602038342"
                                        target="_blank">In accordance with Section 2 of the Ministerial Regulation on
                                        Standards for the Administration and Management of Occupational Safety, Health, and
                                        Environmental Conditions in the Workplace B.E. 2549 (2006)</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['5436ecd643']))<a href="{{ $companyUrls['5436ecd643'] }}">Annex #{{ $appx++ }}
                                        Safety
                                        Committees</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr>
                                <td class="level-3">- Fire Fighting and Evacuation</td>
                                <td class="link-text"><a
                                        href="https://www.ratchakitcha.soc.go.th/DATA/PDF/2556/A/002/24.PDF"
                                        target="_blank">In accordance with Section 30 of the Ministry Regulation on
                                        Standards for the Administration and Management of Occupational Safety, Health, and
                                        Environmental Conditions in the Workplace B.E. 2556 (2013)</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['198b38e1b7']))<a href="{{ $companyUrls['198b38e1b7'] }}">Annex #{{ $appx++ }}
                                        Firefighting
                                        and Fire Evacuation</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Hazardous Material Possession License</td>
                                <td class="link-text"><a
                                        href="https://www.diw.go.th/webdiw/wp-content/uploads/2021/07/law-haz-29032535-eng.pdf"
                                        target="_blank">In accordance with Section 18 of the Hazardous Substance Act B.E.
                                        2535 (1992)</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['0c66349597']))<a href="{{ $companyUrls['0c66349597'] }}">Annex #{{ $appx++ }}
                                        Hazardous
                                        Material Possession License</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr>
                                <td class="level-2"><strong>- Trade, tax and customs regulations</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Calibration of Weighing and Measuring Instruments</td>
                                <td class="link-text"><a
                                        href="https://law.dit.go.th/Upload/Document/d24f4df6-9cad-4644-871b-a627c881970e.pdf"
                                        target="_blank">In
                                        accordance with the Measurement Act B.E. 2542 (1999)</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['e8a5056e1b']))<a href="{{ $companyUrls['e8a5056e1b'] }}">Annex #{{ $appx++ }}
                                        Calibration
                                        of Weighing and Measuring Instruments</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Certificate of Value Added Tax Registration</td>
                                <td class="link-text"><a href="https://www.rd.go.th/english/37718.html"
                                        target="_blank">In accordance with Revenue Code Section 4 Value Added Tax</a></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['ccd300cfcf']))<a href="{{ $companyUrls['ccd300cfcf'] }}">Annex #{{ $appx++ }}
                                        Certificate
                                        of Value Added Tax Registration</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Natural Rubber Trading License</td>
                                <td class="link-text"><a
                                        href="https://www.doa.go.th/th/wp-content/uploads/2020/11/%E0%B8%9E%E0%B8%A3%E0%B8%B0%E0%B8%A3%E0%B8%B2%E0%B8%8A%E0%B8%9A%E0%B8%B1%E0%B8%8D%E0%B8%8D%E0%B8%B1%E0%B8%95%E0%B8%B4%E0%B8%84%E0%B8%A7%E0%B8%9A%E0%B8%84%E0%B8%B8%E0%B8%A1%E0%B8%A2%E0%B8%B2%E0%B8%87-%E0%B8%9E.%E0%B8%A8.-2542-%E0%B8%89%E0%B8%9A%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%9B%E0%B8%A5%E0%B9%80%E0%B8%9B%E0%B9%87%E0%B8%99%E0%B8%A0%E0%B8%B2%E0%B8%A9%E0%B8%B2%E0%B8%AD%E0%B8%B1%E0%B8%87%E0%B8%81%E0%B8%A4%E0%B8%A9.pdf"
                                        target="_blank">In accordance with Section 22 the Rubber Control Act B.E. 2542
                                        (1999)</a>
                                </td>
                                <td style="text-align: center;">@if (!empty($companyUrls['6d0d3f91b1']))<a href="{{ $companyUrls['6d0d3f91b1'] }}">Annex #{{ $appx++ }}
                                        Natural
                                        Rubber Trading License</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif</td>
                            </tr>
                            <tr>
                                <td class="level-2"><strong>- Legal Compliance at plots level</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Legal compliance verification for each rubber plantation used for
                                    production</td>
                                <td class="link-text">The legal complaints at the plot level have been verified, and the
                                    results are described in a GeoJSON file</td>
                                <td style="text-align: center;">@if (!empty($companyUrls['0cd09dd8f8']))<a href="{{ $companyUrls['0cd09dd8f8'] }}">Annex #{{ $appx++ }}
                                        Legal
                                        Compliance Verification for Rubber Plantations</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            {{-- <tr>
                                <td class="level-1"><strong>- Export</strong></td>
                                <td></td>
                                <td></td>
                            </tr> --}}
                            {{-- <tr class="border-bottom-row">
                                <td class="level-2">- Import and Export License</td>
                                <td class="link-text">In accordance with the Export and Import of Goods Act, B.E. 2522
                                    (1979)</td>
                                <td style="text-align: center;"><a href="#">Annex #{{ $appx++ }} H.V. Fila
                                        Export documentation(ยังไม่มีไฟล์)</a>
                                </td>
                            </tr> --}}
                            <tr>
                                <td class="level-1"><strong>- Others</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="level-2">- ISO 9001 Certificate </td>
                                <td class="link-text"></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['7129af1076']))<a href="{{ $companyUrls['7129af1076'] }}">Annex #{{ $appx++ }}
                                        ISO 9001
                                        Certificate</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- Forest Certificate </td>
                                <td class="link-text">FSC Chain of Custody</td>
                                <td style="text-align: center;">@if (!empty($companyUrls['dad8ea5689']))<a href="{{ $companyUrls['dad8ea5689'] }}">Annex #{{ $appx++ }}
                                        Forest
                                        Certificate</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- OEKO-Tex Certificate</td>
                                <td class="link-text"></td>
                                <td style="text-align: center;">@if (!empty($companyUrls['2877ec728a']))<a href="{{ $companyUrls['2877ec728a'] }}">Annex #{{ $appx++ }}
                                        OEKO-Tex
                                        Certificate</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- DDS Summary</td>
                                <td class="link-text">Due Diligence System</td>
                                <td style="text-align: center;">@if (!empty($companyUrls['5620561ea7']))<a href="{{ $companyUrls['5620561ea7'] }}">Annex #{{ $appx++ }}
                                        Due
                                        Diligence System</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- EUDR Supplier Audit Report</td>
                                <td class="link-text">A third party conducted an audit of the company’s EUDR procedures and
                                    their implementation across the entire supply chain.</td>
                                <td style="text-align: center;">@if (!empty($companyUrls['c8796fc093']))<a href="{{ $companyUrls['c8796fc093'] }}">Annex #{{ $appx++ }}
                                        EUDR
                                        Supplier Audit Report</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- PDPA</td>
                                <td class="link-text"><a
                                        href="https://data.thailand.opendevelopmentmekong.net/en/laws_record/2562?utm_source=chatgpt.com">In
                                        accordance with Personal Data Protection Act B.E.2562 (2019</a>)</td>
                                <td style="text-align: center;">@if (!empty($companyUrls['3965136a86']))<a href="{{ $companyUrls['3965136a86'] }}">Annex #{{ $appx++ }}
                                        PDPA</a>@else <span class="text-muted">ยังไม่เปิดใช้หลักฐาน</span> @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- Indigenous people</td>
                                <td class="link-text"></td>
                                <td style="text-align: center;"><a
                                        href="https://www.thaiipportal.info/geo-informatics">https://www.thaiipportal.info/geo-informatics</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- Complaints mechanism</td>
                                <td class="link-text">Complaint submission channels related to the EUDR system</td>
                                <td style="text-align: center;">Line : @095wvoyb /
                                    E-mail : info@hvfila.co.th

                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- ส่วนท้ายเอกสาร (Footer Section) --}}
                    <div
                        style="font-size: 11px; margin-top: 15px; border-top: 1px solid #000; padding-top: 8px; line-height: 1.4;">
                        <div style="margin-bottom: 8px;">
                            <strong>Complaints mechanism:</strong> Complaint submission channels related to the EUDR system:
                            Line: @095wvoyb | E-mail: info@hvfila.co.th
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <strong>[1] Article 9: Information Requirements</strong><br>
                                (e) the name, postal address an email of any business or person from they have been
                                supplied.<br>
                                (1) (a) a description (b) the quantities, and (c) the country of production.<br>
                                (d) the geolocation of all plots of land where the relevant commodities that the relevant
                                product contains.
                            </div>
                            <div>
                                <strong>[1] Annex I:</strong> Relevant commodities and relevant products as referred to in
                                Article 1<br>
                                <strong>[1] FSC Chain of Custody Certification:</strong> FSC-STD-40-004 V3-1 EN<br>
                                <strong>[1] FSC Chain of Custody Certification:</strong> FSC-STD-40-004 V3-1 EN
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        @endif

    </main>

    <script>
        document.getElementById('searchForm')?.addEventListener('submit', function() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        });
    </script>



    @unless ($customerPortal)
        @include('layouts.footer')
    @endunless
@endsection
