<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة رقم {{ $sale->receipt_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .ticket {
            width: 80mm;
            max-width: 80mm;
            margin: 20px auto;
            background: #fff;
            padding: 15px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        th, td {
            border-bottom: 1px dashed #000;
            padding: 6px 0;
            font-size: 13px;
        }

        .payment-summary {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 14px;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        @media print {
            @page { margin: 0; }
            body { margin: 0; background-color: #fff; }
            .ticket { margin: 0; box-shadow: none; padding: 5mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="ticket">
        <div class="text-center">
            <h2 style="margin-bottom: 5px;">سوبر ماركت الإخوة</h2>
            <p style="margin: 0; font-size: 12px;">الولاية الشمالية - سوق الخرطوم بالأسفل</p>
            <p style="margin: 5px 0;">رقم الفاتورة: {{ $sale->receipt_number }}</p>
            <p style="margin: 0;">التاريخ: {{ $sale->created_at->format('Y-m-d H:i') }}</p>
            <hr style="border-top: 1px dashed #000; margin-top: 10px;">
        </div>

        <div style="margin-bottom: 10px; font-size: 13px;">
            <p style="margin: 3px 0;">العميل: <strong>{{ $sale->customer ? $sale->customer->name : 'زبون نقدي عام' }}</strong></p>
            <p style="margin: 3px 0;">طريقة الدفع: <strong>{{ $sale->payment_method == 'bankak' ? 'تطبيق بنكك' : 'كاش نقدي' }}</strong></p>
            @if($sale->payment_method == 'bankak' && $sale->transaction_number)
                <p style="margin: 3px 0;">رقم الإشعار: {{ $sale->transaction_number }}</p>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-right">الصنف</th>
                    <th class="text-center">الكمية</th>
                    <th class="text-left">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product?->name ?? 'منتج محذوف' }} <br> <small>({{ $item->unit?->name ?? 'وحدة غير معروفة' }})</small></td>
                    <td class="text-center">{{ (float) $item->quantity }}</td>
                    <td class="text-left font-bold">{{ number_format($item->subtotal, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="payment-summary">
            <div class="payment-row">
                <span>الإجمالي الكلي:</span>
                <span class="font-bold">{{ number_format($sale->total_amount, 0) }}</span>
            </div>
            
            <div class="payment-row">
                <span>المدفوع الفعلي:</span>
                <span class="font-bold">{{ number_format($sale->paid_amount, 0) }}</span>
            </div>

            @if($sale->remaining_amount > 0)
            <div class="payment-row" style="border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
                <span style="font-weight: bold;">المتبقي (آجل):</span>
                <span style="font-weight: bold; font-size: 16px;">{{ number_format($sale->remaining_amount, 0) }}</span>
            </div>
            @endif
        </div>

        <div class="text-center" style="margin-top: 15px; border-top: 1px dashed #000; padding-top: 10px;">
            <p style="margin: 0; font-size: 12px;">شكراً لتعاملكم معنا!</p>
        </div>

        <div class="text-center no-print" style="margin-top: 30px;">
            <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: #4CAF50; color: white; border: none; border-radius: 5px; margin-bottom: 10px;">
                🖨️ طباعة الفاتورة
            </button>
            <br>
            <a href="/pos" style="text-decoration: none; color: #2196F3; font-weight: bold; border: 1px solid #2196F3; padding: 10px 20px; display: inline-block; border-radius: 5px;">
                ⬅️ العودة لنقطة البيع
            </a>
        </div>
    </div>

</body>
</html>