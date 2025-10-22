<?php
include "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $reference_id = $_POST['reference_id'];
    $amount = $_POST['amount'];
    $phone = $_POST['phone'];
    
    // Here you would typically save to database
    // For now, we'll just show confirmation
    
    $transaction_data = [
        'email' => $email,
        'reference_id' => $reference_id,
        'amount' => $amount,
        'phone' => $phone,
        'timestamp' => date('Y-m-d H:i:s'),
        'status' => 'PENDING'
    ];
    
    // Store in session for display
    $_SESSION['last_transaction'] = $transaction_data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Complete - MTN MoMo Sandbox</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #00cc00;
            margin-bottom: 20px;
        }
        .success-icon {
            font-size: 48px;
            color: #00cc00;
            margin-bottom: 20px;
        }
        .transaction-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .transaction-details p {
            margin: 10px 0;
        }
        button {
            padding: 12px 30px;
            background-color: #ffcc00;
            color: black;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin: 10px;
        }
        button:hover {
            background-color: #e6b800;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>Transaction Complete!</h1>
        
        <div class="transaction-details">
            <h3>Transaction Summary:</h3>
            <p><strong>Reference ID:</strong> <?php echo $reference_id; ?></p>
            <p><strong>Amount:</strong> <?php echo $amount; ?> UGX</p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Status:</strong> PENDING (Sandbox)</p>
            <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
        
        <p><strong>Receipt will be sent to your email shortly.</strong></p>
        <p>Thank you for testing the MTN MoMo Sandbox API!</p>
        
        <div>
            <button onclick="window.location.href='payment_form.php'">Make Another Payment</button>
            <button onclick="window.location.href='requesttopaytransactionstatus.php?reference=<?php echo $reference_id; ?>'">Check Status</button>
        </div>
    </div>
</body>
</html>