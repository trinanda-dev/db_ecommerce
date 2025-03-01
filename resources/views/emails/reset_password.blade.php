<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h3>Halo, {{ $data['name'] }}</h3>
    <p>Kami menerima permintaan reset password untuk akun Anda.</p>
    <p>Salin kode token berikut dan tempelkan pada halaman token</p>
    <a href="{{ $data['reset_url'] }}">{{ $data['reset_url'] }}</a>
    <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
</body>
</html>
