<?php
include "db.php";

$name = $_POST['customer_name'];
$vehicle = $_POST['vehicle_type'];
$days = $_POST['days'];
$payment = $_POST['payment_status'];
$total_cost = $days * 1000; // e.g., ₹1000 per day

$sql = "INSERT INTO rentals (customer_name, vehicle_type, rental_days, total_cost, payment_status)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiss", $name, $vehicle, $days, $total_cost, $payment);
$stmt->execute();

echo "Rental added successfully! <a href='index.php'>Back</a>";
?>
