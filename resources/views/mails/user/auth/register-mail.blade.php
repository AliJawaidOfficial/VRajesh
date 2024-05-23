<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <p>Hello {{ $name }}</p>
    <p>Please click on the link below to verify your email</p>
    <a href="{{ $url }}" target="_blank">Verify</a>
</body>

</html>
