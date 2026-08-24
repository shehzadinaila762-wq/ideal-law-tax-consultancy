<?php

require __DIR__ . '/../config/database.php';

if ($conn->connect_error) {
	die("Database connection failed: " . $conn->connect_error);
}

$gallery = $conn->query(
	"SELECT * FROM gallery ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Gallery - Ideal Law & Tax Consultancy</title>

	<link rel="stylesheet" href="assets/css/index.css?v=6">

	<style>
		.gallery-container {
			width: 90%;
			max-width: 1200px;
			margin: 30px auto 60px;
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: 20px;
		}

		.gallery-item {
			width: 100%;
			height: 250px;
			background: white;
			padding: 10px;
			border-radius: 8px;
			box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
			overflow: hidden;
		}

		.gallery-item a {
			display: block;
			width: 100%;
			height: 100%;
		}

		.gallery-item img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			display: block;
			border-radius: 6px;
		}

		.no-gallery {
			text-align: center;
			width: 100%;
		}

		@media (max-width: 700px) {

			.gallery-container {
				grid-template-columns: 1fr;
			}

			.gallery-item {
				height: 250px;
			}

		}
	</style>

</head>

<body>

	<?php include __DIR__ . '/header.php'; ?>

	<section id="gallery">

		<h2 class="section-title">Our Gallery</h2>

		<div class="gallery-container">

			<?php

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

	<footer>

		<p>
			© 2026 Ideal Law and Tax Consultancy. All Rights Reserved.
		</p>

	</footer>

</body>

</html>
