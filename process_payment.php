<?php
include "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $phone = $_POST['phone'];
    
    // Format phone number correctly
    $phone = formatPhoneNumber($phone);
    
    // Generate reference ID
    $reference_id = rand(10000000, 99999999);
    
    // Store in session
    $_SESSION['payment_data'] = [
        'amount' => $amount,
        'phone' => $phone,
        'reference_id' => $reference_id,
        'currency' => 'EUR'
    ];
} else {
    header("Location: payment_form.php");
    exit();
}

// Process MoMo payment
include "createaccesstoken.php";

$url = "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay";

$headers = array(
    'Authorization: Bearer '.$access_token,
    'X-Reference-Id: '. $reference_id,
    'X-Target-Environment: sandbox',
    'Content-Type: application/json',
    'Ocp-Apim-Subscription-Key: '.$secodary_key
);

$body = array(
    'amount' => strval($amount),
    'currency' => 'EUR',
    "externalId" => strval($reference_id),
    'payer' => array(
        'partyIdType' => 'MSISDN',
        'partyId' => $phone
    ),
    'payerMessage' => 'Payment for services',
    'payeeNote' => 'Thank you for your payment'
);

$json_body = json_encode($body);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $json_body,
    CURLOPT_SSL_VERIFYPEER => false
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing - MTN MoMo Sandbox</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .success { color: #00cc00; font-size: 24px; }
        .simulation { background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: left; }
        .phone-simulation { background: #2d3436; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center; font-family: monospace; }
        .btn { padding: 12px 24px; margin: 10px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .approve-btn { background: #00b894; color: white; }
        .decline-btn { background: #d63031; color: white; }
        .status-pending { color: #f39c12; }
        .status-success { color: #00b894; }
        .status-failed { color: #d63031; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($httpcode == 202): ?>
            <div class="success">✅ Payment Request Created Successfully!</div>
            <p><strong>Reference ID:</strong> <?php echo $reference_id; ?></p>
            <p><strong>Amount:</strong> <?php echo $amount; ?> EUR</p>
            <p><strong>Phone:</strong> <?php echo $phone; ?></p>

            <div class="simulation">
                <h3>🧪 Sandbox Simulation</h3>
                <p>In the <strong>Sandbox environment</strong>, no actual prompt is sent to the phone.</p>
                <p>This is a simulation of what would happen in production.</p>
            </div>

            <!-- Phone Simulation -->
            <div class="phone-simulation">
                <h4>📱 MoMo App Simulation</h4>
                <p>You have a payment request:</p>
                <p><strong>Amount: <?php echo $amount; ?> EUR</strong></p>
                <p>Reference: <?php echo $reference_id; ?></p>
                <div style="margin: 20px 0;">
                    <button class="btn approve-btn" onclick="simulatePayment('APPROVE')">✅ Approve Payment</button>
                    <button class="btn decline-btn" onclick="simulatePayment('DECLINE')">❌ Decline Payment</button>
                </div>
            </div>

            <!-- Status Checker -->
            <div id="status-section">
                <h4>Payment Status: <span id="status-text" class="status-pending">PENDING</span></h4>
                <div id="status-details"></div>
            </div>

            <!-- Email Collection -->
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                <h3>📧 Get Receipt via Email</h3>
                <form method="POST" action="save_transaction.php" id="email-form">
                    <input type="email" name="email" placeholder="your@email.com" required 
                           style="padding: 10px; width: 70%; margin: 10px;">
                    <input type="hidden" name="reference_id" value="<?php echo $reference_id; ?>">
                    <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                    <input type="hidden" name="status" id="payment-status" value="PENDING">
                    <button type="submit" style="padding: 10px 20px;">Save Transaction</button>
                </form>
            </div>

        <?php else: ?>
            <div style="color: red; font-size: 24px;">❌ Payment Failed</div>
            <p>Status Code: <?php echo $httpcode; ?></p>
            <p>Error: <?php echo htmlspecialchars($response); ?></p>
            <button onclick="window.location.href='payment_form.php'" style="padding: 10px 20px;">
                Try Again
            </button>
        <?php endif; ?>
    </div>

    <script>
    let paymentReference = '<?php echo $reference_id; ?>';
    
    function simulatePayment(action) {
        const statusText = document.getElementById('status-text');
        const statusDetails = document.getElementById('status-details');
        const paymentStatus = document.getElementById('payment-status');
        
        if (action === 'APPROVE') {
            statusText.textContent = 'SUCCESSFUL';
            statusText.className = 'status-success';
            statusDetails.innerHTML = '<p>✅ Payment approved successfully!</p><p>Funds have been transferred.</p>';
            paymentStatus.value = 'SUCCESSFUL';
            
            // Show success message
            alert('Payment Approved! In production, user would enter PIN on their phone.');
            
        } else if (action === 'DECLINE') {
            statusText.textContent = 'FAILED';
            statusText.className = 'status-failed';
            statusDetails.innerHTML = '<p>❌ Payment was declined by user.</p>';
            paymentStatus.value = 'FAILED';
            
            alert('Payment Declined! In production, user would cancel the transaction.');
        }
        
        // Enable the email form
        document.querySelector('#email-form button').disabled = false;
    }
    
    // Simulate checking payment status (for demo purposes)
    function checkPaymentStatus() {
        // In sandbox, we simulate the status changes
        console.log('Checking payment status for:', paymentReference);
    }
    
    // Check status every 5 seconds
    setInterval(checkPaymentStatus, 5000);
    </script>
</body>
</html>

<?php
function formatPhoneNumber($phone) {
    $phone = preg_replace('/\D/', '', $phone);
    
    if (substr($phone, 0, 3) === '256') {
        return '+' . $phone;
    }
    
    if (substr($phone, 0, 1) === '0') {
        return '+256' . substr($phone, 1);
    }
    
    if (substr($phone, 0, 1) !== '+') {
        return '+' . $phone;
    }
    
    return $phone;
}
?>