<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Email Template</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .email-container {
      background-color: #ffffff;
      max-width: 600px;
      margin: 40px auto;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .logo {
      text-align: center;
      margin-bottom: 20px;
    }
    .logo img {
      max-width: 150px;
      height: auto;
    }
    .content {
      font-size: 16px;
      color: #333333;
      line-height: 1.6;
    }
    .footer {
      text-align: center;
      margin-top: 30px;
      font-size: 12px;
      color: #999999;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="logo">
      <img src="http://kblegalassociates.com/Dashboard/images/logo.png" alt="Company Logo">
    </div>
    <div class="content">
      {!! $details['body'] !!}
    </div>
    <div class="footer">
      © {{ $details['year'] }} [K & B Legal Associates]. All rights reserved.
    </div>
  </div>
</body>
</html>