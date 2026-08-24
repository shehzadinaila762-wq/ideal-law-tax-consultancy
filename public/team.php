<?php

require __DIR__ . '/../config/database.php';

if ($conn->connect_error) {
	die("Database connection failed: " . $conn->connect_error);
}

$team = $conn->query("SELECT * FROM team_members ORDER BY id ASC");

?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Our Team - Ideal Law & Tax Consultancy</title>

	<link rel="stylesheet" href="assets/css/index.css?v=6">

</head>

<body>

	<?php include __DIR__ . '/header.php'; ?>

	<section id="team">

		<h2 class="section-title">Our Team</h2>

		<div class="team">

			<?php

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

	</section>

	<?php include __DIR__ . '/footer.php'; ?>

</body>

</html>
