<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Rental Management System</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 900px;
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
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #718096;
            font-size: 1.1rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .menu-item {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border: 2px solid transparent;
            border-radius: 15px;
            padding: 25px;
            text-decoration: none;
            color: #2d3748;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
            background: linear-gradient(135deg, #ffffff, #f7fafc);
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }

        .menu-item:hover::before {
            left: 100%;
        }

        .menu-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .menu-content h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #2d3748;
        }

        .menu-content p {
            font-size: 0.9rem;
            color: #718096;
            line-height: 1.4;
        }

        .stats-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(66, 153, 225, 0.3);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }

            .menu-item {
                padding: 20px;
            }

            .menu-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
                margin-right: 15px;
            }
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vehicle Rental Management</h1>
            <p>Streamline your rental operations with our comprehensive management system</p>
        </div>

        <div class="menu-grid">
            <a href="add_customer.php" class="menu-item">
                <div class="menu-icon">👤</div>
                <div class="menu-content">
                    <h3>Add Customer</h3>
                    <p>Register new customers and manage their information</p>
                </div>
            </a>

            <a href="add_vehicle.php" class="menu-item">
                <div class="menu-icon">🚗</div>
                <div class="menu-content">
                    <h3>Add Vehicle</h3>
                    <p>Add new vehicles to your rental fleet</p>
                </div>
            </a>

            <a href="rent_vehicle.php" class="menu-item">
                <div class="menu-icon">📋</div>
                <div class="menu-content">
                    <h3>Rent Vehicle</h3>
                    <p>Process new rental agreements and bookings</p>
                </div>
            </a>

            <a href="add_maintenance.php" class="menu-item">
                <div class="menu-icon">🔧</div>
                <div class="menu-content">
                    <h3>Add Maintenance</h3>
                    <p>Schedule and track vehicle maintenance records</p>
                </div>
            </a>

            <a href="payment.php" class="menu-item">
                <div class="menu-icon">💳</div>
                <div class="menu-content">
                    <h3>Make Payment</h3>
                    <p>Process payments and manage transactions</p>
                </div>
            </a>

            <a href="view_customers.php" class="menu-item">
                <div class="menu-icon">👥</div>
                <div class="menu-content">
                    <h3>View Customers</h3>
                    <p>Browse and manage customer database</p>
                </div>
            </a>

            <a href="view_vehicles.php" class="menu-item">
                <div class="menu-icon">🚙</div>
                <div class="menu-content">
                    <h3>View Vehicles</h3>
                    <p>Monitor your vehicle fleet and availability</p>
                </div>
            </a>

            <a href="view_rentals.php" class="menu-item">
                <div class="menu-icon">📊</div>
                <div class="menu-content">
                    <h3>View Rentals</h3>
                    <p>Track active and completed rental agreements</p>
                </div>
            </a>

            <a href="view_maintenance.php" class="menu-item">
                <div class="menu-icon">🔍</div>
                <div class="menu-content">
                    <h3>View Maintenance</h3>
                    <p>Review maintenance history and schedules</p>
                </div>
            </a>

            <a href="view_payments.php" class="menu-item">
                <div class="menu-icon">💰</div>
                <div class="menu-content">
                    <h3>View Payments</h3>
                    <p>Monitor payment history and financial records</p>
                </div>
            </a>
        </div>

        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">248</div>
                    <div class="stat-label">Active Rentals</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">156</div>
                    <div class="stat-label">Total Vehicles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">1,342</div>
                    <div class="stat-label">Registered Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Fleet Availability</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2025 Vehicle Rental Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>