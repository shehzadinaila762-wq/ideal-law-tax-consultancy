-- Database: ideal_law
-- Setup for MySQL / MariaDB

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ==========================================================
-- Table: clients
-- Stores each client and how much they owe/paid
-- ==========================================================

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `service` varchar(150) DEFAULT NULL,
  `total_fee` decimal(10, 2) DEFAULT 0.00,
  `paid_amount` decimal(10, 2) DEFAULT 0.00,
  `status` enum('Active', 'Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cnic` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `clients` (
    `id`, `client_name`, `phone`, `service`, `total_fee`, `paid_amount`,
    `status`, `created_at`, `cnic`, `email`, `designation`
  )
VALUES (
    8, 'client', '03000000000000', 'Legal Services', 10000.00, 12000.00,
    'Inactive', '2026-08-23 20:16:13', '123456677899', 'test@gmail.com', 'test updated'
  );

-- ==========================================================
-- Table: gallery
-- Stores uploaded images shown on the website gallery
-- ==========================================================

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `gallery` (`id`, `image_name`, `image_path`, `created_at`)
VALUES (
    3,
    'WhatsApp Image 2026-08-22 at 6.07.10 PM.jpeg',
    'uploads/1787515864_WhatsApp Image 2026-08-22 at 6.07.10 PM.jpeg',
    '2026-08-23 20:11:04'
  ),
  (
    4,
    'WhatsApp Image 2026-08-22 at 5.54.59 PM (1).jpeg',
    'uploads/1787517223_WhatsApp Image 2026-08-22 at 5.54.59 PM (1).jpeg',
    '2026-08-23 20:33:43'
  );

-- ==========================================================
-- Table: payments
-- Stores each payment a client makes (linked via client_id)
-- ==========================================================

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `amount` decimal(10, 2) NOT NULL,
  `payment_date` date NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `payments` (
    `id`, `client_id`, `amount`, `payment_date`, `notes`, `created_at`
  )
VALUES (
    10, 8, 2000.00, '2026-08-23', 'Initial payment', '2026-08-23 20:16:13'
  ),
  (
    11, 8, 10000.00, '2026-08-23', 'test payment', '2026-08-23 20:18:18'
  );

-- ==========================================================
-- Table: queries
-- Stores contact form messages from website visitors
-- ==========================================================

CREATE TABLE `queries` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending', 'Resolved') DEFAULT 'Pending'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `queries` (
    `id`, `first_name`, `last_name`, `email`, `phone`, `message`, `created_at`, `status`
  )
VALUES (4, 'Naila', 'client', 'test@gmail.com', '030000000000000', 'test query', '2026-08-22 17:25:03', 'Resolved'),
  (5, 'Naila', 'Shehzadi', 'test@gmail.com', '03000000000000', 'query test', '2026-08-22 17:28:38', 'Pending'),
  (6, 'Naila', 'client', 'test@gmail.com', '030000000000000', 'test query', '2026-08-22 17:40:26', 'Resolved'),
  (7, 'Naila', 'client', 'test@gmail.com', '030000000000000', 'test query', '2026-08-22 17:41:34', 'Pending'),
  (8, 'test', 'client', 'test@gmail.com', '030000000000000', 'TEST QUERY', '2026-08-22 18:41:53', 'Pending'),
  (9, 'test', 'client', 'test@gmail.com', '030000000000000', 'TEST QUERY', '2026-08-22 18:46:58', 'Pending'),
  (10, 'WAHEED', 'client', 'test@gmail.com', '03000000000000', 'QUERYYYYY ', '2026-08-22 18:47:39', 'Pending'),
  (11, 'WAHEED', 'client', 'test@gmail.com', '03000000000000', 'QUERYYYYY ', '2026-08-22 18:56:39', 'Pending'),
  (12, 'WAHEED', 'client', 'test@gmail.com', '03000000000000', 'QUERYYYYY ', '2026-08-22 19:14:13', 'Pending'),
  (13, 'WAHEED', 'client', 'test@gmail.com', '03000000000000', 'QUERYYYYY ', '2026-08-22 19:16:41', 'Pending'),
  (14, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 20:21:02', 'Resolved'),
  (15, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 20:27:14', 'Pending'),
  (16, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 20:42:40', 'Pending'),
  (17, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 20:44:56', 'Pending'),
  (18, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 20:48:13', 'Pending'),
  (19, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 20:51:48', 'Pending'),
  (20, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 20:52:17', 'Pending'),
  (21, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 20:56:01', 'Pending'),
  (22, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 20:56:15', 'Pending'),
  (23, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-23 21:00:29', 'Pending'),
  (24, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-24 13:00:58', 'Pending'),
  (25, 'test', 'user', 'test@example.com', '3332050000', 'test user query', '2026-08-24 13:18:55', 'Pending');

-- ==========================================================
-- Table: services
-- Stores service categories and their list items
-- ==========================================================

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `icon` varchar(20) DEFAULT '⚖️',
  `items` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('Active', 'Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `services` (`id`, `title`, `icon`, `items`, `sort_order`, `status`, `created_at`)
VALUES (1, 'Legal Services', '⚖️', 'Legal Consultation & Advice\nLegal Documentation & Drafting\nContract & Agreement Drafting\nProperty & Land Matters\nCivil & Criminal Matters\nFamily Law Matters\nCorporate Legal Advisory\nLegal Notices\nCourt & Litigation Services', 1, 'Active', '2026-08-24 12:00:00'),
  (2, 'Tax Consultation', '💰', 'Income Tax Return\nGeneral Sales Tax (GST)\nRegional Sales Tax (BRA)\nFederal Excise Tax\nPOS (Point of Sale)\nNTN (National Tax Number)', 2, 'Active', '2026-08-24 12:00:00'),
  (3, 'Corporate Services', '🏢', 'Company Registration\nFirm Registration\nPakistan Engineering Council Registration\nTrademark Registration\nImport / Export License\nChamber of Commerce Registration\nNGO / NPO Registration', 3, 'Active', '2026-08-24 12:00:00'),
  (4, 'Documentation Services', '📄', 'Legal Document Preparation\nAgreements & Contracts\nAffidavits\nApplications & Official Documents\nPower of Attorney\nLegal Notices\nBusiness & Company Documents\nTax & Regulatory Documents', 4, 'Active', '2026-08-24 12:00:00');

-- ==========================================================
-- Table: team_members
-- Stores staff shown on the website's team page
-- ==========================================================

CREATE TABLE `team_members` (
  `id` int(11) NOT NULL,
  `member_name` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `icon` varchar(20) DEFAULT '⚖️',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `team_members` (
    `id`, `member_name`, `designation`, `icon`, `created_at`
  )
VALUES (2, 'Adv.Haqdad Azad', 'C.E.O', '⚖️', '2026-08-24 12:53:40'),
  (3, 'Adv. Palwasha Shehzadi', 'Advocate', '⚖️', '2026-08-24 12:54:42'),
  (4, 'Adv. Hamad Nasar', 'Advocate', '⚖️', '2026-08-24 12:55:21'),
  (5, 'Naila Shehzadi', 'Tax Consultant', '💼', '2026-08-24 12:57:37');

-- ==========================================================
-- Primary keys
-- ==========================================================

ALTER TABLE `clients` ADD PRIMARY KEY (`id`);
ALTER TABLE `gallery` ADD PRIMARY KEY (`id`);
ALTER TABLE `payments` ADD PRIMARY KEY (`id`);
ALTER TABLE `queries` ADD PRIMARY KEY (`id`);
ALTER TABLE `services` ADD PRIMARY KEY (`id`);
ALTER TABLE `team_members` ADD PRIMARY KEY (`id`);

-- ==========================================================
-- Auto-increment starting points (so new rows get the next free id)
-- ==========================================================

ALTER TABLE `clients` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 9;
ALTER TABLE `gallery` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 5;
ALTER TABLE `payments` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 12;
ALTER TABLE `queries` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 26;
ALTER TABLE `services` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 5;
ALTER TABLE `team_members` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 6;

COMMIT;
