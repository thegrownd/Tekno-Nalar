<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel Baru dari TeknoNalar</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background-color: #f8fafc; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h2 style="color: #0891b2; margin: 0;">TeknoNalar</h2>
    </div>

    <div style="background-color: #ffffff; padding: 20px; border: 1px solid #e2e8f0; border-radius: 0 0 8px 8px;">
        <p>Halo <strong>{{ $subscriber->name }}</strong>,</p>
        
        <p>Kami baru saja menerbitkan artikel baru yang mungkin Anda sukai!</p>

        <h1 style="font-size: 24px; color: #1e293b; margin-top: 20px;">{{ $article->title }}</h1>
        
        <p style="color: #64748b; font-style: italic;">
            {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 150) }}
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/articles/' . $article->id) }}" style="background-color: #06b6d4; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Baca Artikel Lengkap
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #94a3b8; text-align: center;">
            Anda menerima email ini karena Anda berlangganan newsletter TeknoNalar.<br>
            Jika Anda ingin berhenti berlangganan, silakan <a href="{{ $unsubscribeUrl }}" style="color: #06b6d4;">klik di sini</a>.
        </p>
    </div>

</body>
</html>
