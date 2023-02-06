<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Restore</title>
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
        a {
            display: block;
            text-align: center;
            background-color: slateblue;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h3>Dear {{ $data['name'] }},</h3>
    <p>We received a request to reset your password for your account with us. If you did not make this request, simply ignore this email.</p>
    <p>To reset your password, we generate for you a temparery password till you changed id:</p>
    <!-- <a href="{{ $reset_link }}">Reset Password</a> -->
    <h2>$data['password']</h2>
    <p>If you need further assistance, please feel free to reach out to us at support@example.com.</p>
    <p>Best regards,</p>
    <p>The Support Team</p>
</body>
</html>