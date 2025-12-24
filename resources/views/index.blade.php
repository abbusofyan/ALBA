<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEP-UP</title>
    <meta name="theme-color" content="#009dd3">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('STEP-UP_files/favicon.ico') }}" type="image/x-icon" sizes="16x16">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('STEP-UP_files/style.min.css') }}">
</head>
<body>
    <div class="bg-page">
        <div class="container">
            <!-- Header -->
            <header class="header">
                <img src="{{ asset('STEP-UP_files/logo.png') }}" alt="STEP-UP Logo">
            </header>

            <!-- Main Scan Section -->
            <div class="scan-wrapper">
                <div class="trash-container">
                    <img src="{{ asset('STEP-UP_files/trash.png') }}" alt="Trash">
                </div>

                <div class="scan-container">
                    <div class="heading">
                        <h1>Welcome to STEP UP Sustainability</h1>
                        {{-- <span>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span> --}}
                    </div>

                    <!-- QR Code Section -->
                    <div class="qr-code-wrapper">
                        <div class="image-container">
                            <img src="{{ asset('STEP-UP_files/qrcode.png') }}" alt="QR Code">
                        </div>
                        <div id="info-container" class="information hidden">
                            <p class="bin-info"><span class="bin-type"></span> Information</p>
                            <p class="bin-code">Sustainability <span class="bin-type"></span> #<span class="bin-id"></span></p>
                            <p class="bin-address"></p>
                        </div>
                    </div>

                    <!-- Redeem Section -->
                    <div class="redeem-wrapper">
                        <p class="redeem-label">Redeem CO<sub>2</sub> Points</p>
                        {{-- <p class="coming-soon">STEP UP Sustainability App Launching Soon</p> --}}
                        <p class="download-label">Download the STEP UP Sustainability App</p>
                        <div class="download-links">
                            <a href="https://apps.apple.com/us/app/alba-step-up/id1504649912" target="_blank">
                                <img src="{{ asset('STEP-UP_files/app_store.png') }}" class="mr-20" alt="App Store">
                            </a>
                            <a href="https://play.google.com/store/apps/details?id=com.app.stepup" target="_blank">
                                <img src="{{ asset('STEP-UP_files/play_store.png') }}" alt="Google Play">
                            </a>
                        </div>
                        <p class="privacy">Read more about our
                            <a href="{{ url('privacy-policy') }}">Privacy Policy</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer>
                <div class="company-info">
                    <p class="company">Singapore Trash Evolution Program</p>
                    <p class="info">A joint initiative towards a zero waste nation by,</p>
                    <img src="{{ asset('STEP-UP_files/alba.svg') }}" class="mt-10" alt="Alba">
                </div>

                <div class="contact-info flex-center">
                    <div class="contact-wrapper">
                        <div class="contact">
                            <img src="{{ asset('STEP-UP_files/call.svg') }}" class="mr-10" alt="Call">
                            <a href="tel:8008526860">Call Us: 800 852 6860</a>
                        </div>
                        <div class="contact">
                            <img src="{{ asset('STEP-UP_files/email.svg') }}" class="mr-10" alt="Email">
                            <a href="mailto:contact.sg@albagroup.asia">Email us: contact.sg@albagroup.asia</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('STEP-UP_files/jquery.min.js') }}"></script>
    <script src="{{ asset('STEP-UP_files/index.min.js') }}"></script>
</body>
</html>
