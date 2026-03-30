<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف حساب | {{ $customer->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        /* إعدادات ورقة A4 التلقائية */
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }
        /* إخفاء كل شيء غير ضروري عند الطباعة */
        @media print {
            .no-print { display: none !important; }
        }
        
        .container { max-width: 100%; margin: 0 auto; }
        
        /* ترويسة التقرير */
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .store-info h1 { margin: 0 0 5px 0; font-size: 24px; font-weight: 800; }
        .store-info p { margin: 2px 0; font-size: 12px; }
        
        .report-title {
            text-align: center;
            flex-grow: 1;
        }
        .report-title h2 {
            margin: 0;
            font-size: 20px;
            border: 2px solid #000;
            padding: 5px 20px;
            border-radius: 5px;
            display: inline-block;
        }
        
        /* معلومات العميل */
        .customer-info {
            display: flex;
            justify-content: space-between;
            background: #f9f9f9;
            border: 1px solid #ccc;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .customer-info div { font-weight: 600; }
        
        /* الجداول */
        h3 { font-size: 16px; margin-bottom: 8px; font-weight: 800; border-bottom: 1px dashed #ccc; display: inline-block; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 13px;
            page-break-inside: avoid; /* يمنع انقسام الجدول بين صفحتين بشكل مشوه */
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: right;
        }
        th { background-color: #f2f2f2; font-weight: 800; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        /* الإجماليات والتوقيع */
        .grand-total {
            font-size: 20px;
            font-weight: 800;
            text-align: center;
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 40px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 20px;
        }
        .signature-box {
            width: 250px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: 600;
        }

        /* زر الطباعة للشاشة */
        .print-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body onload="window.print();">

    <div class="container">
        
        <button onclick="window.print()" class="no-print print-btn">🖨️ طباعة الكشف الآن</button>

        <div class="header">
            <div class="store-info">
                <h1>{{ $settings->store_name ?? 'سوبر ماركت الإخوة' }}</h1>
                <p>هاتف: <span dir="ltr">{{ $settings->phone ?? '---' }}</span></p>
                <p>العنوان: {{ $settings->address ?? '---' }}</p>
            </div>
            <div class="report-title">
                <h2>كشف حساب تفصيلي</h2>
                <p>تاريخ الإصدار: {{ date('Y-m-d h:i A') }}</p>
            </div>
            <div style="width: 150px;">
                </div>
        </div>

        <div class="customer-info">
            <div>اسم العميل: {{ $customer->name }}</div>
            <div>رقم الهاتف: <span dir="ltr">{{ $customer->phone ?? 'غير مسجل' }}</span></div>
            <div style="color: #dc2626; font-size: 16px;">الرصيد المطلوب (الدين): {{ number_format($customer->balance, 0) }} SDG</div>
        </div>

        <h3>1. الفواتير والمسحوبات 🛒</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">تاريخ الفاتورة</th>
                    <th style="width: 15%;">رقم الفاتورة</th>
                    <th>حالة الفاتورة</th>
                    <th class="text-center">إجمالي الفاتورة</th>
                    <th class="text-center">المدفوع منها</th>
                    <th class="text-left">المتبقي (الآجل)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                    <td>{{ $sale->receipt_number }}</td>
                    <td>{{ $sale->payment_status == 'paid' ? 'خالصة' : 'آجلة / جزئية' }}</td>
                    <td class="text-center">{{ number_format($sale->total_amount, 0) }}</td>
                    <td class="text-center">{{ number_format($sale->paid_amount, 0) }}</td>
                    <td class="text-left font-bold">{{ number_format($sale->remaining_amount, 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">لا توجد مسحوبات مسجلة.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>2. الدفعات النقدية والتحويلات (سندات القبض) 💵</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">تاريخ الدفعة</th>
                    <th style="width: 15%;">طريقة الدفع</th>
                    <th>رقم المرجع (بنكك)</th>
                    <th>الملاحظات</th>
                    <th class="text-left">المبلغ المدفوع</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                    <td>{{ $payment->payment_method == 'cash' ? 'نقداً (كاش)' : 'تحويل (بنكك)' }}</td>
                    <td dir="ltr" class="text-right">{{ $payment->transaction_number ?? '---' }}</td>
                    <td>{{ $payment->notes ?? '---' }}</td>
                    <td class="text-left font-bold">{{ number_format($payment->amount, 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">لا توجد دفعات مسجلة.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="grand-total">
            الرصيد النهائي المطلوب سداده: <span dir="ltr">{{ number_format($customer->balance, 0) }} SDG</span>
        </div>

        <div class="signatures">
            <div class="signature-box">
                توقيع المستلم (العميل)
                <br><br><br>
            </div>
            <div class="signature-box">
                توقيع المحاسب (الختم)
                <br><br><br>
                {{ auth()->user()->name }}
            </div>
        </div>

    </div>

</body>
</html>