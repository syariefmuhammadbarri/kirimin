<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #1e40af; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .body { padding: 40px 30px; color: #333; }
        .otp-box { background: #f0f4ff; border: 2px dashed #93c5fd; border-radius: 12px; padding: 25px; text-align: center; margin: 25px 0; }
        .otp-code { font-size: 42px; font-weight: 800; letter-spacing: 12px; color: #1e40af; font-family: 'Courier New', monospace; }
        .footer { padding: 20px 30px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; }
        .info-text { color: #6b7280; font-size: 14px; line-height: 1.6; }
        .expiry { background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; margin-top: 20px; font-size: 13px; color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Verifikasi Akun</h1>
        </div>
        <div class="body">
            <p style="font-size: 16px; font-weight: 600; color: #111827;">Halo, {{ $name }}!</p>
            <p class="info-text">Terima kasih telah mendaftar di BAZMA Express. Gunakan kode verifikasi di bawah ini untuk mengaktifkan akun Anda.</p>
            
            <div class="otp-box">
                <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px;">Kode Verifikasi</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <div class="expiry">
                ⏰ Kode ini berlaku selama <strong>30 menit</strong> sejak email ini dikirim. Jangan bagikan kode ini kepada siapa pun.
            </div>

            <p class="info-text" style="margin-top: 24px;">
                Jika Anda tidak merasa mendaftar di BAZMA Express, abaikan email ini.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} BAZMA Express. All rights reserved.<br>
            Email ini dikirim otomatis, jangan membalas email ini.
        </div>
    </div>
</body>
</html>