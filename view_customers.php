<?php
include "db.php";

// Initialize variables
$customers = [];
$error_message = "";

try {
    // Check database connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Fetch all customers from the database
    $stmt = $conn->prepare("SELECT * FROM customers");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result === false) {
        throw new Exception("Get result failed: " . $stmt->error);
    }
    
    $customers = $result->fetch_all(MYSQLI_ASSOC);
    
    // Close statement
    $stmt->close();
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
} finally {
    // Close connection (if you're not reusing it elsewhere)
    // $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customers - Vehicle Rental Management</title>
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

        .customers-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #f8fafc;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .customers-table th,
        .customers-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .customers-table th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .customers-table tr:last-child td {
            border-bottom: none;
        }

        .customers-table tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .customer-name {
            font-weight: 600;
            color: #2d3748;
        }

        .customer-phone {
            color: #4a5568;
        }

        .customer-email {
            color: #667eea;
            word-break: break-all;
        }

        .customer-date {
            color: #718096;
            font-size: 0.9rem;
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

        .no-customers {
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

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 10px;
        }

        .pagination-btn {
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #4a5568;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e0;
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: transparent;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
                margin: 10px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .customers-table {
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
            <h1>Customer Management</h1>
            <p>View and manage all registered customers</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="message error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="actions-bar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Search customers...">
            </div>
            <a href="add_customer.php" class="add-btn">Add New Customer</a>
        </div>

        <?php if (!empty($customers)): ?>
            <div class="table-responsive">
                <table class="customers-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                        
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td class="customer-name"><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td>
                                    <div class="customer-phone"><?php echo htmlspecialchars($customer['phone']); ?></div>
                                    <?php if (!empty($customer['email'])): ?>
                                        <div class="customer-email"><?php echo htmlspecialchars($customer['email']); ?></div>
                                    <?php endif; ?>
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

            <div class="pagination">
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn">Next →</button>
            </div>
        <?php else: ?>
            <div class="no-customers">
                <p>No customers found. Would you like to <a href="add_customer.php">add a new customer</a>?</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.querySelector('.search-input');
            const customerRows = document.querySelectorAll('.customers-table tbody tr');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                customerRows.forEach(row => {
                    const name = row.querySelector('.customer-name').textContent.toLowerCase();
                    const phone = row.querySelector('.customer-phone').textContent.toLowerCase();
                    const email = row.querySelector('.customer-email') ? 
                        row.querySelector('.customer-email').textContent.toLowerCase() : '';
                    
                    if (name.includes(searchTerm) || phone.includes(searchTerm) || email.includes(searchTerm)) {
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
                    const customerName = row.querySelector('.customer-name').textContent;
                    
                    if (this.classList.contains('delete')) {
                        if (confirm(`Are you sure you want to delete ${customerName}?`)) {
                            // Here you would typically make an AJAX call to delete the customer
                            row.style.opacity = '0.5';
                            setTimeout(() => {
                                row.remove();
                                // Check if table is empty after deletion
                                if (document.querySelectorAll('.customers-table tbody tr').length === 0) {
                                    document.querySelector('.table-responsive').innerHTML = `
                                        <div class="no-customers">
                                            <p>No customers found. Would you like to <a href="add_customer.php">add a new customer</a>?</p>
                                        </div>
                                    `;
                                }
                            }, 300);
                        }
                    } else {
                        // Edit functionality would go here
                        alert(`Edit customer: ${customerName}`);
                    }
                });
            });
            
            // Pagination button handlers
            document.querySelectorAll('.pagination-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!this.classList.contains('active')) {
                        document.querySelector('.pagination-btn.active').classList.remove('active');
                        this.classList.add('active');
                        // Here you would typically load the new page of results
                    }
                });
            });
        });
    </script>
</body>
</html>