
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Code Verification</title>
    <style>
        body {
            background-color: skyblue;
            color: white;
            font-family: Arial, sans-serif;
        }
        h3 {
            text-align: center;
            margin-top: 50px;
        }
        p {
            text-align: center;
            margin-top: 20px;
        }
        h2 {
            text-align: center;
            background-color: slateblue;
            padding: 10px 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h3>Dear {{ $data['name'] }},</h3>
    <p>We received a request to verify your email address with us. If you did not make this request, simply ignore this email.</p>
    <p>To verify your email, use the following code:</p>
    <h2>{{ $data['code'] }}</h2>
    <p>If you need further assistance, please feel free to reach out to us at support@example.com.</p>
    <p>Best regards,</p>
    <p>The Support Team</p>
</body>
</html>