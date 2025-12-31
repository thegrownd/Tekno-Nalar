<!DOCTYPE html>
<html>
<head>
    <title>Kode Verifikasi Anda</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #0056b3;">Verifikasi Email Anda</h2>
        <p>Halo,</p>
        <p>Terima kasih telah mendaftar. Gunakan kode OTP berikut untuk memverifikasi alamat email Anda:</p>
        
        <div style="background-color: #f4f4f4; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0;">
            <span style="font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #333;">{{ $otp }}</span>
        </div>
        
        <p>Kode ini berlaku selama 15 menit.</p>
        <p>Jika Anda tidak merasa melakukan pendaftaran ini, abaikan email ini.</p>
        
        <br>
        <p>Salam,</p>
        <p><strong>Tim {{ config('app.name') }}</strong></p>
    </div>
</body>
</html>
