<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #006400;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #006400;
        }
        .receipt-title {
            font-size: 18px;
            margin-top: 10px;
        }
        .receipt-number {
            font-size: 14px;
            color: #666;
        }
        .details {
            margin: 20px 0;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .details td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
        }
        .amount {
            font-size: 24px;
            text-align: center;
            margin: 30px 0;
            color: #006400;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 10px;
            color: #666;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 15px;
            border-radius: 3px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">ZIFA CONNECT</div>
        <div class="receipt-title">PAYMENT RECEIPT</div>
        <div class="receipt-number">{{ $receipt_number }}</div>
    </div>

    <div class="details">
        <table>
            <tr>
                <td>Receipt Date:</td>
                <td>{{ $generated_at->format('F j, Y') }}</td>
            </tr>
            <tr>
                <td>Payment Reference:</td>
                <td>{{ $payment->payment_reference }}</td>
            </tr>
            <tr>
                <td>Invoice Number:</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>{{ $invoice->description }}</td>
            </tr>
            <tr>
                <td>Payment Method:</td>
                <td>{{ ucfirst($payment->gateway_method ?? $payment->gateway) }}</td>
            </tr>
            <tr>
                <td>Transaction ID:</td>
                <td>{{ $payment->gateway_transaction_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Payment Date:</td>
                <td>{{ $payment->paid_at?->format('F j, Y H:i') }}</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td><span class="status-paid">PAID</span></td>
            </tr>
        </table>
    </div>

    <div class="amount">
        {{ $payment->currency }} {{ number_format($payment->amount_cents / 100, 2) }}
    </div>

    <div class="footer">
        <p>Thank you for your payment!</p>
        <p>Zimbabwe Football Association (ZIFA)</p>
        <p>53 Livingstone Avenue, Harare, Zimbabwe</p>
        <p>Tel: +263 4 791 645 | Email: info@zifa.org.zw</p>
        <p style="margin-top: 15px; font-size: 9px;">
            This is a computer-generated receipt and does not require a signature.
        </p>
    </div>
</body>
</html>
