<!DOCTYPE html>
<html>
<head>
    <title>Selamat Datang di Newsletter Kami!</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-bottom: 1px solid #e9ecef; }
        .content { padding: 30px 20px; }
        .footer { text-align: center; font-size: 12px; color: #6c757d; margin-top: 30px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #0891b2; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Selamat Datang!</h2>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Terima kasih telah berlangganan newsletter kami. Kami sangat senang Anda bergabung!</p>
            <p>Anda akan menjadi yang pertama mengetahui tentang artikel terbaru, tips teknologi, dan update keamanan dari kami.</p>
            
            <p>Jika Anda tidak merasa mendaftar, silakan abaikan email ini.</p>
            
            <p style="margin-top: 30px;">
                Salam hangat,<br>
                Tim UAP
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UAP. All rights reserved.</p>
            <p><a href="{{ $unsubscribeUrl }}" style="color: #6c757d;">Berhenti Berlangganan</a></p>
        </div>
    </div>
</body>
</html>
