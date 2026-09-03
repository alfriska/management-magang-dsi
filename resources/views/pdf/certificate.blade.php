<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sertifikat Magang - {{ $intern->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1e293b;
            background: #fffef5;
        }

        .page {
            width: 100%;
            height: 100%;
            padding: 15px;
            position: relative;
        }

        /* Border Frame */
        .border-frame {
            border: 3px double #b8860b;
            padding: 15px;
            height: 100%;
            position: relative;
        }

        .inner-frame {
            border: 1px solid #daa520;
            padding: 12px;
            height: 100%;
            text-align: center;
        }

        /* Corner Ornaments using table trick */
        .corner-table {
            position: absolute;
            width: 50px;
            height: 50px;
        }

        .corner-tl {
            top: 35px;
            left: 35px;
            border-top: 3px solid #b8860b;
            border-left: 3px solid #b8860b;
        }

        .corner-tr {
            top: 35px;
            right: 35px;
            border-top: 3px solid #b8860b;
            border-right: 3px solid #b8860b;
        }

        .corner-bl {
            bottom: 35px;
            left: 35px;
            border-bottom: 3px solid #b8860b;
            border-left: 3px solid #b8860b;
        }

        .corner-br {
            bottom: 35px;
            right: 35px;
            border-bottom: 3px solid #b8860b;
            border-right: 3px solid #b8860b;
        }

        /* Header */
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3px;
        }

        .tagline {
            font-size: 9px;
            color: #64748b;
            font-style: italic;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .divider {
            width: 150px;
            height: 1px;
            background: linear-gradient(to right, transparent, #daa520, transparent);
            margin: 0 auto 10px;
        }

        /* Certificate Title */
        .cert-title {
            font-size: 32px;
            font-weight: bold;
            color: #b8860b;
            margin-bottom: 3px;
            font-style: italic;
        }

        .cert-subtitle {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #475569;
            margin-bottom: 5px;
        }

        .cert-number {
            font-size: 8px;
            color: #b8860b;
            margin-bottom: 12px;
        }

        /* Recipient */
        .presented-to {
            font-size: 10px;
            font-style: italic;
            color: #64748b;
            margin-bottom: 5px;
        }

        .recipient-name {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
            padding-bottom: 5px;
            border-bottom: 2px solid #daa520;
            display: inline-block;
        }

        .school-name {
            font-size: 10px;
            color: #475569;
            margin-bottom: 10px;
        }

        /* Description */
        .description {
            font-size: 9px;
            color: #334155;
            line-height: 1.5;
            max-width: 450px;
            margin: 0 auto 8px;
        }

        .period {
            font-size: 9px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 10px;
        }

        /* Grade Box */
        .grade-box {
            display: inline-block;
            border: 2px solid #daa520;
            background: #fffef5;
            padding: 6px 25px;
            margin-bottom: 12px;
        }

        .grade-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .grade-score {
            font-size: 18px;
            font-weight: bold;
            color: #b8860b;
            letter-spacing: 2px;
            bottom: 45mm;
            left: 25mm;
            right: 25mm;
        }

        .sig-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            width: 100%;
        }

        /* Signature Section - Using Table for DomPDF stability */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 5px;
        }

        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 15px;
        }

        .signature-label {
            font-size: 8px;
            color: #64748b;
            margin-bottom: 30px;
        .sig-line {
            border-bottom: 2px solid black;
            width: 70mm;
            margin: 10mm auto 2mm;
        }

        .signature-line {
            border-bottom: 1px solid #1e293b;
            width: 120px;
            margin: 0 auto 3px;
        }

        .signature-name {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
        }

        .signature-title {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 40px;
            right: 45px;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- Corner Ornaments -->
        <div class="corner-table corner-tl"></div>
        <div class="corner-table corner-tr"></div>
        <div class="corner-table corner-bl"></div>
        <div class="corner-table corner-br"></div>

        <div class="border-frame">
            <div class="inner-frame">

                <!-- Header -->
                <div class="company-name">PT. Duta Solusi Informatika</div>
                <div class="tagline">Excellence in Technology</div>
                <div class="divider"></div>

                <!-- Title -->
                <div class="cert-title">Sertifikat</div>
                <div class="cert-subtitle">Kelulusan Magang</div>
                <div class="cert-number">Nomor: {{ $intern->certificate_number }}</div>

                <!-- Recipient -->
                <div class="presented-to">Dengan bangga diberikan kepada:</div>
                <div class="recipient-name">{{ $intern->user->name }}</div>
                <div class="school-name">{{ $intern->school }} — Divisi {{ $intern->department }}</div>

                <!-- Description -->
                <div class="description">
                    Telah menyelesaikan program magang di PT. Duta Solusi Informatika dengan menunjukkan
                    dedikasi, integritas, dan profesionalisme yang tinggi.
                </div>
                <div class="period">
                    Periode: {{ $intern->start_date->format('d F Y') }} — {{ $intern->end_date->format('d F Y') }}
                </div>

                <!-- Grade -->
                <div class="grade-box">
                    <div class="grade-label">Predikat Kelulusan</div>
                    <div class="grade-score">
                        {{ $intern->getOverallScore() >= 90 ? 'SANGAT BAIK' : ($intern->getOverallScore() >= 80 ? 'BAIK' : 'CUKUP') }}
                    </div>
                </div>

                <!-- Signatures -->
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-label">Mengetahui,</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">Manager DSI</div>
                        <div class="signature-title">Pembimbing Lapangan</div>
                    </div>
                    <div class="signature-box">
                        <!-- Empty middle spacer -->
                    </div>
                    <div class="signature-box">
                        <div class="signature-label">Malang, {{ now()->format('d F Y') }}</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">Direktur DSI</div>
                        <div class="signature-title">Pimpinan Perusahaan</div>
                    </div>
                </div>

            </div>
        </div>

        <div class="footer">Diterbitkan: {{ now()->format('d/m/Y') }}</div>
    <div class="footer">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <div class="sig-box" style="margin: 0 auto;">
                        <div class="sig-line"></div>
                        <div class="sig-name">Manager DSI</div>
                        <div class="sig-title">Pembimbing Lapangan</div>
                    </div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <div class="sig-box" style="margin: 0 auto;">
                        <div class="sig-line"></div>
                        <div class="sig-name">Kepala Direktur DSI</div>
                        <div class="sig-title">Pimpinan Perusahaan</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
