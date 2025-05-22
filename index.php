<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Vehicle Rental</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Vehicle Rental Form</h2>
  <form action="rent_vehicle.php" method="POST">
    <label>Customer Name:</label>
    <input type="text" name="customer_name" required><br>

    <label>Vehicle Type:</label>
    <select name="vehicle_type">
      <option value="Car">Car</option>
      <option value="Bike">Bike</option>
      <option value="Van">Van</option>
    </select><br>

    <label>Rental Days:</label>
    <input type="number" name="days" min="1" required><br>

    <label>Payment Status:</label>
    <select name="payment_status">
      <option value="Paid">Paid</option>
      <option value="Due">Due</option>
    </select><br>

    <input type="submit" value="Rent Vehicle">
  </form>

  <br>
  <a href="view_rentals.php">View All Rentals</a>
</body>
</html>
