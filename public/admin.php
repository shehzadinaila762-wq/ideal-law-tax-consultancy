<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
	header("Location: login.php");
	exit;
}

require __DIR__ . '/../config/database.php';

if ($conn->connect_error) {
	die("Database connection failed: " . $conn->connect_error);
}

/* Create clients table */
$conn->query("CREATE TABLE IF NOT EXISTS clients (
	id INT AUTO_INCREMENT PRIMARY KEY,
	client_name VARCHAR(100) NOT NULL,
	cnic VARCHAR(20),
	email VARCHAR(100),
	phone VARCHAR(30),
	designation VARCHAR(100),
	service VARCHAR(150),
	total_fee DECIMAL(10,2) DEFAULT 0,
	paid_amount DECIMAL(10,2) DEFAULT 0,
	status ENUM('Active','Inactive') DEFAULT 'Active',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

/* Add missing client columns */
$columns = [
	"cnic" => "ALTER TABLE clients ADD cnic VARCHAR(20)",
	"email" => "ALTER TABLE clients ADD email VARCHAR(100)",
	"designation" => "ALTER TABLE clients ADD designation VARCHAR(100)"
];

foreach ($columns as $column => $query) {
	$check = $conn->query("SHOW COLUMNS FROM clients LIKE '$column'");

	if ($check && $check->num_rows == 0) {
		$conn->query($query);
	}
}

/* Create payment history table */
$conn->query("CREATE TABLE IF NOT EXISTS payments (
	id INT AUTO_INCREMENT PRIMARY KEY,
	client_id INT NOT NULL,
	amount DECIMAL(10,2) NOT NULL,
	payment_date DATE NOT NULL,
	notes VARCHAR(255),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

/* Create services table */
$conn->query("CREATE TABLE IF NOT EXISTS services (
	id INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(150) NOT NULL,
	icon VARCHAR(20) DEFAULT '⚖️',
	items TEXT NOT NULL,
	sort_order INT DEFAULT 0,
	status ENUM('Active','Inactive') DEFAULT 'Active',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

/* Seed default services if table is empty */
$service_count = $conn->query(
	"SELECT COUNT(*) AS total FROM services"
)->fetch_assoc()['total'];

if ((int) $service_count === 0) {

	$default_services = [
		[
			"Legal Services",
			"⚖️",
			"Legal Consultation & Advice\nLegal Documentation & Drafting\nContract & Agreement Drafting\nProperty & Land Matters\nCivil & Criminal Matters\nFamily Law Matters\nCorporate Legal Advisory\nLegal Notices\nCourt & Litigation Services",
			1
		],
		[
			"Tax Consultation",
			"💰",
			"Income Tax Return\nGeneral Sales Tax (GST)\nRegional Sales Tax (BRA)\nFederal Excise Tax\nPOS (Point of Sale)\nNTN (National Tax Number)",
			2
		],
		[
			"Corporate Services",
			"🏢",
			"Company Registration\nFirm Registration\nPakistan Engineering Council Registration\nTrademark Registration\nImport / Export License\nChamber of Commerce Registration\nNGO / NPO Registration",
			3
		],
		[
			"Documentation Services",
			"📄",
			"Legal Document Preparation\nAgreements & Contracts\nAffidavits\nApplications & Official Documents\nPower of Attorney\nLegal Notices\nBusiness & Company Documents\nTax & Regulatory Documents",
			4
		]
	];

	$seed = $conn->prepare(
		"INSERT INTO services (title, icon, items, sort_order, status)
		 VALUES (?, ?, ?, ?, 'Active')"
	);

	foreach ($default_services as $service_row) {

		$title = $service_row[0];
		$icon = $service_row[1];
		$items = $service_row[2];
		$sort_order = $service_row[3];

		$seed->bind_param(
			"sssi",
			$title,
			$icon,
			$items,
			$sort_order
		);

		$seed->execute();
	}
}
/* Add Team Member */

if (isset($_POST['add_member'])) {

	$member_name = $_POST['member_name'];
	$designation = $_POST['designation'];
	$icon = $_POST['icon'];

	$stmt = $conn->prepare(
		"INSERT INTO team_members (member_name, designation, icon)
		 VALUES (?, ?, ?)"
	);

	$stmt->bind_param(
		"sss",
		$member_name,
		$designation,
		$icon
	);

	$stmt->execute();

	header("Location: admin.php");
	exit;
}
/* Load Team Member for Edit */

$edit_member = null;

if (isset($_GET['edit_member'])) {

	$edit_id = intval($_GET['edit_member']);

	$edit_result = $conn->query(
		"SELECT * FROM team_members WHERE id = $edit_id"
	);

	if ($edit_result && $edit_result->num_rows > 0) {
		$edit_member = $edit_result->fetch_assoc();
	}
}

/* Edit Team Member */

if (isset($_POST['edit_member'])) {

	$id = intval($_POST['member_id']);

	$member_name = $_POST['member_name'];
	$designation = $_POST['designation'];
	$icon = $_POST['icon'];

	$stmt = $conn->prepare(
		"UPDATE team_members
		 SET member_name=?, designation=?, icon=?
		 WHERE id=?"
	);

	$stmt->bind_param(
		"sssi",
		$member_name,
		$designation,
		$icon,
		$id
	);

	$stmt->execute();

	header("Location: admin.php");
	exit;
}

/* Delete Team Member */

if (isset($_GET['delete_member'])) {

	$id = intval($_GET['delete_member']);

	$stmt = $conn->prepare(
		"DELETE FROM team_members WHERE id=?"
	);

	$stmt->bind_param("i", $id);
	$stmt->execute();

	header("Location: admin.php");
	exit;
}

/* Add Service */
if (isset($_POST['add_service'])) {

	$title = trim($_POST['service_title']);
	$icon = trim($_POST['service_icon']);
	$items = trim($_POST['service_items']);
	$sort_order = intval($_POST['sort_order']);
	$status = $_POST['service_status'];

	if ($icon === "") {
		$icon = "⚖️";
	}

	$stmt = $conn->prepare(
		"INSERT INTO services (title, icon, items, sort_order, status)
		 VALUES (?, ?, ?, ?, ?)"
	);

	$stmt->bind_param(
		"sssis",
		$title,
		$icon,
		$items,
		$sort_order,
		$status
	);

	$stmt->execute();

	header("Location: admin.php");
	exit;
}

/* Load Service for Edit */
$edit_service = null;

if (isset($_GET['edit_service'])) {

	$service_id = intval($_GET['edit_service']);

	$service_result = $conn->query(
		"SELECT * FROM services WHERE id = $service_id"
	);

	if ($service_result && $service_result->num_rows > 0) {
		$edit_service = $service_result->fetch_assoc();
	}
}

/* Edit Service */
if (isset($_POST['edit_service'])) {

	$id = intval($_POST['service_id']);
	$title = trim($_POST['service_title']);
	$icon = trim($_POST['service_icon']);
	$items = trim($_POST['service_items']);
	$sort_order = intval($_POST['sort_order']);
	$status = $_POST['service_status'];

	if ($icon === "") {
		$icon = "⚖️";
	}

	$stmt = $conn->prepare(
		"UPDATE services
		 SET title=?, icon=?, items=?, sort_order=?, status=?
		 WHERE id=?"
	);

	$stmt->bind_param(
		"sssisi",
		$title,
		$icon,
		$items,
		$sort_order,
		$status,
		$id
	);

	$stmt->execute();

	header("Location: admin.php");
	exit;
}

/* Delete Service */
if (isset($_GET['delete_service'])) {

	$id = intval($_GET['delete_service']);

	$stmt = $conn->prepare(
		"DELETE FROM services WHERE id=?"
	);

	$stmt->bind_param("i", $id);
	$stmt->execute();

	header("Location: admin.php");
	exit;
}

/* Toggle Service Status */
if (isset($_GET['toggle_service'])) {

	$id = intval($_GET['toggle_service']);

	$service = $conn->query(
		"SELECT status FROM services WHERE id = $id"
	)->fetch_assoc();

	if ($service) {

		$new_status =
			($service['status'] == 'Active')
			? 'Inactive'
			: 'Active';

		$stmt = $conn->prepare(
			"UPDATE services SET status=? WHERE id=?"
		);

		$stmt->bind_param(
			"si",
			$new_status,
			$id
		);

		$stmt->execute();
	}

	header("Location: admin.php");
	exit;
}

/* Add Client */
if (isset($_POST['add_client'])) {

	$name = $_POST['client_name'];
	$cnic = $_POST['cnic'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$designation = $_POST['designation'];
	$service = $_POST['service'];
	$total_fee = $_POST['total_fee'];
	$paid_amount = $_POST['paid_amount'];
	$status = $_POST['status'];

	$stmt = $conn->prepare("INSERT INTO clients
		(client_name, cnic, email, phone, designation, service, total_fee, paid_amount, status)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

	$stmt->bind_param(
		"ssssssdds",
		$name,
		$cnic,
		$email,
		$phone,
		$designation,
		$service,
		$total_fee,
		$paid_amount,
		$status
	);

	$stmt->execute();

	$client_id = $stmt->insert_id;

	if ($paid_amount > 0) {

		$date = date("Y-m-d");
		$notes = "Initial payment";

		$payment = $conn->prepare("INSERT INTO payments
			(client_id, amount, payment_date, notes)
			VALUES (?, ?, ?, ?)");

		$payment->bind_param(
			"idss",
			$client_id,
			$paid_amount,
			$date,
			$notes
		);

		$payment->execute();
	}

	header("Location: admin.php");
	exit;
}
/* Load Client for Edit */

$edit_client = null;

if (isset($_GET['edit'])) {

	$edit_id = intval($_GET['edit']);

	$edit_result = $conn->query(
		"SELECT * FROM clients WHERE id = $edit_id"
	);

	if ($edit_result && $edit_result->num_rows > 0) {
		$edit_client = $edit_result->fetch_assoc();
	}
}

/* Edit Client */
if (isset($_POST['edit_client'])) {

	$id = intval($_POST['client_id']);

	$name = $_POST['client_name'];
	$cnic = $_POST['cnic'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$designation = $_POST['designation'];
	$service = $_POST['service'];
	$total_fee = $_POST['total_fee'];
	$status = $_POST['status'];

	$stmt = $conn->prepare(
		"UPDATE clients SET
		client_name=?,
		cnic=?,
		email=?,
		phone=?,
		designation=?,
		service=?,
		total_fee=?,
		status=?
		WHERE id=?"
	);

	$stmt->bind_param(
		"ssssssdsi",
		$name,
		$cnic,
		$email,
		$phone,
		$designation,
		$service,
		$total_fee,
		$status,
		$id
	);

	$stmt->execute();

	header("Location: admin.php");
	exit;
}
/* Open Edit Client Form */
$edit_client = null;

if (isset($_GET['edit'])) {

	$edit_id = intval($_GET['edit']);

	$edit_result = $conn->query(
		"SELECT * FROM clients WHERE id = $edit_id"
	);

	if ($edit_result && $edit_result->num_rows > 0) {
		$edit_client = $edit_result->fetch_assoc();
	}
}

/* Delete Client */
if (isset($_GET['delete'])) {

	$id = intval($_GET['delete']);

	$conn->query("DELETE FROM payments WHERE client_id = $id");
	$conn->query("DELETE FROM clients WHERE id = $id");

	header("Location: admin.php");
	exit;
}

/* Change Client Status */
if (isset($_GET['toggle'])) {

	$id = intval($_GET['toggle']);

	$client = $conn->query(
		"SELECT status FROM clients WHERE id = $id"
	)->fetch_assoc();

	if ($client) {

		$new_status =
			($client['status'] == 'Active')
			? 'Inactive'
			: 'Active';

		$stmt = $conn->prepare(
			"UPDATE clients SET status=? WHERE id=?"
		);

		$stmt->bind_param(
			"si",
			$new_status,
			$id
		);

		$stmt->execute();
	}

	header("Location: admin.php");
	exit;
}

/* Add Payment */
if (isset($_POST['add_payment'])) {

	$client_id = intval($_POST['client_id']);
	$amount = floatval($_POST['amount']);
	$payment_date = $_POST['payment_date'];
	$notes = $_POST['notes'];

	$stmt = $conn->prepare("INSERT INTO payments
		(client_id, amount, payment_date, notes)
		VALUES (?, ?, ?, ?)");

	$stmt->bind_param(
		"idss",
		$client_id,
		$amount,
		$payment_date,
		$notes
	);

	$stmt->execute();

	$stmt2 = $conn->prepare(
		"UPDATE clients
		 SET paid_amount = paid_amount + ?
		 WHERE id = ?"
	);

	$stmt2->bind_param(
		"di",
		$amount,
		$client_id
	);

	$stmt2->execute();

	header("Location: admin.php");
	exit;
}

/* Query Status */
$check_status = $conn->query(
	"SHOW COLUMNS FROM queries LIKE 'status'"
);

if ($check_status && $check_status->num_rows == 0) {

	$conn->query(
		"ALTER TABLE queries
		 ADD status ENUM('Pending','Resolved')
		 DEFAULT 'Pending'"
	);
}

/* Resolve Query */
if (isset($_GET['resolve_query'])) {

	$query_id = intval($_GET['resolve_query']);

	$stmt = $conn->prepare(
		"UPDATE queries SET status='Resolved' WHERE id=?"
	);

	$stmt->bind_param("i", $query_id);
	$stmt->execute();

	header("Location: admin.php");
	exit;
}

/* Delete Query */
if (isset($_GET['delete_query'])) {

	$query_id = intval($_GET['delete_query']);

	$stmt = $conn->prepare(
		"DELETE FROM queries WHERE id=?"
	);

	$stmt->bind_param("i", $query_id);
	$stmt->execute();

	header("Location: admin.php");
	exit;
}
/* Create Team Members Table */

$conn->query("CREATE TABLE IF NOT EXISTS team_members (
	id INT AUTO_INCREMENT PRIMARY KEY,
	member_name VARCHAR(100) NOT NULL,
	designation VARCHAR(100) NOT NULL,
	icon VARCHAR(20) DEFAULT '⚖️',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

/* Create Gallery Table */
$conn->query("CREATE TABLE IF NOT EXISTS gallery (
	id INT AUTO_INCREMENT PRIMARY KEY,
	image_name VARCHAR(255) NOT NULL,
	image_path VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
/* Gallery Upload */
/* Gallery Upload */

if (isset($_POST['upload_image'])) {

	if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] == 0) {

		$image_name = $_FILES['gallery_image']['name'];
		$tmp_name = $_FILES['gallery_image']['tmp_name'];

		$allowed = ['jpg', 'jpeg', 'png', 'webp'];
		$extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

		if (in_array($extension, $allowed)) {

			$new_name = time() . "_" . basename($image_name);
			$upload_path = "uploads/" . $new_name;

			if (move_uploaded_file($tmp_name, $upload_path)) {

				$stmt = $conn->prepare(
					"INSERT INTO gallery (image_name, image_path)
					 VALUES (?, ?)"
				);

				$stmt->bind_param(
					"ss",
					$image_name,
					$upload_path
				);

				$stmt->execute();
			}
		}
	}

	header("Location: admin.php");
	exit;
}
/* Load Gallery Image for Edit */

$edit_gallery = null;

if (isset($_GET['edit_gallery'])) {

	$edit_gallery_id = intval($_GET['edit_gallery']);

	$edit_gallery_result = $conn->query(
		"SELECT * FROM gallery WHERE id = $edit_gallery_id"
	);

	if ($edit_gallery_result && $edit_gallery_result->num_rows > 0) {
		$edit_gallery = $edit_gallery_result->fetch_assoc();
	}
}

/* Edit Gallery Image */

if (isset($_POST['edit_gallery'])) {

	$gallery_id = intval($_POST['gallery_id']);

	if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] == 0) {

		$image_name = $_FILES['gallery_image']['name'];
		$tmp_name = $_FILES['gallery_image']['tmp_name'];

		$allowed = ['jpg', 'jpeg', 'png', 'webp'];
		$extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

		if (in_array($extension, $allowed)) {

			$old_image = $conn->query(
				"SELECT image_path FROM gallery WHERE id = $gallery_id"
			)->fetch_assoc();

			$new_name = time() . "_" . basename($image_name);
			$upload_path = "uploads/" . $new_name;

			if (move_uploaded_file($tmp_name, $upload_path)) {

				if ($old_image && file_exists($old_image['image_path'])) {
					unlink($old_image['image_path']);
				}

				$stmt = $conn->prepare(
					"UPDATE gallery
					 SET image_name=?, image_path=?
					 WHERE id=?"
				);

				$stmt->bind_param(
					"ssi",
					$image_name,
					$upload_path,
					$gallery_id
				);

				$stmt->execute();
			}
		}
	}

	header("Location: admin.php");
	exit;
}

/* Delete Gallery Image */

if (isset($_GET['delete_gallery'])) {

	$gallery_id = intval($_GET['delete_gallery']);

	$image = $conn->query(
		"SELECT image_path FROM gallery WHERE id = $gallery_id"
	)->fetch_assoc();

	if ($image) {

		if (file_exists($image['image_path'])) {
			unlink($image['image_path']);
		}

		$conn->query(
			"DELETE FROM gallery WHERE id = $gallery_id"
		);
	}

	header("Location: admin.php");
	exit;
}

/* Dashboard */
$total_clients = $conn->query(
	"SELECT COUNT(*) AS total FROM clients"
)->fetch_assoc()['total'];

$active_clients = $conn->query(
	"SELECT COUNT(*) AS total
	 FROM clients
	 WHERE status='Active'"
)->fetch_assoc()['total'];

$inactive_clients = $conn->query(
	"SELECT COUNT(*) AS total
	 FROM clients
	 WHERE status='Inactive'"
)->fetch_assoc()['total'];

$total_fee = $conn->query(
	"SELECT COALESCE(SUM(total_fee),0) AS total
	 FROM clients"
)->fetch_assoc()['total'];

$total_paid = $conn->query(
	"SELECT COALESCE(SUM(paid_amount),0) AS total
	 FROM clients"
)->fetch_assoc()['total'];

$total_pending = $total_fee - $total_paid;

/* Client Records */
$result = $conn->query(
	"SELECT * FROM clients ORDER BY id DESC"
);

/* Payment Clients */
$payment_clients = $conn->query(
	"SELECT id, client_name
	 FROM clients
	 ORDER BY client_name"
);

/* Client Queries */
$queries = $conn->query(
	"SELECT * FROM queries ORDER BY id DESC"
);

/* Services */
$services_list = $conn->query(
	"SELECT * FROM services ORDER BY sort_order ASC, id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Admin Panel - Ideal Law</title>

	<style>
		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
			font-family: Arial, sans-serif;
		}

		body {
			background: #f4f6f9;
			color: #222;
		}

		header {
			background: #0b1f3a;
			color: white;
			padding: 22px 7%;
		}

		header h1 {
			color: #d4af37;
		}

		.container {
			width: 94%;
			max-width: 1400px;
			margin: 30px auto;
		}

		.back {
			display: inline-block;
			margin-bottom: 20px;
			text-decoration: none;
			color: #0b1f3a;
			font-weight: bold;
		}

		.dashboard {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
			gap: 18px;
			margin-bottom: 30px;
		}

		.dashboard-card {
			background: white;
			padding: 22px;
			text-align: center;
			border-radius: 7px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
			border-top: 4px solid #d4af37;
		}

		.dashboard-card h3 {
			color: #0b1f3a;
			font-size: 15px;
			margin-bottom: 10px;
		}

		.number {
			font-size: 25px;
			font-weight: bold;
			color: #0b1f3a;
		}

		.form-box,
		.table-box {
			background: white;
			padding: 28px;
			margin-bottom: 30px;
			border-radius: 7px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
		}

		.form-box h2,
		.table-box h2 {
			color: #0b1f3a;
			margin-bottom: 20px;
		}

		.form-grid {
			display: grid;
			grid-template-columns: repeat(2, 1fr);
			gap: 10px 20px;
		}

		label {
			font-weight: bold;
			color: #333;
		}

		input,
		select,
		textarea {
			width: 100%;
			padding: 11px;
			margin: 7px 0 15px;
			border: 1px solid #ccc;
			border-radius: 4px;
		}

		textarea {
			min-height: 140px;
			resize: vertical;
		}

		button {
			background: #0b1f3a;
			color: white;
			border: none;
			padding: 12px 22px;
			border-radius: 4px;
			cursor: pointer;
		}

		button:hover {
			background: #d4af37;
			color: #0b1f3a;
		}

		.full {
			grid-column: 1 / -1;
		}

		.table-wrapper {
			overflow-x: auto;
		}

		table {
			width: 100%;
			min-width: 1200px;
			border-collapse: collapse;
		}

		th {
			background: #0b1f3a;
			color: white;
			padding: 12px 8px;
		}

		td {
			padding: 11px 8px;
			border-bottom: 1px solid #ddd;
			text-align: center;
		}

		.active {
			color: green;
			font-weight: bold;
		}

		.inactive {
			color: red;
			font-weight: bold;
		}

		.paid {
			color: green;
			font-weight: bold;
		}

		.pending {
			color: #d35400;
			font-weight: bold;
		}

		.action {
			display: inline-block;
			padding: 6px 9px;
			margin: 2px;
			text-decoration: none;
			border-radius: 3px;
			font-size: 12px;
			background: #0b1f3a;
			color: white;
		}

		.delete {
			background: #b22222;
		}

		.payment-box {
			background: #eef1f5;
			padding: 20px;
			border-radius: 5px;
		}

		@media(max-width:700px) {

			.form-grid {
				grid-template-columns: 1fr;
			}

			.full {
				grid-column: auto;
			}
		}
	</style>

</head>

<body>

	<header>

		<h1>Ideal Law & Tax Consultancy</h1>
		<p>Admin Panel & Client Management</p>

	</header>

	<div class="container">

		<a href="index.php" class="back">← Back to Website</a>
		<a href="logout.php" class="back">Logout</a>

		<!-- DASHBOARD -->

		<div class="dashboard">

			<div class="dashboard-card">
				<h3>Total Clients</h3>
				<div class="number"><?php echo $total_clients; ?></div>
			</div>

			<div class="dashboard-card">
				<h3>Active Clients</h3>
				<div class="number"><?php echo $active_clients; ?></div>
			</div>

			<div class="dashboard-card">
				<h3>Inactive Clients</h3>
				<div class="number"><?php echo $inactive_clients; ?></div>
			</div>

			<div class="dashboard-card">
				<h3>Total Fee</h3>
				<div class="number">Rs. <?php echo number_format($total_fee); ?></div>
			</div>

			<div class="dashboard-card">
				<h3>Total Paid</h3>
				<div class="number">Rs. <?php echo number_format($total_paid); ?></div>
			</div>

			<div class="dashboard-card">
				<h3>Total Pending</h3>
				<div class="number">Rs. <?php echo number_format($total_pending); ?></div>
			</div>

		</div>

		<!-- SERVICES -->

		<div class="form-box">

			<h2>Manage Services</h2>

			<form method="POST">

				<div class="form-grid">

					<div>
						<label for="service_title_add">Service Title</label>
						<input id="service_title_add" type="text" name="service_title" required>
					</div>

					<div>
						<label for="service_icon_add">Icon</label>
						<input id="service_icon_add" type="text" name="service_icon" value="⚖️" placeholder="⚖️ / 💰 / 🏢">
					</div>

					<div>
						<label for="sort_order_add">Sort Order</label>
						<input id="sort_order_add" type="number" name="sort_order" value="0" required>
					</div>

					<div>
						<label for="service_status_add">Status</label>
						<select id="service_status_add" name="service_status">
							<option value="Active">Active</option>
							<option value="Inactive">Inactive</option>
						</select>
					</div>

					<div class="full">
						<label for="service_items_add">Service Items (One line per item)</label>
						<textarea id="service_items_add" name="service_items" required></textarea>
					</div>

					<div class="full">

						<button type="submit" name="add_service">
							+ Add Service
						</button>

					</div>

				</div>

			</form>

		</div>

		<?php if ($edit_service): ?>

			<div class="form-box">

				<h2>Edit Service</h2>

				<form method="POST">

					<input type="hidden" name="service_id" value="<?php echo $edit_service['id']; ?>">

					<div class="form-grid">

						<div>
							<label for="service_title_edit">Service Title</label>
							<input id="service_title_edit" type="text" name="service_title"
								value="<?php echo htmlspecialchars($edit_service['title']); ?>" required>
						</div>

						<div>
							<label for="service_icon_edit">Icon</label>
							<input id="service_icon_edit" type="text" name="service_icon"
								value="<?php echo htmlspecialchars($edit_service['icon']); ?>">
						</div>

						<div>
							<label for="sort_order_edit">Sort Order</label>
							<input id="sort_order_edit" type="number" name="sort_order"
								value="<?php echo (int) $edit_service['sort_order']; ?>" required>
						</div>

						<div>
							<label for="service_status_edit">Status</label>
							<select id="service_status_edit" name="service_status">
								<option value="Active" <?php echo $edit_service['status'] == 'Active' ? 'selected' : ''; ?>>
									Active
								</option>
								<option value="Inactive" <?php echo $edit_service['status'] == 'Inactive' ? 'selected' : ''; ?>>
									Inactive
								</option>
							</select>
						</div>

						<div class="full">
							<label for="service_items_edit">Service Items (One line per item)</label>
							<textarea id="service_items_edit" name="service_items" required><?php echo htmlspecialchars($edit_service['items']); ?></textarea>
						</div>

						<div class="full">
							<button type="submit" name="edit_service">
								Update Service
							</button>
						</div>

					</div>

				</form>

			</div>

		<?php endif; ?>

		<div class="table-box">

			<h2>Services List</h2>

			<div class="table-wrapper">

				<table>

					<tr>
						<th>ID</th>
						<th>Icon</th>
						<th>Title</th>
						<th>Items</th>
						<th>Order</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>

					<?php while ($service_row = $services_list->fetch_assoc()): ?>

						<tr>

							<td><?php echo $service_row['id']; ?></td>

							<td><?php echo htmlspecialchars($service_row['icon']); ?></td>

							<td><?php echo htmlspecialchars($service_row['title']); ?></td>

							<td style="text-align:left; max-width:420px;">
								<?php
								$items_preview = preg_split('/\r\n|\r|\n/', $service_row['items']);

								foreach ($items_preview as $item_line):

									$item_line = trim($item_line);

									if ($item_line === '') {
										continue;
									}
									?>
									<div>• <?php echo htmlspecialchars($item_line); ?></div>
								<?php endforeach; ?>
							</td>

							<td><?php echo (int) $service_row['sort_order']; ?></td>

							<td class="<?php echo strtolower($service_row['status']); ?>">
								<?php echo $service_row['status']; ?>
							</td>

							<td>

								<a class="action" href="admin.php?edit_service=<?php echo $service_row['id']; ?>">
									Edit
								</a>

								<a class="action" href="admin.php?toggle_service=<?php echo $service_row['id']; ?>">
									Toggle
								</a>

								<a class="action delete" href="admin.php?delete_service=<?php echo $service_row['id']; ?>"
									onclick="return confirm('Are you sure you want to delete this service?');">
									Delete
								</a>

							</td>

						</tr>

					<?php endwhile; ?>

				</table>

			</div>

		</div>
		<!-- TEAM MEMBERS -->

		<div class="form-box">

			<h2>Team Members</h2>

			<form method="POST">

				<div class="form-grid">

					<div>
						<label>Member Name</label>
						<input type="text" name="member_name" required>
					</div>

					<div>
						<label>Designation</label>
						<input type="text" name="designation" required>
					</div>

					<div>
						<label>Icon</label>
						<input type="text" name="icon" value="⚖️">
					</div>

					<div class="full">

						<button type="submit" name="add_member">
							+ Add Team Member
						</button>

					</div>

				</div>

			</form>

		</div>
		<?php if ($edit_member): ?>

			<div class="form-box">

				<h2>Edit Team Member</h2>

				<form method="POST">

					<input type="hidden" name="member_id" value="<?php echo $edit_member['id']; ?>">

					<label>Member Name</label>

					<input type="text" name="member_name"
						value="<?php echo htmlspecialchars($edit_member['member_name']); ?>" required>

					<label>Designation</label>

					<input type="text" name="designation"
						value="<?php echo htmlspecialchars($edit_member['designation']); ?>" required>

					<label>Icon</label>

					<input type="text" name="icon" value="<?php echo htmlspecialchars($edit_member['icon']); ?>">

					<button type="submit" name="edit_member">
						Update Member
					</button>

				</form>

			</div>

		<?php endif; ?>

		<!-- TEAM MEMBERS LIST -->

		<div class="table-box">

			<h2>Team Members List</h2>

			<div class="table-wrapper">

				<table>

					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Designation</th>
						<th>Icon</th>
						<th>Actions</th>
					</tr>

					<?php

					$team_members = $conn->query(
						"SELECT * FROM team_members ORDER BY id DESC"
					);

					while ($member = $team_members->fetch_assoc()):

						?>

						<tr>

							<td><?php echo $member['id']; ?></td>

							<td>
								<?php echo htmlspecialchars($member['member_name']); ?>
							</td>

							<td>
								<?php echo htmlspecialchars($member['designation']); ?>
							</td>

							<td>
								<?php echo htmlspecialchars($member['icon']); ?>
							</td>

							<td>

								<a class="action" href="admin.php?edit_member=<?php echo $member['id']; ?>">
									Edit
								</a>

								<a class="action delete" href="admin.php?delete_member=<?php echo $member['id']; ?>"
									onclick="return confirm('Are you sure you want to delete this member?');">
									Delete
								</a>

							</td>

						</tr>

					<?php endwhile; ?>

				</table>

			</div>

		</div>

		<!-- GALLERY -->

		<div class="form-box">

			<h2>Gallery</h2>

			<form method="POST" enctype="multipart/form-data">

				<label>Select Image</label>

				<input type="file" name="gallery_image" accept=".jpg,.jpeg,.png,.webp" required>

				<button type="submit" name="upload_image">
					+ Upload Image
				</button>

			</form>

		</div>

		<!-- GALLERY IMAGES -->
		<?php if ($edit_gallery): ?>

			<div class="form-box">

				<h2>Edit Gallery Image</h2>

				<form method="POST" enctype="multipart/form-data">

					<input type="hidden" name="gallery_id" value="<?php echo $edit_gallery['id']; ?>">

					<label>Current Image</label>

					<br><br>

					<img src="<?php echo htmlspecialchars($edit_gallery['image_path']); ?>"
						style="width:200px;height:150px;object-fit:cover;border-radius:5px;">

					<br><br>

					<label>Select New Image</label>

					<input type="file" name="gallery_image" accept=".jpg,.jpeg,.png,.webp" required>

					<button type="submit" name="edit_gallery">
						Update Image
					</button>

				</form>

			</div>

		<?php endif; ?>

		<div class="table-box">

			<h2>Gallery Images</h2>

			<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:20px;">

				<?php

				$gallery = $conn->query("SELECT * FROM gallery ORDER BY id DESC");

				while ($image = $gallery->fetch_assoc()):

					?>

					<div style="background:#f4f6f9; padding:15px; text-align:center; border-radius:7px;">

						<img src="<?php echo htmlspecialchars($image['image_path']); ?>"
							style="width:100%; height:180px; object-fit:cover; border-radius:5px;">

						<p style="margin:10px 0;">
							<?php echo htmlspecialchars($image['image_name']); ?>
						</p>

						<a class="action" href="admin.php?edit_gallery=<?php echo $image['id']; ?>">
							Edit
						</a>

						<a class="action delete" href="admin.php?delete_gallery=<?php echo $image['id']; ?>"
							onclick="return confirm('Are you sure you want to delete this image?');">
							Delete
						</a>

					</div>

				<?php endwhile; ?>

			</div>

		</div>
		<!-- ADD CLIENT -->

		<div class="form-box">

			<h2>Add New Client</h2>

			<form method="POST">

				<div class="form-grid">

					<div>
						<label>Client Name</label>
						<input type="text" name="client_name" required>
					</div>

					<div>
						<label>CNIC</label>
						<input type="text" name="cnic" placeholder="XXXXX-XXXXXXX-X">
					</div>

					<div>
						<label>Email</label>
						<input type="email" name="email">
					</div>

					<div>
						<label>Phone Number</label>
						<input type="text" name="phone">
					</div>

					<div>
						<label>Designation</label>
						<input type="text" name="designation">
					</div>

					<div>
						<label>Service</label>

						<select name="service">
							<?php
							$service_options_add = $conn->query(
								"SELECT title FROM services WHERE status='Active' ORDER BY sort_order ASC, id ASC"
							);

							if ($service_options_add && $service_options_add->num_rows > 0):
								while ($service_item = $service_options_add->fetch_assoc()):
									?>
									<option><?php echo htmlspecialchars($service_item['title']); ?></option>
								<?php
								endwhile;
							else:
								?>
								<option value="">No active service available</option>
							<?php endif; ?>

						</select>

					</div>

					<div>
						<label>Total Fee</label>
						<input type="number" name="total_fee" step="0.01" required>
					</div>

					<div>
						<label>Initial Paid Amount</label>
						<input type="number" name="paid_amount" step="0.01" value="0" required>
					</div>

					<div>
						<label>Status</label>

						<select name="status">

							<option value="Active">Active</option>
							<option value="Inactive">Inactive</option>

						</select>

					</div>

					<div class="full">

						<button type="submit" name="add_client">
							+ Add Client
						</button>

					</div>

				</div>

			</form>

		</div>

		<?php if ($edit_client): ?>

			<div class="form-box">

				<h2>Edit Client</h2>

				<form method="POST">

					<input type="hidden" name="client_id" value="<?php echo $edit_client['id']; ?>">

					<div class="form-grid">

						<div>
							<label>Client Name</label>
							<input type="text" name="client_name"
								value="<?php echo htmlspecialchars($edit_client['client_name']); ?>" required>
						</div>

						<div>
							<label>CNIC</label>
							<input type="text" name="cnic" value="<?php echo htmlspecialchars($edit_client['cnic']); ?>">
						</div>

						<div>
							<label>Email</label>
							<input type="email" name="email" value="<?php echo htmlspecialchars($edit_client['email']); ?>">
						</div>

						<div>
							<label>Phone Number</label>
							<input type="text" name="phone" value="<?php echo htmlspecialchars($edit_client['phone']); ?>">
						</div>

						<div>
							<label>Designation</label>
							<input type="text" name="designation"
								value="<?php echo htmlspecialchars($edit_client['designation']); ?>">
						</div>

						<div>
							<label>Service</label>

							<select name="service">
								<?php
								$service_options_edit = $conn->query(
									"SELECT title FROM services ORDER BY sort_order ASC, id ASC"
								);

								if ($service_options_edit && $service_options_edit->num_rows > 0):
									while ($service_item = $service_options_edit->fetch_assoc()):
										?>
											<option <?php echo $edit_client['service'] == $service_item['title'] ? 'selected' : ''; ?>>
											<?php echo htmlspecialchars($service_item['title']); ?>
										</option>
									<?php
									endwhile;
								else:
									?>
									<option selected><?php echo htmlspecialchars($edit_client['service']); ?></option>
								<?php endif; ?>

							</select>

						</div>

						<div>
							<label>Total Fee</label>
							<input type="number" name="total_fee" step="0.01"
								value="<?php echo $edit_client['total_fee']; ?>" required>
						</div>

						<div>
							<label>Status</label>

							<select name="status">

								<option value="Active" <?php if ($edit_client['status'] == "Active")
									echo "selected"; ?>>
									Active
								</option>

								<option value="Inactive" <?php if ($edit_client['status'] == "Inactive")
									echo "selected"; ?>>
									Inactive
								</option>

							</select>

						</div>

						<div class="full">

							<button type="submit" name="edit_client">
								Update Client
							</button>

						</div>

					</div>

				</form>

			</div>

		<?php endif; ?>
		<!-- PAYMENT -->

		<div class="form-box">

			<h2>Record New Payment</h2>

			<div class="payment-box">

				<form method="POST">

					<label>Select Client</label>

					<select name="client_id" required>

						<option value="">-- Select Client --</option>

						<?php while ($pc = $payment_clients->fetch_assoc()): ?>

							<option value="<?php echo $pc['id']; ?>">
								<?php echo htmlspecialchars($pc['client_name']); ?>
							</option>

						<?php endwhile; ?>

					</select>

					<label>Payment Amount</label>

					<input type="number" name="amount" step="0.01" required>

					<label>Payment Date</label>

					<input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>

					<label>Notes</label>

					<input type="text" name="notes" placeholder="e.g. Second installment">

					<button type="submit" name="add_payment">
						+ Add Payment
					</button>

				</form>

			</div>

		</div>

		<!-- CLIENT QUERIES -->
		<!-- CLIENT QUERIES -->

		<div class="table-box">

			<h2>Client Queries</h2>

			<div class="table-wrapper">

				<table>

					<tr>
						<th>ID</th>
						<th>Client Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Message</th>
						<th>Status</th>
						<th>Action</th>
					</tr>

					<?php while ($query = $queries->fetch_assoc()): ?>

						<tr>

							<td><?php echo $query['id']; ?></td>

							<td>
								<?php
								echo htmlspecialchars(
									$query['first_name'] . " " . $query['last_name']
								);
								?>
							</td>

							<td>
								<?php echo htmlspecialchars($query['email']); ?>
							</td>

							<td>
								<?php echo htmlspecialchars($query['phone']); ?>
							</td>

							<td>
								<?php echo htmlspecialchars($query['message']); ?>
							</td>

							<td>

								<?php if ($query['status'] == 'Resolved'): ?>

									<span class="active">Resolved</span>

								<?php else: ?>

									<span class="pending">Pending</span>

								<?php endif; ?>

							</td>

							<td>

								<?php if ($query['status'] != 'Resolved'): ?>

									<a class="action" href="admin.php?resolve_query=<?php echo $query['id']; ?>">
										Resolve
									</a>

								<?php endif; ?>

								<a class="action delete" href="admin.php?delete_query=<?php echo $query['id']; ?>"
									onclick="return confirm('Are you sure you want to delete this query?');">
									Delete
								</a>

							</td>

						</tr>

					<?php endwhile; ?>

				</table>

			</div>

		</div>

		<!-- CLIENT TABLE -->

		<div class="table-box">

			<h2>Client Records</h2>

			<div class="table-wrapper">

				<table>

					<tr>

						<th>ID</th>
						<th>Name</th>
						<th>CNIC</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Designation</th>
						<th>Service</th>
						<th>Total Fee</th>
						<th>Paid</th>
						<th>Pending</th>
						<th>Status</th>
						<th>Actions</th>

					</tr>

					<?php while ($row = $result->fetch_assoc()): ?>

						<?php
						$pending = $row['total_fee'] - $row['paid_amount'];
						?>

						<tr>

							<td><?php echo $row['id']; ?></td>

							<td>
								<?php echo htmlspecialchars($row['client_name']); ?>
							</td>

							<td>
								<?php echo htmlspecialchars($row['cnic']); ?>
							</td>

							<td>
								<?php echo htmlspecialchars($row['email']); ?>
							</td>

							<td>
								<?php echo htmlspecialchars($row['phone']); ?>
							</td>

							<td>
								<?php echo htmlspecialchars($row['designation']); ?>
							</td>

							<td>
								<?php echo htmlspecialchars($row['service']); ?>
							</td>

							<td>
								Rs. <?php echo number_format($row['total_fee']); ?>
							</td>

							<td class="paid">
								Rs. <?php echo number_format($row['paid_amount']); ?>
							</td>

							<td class="pending">
								Rs. <?php echo number_format($pending); ?>
							</td>

							<td class="<?php echo strtolower($row['status']); ?>">
								<?php echo $row['status']; ?>
							</td>

							<td>

								<a class="action" href="admin.php?edit=<?php echo $row['id']; ?>">
									Edit
								</a>

								<a class="action delete" href="admin.php?delete=<?php echo $row['id']; ?>"
									onclick="return confirm('Are you sure you want to delete this client?');">
									Delete
								</a>

							</td>

						</tr>

					<?php endwhile; ?>

				</table>

			</div>

		</div>

	</div>

</body>

</html>
