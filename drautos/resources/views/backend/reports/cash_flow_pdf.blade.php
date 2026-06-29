<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Flow Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 15px;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #083259;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #083259;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 16px;
            margin-top: 5px;
            color: #555;
        }
        .meta-info {
            margin-bottom: 25px;
            font-size: 12px;
            color: #666;
            line-height: 1.5;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5px 0;
        }
        .summary-label {
            font-weight: bold;
            color: #475569;
        }
        .summary-value {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #083259;
            color: #ffffff;
            font-weight: bold;
            padding: 10px;
            font-size: 12px;
            border: 1px solid #083259;
        }
        .data-table td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-success { color: #10b981; }
        .text-danger { color: #ef4444; }
        .text-primary { color: #083259; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Danyal Autos</div>
        <div class="report-title">Cash Flow Statement</div>
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td class="meta-info" style="width: 50%;">
                <strong>Date Range:</strong> {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}<br>
                <strong>Grouping interval:</strong> {{ ucfirst($groupBy) }}<br>
                <strong>Generated At:</strong> {{ date('Y-m-d H:i:s') }}
            </td>
            <td style="width: 50%; text-align: right; font-size: 12px; color: #666; vertical-align: top;">
                <strong>Danyal Autos Co.</strong><br>
                Admin Reporting Portal
            </td>
        </tr>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Total Inflow (Money In):</td>
                <td class="summary-value text-success">Rs. {{ number_format($totalMoneyIn) }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Outflow (Money Out):</td>
                <td class="summary-value text-danger">Rs. {{ number_format($totalMoneyOut) }}</td>
            </tr>
            <tr style="border-top: 1px solid #cbd5e1;">
                <td class="summary-label" style="padding-top: 8px;">Net Position (Cash Flow):</td>
                <td class="summary-value {{ ($totalMoneyIn - $totalMoneyOut) >= 0 ? 'text-success' : 'text-danger' }}" style="padding-top: 8px; font-size: 16px;">
                    Rs. {{ number_format($totalMoneyIn - $totalMoneyOut) }}
                </td>
            </tr>
        </table>
    </div>

    <h4 style="color: #083259; margin-bottom: 10px; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px;">Detailed Transaction Ledger</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Date</th>
                <th style="width: 43%; text-align: left;">Detail (Who & What)</th>
                <th style="width: 15%;">Wallet / Account</th>
                <th style="width: 10%;">Operator</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 10%; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $txn)
            <tr>
                <td>{{ Carbon\Carbon::parse($txn->transaction_date)->format('M d, Y') }}</td>
                <td style="text-align: left; font-weight: bold; color: #1e293b; font-size: 11px;">{{ $txn->resolved_details }}</td>
                <td style="color: #475569; font-size: 11px;">{{ $txn->account->name ?? 'N/A' }}</td>
                <td style="font-size: 11px; font-weight: bold; color: #083259;">{{ $txn->resolved_operator }}</td>
                <td>
                    <span style="font-weight: bold; font-size: 10px;" class="{{ $txn->type == 'in' ? 'text-success' : 'text-danger' }}">
                        {{ strtoupper($txn->type == 'in' ? 'Inflow' : 'Outflow') }}
                    </span>
                </td>
                <td style="text-align: right; font-weight: bold;" class="{{ $txn->type == 'in' ? 'text-success' : 'text-danger' }}">
                    {{ $txn->type == 'in' ? '+' : '-' }} Rs. {{ number_format($txn->amount) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 20px; color: #94a3b8; text-align: center;">No transactions recorded in this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Copyright &copy; {{ date('Y') }} Danyal Autos Co. | Generated Automatically.
    </div>
</body>
</html>
