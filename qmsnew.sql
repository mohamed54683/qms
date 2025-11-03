-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2025 at 11:43 AM
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
-- Database: `qmsnew`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_plans`
--

CREATE TABLE `action_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_visit_id` bigint(20) UNSIGNED NOT NULL,
  `item` varchar(255) NOT NULL,
  `issue` varchar(255) NOT NULL DEFAULT 'Not compliant',
  `action_required` text NOT NULL,
  `what` text DEFAULT NULL,
  `where` text DEFAULT NULL,
  `why` text DEFAULT NULL,
  `how` text DEFAULT NULL,
  `who` text DEFAULT NULL,
  `when_detail` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `priority` enum('High','Medium','Low') NOT NULL DEFAULT 'Medium',
  `status` enum('Pending','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `approved_at` timestamp NULL DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `visit_purpose` varchar(255) DEFAULT NULL,
  `area_manager` varchar(255) DEFAULT NULL,
  `restaurant_manager` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `action_plans`
--

INSERT INTO `action_plans` (`id`, `store_visit_id`, `item`, `issue`, `action_required`, `what`, `where`, `why`, `how`, `who`, `when_detail`, `comment`, `priority`, `status`, `is_approved`, `approved_at`, `assigned_to`, `due_date`, `notes`, `completed_at`, `created_at`, `updated_at`, `approved_by`, `visit_purpose`, `area_manager`, `restaurant_manager`) VALUES
(18, 24, 'Coaching and Directing', 'Coaching and Directing - Issue identified during visit', 'Implement proper coaching and directing procedures with management training', 'Coaching and Directing needs improvement', 'Al Basateen', 'Non-compliance identified during quality visit', 'Implement proper coaching and directing procedures with management training', 'Restaurant staff and management', 'During visit on 2025-11-03', NULL, 'Medium', 'Pending', 1, NULL, NULL, NULL, 'no', NULL, '2025-11-03 03:59:47', '2025-11-03 03:59:47', NULL, 'Operational Assessment', 'GS QMS Admin', 'To be assigned');

-- --------------------------------------------------------

--
-- Table structure for table `action_plan_images`
--

CREATE TABLE `action_plan_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `action_plan_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `action_plan_images`
--

INSERT INTO `action_plan_images` (`id`, `action_plan_id`, `image_path`, `field_name`, `original_name`, `file_size`, `mime_type`, `created_at`, `updated_at`) VALUES
(6, 18, 'action-plans/1762153183_690852df5962b.jpeg', 'coaching_directing', '1762067729262.jpeg', 114710, 'image/jpeg', '2025-11-03 03:59:47', '2025-11-03 03:59:47');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:69:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:19:\"create_store_visits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:17:\"view_store_visits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:17:\"edit_store_visits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:19:\"delete_store_visits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:20:\"approve_store_visits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:19:\"create_action_plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:17:\"view_action_plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:17:\"edit_action_plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:19:\"delete_action_plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:14:\"view_dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"manage_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:18:\"manage_restaurants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:14:\"view_analytics\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:12:\"view_reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:19:\"submit_store_visits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:19:\"review_store_visits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:19:\"export_store_visits\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:20:\"approve_action_plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:19:\"assign_action_plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:21:\"complete_action_plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:10:\"view_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:12:\"create_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:10:\"edit_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:12:\"delete_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:12:\"assign_roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:18:\"view_user_profiles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:16:\"view_restaurants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:18:\"create_restaurants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:16:\"edit_restaurants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:18:\"delete_restaurants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:15:\"assign_managers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:10:\"view_roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:12:\"create_roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:10:\"edit_roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:12:\"delete_roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:12:\"manage_roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:16:\"view_permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:18:\"create_permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:16:\"edit_permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:18:\"delete_permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:18:\"manage_permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:25:\"view_temperature_tracking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:27:\"create_temperature_tracking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:25:\"edit_temperature_tracking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:27:\"delete_temperature_tracking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:28:\"approve_temperature_tracking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:19:\"view_qsc_checklists\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:21:\"create_qsc_checklists\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:19:\"edit_qsc_checklists\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:21:\"delete_qsc_checklists\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:20:\"view_system_settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:20:\"edit_system_settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:15:\"view_audit_logs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:13:\"manage_system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:13:\"backup_system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:14:\"restore_system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:16:\"generate_reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:14:\"export_reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:16:\"view_all_reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:16:\"schedule_reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:16:\"access_all_pages\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:13:\"view_all_data\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:13:\"edit_all_data\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:15:\"delete_all_data\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:18:\"super_admin_access\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:19:\"bypass_restrictions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:19:\"view_sensitive_data\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:19:\"edit_sensitive_data\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:24:\"view_area_manager_filter\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}}s:5:\"roles\";a:2:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}}}', 1762244733);

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
(4, '2025_10_06_133227_create_permission_tables', 1),
(5, '2025_10_06_134500_create_restaurants_table', 1),
(6, '2025_10_06_134545_create_user_restaurant_pivot_table', 1),
(7, '2025_10_06_175817_create_store_visits_table', 1),
(8, '2025_10_06_184941_add_complete_sections_to_store_visits_table', 1),
(9, '2025_10_06_185003_add_complete_sections_to_store_visits_table', 1),
(10, '2025_10_07_081250_add_phone_and_department_to_users_table', 1),
(11, '2025_10_07_091517_add_manager_id_to_restaurants_table', 1),
(12, '2025_10_07_095050_add_enhanced_fields_to_users_table', 1),
(13, '2025_10_07_100148_add_enhanced_fields_to_restaurants_table', 1),
(14, '2025_10_07_101035_add_manager_ids_to_restaurants_table', 1),
(15, '2025_10_07_101601_add_brand_field_to_restaurants_table', 1),
(16, '2025_10_07_115617_create_store_visit_answers_table', 1),
(17, '2025_10_07_115658_add_workflow_fields_to_store_visits_table', 1),
(18, '2025_10_07_171633_create_action_plans_table', 1),
(19, '2025_10_07_174923_recreate_action_plans_table', 1),
(20, '2025_10_07_183945_add_workflow_fields_to_action_plans_table', 1),
(21, '2025_10_07_194107_add_completed_at_to_store_visits_table', 1),
(22, '2025_10_08_000001_create_temperature_tracking_forms_table', 1),
(23, '2025_10_08_000002_create_temperature_tracking_items_table', 1),
(24, '2025_10_08_000003_create_temperature_shift_turnovers_table', 1),
(25, '2025_10_08_062505_add_indexes_to_store_visits_table', 1),
(26, '2025_10_08_120408_remove_qsc_checklist_id_from_action_plans_table', 1),
(27, '2025_10_08_144859_create_qsc_checklists_table', 1),
(28, '2025_10_09_000004_add_extended_fields_to_temperature_tracking_forms_table', 1),
(29, '2025_10_09_010000_align_temperature_tracking_forms_base_structure', 1),
(30, '2025_10_09_073517_add_shift_to_temperature_tracking_forms_table', 1),
(31, '2025_10_09_093303_create_page_permissions_table', 1),
(32, '2025_10_26_122602_add_coordinates_to_restaurants_table', 2),
(36, '2025_10_28_083954_create_action_plan_images_table', 3),
(37, '2025_10_29_add_all_restaurants', 4),
(38, '2025_10_30_073944_add_store_id_to_temperature_tracking_forms_table', 5),
(39, '2025_10_30_082126_add_status_to_temperature_tracking_forms_table', 6),
(40, '2025_10_30_082132_add_status_and_mic_to_temperature_tracking_forms_table', 6),
(41, '2025_10_30_084036_make_store_visit_id_nullable_in_temperature_tracking_forms', 7),
(42, '2025_10_30_084045_make_store_visit_id_nullable_in_temperature_tracking_forms', 7),
(43, '2025_11_02_123816_create_user_area_managers_table', 8),
(44, '2025_11_02_123834_add_area_manager_filter_permission', 8),
(45, '2025_11_03_181900_add_view_area_manager_filter_permission', 9),
(46, '2025_11_03_182000_create_area_managers', 10);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 2),
(5, 'App\\Models\\User', 2),
(6, 'App\\Models\\User', 2),
(7, 'App\\Models\\User', 2),
(8, 'App\\Models\\User', 2),
(9, 'App\\Models\\User', 2),
(10, 'App\\Models\\User', 2),
(11, 'App\\Models\\User', 2),
(12, 'App\\Models\\User', 2),
(13, 'App\\Models\\User', 2),
(14, 'App\\Models\\User', 2),
(15, 'App\\Models\\User', 2),
(16, 'App\\Models\\User', 2),
(17, 'App\\Models\\User', 2),
(18, 'App\\Models\\User', 2),
(19, 'App\\Models\\User', 2),
(20, 'App\\Models\\User', 2),
(21, 'App\\Models\\User', 2),
(22, 'App\\Models\\User', 2),
(23, 'App\\Models\\User', 2),
(24, 'App\\Models\\User', 2),
(25, 'App\\Models\\User', 2),
(26, 'App\\Models\\User', 2),
(27, 'App\\Models\\User', 2),
(28, 'App\\Models\\User', 2),
(29, 'App\\Models\\User', 2),
(30, 'App\\Models\\User', 2),
(31, 'App\\Models\\User', 2),
(32, 'App\\Models\\User', 2),
(33, 'App\\Models\\User', 2),
(34, 'App\\Models\\User', 2),
(35, 'App\\Models\\User', 2),
(36, 'App\\Models\\User', 2),
(37, 'App\\Models\\User', 2),
(38, 'App\\Models\\User', 2),
(39, 'App\\Models\\User', 2),
(40, 'App\\Models\\User', 2),
(41, 'App\\Models\\User', 2),
(42, 'App\\Models\\User', 2),
(43, 'App\\Models\\User', 2),
(44, 'App\\Models\\User', 2),
(45, 'App\\Models\\User', 2),
(46, 'App\\Models\\User', 2),
(47, 'App\\Models\\User', 2),
(48, 'App\\Models\\User', 2),
(49, 'App\\Models\\User', 2),
(50, 'App\\Models\\User', 2),
(51, 'App\\Models\\User', 2),
(52, 'App\\Models\\User', 2),
(53, 'App\\Models\\User', 2),
(54, 'App\\Models\\User', 2),
(55, 'App\\Models\\User', 2),
(56, 'App\\Models\\User', 2),
(57, 'App\\Models\\User', 2),
(58, 'App\\Models\\User', 2),
(59, 'App\\Models\\User', 2),
(60, 'App\\Models\\User', 2),
(61, 'App\\Models\\User', 2),
(62, 'App\\Models\\User', 2),
(63, 'App\\Models\\User', 2),
(64, 'App\\Models\\User', 2),
(65, 'App\\Models\\User', 2),
(66, 'App\\Models\\User', 2),
(67, 'App\\Models\\User', 2),
(68, 'App\\Models\\User', 2),
(69, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 5);

-- --------------------------------------------------------

--
-- Table structure for table `page_permissions`
--

CREATE TABLE `page_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `page_key` varchar(255) NOT NULL,
  `page_route` varchar(255) DEFAULT NULL,
  `page_icon` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_permissions`
--

INSERT INTO `page_permissions` (`id`, `page_name`, `page_key`, `page_route`, `page_icon`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'dashboard', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(2, 'Store Visits', 'store_visits', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(3, 'Action Plans', 'action_plans', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(4, 'User Management', 'users', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(5, 'Restaurant Management', 'restaurants', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(6, 'QSC Checklist', 'qsc_checklist', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(7, 'Temperature Tracking', 'temperature_tracking', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(8, 'Permission Management', 'permission_management', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(9, 'Settings', 'settings', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(10, 'Reports', 'reports', NULL, NULL, 1, 0, '2025-10-14 04:09:03', '2025-10-14 04:09:03');

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
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'create_store_visits', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(2, 'view_store_visits', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(3, 'edit_store_visits', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(4, 'delete_store_visits', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(5, 'approve_store_visits', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(6, 'create_action_plans', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(7, 'view_action_plans', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(8, 'edit_action_plans', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(9, 'delete_action_plans', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(10, 'view_dashboard', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(11, 'manage_users', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(12, 'manage_restaurants', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(13, 'view_analytics', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(14, 'view_reports', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(15, 'submit_store_visits', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(16, 'review_store_visits', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(17, 'export_store_visits', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(18, 'approve_action_plans', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(19, 'assign_action_plans', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(20, 'complete_action_plans', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(21, 'view_users', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(22, 'create_users', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(23, 'edit_users', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(24, 'delete_users', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(25, 'assign_roles', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(26, 'view_user_profiles', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(27, 'view_restaurants', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(28, 'create_restaurants', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(29, 'edit_restaurants', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(30, 'delete_restaurants', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(31, 'assign_managers', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(32, 'view_roles', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(33, 'create_roles', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(34, 'edit_roles', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(35, 'delete_roles', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(36, 'manage_roles', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(37, 'view_permissions', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(38, 'create_permissions', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(39, 'edit_permissions', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(40, 'delete_permissions', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(41, 'manage_permissions', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(42, 'view_temperature_tracking', 'web', '2025-10-14 04:03:26', '2025-10-14 04:03:26'),
(43, 'create_temperature_tracking', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(44, 'edit_temperature_tracking', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(45, 'delete_temperature_tracking', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(46, 'approve_temperature_tracking', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(47, 'view_qsc_checklists', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(48, 'create_qsc_checklists', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(49, 'edit_qsc_checklists', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(50, 'delete_qsc_checklists', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(51, 'view_system_settings', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(52, 'edit_system_settings', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(53, 'view_audit_logs', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(54, 'manage_system', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(55, 'backup_system', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(56, 'restore_system', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(57, 'generate_reports', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(58, 'export_reports', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(59, 'view_all_reports', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(60, 'schedule_reports', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(61, 'access_all_pages', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(62, 'view_all_data', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(63, 'edit_all_data', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(64, 'delete_all_data', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(65, 'super_admin_access', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(66, 'bypass_restrictions', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(67, 'view_sensitive_data', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(68, 'edit_sensitive_data', 'web', '2025-10-14 04:03:27', '2025-10-14 04:03:27'),
(69, 'view_area_manager_filter', 'web', '2025-11-02 09:38:48', '2025-11-02 09:38:48');

-- --------------------------------------------------------

--
-- Table structure for table `qsc_checklists`
--

CREATE TABLE `qsc_checklists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `mod` varchar(255) NOT NULL,
  `day` varchar(255) DEFAULT NULL,
  `time_option` enum('12PM','5PM','8PM','11PM') NOT NULL,
  `exterior_lights_open` enum('yes','no') DEFAULT NULL,
  `exterior_lights_open_comment` text DEFAULT NULL,
  `exterior_logo_clean` enum('yes','no') DEFAULT NULL,
  `exterior_logo_clean_comment` text DEFAULT NULL,
  `exterior_parking_clean` enum('yes','no') DEFAULT NULL,
  `exterior_parking_clean_comment` text DEFAULT NULL,
  `exterior_no_graffiti` enum('yes','no') DEFAULT NULL,
  `exterior_no_graffiti_comment` text DEFAULT NULL,
  `doors_glass_clean` enum('yes','no') DEFAULT NULL,
  `doors_glass_clean_comment` text DEFAULT NULL,
  `doors_door_handle` enum('yes','no') DEFAULT NULL,
  `doors_door_handle_comment` text DEFAULT NULL,
  `doors_entrance_clean` enum('yes','no') DEFAULT NULL,
  `doors_entrance_clean_comment` text DEFAULT NULL,
  `frontline_areas_organized` enum('yes','no') DEFAULT NULL,
  `frontline_areas_organized_comment` text DEFAULT NULL,
  `frontline_customers_greeted` enum('yes','no') DEFAULT NULL,
  `frontline_customers_greeted_comment` text DEFAULT NULL,
  `frontline_menu_available` enum('yes','no') DEFAULT NULL,
  `frontline_menu_available_comment` text DEFAULT NULL,
  `frontline_seven_steps` enum('yes','no') DEFAULT NULL,
  `frontline_seven_steps_comment` text DEFAULT NULL,
  `frontline_tables_clean` enum('yes','no') DEFAULT NULL,
  `frontline_tables_clean_comment` text DEFAULT NULL,
  `frontline_high_chairs` enum('yes','no') DEFAULT NULL,
  `frontline_high_chairs_comment` text DEFAULT NULL,
  `frontline_no_damaged_tables` enum('yes','no') DEFAULT NULL,
  `frontline_no_damaged_tables_comment` text DEFAULT NULL,
  `restroom_no_full_trash` enum('yes','no') DEFAULT NULL,
  `restroom_no_full_trash_comment` text DEFAULT NULL,
  `restroom_soap_available` enum('yes','no') DEFAULT NULL,
  `restroom_soap_available_comment` text DEFAULT NULL,
  `restroom_hand_dryer` enum('yes','no') DEFAULT NULL,
  `restroom_hand_dryer_comment` text DEFAULT NULL,
  `holding_product_temp` enum('yes','no') DEFAULT NULL,
  `holding_product_temp_comment` text DEFAULT NULL,
  `holding_product_age` enum('yes','no') DEFAULT NULL,
  `holding_product_age_comment` text DEFAULT NULL,
  `holding_check_product` enum('yes','no') DEFAULT NULL,
  `holding_check_product_comment` text DEFAULT NULL,
  `holding_products_fresh` enum('yes','no') DEFAULT NULL,
  `holding_products_fresh_comment` text DEFAULT NULL,
  `holding_internal_temp` enum('yes','no') DEFAULT NULL,
  `holding_internal_temp_comment` text DEFAULT NULL,
  `holding_shortening_level` enum('yes','no') DEFAULT NULL,
  `holding_shortening_level_comment` text DEFAULT NULL,
  `holding_check_shortening` enum('yes','no') DEFAULT NULL,
  `holding_check_shortening_comment` text DEFAULT NULL,
  `holding_fryer_maintenance` enum('yes','no') DEFAULT NULL,
  `holding_fryer_maintenance_comment` text DEFAULT NULL,
  `holding_use_tray` enum('yes','no') DEFAULT NULL,
  `holding_use_tray_comment` text DEFAULT NULL,
  `holding_fry_basket` enum('yes','no') DEFAULT NULL,
  `holding_fry_basket_comment` text DEFAULT NULL,
  `holding_fries_salted` enum('yes','no') DEFAULT NULL,
  `holding_fries_salted_comment` text DEFAULT NULL,
  `holding_fries_cooking` enum('yes','no') DEFAULT NULL,
  `holding_fries_cooking_comment` text DEFAULT NULL,
  `holding_buns_quality` enum('yes','no') DEFAULT NULL,
  `holding_buns_quality_comment` text DEFAULT NULL,
  `holding_teflon_sheet` enum('yes','no') DEFAULT NULL,
  `holding_teflon_sheet_comment` text DEFAULT NULL,
  `holding_bread_standard` enum('yes','no') DEFAULT NULL,
  `holding_bread_standard_comment` text DEFAULT NULL,
  `thawing_day_labels` enum('yes','no') DEFAULT NULL,
  `thawing_day_labels_comment` text DEFAULT NULL,
  `thawing_no_tampering` enum('yes','no') DEFAULT NULL,
  `thawing_no_tampering_comment` text DEFAULT NULL,
  `thawing_temp_range` enum('yes','no') DEFAULT NULL,
  `thawing_temp_range_comment` text DEFAULT NULL,
  `thawing_no_overstock` enum('yes','no') DEFAULT NULL,
  `thawing_no_overstock_comment` text DEFAULT NULL,
  `thawing_utensils_clean` enum('yes','no') DEFAULT NULL,
  `thawing_utensils_clean_comment` text DEFAULT NULL,
  `thawing_sink_setup` enum('yes','no') DEFAULT NULL,
  `thawing_sink_setup_comment` text DEFAULT NULL,
  `thawing_portion_standards` enum('yes','no') DEFAULT NULL,
  `thawing_portion_standards_comment` text DEFAULT NULL,
  `thawing_sultan_sauce` enum('yes','no') DEFAULT NULL,
  `thawing_sultan_sauce_comment` text DEFAULT NULL,
  `thawing_vegetables_date` enum('yes','no') DEFAULT NULL,
  `thawing_vegetables_date_comment` text DEFAULT NULL,
  `thawing_follow_fifo` enum('yes','no') DEFAULT NULL,
  `thawing_follow_fifo_comment` text DEFAULT NULL,
  `burger_standard_setup` enum('yes','no') DEFAULT NULL,
  `burger_standard_setup_comment` text DEFAULT NULL,
  `burger_sauce_spiral` enum('yes','no') DEFAULT NULL,
  `burger_sauce_spiral_comment` text DEFAULT NULL,
  `burger_ingredients_order` enum('yes','no') DEFAULT NULL,
  `burger_ingredients_order_comment` text DEFAULT NULL,
  `manager_signature` varchar(255) NOT NULL,
  `status` enum('Draft','Submitted','Reviewed','Approved') NOT NULL DEFAULT 'Submitted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `brand` enum('SDB','FB','TNDR','Pizza Dealer') DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','under-review') NOT NULL DEFAULT 'active',
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `restaurant_manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `area_manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `manager` varchar(255) DEFAULT NULL,
  `opening_hours` varchar(100) DEFAULT NULL,
  `seating_capacity` int(11) DEFAULT NULL,
  `staff_count` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `code`, `brand`, `type`, `status`, `address`, `city`, `location`, `latitude`, `longitude`, `phone`, `restaurant_manager_id`, `area_manager_id`, `email`, `manager`, `opening_hours`, `seating_capacity`, `staff_count`, `notes`, `is_active`, `created_at`, `updated_at`, `manager_id`) VALUES
(1, 'Al Basateen', 'AQ001', NULL, NULL, 'active', NULL, NULL, 'Al Qassim', 21.67684393, 39.12024071, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-10-26 09:02:18', '2025-11-02 05:31:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_user`
--

CREATE TABLE `restaurant_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurant_user`
--

INSERT INTO `restaurant_user` (`id`, `user_id`, `restaurant_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, NULL),
(2, 6, 1, NULL, NULL),
(3, 7, 1, NULL, NULL),
(4, 8, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(2, 'Area Manager', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(3, 'Regional Manager', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(4, 'Store Manager', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(5, 'MIC', 'web', '2025-10-14 03:59:20', '2025-10-14 03:59:20'),
(6, 'admin', 'web', '2025-10-14 04:13:26', '2025-10-14 04:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 6),
(2, 1),
(2, 6),
(3, 1),
(3, 6),
(4, 1),
(4, 6),
(5, 1),
(5, 6),
(6, 1),
(6, 6),
(7, 1),
(7, 6),
(8, 1),
(8, 6),
(9, 1),
(9, 6),
(10, 1),
(10, 6),
(11, 1),
(11, 6),
(12, 1),
(12, 6),
(13, 1),
(13, 6),
(14, 1),
(14, 6),
(15, 1),
(15, 6),
(16, 1),
(16, 6),
(17, 1),
(17, 6),
(18, 1),
(18, 6),
(19, 1),
(19, 6),
(20, 1),
(20, 6),
(21, 1),
(21, 6),
(22, 1),
(22, 6),
(23, 1),
(23, 6),
(24, 1),
(24, 6),
(25, 1),
(25, 6),
(26, 1),
(26, 6),
(27, 1),
(27, 6),
(28, 1),
(28, 6),
(29, 1),
(29, 6),
(30, 1),
(30, 6),
(31, 1),
(31, 6),
(32, 1),
(32, 6),
(33, 1),
(33, 6),
(34, 1),
(34, 6),
(35, 1),
(35, 6),
(36, 1),
(36, 6),
(37, 1),
(37, 6),
(38, 1),
(38, 6),
(39, 1),
(39, 6),
(40, 1),
(40, 6),
(41, 1),
(41, 6),
(42, 1),
(42, 6),
(43, 1),
(43, 6),
(44, 1),
(44, 6),
(45, 1),
(45, 6),
(46, 1),
(46, 6),
(47, 1),
(47, 6),
(48, 1),
(48, 6),
(49, 1),
(49, 6),
(50, 1),
(50, 6),
(51, 1),
(51, 6),
(52, 1),
(52, 6),
(53, 1),
(53, 6),
(54, 1),
(54, 6),
(55, 1),
(55, 6),
(56, 1),
(56, 6),
(57, 1),
(57, 6),
(58, 1),
(58, 6),
(59, 1),
(59, 6),
(60, 1),
(60, 6),
(61, 1),
(61, 6),
(62, 1),
(62, 6),
(63, 1),
(63, 6),
(64, 1),
(64, 6),
(65, 1),
(65, 6),
(66, 1),
(66, 6),
(67, 1),
(67, 6),
(68, 1),
(68, 6),
(69, 1),
(69, 6);

-- --------------------------------------------------------

--
-- Table structure for table `role_page_permissions`
--

CREATE TABLE `role_page_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `page_permission_id` bigint(20) UNSIGNED NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `can_create` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `can_approve` tinyint(1) NOT NULL DEFAULT 0,
  `show_in_dashboard` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_page_permissions`
--

INSERT INTO `role_page_permissions` (`id`, `role_id`, `page_permission_id`, `can_view`, `can_create`, `can_edit`, `can_delete`, `can_approve`, `show_in_dashboard`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(2, 1, 2, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(3, 1, 3, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(4, 1, 4, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(5, 1, 5, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(6, 1, 6, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(7, 1, 7, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(8, 1, 8, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(9, 1, 9, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(10, 1, 10, 1, 1, 1, 1, 1, 1, '2025-10-14 04:09:03', '2025-10-14 04:09:03'),
(11, 2, 1, 0, 0, 0, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(12, 2, 2, 1, 1, 1, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(13, 2, 3, 1, 1, 1, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(14, 2, 4, 0, 0, 0, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(15, 2, 5, 0, 0, 0, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(16, 2, 6, 1, 1, 1, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(17, 2, 7, 1, 1, 1, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(18, 2, 8, 0, 0, 0, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(19, 2, 9, 0, 0, 0, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(20, 2, 10, 0, 0, 0, 0, 0, 1, '2025-10-26 08:55:49', '2025-10-26 08:55:49'),
(21, 4, 1, 0, 0, 0, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(22, 4, 2, 1, 0, 0, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(23, 4, 3, 1, 1, 1, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(24, 4, 4, 0, 0, 0, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(25, 4, 5, 0, 0, 0, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(26, 4, 6, 1, 1, 1, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(27, 4, 7, 1, 1, 1, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(28, 4, 8, 0, 0, 0, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(29, 4, 9, 0, 0, 0, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(30, 4, 10, 0, 0, 0, 0, 0, 1, '2025-10-26 08:56:20', '2025-10-26 08:56:20'),
(31, 6, 1, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02'),
(32, 6, 2, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02'),
(33, 6, 3, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02'),
(34, 6, 4, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02'),
(35, 6, 5, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02'),
(36, 6, 6, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02'),
(37, 6, 7, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02'),
(38, 6, 8, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02'),
(39, 6, 9, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02'),
(40, 6, 10, 1, 1, 1, 1, 1, 1, '2025-11-02 07:31:02', '2025-11-02 07:31:02');

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
('3QnqODPDPGSDAXuS1wlkUj2kmYUVLKbbq1YuuGIT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZFNTdE5WVXFIdlJmQUV1MGZZa3REWFhlaVFhSWI4cmJsb1hiN1JEZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762158726),
('3QsxIIN4pOk9vALBvq3xf8k7SlM6D5CrjFIAR6Nu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVWJ3MENZMDVhSTVZSzZXMnNFQ01qOW9WNjRxWlZXV21OYlRRZHZrVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762159083),
('4QOPJ3GlAhXSuMYQiGJjoIrYZudFmsBvUiSjZsHS', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidHBleEpQakV6Y1lzQU5pdkxRQlBUcmRENmFPU0VrSndNQmE1WVo4dyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvYWN0aW9uLXBsYW5zP2lkPTRhMzQ2ZThmLTA4OGQtNDYzNy05YmZhLTE0NGRlOTA4ODlkOSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc2MjE1OTMyODg1MCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjExMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9hY3Rpb24tcGxhbnM/aWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU5MzI4ODUwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762159328),
('7eDtFarZA0El6q9Pk4XtF3sPlU4OEZtvtEudLppC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOEVsT0hNdE1sTUxtY1NUMkg1UllpOVRTeVFISEk3aEVkR1dYNm1xViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6OTU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC8/aWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU5MzI2NDQ0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762159326),
('AfL61ITlbLfaKI2clFwkKbKPb4kkTSvrvjISEiRN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidXNKY1JaWG54S3NOY2Zsb1VhbjNWTmhWb2h3TmxDOG1kaE00UTRGOSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvc3RvcmUtdmlzaXRzLzI0L3ByaW50LXJlcG9ydD9pZD00YTM0NmU4Zi0wODhkLTQ2MzctOWJmYS0xNDRkZTkwODg5ZDkmdnNjb2RlQnJvd3NlclJlcUlkPTE3NjIxNjAzNzYwNDkiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoxMjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvc3RvcmUtdmlzaXRzLzI0L3ByaW50LXJlcG9ydD9pZD00YTM0NmU4Zi0wODhkLTQ2MzctOWJmYS0xNDRkZTkwODg5ZDkmdnNjb2RlQnJvd3NlclJlcUlkPTE3NjIxNjAzNzYwNDkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762160376),
('arYUg0kxeiU3CiraOLABy2FOHPQfijlPh3zG0sEI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOHNEanpBS0R6Q1FSR1FBTjFqYTdDRnJzMFBVTGROTXQ4cEZGaWtXTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762157785),
('hrWfXqJHcrRxBqESySjjIc1I5hHUxbvz3ixu1Qpi', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRnE2RjVMS2g0bDdRSGVSc3FieW9rT0VkOVlMOElGUmRBMkRiMFp0SCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762157921),
('IBcZJsGro2pp1FAX3TkCF4N5MowInUkbOz8HLhPT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibmRiSW50MmgyQUljdmZJTnB6eXRZck1SWWVEejlTbVlyUWxKMkxZSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762159256),
('jaSVyjTKlbjMVmS6j9pCjlrYdQbzzkbCf1EYByfg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRmFaSUkyVnpvdnBYU3lSRGZXUk9FS2hNM3B4eHNmSEhYbVk5MWYxdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762159329),
('KUMRVCIO9QuPc4Oew34NsWzjlozaUDjSnxO82ANI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM1Q4MTJzNDl1ZG9zQ0lXQVpmWTNzWG5EbkR3TVJYVXNyb0hDbkpCNyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvYWN0aW9uLXBsYW5zP2lkPTRhMzQ2ZThmLTA4OGQtNDYzNy05YmZhLTE0NGRlOTA4ODlkOSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc2MjE1ODcyNTg2NCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjExMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9hY3Rpb24tcGxhbnM/aWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU4NzI1ODY0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762158726),
('KzVgP2R0u9Etw6EtoNXkD5CrLl5Mp8CHwg8Fyo3L', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRDdiUDh4aGZnNURBdURjTDNEZ2hxMmJqYmNxN1RYY1BQczFNMXdVaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvYWN0aW9uLXBsYW5zP2FyZWFfbWFuYWdlcj0xJmlkPTRhMzQ2ZThmLTA4OGQtNDYzNy05YmZhLTE0NGRlOTA4ODlkOSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc2MjE1OTA3MjQ4MCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjEyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9hY3Rpb24tcGxhbnM/YXJlYV9tYW5hZ2VyPTEmaWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU5MDcyNDgwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762159072),
('lbIHD16fEmmtVK6bhPvjKbkKS6Pa344G9fwsqrIU', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDdJdDd1UnZlRFFPRm5FY0hpZWhTazdaZGJwNHlzRmVucDBoSEMzNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762160376),
('OTdEtWHdcAxZKK5aw95Zsn7aHrLkhAet3EYCWXd6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYlpYaTh2NHpKc1E5eVp2SHo1VWNTdDVZa1B2TXVSYkN2V3hEUDlueCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvZGFzaGJvYXJkP2lkPTRhMzQ2ZThmLTA4OGQtNDYzNy05YmZhLTE0NGRlOTA4ODlkOSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc2MjE1NzkyMTQ5MCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjEwODoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9kYXNoYm9hcmQ/aWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU3OTIxNDkwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762157921),
('PaZQpqXtkNTEiVntblYlvvZNnjYRT464JAIZ9sNl', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNlhHWnBQNnczekFTeUVpbVk1dHRwZzZGNk9BZloxU0U2MFFsTk5ENiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvYWN0aW9uLXBsYW5zP2lkPTRhMzQ2ZThmLTA4OGQtNDYzNy05YmZhLTE0NGRlOTA4ODlkOSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc2MjE1OTI4MTY4OSI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjExMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9hY3Rpb24tcGxhbnM/aWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU5MjgxNjg5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762159282),
('QC7aEa7gM9pF3hv0NCriX0pTMKNhjkVBAOucJeMS', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZmVkb1V4emJhSXNoR3dDZkxYNDgydWlqMDZ3bjJ1eFdaUGtDbFdMQyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvYWN0aW9uLXBsYW5zP2lkPTRhMzQ2ZThmLTA4OGQtNDYzNy05YmZhLTE0NGRlOTA4ODlkOSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc2MjE1OTI1NjU0NCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjExMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9hY3Rpb24tcGxhbnM/aWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU5MjU2NTQ0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762159256),
('rftD2RXBiclpr5aDzgApfSYlRKvYxpnDNSQbSYAO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid3BDazRFbGhCeWx4NWJrbWtHamlVcmJvSGFwMXBiV3FLZ0kxTkFsWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762159326),
('sjf5yO7AsMP2sSBQj3EEUZnKxD0YPSSS0O4K4vxy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOUJUeWtFeGZ0WDNLaTJGdXh0VTdvOFlmdGtOVVhJREdYcTV2NWF1WiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762159326),
('St4KS9SdVxeiwSE2Da03n7RnI0xMaqusxZWgLMHF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRzlpOG5FTGhzN0Y1Z3FQaUZSQ3NxUWw1VjZQNUhKcTZFdHp4T3BDViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvZGFzaGJvYXJkP2lkPTRhMzQ2ZThmLTA4OGQtNDYzNy05YmZhLTE0NGRlOTA4ODlkOSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc2MjE1Nzc4NTIxOCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjEwODoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9kYXNoYm9hcmQ/aWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU3Nzg1MjE4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762157785),
('t5BwafDt56ejlME8uX0KruXn4Q2qXQi1eYvSZ6FC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaDlVTFVtQVRPVlZ6NGkxQk1IUVpTbWJBUTlsaXh2bTFUZHptdXFhMCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvYWN0aW9uLXBsYW5zP2lkPTRhMzQ2ZThmLTA4OGQtNDYzNy05YmZhLTE0NGRlOTA4ODlkOSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc2MjE1ODk2NzkzNyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjExMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9hY3Rpb24tcGxhbnM/aWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU4OTY3OTM3Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762158968),
('VgSfmyXSGGr6nniMWG9Iwz71YNXUutLwW4tUTGtf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZFdXdktZclhqd2NnR2F0ZlZienpXb1lMY0ZUU3dqd2Nob2ZMWU5uSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762158968),
('VMgxfQjGIBSXBVYFigF8ci2tktBOpRNiRd76DeZN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjYydmZZVk5oV2lPcE1lWTZNdmtsOEl2QU5aRnFyVnBnQnJ5enhvWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762159282),
('WCzjZcEFK0CpwUtwtPIySF8ssqM6WvKmpeTdOcf9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaGxXUVFRQ2Fjc05sNEY1aWozSTBFRXdWQXdiTzdkM3RYRzNTdDNLUyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvYWN0aW9uLXBsYW5zP2lkPTRhMzQ2ZThmLTA4OGQtNDYzNy05YmZhLTE0NGRlOTA4ODlkOSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc2MjE1OTA4MjkyOSI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjExMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9hY3Rpb24tcGxhbnM/aWQ9NGEzNDZlOGYtMDg4ZC00NjM3LTliZmEtMTQ0ZGU5MDg4OWQ5JnZzY29kZUJyb3dzZXJSZXFJZD0xNzYyMTU5MDgyOTI5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762159083),
('WRokjZRXf4ElhsKebGb0Qe4NgbZ4KzOEF1RazaaG', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiamlzbmxvdUh1Q1ZHelRYa0VVdFJJR0Z0MlZ4a0c3WkVNeVVDdDFYYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Ftcy9kYXNoYm9hcmQiO319', 1762166555),
('YfwY8pzJsjBseopz62duIqOCh5YbdzAOpXdBHPbM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.105.1 Chrome/138.0.7204.251 Electron/37.6.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicEpRaUpkREs3VmpWaTM4NXNPdDdNMFpsN2FEaDNvMWpiYzlrTDRDWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xbXMvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762159073);

-- --------------------------------------------------------

--
-- Table structure for table `store_visits`
--

CREATE TABLE `store_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_name` varchar(255) NOT NULL,
  `mic` enum('Morning','Evening') NOT NULL,
  `visit_date` date NOT NULL,
  `score` int(11) DEFAULT NULL,
  `calculated_score` decimal(5,2) DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `yes_answers` int(11) DEFAULT NULL,
  `no_answers` int(11) DEFAULT NULL,
  `notifications_sent` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notifications_sent`)),
  `purpose_of_visit` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`purpose_of_visit`)),
  `oca_board_followed` tinyint(1) DEFAULT NULL,
  `oca_board_comments` text DEFAULT NULL,
  `staff_know_duty` tinyint(1) DEFAULT NULL,
  `staff_duty_comments` text DEFAULT NULL,
  `coaching_directing` tinyint(1) DEFAULT NULL,
  `coaching_comments` text DEFAULT NULL,
  `smile_greetings` tinyint(1) DEFAULT NULL,
  `smile_comments` text DEFAULT NULL,
  `suggestive_selling` tinyint(1) DEFAULT NULL,
  `suggestive_comments` text DEFAULT NULL,
  `offer_promotion` tinyint(1) DEFAULT NULL,
  `promotion_comments` text DEFAULT NULL,
  `thank_direction` tinyint(1) DEFAULT NULL,
  `thank_comments` text DEFAULT NULL,
  `team_work_hustle` tinyint(1) DEFAULT NULL,
  `teamwork_comments` text DEFAULT NULL,
  `order_accuracy` tinyint(1) DEFAULT NULL,
  `accuracy_comments` text DEFAULT NULL,
  `service_time` tinyint(1) DEFAULT NULL,
  `service_comments` text DEFAULT NULL,
  `dine_in` tinyint(1) DEFAULT NULL,
  `dine_comments` text DEFAULT NULL,
  `take_out` tinyint(1) DEFAULT NULL,
  `takeout_comments` text DEFAULT NULL,
  `family` tinyint(1) DEFAULT NULL,
  `family_comments` text DEFAULT NULL,
  `delivery` tinyint(1) DEFAULT NULL,
  `delivery_comments` text DEFAULT NULL,
  `drive_thru` tinyint(1) DEFAULT NULL,
  `drive_comments` text DEFAULT NULL,
  `weekly_schedule` tinyint(1) DEFAULT NULL,
  `schedule_comments` text DEFAULT NULL,
  `mod_financial_goal` tinyint(1) DEFAULT NULL,
  `financial_comments` text DEFAULT NULL,
  `sales_objectives` tinyint(1) DEFAULT NULL,
  `sales_comments` text DEFAULT NULL,
  `cash_policies` tinyint(1) DEFAULT NULL,
  `cash_comments` text DEFAULT NULL,
  `daily_waste` tinyint(1) DEFAULT NULL,
  `waste_comments` text DEFAULT NULL,
  `ttf_followed` tinyint(1) DEFAULT NULL,
  `ttf_comments` text DEFAULT NULL,
  `sandwich_assembly` tinyint(1) DEFAULT NULL,
  `assembly_comments` text DEFAULT NULL,
  `qsc_completed` tinyint(1) DEFAULT NULL,
  `qsc_comments` text DEFAULT NULL,
  `oil_standards` tinyint(1) DEFAULT NULL,
  `oil_comments` text DEFAULT NULL,
  `day_labels` tinyint(1) DEFAULT NULL,
  `labels_comments` text DEFAULT NULL,
  `equipment_working` tinyint(1) DEFAULT NULL,
  `equipment_comments` text DEFAULT NULL,
  `fryer_condition` tinyint(1) DEFAULT NULL,
  `fryer_comments` text DEFAULT NULL,
  `vegetable_prep` tinyint(1) DEFAULT NULL,
  `vegetable_comments` text DEFAULT NULL,
  `employee_appearance` tinyint(1) DEFAULT NULL,
  `appearance_comments` text DEFAULT NULL,
  `equipment_wrapped` tinyint(1) DEFAULT NULL,
  `wrapped_comments` text DEFAULT NULL,
  `sink_setup` tinyint(1) DEFAULT NULL,
  `sink_comments` text DEFAULT NULL,
  `sanitizer_standard` tinyint(1) DEFAULT NULL,
  `sanitizer_comments` text DEFAULT NULL,
  `dining_area_clean` tinyint(1) DEFAULT NULL,
  `dining_comments` text DEFAULT NULL,
  `restroom_clean` tinyint(1) DEFAULT NULL,
  `restroom_comments` text DEFAULT NULL,
  `general_comments` text DEFAULT NULL,
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photos`)),
  `status` enum('draft','submitted','under_review','action_plans_created','completed','approved','rejected') NOT NULL DEFAULT 'draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_visit_date` date DEFAULT NULL,
  `last_visit_summary` text DEFAULT NULL,
  `last_visit_update` text DEFAULT NULL,
  `other_follow_up` text DEFAULT NULL,
  `what_did_you_see` text DEFAULT NULL,
  `why_had_issue` text DEFAULT NULL,
  `how_to_improve` text DEFAULT NULL,
  `who_when_responsible` text DEFAULT NULL,
  `action_plan_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`action_plan_items`)),
  `performance_score_percentage` decimal(5,2) DEFAULT NULL,
  `report_timestamp` timestamp NULL DEFAULT NULL,
  `digital_signature_mic` varchar(255) DEFAULT NULL,
  `digital_signature_reviewer` varchar(255) DEFAULT NULL,
  `report_type` enum('Quality Audit','Operational Assessment','Training Audit','Measurement and Coaching') DEFAULT NULL,
  `export_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`export_history`)),
  `created_by_name` varchar(255) DEFAULT NULL,
  `reviewer_name` varchar(255) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `action_plans_completed_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `area_manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `store_manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_visits`
--

INSERT INTO `store_visits` (`id`, `user_id`, `restaurant_name`, `mic`, `visit_date`, `score`, `calculated_score`, `total_questions`, `yes_answers`, `no_answers`, `notifications_sent`, `purpose_of_visit`, `oca_board_followed`, `oca_board_comments`, `staff_know_duty`, `staff_duty_comments`, `coaching_directing`, `coaching_comments`, `smile_greetings`, `smile_comments`, `suggestive_selling`, `suggestive_comments`, `offer_promotion`, `promotion_comments`, `thank_direction`, `thank_comments`, `team_work_hustle`, `teamwork_comments`, `order_accuracy`, `accuracy_comments`, `service_time`, `service_comments`, `dine_in`, `dine_comments`, `take_out`, `takeout_comments`, `family`, `family_comments`, `delivery`, `delivery_comments`, `drive_thru`, `drive_comments`, `weekly_schedule`, `schedule_comments`, `mod_financial_goal`, `financial_comments`, `sales_objectives`, `sales_comments`, `cash_policies`, `cash_comments`, `daily_waste`, `waste_comments`, `ttf_followed`, `ttf_comments`, `sandwich_assembly`, `assembly_comments`, `qsc_completed`, `qsc_comments`, `oil_standards`, `oil_comments`, `day_labels`, `labels_comments`, `equipment_working`, `equipment_comments`, `fryer_condition`, `fryer_comments`, `vegetable_prep`, `vegetable_comments`, `employee_appearance`, `appearance_comments`, `equipment_wrapped`, `wrapped_comments`, `sink_setup`, `sink_comments`, `sanitizer_standard`, `sanitizer_comments`, `dining_area_clean`, `dining_comments`, `restroom_clean`, `restroom_comments`, `general_comments`, `photos`, `status`, `submitted_at`, `created_at`, `updated_at`, `last_visit_date`, `last_visit_summary`, `last_visit_update`, `other_follow_up`, `what_did_you_see`, `why_had_issue`, `how_to_improve`, `who_when_responsible`, `action_plan_items`, `performance_score_percentage`, `report_timestamp`, `digital_signature_mic`, `digital_signature_reviewer`, `report_type`, `export_history`, `created_by_name`, `reviewer_name`, `reviewed_at`, `action_plans_completed_at`, `approved_at`, `completed_at`, `attachments`, `restaurant_id`, `area_manager_id`, `store_manager_id`, `reviewed_by`) VALUES
(24, 2, 'Al Basateen', 'Morning', '2025-11-03', 97, NULL, NULL, NULL, NULL, NULL, '[\"Operational Assessment\"]', 1, 'DONE', 1, 'DONE', 0, 'no', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 1, 'DONE', 'DONE', NULL, 'approved', NULL, '2025-11-03 03:59:43', '2025-11-03 03:59:47', '2025-11-03', 'DONE', 'DONE', 'DONE', 'DONE', 'DONE', 'DONE', 'DONE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `store_visit_answers`
--

CREATE TABLE `store_visit_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `section` varchar(255) NOT NULL,
  `question` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `answer` tinyint(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temperature_shift_turnovers`
--

CREATE TABLE `temperature_shift_turnovers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `mic_morning_status` enum('Y','N') DEFAULT NULL,
  `mic_evening_status` enum('Y','N') DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temperature_tracking_forms`
--

CREATE TABLE `temperature_tracking_forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `cooked_products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cooked_products`)),
  `holding_products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`holding_products`)),
  `vegetables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vegetables`)),
  `equipment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`equipment`)),
  `sanitizer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sanitizer`)),
  `receiving_products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`receiving_products`)),
  `shift` varchar(255) DEFAULT NULL,
  `mic_morning` varchar(255) DEFAULT NULL,
  `mic_evening` varchar(255) DEFAULT NULL,
  `corrective_action` text DEFAULT NULL,
  `shift_turnover` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shift_turnover`)),
  `status` enum('Draft','Submitted','Reviewed') NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `side_cooked` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`side_cooked`)),
  `side_holding` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`side_holding`)),
  `vegetable_receiving` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vegetable_receiving`)),
  `product_receiving_side` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_receiving_side`)),
  `corrective_action_upper` text DEFAULT NULL,
  `corrective_action_lower` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temperature_tracking_items`
--

CREATE TABLE `temperature_tracking_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `time_slot` varchar(255) NOT NULL,
  `temperature` double DEFAULT NULL,
  `ppm` int(11) DEFAULT NULL,
  `status` enum('Y','N') DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `employee_id` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `avatar_url`, `employee_id`, `email_verified_at`, `password`, `last_login_at`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'GS QMS Admin', 'it@ghidas.com', NULL, NULL, NULL, '$2y$12$S67TuBr07HdMF54Vp2UIOOyMrstp1Eh0jIeZiMrYZtSKWWxjPvdny', NULL, 1, 'SQCFMo25L2423QletBoyBZtUh4h8VG0UX4MC9AZFfWY7XhFbv0vHyGQ1hRuY', '2025-10-14 03:59:20', '2025-11-02 04:00:33'),
(6, 'John Smith', 'john.smith@qms.com', NULL, NULL, NULL, '$2y$12$vqCoXGar9DapFd.bHiuX8eb4o7WdwiaJaBRla7EjAw6eg0jCNdTS6', NULL, 1, NULL, '2025-11-03 05:24:46', '2025-11-03 05:24:46'),
(7, 'Sarah Johnson', 'sarah.johnson@qms.com', NULL, NULL, NULL, '$2y$12$sxiEhsc5q8Su/UmgGOlziOf5i61v29ZqPxERo/AHYQO2zjqLoOlo2', NULL, 1, NULL, '2025-11-03 05:24:46', '2025-11-03 05:24:46'),
(8, 'Mike Wilson', 'mike.wilson@qms.com', NULL, NULL, NULL, '$2y$12$VZkaKVrb5f9oKlk/AqyVEeOY4VWR6kFspMkrRhjJDxGy3/KO9Ib7W', NULL, 1, NULL, '2025-11-03 05:24:46', '2025-11-03 05:24:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_area_managers`
--

CREATE TABLE `user_area_managers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `area_manager_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action_plans`
--
ALTER TABLE `action_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_plans_store_visit_id_status_index` (`store_visit_id`,`status`),
  ADD KEY `action_plans_assigned_to_status_index` (`assigned_to`,`status`),
  ADD KEY `action_plans_due_date_index` (`due_date`),
  ADD KEY `action_plans_is_approved_status_index` (`is_approved`,`status`),
  ADD KEY `action_plans_approved_by_index` (`approved_by`);

--
-- Indexes for table `action_plan_images`
--
ALTER TABLE `action_plan_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_plan_images_action_plan_id_field_name_index` (`action_plan_id`,`field_name`);

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
-- Indexes for table `page_permissions`
--
ALTER TABLE `page_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_permissions_page_key_unique` (`page_key`);

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
-- Indexes for table `qsc_checklists`
--
ALTER TABLE `qsc_checklists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qsc_checklists_user_id_foreign` (`user_id`),
  ADD KEY `qsc_checklists_store_name_date_index` (`store_name`,`date`),
  ADD KEY `qsc_checklists_time_option_date_index` (`time_option`,`date`),
  ADD KEY `qsc_checklists_status_index` (`status`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurants_code_unique` (`code`),
  ADD KEY `restaurants_manager_id_foreign` (`manager_id`),
  ADD KEY `restaurants_type_index` (`type`),
  ADD KEY `restaurants_status_index` (`status`),
  ADD KEY `restaurants_city_index` (`city`),
  ADD KEY `restaurants_restaurant_manager_id_index` (`restaurant_manager_id`),
  ADD KEY `restaurants_area_manager_id_index` (`area_manager_id`),
  ADD KEY `restaurants_brand_index` (`brand`),
  ADD KEY `idx_restaurant_coordinates` (`latitude`,`longitude`);

--
-- Indexes for table `restaurant_user`
--
ALTER TABLE `restaurant_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_user_user_id_restaurant_id_unique` (`user_id`,`restaurant_id`),
  ADD KEY `restaurant_user_restaurant_id_foreign` (`restaurant_id`);

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
-- Indexes for table `role_page_permissions`
--
ALTER TABLE `role_page_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_page_permissions_role_id_page_permission_id_unique` (`role_id`,`page_permission_id`),
  ADD KEY `role_page_permissions_page_permission_id_foreign` (`page_permission_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `store_visits`
--
ALTER TABLE `store_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store_visits_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `store_visits_area_manager_id_foreign` (`area_manager_id`),
  ADD KEY `store_visits_store_manager_id_foreign` (`store_manager_id`),
  ADD KEY `store_visits_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `store_visits_visit_date_index` (`visit_date`),
  ADD KEY `store_visits_status_index` (`status`),
  ADD KEY `store_visits_mic_index` (`mic`),
  ADD KEY `store_visits_restaurant_name_index` (`restaurant_name`),
  ADD KEY `store_visits_user_id_visit_date_index` (`user_id`,`visit_date`),
  ADD KEY `store_visits_status_visit_date_index` (`status`,`visit_date`),
  ADD KEY `store_visits_created_at_index` (`created_at`);

--
-- Indexes for table `store_visit_answers`
--
ALTER TABLE `store_visit_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store_visit_answers_visit_id_answer_index` (`visit_id`,`answer`);

--
-- Indexes for table `temperature_shift_turnovers`
--
ALTER TABLE `temperature_shift_turnovers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `temperature_shift_turnovers_form_id_foreign` (`form_id`);

--
-- Indexes for table `temperature_tracking_forms`
--
ALTER TABLE `temperature_tracking_forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `temperature_tracking_forms_store_visit_id_foreign` (`store_visit_id`),
  ADD KEY `temperature_tracking_forms_store_id_foreign` (`store_id`),
  ADD KEY `temperature_tracking_forms_created_by_foreign` (`created_by`);

--
-- Indexes for table `temperature_tracking_items`
--
ALTER TABLE `temperature_tracking_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `temperature_tracking_items_form_id_foreign` (`form_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_is_active_index` (`is_active`),
  ADD KEY `users_employee_id_index` (`employee_id`),
  ADD KEY `users_last_login_at_index` (`last_login_at`);

--
-- Indexes for table `user_area_managers`
--
ALTER TABLE `user_area_managers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_area_managers_user_id_area_manager_id_unique` (`user_id`,`area_manager_id`),
  ADD KEY `user_area_managers_user_id_index` (`user_id`),
  ADD KEY `user_area_managers_area_manager_id_index` (`area_manager_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_plans`
--
ALTER TABLE `action_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `action_plan_images`
--
ALTER TABLE `action_plan_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `page_permissions`
--
ALTER TABLE `page_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `qsc_checklists`
--
ALTER TABLE `qsc_checklists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `restaurant_user`
--
ALTER TABLE `restaurant_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `role_page_permissions`
--
ALTER TABLE `role_page_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `store_visits`
--
ALTER TABLE `store_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `store_visit_answers`
--
ALTER TABLE `store_visit_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temperature_shift_turnovers`
--
ALTER TABLE `temperature_shift_turnovers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temperature_tracking_forms`
--
ALTER TABLE `temperature_tracking_forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `temperature_tracking_items`
--
ALTER TABLE `temperature_tracking_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_area_managers`
--
ALTER TABLE `user_area_managers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `action_plans`
--
ALTER TABLE `action_plans`
  ADD CONSTRAINT `action_plans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `action_plans_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `action_plans_store_visit_id_foreign` FOREIGN KEY (`store_visit_id`) REFERENCES `store_visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `action_plan_images`
--
ALTER TABLE `action_plan_images`
  ADD CONSTRAINT `action_plan_images_action_plan_id_foreign` FOREIGN KEY (`action_plan_id`) REFERENCES `action_plans` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `qsc_checklists`
--
ALTER TABLE `qsc_checklists`
  ADD CONSTRAINT `qsc_checklists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD CONSTRAINT `restaurants_area_manager_id_foreign` FOREIGN KEY (`area_manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `restaurants_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `restaurants_restaurant_manager_id_foreign` FOREIGN KEY (`restaurant_manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `restaurant_user`
--
ALTER TABLE `restaurant_user`
  ADD CONSTRAINT `restaurant_user_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_page_permissions`
--
ALTER TABLE `role_page_permissions`
  ADD CONSTRAINT `role_page_permissions_page_permission_id_foreign` FOREIGN KEY (`page_permission_id`) REFERENCES `page_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_page_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `store_visits`
--
ALTER TABLE `store_visits`
  ADD CONSTRAINT `store_visits_area_manager_id_foreign` FOREIGN KEY (`area_manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `store_visits_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `store_visits_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `store_visits_store_manager_id_foreign` FOREIGN KEY (`store_manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `store_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `store_visit_answers`
--
ALTER TABLE `store_visit_answers`
  ADD CONSTRAINT `store_visit_answers_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `store_visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `temperature_shift_turnovers`
--
ALTER TABLE `temperature_shift_turnovers`
  ADD CONSTRAINT `temperature_shift_turnovers_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `temperature_tracking_forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `temperature_tracking_forms`
--
ALTER TABLE `temperature_tracking_forms`
  ADD CONSTRAINT `temperature_tracking_forms_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `temperature_tracking_forms_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `temperature_tracking_forms_store_visit_id_foreign` FOREIGN KEY (`store_visit_id`) REFERENCES `store_visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `temperature_tracking_items`
--
ALTER TABLE `temperature_tracking_items`
  ADD CONSTRAINT `temperature_tracking_items_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `temperature_tracking_forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_area_managers`
--
ALTER TABLE `user_area_managers`
  ADD CONSTRAINT `user_area_managers_area_manager_id_foreign` FOREIGN KEY (`area_manager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_area_managers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
