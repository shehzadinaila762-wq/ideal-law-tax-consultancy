<?php

require __DIR__ . '/../config/database.php';

if ($conn->connect_error) {
	die("Database connection failed: " . $conn->connect_error);
}

$message_sent = false;

if (isset($_POST['send_query'])) {

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$message = $_POST['message'];

	$stmt = $conn->prepare(
		"INSERT INTO queries
        (first_name, last_name, email, phone, message)
        VALUES (?, ?, ?, ?, ?)"
	);

	$stmt->bind_param(
		"sssss",
		$first_name,
		$last_name,
		$email,
		$phone,
		$message
	);

	if ($stmt->execute()) {
		$message_sent = true;
	}

	$stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Contact - Ideal Law & Tax Consultancy</title>

	<link rel="stylesheet" href="assets/css/index.css?v=6">

</head>

<body>

	<?php include __DIR__ . '/header.php'; ?>

	<section class="contact" id="contact">

		<div class="contact-card">

			<h3>Send Your Query</h3>

			<?php if ($message_sent): ?>

				<div class="success-message">
					Your query has been submitted successfully.
				</div>

			<?php endif; ?>

			<form method="POST" action="contact.php">

				<label>First Name</label>

				<input type="text" name="first_name" placeholder="First Name" required>

				<label>Last Name</label>

				<input type="text" name="last_name" placeholder="Last Name" required>

				<label>Email</label>

				<input type="email" name="email" placeholder="Your Email" required>

				<label>Phone Number</label>

				<input type="text" name="phone" placeholder="03XXXXXXXXX" required>

				<label>Message</label>

				<textarea name="message" placeholder="Write your query..." rows="5" required></textarea>

				<button type="submit" name="send_query" class="btn btn-primary"
					style="border:none;cursor:pointer;margin-top:15px;">
					Send Query
				</button>

			</form>

		</div>

		<h2 class="section-title">Contact Us</h2>

		<p>📍 T11 KFK Plaza, Adalat Road, Quetta</p>

		<p>📞 03337809635</p>

		<p>📞 03338634288</p>

		<p>✉️ ideallawandconsultancy@gmail.com</p>

		<a href="admin.php" class="admin-btn">
			Admin Panel
		</a>

	</section>

	<?php include __DIR__ . '/footer.php'; ?>

</body>

</html>
