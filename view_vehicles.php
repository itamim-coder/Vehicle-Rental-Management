<?php
include "db.php";

// Initialize variables
$vehicles = [];
$error_message = "";
$success_message = "";

// Handle status updates
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_status'])) {
    try {
        $stmt = $conn->prepare("UPDATE vehicles SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $_POST['new_status'], $_POST['vehicle_id']);
        
        if ($stmt->execute()) {
            $success_message = "Vehicle status updated successfully!";
        } else {
            $error_message = "Error updating vehicle status.";
        }
    } catch (Exception $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Fetch all vehicles
try {
    $stmt = $conn->prepare("SELECT * FROM vehicles ORDER BY status, vehicle_type, model");
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicles = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Vehicles - Vehicle Rental Management</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 1200px;
            width: 100%;
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

        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            position: relative;
            flex-grow: 1;
            max-width: 400px;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 40px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #718096;
        }

        .add-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .add-btn:active {
            transform: translateY(0);
        }

        .add-btn::before {
            content: '+';
            font-size: 1.2rem;
        }

        .vehicles-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #f8fafc;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .vehicles-table th,
        .vehicles-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .vehicles-table th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .vehicles-table tr:last-child td {
            border-bottom: none;
        }

        .vehicles-table tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .vehicle-type {
            font-weight: 600;
            color: #2d3748;
        }

        .vehicle-model {
            color: #4a5568;
        }

        .vehicle-reg {
            font-family: monospace;
            color: #2d3748;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-available {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .status-rented {
            background-color: #fed7d7;
            color: #742a2a;
        }

        .status-maintenance {
            background-color: #feebc8;
            color: #7b341e;
        }

        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .status-select {
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            background: white;
            transition: all 0.3s ease;
        }

        .status-select:focus {
            outline: none;
            border-color: #667eea;
        }

        .update-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .update-btn:hover {
            background: #5a67d8;
        }

        .action-btn {
            background: none;
            border: none;
            color: #667eea;
            cursor: pointer;
            font-size: 1rem;
            margin: 0 5px;
            transition: all 0.2s ease;
            padding: 5px;
            border-radius: 5px;
        }

        .action-btn:hover {
            color: #764ba2;
            background: rgba(102, 126, 234, 0.1);
        }

        .action-btn.delete {
            color: #e53e3e;
        }

        .action-btn.delete:hover {
            background: rgba(229, 62, 62, 0.1);
        }

        .no-vehicles {
            text-align: center;
            padding: 40px;
            color: #718096;
            font-size: 1.1rem;
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

        .success-message {
            background: #f0fff4;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .success-message::before {
            content: '✓';
            margin-right: 10px;
            font-weight: bold;
            color: #38a169;
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

            .vehicles-table {
                display: block;
                overflow-x: auto;
            }

            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                max-width: 100%;
            }

            .add-btn {
                width: 100%;
                justify-content: center;
            }

            .status-form {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">Back to Dashboard</a>
        
        <div class="header">
            <h1>Vehicle Management</h1>
            <p>View and manage all vehicles in the fleet</p>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="message success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="message error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="actions-bar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Search vehicles...">
            </div>
            <a href="add_vehicle.php" class="add-btn">Add New Vehicle</a>
        </div>

        <?php if (!empty($vehicles)): ?>
            <div class="table-responsive">
                <table class="vehicles-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Registration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td class="vehicle-type"><?php echo htmlspecialchars($vehicle['vehicle_type']); ?></td>
                                <td class="vehicle-model"><?php echo htmlspecialchars($vehicle['model']); ?></td>
                                <td class="vehicle-reg"><?php echo htmlspecialchars($vehicle['registration_no']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($vehicle['status']); ?>">
                                        <?php echo htmlspecialchars($vehicle['status']); ?>
                                    </span>
                                    <form method="post" class="status-form">
                                        <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                                        <select name="new_status" class="status-select">
                                            <option value="Available" <?php echo $vehicle['status'] === 'Available' ? 'selected' : ''; ?>>Available</option>
                                            <option value="Rented" <?php echo $vehicle['status'] === 'Rented' ? 'selected' : ''; ?>>Rented</option>
                                            <option value="Maintenance" <?php echo $vehicle['status'] === 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                        </select>
                                        <button type="submit" name="update_status" class="update-btn">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <button class="action-btn" title="Edit">✏️</button>
                                    <button class="action-btn delete" title="Delete">🗑️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-vehicles">
                <p>No vehicles found. Would you like to <a href="add_vehicle.php">add a new vehicle</a>?</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.querySelector('.search-input');
            const vehicleRows = document.querySelectorAll('.vehicles-table tbody tr');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                vehicleRows.forEach(row => {
                    const type = row.querySelector('.vehicle-type').textContent.toLowerCase();
                    const model = row.querySelector('.vehicle-model').textContent.toLowerCase();
                    const reg = row.querySelector('.vehicle-reg').textContent.toLowerCase();
                    const status = row.querySelector('.status-badge').textContent.toLowerCase();
                    
                    if (type.includes(searchTerm) || model.includes(searchTerm) || 
                        reg.includes(searchTerm) || status.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            
            // Add confirmation for delete actions
            document.querySelectorAll('.action-btn.delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const vehicleModel = row.querySelector('.vehicle-model').textContent;
                    
                    if (confirm(`Are you sure you want to delete ${vehicleModel}? This action cannot be undone.`)) {
                        // Here you would typically make an AJAX call to delete the vehicle
                        row.style.opacity = '0.5';
                        setTimeout(() => {
                            row.remove();
                            // Check if table is empty after deletion
                            if (document.querySelectorAll('.vehicles-table tbody tr').length === 0) {
                                document.querySelector('.table-responsive').innerHTML = `
                                    <div class="no-vehicles">
                                        <p>No vehicles found. Would you like to <a href="add_vehicle.php">add a new vehicle</a>?</p>
                                    </div>
                                `;
                            }
                        }, 300);
                    }
                });
            });
        });
    </script>
</body>
</html>