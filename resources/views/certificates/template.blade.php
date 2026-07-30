<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate</title>
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
            background: #F8FAFC;
        }
        .certificate {
            width: 100%;
            height: 100%;
            padding: 60px 80px;
            box-sizing: border-box;
            position: relative;
        }
        .border-frame {
            border: 3px solid #D4A017;
            padding: 40px 60px;
            min-height: 500px;
            position: relative;
        }
        .border-frame:before {
            content: '';
            position: absolute;
            top: 10px; left: 10px; right: 10px; bottom: 10px;
            border: 1px solid #38081230;
            pointer-events: none;
        }
        h1 {
            text-align: center;
            color: #380812;
            font-size: 32px;
            margin-bottom: 5px;
            letter-spacing: 2px;
        }
        .subtitle {
            text-align: center;
            color: #6b0f23;
            font-size: 12px;
            margin-bottom: 30px;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .cert-title {
            text-align: center;
            color: #D4A017;
            font-size: 24px;
            margin: 20px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        .content {
            text-align: center;
            color: #333;
            font-size: 14px;
            line-height: 2;
        }
        .content .name {
            font-size: 28px;
            font-weight: bold;
            color: #380812;
            margin: 15px 0;
        }
        .content .course-name {
            font-size: 18px;
            font-weight: bold;
            color: #D4A017;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #666;
        }
        .footer .cert-number {
            font-size: 10px;
            color: #999;
        }
        .seal {
            text-align: center;
            margin-top: 20px;
        }
        .seal svg { width: 60px; height: 60px; }
        .gold-line {
            width: 200px;
            height: 2px;
            background: #D4A017;
            margin: 10px auto;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="border-frame">
            <h1>OfficeWill</h1>
            <div class="subtitle">合同会社オフィスウィル (Yogyakarta)</div>
            <div class="gold-line"></div>
            <div class="cert-title">Certificate of Completion</div>
            <div class="gold-line"></div>
            <div class="content">
                <p>This is to certify that</p>
                <div class="name">{{ $employee->full_name }}</div>
                <p>has successfully completed the course</p>
                <div class="course-name">{{ $course->course_name }}</div>
                <p>on {{ $issued_at->format('F d, Y') }}</p>
            </div>
            <div class="seal">
                <svg viewBox="0 0 64 64" width="60" height="60">
                    <rect x="2" y="2" width="60" height="60" rx="30" fill="#380812" stroke="#D4A017" stroke-width="2"/>
                    <rect x="12" y="12" width="40" height="40" rx="20" fill="none" stroke="rgba(212,160,23,0.4)" stroke-width="1.2"/>
                    <rect x="22" y="22" width="20" height="20" rx="10" fill="none" stroke="rgba(212,160,23,0.3)" stroke-width="1"/>
                </svg>
            </div>
            <div class="footer">
                <div>
                    <p><strong>OfficeWill LLC</strong></p>
                    <p>Yogyakarta Branch</p>
                </div>
                <div class="cert-number">
                    <p>{{ $certificate_number }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
