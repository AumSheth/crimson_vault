-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2025 at 11:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crimson_vault`
--

-- --------------------------------------------------------

--
-- Table structure for table `accused`
--

CREATE TABLE `accused` (
  `accused_id` int(11) NOT NULL,
  `case_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `skin_colour` varchar(30) DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `current_charges` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accused`
--

INSERT INTO `accused` (`accused_id`, `case_id`, `name`, `father_name`, `gender`, `birthdate`, `height`, `weight`, `skin_colour`, `blood_group`, `current_charges`) VALUES
(1, 1, 'Raju Verma', 'Shyam Verma', 'Male', '1990-06-15', 173.50, 70.20, 'Wheatish', 'B+', 'Robbery and possession'),
(2, 2, 'Danish Qureshi', 'Imran Qureshi', 'Male', '1989-04-20', 178.80, 75.60, 'Fair', 'O+', 'Cyber banking fraud'),
(3, 3, 'Pradeep Naik', 'Suresh Naik', 'Male', '1985-10-12', 166.40, 72.00, 'Dark', 'A-', 'Property dispute'),
(4, 4, 'Rekha Khan', 'Usman Khan', 'Female', '1996-08-05', 159.00, 55.30, 'Fair', 'AB+', 'SIM cloning and phishing'),
(5, 5, 'Aslam Sheikh', 'Firoz Sheikh', 'Male', '1993-07-19', 175.20, 80.00, 'Wheatish', 'B-', 'Snatching chain'),
(6, 6, 'Govind Pawar', 'Mahadev Pawar', 'Male', '1980-01-03', 169.90, 85.50, 'Dark', 'O-', 'Unauthorized construction'),
(7, 7, 'Anjali Patil', 'Sanjay Patil', 'Female', '1995-05-14', 160.20, 52.40, 'Fair', 'A+', 'Aadhaar fraud'),
(8, 8, 'Tushar Solanki', 'Vinod Solanki', 'Male', '1991-03-27', 181.30, 89.70, 'Wheatish', 'AB-', 'Physical assault'),
(9, 9, 'Irfan Shaikh', 'Javed Shaikh', 'Male', '1987-11-04', 170.50, 64.00, 'Dark', 'B+', 'PAN misuse'),
(10, 10, 'Neha Tiwari', 'Mahesh Tiwari', 'Female', '2000-12-09', 162.50, 53.50, 'Fair', 'O+', 'Property title conflict'),
(11, 20, 'Abhishek Jindal', 'Nirmal Jindal', 'Male', '1998-10-10', 176.00, 76.00, 'White', 'B+', 'Murder of Mr. Agarwal'),
(12, 20, 'Tipendra gada', 'Jethalal gada', 'Male', '2000-08-15', 177.00, 60.00, 'Brown', 'AB+', 'Murder');

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `analytics_id` int(11) NOT NULL,
  `case_id` int(11) DEFAULT NULL,
  `prediction` text DEFAULT NULL,
  `generated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `analytics`
--

INSERT INTO `analytics` (`analytics_id`, `case_id`, `prediction`, `generated_at`) VALUES
(1, 1, 'High probability of repeat offender based on crime pattern', '2025-08-15 10:30:00'),
(2, 2, 'Linked to multiple phishing networks across Mumbai region', '2025-08-15 11:10:00'),
(3, 3, 'Civil settlement predicted within 3 months', '2025-08-15 12:00:00'),
(4, 4, 'Fraudulent SIM cloning linked to organized cybercrime group', '2025-08-15 13:25:00'),
(5, 6, 'Risk of large-scale land fraud detected in construction dispute', '2025-08-15 14:45:00'),
(6, 20, 'You can trace the murderer using the DNA on the founded weapon', '2025-09-05 20:19:36');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `action`, `ip_address`, `timestamp`) VALUES
(1, 11, 'User logged in', '::1', '2025-08-24 19:09:06'),
(2, 11, 'User logged in', '::1', '2025-08-25 14:13:02'),
(3, 11, 'User logged in', '::1', '2025-08-25 19:50:41'),
(4, 11, 'User logged in', '::1', '2025-08-25 20:27:25'),
(5, 11, 'User logged in', '::1', '2025-08-26 12:24:11'),
(6, 11, 'User logged in', '::1', '2025-08-26 12:35:14'),
(7, 11, 'User logged in', '::1', '2025-08-26 15:44:37'),
(8, 11, 'User logged in', '::1', '2025-08-26 16:46:36'),
(9, 12, 'User logged in', '::1', '2025-08-26 21:05:26'),
(10, 12, 'User logged in', '::1', '2025-08-29 14:13:56'),
(11, 12, 'User logged in', '::1', '2025-08-29 15:17:54'),
(12, 12, 'User logged in', '::1', '2025-08-29 15:32:07'),
(13, 12, 'User logged in', '::1', '2025-09-01 12:52:30'),
(14, 12, 'User logged in', '::1', '2025-09-02 12:34:32'),
(15, 11, 'User logged in', '::1', '2025-09-02 12:46:48'),
(16, 11, 'User logged in', '::1', '2025-09-02 16:07:34'),
(17, 13, 'User logged in', '::1', '2025-09-05 18:53:24'),
(18, 12, 'User logged in', '::1', '2025-09-05 20:20:10'),
(19, 13, 'User logged in', '::1', '2025-09-06 13:38:58'),
(20, 12, 'User logged in', '::1', '2025-09-06 16:08:44'),
(21, 13, 'User logged in', '::1', '2025-09-06 16:15:30'),
(22, 12, 'User logged in', '::1', '2025-09-08 13:23:39'),
(23, 13, 'User logged in', '::1', '2025-09-08 13:54:55'),
(24, 11, 'User logged in', '::1', '2025-09-08 16:08:48'),
(25, 11, 'User logged in', '::1', '2025-09-09 13:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `case_id` int(11) NOT NULL,
  `officer_id` int(11) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `case_type` varchar(15) DEFAULT NULL,
  `date_filed` date DEFAULT NULL,
  `status` varchar(15) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cases`
--

INSERT INTO `cases` (`case_id`, `officer_id`, `title`, `case_type`, `date_filed`, `status`, `description`, `created_at`) VALUES
(1, 3, 'Robbery at Kurla Market', 'Criminal', '2025-04-01', 'Open', 'Theft reported near vegetable lane', '2025-04-01 04:30:00'),
(2, 5, 'Online Banking Scam', 'Cybercrime', '2025-04-08', 'Under Trial', 'Victim lost ₹1.5L through phishing', '2025-10-04 08:45:08'),
(3, 7, 'Land Dispute in Satara', 'Civil', '2025-03-12', 'Closed', 'Dispute between two families', '2025-03-12 05:55:00'),
(4, 9, 'SIM Cloning Incident', 'Cybercrime', '2025-02-26', 'Under Trial', 'Fraud calls made via fake SIM', '2025-10-04 08:45:08'),
(5, 10, 'Chain Snatching at Dadar', 'Criminal', '2025-05-03', 'Closed', 'Complaint filed by college student', '2025-05-03 09:00:00'),
(6, 5, 'Illegal Construction Case', 'Civil', '2025-06-01', 'Open', 'Encroachment of road-facing land', '2025-06-01 10:30:00'),
(7, 3, 'Aadhaar Card Fraud', 'Cybercrime', '2025-06-10', 'Open', 'Forged document used for bank loan', '2025-06-10 06:10:00'),
(8, 7, 'Roadside Fight Complaint', 'Criminal', '2025-05-18', 'Closed', 'Brawl between bike riders', '2025-05-18 04:40:00'),
(9, 9, 'PAN Card Misuse', 'Cybercrime', '2025-07-07', 'Under Trial', 'Someone used fake PAN for sim', '2025-10-04 08:45:08'),
(10, 10, 'Property Title Dispute', 'Civil', '2025-08-01', 'Open', 'Title documents under investigation', '2025-08-01 03:30:00'),
(11, 11, 'Online Money Transfrer Fraud', 'Cyber', '2025-08-20', 'Closed', 'Please check the processing', '2025-08-25 15:32:45'),
(19, 12, 'Threat to Mr. Ambani', 'Criminal', '2025-09-01', 'Under Trial', 'No description as of now', '2025-10-04 08:45:08'),
(20, 12, 'Murder of Mr. Agarwal', 'Criminal', '2025-09-02', 'Under Trial', 'On 2/9/2025. A murder has been registered on 12:47 PM on Tuesday.', '2025-10-04 08:45:08');

-- --------------------------------------------------------

--
-- Table structure for table `evidence`
--

CREATE TABLE `evidence` (
  `evidence_id` int(11) NOT NULL,
  `case_id` int(11) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `access_level` varchar(15) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evidence`
--

INSERT INTO `evidence` (`evidence_id`, `case_id`, `uploaded_by`, `file_name`, `file_path`, `file_type`, `access_level`, `uploaded_at`) VALUES
(1, 1, 3, 'crime_scene_report.pdf', 'c:/xampp/htdocs/crimson_vault/uploads/crime_scene_report.pdf', 'pdf', 'public', '2025-08-10 10:15:00'),
(2, 2, 7, 'cyber_fraud_analysis.pdf', 'c:/xampp/htdocs/crimson_vault/uploads/cyber_fraud_analysis.pdf', 'pdf', 'confidential', '2025-08-11 11:30:00'),
(3, 3, 4, 'property_dispute_summary.pdf', 'c:/xampp/htdocs/crimson_vault/uploads/property_dispute_summary.pdf', 'pdf', 'restricted', '2025-08-12 09:45:00'),
(4, 4, 9, 'sim_cloning_casefile.pdf', 'c:/xampp/htdocs/crimson_vault/uploads/sim_cloning_casefile.pdf', 'pdf', 'confidential', '2025-08-13 14:20:00'),
(5, 6, 5, 'construction_fraud_docs.pdf', 'c:/xampp/htdocs/crimson_vault/uploads/construction_fraud_docs.pdf', 'pdf', 'public', '2025-08-14 16:50:00'),
(7, 11, 11, 'Online_Money_Transfer_Case_Report.pdf', 'uploads/evidence/1756113019_ff3cb5b5_Online_Money_Transfer_Case_Report.pdf.enc', 'pdf', 'Private', '2025-08-25 14:40:19'),
(8, 20, 12, 'murder of mr agarwal.pdf', 'uploads/evidence/1759566880_d770f409_murder_of_mr_agarwal.pdf.enc', 'pdf', 'Private', '2025-10-04 14:04:40');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `fdb_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`fdb_id`, `user_id`, `message`, `created_at`) VALUES
(1, 2, 'The case management interface is easy to use, but search filters could be improved.', '2025-08-15 09:45:00'),
(2, 3, 'Please add a feature to export case reports directly in PDF format.', '2025-08-15 10:20:00'),
(3, 4, 'Sometimes the system logs me out automatically while reviewing evidence.', '2025-08-15 11:05:00'),
(4, 6, 'Notifications are very helpful, but it would be great if we had SMS alerts too.', '2025-08-15 12:15:00'),
(5, 8, 'Overall smooth experience, but uploading large evidence files takes time.', '2025-08-15 13:30:00'),
(7, 1, 'Very nice', '2025-08-26 12:52:21'),
(8, 1, 'Nice UI ', '2025-08-26 12:54:58'),
(9, 1, 'Easy to use functionality', '2025-08-26 12:56:20'),
(10, 1, 'Nice ', '2025-08-26 12:58:06'),
(11, 1, 'Good', '2025-08-26 13:11:11'),
(12, 11, 'Very nice :)', '2025-08-26 13:17:52');

-- --------------------------------------------------------

--
-- Table structure for table `judgments`
--

CREATE TABLE `judgments` (
  `judgment_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `judgment_text` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `judgments`
--

INSERT INTO `judgments` (`judgment_id`, `case_id`, `user_id`, `judgment_text`, `created_at`) VALUES
(1, 6, 13, 'The constuction was done without proper legal documentation and also didn\'t followed the safety regulations too. And for that charges the court is ordering the related police department to destroy the construction and municipal corporation to take the complete land ownership back to the government', '2025-09-05 19:45:19');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `mfa_enabled` tinyint(1) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `role_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`user_id`, `full_name`, `email`, `phone`, `password_hash`, `mfa_enabled`, `last_login`, `status`, `role_name`) VALUES
(1, 'Aum Sheth', 'aums35470@gmail.com', '8849081153', '$2y$10$jjzfrSRsks9H0aeowiQBLuOl3UpW1Vfc82o0J4FpOpFdzecqfcIES', 1, '2025-09-08 13:45:35', 'active', 'Admin'),
(2, 'Anjali Sharma', 'anjali.sharma@example.com', '9823054789', '$2y$10$19V0HL5fdz.lljlPWGgToeieGIB.hCH5Qwc3jMCjpBcjp8O9kVTzC', 0, '2025-08-02 09:30:00', 'active', 'Legal Personnel'),
(3, 'Vikram Patil', 'vikram.patil@example.com', '9812345678', '$2y$10$408Y.cBU.PoXnwwJ05Di8OF25QRrU6RDODVg162CRxO.qFu2BrxKi', 1, '2025-08-03 11:45:00', 'suspended', 'Police'),
(4, 'Nisha Iyer', 'nisha.iyer@example.com', '9898989898', '$2y$10$tWTY0PaFbGPzjK3sA5JEUOfBuRxOZV5R7RWWget5mLtPB8d6sk9/O', 1, '2025-08-02 15:12:00', 'inactive', 'Legal Personnel'),
(5, 'Karan Joshi', 'karan.joshi@example.com', '9745612345', '$2y$10$RigWI1.UDGyzOIbIb.oeA.g8w4HwDvW7vKFdOeZr4WLaR3x3NnQGW', 0, '2025-08-01 08:20:00', 'active', 'Police'),
(6, 'Meera Das', 'meera.das@example.com', '9654781234', '$2y$10$aaespWw/9Q/g1BEcnW3rBeVcwIh8cENRWH.ylqvIwdIpUn9bTIwp.', 1, '2025-08-03 17:30:00', 'active', 'Legal Personnel'),
(7, 'Aditya Verma', 'aditya.verma@example.com', '9123456780', '$2y$10$GIbE7k5h4CM1g1dJk5be5OtWYkxyb6QbNb2BFnm.0Ix2Rj5fJkkqu', 1, '2025-08-04 14:40:00', 'suspended', 'Police'),
(8, 'Swati Kulkarni', 'swati.kulkarni@example.com', '9876598765', '$2y$10$YoPAEsNXWbhp/hsbDU./mu2ZzMJQpqatF5wzpdhDNuC54mK3BJBIW', 0, '2025-08-01 07:15:00', 'active', 'Legal Personnel'),
(9, 'Ramesh Pawar', 'ramesh.pawar@example.com', '9800011223', '$2y$10$wl4axRzi/1B3GBRf4T1zv.6DlpwhDqPXHEJyWIFnPbM/fYn6vz8iW', 1, '2025-08-03 20:10:00', 'inactive', 'Police'),
(10, 'Priya Nair', 'priya.nair@example.com', '9898123456', '$2y$10$3m4/eKc9pGGV3FMxAbq9b.7H.vqSjPpzaALc33HC3YQfq8K2h7uta', 1, '2025-08-05 12:45:00', 'active', 'Police'),
(11, 'Nandini Prajapati', 'nandiniprajapati2509@gmail.com', '9825940032', '$2y$10$QlX/T9PcnmFYknz2sNTlW.WiXb12wipbd/O6Cr51OTiiCDcE/17C6', 0, '2025-09-09 13:28:23', 'active', 'Police'),
(12, 'Dhiren Parmar', 'shethaum84@gmail.com', '9856432556', '$2y$10$DZ8BELcVYOIamgAvs1ki4u8iN71ei8nYBH2DsPBih.dlvW1Ehkzxq', 0, '2025-09-08 13:23:39', 'active', 'Police'),
(13, 'Ayush Chauhan', 'ayushchauhan4747@gmail.com', '9979592134', '$2y$10$4BUdTOIC3Of03iS2eU9cNuS1Q0eLdPmOPtM2wC4QbbjK5imtVSRh.', 0, '2025-09-08 13:54:55', 'active', 'Legal Personnel'),
(14, 'Prince Devmorari', 'editingpp7@gmail.com', '9313062530', '$2y$10$Er.9AWN1hErZtD5jxK1Y3uIhVJdGLnKzPbfES8/2dYun2YNhB4/8W', 0, '2025-09-05 15:22:11', 'active', 'Legal Personnel'),
(16, 'Ashvin Gohil', 'ash.idealman@gmail.com', '7201913709', '$2y$10$aMtcRangW.e9Rqg5Doar3uYk3xyztcbfu2BkABJXq6mPn0TmM2Fr2', 0, '2025-09-22 09:49:24', 'active', 'Legal Personnel'),
(17, 'Jenish Boricha', 'jvb.ombca2023@gmail.com', '7201054125', '$2y$10$wtIa.uNu4Vr4Bc9Zjjle..1KLWQsxZI4MeO/ZvJlO3NhIkBbdt3h2', 0, NULL, 'Active', 'Police'),
(18, 'Priti Solanki', 'ps4791253@gmail.com', '9265027164', '$2y$10$gBRDzJCfIEQT7aZcY2XdK.odGfczz1/JAEkgZIrjSjaT0mljTAmlu', 1, '2025-10-07 20:02:18', 'active', 'Police'),
(19, 'Preeti Solanki', 'psv.ombca2023@gmail.com', '9265027164', '$2y$10$WobZlRGO90yR8h8lL8.eFeGq.zAPYIx8G6HxaVihhuCSWdKlPue86', 1, '2025-10-07 20:03:43', 'active', 'Legal Personnel');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `ntf_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`ntf_id`, `user_id`, `message`, `status`, `created_at`) VALUES
(1, 1, 'Your account settings were updated successfully.', 'unread', '2025-08-15 09:30:00'),
(2, 2, 'A new case has been assigned to you: Online Banking Scam.', 'unread', '2025-08-15 10:00:00'),
(3, 3, 'Reminder: Case hearing scheduled for tomorrow.', 'unread', '2025-08-15 11:15:00'),
(4, 5, 'Evidence has been uploaded to Case #6.', 'unread', '2025-08-15 12:40:00'),
(5, 7, 'System alert: Multiple login attempts detected on your account.', 'unread', '2025-08-15 14:05:00'),
(6, 12, 'This is an testing notification so please there\'s no need to take concern about anything', 'read', '2025-09-03 17:48:37'),
(7, 12, 'This is an testing notification so please there\'s no need to take concern about anything', 'read', '2025-09-03 17:50:13'),
(8, 12, 'Update your device\'s RAM', 'read', '2025-09-06 12:35:29'),
(9, 12, 'Please upgrade RAM', 'read', '2025-09-06 12:36:35');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `similar_cases`
--

CREATE TABLE `similar_cases` (
  `id` int(11) NOT NULL,
  `analytics_id` int(11) DEFAULT NULL,
  `similar_case_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `similar_cases`
--

INSERT INTO `similar_cases` (`id`, `analytics_id`, `similar_case_id`) VALUES
(1, 1, 5),
(2, 2, 7),
(3, 3, 10),
(4, 4, 9),
(5, 5, 8);

-- --------------------------------------------------------

--
-- Table structure for table `support_requests`
--

CREATE TABLE `support_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `issue` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_requests`
--

INSERT INTO `support_requests` (`id`, `user_id`, `name`, `email`, `issue`, `created_at`) VALUES
(1, 12, 'Dhiren Parmar', 'shethaum84@gmail.com', 'I can\\\'t upload the evidence properly', '2025-08-26 21:35:46'),
(2, 13, 'Ayush Chauhan', 'ayushchauhan4747@gmail.com', 'Analytics is not working \\r\\n', '2025-09-06 13:43:57');

-- --------------------------------------------------------

--
-- Table structure for table `trial_proceedings`
--

CREATE TABLE `trial_proceedings` (
  `tp_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `proceeding_note` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trial_proceedings`
--

INSERT INTO `trial_proceedings` (`tp_id`, `case_id`, `user_id`, `proceeding_note`, `created_at`) VALUES
(1, 20, 13, 'No accussed is yet found so the court is giving time for a week to the investigating officer to find the accussed', '2025-09-05 19:32:00'),
(2, 20, 13, 'Mr. Aggarwal is found not guilty', '2025-09-06 16:16:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accused`
--
ALTER TABLE `accused`
  ADD PRIMARY KEY (`accused_id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`analytics_id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`case_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- Indexes for table `evidence`
--
ALTER TABLE `evidence`
  ADD PRIMARY KEY (`evidence_id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`fdb_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `judgments`
--
ALTER TABLE `judgments`
  ADD PRIMARY KEY (`judgment_id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`ntf_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `similar_cases`
--
ALTER TABLE `similar_cases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_id` (`analytics_id`),
  ADD KEY `similar_case_id` (`similar_case_id`);

--
-- Indexes for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trial_proceedings`
--
ALTER TABLE `trial_proceedings`
  ADD PRIMARY KEY (`tp_id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accused`
--
ALTER TABLE `accused`
  MODIFY `accused_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `analytics_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `case_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `evidence`
--
ALTER TABLE `evidence`
  MODIFY `evidence_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `fdb_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `judgments`
--
ALTER TABLE `judgments`
  MODIFY `judgment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `ntf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `similar_cases`
--
ALTER TABLE `similar_cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `support_requests`
--
ALTER TABLE `support_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `trial_proceedings`
--
ALTER TABLE `trial_proceedings`
  MODIFY `tp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accused`
--
ALTER TABLE `accused`
  ADD CONSTRAINT `accused_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`case_id`);

--
-- Constraints for table `analytics`
--
ALTER TABLE `analytics`
  ADD CONSTRAINT `analytics_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`case_id`);

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`);

--
-- Constraints for table `cases`
--
ALTER TABLE `cases`
  ADD CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`officer_id`) REFERENCES `members` (`user_id`);

--
-- Constraints for table `evidence`
--
ALTER TABLE `evidence`
  ADD CONSTRAINT `evidence_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`case_id`),
  ADD CONSTRAINT `evidence_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `members` (`user_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`);

--
-- Constraints for table `judgments`
--
ALTER TABLE `judgments`
  ADD CONSTRAINT `judgments_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`case_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `judgments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`);

--
-- Constraints for table `similar_cases`
--
ALTER TABLE `similar_cases`
  ADD CONSTRAINT `similar_cases_ibfk_1` FOREIGN KEY (`analytics_id`) REFERENCES `analytics` (`analytics_id`),
  ADD CONSTRAINT `similar_cases_ibfk_2` FOREIGN KEY (`similar_case_id`) REFERENCES `cases` (`case_id`);

--
-- Constraints for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD CONSTRAINT `support_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`);

--
-- Constraints for table `trial_proceedings`
--
ALTER TABLE `trial_proceedings`
  ADD CONSTRAINT `trial_proceedings_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`case_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trial_proceedings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
