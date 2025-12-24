<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome Email</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
      color: #333;
    }
    .container {
		background-color: #f9f9f9;
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .logo {
      width: 120px;
      margin-bottom: 30px;
    }
    h1 {
      font-size: 26px;
      color: #333;
    }
    p {
      font-size: 16px;
      line-height: 1.6;
    }
    .account-details {
      margin-top: 20px;
      padding: 15px;
      background: #f1f1f1;
      border-radius: 6px;
    }
    .account-details p {
      margin: 5px 0;
    }
    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 24px;
      background: #00a9e0;
      color: #fff;
      font-weight: bold;
      text-decoration: none;
      border-radius: 6px;
    }
    .footer {
      margin-top: 40px;
      font-size: 12px;
      color: #999;
      text-align: center;
    }
    .support {
      margin-top: 20px;
      font-size: 14px;
      color: #666;
    }
    .support a {
      color: #00a9e0;
      text-decoration: none;
    }
    .view-browser {
      text-align: center;
      margin-top: 20px;
    }
    .view-browser a {
      color: #007bff;
      text-decoration: none;
      font-size: 14px;
    }
	.logo {
	  height: 50px;             /* adjust as needed */
	  background-image: url('https://alba.customized3.corsivalab.xyz/images/logo/logo-long.png');
	  background-size: contain;
	  background-repeat: no-repeat;
	}
  </style>
</head>
<body>
  <div class="container">
	  <div class="logo"></div>

    <p>Hi <strong>{{$accountDetail['name']}}</strong>,</p>
    <h1>Welcome to ALBA E-Waste Step Up! 🌱</h1>

    <p>
      We’re excited to let you know that your ALBA E-Waste Step Up account has been successfully activated!
      <br /><br />
	  Please login to ALBA Mobile Apps using your Unique ID
    </p>

    <div class="account-details">
      <p><strong>Unique ID:</strong> {{$accountDetail['username']}}</p>
	  <p><strong>Email:</strong> {{$accountDetail['email']}}</p>
	  <p><strong>Phone:</strong> {{$accountDetail['phone']}}</p>
      {{-- <p>🔒 <em>For security, please change your password after your first login.</em></p> --}}
    </div>

    {{-- <a href="#" class="btn" style="color:white">Sign in to your account</a> --}}

    <div class="support">
      <p>If you need any help getting started, our team is here to support you. Feel free to reach out at <a href="mailto:support@alba-ewaste.sg">support@alba-ewaste.sg</a>.</p>
    </div>

    <p>Thank you for partnering with us to create a greener future! 🌍♻️</p>

    <hr style="margin: 40px 0;" />

    <div class="footer">
      <p>If you did not sign up for this account you can ignore this email.</p>
      <p>&copy; <?= date('Y'); ?> <a href="https://step-up.sg/"></a>ALBA</p>
    </div>


  </div>
</body>
</html>
