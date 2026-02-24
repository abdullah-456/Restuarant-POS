-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2026 at 10:58 AM
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
-- Database: `pos_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'DESI', 1, '2026-02-20 11:19:14', '2026-02-20 11:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `name`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Appetizers', 'Start your meal right', 1, 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(2, 'Main Courses', 'Delicious main dishes', 2, 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(3, 'Desserts', 'Sweet endings', 3, 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(4, 'Beverages', 'Drinks and refreshments', 4, 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(5, 'Salad', 'Fresh and healthys', 5, 1, '2026-02-19 13:23:04', '2026-02-20 02:07:14', NULL),
(6, 'Desi', 'Karahi daal', 7, 1, '2026-02-20 02:08:34', '2026-02-20 02:08:34', NULL),
(7, 'abja', 'jsbvj', 7, 1, '2026-02-20 02:08:45', '2026-02-20 02:08:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `preparation_time` int(11) NOT NULL DEFAULT 15,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `description`, `price`, `image`, `preparation_time`, `is_available`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(17, 1, 'Karahi', NULL, 1800.00, NULL, 15, 1, 1, 0, '2026-02-22 11:16:44', '2026-02-22 11:16:44', NULL),
(18, 1, 'Handi', NULL, 1600.00, NULL, 15, 1, 1, 0, '2026-02-22 11:16:55', '2026-02-22 11:16:55', NULL),
(19, 2, 'Coke', NULL, 200.00, NULL, 15, 1, 1, 0, '2026-02-22 11:17:05', '2026-02-22 11:17:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_create_tables_table', 1),
(5, '2024_01_01_000002_create_menu_categories_table', 1),
(6, '2024_01_01_000003_create_menu_items_table', 1),
(7, '2024_01_01_000004_create_orders_table', 1),
(8, '2024_01_01_000005_create_order_items_table', 1),
(9, '2024_01_01_000006_create_payments_table', 1),
(10, '2024_01_01_000007_create_activity_logs_table', 1),
(11, '2024_01_01_000008_create_settings_table', 1),
(12, '2026_02_20_091454_add_role_to_users_table', 2),
(13, '2026_02_20_100000_create_restaurant_tables_table', 2),
(14, '2026_02_20_100100_create_categories_table', 2),
(15, '2026_02_20_100200_create_menu_items_table', 3),
(16, '2026_02_20_100300_create_orders_table', 4),
(17, '2026_02_20_100400_create_order_items_table', 4),
(18, '2026_02_20_100500_create_payments_table', 4),
(19, '2026_02_20_100600_create_settings_table', 4),
(20, '2026_02_20_161119_add_paid_at_to_orders_table', 5),
(21, '2026_02_20_161758_add_restaurant_table_id_to_orders_table', 6),
(22, '2026_02_20_164910_add_paid_at_to_payments_table', 7),
(23, '2026_02_20_165541_add_payment_tracking_fields_to_orders_table', 8),
(24, '2026_02_20_165733_update_orders_status_enum', 9),
(25, '2026_02_21_100846_add_subtotal_to_order_items_table', 10),
(26, '2026_02_21_103819_add_is_new_to_order_items_table', 11),
(27, '2026_02_21_104007_add_modified_fields_to_orders_table', 12),
(28, '2026_02_22_115446_alter_order_items_status_enum', 13);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `table_id` bigint(20) UNSIGNED NOT NULL,
  `waiter_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','confirmed','preparing','ready','paid','cancelled') NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_charge_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `service_charge_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `preparing_at` timestamp NULL DEFAULT NULL,
  `ready_at` timestamp NULL DEFAULT NULL,
  `served_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `restaurant_table_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `table_id`, `waiter_id`, `status`, `paid_at`, `notes`, `cancellation_reason`, `subtotal`, `tax_rate`, `tax_amount`, `service_charge_rate`, `service_charge_amount`, `discount_percentage`, `discount_amount`, `total`, `total_paid`, `remaining_amount`, `confirmed_at`, `preparing_at`, `ready_at`, `served_at`, `completed_at`, `created_at`, `updated_at`, `deleted_at`, `restaurant_table_id`) VALUES
(7, 'ORD-2024-0001', 5, 1, '', NULL, 'Extra napkins please', NULL, 21.98, 10.00, 2.31, 5.00, 1.10, 0.00, 0.00, 25.39, 0.00, 25.39, '2024-01-15 13:30:00', '2024-01-15 13:35:00', '2024-01-15 13:50:00', '2024-01-15 14:00:00', '2024-01-15 14:15:00', '2024-01-15 13:25:00', '2026-02-22 05:37:23', NULL, NULL),
(8, 'ORD-2024-0002', 8, 1, 'paid', NULL, 'Allergy: gluten intolerance', NULL, 120.00, 10.00, 12.00, 5.00, 6.00, 10.00, 12.00, 126.00, 126.00, 0.00, '2024-01-15 14:00:00', '2024-01-15 14:05:00', '2026-02-20 11:27:05', NULL, NULL, '2024-01-15 13:55:00', '2026-02-20 12:01:16', NULL, NULL),
(9, 'ORD-2024-0003', 12, 2, 'cancelled', NULL, 'Customer requested cancellation', 'Customer changed mind', 45.00, 10.00, 4.50, 5.00, 2.25, 0.00, 0.00, 51.75, 0.00, 0.00, '2024-01-15 14:30:00', NULL, NULL, NULL, NULL, '2024-01-15 14:25:00', '2024-01-15 14:35:00', '2024-01-15 14:35:00', NULL),
(12, 'ORD-6999844739B46', 2, 1, 'cancelled', NULL, NULL, NULL, 9111.88, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 9111.88, 0.00, 9111.88, NULL, NULL, NULL, NULL, NULL, '2026-02-21 05:09:11', '2026-02-21 12:58:59', NULL, 2),
(13, 'ORD-6999DED6859F6', 3, 1, 'paid', NULL, NULL, NULL, 5575.84, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5575.84, 5575.84, 0.00, '2026-02-22 05:50:19', NULL, '2026-02-22 05:50:34', NULL, NULL, '2026-02-21 11:35:34', '2026-02-22 06:56:46', NULL, 3),
(14, 'ORD-6999F41E092B6', 2, 2, 'pending', NULL, NULL, NULL, 1830.97, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1830.97, 0.00, 1830.97, NULL, NULL, NULL, NULL, NULL, '2026-02-21 13:06:22', '2026-02-21 13:06:41', NULL, 2),
(15, 'ORD-699ADFACABD3D', 4, 1, 'confirmed', NULL, NULL, NULL, 21.98, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 21.98, 0.00, 21.98, '2026-02-22 05:51:44', NULL, NULL, NULL, NULL, '2026-02-22 05:51:24', '2026-02-22 05:51:44', NULL, 4),
(16, 'ORD-699AE22BAEE04', 5, 1, 'paid', NULL, NULL, NULL, 1821.98, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1821.98, 1821.98, 0.00, '2026-02-22 06:02:03', NULL, '2026-02-22 06:56:00', NULL, NULL, '2026-02-22 06:02:03', '2026-02-22 06:57:08', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT 0,
  `added_at` timestamp NULL DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','confirmed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `cashier_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method` enum('cash','card','mobile_payment','other') NOT NULL DEFAULT 'cash',
  `amount` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `cashier_id`, `payment_method`, `amount`, `notes`, `created_at`, `updated_at`) VALUES
(1, 8, 1, 'cash', 126.00, 'Tendered: $126.00 | Change: $0.00', '2026-02-20 11:53:50', '2026-02-20 11:53:50'),
(2, 8, 1, 'cash', 126.00, 'Tendered: $126.00 | Change: $0.00', '2026-02-20 11:56:08', '2026-02-20 11:56:08'),
(3, 8, 1, 'cash', 126.00, 'Tendered: $126.00 | Change: $0.00', '2026-02-20 11:56:25', '2026-02-20 11:56:25'),
(4, 8, 1, 'cash', 126.00, 'Tendered: $126.00 | Change: $0.00', '2026-02-20 11:59:45', '2026-02-20 11:59:45'),
(5, 8, 1, 'cash', 126.00, 'Tendered: $126.00 | Change: $0.00', '2026-02-20 11:59:54', '2026-02-20 11:59:54'),
(6, 8, 1, 'cash', 126.00, 'Tendered: $126.00 | Change: $0.00', '2026-02-20 12:01:16', '2026-02-20 12:01:16'),
(7, 13, 1, 'cash', 5575.84, 'Tendered: $5,580.00 | Change: $4.16', '2026-02-22 06:56:46', '2026-02-22 06:56:46'),
(8, 16, 1, 'cash', 1821.98, 'Tendered: $1,821.98 | Change: $0.00', '2026-02-22 06:57:08', '2026-02-22 06:57:08');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(10) UNSIGNED NOT NULL DEFAULT 2,
  `status` enum('available','occupied','reserved','cleaning') NOT NULL DEFAULT 'available',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`id`, `name`, `capacity`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'table 1', 2, 'available', 1, '2026-02-20 11:13:05', '2026-02-21 12:58:59'),
(3, 'Table 2', 4, 'available', 1, '2026-02-21 11:35:24', '2026-02-22 06:56:46'),
(4, 'table 3', 2, 'occupied', 1, '2026-02-22 05:50:51', '2026-02-22 05:51:44');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('TXPuIQQeOcKri4yKyh39QhSgXpGLIJNH7lgOXpMj', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVmFvMHR0aGMxU1I0aWRjOElYUjdvUjNhclluM1l4UDRvbVhBMjlKQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC93YWl0ZXIvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjE2OiJ3YWl0ZXIuZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1771775242),
('uu5hNixJB1RquYQUhdEqhPpwCslSsYdPuBYUDdFR', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNWE0OHFmQXhGUTNTVGlidE5MTHcyZHlOT0ZQY3hmd0RxNFBLN1l2OCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZXBvcnRzIjtzOjU6InJvdXRlIjtzOjE5OiJhZG1pbi5yZXBvcnRzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1771777208);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'tax_rate', '10', 'number', 'Tax rate percentage', '2026-02-19 13:23:04', '2026-02-19 13:23:04'),
(2, 'service_charge_rate', '5', 'number', 'Service charge rate percentage', '2026-02-19 13:23:04', '2026-02-19 13:23:04'),
(3, 'restaurant_name', 'Restaurant POS', 'string', 'Restaurant name', '2026-02-19 13:23:04', '2026-02-19 13:23:04'),
(4, 'restaurant_address', '123 Main Street, City, State 12345', 'string', 'Restaurant address', '2026-02-19 13:23:04', '2026-02-19 13:23:04'),
(5, 'restaurant_phone', '+1 (555) 123-4567', 'string', 'Restaurant phone number', '2026-02-19 13:23:04', '2026-02-19 13:23:04');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 4,
  `status` enum('available','occupied','reserved','cleaning') NOT NULL DEFAULT 'available',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `name`, `capacity`, `status`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Table 1', 8, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(2, 'Table 2', 8, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(3, 'Table 3', 5, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(4, 'Table 4', 3, 'available', 1, '2026-02-19 13:23:04', '2026-02-20 02:03:43', NULL),
(5, 'Table 5', 7, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(6, 'Table 6', 4, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(7, 'Table 7', 5, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(8, 'Table 8', 7, 'occupied', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(9, 'Table 9', 2, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(10, 'Table 10', 2, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(11, 'Table 11', 4, 'reserved', 1, '2026-02-19 13:23:04', '2026-02-20 02:04:15', NULL),
(12, 'Table 12', 3, 'occupied', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(13, 'Table 13', 3, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(14, 'Table 14', 5, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(15, 'Table 15', 7, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(16, 'Table 16', 4, 'occupied', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(17, 'Table 17', 7, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(18, 'Table 18', 6, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(19, 'Table 19', 4, 'available', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(20, 'Table 20', 4, 'occupied', 1, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','waiter','kitchen','cashier') NOT NULL DEFAULT 'waiter',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin User', 'admin@pos.com', NULL, '$2y$12$/UtL1RaFy0Dne42nODfch.h2D1.qs5BRr51Jc9.kxH3U9gKv/tiWu', 'admin', 1, NULL, '2026-02-19 13:23:03', '2026-02-19 13:23:03', NULL),
(2, 'John Waiter', 'waiter@pos.com', NULL, '$2y$12$.s/9vSSzhwMM2P.4FJQAEuZb8X70XqWty1mVSAo9WiPIKWtcGuRBW', 'waiter', 1, NULL, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(3, 'Kitchen Staff', 'kitchen@pos.com', NULL, '$2y$12$S2sZBNHV7IoI50CN506nr.qAoLezCbaRUGt3PX/EBzS7plc9upzEW', 'kitchen', 1, NULL, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL),
(4, 'Cashier Staff', 'cashier@pos.com', NULL, '$2y$12$mKEEwRwP3BR3Vkw45oY5l.jsTxYCxClnRsC2U6iL6QGEXOJ4XGy92', 'cashier', 1, NULL, '2026-02-19 13:23:04', '2026-02-19 13:23:04', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_index` (`user_id`),
  ADD KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `activity_logs_action_index` (`action`),
  ADD KEY `activity_logs_created_at_index` (`created_at`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
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
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_category_id_index` (`category_id`),
  ADD KEY `menu_items_is_available_index` (`is_available`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_order_number_index` (`order_number`),
  ADD KEY `orders_table_id_index` (`table_id`),
  ADD KEY `orders_waiter_id_index` (`waiter_id`),
  ADD KEY `orders_status_index` (`status`),
  ADD KEY `orders_created_at_index` (`created_at`),
  ADD KEY `orders_restaurant_table_id_foreign` (`restaurant_table_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_index` (`order_id`),
  ADD KEY `order_items_menu_item_id_index` (`menu_item_id`),
  ADD KEY `order_items_status_index` (`status`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_index` (`order_id`),
  ADD KEY `payments_cashier_id_index` (`cashier_id`),
  ADD KEY `payments_created_at_index` (`created_at`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `settings_key_unique` (`key`),
  ADD KEY `settings_key_index` (`key`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_restaurant_table_id_foreign` FOREIGN KEY (`restaurant_table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`),
  ADD CONSTRAINT `orders_waiter_id_foreign` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`),
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
