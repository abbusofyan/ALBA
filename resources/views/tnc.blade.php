<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - ALBA STEP UP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Quicksand', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 3rem;
        }

        .logo {
            max-width: 200px;
            height: auto;
        }

        h1 {
            font-size: 2.5rem;
            color: #1e3a8a;
            margin-bottom: 2rem;
            font-weight: 700;
        }

        .section-heading {
            font-size: 1.2rem;
            color: #1e3a8a;
            font-weight: 700;
            margin: 2rem 0 1rem 0;
        }

        .subsection-heading {
            font-size: 1rem;
            color: #1e3a8a;
            font-weight: 700;
            font-style: italic;
            margin: 1.5rem 0 0.5rem 0;
        }

        p {
            margin-bottom: 1rem;
            text-align: justify;
        }

        ul {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }

        li {
            margin-bottom: 0.5rem;
        }

        .intro-text {
            font-size: 1rem;
            margin-bottom: 2rem;
            background-color: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #1e3a8a;
        }

        .warning-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .warning-box h3 {
            color: #dc2626;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .highlight-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-left: 4px solid #0284c7;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .contact-info {
            background-color: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 2rem;
            border-left: 4px solid #1e3a8a;
        }

        .contact-info p {
            margin-bottom: 0.5rem;
        }

        .contact-email {
            color: #1e3a8a;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 15px;
            }

            h1 {
                font-size: 2rem;
            }

            .section-heading {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
			<img src="{{asset('images/logo/logo-long.png')}}" alt="ALBA E-Waste Singapore" class="logo">
        </div>

        <h1>Terms and Conditions</h1>

        <div class="intro-text">
            <p><strong>Before using the ALBA STEP UP app, please read and accept the following terms:</strong></p>
        </div>

        <div class="section-heading">Purpose of the App</div>
        <p>The ALBA STEP UP app is developed by ALBA to support recycling efforts in Singapore. Users can track their recycling activities, earn CO₂ points, and redeem rewards through the platform.</p>

        <div class="section-heading">Data Collection & Usage</div>
        <p>By using this app, you consent to ALBA collecting and using your personal data (e.g. name, email, location, recycling activity) to:</p>
        <ul>
            <li>Track your recycling contributions</li>
            <li>Award and manage your CO₂ points</li>
            <li>Improve user experience and service offerings</li>
            <li>Communicate important updates or promotions</li>
        </ul>
        <p>Your data will be managed in accordance with the Personal Data Protection Act (PDPA) of Singapore.</p>

        <div class="section-heading">User Responsibilities</div>
        <p>You agree to:</p>
        <ul>
            <li>Submit accurate and honest data</li>
            <li>Use the app for its intended purpose</li>
            <li>Refrain from any misuse or abuse of the CO₂ point system</li>
        </ul>

        <div class="section-heading">Fraudulent Activity & Account Termination</div>
        <div class="warning-box">
            <h3>Important Notice</h3>
            <p>ALBA takes integrity seriously. If any fraudulent activity is detected—such as falsified submissions or attempts to game the system for additional CO₂ points—ALBA reserves the right to:</p>
        </div>
        <ul>
            <li>Investigate the account activity</li>
            <li>Forfeit all accumulated points and rewards</li>
            <li>Suspend or permanently terminate the user's account without prior notice</li>
        </ul>

        <div class="section-heading">CO₂ Points & Rewards</div>
        <div class="highlight-box">
            <p><strong>Important Information about CO₂ Points:</strong></p>
            <ul>
                <li>CO₂ points are non-transferable and have no cash value</li>
                <li>Rewards are subject to availability and may be changed or withdrawn without notice</li>
                <li>ALBA reserves the right to amend the point system or redemption mechanics at any time</li>
            </ul>
        </div>

        <div class="section-heading">Limitation of Liability</div>
        <p>ALBA shall not be held liable for any loss or damage arising from use of the app, including point calculation issues, missed redemptions, or data inaccuracies.</p>

        <div class="section-heading">Changes to Terms</div>
        <p>These terms may be updated from time to time. Continued use of the app implies acceptance of any revised terms.</p>

        <div class="contact-info">
            <p>For questions about these Terms and Conditions, please contact us at:</p>
            <p class="contact-email">contact@alba-ewaste.sg</p>
        </div>
    </div>
</body>
</html>
