<?php
require_once __DIR__ . '/../config/database.php';

$conn->query("CREATE TABLE IF NOT EXISTS services (
	id INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(150) NOT NULL,
	icon VARCHAR(20) DEFAULT '⚖️',
	items TEXT NOT NULL,
	sort_order INT DEFAULT 0,
	status ENUM('Active','Inactive') DEFAULT 'Active',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$service_cards = [];

$services_result = $conn->query(
	"SELECT title, icon, items
	 FROM services
	 WHERE status='Active'
	 ORDER BY sort_order ASC, id ASC"
);

if ($services_result && $services_result->num_rows > 0) {

	while ($service = $services_result->fetch_assoc()) {

		$items = preg_split('/\r\n|\r|\n/', $service['items']);
		$clean_items = [];

		foreach ($items as $item) {

			$item = trim($item);

			if ($item !== '') {
				$clean_items[] = $item;
			}
		}

		$service_cards[] = [
			'title' => $service['title'],
			'icon' => $service['icon'],
			'items' => $clean_items
		];
	}
}

if (count($service_cards) === 0) {

	$service_cards = [
		[
			'title' => 'Legal Services',
			'icon' => '⚖️',
			'items' => [
				'Legal Consultation & Advice',
				'Legal Documentation & Drafting',
				'Contract & Agreement Drafting',
				'Property & Land Matters',
				'Civil & Criminal Matters',
				'Family Law Matters',
				'Corporate Legal Advisory',
				'Legal Notices',
				'Court & Litigation Services'
			]
		],
		[
			'title' => 'Tax Consultation',
			'icon' => '💰',
			'items' => [
				'Income Tax Return',
				'General Sales Tax (GST)',
				'Regional Sales Tax (BRA)',
				'Federal Excise Tax',
				'POS (Point of Sale)',
				'NTN (National Tax Number)'
			]
		],
		[
			'title' => 'Corporate Services',
			'icon' => '🏢',
			'items' => [
				'Company Registration',
				'Firm Registration',
				'Pakistan Engineering Council Registration',
				'Trademark Registration',
				'Import / Export License',
				'Chamber of Commerce Registration',
				'NGO / NPO Registration'
			]
		],
		[
			'title' => 'Documentation Services',
			'icon' => '📄',
			'items' => [
				'Legal Document Preparation',
				'Agreements & Contracts',
				'Affidavits',
				'Applications & Official Documents',
				'Power of Attorney',
				'Legal Notices',
				'Business & Company Documents',
				'Tax & Regulatory Documents'
			]
		]
	];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Services - Ideal Law & Tax Consultancy</title>

	<link rel="stylesheet" href="assets/css/index.css?v=6">
</head>

<body>

	<?php include __DIR__ . '/header.php'; ?>

	<section id="services">

		<h2 class="section-title">Our Services</h2>

		<div class="services">
			<?php foreach ($service_cards as $service): ?>

				<div class="card">

					<h3><?php echo htmlspecialchars($service['icon'] . ' ' . $service['title']); ?></h3>

					<ul>
						<?php foreach ($service['items'] as $item): ?>
							<li><?php echo htmlspecialchars($item); ?></li>
						<?php endforeach; ?>
					</ul>

				</div>

			<?php endforeach; ?>

		</div>

	</section>

	<footer>

		<p>
			© 2026 Ideal Law and Tax Consultancy. All Rights Reserved.
		</p>

	</footer>

</body>

</html>
