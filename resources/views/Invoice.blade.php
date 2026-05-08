@section('title', 'HVF | EUDR Invoice')
@extends('layouts.master')

@section('content')
    @include('layouts.header')
    @include('layouts.sidebar')

    <style>
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

    <main id="main" class="main">
        <div class="container-fluid no-print">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">

                    <form method="POST" action="{{ route('invoice.search') }}" class="card border-0 shadow rounded-4 p-4">

                        @csrf

                        <h5 class="fw-bold mb-3 text-center">
                            🔍 ค้นหา Invoice
                        </h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Pack ID
                            </label>
                            <input type="text" name="pack_id" class="form-control form-control-lg rounded-3"
                                placeholder="กรอก Pack ID..." value="{{ $packId ?? '' }}" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3">
                                ค้นหา
                            </button>
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

        @php
            $appx = 1;
        @endphp

        @if (isset($header))
            <div class="a4-wrapper">
                <div class="a4-page">
                    <div class="report-header" style="text-align: center; margin-bottom: 15px;">
                        <img src="../assets/img/logo-hvfilla.png" style="max-width: 250px;">
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
                                <td class="" style="text-align: center;"><a
                                        href="{{ url('company_docs/download/ccd7458cb2') }}" target="_blank"
                                        class="link-text">
                                        Appx #{{ $appx++ }} H.V.FILA CO., LTD Business Registration Certificate
                                    </a></td>
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
                                <td style="text-align: center;"><a href="{{ url('company_docs/download/99beb081ee') }}"
                                        target="_blank">Appx #{{ $appx++ }} H.V. FILA Supply Chain Mapping</a>
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
                                                <a href="{{ route('geojson.download', ['filename' => $file]) }}"
                                                    class="link-text" target="_self"> {{-- ใน PDF ใช้ _self จะเสถียรกว่า --}}
                                                    Appx #{{ $appx++ }} GeoJSON file
                                                </a>
                                                <br>
                                            @endforeach
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
                                <td class="border-bottom-row" style="text-align: center;"><a
                                        href="{{ url('company_docs/download/ea226c80bc') }}">Appx
                                        #{{ $appx++ }} -
                                        Rubber plantations
                                        Deforestation – free Demonstration</a> </td>
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
                            <tr>
                                <td class="level-1"><strong>- Production (Chain of Custody #1): </strong></td>
                                <td><span class="red-bold">Non FSC (Company
                                        A)</span></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/ccd7458cb2') }}">Appx #{{ $appx++ }}
                                        Business
                                        Registration Certificate</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Factory license</td>
                                <td class="link-text"><a
                                        href="http://reg3.diw.go.th/legal/wp-content/uploads/2017/05/fac-en.pdf">In
                                        accordance with Section 12 of the Factory Act B.E. 2535 (1992)</a>
                                </td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/53cf4e48cf') }}">Appx #{{ $appx++ }}
                                        Factory
                                        license</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/b7e53d08c4') }}">Appx #{{ $appx++ }}
                                        Wastewater
                                        Treatment Plant</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Water Pollution</td>
                                <td class="link-text"><a
                                        href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act">In
                                        accordance with Section 68 Enhancement and Conservation of
                                        National
                                        Environmental Quality Act B.E. 2535</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/a9b90478e3') }}">Appx #{{ $appx++ }}
                                        Water
                                        Pollution Discharge Report (Form Ror Wor 2)</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Air Pollution</td>
                                <td class="link-text"><a
                                        href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act">In
                                        accordance with Section 68 Enhancement and Conservation of
                                        National
                                        Environmental Quality Act B.E. 2535</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/3a356e41a0') }}">Appx #{{ $appx++ }}
                                        Air Pollution
                                        Emission Report (Form Ror Wor 3)</a>
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
                                        accordance with Section 36 of the Social
                                        Security Act B.E. 2533"
                                        (1990)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/07d6d1e923') }}">Appx #{{ $appx++ }}
                                        Social
                                        Security Certificate of Registration</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Employment and Working Conditions Declaration Form (KR 11)</td>
                                <td class="link-text"><a
                                        href="https://data.thailand.opendevelopmentmekong.net/th/laws_record/labour-protection-act-b-e-2541-2008-with-updates-as-of-2017/resource/6169fd03-eae9-49b0-920c-b6ca109a9e0a">In
                                        accordance with Section 155/1 of the Labour Protection Act, B.E.
                                        2541 (1998)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/591ce55991') }}">Appx #{{ $appx++ }} -
                                        KR 11</a>
                                </td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Foreigner Workers</td>
                                <td class="link-text"><a
                                        href="https://www.doe.go.th/prd/chiangmai/news/param/site/111/cat/7/sub/0/pull/detail/view/detail/object_id/72769">Registration
                                        of Foreign Workers 2023 According to the Cabinet
                                        Resolution of July 5, 2023</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/f3ce181606') }}">Appx #{{ $appx++ }}
                                        Foreigner
                                        Workers List</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/6aa4254141') }}">Appx #{{ $appx++ }}
                                        License for
                                        Operating Health Hazardous Activities.</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Appointment of the Occupational Health and Safety Committee</td>
                                <td class="link-text"><a
                                        href="https://www.tosh.or.th/images/file/2020/k2-816.pdf?_t=1602038342"
                                        target="_blank">In accordance with Section 2 of the Ministerial Regulation on
                                        Standards for the Administration and Management of Occupational Safety, Health, and
                                        Environmental Conditions in the Workplace B.E. 2549 (2006)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/5436ecd643') }}">Appx #{{ $appx++ }}
                                        Safety
                                        Committees</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Fire Fighting and Evacuation</td>
                                <td class="link-text"><a
                                        href="https://www.ratchakitcha.soc.go.th/DATA/PDF/2556/A/002/24.PDF"
                                        target="_blank">In accordance with Section 30 of the Ministry Regulation on
                                        Standards for the Administration and Management of Occupational Safety, Health, and
                                        Environmental Conditions in the Workplace B.E. 2556 (2013)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/198b38e1b7') }}">Annex #{{ $appx++ }}
                                        Firefighting
                                        and Fire Evacuation</a></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Hazardous Material Possession License</td>
                                <td class="link-text"><a
                                        href="https://www.diw.go.th/webdiw/wp-content/uploads/2021/07/law-haz-29032535-eng.pdf"
                                        target="_blank">In accordance with Section 18 of the Hazardous Substance Act B.E.
                                        2535 (1992)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/0c66349597') }}">Annex #{{ $appx++ }}
                                        Hazardous
                                        Material Possession License</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/e8a5056e1b') }}">Annex #{{ $appx++ }}
                                        Calibration
                                        of Weighing and Measuring Instruments</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Certificate of Value Added Tax Registration</td>
                                <td class="link-text"><a href="https://www.rd.go.th/english/37718.html"
                                        target="_blank">In accordance with Revenue Code Section 4 Value Added Tax</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/ccd300cfcf') }}">Annex #{{ $appx++ }}
                                        Certificate
                                        of Value Added Tax Registration</a></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Natural Rubber Trading License</td>
                                <td class="link-text"><a
                                        href="https://www.doa.go.th/th/wp-content/uploads/2020/11/%E0%B8%9E%E0%B8%A3%E0%B8%B0%E0%B8%A3%E0%B8%B2%E0%B8%8A%E0%B8%9A%E0%B8%B1%E0%B8%8D%E0%B8%8D%E0%B8%B1%E0%B8%95%E0%B8%B4%E0%B8%84%E0%B8%A7%E0%B8%9A%E0%B8%84%E0%B8%B8%E0%B8%A1%E0%B8%A2%E0%B8%B2%E0%B8%87-%E0%B8%9E.%E0%B8%A8.-2542-%E0%B8%89%E0%B8%9A%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%9B%E0%B8%A5%E0%B9%80%E0%B8%9B%E0%B9%87%E0%B8%99%E0%B8%A0%E0%B8%B2%E0%B8%A9%E0%B8%B2%E0%B8%AD%E0%B8%B1%E0%B8%87%E0%B8%81%E0%B8%A4%E0%B8%A9.pdf"
                                        target="_blank">In accordance with Section 22 the Rubber Control Act B.E. 2542
                                        (1999)</a>
                                </td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/6d0d3f91b1') }}">Annex #{{ $appx++ }}
                                        Natural
                                        Rubber Trading License</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/0cd09dd8f8') }}">Annex #{{ $appx++ }}
                                        Legal
                                        Compliance Verification for Rubber Plantations</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2"><strong>- Others</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- ISO 9001 Certificate</td>
                                <td class="link-text"></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/7129af1076') }}">Annex #{{ $appx++ }}
                                        ISO 9001
                                        Certificate</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-1"><strong>- Production (Chain of Custody #2): </strong></td>
                                <td><span class="red-bold">FSC (Company B) </span></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/ccd7458cb2') }}">Annex #{{ $appx++ }}
                                        Business
                                        Registration Certificate</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Factory license</td>
                                <td class="link-text"><a
                                        href="http://reg3.diw.go.th/legal/wp-content/uploads/2017/05/fac-en.pdf">In
                                        accordance with Section 12 of the Factory Act B.E. 2535 (1992)</a>
                                </td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/53cf4e48cf') }}">Annex #{{ $appx++ }}
                                        Factory
                                        license</a></td>
                            </tr>
                            <tr>
                                <td class="level-2"><strong>- Environment regulations</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Wastewater Treatment Plant</td>
                                <td class="link-text"><a href="https://faolex.fao.org/docs/pdf/tha19415.pdf">accordance
                                        with Section 70 of the Enhancement and Conservation of
                                        National Environmental Quality Act, B.E. 2535 (1992)</a>In
                                </td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/b7e53d08c4') }}">Annex #{{ $appx++ }}
                                        Wastewater
                                        Treatment Plant</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Water Pollution</td>
                                <td class="link-text"><a
                                        href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act">In
                                        accordance with Section 68 Enhancement and Conservation of
                                        National
                                        Environmental Quality Act B.E. 2535</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/a9b90478e3') }}">Annex #{{ $appx++ }}
                                        Water
                                        Pollution Discharge Report (Form Ror Wor 2)</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Air Pollution</td>
                                <td class="link-text"><a
                                        href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act">In
                                        accordance with Section 68 Enhancement and Conservation of
                                        National
                                        Environmental Quality Act B.E. 2535</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/3a356e41a0') }}">Annex #{{ $appx++ }}
                                        Air
                                        Pollution Emission Report (Form Ror Wor 3)</a>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/07d6d1e923') }}">Annex #{{ $appx++ }}
                                        Social
                                        Security Certificate of Registration</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Employment and Working Conditions Declaration Form (KR 11)</td>
                                <td class="link-text"><a
                                        href="https://data.thailand.opendevelopmentmekong.net/th/laws_record/labour-protection-act-b-e-2541-2008-with-updates-as-of-2017/resource/6169fd03-eae9-49b0-920c-b6ca109a9e0a">In
                                        accordance with Section 155/1 of the Labour Protection Act, B.E.
                                        2541 (1998)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/591ce55991') }}">Annex #{{ $appx++ }} -
                                        KR 11</a>
                                </td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Foreigner Workers</td>
                                <td class="link-text"><a
                                        href="https://www.doe.go.th/prd/chiangmai/news/param/site/111/cat/7/sub/0/pull/detail/view/detail/object_id/72769">Registration
                                        of Foreign Workers 2023 According to the Cabinet
                                        Resolution of July 5, 2023</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/f3ce181606') }}">Annex #{{ $appx++ }}
                                        Foreigner
                                        Workers List</a></td>
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
                                        (1992) and Amendments</td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/6aa4254141') }}">Annex #{{ $appx++ }}
                                        License for
                                        Operating Health Hazardous Activities.</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Appointment of the Occupational Health and Safety Committee</td>
                                <td class="link-text"><a
                                        href="https://www.tosh.or.th/images/file/2020/k2-816.pdf?_t=1602038342"
                                        target="_blank">In accordance with Section 2 of the Ministerial Regulation on
                                        Standards for the Administration and Management of Occupational Safety, Health, and
                                        Environmental Conditions in the Workplace B.E. 2549 (2006)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/5436ecd643') }}">Annex #{{ $appx++ }}
                                        Safety
                                        Committees</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Fire Fighting and Evacuation</td>
                                <td class="link-text"><a
                                        href="https://www.ratchakitcha.soc.go.th/DATA/PDF/2556/A/002/24.PDF"
                                        target="_blank">In accordance with Section 30 of the Ministry Regulation on
                                        Standards for the Administration and Management of Occupational Safety, Health, and
                                        Environmental Conditions in the Workplace B.E. 2556 (2013)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/198b38e1b7') }}">Annex #{{ $appx++ }}
                                        Firefighting
                                        and Fire Evacuation</a></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Hazardous Material Possession License</td>
                                <td class="link-text"><a
                                        href="https://www.diw.go.th/webdiw/wp-content/uploads/2021/07/law-haz-29032535-eng.pdf"
                                        target="_blank">In accordance with Section 18 of the Hazardous Substance Act B.E.
                                        2535 (1992)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/0c66349597') }}">Annex #{{ $appx++ }}
                                        Hazardous
                                        Material Possession License</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/e8a5056e1b') }}">Annex #{{ $appx++ }}
                                        Calibration
                                        of Weighing and Measuring Instruments</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Certificate of Value Added Tax Registration</td>
                                <td class="link-text"><a href="https://www.rd.go.th/english/37718.html"
                                        target="_blank">In accordance with Revenue Code Section 4 Value Added Tax</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/ccd300cfcf') }}">Annex #{{ $appx++ }}
                                        Certificate
                                        of Value Added Tax Registration</a></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Natural Rubber Trading License</td>
                                <td class="link-text"><a
                                        href="https://www.doa.go.th/th/wp-content/uploads/2020/11/%E0%B8%9E%E0%B8%A3%E0%B8%B0%E0%B8%A3%E0%B8%B2%E0%B8%8A%E0%B8%9A%E0%B8%B1%E0%B8%8D%E0%B8%8D%E0%B8%B1%E0%B8%95%E0%B8%B4%E0%B8%84%E0%B8%A7%E0%B8%9A%E0%B8%84%E0%B8%B8%E0%B8%A1%E0%B8%A2%E0%B8%B2%E0%B8%87-%E0%B8%9E.%E0%B8%A8.-2542-%E0%B8%89%E0%B8%9A%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%9B%E0%B8%A5%E0%B9%80%E0%B8%9B%E0%B9%87%E0%B8%99%E0%B8%A0%E0%B8%B2%E0%B8%A9%E0%B8%B2%E0%B8%AD%E0%B8%B1%E0%B8%87%E0%B8%81%E0%B8%A4%E0%B8%A9.pdf"
                                        target="_blank">In accordance with Section 22 the Rubber Control Act B.E. 2542
                                        (1999)</a>
                                </td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/6d0d3f91b1') }}">Annex #{{ $appx++ }}
                                        Natural
                                        Rubber Trading License</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/0cd09dd8f8') }}">Annex #{{ $appx++ }}
                                        Legal
                                        Compliance Verification for Rubber Plantations</a>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/7129af1076') }}">Annex #{{ $appx++ }}
                                        ISO 9001
                                        Certificate</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Forest Certificate</td>
                                <td class="link-text">FSC Forest Management</td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/dad8ea5689') }}">Annex #{{ $appx++ }}
                                        Forest
                                        Certificate FM</a></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Forest Certificate</td>
                                <td class="link-text">FSC Chain of Custody</td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/dad8ea5689') }}">Annex #{{ $appx++ }}
                                        Forest
                                        Certificate COC</a></td>
                            </tr>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/ccd7458cb2') }}">Annex #{{ $appx++ }}
                                        Business
                                        Registration Certificate</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Factory license</td>
                                <td class="link-text"><a
                                        href="http://reg3.diw.go.th/legal/wp-content/uploads/2017/05/fac-en.pdf">In
                                        accordance with Section 12 of the Factory Act B.E. 2535 (1992)</a>
                                </td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/53cf4e48cf') }}">Annex #{{ $appx++ }}
                                        Factory
                                        license</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/b7e53d08c4') }}">Annex #{{ $appx++ }}
                                        Wastewater
                                        Treatment Plant</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Water Pollution</td>
                                <td class="link-text"><a
                                        href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act">In
                                        accordance with Section 68 Enhancement and Conservation of
                                        National
                                        Environmental Quality Act B.E. 2535</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/a9b90478e3') }}">Annex #{{ $appx++ }}
                                        Water
                                        Pollution Discharge Report (Form Ror Wor 2)</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Air Pollution</td>
                                <td class="link-text"><a
                                        href="https://data.opendevelopmentmekong.net/th/laws_record/enhancement-and-conservation-of-national-environmental-quality-act">In
                                        accordance with Section 68 Enhancement and Conservation of
                                        National
                                        Environmental Quality Act B.E. 2535</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/3a356e41a0') }}">Annex #{{ $appx++ }}
                                        Air
                                        Pollution Emission Report (Form Ror Wor 3)</a>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/07d6d1e923') }}">Annex #{{ $appx++ }}
                                        Social
                                        Security Certificate of Registration</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Employment and Working Conditions Declaration Form (KR 11)</td>
                                <td class="link-text"><a
                                        href="https://data.thailand.opendevelopmentmekong.net/th/laws_record/labour-protection-act-b-e-2541-2008-with-updates-as-of-2017/resource/6169fd03-eae9-49b0-920c-b6ca109a9e0a">In
                                        accordance with Section 155/1 of the Labour Protection Act, B.E.
                                        2541 (1998)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/591ce55991') }}">Annex #{{ $appx++ }}
                                        -
                                        KR 11</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Foreigner Workers</td>
                                <td class="link-text"><a
                                        href="https://www.doe.go.th/prd/chiangmai/news/param/site/111/cat/7/sub/0/pull/detail/view/detail/object_id/72769">Registration
                                        of Foreign Workers 2023 According to the Cabinet
                                        Resolution of July 5, 2023</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/f3ce181606') }}">Annex #{{ $appx++ }}
                                        Foreigner
                                        Workers List</a></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Anti-Bribery and Corruption Policy</td>
                                <td class="link-text"><a
                                        href="https://nacc.go.th/files/article/attachments/main_old_article_20190614144832.pdf?csrt=6294656410101426070"
                                        target="_blank">In accordance with Section 176 of the Organic Act On
                                        Anti-Corruption
                                        B.E.2561 (2018)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/591ce55991') }}">Annex #{{ $appx++ }}
                                        Anti-Bribery
                                        and Corruption Policy</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/6aa4254141') }}">Annex #{{ $appx++ }}
                                        License for
                                        Operating Health Hazardous Activities.</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Appointment of the Occupational Health and Safety Committee</td>
                                <td class="link-text"><a
                                        href="https://www.tosh.or.th/images/file/2020/k2-816.pdf?_t=1602038342"
                                        target="_blank">In accordance with Section 2 of the Ministerial Regulation on
                                        Standards for the Administration and Management of Occupational Safety, Health, and
                                        Environmental Conditions in the Workplace B.E. 2549 (2006)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/5436ecd643') }}">Annex #{{ $appx++ }}
                                        Safety
                                        Committees</a></td>
                            </tr>
                            <tr>
                                <td class="level-3">- Fire Fighting and Evacuation</td>
                                <td class="link-text"><a
                                        href="https://www.ratchakitcha.soc.go.th/DATA/PDF/2556/A/002/24.PDF"
                                        target="_blank">In accordance with Section 30 of the Ministry Regulation on
                                        Standards for the Administration and Management of Occupational Safety, Health, and
                                        Environmental Conditions in the Workplace B.E. 2556 (2013)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/198b38e1b7') }}">Annex #{{ $appx++ }}
                                        Firefighting
                                        and Fire Evacuation</a></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Hazardous Material Possession License</td>
                                <td class="link-text"><a
                                        href="https://www.diw.go.th/webdiw/wp-content/uploads/2021/07/law-haz-29032535-eng.pdf"
                                        target="_blank">In accordance with Section 18 of the Hazardous Substance Act B.E.
                                        2535 (1992)</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/0c66349597') }}">Annex #{{ $appx++ }}
                                        Hazardous
                                        Material Possession License</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/e8a5056e1b') }}">Annex #{{ $appx++ }}
                                        Calibration
                                        of Weighing and Measuring Instruments</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-3">- Certificate of Value Added Tax Registration</td>
                                <td class="link-text"><a href="https://www.rd.go.th/english/37718.html"
                                        target="_blank">In accordance with Revenue Code Section 4 Value Added Tax</a></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/ccd300cfcf') }}">Annex #{{ $appx++ }}
                                        Certificate
                                        of Value Added Tax Registration</a></td>
                            </tr>
                            <tr class="border-bottom-row">
                                <td class="level-3">- Natural Rubber Trading License</td>
                                <td class="link-text"><a
                                        href="https://www.doa.go.th/th/wp-content/uploads/2020/11/%E0%B8%9E%E0%B8%A3%E0%B8%B0%E0%B8%A3%E0%B8%B2%E0%B8%8A%E0%B8%9A%E0%B8%B1%E0%B8%8D%E0%B8%8D%E0%B8%B1%E0%B8%95%E0%B8%B4%E0%B8%84%E0%B8%A7%E0%B8%9A%E0%B8%84%E0%B8%B8%E0%B8%A1%E0%B8%A2%E0%B8%B2%E0%B8%87-%E0%B8%9E.%E0%B8%A8.-2542-%E0%B8%89%E0%B8%9A%E0%B8%B1%E0%B8%9A%E0%B9%81%E0%B8%9B%E0%B8%A5%E0%B9%80%E0%B8%9B%E0%B9%87%E0%B8%99%E0%B8%A0%E0%B8%B2%E0%B8%A9%E0%B8%B2%E0%B8%AD%E0%B8%B1%E0%B8%87%E0%B8%81%E0%B8%A4%E0%B8%A9.pdf"
                                        target="_blank">In accordance with Section 22 the Rubber Control Act B.E. 2542
                                        (1999)</a>
                                </td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/6d0d3f91b1') }}">Annex #{{ $appx++ }}
                                        Natural
                                        Rubber Trading License</a></td>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/0cd09dd8f8') }}">Annex #{{ $appx++ }}
                                        Legal
                                        Compliance Verification for Rubber Plantations</a>
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
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/7129af1076') }}">Annex #{{ $appx++ }}
                                        ISO 9001
                                        Certificate</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- Forest Certificate </td>
                                <td class="link-text">FSC Chain of Custody</td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/dad8ea5689') }}">Annex #{{ $appx++ }}
                                        Forest
                                        Certificate</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- OEKO-Tex Certificate</td>
                                <td class="link-text"></td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/2877ec728a') }}">Annex #{{ $appx++ }}
                                        OEKO-Tex
                                        Certificate</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- DDS Summary</td>
                                <td class="link-text">Due Diligence System</td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/5620561ea7') }}">Annex #{{ $appx++ }}
                                        Due
                                        Diligence System</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- EUDR Supplier Audit Report</td>
                                <td class="link-text">A third party conducted an audit of the company’s EUDR procedures and
                                    their implementation across the entire supply chain.</td>
                                <td style="text-align: center;"><a href="#">Annex #{{ $appx++ }} EUDR
                                        Supplier Audit Report(ยังไม่มีไฟล์)</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="level-2">- PDPA</td>
                                <td class="link-text"><a
                                        href="https://data.thailand.opendevelopmentmekong.net/en/laws_record/2562?utm_source=chatgpt.com">In
                                        accordance with Personal Data Protection Act B.E.2562 (2019</a>)</td>
                                <td style="text-align: center;"><a
                                        href="{{ url('company_docs/download/3965136a86') }}">Annex #{{ $appx++ }}
                                        PDPA</a>
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

            <div class="text-center mt-3 no-print pb-5">
                <button onclick="window.print()" class="btn btn-success btn-lg">Print or PDF (A4)</button>
            </div>
        @endif

    </main>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        });
    </script>



    @include('layouts.footer')
@endsection
