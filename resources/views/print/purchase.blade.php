<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة مشتريات | #{{ $purchase->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800;900&display=swap" rel="stylesheet">
    <style>
        /* إعدادات ورقة A4 التلقائية */
        @page { size: A4; margin: 15mm; }
        body {
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }
        @media print { .no-print { display: none !important; } }
        
        .container { max-width: 100%; margin: 0 auto; }
        
        /* الترويسة */
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .store-info h1 { margin: 0 0 5px 0; font-size: 24px; font-weight: 900; }
        .store-info p { margin: 2px 0; font-size: 12px; font-weight: 600; }
        
        .report-title { text-align: center; flex-grow: 1; }
        .report-title h2 {
            margin: 0;
            font-size: 22px;
            border: 2px solid #000;
            padding: 5px 25px;
            border-radius: 8px;
            display: inline-block;
            background-color: #f8f9fa;
        }
        
        /* معلومات المورد والفاتورة */
        .info-box {
            display: flex;
            justify-content: space-between;
            background: #fff;
            border: 2px solid #000;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .info-box div { font-weight: 800; font-size: 15px;}
        
        /* جدول الأصناف */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }
        th, td { border: 1px solid #000; padding: 10px; text-align: right; }
        th { background-color: #e5e7eb; font-weight: 900; font-size: 13px; }
        .text-center { text-align: center; }
        
        /* خلاصة الحساب */
        .totals-container {
            width: 50%;
            float: left;
            border: 2px solid #000;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 40px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 15px;
            border-bottom: 1px solid #ccc;
            font-weight: 800;
        }
        .totals-row:last-child { border-bottom: none; background-color: #f3f4f6; font-size: 18px; }
        .text-red { color: #dc2626; }
        
        /* التوقيعات */
        .clear { clear: both; }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .signature-box {
            width: 250px;
            text-align: center;
            border-top: 2px dashed #000;
            padding-top: 10px;
            font-weight: 800;
            font-size: 16px;
        }
        .print-btn {
            display: block; width: 200px; margin: 20px auto; padding: 10px;
            text-align: center; background: #2563eb; color: white; text-decoration: none;
            font-weight: bold; border-radius: 8px; cursor: pointer;
        }
    </style>
</head>
<body onload="window.print();">

    <div class="container">
        
        <button onclick="window.print()" class="no-print print-btn">🖨️ طباعة الفاتورة (A4)</button>

        <div class="header">
            <div class="store-info">
                <h1>{{ $settings->store_name ?? 'يسير للخدمات' }}</h1>
                <p>هاتف: <span dir="ltr">{{ $settings->phone ?? '---' }}</span></p>
                <p>العنوان: {{ $settings->address ?? '---' }}</p>
            </div>
            <div class="report-title">
                <h2>فاتورة مشتريات (إدخال بضاعة)</h2>
                <p style="margin-top:10px; font-weight: bold;">تاريخ الطباعة: {{ date('Y-m-d h:i A') }}</p>
            </div>
            <div style="width: 150px;"></div>
        </div>

        <div class="info-box">
            <div>المرجع المستندي: <span dir="ltr">#{{ $purchase->id }}</span></div>
            <div>اسم المورد: {{ $purchase->supplier_name ?: 'مورد عام نقدي' }}</div>
            <div>تاريخ الفاتورة: {{ date('Y-m-d', strtotime($purchase->purchase_date)) }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">م</th>
                    <th style="width: 35%;">الصنف (المنتج)</th>
                    <th class="text-center">الوحدة</th>
                    <th class="text-center">الكمية</th>
                    <th class="text-center">التكلفة (للوحدة)</th>
                    <th class="text-center">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-weight: 800;">{{ $item->product ? $item->product->name : 'منتج محذوف' }}</td>
                    <td class="text-center">{{ $item->unit ? $item->unit->name : '-' }}</td>
                    <td class="text-center font-bold">{{ (float) $item->quantity }}</td>
                    <td class="text-center">{{ number_format($item->unit_cost_price, 0) }}</td>
                    <td class="text-center font-bold">{{ number_format($item->quantity * $item->unit_cost_price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-container">
            <div class="totals-row">
                <span>إجمالي الفاتورة:</span>
                <span dir="ltr">{{ number_format($purchase->total_amount, 0) }}</span>
            </div>
            <div class="totals-row">
                <span>المدفوع كاش:</span>
                <span dir="ltr">{{ number_format($purchase->paid_cash, 0) }}</span>
            </div>
            <div class="totals-row">
                <span>المدفوع تحويل (بنكك):</span>
                <span dir="ltr">{{ number_format($purchase->paid_bankak, 0) }}</span>
            </div>
            <div class="totals-row text-red">
                <span>المتبقي (الآجل):</span>
                <span dir="ltr">{{ number_format($purchase->remaining_amount, 0) }} SDG</span>
            </div>
        </div>

        <div class="clear"></div>

        <div class="signatures">
            <div class="signature-box">
                توقيع المورد / المندوب
                <br><br><br>
            </div>
            <div class="signature-box">
                توقيع المستلم (أمين المخزن)
                <br><br><br>
                {{ auth()->user()->name }}
            </div>
        </div>

    </div>

</body>
</html>