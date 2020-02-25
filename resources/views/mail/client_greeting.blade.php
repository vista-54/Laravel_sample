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
<p>Hello,</p>
<p>Thank you for registering, we are delighted to have you as a privileged member.</p>
<p>Best Regards, <br>
{{$client->user->full_name}}
</p>
</body>
</html>
