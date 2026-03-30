<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة رقم {{ $sale->receipt_number }}</title>
    <style>
        /* إعدادات الخطوط لتناسب الطابعات الحرارية (بدون هوامش زائدة) */
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
        
        body {
            font-family: 'Cairo', sans-serif;
            font-size: 13px; /* حجم خط ممتاز لطابعات 80mm */
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #e5e7eb; /* رمادي خفيف للشاشة فقط */
        }

        .ticket {
            width: 78mm; /* عرض الطابعة 80mm (نترك 2mm هامش أمان) */
            max-width: 78mm;
            margin: 20px auto;
            background: #fff;
            padding: 5mm;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-radius: 5px;
        }

        /* تنسيقات النصوص الأساسية */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .font-black { font-weight: 900; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .divider-thick {
            border-top: 2px solid #000;
            margin: 10px 0;
        }

        /* تنسيق الترويسة */
        .header h1 {
            font-size: 22px;
            margin: 0 0 5px 0;
            letter-spacing: -0.5px;
        }
        .header p { margin: 2px 0; font-size: 12px; }

        /* تنسيق بيانات الفاتورة */
        .meta-data { margin-bottom: 10px; font-size: 12px; }
        .meta-data table { width: 100%; border: none; margin: 0; }
        .meta-data td { padding: 2px 0; border: none; }

        /* تنسيق جدول المنتجات */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .items-table th {
            border-bottom: 2px solid #000;
            border-top: 2px solid #000;
            padding: 6px 0;
            font-size: 12px;
        }
        .items-table td {
            border-bottom: 1px dashed #ccc;
            padding: 8px 0;
            vertical-align: top;
        }
        
        .item-name { font-weight: bold; font-size: 13px; line-height: 1.2; }
        .item-meta { font-size: 10px; color: #444; margin-top: 2px; }

        /* تنسيق قسم المجاميع */
        .totals-section { margin-top: 10px; }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .grand-total {
            font-size: 20px;
            font-weight: 900;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 8px 0;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }

        /* أزرار الشاشة (تختفي عند الطباعة) */
        .screen-actions {
            margin-top: 30px;
            text-align: center;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-family: inherit;
            font-weight: bold;
            font-size: 16px;
            text-decoration: none;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            transition: opacity 0.2s;
            margin: 5px;
        }
        .btn-print { background: #10b981; color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); }
        .btn-back { background: #3b82f6; color: white; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); }
        .btn:hover { opacity: 0.9; }

        /* إعدادات الطباعة الحقيقية */
        @media print {
            @page { 
                margin: 0; 
                /* هذا يخبر الويندوز أن هذه ورقة حرارية متصلة */
                size: 80mm auto; 
            }
            body { background-color: #fff; }
            .ticket { 
                margin: 0; 
                box-shadow: none; 
                border-radius: 0; 
                padding: 0 3mm; /* هامش داخلي صغير للورق */
                width: 74mm; 
            }
            .screen-actions { display: none !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="ticket">
        
        <div class="header text-center">
            <h1>{{ \App\Models\Setting::first()->store_name }}</h1>
            <p class="font-bold">أجود المنتجات الغذائية والاستهلاكية</p>
            <p>{{ \App\Models\Setting::first()->address }}</p>
            <p>هاتف: {{ \App\Models\Setting::first()->phone }}</p>
        </div>

        <div class="divider"></div>

        <div class="meta-data">
            <table>
                <tr>
                    <td style="width: 35%;">رقم الفاتورة:</td>
                    <td class="font-bold text-left" style="font-family: 'Courier New', monospace;">#{{ $sale->receipt_number }}</td>
                </tr>
                <tr>
                    <td>التاريخ:</td>
                    <td class="text-left" dir="ltr">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <td>الكاشير:</td>
                    <td class="text-left">{{ $sale->user ? $sale->user->name : 'النظام' }}</td>
                </tr>
                <tr>
                    <td>العميل:</td>
                    <td class="text-left font-bold">{{ $sale->customer ? $sale->customer->name : 'زبون نقدي عام' }}</td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-right" style="width: 50%;">الصنف</th>
                    <th class="text-center" style="width: 20%;">الكمية</th>
                    <th class="text-center" style="width: 30%;">القيمة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product?->name ?? 'منتج محذوف' }}</div>
                        <div class="item-meta">
                            {{ (float) $item->unit_price }} × {{ $item->unit?->name ?? 'وحدة' }}
                        </div>
                    </td>
                    <td class="text-center font-bold" style="vertical-align: middle;">{{ (float) $item->quantity }}</td>
                    <td class="text-center font-bold" style="vertical-align: middle;">{{ number_format($item->subtotal, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            
            <div class="grand-total">
                <span>الإجمالي المطلوب</span>
                <span>{{ number_format($sale->total_amount, 0) }}</span>
            </div>

            <div style="font-size: 13px; margin-bottom: 8px;">
                @if($sale->paid_cash > 0)
                <div class="total-row">
                    <span>مدفوع نقداً (كاش):</span>
                    <span class="font-bold">{{ number_format($sale->paid_cash, 0) }}</span>
                </div>
                @endif
                
                @if($sale->paid_bankak > 0)
                <div class="total-row">
                    <span>مدفوع تحويل (بنكك):</span>
                    <span class="font-bold">{{ number_format($sale->paid_bankak, 0) }}</span>
                </div>
                @if($sale->transaction_number)
                <div class="total-row text-left" style="font-size: 11px; margin-top: -3px; color: #444;">
                    <span dir="ltr">Ref: {{ $sale->transaction_number }}</span>
                </div>
                @endif
                @endif
            </div>

            <div class="divider"></div>

            @php
                $totalPaid = $sale->paid_cash + $sale->paid_bankak;
                $remaining = $sale->total_amount - $totalPaid;
            @endphp
            
            @if($remaining > 0)
            <div class="total-row" style="margin-top: 8px;">
                <span class="font-bold">المتبقي (آجل على العميل):</span>
                <span class="font-black" style="font-size: 16px;">{{ number_format($remaining, 0) }}</span>
            </div>
            @elseif($remaining < 0)
            <div class="total-row" style="margin-top: 8px;">
                <span class="font-bold">الباقي (إرجاع للعميل):</span>
                <span class="font-black" style="font-size: 16px;">{{ number_format(abs($remaining), 0) }}</span>
            </div>
            @endif
        </div>

        <div class="text-center" style="margin-top: 20px;">
            <p class="font-bold" style="margin: 0; font-size: 14px;">شكراً لزيارتكم!</p>
            <p style="margin: 3px 0; font-size: 11px;">{{ \App\Models\Setting::first()->receipt_footer }}</p>
            
            <div style="margin-top: 15px; font-family: 'Courier New', monospace; letter-spacing: 2px;">
                |||| || ||| || ||| ||| || ||
                <br>
                <span style="font-size: 10px; letter-spacing: 0;">{{ $sale->receipt_number }}</span>
            </div>
            
            <p style="margin-top: 10px; font-size: 10px; color: #666;">تم الإصدار بواسطة نظام "يَسير" POS</p>
        </div>

    </div>

    <div class="screen-actions no-print">
        <button onclick="window.print()" class="btn btn-print">
            🖨️ إعادة طباعة الفاتورة
        </button>
        <br><br>
        <a href="{{ route('pos') }}" class="btn btn-back">
            🛒 العودة لشاشة الكاشير (فاتورة جديدة)
        </a>
    </div>

</body>
</html>