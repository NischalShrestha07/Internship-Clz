<!DOCTYPE html>
<html>

<head>
    <title>Payslip - {{ $payslip->period }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: white;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .page-break {
                page-break-after: always;
            }
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 7px;
        }

        .payslip-title {
            font-size: 24px;
            margin: 10px 0;
            color: #333;
        }

        .section {
            margin: 10px 0;
        }

        .section-title {
            background: #f8f9fa;
            padding: 10px 12px;
            border-left: 5px solid #2c5aa0;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 10px 0;
        }

        .info-item {
            margin: 8px 0;
            font-size: 14px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 140px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 14px;
        }

        .table th {
            background: #2c5aa0;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #1e3d6f;
        }

        .table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .total-row {
            background: #e9ecef !important;
            font-weight: bold;
            font-size: 15px;
        }

        .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .net-salary {
            background: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            margin: 10px 0;
            border-radius: 8px;
            border: 2px solid #1e7e34;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            color: #666;
            font-size: 12px;
        }

        .breakdown-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2c5aa0;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            z-index: 1000;
        }

        .print-btn:hover {
            background: #1e3d6f;
        }
    </style>
</head>

<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Print / Save as PDF
    </button>

    <div class="header">
        <div class="company-name">YOUR COMPANY NAME</div>
        <div class="payslip-title">SALARY SLIP</div>
        <div style="font-size: 16px; margin-top: 5px;">Period: {{ $payslip->period }}</div>
    </div>

    <div class="section">
        <div class="section-title">Employee Information</div>
        <div class="info-grid">
            <div>
                <div class="info-item"><span class="info-label">Employee Name:</span> {{ $payslip->employee_name }}
                </div>
                <div class="info-item"><span class="info-label">Position:</span> {{ $payslip->employee->position ??
                    'N/A' }}</div>
            </div>
            <div>
                <div class="info-item"><span class="info-label">Payment Date:</span> {{ $payslip->payment_date ?
                    $payslip->payment_date->format('d M, Y') : 'Pending' }}</div>
                <div class="info-item"><span class="info-label">Status:</span> {{ strtoupper($payslip->status) }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Salary Breakdown</div>
        <div class="breakdown-grid">
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="2">EARNINGS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $salaryBreakdown = app('App\Livewire\Payroll\MyPayslips')->getSalaryBreakdown($payslip);
                        @endphp
                        @foreach($salaryBreakdown as $earning)
                        <tr>
                            <td>{{ $earning['label'] }}</td>
                            <td class="amount">Rs {{ number_format($earning['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                        @if($payslip->overtime_pay > 0)
                        <tr>
                            <td>Overtime Pay</td>
                            <td class="amount">Rs {{ number_format($payslip->overtime_pay, 2) }}</td>
                        </tr>
                        @endif
                        @if($payslip->bonus > 0)
                        <tr>
                            <td>Bonus</td>
                            <td class="amount">Rs {{ number_format($payslip->bonus, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="total-row">
                            <td><strong>TOTAL EARNINGS</strong></td>
                            <td class="amount"><strong>Rs {{ number_format($payslip->total_earnings, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="2">DEDUCTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $deductionBreakdown = app('App\Livewire\Payroll\MyPayslips')->getDeductionBreakdown($payslip);
                        @endphp
                        @foreach($deductionBreakdown as $deduction)
                        <tr>
                            <td>{{ $deduction['label'] }}</td>
                            <td class="amount">Rs {{ number_format($deduction['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td><strong>TOTAL DEDUCTIONS</strong></td>
                            <td class="amount"><strong>Rs {{ number_format($payslip->total_deductions, 2) }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Attendance Summary</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Working Days</th>
                    <th>Present Days</th>
                    <th>Absent Days</th>
                    <th>Overtime Hours</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $payslip->working_days }}</td>
                    <td>{{ $payslip->present_days }}</td>
                    <td>{{ $payslip->absent_days }}</td>
                    <td>{{ $payslip->overtime_hours }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="net-salary">
        <h2 style="margin: 0 0 10px 0;">NET SALARY PAYABLE</h2>
        <h1 style="margin: 0 0 15px 0; font-size: 36px;">Rs {{ number_format($payslip->net_salary, 2) }}</h1>
        <p style="margin: 0; font-size: 16px;">
            @php
            $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
            $words = $formatter->format($payslip->net_salary);
            @endphp
            <strong>Amount in words:</strong> {{ ucwords($words) }} Rupees Only
        </p>
    </div>

    <div class="footer">
        <p>This is a computer-generated payslip and does not require a signature.</p>
        <p>For queries, please contact the HR department.</p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    <script>
        // Auto-print when page loads (optional)
        window.onload = function() {
            // Uncomment the line below if you want auto-print
            window.print();
        };
    </script>
</body>

</html>