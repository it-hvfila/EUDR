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
        font-size: 15px;
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
        font-size: 14px;
        word-wrap: break-word;
        border-bottom: none;
    }

    .main-header {
        background: #2d5a27 !important;
        color: #fff;
        text-align: center;
        font-weight: bold;
        border-bottom: 1px solid #000 !important;
        font-size: 18px;
    }

    .sub-header {
        background: #d9d9d9 !important;
        color: #000;
        text-align: center;
        font-weight: bold;
        border-bottom: 1px solid #000 !important;
    }

    .section-bar {
        background: #1a4d5e !important;
        color: #fff;
        font-weight: bold;
        font-size: 16px;
        padding: 4px 10px;
        border-bottom: 1px solid #000 !important;
    }

    .compliance-bar {
        background: #aad0e6 !important;
        color: #000;
        font-weight: bold;
        font-size: 16px;
        padding: 4px 10px;
        border-bottom: 1px solid #000 !important;
    }

    /* ระบบ Indent ด้วยเครื่องหมายขีด (-) */
    .level-1 {
        padding-left: 15px !important;
    }

    /* หัวข้อหลักที่มีขีดเดียว */
    .level-2 {
        padding-left: 35px !important;
    }

    /* หัวข้อย่อยที่มีขีดสองอัน หรือขีดเยื้อง */
    .level-3 {
        padding-left: 55px !important;
    }

    /* รายละเอียดลึกสุด */

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
        .no-print {
            display: none !important;
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
    }
</style>

<main id="main" class="main">
    <div class="container-fluid no-print">
        {{-- Search Form --}}
        <form method="POST" action="{{ route('invoice.search') }}" class="card p-3 mb-4 shadow-sm">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Pack ID</label>
                    <input type="text" name="pack_id" class="form-control" value="{{ $packId ?? '' }}" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">ค้นหา</button>
                </div>
            </div>
        </form>
    </div>

    @if (isset($header))
    <div class="a4-wrapper">
        <div class="a4-page">
            <div class="report-header" style="text-align: center; margin-bottom: 15px;">
                <img src="{{ asset('assets/img/logo-h-v-fila.png') }}" style="max-width: 90px;">
                <div style="color: #2d5a27; font-style: italic; font-size: 14px;">“High Quality Mind Service”</div>
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
                        <td class="level-1">- Invoice No. ( {{ $header['invoice_no'] }} )</td>
                        <td></td>
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

                    {{-- 2. Supply Chain --}}
                    <tr class="section-bar">
                        <td colspan="3">Supply Chain</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-1">- Supply chain mapping</td>
                        <td></td>
                        <td style="text-align: center;">Appx #2 H.V. FILA Supply Chain Mapping</td>
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
                            <a href="{{ asset('storage/' . $file) }}" target="_blank" download>
                                Appx #3 GeoJSON file
                            </a>
                            <br>
                            @endforeach
                            @else
                            <span style="color:red;">No GeoJSON</span>
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
                        <td>(CF = output / input)</td>
                    </tr>
                    @endforeach


                    {{-- 4. Deforestation --}}
                    <tr class="section-bar">
                        <td colspan="3">Deforestation – free Verification</td>
                    </tr>
                    <tr>
                        <td class="level-1">- Rubber plantations Demonstration</td>
                        <td>Demonstrated all the plantation plots with forest, conservation areas, etc.</td>
                        <td class="border-bottom-row" style="text-align: center;">Appx #5 - Rubber plantations
                            Deforestation – free Demonstration </td>
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
                        <td class="link-text">In accordance with Section 1097³ of the Civil Code</td>
                        <td style="text-align: center;">Appx #6 Business Registration Certificate</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Factory license</td>
                        <td class="link-text">In accordance with Section 12 of the Factory Act B.E. 2535 (1992)</td>
                        <td style="text-align: center;">Appx #7 Factory license</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Environment regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- Wastewater Treatment Plant</td>
                        <td class="link-text">In accordance with Section 70 of the Enhancement and Conservation of
                            National Environmental Quality Act, B.E. 2535 (1992)</td>
                        <td style="text-align: center;">Appx #8 Wastewater Treatment Plant</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Water Pollution</td>
                        <td class="link-text">In accordance with Section 68 Enhancement and Conservation of National
                            Environmental Quality Act B.E. 2535</td>
                        <td style="text-align: center;">Appx #9 Water Pollution Discharge Report (Form Ror Wor 2)
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Air Pollution</td>
                        <td class="link-text">In accordance with Section 68 Enhancement and Conservation of National
                            Environmental Quality Act B.E. 2535</td>
                        <td style="text-align: center;">Appx #10 Air Pollution Emission Report (Form Ror Wor 3)</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Labor right regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- Employer Registration for Social Security</td>
                        <td class="link-text">In accordance with Section 36 of the Social Security Act B.E. 2533"
                            (1990)</td>
                        <td style="text-align: center;">Appx #11 Social Security Certificate of Registration</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Employment and Working Conditions Declaration Form (KR 11)</td>
                        <td class="link-text">In accordance with Section 155/1 of the Labour Protection Act, B.E.
                            2541 (1998)</td>
                        <td style="text-align: center;">Appx #12 - KR 11</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Foreigner Workers</td>
                        <td class="link-text">Registration of Foreign Workers 2023 According to the Cabinet
                            Resolution of July 5, 2023</td>
                        <td style="text-align: center;">Appx #13 Foreigner Workers List</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Occupational Health and Safety regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- License for Operating Health Hazardous Activities.</td>
                        <td class="link-text">In accordance with Section 32 of the Public Health Act B.E. 2535
                            (1992) and Amendments</td>
                        <td style="text-align: center;">Appx #14 License for Operating Health Hazardous Activities.
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Appointment of the Occupational Health and Safety Committee</td>
                        <td class="link-text">In accordance with Section 2 of the Ministerial Regulation on
                            Standards for the Administration and Management of Occupational Safety, Health, and
                            Environmental Conditions in the Workplace B.E. 2549 (2006)</td>
                        <td style="text-align: center;">Appx #15 Safety Committees</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Fire Fighting and Evacuation</td>
                        <td class="link-text">The Ministry Regulation sets forth standards for the management and
                            implementation of occupational safety, health, and environmental conditions in the
                            workplace concerning fire prevention and suppression, as outlined in Section 30 of the
                            regulation</td>
                        <td style="text-align: center;">Annex #16 Firefighting and Fire Evacuation</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Hazardous Material Possession License</td>
                        <td class="link-text">In accordance with Section 18 of the Hazardous Substance Act B.E.
                            2535 (1992)</td>
                        <td style="text-align: center;">Annex #17 Hazardous Material Possession License</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Trade, tax and customs regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- Calibration of Weighing and Measuring Instruments</td>
                        <td class="link-text">In accordance with the Measurement Act B.E. 2542 (1999)</td>
                        <td style="text-align: center;">Annex #18 Calibration of Weighing and Measuring Instruments
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Certificate of Value Added Tax Registration</td>
                        <td class="link-text">In accordance with Revenue Code Section 4 Value Added Tax</td>
                        <td style="text-align: center;">Annex #19 Certificate of Value Added Tax Registration</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Natural Rubber Trading License</td>
                        <td class="link-text">In accordance with Section 22 the Rubber Control Act B.E. 2542 (1999)
                        </td>
                        <td style="text-align: center;">Annex #20 Natural Rubber Trading License</td>
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
                        <td style="text-align: center;">Annex #21 Natural Rubber Trading License
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
                        <td style="text-align: center;">Annex #22 Natural Rubber Trading License
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
                        <td class="link-text">In accordance with Section 1097³ of the Civil Code</td>
                        <td style="text-align: center;">Appx #23 Business Registration Certificate</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Factory license</td>
                        <td class="link-text">In accordance with Section 12 of the Factory Act B.E. 2535 (1992)
                        </td>
                        <td style="text-align: center;">Appx #24 Factory license</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Environment regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- Wastewater Treatment Plant</td>
                        <td class="link-text">In accordance with Section 70 of the Enhancement and Conservation of
                            National Environmental Quality Act, B.E. 2535 (1992)</td>
                        <td style="text-align: center;">Appx #25 Wastewater Treatment Plant</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Water Pollution</td>
                        <td class="link-text">In accordance with Section 68 Enhancement and Conservation of
                            National
                            Environmental Quality Act B.E. 2535</td>
                        <td style="text-align: center;">Appx #26 Water Pollution Discharge Report (Form Ror Wor 2)
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Air Pollution</td>
                        <td class="link-text">In accordance with Section 68 Enhancement and Conservation of
                            National
                            Environmental Quality Act B.E. 2535</td>
                        <td style="text-align: center;">Appx #27 Air Pollution Emission Report (Form Ror Wor 3)
                        </td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Labor right regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- Employer Registration for Social Security</td>
                        <td class="link-text">In accordance with Section 36 of the Social Security Act B.E. 2533"
                            (1990)</td>
                        <td style="text-align: center;">Appx #28 Social Security Certificate of Registration</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Employment and Working Conditions Declaration Form (KR 11)</td>
                        <td class="link-text">In accordance with Section 155/1 of the Labour Protection Act, B.E.
                            2541 (1998)</td>
                        <td style="text-align: center;">Appx #29 - KR 11</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Foreigner Workers</td>
                        <td class="link-text">Registration of Foreign Workers 2023 According to the Cabinet
                            Resolution of July 5, 2023</td>
                        <td style="text-align: center;">Appx #30 Foreigner Workers List</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Occupational Health and Safety regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- License for Operating Health Hazardous Activities.</td>
                        <td class="link-text">In accordance with Section 32 of the Public Health Act B.E. 2535
                            (1992) and Amendments</td>
                        <td style="text-align: center;">Appx #31 License for Operating Health Hazardous Activities.
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Appointment of the Occupational Health and Safety Committee</td>
                        <td class="link-text">In accordance with Section 2 of the Ministerial Regulation on
                            Standards for the Administration and Management of Occupational Safety, Health, and
                            Environmental Conditions in the Workplace B.E. 2549 (2006)</td>
                        <td style="text-align: center;">Appx #32 Safety Committees</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Fire Fighting and Evacuation</td>
                        <td class="link-text">The Ministry Regulation sets forth standards for the management and
                            implementation of occupational safety, health, and environmental conditions in the
                            workplace concerning fire prevention and suppression, as outlined in Section 30 of the
                            regulation</td>
                        <td style="text-align: center;">Annex #33 Firefighting and Fire Evacuation</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Hazardous Material Possession License</td>
                        <td class="link-text">In accordance with Section 18 of the Hazardous Substance Act B.E.
                            2535 (1992)</td>
                        <td style="text-align: center;">Annex #34 Hazardous Material Possession License</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Trade, tax and customs regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- Calibration of Weighing and Measuring Instruments</td>
                        <td class="link-text">In accordance with the Measurement Act B.E. 2542 (1999)</td>
                        <td style="text-align: center;">Annex #35 Calibration of Weighing and Measuring Instruments
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Certificate of Value Added Tax Registration</td>
                        <td class="link-text">In accordance with Revenue Code Section 4 Value Added Tax</td>
                        <td style="text-align: center;">Annex #36 Certificate of Value Added Tax Registration</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Natural Rubber Trading License</td>
                        <td class="link-text">In accordance with Section 22 the Rubber Control Act B.E. 2542 (1999)
                        </td>
                        <td style="text-align: center;">Annex #37 Natural Rubber Trading License</td>
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
                        <td style="text-align: center;">Annex #38 Natural Rubber Trading License
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
                        <td style="text-align: center;">Annex #39 Natural Rubber Trading License
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Forest Certificate</td>
                        <td class="link-text">FSC Forest Management</td>
                        <td style="text-align: center;">Annex #40 Forest Certificate FM</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Forest Certificate</td>
                        <td class="link-text">FSC Chain of Custody</td>
                        <td style="text-align: center;">Annex #41 Forest Certificate COC</td>
                    </tr>
                    <tr>
                        <td class="level-1"><strong>- Production (Chain of Custody #3): </strong></td>
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
                        <td class="link-text">In accordance with Section 1097³ of the Civil Code</td>
                        <td style="text-align: center;">Appx #42 Business Registration Certificate</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Factory license</td>
                        <td class="link-text">In accordance with Section 12 of the Factory Act B.E. 2535 (1992)
                        </td>
                        <td style="text-align: center;">Appx #43 Factory license</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Environment regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- Wastewater Treatment Plant</td>
                        <td class="link-text">In accordance with Section 70 of the Enhancement and Conservation of
                            National Environmental Quality Act, B.E. 2535 (1992)</td>
                        <td style="text-align: center;">Appx #44 Wastewater Treatment Plant</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Water Pollution</td>
                        <td class="link-text">In accordance with Section 68 Enhancement and Conservation of
                            National
                            Environmental Quality Act B.E. 2535</td>
                        <td style="text-align: center;">Appx #45 Water Pollution Discharge Report (Form Ror Wor 2)
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Air Pollution</td>
                        <td class="link-text">In accordance with Section 68 Enhancement and Conservation of
                            National
                            Environmental Quality Act B.E. 2535</td>
                        <td style="text-align: center;">Appx #46 Air Pollution Emission Report (Form Ror Wor 3)
                        </td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Labor right regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- Employer Registration for Social Security</td>
                        <td class="link-text">In accordance with Section 36 of the Social Security Act B.E. 2533"
                            (1990)</td>
                        <td style="text-align: center;">Appx #47 Social Security Certificate of Registration</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Employment and Working Conditions Declaration Form (KR 11)</td>
                        <td class="link-text">In accordance with Section 155/1 of the Labour Protection Act, B.E.
                            2541 (1998)</td>
                        <td style="text-align: center;">Appx #48 - KR 11</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Foreigner Workers</td>
                        <td class="link-text">Registration of Foreign Workers 2023 According to the Cabinet
                            Resolution of July 5, 2023</td>
                        <td style="text-align: center;">Appx #49 Foreigner Workers List</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Anti-Bribery and Corruption Policy</td>
                        <td class="link-text">In accordance with Section 176 of the Organic Act On Anti-Corruption
                            B.E.2561 (2018)</td>
                        <td style="text-align: center;">Appx #50 Anti-Bribery and Corruption Policy</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Occupational Health and Safety regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- License for Operating Health Hazardous Activities.</td>
                        <td class="link-text">In accordance with Section 32 of the Public Health Act B.E. 2535
                            (1992) and Amendments</td>
                        <td style="text-align: center;">Appx #51 License for Operating Health Hazardous Activities.
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Appointment of the Occupational Health and Safety Committee</td>
                        <td class="link-text">In accordance with Section 2 of the Ministerial Regulation on
                            Standards for the Administration and Management of Occupational Safety, Health, and
                            Environmental Conditions in the Workplace B.E. 2549 (2006)</td>
                        <td style="text-align: center;">Appx #52 Safety Committees</td>
                    </tr>
                    <tr>
                        <td class="level-3">- Fire Fighting and Evacuation</td>
                        <td class="link-text">The Ministry Regulation sets forth standards for the management and
                            implementation of occupational safety, health, and environmental conditions in the
                            workplace concerning fire prevention and suppression, as outlined in Section 30 of the
                            regulation</td>
                        <td style="text-align: center;">Annex #53 Firefighting and Fire Evacuation</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Hazardous Material Possession License</td>
                        <td class="link-text">In accordance with Section 18 of the Hazardous Substance Act B.E.
                            2535 (1992)</td>
                        <td style="text-align: center;">Annex #54 Hazardous Material Possession License</td>
                    </tr>
                    <tr>
                        <td class="level-2"><strong>- Trade, tax and customs regulations</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-3">- Calibration of Weighing and Measuring Instruments</td>
                        <td class="link-text">In accordance with the Measurement Act B.E. 2542 (1999)</td>
                        <td style="text-align: center;">Annex #55 Calibration of Weighing and Measuring Instruments
                        </td>
                    </tr>
                    <tr>
                        <td class="level-3">- Certificate of Value Added Tax Registration</td>
                        <td class="link-text">In accordance with Revenue Code Section 4 Value Added Tax</td>
                        <td style="text-align: center;">Annex #56 Certificate of Value Added Tax Registration</td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-3">- Natural Rubber Trading License</td>
                        <td class="link-text">In accordance with Section 22 the Rubber Control Act B.E. 2542 (1999)
                        </td>
                        <td style="text-align: center;">Annex #57 Natural Rubber Trading License</td>
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
                        <td style="text-align: center;">Annex #58 Natural Rubber Trading License
                        </td>
                    </tr>
                    <tr>
                        <td class="level-1"><strong>- Export</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="border-bottom-row">
                        <td class="level-2">- Import and Export License</td>
                        <td class="link-text">In accordance with the Export and Import of Goods Act, B.E. 2522
                            (1979)</td>
                        <td style="text-align: center;">Annex #59 H.V. Fila Export documentation
                        </td>
                    </tr>
                    <tr>
                        <td class="level-1"><strong>- Others</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="level-2">- ISO 9001 Certificate </td>
                        <td class="link-text"></td>
                        <td style="text-align: center;">Annex #60 ISO 9001 Certificate
                        </td>
                    </tr>
                    <tr>
                        <td class="level-2">- Forest Certificate </td>
                        <td class="link-text">FSC Chain of Custody</td>
                        <td style="text-align: center;">Annex #61 Forest Certificate
                        </td>
                    </tr>
                    <tr>
                        <td class="level-2">- OEKO-Tex Certificate</td>
                        <td class="link-text"></td>
                        <td style="text-align: center;">Annex #62 OEKO-Tex Certificate
                        </td>
                    </tr>
                    <tr>
                        <td class="level-2">- DDS Summary</td>
                        <td class="link-text">Due Diligence System</td>
                        <td style="text-align: center;">Annex #63 Due Diligence System
                        </td>
                    </tr>
                    <tr>
                        <td class="level-2">- EUDR Supplier Audit Report</td>
                        <td class="link-text">A third party conducted an audit of the company’s EUDR procedures and
                            their implementation across the entire supply chain.</td>
                        <td style="text-align: center;">Annex #64 EUDR Supplier Audit Report
                        </td>
                    </tr>
                    <tr>
                        <td class="level-2">- PDPA</td>
                        <td class="link-text">In accordance with Personal Data Protection Act B.E.2562 (2019)</td>
                        <td style="text-align: center;">Annex #65- PDPA
                        </td>
                    </tr>
                    <tr>
                        <td class="level-2">- Indigenous people</td>
                        <td class="link-text"></td>
                        <td style="text-align: center;">https://www.thaiipportal.info/geo-informatics
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

            <div style="font-size: 12px; margin-top: 8px; border-top: 1px solid #ccc; padding-top: 4px;">
                <strong>Complaints mechanism:</strong> Complaint submission channels related to the EUDR system:
                Line: @095wvoyb | E-mail: info@hvfila.co.th
            </div>
        </div>
    </div>

    <div class="text-center mt-3 no-print pb-5">
        <button onclick="window.print()" class="btn btn-success btn-lg">Print Report (A4)</button>
    </div>
    @endif
</main>

@include('layouts.footer')
@endsection