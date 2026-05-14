<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #334155; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px; background: #f8fafc; border-radius: 24px; }
        .card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .logo { text-align: center; margin-bottom: 30px; }
        .logo img { height: 60px; }
        h1 { color: #1e293b; font-size: 24px; font-weight: 800; text-align: center; margin-bottom: 20px; text-transform: uppercase; letter-spacing: -0.025em; }
        p { margin-bottom: 20px; }
        .btn-container { text-align: center; margin-top: 30px; margin-bottom: 30px; }
        .btn { background: #2563eb; color: white !important; padding: 16px 32px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em; display: inline-block; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 40px; }
        .warning { font-size: 11px; color: #94a3b8; text-align: center; border-top: 1px solid #f1f5f9; pt: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h2 style="color: #1e293b; font-weight: 900;">Water<span style="color: #2563eb;">Sense</span></h2>
        </div>
        <div class="card">
            <h1>Reset Your Password</h1>
            <p>Halo,</p>
            <p>Kami menerima permintaan untuk mereset password akun WaterSense Anda. Klik tombol di bawah ini untuk melanjutkan proses pemulihan akun:</p>
            
            <div class="btn-container">
                <a href="{{ url('password/reset/'.$token.'?email='.$email) }}" class="btn">RESET PASSWORD SEKARANG</a>
            </div>

            <p>Link ini hanya berlaku selama 60 menit. Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} WaterSense IT Command Center. Karawang, Jawa Barat.<br>
            <i>Automated System Notification - Do Not Reply</i>
        </div>
        <div class="warning">
            Jika tombol tidak berfungsi, salin dan tempel link berikut ke browser Anda:<br>
            {{ url('password/reset/'.$token.'?email='.$email) }}
        </div>
    </div>
</body>
</html>
