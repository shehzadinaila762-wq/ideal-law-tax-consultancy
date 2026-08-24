<?php

require __DIR__ . '/../config/database.php';

if ($conn->connect_error) {
	die("Database connection failed: " . $conn->connect_error);
}

if (isset($_POST['send_query'])) {

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$message = $_POST['message'];

	$stmt = $conn->prepare(
		"INSERT INTO queries (first_name, last_name, email, phone, message)
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

	if (!$stmt->execute()) {
		die("Query could not be saved: " . $stmt->error);
	}

	$stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Ideal Law and Tax Consultancy</title>
	<link rel="stylesheet" href="assets/css/index.css?v=5">

</head>

<body>

	<header>

		<div class="logo">
			<span class="logo-icon"> ⚖️ </span>
			<span>IDEAL LAW & TAX CONSULTANCY </span>
		</div>

		<nav>
			<a href="index.php">Home</a>
			<a href="services.php">Services</a>
			<a href="about.php">About</a>
			<a href="gallery.php">Gallery</a>
			<a href="team.php">Our Team</a>
			<a href="contact.php">Contact</a>
			<a href="admin.php">Admin Panel</a>
		</nav>

	</header>

	<section class="hero" id="home">

		<div>

			<h1>Ideal Law & Tax Consultancy</h1>

			<p>
				Professional Legal, Tax, Corporate and Documentation Services
				for Individuals, Businesses and Organizations.
			</p>

			<a href="#contact" class="btn">Contact Us</a>

		</div>

	</section>

	<section id="services">

		<h2 class="section-title">Our Services</h2>

		<div class="services">

			<div class="card">

				<h3>⚖️ Legal Services</h3>

				<ul>
					<li>Legal Consultation & Advice</li>
					<li>Legal Documentation & Drafting</li>
					<li>Contract & Agreement Drafting</li>
					<li>Property & Land Matters</li>
					<li>Civil & Criminal Matters</li>
					<li>Family Law Matters</li>
					<li>Corporate Legal Advisory</li>
					<li>Legal Notices</li>
					<li>Court & Litigation Services</li>
				</ul>

			</div>

			<div class="card">

				<h3>💰 Tax Consultation</h3>

				<ul>
					<li>Income Tax Return</li>
					<li>General Sales Tax (GST)</li>
					<li>Regional Sales Tax (BRA)</li>
					<li>Federal Excise Tax</li>
					<li>POS (Point of Sale)</li>
					<li>NTN (National Tax Number)</li>
				</ul>

			</div>

			<div class="card">

				<h3>🏢 Corporate Services</h3>

				<ul>
					<li>Company Registration</li>
					<li>Firm Registration</li>
					<li>Pakistan Engineering Council Registration</li>
					<li>Trademark Registration</li>
					<li>Import / Export License</li>
					<li>Chamber of Commerce Registration</li>
					<li>NGO / NPO Registration</li>
				</ul>

			</div>

			<div class="card">

				<h3>📄 Documentation Services</h3>

				<ul>
					<li>Legal Document Preparation</li>
					<li>Agreements & Contracts</li>
					<li>Affidavits</li>
					<li>Applications & Official Documents</li>
					<li>Power of Attorney</li>
					<li>Legal Notices</li>
					<li>Business & Company Documents</li>
					<li>Tax & Regulatory Documents</li>
				</ul>

			</div>

		</div>

	</section>

	<section class="about" id="about">

		<div class="about-container">

			<div class="about-content">

				<h2 class="section-title">About Us</h2>

				<p>
					Ideal Law and Tax Consultancy provides professional
					legal, tax, corporate and documentation services.
				</p>

				<p>
					Our aim is to provide reliable, professional and
					client-focused solutions for individuals, businesses
					and organizations.
				</p>

				<p>
					We focus on professional advice, accurate documentation,
					regulatory compliance and quality client service.
				</p>

			</div>

			<div class="fbr-card">

				<img src="assets/images/fbrlogo.jpeg">

				<h3>Tax & Regulatory Services</h3>

				<p>
					Professional assistance for tax registration,
					tax returns, sales tax matters and regulatory
					documentation.
				</p>

				<div class="fbr-line"></div>

				<p class="fbr-note">
					FBR-related tax and compliance assistance
				</p>

			</div>

		</div>

	</section>
	<!-- GALLERY SECTION -->

	<section id="gallery">

		<h2 class="section-title">Our Gallery</h2>

		<div class="gallery-container">

			<?php

			$gallery = $conn->query(
				"SELECT * FROM gallery ORDER BY id DESC"
			);

			if ($gallery && $gallery->num_rows > 0):

				while ($image = $gallery->fetch_assoc()):

					?>

					<div class="gallery-item">
						<a href="<?php echo htmlspecialchars($image['image_path']); ?>" target="_blank">
							<img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Ideal Law and Tax Consultancy">
						</a>

					</div>

					<?php

				endwhile;

			else:

				?>

				<p class="no-gallery">
					No images available yet.
				</p>

									<?php endif; ?>

		</div>

	</section>

	<!-- GALLERY -->

	<section id="team">

		<h2 class="section-title">Our Team</h2>

		<div class="team">

			<?php

			$team = $conn->query("SELECT * FROM team_members ORDER BY id ASC");

			if ($team && $team->num_rows > 0):

				while ($member = $team->fetch_assoc()):

					?>

					<div class="member">

						<div class="member-icon">
																			<?php echo htmlspecialchars($member['icon']); ?>
						</div>

						<h3>
																			<?php echo htmlspecialchars($member['member_name']); ?>
						</h3>

						<p>
																			<?php echo htmlspecialchars($member['designation']); ?>
						</p>

					</div>

					<?php

				endwhile;

			else:

				?>

				<p>No team members available yet.</p>

									<?php endif; ?>

		</div>

	</section>

	<section class="contact" id="contact">

		<div class="contact-card">

			<h3>Send Your Query</h3>

			<form method="POST" action="index.php">

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

		<a href="admin.php" class="admin-btn">Admin Panel</a>

	</section>

	<footer>

		<p>
			© 2026 Ideal Law and Tax Consultancy. All Rights Reserved.
		</p>

	</footer>

</body>

</html>
