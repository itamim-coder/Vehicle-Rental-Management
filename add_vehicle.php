<?php
include "db.php";

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $stmt = $conn->prepare("INSERT INTO vehicles (vehicle_type, model, registration_no, status) VALUES (?, ?, ?, 'Available')");
        $stmt->bind_param("sss", $_POST['type'], $_POST['model'], $_POST['reg']);
        
        if ($stmt->execute()) {
            $success_message = "Vehicle added successfully!";
        } else {
            $error_message = "Error adding vehicle. Please try again.";
        }
    } catch (Exception $e) {
        $error_message = "Database error occurred. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle - Vehicle Rental Management</title>
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

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-input:hover {
            border-color: #cbd5e0;
        }

        .required::after {
            content: ' *';
            color: #e53e3e;
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
            <h1>Add New Vehicle</h1>
            <p>Register a new vehicle in the rental management system</p>
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
                    <label for="type" class="form-label required">Vehicle Type</label>
                    <input 
                        type="text" 
                        id="type" 
                        name="type" 
                        class="form-input" 
                        placeholder="Enter vehicle type (e.g., Sedan, SUV)"
                        required
                        value="<?php echo isset($_POST['type']) ? htmlspecialchars($_POST['type']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="model" class="form-label required">Model</label>
                    <input 
                        type="text" 
                        id="model" 
                        name="model" 
                        class="form-input" 
                        placeholder="Enter vehicle model"
                        required
                        value="<?php echo isset($_POST['model']) ? htmlspecialchars($_POST['model']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="reg" class="form-label required">Registration Number</label>
                    <input 
                        type="text" 
                        id="reg" 
                        name="reg" 
                        class="form-input" 
                        placeholder="Enter registration number"
                        required
                        value="<?php echo isset($_POST['reg']) ? htmlspecialchars($_POST['reg']) : ''; ?>"
                    >
                </div>

                <button type="submit" class="submit-btn">Add Vehicle</button>
            </form>
        </div>

        <div class="form-footer">
            <p>All required fields must be completed to register a new vehicle</p>
        </div>
    </div>

    <script>
        // Enhanced form validation and UX
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = document.querySelectorAll('.form-input');
            
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
                
                // Validate registration number format (basic check)
                if (field.name === 'reg' && value) {
                    const regRegex = /^[A-Z0-9]{6,12}$/i;
                    if (!regRegex.test(value)) {
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
                    const firstError = document.querySelector('.form-input.error');
                    if (firstError) {
                        firstError.focus();
                    }
                }
            });
        });
    </script>
</body>
</html>