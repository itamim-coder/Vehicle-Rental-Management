<?php
include "db.php";

// Initialize variables
$maintenance_records = [];
$error_message = "";

try {
    // Check database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Fetch all maintenance records with vehicle details
    $sql = "SELECT 
                m.id,
           
                m.service_date,
                m.cost,
                m.description
            FROM maintenance m
            JOIN vehicles v ON m.vehicle_id = v.id
            ORDER BY m.service_date DESC";

    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $maintenance_records = $result->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Records - Vehicle Rental Management</title>
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

        .maintenance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #f8fafc;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .maintenance-table th,
        .maintenance-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .maintenance-table th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .maintenance-table tr:last-child td {
            border-bottom: none;
        }

        .maintenance-table tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .vehicle-info {
            font-weight: 600;
            color: #2d3748;
        }

        .vehicle-details {
            font-size: 0.85rem;
            color: #718096;
        }

        .service-type {
            color: #4a5568;
            font-weight: 500;
        }

        .service-date {
            white-space: nowrap;
        }

        .service-cost {
            font-weight: 600;
            color: #2d3748;
        }

        .service-description {
            color: #4a5568;
            font-size: 0.9rem;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

        .no-records {
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

            .maintenance-table {
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
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">Back to Dashboard</a>
        
        <div class="header">
            <h1>Maintenance Records</h1>
            <p>View and manage all vehicle maintenance history</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="message error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="actions-bar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Search maintenance records...">
            </div>
            <a href="add_maintenance.php" class="add-btn">Add Maintenance</a>
        </div>

        <?php if (!empty($maintenance_records)): ?>
            <div class="table-responsive">
                <table class="maintenance-table">
                    <thead>
                        <tr>
                            <!-- <th>Vehicle</th>
                            <th>Service Type</th> -->
                            <th>Date</th>
                            <th>Cost</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($maintenance_records as $record): ?>
                            <tr>
                                <!-- <td>
                                    <div class="vehicle-info"><?php echo htmlspecialchars($record['vehicle_type'] . ' ' . $record['model']); ?></div>
                                    <div class="vehicle-details"><?php echo htmlspecialchars($record['registration_no']); ?></div>
                                </td> -->
                                <!-- <td class="service-type"><?php echo htmlspecialchars($record['service_type']); ?></td> -->
                                <td class="service-date"><?php echo htmlspecialchars($record['service_date']); ?></td>
                                <td class="service-cost">$<?php echo number_format($record['cost'], 2); ?></td>
                                <td class="service-description" title="<?php echo htmlspecialchars($record['description']); ?>">
                                    <?php echo htmlspecialchars($record['description']); ?>
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
            <div class="no-records">
                <p>No maintenance records found. Would you like to <a href="add_maintenance.php">add a new record</a>?</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.querySelector('.search-input');
            const maintenanceRows = document.querySelectorAll('.maintenance-table tbody tr');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                maintenanceRows.forEach(row => {
                    const vehicle = row.querySelector('.vehicle-info').textContent.toLowerCase();
                    const serviceType = row.querySelector('.service-type').textContent.toLowerCase();
                    const description = row.querySelector('.service-description').textContent.toLowerCase();
                    
                    if (vehicle.includes(searchTerm) || serviceType.includes(searchTerm) || description.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            
            // Add click handlers for action buttons
            document.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const vehicle = row.querySelector('.vehicle-info').textContent;
                    const serviceType = row.querySelector('.service-type').textContent;
                    
                    if (this.classList.contains('delete')) {
                        if (confirm(`Are you sure you want to delete the maintenance record for ${vehicle} (${serviceType})?`)) {
                            // Here you would typically make an AJAX call to delete the record
                            row.style.opacity = '0.5';
                            setTimeout(() => {
                                row.remove();
                                // Check if table is empty after deletion
                                if (document.querySelectorAll('.maintenance-table tbody tr').length === 0) {
                                    document.querySelector('.table-responsive').innerHTML = `
                                        <div class="no-records">
                                            <p>No maintenance records found. Would you like to <a href="add_maintenance.php">add a new record</a>?</p>
                                        </div>
                                    `;
                                }
                            }, 300);
                        }
                    } else {
                        // Edit functionality would go here
                        alert(`Edit maintenance record for ${vehicle} (${serviceType})`);
                    }
                });
            });
        });
    </script>
</body>
</html>