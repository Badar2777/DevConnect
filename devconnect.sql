-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 10:28 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `devconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `applied_jobs`
--

CREATE TABLE `applied_jobs` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `developer_id` int(11) NOT NULL,
  `applied_at` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applied_jobs`
--

INSERT INTO `applied_jobs` (`id`, `job_id`, `client_id`, `developer_id`, `applied_at`, `status`) VALUES
(1, 5, 32, 31, '2025-07-20 01:24:44', 'rejected'),
(2, 6, 32, 31, '2025-07-20 21:31:29', 'approved'),
(3, 18, 32, 31, '2025-07-22 02:00:28', 'rejected'),
(4, 17, 32, 31, '2025-07-22 02:01:28', 'approved'),
(5, 16, 32, 31, '2025-07-22 02:01:54', 'approved'),
(6, 11, 32, 31, '2025-07-22 02:03:14', 'rejected'),
(7, 14, 32, 31, '2025-07-22 02:06:01', 'rejected'),
(8, 15, 32, 31, '2025-07-22 02:06:09', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `developers`
--

CREATE TABLE `developers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `developer_notifications`
--

CREATE TABLE `developer_notifications` (
  `id` int(11) NOT NULL,
  `developer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `budget` varchar(100) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `admin_seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `client_id`, `title`, `description`, `skills`, `budget`, `deadline`, `created_at`, `admin_seen`) VALUES
(3, 27, 'Full Stack Developer', 'I want a fully functional website for my bussiness', 'JavaScript', '$500', '2025-06-20', '2025-07-12 21:46:47', 1),
(4, 32, 'dev', 'project', 'js', '$500', '2025-10-09', '2025-07-20 00:50:58', 0),
(5, 32, 'dev', 'file', 'JavaScript', '$500', '2026-10-20', '2025-07-20 00:58:21', 0),
(6, 32, 'web dev', 'web dev', 'JavaScript', '$500', '2007-10-20', '2025-07-20 21:31:04', 0),
(7, 32, 'web dev', 'dev', 'php', '660', '2007-10-20', '2025-07-20 21:41:01', 0),
(8, 32, 'frontend', 'web dev', 'JavaScript', '$500', '2206-10-20', '2025-07-21 00:04:14', 0),
(9, 32, 'frontend', 'web dev', 'JavaScript', '$500', '2206-10-20', '2025-07-21 00:04:30', 0),
(10, 32, 'web', 'dev', 'php', '$500', '2007-10-20', '2025-07-21 23:24:59', 0),
(11, 32, 'web', 'dev', 'JavaScript', '$500', '2007-10-20', '2025-07-21 23:34:10', 0),
(12, 32, 'Web', 'Dev', 'HTML', '$500', '2007-10-20', '2025-07-21 23:37:55', 0),
(13, 32, 'Web', 'Dev', 'js', '$500', '2025-07-23', '2025-07-21 23:46:19', 0),
(14, 32, 'web', 'dev', 'php', '$500', '2025-07-30', '2025-07-21 23:55:59', 0),
(15, 32, 'ffff', 'bjhhjhhghghg', 'js', '$500', '2025-07-30', '2025-07-22 00:09:55', 0),
(16, 32, 'luiuyyu', 'vhyu', 'HTML', '$500', '2023-08-07', '2025-07-22 00:28:00', 0),
(17, 32, 'bbb', 'uiiioi', 'php', '$500', '2025-07-25', '2025-07-22 01:40:11', 0),
(18, 32, 'wev', 'dsfasf', 'HTML', '660', '3333-03-31', '2025-07-22 02:00:10', 0);

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `developer_id` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `job_id`, `client_id`, `developer_id`, `applied_at`, `status`) VALUES
(1, 3, 0, 31, '2025-07-19 19:48:23', 'pending'),
(2, 4, 0, 31, '2025-07-19 19:51:46', 'pending'),
(3, 5, 0, 31, '2025-07-19 19:58:41', 'pending'),
(4, 6, 32, 31, '2025-07-20 16:47:30', ''),
(5, 7, 32, 31, '2025-07-20 16:47:32', 'rejected'),
(6, 9, 32, 31, '2025-07-20 19:12:34', 'rejected'),
(7, 8, 32, 31, '2025-07-20 19:12:38', ''),
(8, 10, 32, 31, '2025-07-21 18:25:25', 'pending'),
(9, 11, 32, 31, '2025-07-21 18:34:26', 'pending'),
(10, 12, 32, 31, '2025-07-21 18:38:06', 'pending'),
(11, 13, 32, 31, '2025-07-21 18:53:37', 'pending'),
(12, 14, 32, 31, '2025-07-21 19:02:13', ''),
(13, 15, 32, 31, '2025-07-21 19:10:05', ''),
(14, 16, 32, 31, '2025-07-21 19:28:32', ''),
(15, 17, 32, 31, '2025-07-21 20:40:24', '');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `user_id`, `attempts`, `locked_until`) VALUES
(14, 8, 5, '2025-07-18 19:13:53'),
(17, 19, 1, NULL),
(18, 20, 1, NULL),
(19, 25, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `sender_role` enum('client','developer') NOT NULL DEFAULT 'developer',
  `receiver_role` enum('client','developer') NOT NULL DEFAULT 'developer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`, `file_path`, `attachment`, `sender_role`, `receiver_role`) VALUES
(35, 8, 11, 'Hello', 0, '2025-07-09 06:49:40', NULL, NULL, 'developer', 'developer'),
(41, 8, 12, 'Yes i am', 1, '2025-07-09 07:33:57', NULL, NULL, 'developer', 'developer'),
(42, 8, 12, 'Yes i am', 1, '2025-07-09 07:37:51', NULL, NULL, 'developer', 'developer'),
(43, 8, 12, '', 1, '2025-07-09 07:41:10', NULL, NULL, 'developer', 'developer'),
(45, 8, 12, '', 1, '2025-07-09 07:41:28', NULL, NULL, 'developer', 'developer'),
(47, 8, 12, 'How are you', 1, '2025-07-09 08:04:28', NULL, NULL, 'developer', 'developer'),
(48, 8, 12, 'How are you', 1, '2025-07-09 08:06:07', NULL, NULL, 'developer', 'developer'),
(49, 8, 12, 'Yes i am', 1, '2025-07-09 08:06:17', NULL, NULL, 'developer', 'developer'),
(50, 12, 8, 'hello', 1, '2025-07-09 08:12:30', NULL, NULL, 'developer', 'developer'),
(51, 8, 12, 'Yes i am', 1, '2025-07-09 09:00:24', NULL, NULL, 'developer', 'developer'),
(53, 8, 12, 'How are you', 1, '2025-07-09 09:01:01', NULL, NULL, 'developer', 'developer'),
(54, 12, 8, 'is it working', 1, '2025-07-09 09:01:29', NULL, NULL, 'developer', 'developer'),
(73, 8, 12, 'Hello', 1, '2025-07-09 16:59:54', NULL, NULL, 'developer', 'developer'),
(74, 12, 8, 'Hi', 1, '2025-07-09 17:00:10', NULL, NULL, 'developer', 'developer'),
(75, 8, 12, 'How are you', 1, '2025-07-09 17:00:26', NULL, NULL, 'developer', 'developer'),
(76, 8, 12, 'What is going on', 1, '2025-07-09 17:01:10', NULL, NULL, 'developer', 'developer'),
(139, 20, 19, '👋 Hello! I\'d like to connect with you.', 1, '2025-07-11 13:35:36', NULL, NULL, 'developer', 'developer'),
(140, 19, 20, 'Hello', 1, '2025-07-11 13:36:01', NULL, NULL, 'developer', 'developer'),
(141, 24, 8, '👋 Hello! I\'d like to connect with you.', 0, '2025-07-12 14:38:40', NULL, NULL, 'developer', 'developer'),
(142, 24, 8, 'wndj', 0, '2025-07-12 14:38:47', NULL, NULL, 'developer', 'developer'),
(143, 27, 28, '👋 Hello! I\'d like to connect with you.', 1, '2025-07-12 16:44:18', NULL, NULL, 'developer', 'developer'),
(144, 27, 28, 'Hi', 1, '2025-07-12 16:44:40', NULL, NULL, 'developer', 'developer'),
(146, 32, 31, '👋 Hello! I\\\'d like to connect with you.', 1, '2025-07-19 19:22:10', NULL, NULL, 'developer', 'developer'),
(148, 32, 31, 'Ye', 1, '2025-07-20 18:29:01', NULL, NULL, 'developer', 'developer'),
(149, 31, 32, 'Are you okay', 1, '2025-07-21 21:31:02', NULL, NULL, 'developer', 'developer'),
(150, 32, 31, 'Hi', 1, '2025-07-21 21:32:18', NULL, NULL, 'developer', 'developer'),
(151, 32, 31, 'How are you', 1, '2025-07-21 21:35:15', NULL, NULL, 'developer', 'developer'),
(152, 31, 32, 'how are you', 0, '2025-07-22 07:58:52', NULL, NULL, 'developer', 'developer');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('developer','client') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `user_type`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'developer', 'You have been approved for the job: \"Website Redesign\".', 0, '2025-07-20 19:01:32'),
(2, 1, 'developer', 'A client sent you a new message.', 0, '2025-07-20 19:01:32'),
(3, 2, 'client', 'Developer John Doe applied to your job.', 0, '2025-07-20 19:01:32');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `github_link` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `title`, `description`, `github_link`, `image_path`, `created_at`) VALUES
(1, 8, 'Advanced login Page', 'This is a project which i have developed for the customer ', '', 'project_1751912742.png', '2025-07-07 18:25:42'),
(2, 8, 'Advanced login Page', 'This is a project which i have developed for the customer ', '', 'project_1751913204.png', '2025-07-07 18:33:24'),
(3, 8, 'Result card', 'This is my matric result card', '', 'project_1751913995.jpg', '2025-07-07 18:46:36'),
(4, 8, 'Result card', 'This is my matric result card', '', 'project_1751914094.jpg', '2025-07-07 18:48:14'),
(5, 8, 'Result', 'This is my result card', '', 'project_1751914123.jpg', '2025-07-07 18:48:43'),
(6, 8, 'Result', 'This is my result card', '', 'project_1751914483.jpg', '2025-07-07 18:54:43'),
(7, 8, 'Result', 'This is my result card', '', 'project_1751914752.jpg', '2025-07-07 18:59:12'),
(8, 8, 'Result', 'This is my result card', '', 'project_1751914772.jpg', '2025-07-07 18:59:32'),
(9, 8, 'Result', 'This is my result card', '', 'project_1751915008.jpg', '2025-07-07 19:03:28'),
(10, 8, 'Result', 'This is my result card', '', 'project_1751915085.jpg', '2025-07-07 19:04:45'),
(11, 8, 'Result', 'This is my result card', '', 'project_1751915298.jpg', '2025-07-07 19:08:18'),
(12, 8, 'Result', 'This is my result card', '', 'project_1751915308.jpg', '2025-07-07 19:08:28'),
(13, 8, 'result card', 'card', '', 'project_1751916401.jpg', '2025-07-07 19:26:41'),
(14, 8, 'result card', 'card', '', 'project_1751916892.jpg', '2025-07-07 19:34:52'),
(15, 8, 'pic', 'pic', '', 'project_1751916916.jpg', '2025-07-07 19:35:16'),
(16, 8, 'pic', 'pic', '', 'project_1751916923.jpg', '2025-07-07 19:35:23'),
(17, 8, 'binomo', 'jjnjjk;iootouirhdhuashdbbcfbgogoygagapyahsfdg7e', '', 'project_1751917485.jpg', '2025-07-07 19:44:45'),
(20, 11, 'my matric result card', 'This is my metric result card', '', 'project_1751958071.jpg', '2025-07-08 07:01:11'),
(21, 8, 'jihhhh', ';o8tuyf', '', 'project_1751988517.jpg', '2025-07-08 15:28:37'),
(23, 12, 'inter', 'result card', '', 'project_1752044962.png', '2025-07-09 07:09:22'),
(27, 28, 'Landing Page', 'This is the page of the web', '', 'project_1752339241.jpg', '2025-07-12 16:54:01'),
(31, 31, 'Hi', 'Are you good', 'https://github.com/badarulzaman56', 'project_1752953329.jpg', '2025-07-19 19:28:49'),
(32, 31, 'Hello', 'Hi', 'https://github.com/badarulzaman56', 'project_1752953484.jpg', '2025-07-19 19:31:24');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `developer_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resumes`
--

CREATE TABLE `resumes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `education` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resumes`
--

INSERT INTO `resumes` (`id`, `user_id`, `full_name`, `email`, `contact`, `address`, `summary`, `skills`, `education`, `experience`, `created_at`) VALUES
(2, 28, '', 'badarulzaman22@gmail.com', '03057110012', 'Street No 2', 'I am a web developer', 'C, C++, Js', 'Bs IT', 'Web Developer at alpha acadmey', '2025-07-12 16:43:12');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `user_id`, `skill`, `created_at`) VALUES
(6, 11, 'HTML', '2025-07-08 06:59:40'),
(7, 11, 'react', '2025-07-08 06:59:54'),
(14, 8, 'HTML', '2025-07-08 15:25:14'),
(16, 12, 'PHP', '2025-07-09 07:09:02'),
(20, 24, 'SEO', '2025-07-12 14:43:10'),
(21, 24, 'nbfjds', '2025-07-12 14:43:14'),
(22, 28, 'C', '2025-07-12 16:41:15'),
(23, 28, 'C++', '2025-07-12 16:49:29'),
(30, 31, 'REACT', '2025-07-19 19:21:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_type` enum('client','developer','admin') DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `skills` text DEFAULT NULL,
  `resume_file` varchar(255) DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `status` enum('active','frozen') DEFAULT 'active',
  `admin_seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `contact`, `dob`, `password`, `user_type`, `profile_picture`, `created_at`, `skills`, `resume_file`, `resume_path`, `status`, `admin_seen`) VALUES
(6, 'BADAR', 'ZAMAN', 'ulzaman22@gmail.com', '03057110012', '2025-07-17', '$2y$10$EBOa/X1UDcv7rIubUpRZmudQlOZR5mTFumUhai0jv5fXOoFk7vG5q', 'client', NULL, '2025-07-07 17:59:31', NULL, NULL, NULL, 'active', 1),
(8, 'Badar', 'Zaman', 'po@gmail.com', '03057110012', '2025-05-08', '$2y$10$QtNDekp/RBW4af3R8NcB8eY7K69gvvxPz/wnU4tTInQI0/K5Hc3ru', 'developer', 'profile_8.jpg', '2025-07-07 18:22:02', NULL, NULL, NULL, 'active', 1),
(10, 'BADAR', 'ZAMAN', 'badarulzaman33@gmail.com', '03057110012', '2007-10-20', '$2y$10$15Bj5WbAlAvqAIonAOGeR.BgpGZ59w5jSD7gKStJOKR/vkuHLXBRW', 'client', NULL, '2025-07-08 06:06:42', NULL, NULL, NULL, 'active', 1),
(11, 'BADAR', 'ZAMAN', 'muntaha22@gmail.com', '03057110012', '2007-10-20', '$2y$10$N2epeWuKYME4aVp0J4cuSeBWl9WeeLdXv485Oy1y9Xl5kGAepF2wy', 'developer', 'profile_11.jpg', '2025-07-08 06:57:44', NULL, NULL, NULL, 'active', 1),
(12, 'Taghreed', 'Rizwan', 'taghreed@gmail.com', '03328033263', '2005-12-08', '$2y$10$jNoTZf3iYhfIXeZDs1OQ.eL4mdhLbfPq9cb6.IPCAO9oVczv4Ox4m', 'developer', 'developer_1752044924.jpg', '2025-07-09 07:02:34', NULL, NULL, NULL, 'active', 1),
(18, 'Admin', 'DevConnect', 'admin@devconnect.com', NULL, NULL, '0192023a7bbd73250516f069df18b500', 'admin', NULL, '2025-07-11 08:47:48', NULL, NULL, NULL, 'active', 1),
(19, 'BADAR', 'ZAMAN', 'badarulzaman56@gmail.com', '03057110012', '2007-10-20', '$2y$10$5C1uZjgsjNr6Uj7RDl506.UHKzcnH0qDzn597qme2G4sYOIXsI0ye', 'developer', 'developer_1752240843.jpg', '2025-07-11 12:54:01', NULL, NULL, NULL, 'active', 1),
(20, 'Taghreed', 'Rizwan', 'taghreed5@gmail.com', '03328033263', '2025-07-09', '$2y$10$/.CBpMm176t6hnWkRpxgUuAq7eFvZLAEoR.sJp/mU/O3BOgpfPeYW', 'client', 'client_20.jpg', '2025-07-11 13:34:59', NULL, NULL, NULL, 'active', 1),
(21, 'Ali', 'Ahmad', 'ali@gmail.com', '03057110012', '2007-10-20', '$2y$10$qvLRM8gBHYsd5pPnvHXHpO0T7hwX7CMnHK39tYGamy3RLrtTOa4F6', 'developer', NULL, '2025-07-11 14:06:42', NULL, NULL, NULL, 'active', 1),
(22, 'Hello', 'World', 'hello@gmail.com', '03037706439', '2008-02-01', '$2y$10$gC6lt7qOMZCg4jTYJ.5OK.i7dNXIVp.RKCSTdLUbmf3g3gLXMDppG', 'client', NULL, '2025-07-11 14:11:34', NULL, NULL, NULL, 'active', 1),
(23, 'Badar', 'Zaman', 'badar12@gmail.com', '03057110012', '2007-10-20', '$2y$10$4W8NbTbisiAyRWoTzAf5De/AGbG1n9kMZ0eNvlxTYs3skHslwZPQu', 'developer', NULL, '2025-07-11 14:14:59', NULL, NULL, NULL, 'active', 1),
(24, 'Huzaifa', 'Ahmad', 'huzaifa.@com', '03039049409', '2002-09-30', '$2y$10$l0LiHo0DrZBVwzrK2xxba.fv5360AgbFoo.oAs12LqM9v21gGuJ7G', 'developer', NULL, '2025-07-12 14:33:35', NULL, NULL, NULL, 'active', 1),
(25, 'BADAR', 'ZAMAN', 'badarulzaman22@gmail.com', '03057110012', '2007-10-20', '$2y$10$yWFEQZYzRdG.Ejem9Z4pgOFrJ4/pTQrNAVjBQuWOEL55WubAdluH6', 'developer', NULL, '2025-07-12 15:01:57', NULL, NULL, NULL, 'active', 1),
(26, 'Badar', 'Muneer', 'badar2@gmail.com', '03057110012', '2007-10-12', '$2y$10$95o/6xLzJ27zz15fx.lDrOih2o9UQTK1oLog7MrpHglar6k17MdC6', 'developer', NULL, '2025-07-12 15:03:39', NULL, NULL, NULL, 'active', 1),
(27, 'M', 'Amir', 'amir@gmail.com', '03037706439', '2007-10-20', '$2y$10$uzaXUkctWSm3HKjh3dcLx.npaWBrQkpUM4JxQooXYkldxHSEBYNqq', 'client', 'client_27.jpg', '2025-07-12 16:38:58', NULL, NULL, NULL, 'active', 1),
(28, 'Zaman', 'Badar', 'zaman22@gmail.com', '03057110012', '2007-10-20', '$2y$10$c1OmqAzarXGQhyQHVW858OEEYipM7fZ.NDiEuAWGYVFjwWsHUfsi.', 'developer', 'developer_1752338464.jpg', '2025-07-12 16:40:13', NULL, NULL, 'resume_28.pdf', 'active', 1),
(31, 'BADAR', 'ZAMAN', 'badarulzaman13@gmail.com', '03057110012', '2007-10-20', '$2y$10$r4HxfZzMoDZMYuGaF3GYOOg3/3cQNzDHEkPHkIo5Q6xbpoK8Ks46.', 'developer', 'developer_1752952893.jpg', '2025-07-19 18:51:38', NULL, NULL, NULL, 'active', 0),
(32, 'Badar', 'Zaman', 'badar112@gmail.com', '03057110012', '2007-10-20', '$2y$10$gGkLYvb/Li2Zqp7pJtDpV.tr.TQ9aYooDok6nB20MXgT/vY/79oE2', 'client', 'client_32.jpg', '2025-07-19 19:05:10', NULL, NULL, NULL, 'active', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `developer_id` (`developer_id`),
  ADD KEY `fk_client` (`client_id`);

--
-- Indexes for table `developers`
--
ALTER TABLE `developers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `developer_notifications`
--
ALTER TABLE `developer_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `developer_id` (`developer_id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_id` (`job_id`,`developer_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`client_id`,`developer_id`),
  ADD KEY `developer_id` (`developer_id`);

--
-- Indexes for table `resumes`
--
ALTER TABLE `resumes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `developers`
--
ALTER TABLE `developers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `developer_notifications`
--
ALTER TABLE `developer_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `resumes`
--
ALTER TABLE `resumes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  ADD CONSTRAINT `applied_jobs_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applied_jobs_ibfk_2` FOREIGN KEY (`developer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_client` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `developer_notifications`
--
ALTER TABLE `developer_notifications`
  ADD CONSTRAINT `developer_notifications_ibfk_1` FOREIGN KEY (`developer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD CONSTRAINT `login_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`developer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resumes`
--
ALTER TABLE `resumes`
  ADD CONSTRAINT `resumes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
