<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Sentinel WaterSense</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #1e293b; line-height: 1.6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { background: #0f172a; padding: 40px; text-align: center; color: #ffffff; }
        .header img { width: 60px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; }
        .header p { margin: 10px 0 0; font-size: 10px; font-weight: 700; color: #38bdf8; letter-spacing: 3px; text-transform: uppercase; }
        .content { padding: 40px; }
        .welcome-text { font-size: 18px; font-weight: 700; margin-bottom: 10px; color: #0f172a; }
        .instruction { font-size: 14px; color: #64748b; margin-bottom: 30px; }
        .credential-card { background: #f1f5f9; border-radius: 20px; padding: 30px; border: 1px solid #e2e8f0; margin-bottom: 30px; }
        .credential-item { margin-bottom: 15px; }
        .credential-item:last-child { margin-bottom: 0; }
        .label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px; }
        .value { font-size: 16px; font-weight: 700; color: #1e293b; font-family: 'Courier New', Courier, monospace; }
        .role-badge { display: inline-block; padding: 4px 12px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; background: #3b82f6; color: #ffffff; margin-top: 10px; }
        .footer { padding: 30px; text-align: center; border-top: 1px solid #f1f5f9; font-size: 11px; color: #94a3b8; }
        .btn { display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff !important; text-decoration: none; border-radius: 16px; font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 12px rgba(59,130,246,0.3); }
        .security-note { font-size: 11px; color: #94a3b8; font-style: italic; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://cdn-icons-png.flaticon.com/512/3105/3105807.png" alt="WaterSense Logo">
            <h1>Water<span style="color: #38bdf8;">Sense</span></h1>
            <p>IT Command Center Access</p>
        </div>
        
        <div class="content">
            <div class="welcome-text">Halo, {{ $user->name }}!</div>
            <div class="instruction">Akun Sentinel Anda telah berhasil didaftarkan ke sistem pemantauan WaterSense. Silakan gunakan kredensial berikut untuk masuk ke dashboard:</div>
            
            <div class="credential-card">
                <div class="credential-item">
                    <span class="label">Alamat Email</span>
                    <span class="value">{{ $user->email }}</span>
                </div>
                <div class="credential-item">
                    <span class="label">Password Sementara</span>
                    <span class="value">{{ $password }}</span>
                </div>
                <div class="credential-item">
                    <span class="label">Kasta Akses (Role)</span>
                    <span class="role-badge">{{ $user->role }}</span>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="btn">Masuk ke Dashboard</a>
                <p class="security-note">Demi alasan keamanan, mohon segera ganti password Anda setelah berhasil masuk pertama kali.</p>
            </div>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} WaterSense Monitoring System. Karawang, Jawa Barat.<br>
            Sistem ini dipantau secara otomatis oleh Sentinel IT Command Center.
        </div>
    </div>
</body>
</html>
