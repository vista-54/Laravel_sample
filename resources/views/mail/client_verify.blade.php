<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{url('css/register.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
</head>
<body>
<p>Hi.</p>
<p>Click this link to confirm your email: <a href="{{ url('api/client/verify', $verification_code) }}">Link</a></p>
</body>
</html>