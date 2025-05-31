<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Two-Factor Authentication</title>
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
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
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
        }

        .security-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #9a8211 0%, #b8951f 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #b8dacc;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da, #f1b0b7);
            color: #721c24;
            border: 1px solid #f1b2b5;
        }

        .status-enabled {
            background: linear-gradient(135deg, #e8f5e8, #d4edda);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            margin-bottom: 24px;
        }

        .status-enabled h2 {
            color: #2f855a;
            font-size: 20px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .status-enabled p {
            color: #38a169;
            margin-bottom: 16px;
        }

        .setup-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .setup-section h2 {
            color: #2d3748;
            font-size: 18px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qr-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .qr-container svg {
            max-width: 100%;
            height: auto;
        }

        .secret-key {
            background: #edf2f7;
            border-radius: 8px;
            padding: 12px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            text-align: center;
            margin: 16px 0;
            border: 2px dashed #cbd5e0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            text-align: center;
            letter-spacing: 4px;
            font-weight: 600;
        }

        .form-group input:focus {
            outline: none;
            border-color: #9a8211;
            box-shadow: 0 0 0 3px rgba(154, 130, 17, 0.1);
        }

        .error-message {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            min-width: 120px;
        }

        .button-primary {
            background: linear-gradient(135deg, #9a8211 0%, #b8951f 100%);
            color: white;
        }

        .button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(154, 130, 17, 0.3);
        }

        .button-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }

        .button-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(238, 90, 82, 0.3);
        }

        .button-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .button-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-1px);
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
            position: relative;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #718096;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .step.active .step-number {
            background: linear-gradient(135deg, #9a8211 0%, #b8951f 100%);
            color: white;
        }

        .step-text {
            font-size: 12px;
            color: #718096;
            text-align: center;
        }

        .step.active .step-text {
            color: #2d3748;
            font-weight: 600;
        }

        .step-line {
            position: absolute;
            top: 16px;
            left: 50%;
            right: -50%;
            height: 2px;
            background: #e2e8f0;
            z-index: -1;
        }

        .step:last-child .step-line {
            display: none;
        }

        @media (max-width: 640px) {
            .container {
                padding: 24px;
                margin: 10px;
            }

            .header h1 {
                font-size: 24px;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="security-icon">🛡️</div>
            <h1>Two-Factor Authentication</h1>
            <p>Secure your account with an extra layer of protection</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <span>✅</span>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <span>❌</span>
                {{ session('error') }}
            </div>
        @endif

        @if (auth()->user()->google2fa_enabled)
            <div class="status-enabled">
                <h2>
                    <span>✅</span>
                    Two-Factor Authentication Enabled
                </h2>
                <p>Your account is protected with 2FA</p>
                <form action="{{ route('2fa.disable') }}" method="POST">
                    @csrf
                    <button type="submit" class="button button-danger">
                        <span>🔓</span>
                        Disable 2FA
                    </button>
                </form>
            </div>
        @else
            <div class="steps">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-text">Scan QR Code</div>
                    <div class="step-line"></div>
                </div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <div class="step-text">Enter Code</div>
                    <div class="step-line"></div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-text">Complete</div>
                </div>
            </div>

            <div class="setup-section">
                <h2>
                    <span>📱</span>
                    Step 1: Scan QR Code
                </h2>
                <p style="color: #718096; margin-bottom: 16px;">
                    Open your authenticator app (Google Authenticator, Authy, etc.) and scan the QR code below:
                </p>
                
                <div class="qr-container">
                    {!! $qrCodeSvg !!}
                </div>

                <p style="color: #718096; text-align: center; margin-bottom: 8px;">
                    Can't scan the code? Enter this secret manually:
                </p>
                <div class="secret-key">{{ $secretKey }}</div>
            </div>

            <div class="setup-section">
                <h2>
                    <span>🔢</span>
                    Step 2: Verify Setup
                </h2>
                
                <form action="{{ route('2fa.enable') }}" method="POST">
                    @csrf
                    <input type="hidden" name="secret" value="{{ $secretKey }}">
                    
                    <div class="form-group">
                        <label for="code">Enter the 6-digit code from your authenticator app:</label>
                        <input 
                            type="text" 
                            name="code" 
                            id="code" 
                            maxlength="6" 
                            pattern="[0-9]{6}"
                            placeholder="000000"
                            required
                            autocomplete="off"
                        >
                        @error('code')
                            <div class="error-message">
                                <span>⚠️</span>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="actions">
                        <button type="submit" class="button button-primary">
                            <span>🔐</span>
                            Enable 2FA
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="back-link">
            <a href="{{ route('dashboard') }}" class="button button-secondary">
                <span>←</span>
                Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        // Format code input as user types
        document.getElementById('code')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 6) value = value.substring(0, 6);
            e.target.value = value;
        });

        // Auto-submit when 6 digits are entered
        document.getElementById('code')?.addEventListener('input', function(e) {
            if (e.target.value.length === 6) {
                // Small delay to show the complete code
                setTimeout(() => {
                    e.target.form.submit();
                }, 500);
            }
        });
    </script>
</body>
</html>