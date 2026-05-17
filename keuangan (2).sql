-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 17, 2026 at 09:51 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `keuangan`
--

-- --------------------------------------------------------

--
-- Table structure for table `bpjs_categories`
--

CREATE TABLE `bpjs_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `has_health` tinyint(1) NOT NULL DEFAULT '0',
  `has_naker` tinyint(1) NOT NULL DEFAULT '0',
  `is_full_school` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bpjs_categories`
--

INSERT INTO `bpjs_categories` (`id`, `code`, `name`, `has_health`, `has_naker`, `is_full_school`, `created_at`, `updated_at`) VALUES
(1, 'A', 'Peserta BPJS Kes dan Naker (Berbagi Premi)', 1, 1, 0, '2026-05-16 16:20:35', '2026-05-16 16:20:35'),
(2, 'B', 'Peserta BPJS Kes dan Naker (Full Sekolah)', 1, 1, 1, '2026-05-16 16:20:35', '2026-05-16 16:20:35'),
(3, 'C', 'Peserta BPJS Kesehatan Saja', 1, 0, 1, '2026-05-16 16:20:35', '2026-05-16 16:20:35'),
(4, 'D', 'Peserta BPJS Ketenagakerjaan Saja', 0, 1, 1, '2026-05-16 16:20:35', '2026-05-16 16:20:35'),
(5, 'E', 'Belum Peserta', 0, 0, 0, '2026-05-16 16:20:35', '2026-05-16 16:20:35');

-- --------------------------------------------------------

--
-- Table structure for table `bpjs_configs`
--

CREATE TABLE `bpjs_configs` (
  `id` bigint UNSIGNED NOT NULL,
  `umk_reference` decimal(15,2) NOT NULL DEFAULT '2333586.00',
  `health_school_percent` decimal(5,2) NOT NULL DEFAULT '4.00',
  `health_employee_percent` decimal(5,2) NOT NULL DEFAULT '1.00',
  `naker_school_percent` decimal(5,2) NOT NULL DEFAULT '6.24',
  `naker_employee_percent` decimal(5,2) NOT NULL DEFAULT '3.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bpjs_configs`
--

INSERT INTO `bpjs_configs` (`id`, `umk_reference`, `health_school_percent`, `health_employee_percent`, `naker_school_percent`, `naker_employee_percent`, `created_at`, `updated_at`) VALUES
(1, '2333586.00', '4.00', '1.00', '6.24', '3.00', '2026-05-16 16:20:35', '2026-05-16 17:04:01');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('admin@admin.com|127.0.0.1', 'i:2;', 1779001539),
('admin@admin.com|127.0.0.1:timer', 'i:1779001539;', 1779001539);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_transactions`
--

CREATE TABLE `cash_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `category` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_08_143531_create_permission_tables', 1),
(5, '2026_03_08_143602_create_students_table', 1),
(6, '2026_03_08_143603_create_teachers_table', 1),
(7, '2026_03_08_143607_create_fee_types_table', 1),
(8, '2026_03_08_143622_create_student_bills_table', 1),
(9, '2026_03_08_143628_create_payments_table', 1),
(10, '2026_03_08_143631_create_salaries_table', 1),
(11, '2026_03_08_143633_create_cash_transactions_table', 1),
(12, '2026_03_08_160626_create_academic_years_table', 2),
(13, '2026_03_08_160628_create_class_rooms_table', 2),
(14, '2026_03_08_162848_create_settings_table', 3),
(15, '2026_03_08_163206_add_class_room_id_to_students_table', 4),
(16, '2026_03_08_171632_add_user_id_to_students_table', 5),
(17, '2026_03_08_183522_add_semester_to_academic_years_table', 6),
(18, '2026_03_09_021136_create_positions_table', 7),
(19, '2026_03_09_021200_add_position_and_teaching_hours_to_teachers_table', 7),
(20, '2026_03_09_024232_create_position_teacher_table', 8),
(21, '2026_03_09_024247_remove_position_id_from_teachers_table', 8),
(22, '2026_03_09_031434_add_deduction_description_to_salaries_table', 9),
(23, '2026_03_09_040334_add_transport_allowance_to_salaries_table', 10),
(24, '2026_03_09_062145_add_days_present_to_salaries_table', 11),
(25, '2026_03_10_040443_add_payment_date_to_salaries_table', 12),
(27, '2026_03_10_054318_add_health_allowance_to_positions_table', 13),
(28, '2026_05_04_093717_add_education_to_teachers_table', 14),
(29, '2026_05_04_095126_add_salary_fields_to_teachers_table', 15),
(30, '2026_05_16_000000_create_salary_templates_table', 16),
(31, '2026_05_16_000001_major_system_overhaul', 17),
(32, '2026_05_16_141025_create_salary_scales_table', 18),
(33, '2026_05_16_144416_add_gender_to_teachers_table', 19),
(34, '2026_05_16_152935_add_salary_calc_fields_to_teachers_table', 20),
(35, '2026_05_16_231923_create_bpjs_categories_table', 21),
(36, '2026_05_16_231923_create_bpjs_configs_table', 21),
(37, '2026_05_16_231950_add_bpjs_category_id_to_teachers_table', 21),
(38, '2026_05_17_014344_create_salary_deductions_table', 22),
(39, '2026_05_17_035750_remove_transport_from_salaries_table', 23),
(40, '2026_05_17_073000_drop_salary_templates_table', 24),
(41, '2026_05_17_081700_add_major_to_teachers_table', 25);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `allowance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `health_allowance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `name`, `allowance`, `health_allowance`, `created_at`, `updated_at`) VALUES
(3, 'Kepala Sekolah', '1500000.00', '0.00', '2026-03-08 19:34:20', '2026-05-16 06:17:29'),
(13, 'Waka Sekolah', '6400000.00', '0.00', '2026-05-16 06:19:28', '2026-05-16 06:19:28'),
(14, 'Kasubag TU', '6400000.00', '0.00', '2026-05-16 06:19:55', '2026-05-16 06:19:55'),
(15, 'KaProdi', '430000.00', '0.00', '2026-05-16 06:20:15', '2026-05-16 06:20:15'),
(16, 'KaLab', '230000.00', '0.00', '2026-05-16 06:20:30', '2026-05-16 06:20:30'),
(17, 'STP2K', '230000.00', '0.00', '2026-05-16 06:20:51', '2026-05-16 06:20:51'),
(18, 'Koord Eksul', '230000.00', '0.00', '2026-05-16 06:21:09', '2026-05-16 06:21:09'),
(19, 'Wali Kelas', '150000.00', '0.00', '2026-05-16 06:21:30', '2026-05-16 06:21:30'),
(20, 'Operator Sekolah', '430000.00', '0.00', '2026-05-16 06:21:55', '2026-05-16 06:21:55'),
(21, 'Bendahara Sekolah', '430000.00', '0.00', '2026-05-16 06:22:25', '2026-05-16 06:22:25'),
(22, 'Ka Unit Prodi', '230000.00', '0.00', '2026-05-16 06:22:46', '2026-05-16 06:22:46'),
(23, 'Ka BKK', '230000.00', '0.00', '2026-05-16 06:23:05', '2026-05-16 06:23:05'),
(24, 'Pembina Pramuka', '230000.00', '0.00', '2026-05-16 07:23:06', '2026-05-16 07:23:06'),
(25, 'Ka.Unit Prod + Wali', '380000.00', '0.00', '2026-05-16 07:23:36', '2026-05-16 07:23:36'),
(26, 'Wali Kelas 2', '200000.00', '0.00', '2026-05-16 07:24:12', '2026-05-16 07:24:12'),
(27, 'Operator Web', '200000.00', '0.00', '2026-05-16 07:24:32', '2026-05-16 07:24:32'),
(28, 'Operator#', '480000.00', '0.00', '2026-05-16 07:25:06', '2026-05-16 07:25:06'),
(29, 'Guru Piket', '230000.00', '0.00', '2026-05-16 07:25:30', '2026-05-16 07:25:30'),
(30, 'Guru Matematika', '0.00', '0.00', '2026-05-16 08:38:19', '2026-05-16 08:38:19'),
(31, 'Guru Informatika', '0.00', '0.00', '2026-05-16 08:38:33', '2026-05-16 08:38:33');

-- --------------------------------------------------------

--
-- Table structure for table `position_teacher`
--

CREATE TABLE `position_teacher` (
  `id` bigint UNSIGNED NOT NULL,
  `position_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `position_teacher`
--

INSERT INTO `position_teacher` (`id`, `position_id`, `teacher_id`, `created_at`, `updated_at`) VALUES
(6, 21, 2, NULL, NULL),
(7, 3, 4, NULL, NULL),
(10, 20, 6, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-03-08 07:43:16', '2026-03-08 07:43:16'),
(2, 'bendahara', 'web', '2026-03-08 07:43:16', '2026-03-08 07:43:16'),
(3, 'guru', 'web', '2026-03-08 07:43:16', '2026-03-08 07:43:16'),
(4, 'siswa', 'web', '2026-03-08 07:43:16', '2026-03-08 07:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `base_salary` decimal(15,2) NOT NULL,
  `allowance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `days_present` int UNSIGNED NOT NULL DEFAULT '0',
  `deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deduction_description` varchar(255) DEFAULT NULL,
  `net_salary` decimal(15,2) NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_deductions`
--

CREATE TABLE `salary_deductions` (
  `id` bigint UNSIGNED NOT NULL,
  `salary_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `spj_netto` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Nilai Netto dari Laporan SPJ (Gaji Pokok + Tunjangan - BPJS)',
  `tab_khusus` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '8% dari spj_netto',
  `simpanan_wajib` decimal(15,2) NOT NULL DEFAULT '5000.00',
  `simpanan_sukarela` decimal(15,2) NOT NULL DEFAULT '0.00',
  `angsuran_koperasi` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jumlah_koperasi` decimal(15,2) NOT NULL DEFAULT '0.00',
  `dplk_slawi` decimal(15,2) NOT NULL DEFAULT '0.00',
  `dplk_kemantran` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pinjaman_bpd_jateng` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jumlah_bpd` decimal(15,2) NOT NULL DEFAULT '0.00',
  `bank_tgr` decimal(15,2) NOT NULL DEFAULT '0.00',
  `premi_bpjs_anggota` decimal(15,2) NOT NULL DEFAULT '0.00',
  `dansos` decimal(15,2) NOT NULL DEFAULT '20000.00',
  `lainnya_1` decimal(15,2) NOT NULL DEFAULT '0.00',
  `lainnya_2` decimal(15,2) NOT NULL DEFAULT '0.00',
  `denda_fingerprint` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jumlah_potongan` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total seluruh kluster potongan',
  `gaji_bersih` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Take Home Pay (spj_netto - jumlah_potongan)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_scales`
--

CREATE TABLE `salary_scales` (
  `id` bigint UNSIGNED NOT NULL,
  `grade` varchar(255) NOT NULL,
  `mkg` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `salary_scales`
--

INSERT INTO `salary_scales` (`id`, `grade`, `mkg`, `amount`, `created_at`, `updated_at`) VALUES
(1, 'II', 4, '1447700.00', '2026-05-16 07:15:11', '2026-05-16 07:15:11'),
(2, 'I', 0, '1050000.00', '2026-05-16 07:17:53', '2026-05-16 07:17:53'),
(3, 'V', 28, '2792100.00', '2026-05-16 07:34:20', '2026-05-16 07:34:20'),
(4, 'IV', 12, '2090300.00', '2026-05-16 08:21:25', '2026-05-16 08:21:25'),
(5, 'IV', 4, '1846600.00', '2026-05-16 08:22:54', '2026-05-16 08:22:54'),
(6, 'IV', 6, '1904700.00', '2026-05-16 08:23:08', '2026-05-17 01:09:41'),
(7, 'I', 20, '1431800.00', '2026-05-16 20:08:13', '2026-05-16 20:08:13');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('AjvsZHH4rS3623tDRq5QAlBjDhoSmtcvZ4OosHzT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFE0Zmg2bzJTNUNkS3NlREtkMXlsV2FsaXBqUnF6MW1QejJQQmR5UyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779009811),
('Ql9k71TE7yLKkKoJFzYPsp9Bs4NnA5XSgSP8BopK', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVlhQM1pIa3FCVUZEYlVTNXo1ZFUwUFdSRTVwckFUOXYzTHFSMTVWViI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcHJvZmlsZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1779011316);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'SIM Keuangan', '2026-03-08 09:54:41', '2026-03-08 10:37:36'),
(2, 'school_name', 'SMK NU 1 ISLAMIYAH KRAMAT', '2026-03-08 09:54:41', '2026-03-08 10:01:39'),
(3, 'copyright', '© 2026 Sistem Informasi Manajemen Keuangan | SMK NU 1 Islamiyah Kramat', '2026-03-08 09:54:41', '2026-03-08 10:02:50'),
(7, 'logo_url', '/storage/logos/9W1m5XFkJJVtW1xbQKlQ9VVbQcDllmhs1w6yEgYM.jpg', '2026-03-08 10:18:08', '2026-03-08 10:18:08'),
(8, 'bank_name', 'BRI', '2026-03-08 19:47:44', '2026-03-09 21:41:04'),
(9, 'bank_account_number', '606789018273829', '2026-03-08 19:47:44', '2026-03-09 21:41:04'),
(10, 'bank_account_name', 'SMK NU 1  ISLAMIYAH KERAMAT', '2026-03-08 19:47:44', '2026-03-09 21:41:04'),
(11, 'teaching_rate_per_hour', '25000', '2026-03-08 19:47:44', '2026-03-08 21:32:20'),
(12, 'transport_allowance', NULL, '2026-03-08 21:19:11', '2026-05-16 20:54:11'),
(13, 'rate_education_sma', NULL, '2026-05-16 20:54:11', '2026-05-16 20:54:11'),
(14, 'rate_education_d3', NULL, '2026-05-16 20:54:11', '2026-05-16 20:54:11'),
(15, 'rate_education_s1', NULL, '2026-05-16 20:54:11', '2026-05-16 20:54:11'),
(16, 'rate_education_s2', NULL, '2026-05-16 20:54:11', '2026-05-16 20:54:11'),
(17, 'rate_education_s3', NULL, '2026-05-16 20:54:11', '2026-05-16 20:54:11'),
(18, 'rate_service_per_year', NULL, '2026-05-16 20:54:11', '2026-05-16 20:54:11');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `nipty` varchar(255) DEFAULT NULL,
  `nipy` varchar(255) DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `major` varchar(150) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `service_years` int NOT NULL DEFAULT '0',
  `service_months` int NOT NULL DEFAULT '0',
  `grade` varchar(255) DEFAULT NULL,
  `basic_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `teaching_hours` int NOT NULL DEFAULT '32',
  `discipline_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `other_allowance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `joined_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bpjs_category_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `name`, `nipty`, `nipy`, `birth_place`, `birth_date`, `gender`, `education`, `major`, `unit`, `service_years`, `service_months`, `grade`, `basic_salary`, `teaching_hours`, `discipline_percentage`, `other_allowance`, `joined_date`, `created_at`, `updated_at`, `bpjs_category_id`) VALUES
(2, 4, 'Tina Agustina', '2001-03-121', '200103121', 'Tegal', '2026-05-15', 'Perempuan', 'SMA/SMK', NULL, 'SMK Nu 1 Islamiyah Kramat', 5, 2, 'II', '1447700.00', 32, '0.00', '0.00', '2021-03-03', '2026-05-04 01:46:28', '2026-05-16 16:24:13', 2),
(3, 5, 'Rokhimun', '2004-03-028', '200403028', 'Pemalang', '2026-05-21', 'Laki-laki', 'SD', NULL, 'SMK Nu 1 Islamiyah Kramat', 22, 2, 'I', '1431800.00', 32, '0.00', '0.00', '2004-03-03', '2026-05-16 07:22:03', '2026-05-16 20:08:45', 2),
(4, 6, 'Umi Sa\'atun,S.Pd., M.M', '1997-03-019', '199703019', 'Tegal', '1976-11-23', 'Perempuan', 'S2', NULL, 'SMK Nu 1 Islamiyah Kramat', 29, 2, 'V', '2792100.00', 32, '0.00', '0.00', '1997-03-03', '2026-05-16 07:53:16', '2026-05-16 16:28:09', 1),
(6, 8, 'Aenun Akhkam', '22205047', '22205047', 'Tegal', '2026-05-17', 'Laki-laki', 'S1', 'Teknik Informatika', 'SMK NU 1 Islamiyah Kramat', 7, 10, 'IV', '1904700.00', 32, '0.00', '0.00', '2018-07-19', '2026-05-17 00:52:05', '2026-05-17 01:20:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Tina Agustina', 'tina@gmail.com', NULL, '$2y$12$AYrm1od5KGdhvTM5o5NMEOwNFDFUWDhmCHnj7ccb3cCCc/tv3pJDu', 'jtMz6UhcA2L4UyjIsM8iOeGSXhO6KwbixA5gbs9L06RbPvrY9vWxSyWWsBKR', '2026-03-08 07:43:16', '2026-05-16 20:32:57'),
(3, 'Aenun Akhkam', 'guru@gmail.com', NULL, '$2y$12$Z5.JccUIoZmVjIyRmjoN/eTWhAPq9waQx8I9OMT7L0KJe/gauqGxu', NULL, '2026-03-08 10:19:47', '2026-03-08 22:36:03'),
(4, 'Tina Agustina', '123456789000112@school.local', NULL, '$2y$12$DY6drUinI97MvnQrRn3XJOiTv.VpdLt8DK1mIkIJJWYCzZuyyLscu', NULL, '2026-05-04 01:46:28', '2026-05-04 01:46:28'),
(5, 'Rokhimun', '200403028@school.local', NULL, '$2y$12$AS1gQAc5ksE0t5G0vUR5n.9uNCT8Bo1rSZIHu/02LRnpdk0huaGne', NULL, '2026-05-16 07:22:03', '2026-05-16 07:22:03'),
(6, 'Umi Sa\'atun,S.Pd., M.M', '199703019@school.local', NULL, '$2y$12$7aPy13id9OJs2kmZtZt7xOheZIIjeoPwthwN2KKNb4bnTnqwC170G', NULL, '2026-05-16 07:53:16', '2026-05-16 07:53:16'),
(7, 'Windiya, S.Pd.', '2012-03-@school.local', NULL, '$2y$12$OMmZUaDPI0IIL7CMrgdOFeuBE8ZX54aL6T6P6Oi1gbT45v1o21ZGi', NULL, '2026-05-16 08:37:53', '2026-05-16 08:37:53'),
(8, 'Aenun Akhkam', '22205047@school.local', NULL, '$2y$12$xyTNzgKRDQW51zz4JF7fpeOvzq517QPwMIL4jrFeCAHSA8EZ9qA3a', NULL, '2026-05-17 00:52:05', '2026-05-17 00:52:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bpjs_categories`
--
ALTER TABLE `bpjs_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bpjs_categories_code_unique` (`code`);

--
-- Indexes for table `bpjs_configs`
--
ALTER TABLE `bpjs_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `positions_name_unique` (`name`);

--
-- Indexes for table `position_teacher`
--
ALTER TABLE `position_teacher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `position_teacher_position_id_foreign` (`position_id`),
  ADD KEY `position_teacher_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salaries_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `salary_deductions`
--
ALTER TABLE `salary_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_deductions_salary_id_foreign` (`salary_id`),
  ADD KEY `salary_deductions_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `salary_scales`
--
ALTER TABLE `salary_scales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `salary_scales_grade_mkg_unique` (`grade`,`mkg`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teachers_user_id_foreign` (`user_id`),
  ADD KEY `teachers_bpjs_category_id_foreign` (`bpjs_category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bpjs_categories`
--
ALTER TABLE `bpjs_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bpjs_configs`
--
ALTER TABLE `bpjs_configs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `position_teacher`
--
ALTER TABLE `position_teacher`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `salary_deductions`
--
ALTER TABLE `salary_deductions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `salary_scales`
--
ALTER TABLE `salary_scales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `position_teacher`
--
ALTER TABLE `position_teacher`
  ADD CONSTRAINT `position_teacher_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `position_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salaries`
--
ALTER TABLE `salaries`
  ADD CONSTRAINT `salaries_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_deductions`
--
ALTER TABLE `salary_deductions`
  ADD CONSTRAINT `salary_deductions_salary_id_foreign` FOREIGN KEY (`salary_id`) REFERENCES `salaries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `salary_deductions_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_bpjs_category_id_foreign` FOREIGN KEY (`bpjs_category_id`) REFERENCES `bpjs_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
