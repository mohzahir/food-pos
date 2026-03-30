<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عرض أسعار - نظام يَسير</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800;900&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: 'Cairo', sans-serif; color: #1e293b; background: #fff; margin: 0; font-size: 14px; -webkit-print-color-adjust: exact; }
        @media print { .no-print { display: none !important; } }
        
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-area h1 { margin: 0; color: #2563eb; font-size: 32px; font-weight: 900; letter-spacing: -1px; }
        .logo-area p { margin: 5px 0 0; color: #64748b; font-weight: 800; font-size: 12px; }
        .meta-info { text-align: left; }
        .meta-info p { margin: 3px 0; font-weight: 600; }
        
        .client-section { background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; margin-bottom: 30px; }
        .client-section h3 { margin: 0 0 10px 0; color: #0f172a; font-size: 18px; }
        .client-section p { margin: 5px 0; font-size: 15px; font-weight: 600; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px 15px; text-align: right; border-bottom: 1px solid #e2e8f0; }
        th { background: #1e293b; color: #fff; font-weight: 800; font-size: 15px; }
        td { font-size: 15px; font-weight: 600; color: #334155; }
        
        .total-section { background: #eff6ff; border: 2px solid #2563eb; padding: 15px; border-radius: 8px; text-align: left; margin-bottom: 30px; }
        .total-section h2 { margin: 0; color: #1e293b; font-size: 18px; display: inline-block; margin-left: 15px;}
        .total-section .price { font-size: 24px; font-weight: 900; color: #2563eb; }
        
        .terms { margin-bottom: 40px; }
        .terms h4 { border-bottom: 2px dashed #cbd5e1; display: inline-block; padding-bottom: 5px; }
        .terms ul { padding-right: 20px; color: #475569; }
        .terms li { margin-bottom: 8px; }
        
        .footer { display: flex; justify-content: space-between; margin-top: 50px; text-align: center; }
        .signature-box { width: 250px; padding-top: 15px; border-top: 2px solid #0f172a; font-weight: 800; }
        
        .btn { display: block; width: 200px; margin: 20px auto; padding: 12px; text-align: center; background: #2563eb; color: #fff; text-decoration: none; font-weight: 800; border-radius: 8px; cursor: pointer; border: none; }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print btn">🖨️ طباعة عرض السعر</button>

    <div class="header">
        <div class="logo-area">
            <h1>يَسير</h1>
            <p>لحلول نقاط البيع وإدارة الأعمال (POS & ERP)</p>
        </div>
        <div class="meta-info">
            <p>التاريخ: {{ date('Y / m / d') }}</p>
            <p>رقم العرض: QUO-{{ rand(1000, 9999) }}</p>
            <p>صلاحية العرض: 15 يوماً</p>
        </div>
    </div>

    <div class="client-section">
        <h3>الموضوع: عرض توريد وتركيب النظام المحاسبي الشامل "يَسير"</h3>
        <p>السادة الأفاضل / صاحب النشاط التجاري</p>
        <p style="color: #64748b; font-weight: normal; margin-top: 10px;">بناءً على طلبكم، يسعدنا تقديم عرض السعر التالي لتزويدكم بالنظام المتكامل لإدارة نشاطكم التجاري. نحن لا نبيع أجزاءً منفصلة، بل نقدم لكم نظاماً شاملاً يغطي كافة احتياجاتكم في رخصة واحدة.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">م</th>
                <th>الوحدات البرمجية المشمولة في النظام (الرخصة الكاملة)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>وحدة الكاشير ونقاط البيع السريعة (دعم الدفع النقدي وتحويلات بنكك).</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>وحدة إدارة العملاء وتسجيل الديون الآجلة مع طباعة كشوفات الحساب.</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>وحدة المشتريات وإدارة الموردين وتسوية حساباتهم.</td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>وحدة المخازن والجرد مع ميزة <strong>"رادار الصلاحية"</strong> الذكي لتنبيهات التوالف.</td>
            </tr>
            <tr>
                <td style="text-align: center;">5</td>
                <td>المركز المالي، إدارة المصروفات اليومية، وحساب الأرباح والخسائر.</td>
            </tr>
            <tr>
                <td style="text-align: center;">6</td>
                <td>النسخ الاحتياطي السهل لحماية بياناتك من الضياع.</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <h2>التكلفة الإجمالية للرخصة البرمجية (فرع واحد / جهاز واحد):</h2>
        <span class="price" dir="ltr">1,300,000 SDG</span>
    </div>

    <div class="terms">
        <h4>📌 الشروط والأحكام والخدمات المشمولة:</h4>
        <ul>
            <li><strong>التركيب والتدريب:</strong> يشمل السعر تركيب النظام على جهاز الكمبيوتر الخاص بكم، وتدريب موظف الكاشير والإدارة على استخدامه.</li>
            <li><strong>شروط الدفع:</strong> يتم سداد 50% كدفعة مقدمة عند التعاقد، و 50% المتبقية بعد الانتهاء من التركيب وتجربة النظام.</li>
            <li><strong>الأجهزة:</strong> هذا العرض مخصص <strong>للبرمجيات فقط</strong>، ولا يشمل أسعار الأجهزة المادية (أجهزة الكمبيوتر، طابعة الإيصالات، درج الكاشير، قارئ الباركود).</li>
            <li><strong>الدعم الفني:</strong> يمنح العميل شهر (30 يوماً) كفترة ضمان ودعم فني مجاني للبرمجيات.</li>
        </ul>
    </div>

    <div class="footer">
        <div class="signature-box">
            العميل (الختم والتوقيع بالموافقة)
            <br><br><br>
        </div>
        <div class="signature-box">
            مقدم العرض (إدارة نظام يَسير)
            <br><br><br>
            محمد زاهر
        </div>
    </div>

</body>
</html>