<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Challenge</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #9a8211 0%, #7a6b1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .security-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #9a8211 0%, #b8951f 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            position: relative;
            overflow: hidden;
        }

        .security-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .header {
            margin-bottom: 32px;
        }

        .header h1 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            color: #718096;
            font-size: 16px;
            line-height: 1.5;
        }

        .alert-error {
            background: linear-gradient(135deg, #fed7d7, #feb2b2);
            color: #742a2a;
            border: 1px solid #f56565;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            text-align: left;
        }

        .verification-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 32px 24px;
            margin-bottom: 24px;
        }

        .phone-illustration {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #e2e8f0, #cbd5e0);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            position: relative;
        }

        .phone-illustration::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 8px;
            height: 8px;
            background: #9a8211;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.5; transform: translate(-50%, -50%) scale(1.2); }
        }

        .instruction-text {
            color: #4a5568;
            font-size: 16px;
            margin-bottom: 24px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .code-input-container {
            position: relative;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px;
            border: 3px solid #e2e8f0;
            border-radius: 12px;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 8px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: #9a8211;
            box-shadow: 0 0 0 4px rgba(154, 130, 17, 0.1);
            transform: scale(1.02);
        }

        .form-group input::placeholder {
            color: #cbd5e0;
            letter-spacing: 8px;
        }

        .input-helper {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9a8211;
            font-size: 18px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .form-group input:focus + .input-helper {
            opacity: 1;
        }

        .error-message {
            color: #e53e3e;
            font-size: 14px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-weight: 500;
        }

        .button {
            width: 100%;
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .button-primary {
            background: linear-gradient(135deg, #9a8211 0%, #b8951f 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(154, 130, 17, 0.2);
        }

        .button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(154, 130, 17, 0.3);
        }

        .button-primary:active {
            transform: translateY(0);
        }

        .button-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .button-primary:hover::before {
            left: 100%;
        }

        .loading-dots {
            display: none;
            gap: 4px;
        }

        .loading-dots span {
            width: 6px;
            height: 6px;
            background: currentColor;
            border-radius: 50%;
            animation: loading 1.4s infinite ease-in-out;
        }

        .loading-dots span:nth-child(1) { animation-delay: -0.32s; }
        .loading-dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes loading {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        .help-text {
            color: #718096;
            font-size: 14px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
        }

        .countdown {
            color: #9a8211;
            font-weight: 600;
            font-size: 14px;
            margin-top: 12px;
        }

        @media (max-width: 640px) {
            .container {
                padding: 24px;
                margin: 10px;
            }

            .header h1 {
                font-size: 24px;
            }

            .form-group input {
                font-size: 20px;
                letter-spacing: 6px;
                padding: 14px 16px;
            }

            .form-group input::placeholder {
                letter-spacing: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="security-icon">🔐</div>
        
        <div class="header">
            <h1>Security Verification</h1>
            <p>We need to verify your identity to keep your account secure</p>
        </div>

        @if (session('error'))
            <div class="alert-error">
                <span>⚠️</span>
                {{ session('error') }}
            </div>
        @endif

        <div class="verification-section">
            <div class="phone-illustration">📱</div>
            <div class="instruction-text">
                Open your authenticator app and enter the 6-digit verification code
            </div>

            <form action="{{ route('2fa.verify') }}" method="POST" id="verificationForm">
                @csrf
                <div class="form-group">
                    <label for="code">Verification Code</label>
                    <div class="code-input-container">
                        <input 
                            type="text" 
                            name="code" 
                            id="code" 
                            maxlength="6" 
                            pattern="[0-9]{6}"
                            placeholder="000000"
                            required
                            autocomplete="off"
                            inputmode="numeric"
                        >
                        <div class="input-helper">🔍</div>
                    </div>
                    @error('code')
                        <div class="error-message">
                            <span>❌</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <button type="submit" class="button button-primary" id="verifyButton">
                    <span class="button-text">
                        <span>🔓</span>
                        Verify & Continue
                    </span>
                    <div class="loading-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
            </form>

            <div class="help-text">
                <strong>Don't have access to your authenticator app?</strong><br>
                Contact your administrator for assistance with account recovery.
            </div>
            
            <div class="countdown" id="countdown">
                Code expires in: <span id="timer">5:00</span>
            </div>
        </div>
    </div>

    <script>
        // Format code input as user types
        const codeInput = document.getElementById('code');
        const verifyButton = document.getElementById('verifyButton');
        const form = document.getElementById('verificationForm');
        
        codeInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 6) value = value.substring(0, 6);
            e.target.value = value;
            
            // Enable/disable button based on input length
            if (value.length === 6) {
                verifyButton.style.opacity = '1';
                verifyButton.style.pointerEvents = 'auto';
            } else {
                verifyButton.style.opacity = '0.7';
                verifyButton.style.pointerEvents = 'none';
            }
        });

        // Auto-submit when 6 digits are entered
        codeInput.addEventListener('input', function(e) {
            if (e.target.value.length === 6) {
                setTimeout(() => {
                    form.submit();
                }, 800);
            }
        });

        // Loading state on form submission
        form.addEventListener('submit', function() {
            const buttonText = verifyButton.querySelector('.button-text');
            const loadingDots = verifyButton.querySelector('.loading-dots');
            
            buttonText.style.display = 'none';
            loadingDots.style.display = 'flex';
            verifyButton.disabled = true;
        });

        // Countdown timer (5 minutes)
        let timeLeft = 300; // 5 minutes in seconds
        const timerElement = document.getElementById('timer');
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                document.getElementById('countdown').innerHTML = 
                    '<span style="color: #e53e3e;">⏰ Code expired - please refresh to get a new one</span>';
                codeInput.disabled = true;
                verifyButton.disabled = true;
                return;
            }
            
            timeLeft--;
        }
        
        // Update timer every second
        setInterval(updateTimer, 1000);
        updateTimer(); // Initial call

        // Focus input on load
        window.addEventListener('load', function() {
            codeInput.focus();
        });

        // Initially disable button
        verifyButton.style.opacity = '0.7';
        verifyButton.style.pointerEvents = 'none';
    </script>
</body>
</html>