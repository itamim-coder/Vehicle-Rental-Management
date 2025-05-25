<?php
// Initialize variables
$error_message = '';
$rentals = [];

// Database connection with error handling
try {
    // Adjust the path according to your file structure
    include 'db.php'; // Changed from 'includes/db.php' to 'db.php'
    
    // Check if connection was successful
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Fetch all rentals with joined customer and vehicle data
    $sql = "SELECT 
                rentals.id, 
                customers.name AS customer_name, 
                customers.phone AS customer_phone,
                vehicles.vehicle_type,
                vehicles.model AS vehicle_model, 
                vehicles.registration_no,
                rentals.rental_days, 
                rentals.total_cost, 
                rentals.payment_status, 
                rentals.rental_date 
            FROM rentals
            JOIN customers ON rentals.customer_id = customers.id
            JOIN vehicles ON rentals.vehicle_id = vehicles.id
            ORDER BY rentals.rental_date DESC";

    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $rentals = $result->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rentals - Vehicle Rental Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: #2d3748;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #718096;
            font-size: 1rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #764ba2;
            transform: translateX(-5px);
        }

        .back-link::before {
            content: '←';
            margin-right: 8px;
            font-size: 1.2rem;
        }

        .rentals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #f8fafc;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .rentals-table th,
        .rentals-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .rentals-table th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .rentals-table tr:last-child td {
            border-bottom: none;
        }

        .rentals-table tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .customer-info {
            font-weight: 600;
            color: #2d3748;
        }

        .customer-phone {
            font-size: 0.85rem;
            color: #718096;
        }

        .vehicle-info {
            color: #4a5568;
        }

        .vehicle-reg {
            font-family: monospace;
            font-size: 0.85rem;
            color: #718096;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-success {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .badge-warning {
            background-color: #feebc8;
            color: #7b341e;
        }

        .no-rentals {
            text-align: center;
            padding: 40px;
            color: #718096;
            font-size: 1.1rem;
            background: #f8fafc;
            border-radius: 15px;
        }

        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-message {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }

        .error-message::before {
            content: '⚠';
            margin-right: 10px;
            font-weight: bold;
            color: #e53e3e;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
                margin: 10px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .rentals-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">Back to Dashboard</a>
        
        <div class="header">
            <h1>Rental Records</h1>
            <p>View all vehicle rental transactions</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="message error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($rentals)): ?>
            <div class="table-responsive">
                <table class="rentals-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Vehicle</th>
                            <th>Rental Days</th>
                            <th>Total Cost</th>
                            <th>Payment Status</th>
                            <th>Rental Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rentals as $rental): ?>
                            <tr>
                                <td>
                                    <div class="customer-info"><?php echo htmlspecialchars($rental['customer_name']); ?></div>
                                    <div class="customer-phone"><?php echo htmlspecialchars($rental['customer_phone']); ?></div>
                                </td>
                                <td>
                                    <div class="vehicle-info"><?php echo htmlspecialchars($rental['vehicle_type'] . ' ' . $rental['vehicle_model']); ?></div>
                                    <div class="vehicle-reg"><?php echo htmlspecialchars($rental['registration_no']); ?></div>
                                </td>
                                <td><?php echo $rental['rental_days']; ?></td>
                                <td>$<?php echo number_format($rental['total_cost'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $rental['payment_status'] === 'Paid' ? 'success' : 'warning'; ?>">
                                        <?php echo htmlspecialchars($rental['payment_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($rental['rental_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-rentals">
                <p>No rental records found.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>