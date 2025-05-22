<?php
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Rentals</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #e0e7ff;
      --secondary: #3f37c9;
      --dark: #1e1e24;
      --light: #f8f9fa;
      --success: #4cc9f0;
      --warning: #f8961e;
      --danger: #f72585;
      --gray: #adb5bd;
      --gray-light: #e9ecef;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background: #f5f7fb;
      color: var(--dark);
      line-height: 1.6;
      padding: 0;
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem;
    }
    
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--gray-light);
    }
    
    h1, h2, h3 {
      color: var(--dark);
      font-weight: 600;
    }
    
    h1 {
      font-size: 2rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    h2 {
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    h3 {
      font-size: 1.25rem;
      margin-bottom: 1rem;
    }
    
    .rentals-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }
    
    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
      background: var(--primary);
      color: white;
      padding: 1rem;
    }
    
    .card-body {
      padding: 1.5rem;
    }
    
    .card-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.75rem;
    }
    
    .card-row:last-child {
      margin-bottom: 0;
    }
    
    .card-label {
      font-weight: 500;
      color: var(--gray);
    }
    
    .card-value {
      font-weight: 600;
    }
    
    .status {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }
    
    .status-paid {
      background: var(--primary-light);
      color: var(--primary);
    }
    
    .status-pending {
      background: #fff3bf;
      color: #e67700;
    }
    
    .dashboard-section {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }
    
    .stat-card {
      background: white;
      border-radius: 8px;
      padding: 1.5rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      border-top: 4px solid var(--primary);
    }
    
    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary);
      margin: 0.5rem 0;
    }
    
    .stat-label {
      color: var(--gray);
      font-size: 0.9rem;
    }
    
    pre {
      background: #1e1e24;
      color: #f8f9fa;
      padding: 1.5rem;
      border-radius: 8px;
      overflow-x: auto;
      font-family: 'Courier New', Courier, monospace;
      font-size: 0.9rem;
      line-height: 1.5;
      margin-top: 1rem;
    }
    
    .query-title {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1rem;
      color: var(--primary);
    }
    
    @media (max-width: 768px) {
      .container {
        padding: 1rem;
      }
      
      h1 {
        font-size: 1.5rem;
      }
      
      h2 {
        font-size: 1.25rem;
      }
      
      .rentals-grid {
        grid-template-columns: 1fr;
      }
    }
    
    @media (max-width: 480px) {
      .stat-value {
        font-size: 1.5rem;
      }
      
      .card-body {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h1>🚗 Car Rental Dashboard</h1>
    </header>
    
    <?php
    // Get statistics data first to prevent errors
    $res = $conn->query("SELECT COUNT(*) AS total_rentals, 
                         SUM(total_cost) AS revenue, 
                         AVG(total_cost) AS avg_cost 
                         FROM rentals");
    $data = $res ? $res->fetch_assoc() : ['total_rentals' => 0, 'revenue' => 0, 'avg_cost' => 0];
    
    // Format revenue and average cost properly
    $revenue = isset($data['revenue']) ? number_format($data['revenue'], 2) : '0.00';
    $avg_cost = isset($data['avg_cost']) ? number_format($data['avg_cost'], 2) : '0.00';
    ?>
    
    <section class="dashboard-section">
      <h2>Rental Statistics</h2>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-label">Total Rentals</div>
          <div class="stat-value"><?= $data['total_rentals'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Revenue</div>
          <div class="stat-value">₹<?= $revenue ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Average Cost</div>
          <div class="stat-value">₹<?= $avg_cost ?></div>
        </div>
      </div>
    </section>
    
    <section class="dashboard-section">
      <h2>Recent Rentals</h2>
      <div class="rentals-grid">
        <?php
        $result = $conn->query("SELECT * FROM rentals ORDER BY id DESC");
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $statusClass = strtolower($row['payment_status']) == 'paid' ? 'status-paid' : 'status-pending';
            echo "<div class='card'>
                    <div class='card-header'>
                      <h3>{$row['customer_name']}</h3>
                    </div>
                    <div class='card-body'>
                      <div class='card-row'>
                        <span class='card-label'>Vehicle:</span>
                        <span class='card-value'>{$row['vehicle_type']}</span>
                      </div>
                      <div class='card-row'>
                        <span class='card-label'>Duration:</span>
                        <span class='card-value'>{$row['rental_days']} days</span>
                      </div>
                      <div class='card-row'>
                        <span class='card-label'>Total Cost:</span>
                        <span class='card-value'>₹{$row['total_cost']}</span>
                      </div>
                      <div class='card-row'>
                        <span class='card-label'>Status:</span>
                        <span class='status {$statusClass}'>{$row['payment_status']}</span>
                      </div>
                    </div>
                  </div>";
          }
        } else {
          echo "<p>No rentals found.</p>";
        }
        ?>
      </div>
    </section>
    
    <section class="dashboard-section">
      <div class="query-title">
        <h3>SQL Query Examples</h3>
      </div>
      <pre>
<?php
// 1. SELECT DISTINCT
$q1 = $conn->query("SELECT DISTINCT vehicle_type FROM rentals");
echo "1. Distinct Vehicles: ";
if ($q1 && $q1->num_rows > 0) {
  while ($row = $q1->fetch_assoc()) echo $row['vehicle_type'] . " ";
} else {
  echo "None found";
}
echo "\n";

// 2. WHERE + AND
$q2 = $conn->query("SELECT * FROM rentals WHERE rental_days > 3 AND payment_status = 'Paid'");
echo "2. Rentals > 3 days and paid: " . ($q2 ? $q2->num_rows : 0) . "\n";

// 3. ORDER BY + LIMIT
$q3 = $conn->query("SELECT * FROM rentals ORDER BY total_cost DESC LIMIT 1");
echo "3. Highest paying customer: ";
if ($q3 && $q3->num_rows > 0) {
  $row = $q3->fetch_assoc();
  echo $row['customer_name'] . " ₹" . $row['total_cost'];
} else {
  echo "None found";
}
echo "\n";

// 4. NULL check
$q4 = $conn->query("SELECT * FROM rentals WHERE customer_name IS NULL");
echo "4. Null names: " . ($q4 ? $q4->num_rows : 0) . "\n";

// 5. LIKE + WILDCARDS
$q5 = $conn->query("SELECT * FROM rentals WHERE customer_name LIKE '%a%'");
echo "5. Names containing 'a': " . ($q5 ? $q5->num_rows : 0) . "\n";

// 6. BETWEEN
$q6 = $conn->query("SELECT * FROM rentals WHERE rental_days BETWEEN 2 AND 5");
echo "6. Days between 2-5: " . ($q6 ? $q6->num_rows : 0) . "\n";

// 7. ALIAS
$q7 = $conn->query("SELECT AVG(total_cost) AS avg_price FROM rentals");
echo "7. Avg Price: ₹";
if ($q7 && $q7->num_rows > 0) {
  $row = $q7->fetch_assoc();
  echo number_format($row['avg_price'], 2);
} else {
  echo "0.00";
}
echo "\n";

// 8. UPDATE, DELETE (Examples)
echo "8. UPDATE rentals SET payment_status = 'Paid' WHERE id = 1;\n";
echo "   DELETE FROM rentals WHERE id = 2;\n";

// 9. JOIN note
echo "9. JOIN: Add customer or vehicle table and JOIN them in future queries.\n";
?>
      </pre>
    </section>
  </div>
</body>
</html>