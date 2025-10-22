<?php
include "config.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTN MoMo Payment</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #ffcc00; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="number"], input[type="tel"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; }
        button { width: 100%; padding: 15px; background-color: #ffcc00; color: black; border: none; border-radius: 5px; font-size: 18px; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #e6b800; }
        .info-box { background: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #ffcc00; }
        .phone-format { font-size: 12px; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📱 MTN MoMo Payment</h1>
        
        <div class="info-box">
            <strong>Important:</strong> Use a valid test phone number from MTN MoMo Sandbox
        </div>
        
        <form method="POST" action="process_payment.php">
            <div class="form-group">
                <label for="amount">Amount (UGX):</label>
                <input type="number" 
                       id="amount" 
                       name="amount" 
                       step="0.01" 
                       min="1" 
                       max="1000"
                       placeholder="Enter amount" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="phone">MTN Phone Number:</label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       value="+256701234567"
                       placeholder="+256701234567"
                       required>
                <div class="phone-format">
                    Format: +256701234567 (include + sign)<br>
                    Test numbers: +256701234567, +256711223344
                </div>
            </div>
            
            <button type="submit">
                📲 Send Payment Request to Phone
            </button>
        </form>
    </div>

    <script>
        // Auto-format phone number with + sign
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // If user types 256... automatically add +
            if (value.startsWith('256') && !e.target.value.startsWith('+')) {
                e.target.value = '+' + value;
            }
            
            // If user types 07... convert to +2567...
            if (value.startsWith('07') && value.length <= 10) {
                e.target.value = '+256' + value.substring(1);
            }
        });
    </script>
</body>
</html>