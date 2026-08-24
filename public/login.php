<?php
session_start();

$admin_username = "admin";
$admin_password = "Ideal@123";

$error = "";

if (isset($_POST['login'])) {

	$username = $_POST['username'];
	$password = $_POST['password'];

	if ($username === $admin_username && $password === $admin_password) {

		$_SESSION['admin_logged_in'] = true;

		header("Location: admin.php");
		exit;

	} else {

		$error = "Invalid username or password.";

	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Admin Login - Ideal Law</title>

	<style>
		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
			font-family: Arial, sans-serif;
		}

		body {
			min-height: 100vh;
			display: flex;
			justify-content: center;
			align-items: center;
			background:
				linear-gradient(rgba(11, 31, 58, .9), rgba(11, 31, 58, .9)),
				url("https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=1600&q=80");
			background-size: cover;
		}

		.login-box {
			background: white;
			width: 90%;
			max-width: 420px;
			padding: 40px;
			border-radius: 8px;
			box-shadow: 0 5px 25px rgba(0, 0, 0, .3);
		}

		h1 {
			text-align: center;
			color: #0b1f3a;
			margin-bottom: 8px;
		}

		.subtitle {
			text-align: center;
			color: #666;
			margin-bottom: 30px;
		}

		label {
			font-weight: bold;
			color: #333;
		}

		input {
			width: 100%;
			padding: 12px;
			margin: 8px 0 18px;
			border: 1px solid #ccc;
			border-radius: 4px;
		}

		button {
			width: 100%;
			padding: 13px;
			border: none;
			background: #0b1f3a;
			color: white;
			font-weight: bold;
			border-radius: 4px;
			cursor: pointer;
		}

		button:hover {
			background: #d4af37;
			color: #0b1f3a;
		}

		.error {
			background: #ffe5e5;
			color: #b00000;
			padding: 10px;
			margin-bottom: 20px;
			text-align: center;
			border-radius: 4px;
		}

		.back {
			display: block;
			text-align: center;
			margin-top: 20px;
			color: #0b1f3a;
			text-decoration: none;
		}
	</style>

</head>

<body>

	<div class="login-box">

		<h1>Admin Login</h1>

		<p class="subtitle">
			Ideal Law & Tax Consultancy
		</p>

						<?php if ($error): ?>

			<div class="error">
												<?php echo $error; ?>
			</div>

						<?php endif; ?>

		<form method="POST">

			<label>Username</label>

			<input type="text" name="username" required>

			<label>Password</label>

			<input type="password" name="password" required>

			<button type="submit" name="login">
				Login
			</button>

		</form>

		<a href="index.php" class="back">
			← Back to Website
		</a>

	</div>

</body>

</html>
