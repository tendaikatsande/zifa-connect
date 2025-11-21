<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transfer Certificate</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #006400;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #006400;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .certificate-title {
            font-size: 22px;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .certificate-number {
            font-size: 14px;
            color: #006400;
            margin-top: 10px;
        }
        .content {
            margin: 30px 0;
            text-align: center;
        }
        .declaration {
            font-size: 14px;
            line-height: 1.8;
            margin: 20px 40px;
        }
        .player-name {
            font-size: 18px;
            font-weight: bold;
            color: #006400;
        }
        .details {
            margin: 30px 0;
        }
        .details table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        .details td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .details td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
            text-align: right;
            padding-right: 20px;
        }
        .signature {
            margin-top: 50px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 10px auto;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">ZIFA</div>
        <div class="subtitle">Zimbabwe Football Association</div>
        <div class="certificate-title">Transfer Certificate</div>
        <div class="certificate-number">{{ $certificate_number }}</div>
    </div>

    <div class="content">
        <div class="declaration">
            This is to certify that the transfer of player
        </div>

        <div class="player-name">
            {{ $player->first_name }} {{ $player->last_name }}
        </div>
        <div style="margin-top: 5px; color: #666;">
            ZIFA ID: {{ $player->zifa_id }}
        </div>

        <div class="declaration">
            has been approved and duly registered in accordance with
            the ZIFA Transfer Regulations.
        </div>
    </div>

    <div class="details">
        <table>
            <tr>
                <td>From Club:</td>
                <td>{{ $from_club->name }}</td>
            </tr>
            <tr>
                <td>To Club:</td>
                <td>{{ $to_club->name }}</td>
            </tr>
            <tr>
                <td>Transfer Type:</td>
                <td>{{ ucfirst($transfer->type) }}</td>
            </tr>
            <tr>
                <td>Effective Date:</td>
                <td>{{ $transfer->completed_at?->format('F j, Y') }}</td>
            </tr>
            <tr>
                <td>Registration Season:</td>
                <td>{{ date('Y') }}/{{ date('Y') + 1 }}</td>
            </tr>
        </table>
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <div>ZIFA General Secretary</div>
        <div style="margin-top: 20px; font-size: 10px; color: #666;">
            Date: {{ $generated_at->format('F j, Y') }}
        </div>
    </div>

    <div class="footer">
        <p>Zimbabwe Football Association (ZIFA)</p>
        <p>53 Livingstone Avenue, Harare, Zimbabwe</p>
        <p>This certificate is valid for the current registration season only.</p>
        <p style="margin-top: 10px; font-size: 9px;">
            Verify this certificate at: {{ config('app.url') }}/verify/{{ $certificate_number }}
        </p>
    </div>
</body>
</html>
