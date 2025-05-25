<?php
include "db.php";

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Insert maintenance record
        $stmt = $conn->prepare("INSERT INTO maintenance (vehicle_id, service_type, service_date, cost, description) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", 
            $_POST['vehicle'], 
            $_POST['type'], 
            $_POST['date'], 
            $_POST['cost'],
            $_POST['description']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error creating maintenance record: " . $stmt->error);
        }
        
        // Update vehicle status if needed
       if (isset($_POST['update_status'])) {

            $update_stmt = $conn->prepare("UPDATE vehicles SET status = 'Maintenance' WHERE id = ?");
            $update_stmt->bind_param("i", $_POST['vehicle']);
            
            if (!$update_stmt->execute()) {
                throw new Exception("Error updating vehicle status: " . $update_stmt->error);
            }
        }
        
        // Commit transaction
        $conn->commit();
        $success_message = "Maintenance record added successfully!";
        
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error processing maintenance: " . $e->getMessage();
    }
}

// Fetch all vehicles
try {
    $vehicles = $conn->query("SELECT id, vehicle_type, model, registration_no FROM vehicles ORDER BY vehicle_type, model");
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Maintenance - Vehicle Rental Management</title>
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
            max-width: 600px;
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

        .form-container {
            background: #f8fafc;
            border-radius: 15px;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-input:hover, .form-select:hover, .form-textarea:hover {
            border-color: #cbd5e0;
        }

        .required::after {
            content: ' *';
            color: #e53e3e;
        }

        .vehicle-option {
            padding: 8px;
        }

        .vehicle-details {
            font-size: 0.85rem;
            color: #718096;
            margin-top: 2px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }

        .checkbox-label {
            margin-left: 10px;
            color: #4a5568;
            font-size: 0.95rem;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 10px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
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

        .form-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
                margin: 10px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">Back to Dashboard</a>
        
        <div class="header">
            <h1>Add Maintenance Record</h1>
            <p>Log maintenance services for vehicles in the fleet</p>
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

        <div class="form-container">
            <form method="post" novalidate>
                <div class="form-group">
                    <label for="vehicle" class="form-label required">Vehicle</label>
                    <select id="vehicle" name="vehicle" class="form-select" required>
                        <?php while($v = $vehicles->fetch_assoc()): ?>
                            <option value="<?php echo $v['id']; ?>" class="vehicle-option">
                                <?php echo htmlspecialchars($v['vehicle_type'] . ' - ' . $v['model']); ?>
                                <div class="vehicle-details"><?php echo htmlspecialchars($v['registration_no']); ?></div>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="type" class="form-label required">Service Type</label>
                    <input 
                        type="text" 
                        id="type" 
                        name="type" 
                        class="form-input" 
                        placeholder="Enter service type (e.g., Oil Change, Brake Service)"
                        required
                        value="<?php echo isset($_POST['type']) ? htmlspecialchars($_POST['type']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="date" class="form-label required">Service Date</label>
                    <input 
                        type="date" 
                        id="date" 
                        name="date" 
                        class="form-input" 
                        required
                        value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="cost" class="form-label required">Cost</label>
                    <input 
                        type="number" 
                        id="cost" 
                        name="cost" 
                        class="form-input" 
                        placeholder="Enter service cost"
                        min="0"
                        step="0.01"
                        required
                        value="<?php echo isset($_POST['cost']) ? htmlspecialchars($_POST['cost']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-textarea" 
                        placeholder="Enter service details (optional)"
                    ><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <div class="checkbox-group">
                    <input 
                        type="checkbox" 
                        id="update_status" 
                        name="update_status" 
                        value="1"
                        checked
                    >
                    <label for="update_status" class="checkbox-label">Mark vehicle as 'In Maintenance'</label>
                </div>

                <button type="submit" class="submit-btn">Add Maintenance Record</button>
            </form>
        </div>

        <div class="form-footer">
            <p>All required fields must be completed to add a maintenance record</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced form validation
            const form = document.querySelector('form');
            const inputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
            
            // Add real-time validation feedback
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('error')) {
                        validateField(this);
                    }
                });
            });
            
            function validateField(field) {
                const value = field.value.trim();
                
                // Remove previous error styling
                field.classList.remove('error');
                field.style.borderColor = '';
                
                // Check required fields
                if (field.hasAttribute('required') && !value) {
                    field.style.borderColor = '#e53e3e';
                    field.classList.add('error');
                    return false;
                }
                
                // Validate number fields
                if (field.type === 'number') {
                    const min = field.getAttribute('min');
                    
                    if (min && parseFloat(value) < parseFloat(min)) {
                        field.style.borderColor = '#e53e3e';
                        field.classList.add('error');
                        return false;
                    }
                }
                
                // Validate date fields
                if (field.type === 'date') {
                    const selectedDate = new Date(value);
                    const today = new Date();
                    
                    if (selectedDate > today) {
                        field.style.borderColor = '#e53e3e';
                        field.classList.add('error');
                        return false;
                    }
                }
                
                field.style.borderColor = '#38a169';
                return true;
            }
            
            // Form submission validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Show validation message
                    const firstError = document.querySelector('.form-input.error, .form-select.error, .form-textarea.error');
                    if (firstError) {
                        firstError.focus();
                    }
                }
            });
        });
    </script>
</body>
</html>