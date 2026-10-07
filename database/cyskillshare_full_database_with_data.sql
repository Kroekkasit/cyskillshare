-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Oct 07, 2026 at 01:34 PM
-- Server version: 8.4.11
-- PHP Version: 8.3.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cyskillshare`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `target_type`, `target_id`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES
(1, NULL, 'login_attempt', 'user', NULL, '172.23.0.1', 'curl/8.21.0', '{\"login\": \"student2\"}', '2026-10-03 16:07:18'),
(2, 3, 'login', 'user', 3, '172.23.0.1', 'curl/8.21.0', NULL, '2026-10-03 16:07:18'),
(3, 3, 'group_joined', 'collab_group', 5, '172.23.0.1', 'curl/8.21.0', NULL, '2026-10-03 16:07:18'),
(4, 3, 'mentorship_req_rl', 'rate_limit', NULL, '172.23.0.1', 'curl/8.21.0', NULL, '2026-10-03 16:07:18'),
(5, 3, 'mentorship_requested', 'mentorship', 2, '172.23.0.1', 'curl/8.21.0', NULL, '2026-10-03 16:07:18'),
(6, 3, 'people_search_rl', 'rate_limit', NULL, '172.23.0.1', 'curl/8.21.0', NULL, '2026-10-03 16:07:18'),
(7, NULL, 'login_attempt', 'user', NULL, '172.23.0.1', 'curl/8.21.0', '{\"login\": \"instructor1\"}', '2026-10-03 16:07:18'),
(8, 7, 'login', 'user', 7, '172.23.0.1', 'curl/8.21.0', NULL, '2026-10-03 16:07:19'),
(9, 3, 'group_created', 'collab_group', 8, '172.23.0.1', 'curl/8.21.0', '{\"type\": \"study\"}', '2026-10-03 16:07:36'),
(10, 8, 'register', 'user', 8, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 10:25:47'),
(11, 8, 'login', 'user', 8, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 10:25:47'),
(12, 8, 'thread_created', 'thread', 10, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 10:26:05'),
(13, 9, 'register', 'user', 9, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 10:27:10'),
(14, 9, 'login', 'user', 9, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 10:27:10'),
(15, 9, 'portfolio_updated', 'portfolio', 5, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 10:27:45'),
(16, 9, 'reply_created', 'reply', 5, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 10:29:37'),
(17, 8, 'best_answer_marked', 'reply', 5, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 10:29:47'),
(18, 8, 'best_answer_removed', 'reply', 5, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 10:29:50'),
(19, NULL, 'login_attempt', 'user', NULL, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"login\": \"aloha\"}', '2026-10-04 14:33:06'),
(20, 8, 'login', 'user', 8, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 14:33:06'),
(21, 8, 'portfolio_updated', 'portfolio', 6, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 14:33:36'),
(22, 8, 'lab_start_rl', 'rate_limit', NULL, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 14:35:36'),
(23, 8, 'lab_started', 'lab_instance', 1, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"lab_id\": 1}', '2026-10-04 14:35:36'),
(24, 8, 'lab_ready', 'lab_instance', 1, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 14:35:36'),
(25, 8, 'hint_reveal_rl', 'rate_limit', NULL, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 14:39:49'),
(26, 8, 'hint_revealed', 'challenge_hint', 8, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"penalty\": 10, \"challenge_id\": 5}', '2026-10-04 14:39:49'),
(27, 8, 'hint_reveal_rl', 'rate_limit', NULL, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 14:39:52'),
(28, 8, 'hint_revealed', 'challenge_hint', 9, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"penalty\": 20, \"challenge_id\": 5}', '2026-10-04 14:39:52'),
(29, 8, 'hint_reveal_rl', 'rate_limit', NULL, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 14:39:53'),
(30, 8, 'hint_revealed', 'challenge_hint', 10, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"penalty\": 30, \"challenge_id\": 5}', '2026-10-04 14:39:53'),
(31, 8, 'lab_start_rl', 'rate_limit', NULL, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 14:40:16'),
(32, 8, 'lab_started', 'lab_instance', 2, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"lab_id\": 5}', '2026-10-04 14:40:16'),
(33, 8, 'lab_ready', 'lab_instance', 2, '172.23.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-04 14:40:16'),
(34, NULL, 'login_attempt', 'user', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"login\": \"aloha\"}', '2026-10-05 18:02:42'),
(35, 8, 'login', 'user', 8, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-05 18:02:42'),
(36, 8, 'logout', 'user', 8, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-05 18:02:44'),
(37, NULL, 'login_attempt', 'user', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"login\": \"\'OR\'1\'=\'1\"}', '2026-10-05 18:02:58'),
(38, NULL, 'failed_login', 'user', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"login\": \"\'OR\'1\'=\'1\"}', '2026-10-05 18:02:58'),
(39, NULL, 'login_attempt', 'user', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"login\": \"aloha\"}', '2026-10-06 02:18:45'),
(40, 8, 'login', 'user', 8, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:18:45'),
(41, 8, 'thread_created', 'thread', 11, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:18:54'),
(42, 8, 'reply_created', 'reply', 6, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:19:20'),
(43, 8, 'portfolio_updated', 'user', 8, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:24:48'),
(44, 8, 'writeup_created', 'writeup', 6, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:25:32'),
(45, 8, 'hint_reveal_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:28:48'),
(46, 8, 'hint_revealed', 'challenge_hint', 16, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"penalty\": 10, \"challenge_id\": 11}', '2026-10-06 02:28:48'),
(47, 8, 'challenge_attempt_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:29:18'),
(48, 8, 'challenge_attempt_rl_11', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:29:18'),
(49, 8, 'challenge_attempted', 'challenge', 11, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"correct\": false}', '2026-10-06 02:29:18'),
(50, 8, 'challenge_attempt_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:29:32'),
(51, 8, 'challenge_attempt_rl_11', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:29:32'),
(52, 8, 'challenge_attempted', 'challenge', 11, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"correct\": false}', '2026-10-06 02:29:32'),
(53, 8, 'challenge_attempt_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:30:52'),
(54, 8, 'challenge_attempt_rl_11', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:30:52'),
(55, 8, 'challenge_solved', 'challenge', 11, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"points\": 60}', '2026-10-06 02:30:52'),
(56, 8, 'challenge_attempted', 'challenge', 11, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"correct\": true}', '2026-10-06 02:30:52'),
(57, 8, 'evidence_created', 'skill', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 11, \"source_type\": \"challenge\", \"evidence_type\": \"challenge_solved\"}', '2026-10-06 02:30:52'),
(58, 8, 'skill_level_changed', 'skill', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"to\": 1, \"from\": 0}', '2026-10-06 02:30:52'),
(59, 8, 'evidence_created', 'skill', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 11, \"source_type\": \"challenge\", \"evidence_type\": \"challenge_solved\"}', '2026-10-06 02:30:52'),
(60, 8, 'challenge_attempt_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:31:53'),
(61, 8, 'challenge_attempt_rl_12', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:31:53'),
(62, 8, 'challenge_solved', 'challenge', 12, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"points\": 160}', '2026-10-06 02:31:53'),
(63, 8, 'challenge_attempted', 'challenge', 12, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"correct\": true}', '2026-10-06 02:31:53'),
(64, 8, 'evidence_created', 'skill', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 12, \"source_type\": \"challenge\", \"evidence_type\": \"challenge_solved\"}', '2026-10-06 02:31:53'),
(65, 8, 'skill_level_changed', 'skill', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"to\": 2, \"from\": 1}', '2026-10-06 02:31:53'),
(66, 8, 'evidence_created', 'skill', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 12, \"source_type\": \"challenge\", \"evidence_type\": \"challenge_solved\"}', '2026-10-06 02:31:53'),
(67, 8, 'skill_level_changed', 'skill', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"to\": 1, \"from\": 0}', '2026-10-06 02:31:53'),
(68, NULL, 'login_attempt', 'user', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"login\": \"admin\"}', '2026-10-06 02:34:33'),
(69, 1, 'login', 'user', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-10-06 02:34:33'),
(70, 8, 'challenge_attempt_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:40:26'),
(71, 8, 'challenge_attempt_rl_13', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:40:26'),
(72, 8, 'challenge_solved', 'challenge', 13, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"points\": 280}', '2026-10-06 02:40:26'),
(73, 8, 'challenge_attempted', 'challenge', 13, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"correct\": true}', '2026-10-06 02:40:26'),
(74, 8, 'evidence_created', 'skill', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 13, \"source_type\": \"challenge\", \"evidence_type\": \"challenge_hard_solved\"}', '2026-10-06 02:40:26'),
(75, 8, 'evidence_created', 'skill', 19, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 13, \"source_type\": \"challenge\", \"evidence_type\": \"challenge_hard_solved\"}', '2026-10-06 02:40:26'),
(76, 8, 'skill_level_changed', 'skill', 19, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"to\": 1, \"from\": 0}', '2026-10-06 02:40:26'),
(77, 8, 'lab_expired', 'lab_instance', 2, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:40:59'),
(78, 8, 'lab_stopped', 'lab_instance', 2, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:41:06'),
(79, 8, 'lab_start_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:41:15'),
(80, 8, 'lab_started', 'lab_instance', 3, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"lab_id\": 2}', '2026-10-06 02:41:15'),
(81, 8, 'lab_ready', 'lab_instance', 3, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:41:15'),
(82, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:41:31'),
(83, 8, 'lab_submit_rl_3', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:41:31'),
(84, 8, 'attempt_submitted', 'lab_task', 8, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"result\": \"incorrect\", \"instance_id\": 3}', '2026-10-06 02:41:31'),
(85, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:41:36'),
(86, 8, 'lab_submit_rl_3', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:41:36'),
(87, 8, 'task_completed', 'lab_task', 8, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 3}', '2026-10-06 02:41:36'),
(88, 8, 'lab_stopped', 'lab_instance', 3, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 02:41:40'),
(89, NULL, 'login_attempt', 'user', NULL, '172.22.0.1', 'curl/8.21.0', '{\"login\": \"admin\"}', '2026-10-06 02:42:03'),
(90, 1, 'login', 'user', 1, '172.22.0.1', 'curl/8.21.0', NULL, '2026-10-06 02:42:03'),
(91, NULL, 'login_attempt', 'user', NULL, '172.22.0.1', 'curl/8.21.0', '{\"login\": \"student1\"}', '2026-10-06 02:42:03'),
(92, 2, 'login', 'user', 2, '172.22.0.1', 'curl/8.21.0', NULL, '2026-10-06 02:42:04'),
(93, 8, 'mentorship_req_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:02:07'),
(94, 8, 'mentorship_requested', 'mentorship', 3, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:02:07'),
(95, 8, 'project_created', 'project', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:04:31'),
(96, 8, 'project_published', 'project', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:04:31'),
(97, 8, 'evidence_created', 'skill', 6, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 5, \"source_type\": \"project\", \"evidence_type\": \"project\"}', '2026-10-06 03:04:31'),
(98, 8, 'evidence_created', 'skill', 12, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 5, \"source_type\": \"project\", \"evidence_type\": \"project\"}', '2026-10-06 03:04:31'),
(99, 8, 'evidence_created', 'skill', 17, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 5, \"source_type\": \"project\", \"evidence_type\": \"project\"}', '2026-10-06 03:04:31'),
(100, 8, 'evidence_created', 'skill', 25, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 5, \"source_type\": \"project\", \"evidence_type\": \"project\"}', '2026-10-06 03:04:31'),
(101, 8, 'writeup_created', 'writeup', 9, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:06:00'),
(102, 8, 'writeup_published', 'writeup', 9, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:06:00'),
(103, 8, 'evidence_created', 'skill', 2, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 9, \"source_type\": \"writeup\", \"evidence_type\": \"writeup\"}', '2026-10-06 03:06:00'),
(104, 8, 'skill_level_changed', 'skill', 2, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"to\": 1, \"from\": 0}', '2026-10-06 03:06:00'),
(105, 8, 'lab_start_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:09:19'),
(106, 8, 'lab_started', 'lab_instance', 4, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"lab_id\": 5}', '2026-10-06 03:09:19'),
(107, 8, 'lab_ready', 'lab_instance', 4, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:09:19'),
(108, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:09:31'),
(109, 8, 'lab_submit_rl_4', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:09:31'),
(110, 8, 'task_completed', 'lab_task', 21, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 4}', '2026-10-06 03:09:31'),
(111, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:09:43'),
(112, 8, 'lab_submit_rl_4', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:09:43'),
(113, 8, 'task_completed', 'lab_task', 22, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 4}', '2026-10-06 03:09:43'),
(114, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:09:53'),
(115, 8, 'lab_submit_rl_4', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:09:53'),
(116, 8, 'task_completed', 'lab_task', 23, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 4}', '2026-10-06 03:09:53'),
(117, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:10:43'),
(118, 8, 'lab_submit_rl_4', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:10:43'),
(119, 8, 'task_completed', 'lab_task', 24, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 4}', '2026-10-06 03:10:43'),
(120, 8, 'evidence_created', 'skill', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 5, \"source_type\": \"lab\", \"evidence_type\": \"lab\"}', '2026-10-06 03:10:43'),
(121, 8, 'skill_level_changed', 'skill', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"to\": 3, \"from\": 2}', '2026-10-06 03:10:43'),
(122, 8, 'evidence_created', 'skill', 13, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 5, \"source_type\": \"lab\", \"evidence_type\": \"lab\"}', '2026-10-06 03:10:43'),
(123, 8, 'skill_level_changed', 'skill', 13, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"to\": 1, \"from\": 0}', '2026-10-06 03:10:43'),
(124, 8, 'lab_completed', 'lab', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"score\": 100, \"instance_id\": 4}', '2026-10-06 03:10:43'),
(125, 8, 'writeup_created', 'writeup', 10, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:10:58'),
(126, 8, 'lab_start_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:25'),
(127, 8, 'lab_started', 'lab_instance', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"lab_id\": 4}', '2026-10-06 03:13:25'),
(128, 8, 'lab_ready', 'lab_instance', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:25'),
(129, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:30'),
(130, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:30'),
(131, 8, 'attempt_submitted', 'lab_task', 16, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"result\": \"incorrect\", \"instance_id\": 5}', '2026-10-06 03:13:30'),
(132, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:33'),
(133, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:33'),
(134, 8, 'attempt_submitted', 'lab_task', 16, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"result\": \"incorrect\", \"instance_id\": 5}', '2026-10-06 03:13:33'),
(135, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:38'),
(136, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:38'),
(137, 8, 'task_completed', 'lab_task', 16, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 5}', '2026-10-06 03:13:38'),
(138, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:45'),
(139, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:45'),
(140, 8, 'task_completed', 'lab_task', 17, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 5}', '2026-10-06 03:13:45'),
(141, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:53'),
(142, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:53'),
(143, 8, 'task_completed', 'lab_task', 18, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 5}', '2026-10-06 03:13:53'),
(144, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:57'),
(145, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:13:57'),
(146, 8, 'task_completed', 'lab_task', 19, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 5}', '2026-10-06 03:13:57'),
(147, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:16:50'),
(148, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:16:50'),
(149, 8, 'attempt_submitted', 'lab_task', 20, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"result\": \"incorrect\", \"instance_id\": 5}', '2026-10-06 03:16:50'),
(150, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:16:57'),
(151, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:16:57'),
(152, 8, 'attempt_submitted', 'lab_task', 20, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"result\": \"incorrect\", \"instance_id\": 5}', '2026-10-06 03:16:57'),
(153, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:17:04'),
(154, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:17:04'),
(155, 8, 'attempt_submitted', 'lab_task', 20, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"result\": \"incorrect\", \"instance_id\": 5}', '2026-10-06 03:17:04'),
(156, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:17:24'),
(157, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:17:24'),
(158, 8, 'attempt_submitted', 'lab_task', 20, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"result\": \"incorrect\", \"instance_id\": 5}', '2026-10-06 03:17:24'),
(159, 8, 'lab_submit_rl', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:17:53'),
(160, 8, 'lab_submit_rl_5', 'rate_limit', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:17:53'),
(161, 8, 'task_completed', 'lab_task', 20, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"instance_id\": 5}', '2026-10-06 03:17:53'),
(162, 8, 'evidence_created', 'skill', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 4, \"source_type\": \"lab\", \"evidence_type\": \"lab\"}', '2026-10-06 03:17:53'),
(163, 8, 'evidence_created', 'skill', 6, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 4, \"source_type\": \"lab\", \"evidence_type\": \"lab\"}', '2026-10-06 03:17:53'),
(164, 8, 'evidence_created', 'skill', 13, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 4, \"source_type\": \"lab\", \"evidence_type\": \"lab\"}', '2026-10-06 03:17:53'),
(165, 8, 'skill_level_changed', 'skill', 13, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"to\": 2, \"from\": 1}', '2026-10-06 03:17:53'),
(166, 8, 'lab_completed', 'lab', 4, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"score\": 100, \"instance_id\": 5}', '2026-10-06 03:17:53'),
(167, 8, 'writeup_created', 'writeup', 11, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:18:00'),
(168, 8, 'writeup_created', 'writeup', 12, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:18:48'),
(169, 8, 'writeup_published', 'writeup', 12, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:18:48'),
(170, 8, 'evidence_created', 'skill', 8, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"source_id\": 12, \"source_type\": \"writeup\", \"evidence_type\": \"writeup\"}', '2026-10-06 03:18:48'),
(171, 8, 'skill_level_changed', 'skill', 8, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"to\": 1, \"from\": 0}', '2026-10-06 03:18:48'),
(172, 1, 'logout', 'user', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-10-06 03:21:42'),
(173, NULL, 'login_attempt', 'user', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"login\": \"mentor1\"}', '2026-10-06 03:22:17'),
(174, 5, 'login', 'user', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-10-06 03:22:17'),
(175, NULL, 'login_attempt', 'user', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"login\": \"mentor1\"}', '2026-10-06 03:22:27'),
(176, 5, 'login', 'user', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-10-06 03:22:27'),
(177, 5, 'mentorship_accepted', 'mentorship', 3, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-10-06 03:23:08'),
(178, 5, 'logout', 'user', 5, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-10-06 03:24:41'),
(179, NULL, 'login_attempt', 'user', NULL, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"login\": \"admin\"}', '2026-10-06 03:25:07'),
(180, 1, 'login', 'user', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-10-06 03:25:07'),
(181, 8, 'vote_created', 'thread', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"vote_type\": \"up\"}', '2026-10-06 03:39:48'),
(182, 8, 'bookmark_created', 'thread', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:39:52'),
(183, 8, 'portfolio_updated', 'user', 8, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', NULL, '2026-10-06 03:46:27'),
(184, 8, 'vote_created', 'thread', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"vote_type\": \"down\"}', '2026-10-06 03:56:48'),
(185, 8, 'vote_created', 'thread', 1, '172.22.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0', '{\"vote_type\": \"up\"}', '2026-10-06 03:56:50');

-- --------------------------------------------------------

--
-- Table structure for table `arena_events`
--

CREATE TABLE `arena_events` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci,
  `event_type` enum('practice','ctf','competition','workshop') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','upcoming','active','ended','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('public','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `arena_events`
--

INSERT INTO `arena_events` (`id`, `name`, `slug`, `description`, `event_type`, `status`, `visibility`, `start_at`, `end_at`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'KKU Cyber Arena Practice', 'kku-cyber-arena-practice', 'Open practice arena for College of Computing students.\n\nWork through beginner-friendly challenges across web, network, crypto, and forensics. No time pressure — learn at your own pace.', 'practice', 'active', 'public', '2026-09-26 16:06:19', '2026-12-02 16:06:19', 7, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(2, 'Introduction to Web Security CTF', 'introduction-to-web-security-ctf', 'A focused CTF event covering OWASP fundamentals.\n\nFour web challenges — SQL injection, session security, XSS, and access control. Perfect for students completing the web security module.', 'ctf', 'upcoming', 'public', '2026-10-17 16:06:19', '2026-10-24 16:06:19', 7, '2026-10-03 16:06:19', '2026-10-03 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `arena_event_challenges`
--

CREATE TABLE `arena_event_challenges` (
  `event_id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `arena_event_challenges`
--

INSERT INTO `arena_event_challenges` (`event_id`, `challenge_id`) VALUES
(1, 1),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(1, 5),
(1, 7),
(1, 8),
(1, 11),
(1, 14),
(1, 17),
(1, 20);

-- --------------------------------------------------------

--
-- Table structure for table `arena_point_transactions`
--

CREATE TABLE `arena_point_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED DEFAULT NULL,
  `event_id` bigint UNSIGNED DEFAULT NULL,
  `hint_id` bigint UNSIGNED DEFAULT NULL,
  `points` int NOT NULL,
  `reason` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `arena_point_transactions`
--

INSERT INTO `arena_point_transactions` (`id`, `user_id`, `challenge_id`, `event_id`, `hint_id`, `points`, `reason`, `created_at`) VALUES
(1, 8, 5, NULL, 8, -10, 'hint_penalty', '2026-10-04 14:39:49'),
(2, 8, 5, NULL, 9, -20, 'hint_penalty', '2026-10-04 14:39:52'),
(3, 8, 5, NULL, 10, -30, 'hint_penalty', '2026-10-04 14:39:53'),
(4, 8, 11, NULL, 16, -10, 'hint_penalty', '2026-10-06 02:28:48'),
(5, 8, 11, NULL, NULL, 60, 'challenge_solved', '2026-10-06 02:30:52'),
(6, 8, 12, NULL, NULL, 160, 'challenge_solved', '2026-10-06 02:31:53'),
(7, 8, 13, NULL, NULL, 280, 'challenge_solved', '2026-10-06 02:40:26');

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookmarks`
--

INSERT INTO `bookmarks` (`id`, `user_id`, `target_type`, `target_id`, `created_at`) VALUES
(1, 2, 'thread', 2, '2026-10-03 16:06:19'),
(2, 2, 'thread', 6, '2026-10-03 16:06:19'),
(3, 8, 'thread', 1, '2026-10-06 03:39:52');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'General', 'general', 'General discussion and introductions', 'chat', 10, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(2, 'Web Security', 'web-security', 'OWASP, XSS, SQLi, CSRF, and web app security', 'globe', 20, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(3, 'Network Security', 'network-security', 'Firewalls, protocols, packet analysis', 'network', 30, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(4, 'Digital Forensics', 'digital-forensics', 'Disk, memory, and evidence analysis', 'search', 40, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(5, 'Malware Analysis', 'malware-analysis', 'Static and dynamic malware analysis', 'bug', 50, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(6, 'Reverse Engineering', 'reverse-engineering', 'Binary analysis and RE techniques', 'cpu', 60, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(7, 'Cryptography', 'cryptography', 'Crypto concepts, attacks, and tooling', 'lock', 70, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(8, 'OSINT', 'osint', 'Open-source intelligence gathering', 'eye', 80, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(9, 'Cloud Security', 'cloud-security', 'Cloud security and infrastructure hardening', 'cloud', 85, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(10, 'Academic', 'academic', 'Courses, assignments, and academic help', 'book', 90, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(11, 'Career', 'career', 'Internships, jobs, and career advice', 'briefcase', 100, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(12, 'CTF', 'ctf', 'Capture The Flag practice and teams', 'flag', 110, 1, '2026-10-03 16:06:19', '2026-10-03 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `challenges`
--

CREATE TABLE `challenges` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `difficulty` enum('easy','medium','hard','expert') COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int UNSIGNED NOT NULL DEFAULT '100',
  `author_id` bigint UNSIGNED NOT NULL,
  `status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `flag_type` enum('static') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'static',
  `flag_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `case_sensitive` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `first_solved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenges`
--

INSERT INTO `challenges` (`id`, `title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `first_solved_at`, `created_at`, `updated_at`, `published_at`) VALUES
(1, 'SQL Injection Basics', 'sql-injection-basics', 'A vulnerable login form concatenates user input directly into a SQL query.\n\nYour task:\n- Identify the injection point in the username field\n- Bypass authentication without valid credentials\n- Extract the hidden flag from the database\n\n**Learning goals:**\n- Understand why string concatenation is dangerous\n- Practice comment-based and boolean-based injection\n- See why prepared statements are mandatory', 1, 'easy', 75, 7, 'published', 'static', 'e22c9e6c5fe354d654197f30eae4e5f4b1a3001ab3d87670726e692cc64b02f4', 1, 1, 1, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(2, 'PHP Session Weakness', 'php-session-weakness', 'This PHP application stores session data in a predictable way and exposes a debug endpoint.\n\nInvestigate how session IDs are generated and whether session fixation or weak entropy allows privilege escalation.\n\nHints in the source comments mention `session.save_path` — start there.', 1, 'medium', 175, 7, 'published', 'static', 'a41e1bf75256da6a0a2fc6de10acf4e03400789165b65f301379efd47ef54170', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(3, 'Cross-Site Scripting Fundamentals', 'cross-site-scripting-fundamentals', 'A student profile page reflects user-supplied bio text without proper encoding.\n\nDemonstrate a reflected/stored XSS payload that executes in the victim browser context and retrieves the flag from a hidden DOM element.\n\nDocument which context (HTML, attribute, JS) you targeted.', 1, 'easy', 100, 7, 'published', 'static', 'fedf478706468e893893fa32508be16feb68166d58d3082f295fa94fb2421730', 1, 1, 1, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(4, 'Broken Access Control', 'broken-access-control', 'An API endpoint checks authentication but fails to verify authorization. Regular users can access admin-only resources by manipulating object IDs.\n\nFind the `/api/report/{id}` endpoint and escalate to retrieve the administrator flag.\n\nThis mirrors OWASP A01 — Broken Access Control.', 1, 'medium', 200, 7, 'published', 'static', '13890752ddd8f097221fd102c2fd98690f7fe493311fa5e386ed0fbe72d0d7d1', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(5, 'Nmap Fundamentals', 'nmap-fundamentals', 'Scan the target lab host and identify the non-standard service running on a high port.\n\nConnect to the service banner to recover the flag.\n\nRecommended workflow:\n1. Host discovery\n2. Port scan (-sV for version detection)\n3. Service interaction', 2, 'easy', 50, 7, 'published', 'static', '2c287587355fbebf4256422d258de2390ded423db8ab1da0038c129c6cc94b66', 1, 1, 1, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(6, 'TCP Packet Investigation', 'tcp-packet-investigation', 'Analyze the provided packet capture focusing on TCP three-way handshake anomalies and out-of-band data.\n\nOne connection carries the flag in an unexpected TCP segment after connection teardown.\n\nTools: Wireshark, tcpdump, or tshark.', 2, 'medium', 150, 7, 'published', 'static', '752d2661572dd7da8a5a02c717822b2fe205fb5ebd71b4c0367f8a8bf46c7951', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(7, 'Wireshark Basics', 'wireshark-basics', 'Open the sample capture and follow the HTTP stream for a login request.\n\nThe flag is embedded in a custom response header sent by the server after a successful POST.\n\nPractice: Statistics → Protocol Hierarchy, Follow → HTTP Stream.', 2, 'easy', 75, 7, 'published', 'static', '385b5f088e487c5b8b85e41930a636781c32d6f6f88b0df382d977895c8c7f0b', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(8, 'Find the Hidden File', 'find-the-hidden-file', 'A disk image contains a deleted file that standard `ls` will not show.\n\nUse forensic carving or inode recovery to restore the artifact containing the flag.\n\nConsider: unallocated space, file slack, and alternate data streams.', 3, 'easy', 80, 7, 'published', 'static', '14778265627973e63fa0b87a9a1dff05d51fbfe836d23909a755fc69afb1a355', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(9, 'Basic File Metadata', 'basic-file-metadata', 'Examine the provided JPEG and PDF files for embedded metadata.\n\nThe flag may appear in EXIF tags, XMP, or document properties.\n\nTry: exiftool, strings, or online metadata viewers for lab practice.', 3, 'easy', 60, 7, 'published', 'static', 'c78ffcc12d58df5c33691e32aa73b28eec82fc23f1205ad4f7410a7808e9f678', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(10, 'PCAP Investigation', 'pcap-investigation', 'A security incident left behind a PCAP file with DNS exfiltration activity.\n\nReconstruct the exfiltrated data from DNS query subdomains and decode the flag.\n\nFocus on unusual query frequency and long subdomain labels.', 3, 'medium', 180, 7, 'published', 'static', '7a15949dcd4f61f24513e0ab71bd0a6641e02578c9caeb5f81ba5777a51c346c', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(11, 'Linux Permissions', 'linux-permissions', 'On the lab VM, a misconfigured SUID binary allows reading a root-only flag file.\n\nEnumerate permissions with `find / -perm -4000 2>/dev/null` and identify the vulnerable binary.\n\nExplain why SUID on interpreters or scripts is dangerous.', 8, 'easy', 70, 7, 'published', 'static', '90306d00e07c615ab9db375ba58b89c229ec89776ce94d659f1907ff33855f8d', 1, 1, 0, '2026-10-06 02:30:52', '2026-10-03 16:06:19', '2026-10-06 02:30:52', '2026-10-03 16:06:19'),
(12, 'Process Investigation', 'process-investigation', 'A compromised Linux host runs an unexpected background process.\n\nUse `ps`, `/proc`, and `lsof` to identify the malicious process and extract the flag from its command-line arguments or open files.', 8, 'medium', 160, 7, 'published', 'static', 'f56aa597035a44c01e7ba640550a091496a7dfd8d52cf567b58a60e62e593550', 1, 1, 0, '2026-10-06 02:31:53', '2026-10-03 16:06:19', '2026-10-06 02:31:53', '2026-10-03 16:06:19'),
(13, 'Find the Suspicious Process', 'find-the-suspicious-process', 'Review the process list snapshot from a DFIR triage script.\n\nOne process masquerades as a system daemon but has an unusual parent PID and network connection.\n\nIdentify it and recover the flag from its environment block.', 8, 'hard', 280, 7, 'published', 'static', '83c5c650261613a4d1f3a659de74b9b4be8c872fa9f931e0ac088ffab1e808ab', 1, 1, 0, '2026-10-06 02:40:26', '2026-10-03 16:06:19', '2026-10-06 02:40:26', '2026-10-03 16:06:19'),
(14, 'Caesar Cipher', 'caesar-cipher', 'Decrypt the ciphertext below to recover the flag.\n\n```\nSYNT{plfskillfunu_fpnfne_014}\n```\n\nThis is a simple substitution cipher with a fixed shift. Try all 26 rotations or use frequency analysis.', 5, 'easy', 50, 7, 'published', 'static', '13c5c8407b682f065929f2c4ddeb620ed512eed36bfc9586e5ee7875253181ac', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(15, 'Base64 Investigation', 'base64-investigation', 'Multiple layers of encoding hide the flag inside a log file.\n\nThe data may be Base64, URL-encoded, or nested. Decode iteratively until you find the `FLAG{...}` format.', 5, 'easy', 65, 7, 'published', 'static', '721582316698ae5c0d7f5991bd4c4c02b1740d1aa8b7c8be54359e816619c6ab', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(16, 'Weak Password Hash', 'weak-password-hash', 'A leaked database contains MD5 password hashes. One hash corresponds to a service account password that reveals the flag when cracked.\n\nHash: `a1b2c3d4e5f6789012345678901234ab`\n\nUse a wordlist attack — the password is a common lab credential.', 5, 'medium', 190, 7, 'published', 'static', 'aa5d9b9d02efb08bb060818cee9e80022044a750f58fb586cb30f3698c59576a', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(17, 'Username Investigation', 'username-investigation', 'A threat actor uses the handle `cyber_kku_2024` across public platforms.\n\nPerform OSINT to find their profile on a code-sharing site and locate the flag in a public gist or repository README.', 4, 'easy', 55, 7, 'published', 'static', '808b5ae225f4bbd35123972c05b6b2791c281a3319d8de0b04bed36cdba54806', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(18, 'Metadata Hunt', 'metadata-hunt', 'A photo posted on social media contains GPS and device metadata linking to a location.\n\nExtract coordinates and convert them to the flag format: `FLAG{lat_lon}` rounded to 4 decimal places.', 4, 'medium', 155, 7, 'published', 'static', 'c8e7c56e03a1126e96e18b3999cdbb1f17ffabad213bbf7c3bac0ab24c82a0c4', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(19, 'Domain Recon Basics', 'domain-recon-basics', 'Perform passive reconnaissance on `lab.cyskillshare.local`.\n\nEnumerate subdomains and check certificate transparency logs. The flag is hidden in a TXT record on a discovered subdomain.', 4, 'medium', 170, 7, 'published', 'static', 'd3032b5d88b67f058c5c703527258bd6284a2c2c42f031d552b5e0a46b815b42', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(20, 'String Hunting', 'string-hunting', 'A stripped ELF binary holds the flag as a plaintext string.\n\nUse `strings`, `rabin2 -z`, or a hex editor to locate readable sequences without full disassembly.', 6, 'easy', 90, 7, 'published', 'static', 'e41a4e5ead81b9d6fef214bff85a3737bd2b208406d00dd1210f6cb7bf6ee1ae', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(21, 'Basic Binary Analysis', 'basic-binary-analysis', 'Load the 32-bit binary in Ghidra or IDA Free.\n\nLocate the `check_password` function and determine the expected input that prints the success message containing the flag.', 6, 'medium', 200, 7, 'published', 'static', 'bdf14bfca00960db4cfaa5590c921ab652c7538a27c1f33bd707d7221f2ce602', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(22, 'Simple Crackme', 'simple-crackme', 'This crackme validates a serial number with a custom algorithm.\n\nPatch or keygen the correct serial to unlock the flag. Dynamic analysis with GDB or x64dbg is encouraged.', 6, 'hard', 300, 7, 'published', 'static', 'f092b1daae2904ac64821198de29407b591bf8b13eba411023f3895e8ce17441', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(23, 'Suspicious Process', 'suspicious-process', 'Analyze the provided memory dump for injected code and suspicious process handles.\n\nIdentify the malware process name and decode the C2 configuration blob to extract the flag.', 7, 'medium', 175, 7, 'published', 'static', '8009679bb6e5ce974854a16757c0a461208c62b4f8f1f73cb88275519b7d864c', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(24, 'Static Analysis Basics', 'static-analysis-basics', 'Inspect the PE file imports, sections, and strings without executing it.\n\nLook for suspicious API imports (VirtualAlloc, WriteProcessMemory) and embedded resources containing the flag.', 7, 'hard', 260, 7, 'published', 'static', '47d6621f79cbcc80c9849a492aeeb5856c9825e14df08521e02e8635e0b64bca', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(25, 'Persistence Investigation', 'persistence-investigation', 'A Windows host shows signs of persistence after a phishing incident.\n\nAnalyze autostart locations (Registry Run keys, Scheduled Tasks, Services) to find the malicious entry and recover the flag from its command-line payload.', 7, 'expert', 400, 7, 'published', 'static', '127ee942c42370384a6dbee5b50663c4f773d94f005b6f80d3933d5183d86249', 1, 1, 0, NULL, '2026-10-03 16:06:19', '2026-10-03 16:06:19', '2026-10-03 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_categories`
--

CREATE TABLE `challenge_categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_categories`
--

INSERT INTO `challenge_categories` (`id`, `name`, `slug`, `description`, `icon`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Web Security', 'web-security', 'OWASP Top 10, web app exploitation, and secure coding', 'globe', 10, 1, '2026-10-03 16:06:19'),
(2, 'Network Security', 'network-security', 'Packet analysis, scanning, and protocol investigation', 'network', 20, 1, '2026-10-03 16:06:19'),
(3, 'Digital Forensics', 'digital-forensics', 'Disk, memory, and artifact analysis', 'search', 30, 1, '2026-10-03 16:06:19'),
(4, 'OSINT', 'osint', 'Open-source intelligence and reconnaissance', 'eye', 40, 1, '2026-10-03 16:06:19'),
(5, 'Cryptography', 'cryptography', 'Classical ciphers, encoding, and password hashing', 'lock', 50, 1, '2026-10-03 16:06:19'),
(6, 'Reverse Engineering', 'reverse-engineering', 'Binary analysis and crackmes', 'cpu', 60, 1, '2026-10-03 16:06:19'),
(7, 'Malware Analysis', 'malware-analysis', 'Static and dynamic malware investigation', 'bug', 70, 1, '2026-10-03 16:06:19'),
(8, 'Linux', 'linux', 'Linux permissions, processes, and system forensics', 'terminal', 80, 1, '2026-10-03 16:06:19'),
(9, 'Windows', 'windows', 'Windows event logs, registry, and DFIR on Windows', 'monitor', 90, 1, '2026-10-03 16:06:19'),
(10, 'Cloud Security', 'cloud-security', 'Cloud misconfigurations and IAM issues', 'cloud', 100, 1, '2026-10-03 16:06:19'),
(11, 'Mobile Security', 'mobile-security', 'Android and iOS security fundamentals', 'smartphone', 110, 1, '2026-10-03 16:06:19'),
(12, 'Miscellaneous', 'miscellaneous', 'General cybersecurity puzzles and mixed topics', 'puzzle', 120, 1, '2026-10-03 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_files`
--

CREATE TABLE `challenge_files` (
  `id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stored_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint UNSIGNED NOT NULL DEFAULT '0',
  `mime_type` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `challenge_hints`
--

CREATE TABLE `challenge_hints` (
  `id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED NOT NULL,
  `hint_order` int UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `point_penalty` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_hints`
--

INSERT INTO `challenge_hints` (`id`, `challenge_id`, `hint_order`, `content`, `point_penalty`, `created_at`) VALUES
(1, 1, 1, 'Try entering a single quote in the username field and observe the error message.', 10, '2026-10-03 16:06:19'),
(2, 1, 2, 'Use comment syntax (-- or #) to ignore the rest of the query after your payload.', 20, '2026-10-03 16:06:19'),
(3, 1, 3, 'A classic bypass: admin\' OR \'1\'=\'1 in the username with any password.', 30, '2026-10-03 16:06:19'),
(4, 2, 1, 'Check whether the application accepts a client-supplied PHPSESSID without regenerating on login.', 10, '2026-10-03 16:06:19'),
(5, 3, 1, 'Inspect how the bio field is rendered — is output encoded for HTML context?', 10, '2026-10-03 16:06:19'),
(6, 3, 2, 'Try a simple payload: <script>alert(1)</script> and check if it executes on profile view.', 20, '2026-10-03 16:06:19'),
(7, 4, 1, 'Increment the report ID in the URL while logged in as a regular student.', 10, '2026-10-03 16:06:19'),
(8, 5, 1, 'Start with a TCP SYN scan: nmap -sS -T4 <target>', 10, '2026-10-03 16:06:19'),
(9, 5, 2, 'Enable version detection with -sV to identify services on non-standard ports.', 20, '2026-10-03 16:06:19'),
(10, 5, 3, 'Look for a service above port 8000 — connect with netcat to read the banner.', 30, '2026-10-03 16:06:19'),
(11, 6, 1, 'Filter for tcp.flags.reset == 1 and examine packets immediately before RST.', 10, '2026-10-03 16:06:19'),
(12, 8, 1, 'Use fls/icat from The Sleuth Kit or photorec to recover deleted inodes.', 10, '2026-10-03 16:06:19'),
(13, 10, 1, 'Filter DNS traffic: dns in Wireshark display filter.', 10, '2026-10-03 16:06:19'),
(14, 10, 2, 'Look for unusually long subdomain labels — they may encode hex or Base64.', 20, '2026-10-03 16:06:19'),
(15, 10, 3, 'Concatenate subdomain labels in query order, then decode from hex.', 30, '2026-10-03 16:06:19'),
(16, 11, 1, 'Search for SUID binaries owned by root: find / -perm -4000 -user root 2>/dev/null', 10, '2026-10-03 16:06:19'),
(17, 12, 1, 'Sort processes by start time: ps aux --sort=start_time', 10, '2026-10-03 16:06:19'),
(18, 12, 2, 'Inspect /proc/<pid>/cmdline and /proc/<pid>/environ for hidden arguments.', 20, '2026-10-03 16:06:19'),
(19, 13, 1, 'Compare process names against known-good baselines — typosquatting is common.', 10, '2026-10-03 16:06:19'),
(20, 13, 2, 'Check PPID — processes spawned from unexpected parents are suspicious.', 20, '2026-10-03 16:06:19'),
(21, 14, 1, 'The cipher shifts each letter by a fixed number of positions in the alphabet.', 10, '2026-10-03 16:06:19'),
(22, 14, 2, 'Try shift 13 first (ROT13) — if that fails, brute-force shifts 1–25.', 20, '2026-10-03 16:06:19'),
(23, 14, 3, 'The shift value is 13 — decode SYNT to FLAG.', 30, '2026-10-03 16:06:19'),
(24, 15, 1, 'Look for strings ending in = or == — classic Base64 padding.', 10, '2026-10-03 16:06:19'),
(25, 16, 1, 'MD5 is fast — use hashcat -m 0 with rockyou.txt or a small custom wordlist.', 10, '2026-10-03 16:06:19'),
(26, 16, 2, 'The password is a common lab default — try variations of \"password\" and \"admin\".', 20, '2026-10-03 16:06:19'),
(27, 18, 1, 'Run exiftool on the image and look for GPSLatitude/GPSLongitude tags.', 10, '2026-10-03 16:06:19'),
(28, 19, 1, 'Use crt.sh or subfinder for passive subdomain enumeration.', 10, '2026-10-03 16:06:19'),
(29, 19, 2, 'Query TXT records on discovered subdomains with dig or nslookup.', 20, '2026-10-03 16:06:19'),
(30, 21, 1, 'Search for strcmp or memcmp calls in the decompiler — follow the branch on success.', 10, '2026-10-03 16:06:19'),
(31, 22, 1, 'Set a breakpoint on the comparison instruction and inspect register values.', 10, '2026-10-03 16:06:19'),
(32, 22, 2, 'The serial validation XORs each character — try reversing the algorithm.', 20, '2026-10-03 16:06:19'),
(33, 23, 1, 'Use Volatility pslist and malfind to locate injected code regions.', 10, '2026-10-03 16:06:19'),
(34, 24, 1, 'Check the .rsrc section for embedded PE resources with Resource Hacker or peview.', 10, '2026-10-03 16:06:19'),
(35, 24, 2, 'Suspicious imports: VirtualAlloc + WriteProcessMemory often indicate shellcode staging.', 20, '2026-10-03 16:06:19'),
(36, 25, 1, 'Export and review Run/RunOnce registry keys from HKCU and HKLM.', 10, '2026-10-03 16:06:19'),
(37, 25, 2, 'Check Scheduled Tasks for hidden tasks with suspicious actions.', 20, '2026-10-03 16:06:19'),
(38, 25, 3, 'The persistence entry uses a Base64-encoded PowerShell command — decode it.', 30, '2026-10-03 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_hint_usage`
--

CREATE TABLE `challenge_hint_usage` (
  `id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED NOT NULL,
  `hint_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `penalty_applied` int UNSIGNED NOT NULL,
  `revealed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_hint_usage`
--

INSERT INTO `challenge_hint_usage` (`id`, `challenge_id`, `hint_id`, `user_id`, `penalty_applied`, `revealed_at`) VALUES
(1, 5, 8, 8, 10, '2026-10-04 14:39:49'),
(2, 5, 9, 8, 20, '2026-10-04 14:39:52'),
(3, 5, 10, 8, 30, '2026-10-04 14:39:53'),
(4, 11, 16, 8, 10, '2026-10-06 02:28:48');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_skills`
--

CREATE TABLE `challenge_skills` (
  `challenge_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_skills`
--

INSERT INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`) VALUES
(1, 6, 0.50),
(1, 7, 1.00),
(2, 6, 0.50),
(2, 10, 1.00),
(3, 6, 0.50),
(3, 8, 1.00),
(4, 6, 0.50),
(4, 10, 1.00),
(5, 3, 0.60),
(5, 22, 1.00),
(6, 3, 0.50),
(6, 11, 1.00),
(7, 3, 0.50),
(7, 11, 0.80),
(8, 16, 1.00),
(9, 16, 0.80),
(10, 16, 0.60),
(10, 18, 1.00),
(11, 1, 1.00),
(11, 5, 0.50),
(12, 1, 0.80),
(12, 5, 0.50),
(13, 1, 0.70),
(13, 19, 0.50),
(14, 4, 0.70),
(15, 4, 0.70),
(16, 4, 0.80),
(16, 24, 0.40),
(17, 21, 1.00),
(18, 21, 0.80),
(19, 21, 0.50),
(19, 23, 0.80),
(20, 20, 1.00),
(21, 20, 1.00),
(22, 20, 1.00),
(23, 19, 1.00),
(24, 19, 1.00),
(25, 19, 0.80);

-- --------------------------------------------------------

--
-- Table structure for table `challenge_solves`
--

CREATE TABLE `challenge_solves` (
  `id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `points_awarded` int UNSIGNED NOT NULL,
  `hints_used` int UNSIGNED NOT NULL DEFAULT '0',
  `solved_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_solves`
--

INSERT INTO `challenge_solves` (`id`, `challenge_id`, `user_id`, `points_awarded`, `hints_used`, `solved_at`) VALUES
(1, 11, 8, 60, 1, '2026-10-06 02:30:52'),
(2, 12, 8, 160, 0, '2026-10-06 02:31:53'),
(3, 13, 8, 280, 0, '2026-10-06 02:40:26');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_submissions`
--

CREATE TABLE `challenge_submissions` (
  `id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `submitted_flag_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `points_awarded` int NOT NULL DEFAULT '0',
  `attempt_number` int UNSIGNED NOT NULL DEFAULT '1',
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_submissions`
--

INSERT INTO `challenge_submissions` (`id`, `challenge_id`, `user_id`, `submitted_flag_hash`, `is_correct`, `points_awarded`, `attempt_number`, `submitted_at`) VALUES
(1, 11, 8, '68191031b1df2f68c3d8fcbf4962c7bc4bdcab71b10ba82725ff3e1038bd8139', 0, 0, 1, '2026-10-06 02:29:18'),
(2, 11, 8, '054ce8864c80b9b5e875481c9eb9f0a5f186c813bbe80a0bf978ec1da6c2d1c2', 0, 0, 2, '2026-10-06 02:29:32'),
(3, 11, 8, '90306d00e07c615ab9db375ba58b89c229ec89776ce94d659f1907ff33855f8d', 1, 60, 3, '2026-10-06 02:30:52'),
(4, 12, 8, 'f56aa597035a44c01e7ba640550a091496a7dfd8d52cf567b58a60e62e593550', 1, 160, 1, '2026-10-06 02:31:53'),
(5, 13, 8, '83c5c650261613a4d1f3a659de74b9b4be8c872fa9f931e0ac088ffab1e808ab', 1, 280, 1, '2026-10-06 02:40:26');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_tags`
--

CREATE TABLE `challenge_tags` (
  `challenge_id` bigint UNSIGNED NOT NULL,
  `tag_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `challenge_tags`
--

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`) VALUES
(3, 1),
(1, 2),
(11, 4),
(12, 4),
(13, 4),
(6, 5),
(7, 5),
(2, 7),
(23, 9),
(24, 9),
(25, 9),
(8, 10),
(9, 10),
(10, 10),
(12, 10),
(13, 10),
(18, 10),
(23, 10),
(25, 10),
(5, 11),
(6, 11),
(7, 11),
(10, 11),
(19, 11),
(5, 12),
(25, 13),
(14, 14),
(16, 15),
(1, 17),
(2, 17),
(3, 17),
(4, 17),
(20, 18),
(21, 18),
(22, 18),
(24, 18),
(1, 19),
(2, 20),
(4, 20),
(6, 21),
(10, 22),
(14, 23),
(15, 23),
(16, 23),
(9, 24),
(17, 24),
(18, 24),
(19, 24);

-- --------------------------------------------------------

--
-- Table structure for table `channels`
--

CREATE TABLE `channels` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `channel_type` enum('text','announcement','help') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `channels`
--

INSERT INTO `channels` (`id`, `category_id`, `name`, `slug`, `description`, `channel_type`, `is_private`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, '#general', 'general', 'General community chat', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(2, 1, '#introductions', 'introductions', 'Introduce yourself to the community', 'text', 0, 1, 20, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(3, 2, '#web-security', 'web-security', 'Web application security discussions', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(4, 3, '#network-security', 'network-security', 'Network security discussions', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(5, 4, '#digital-forensics', 'digital-forensics', 'Digital forensics discussions', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(6, 5, '#malware-analysis', 'malware-analysis', 'Malware analysis discussions', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(7, 6, '#reverse-engineering', 'reverse-engineering', 'Reverse engineering discussions', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(8, 7, '#cryptography', 'cryptography', 'Cryptography discussions', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(9, 8, '#osint', 'osint', 'OSINT discussions', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(10, 9, '#cloud-security', 'cloud-security', 'Cloud security discussions', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(11, 10, '#assignment-help', 'assignment-help', 'Ask for assignment guidance (no cheating)', 'help', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(12, 10, '#project-help', 'project-help', 'Project collaboration and help', 'help', 0, 1, 20, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(13, 12, '#ctf-general', 'ctf-general', 'CTF discussion and team finding', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19'),
(14, 11, '#internships', 'internships', 'Internship and career opportunities', 'text', 0, 1, 10, '2026-10-03 16:06:19', '2026-10-03 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `collab_groups`
--

CREATE TABLE `collab_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_type` enum('study','ctf','project','course','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'study',
  `owner_id` bigint UNSIGNED NOT NULL,
  `visibility` enum('public','community','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'community',
  `join_policy` enum('open','approval','invite_only') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approval',
  `max_members` int UNSIGNED NOT NULL DEFAULT '20',
  `status` enum('active','archived','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `specializations` json DEFAULT NULL,
  `channel_id` int UNSIGNED DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collab_groups`
--

INSERT INTO `collab_groups` (`id`, `name`, `slug`, `description`, `group_type`, `owner_id`, `visibility`, `join_policy`, `max_members`, `status`, `specializations`, `channel_id`, `featured`, `created_at`, `updated_at`) VALUES
(1, 'Web Security Study Group', 'web-security-study-group', 'Weekly practice on SQLi, XSS, and access control. Beginner-friendly.', 'study', 2, 'community', 'open', 20, 'active', '[\"web\"]', NULL, 0, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(2, 'Linux & Networking Study Group', 'linux-networking-study-group', 'Hands-on Linux permissions, processes, and network fundamentals.', 'study', 3, 'community', 'approval', 20, 'active', NULL, NULL, 0, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(3, 'Digital Forensics Club', 'digital-forensics-club', 'Artifact analysis practice and case discussions.', 'study', 5, 'community', 'approval', 20, 'active', '[\"forensics\"]', NULL, 0, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(4, 'Malware Analysis Study Group', 'malware-analysis-study-group', 'Static analysis notes and safe lab discussions (no live malware hosting).', 'study', 7, 'community', 'invite_only', 20, 'active', '[\"malware\"]', NULL, 0, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(5, 'OSINT Beginners', 'osint-beginners', 'Open-source intelligence fundamentals and ethical practice.', 'study', 4, 'community', 'open', 20, 'active', '[\"osint\"]', NULL, 0, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(6, 'Packet Pirates', 'packet-pirates', 'University CTF practice team. Looking for crypto and reverse help.', 'ctf', 2, 'community', 'approval', 20, 'active', '[\"web\", \"network\", \"forensics\"]', NULL, 0, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(7, 'Open Source SOC Dashboard', 'open-source-soc-dashboard', 'Build a lightweight SOC dashboard for campus lab telemetry. Looking for backend, frontend, and threat intel contributors.', 'project', 3, 'community', 'approval', 12, 'active', NULL, NULL, 0, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(8, 'XSS Practice Circle', 'xss-practice-circle', 'Small group for XSS labs and writeups.', 'study', 3, 'community', 'open', 20, 'active', NULL, NULL, 0, '2026-10-03 16:07:36', '2026-10-03 16:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `collab_group_activities`
--

CREATE TABLE `collab_group_activities` (
  `id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `activity_type` enum('study','challenge','lab','discussion','project','ctf','review') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'study',
  `related_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int UNSIGNED DEFAULT NULL,
  `meeting_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `status` enum('scheduled','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collab_group_activities`
--

INSERT INTO `collab_group_activities` (`id`, `group_id`, `title`, `description`, `activity_type`, `related_type`, `related_id`, `scheduled_at`, `duration_minutes`, `meeting_link`, `created_by`, `status`, `created_at`) VALUES
(1, 1, 'SQL Injection Study Session', 'Walk through parameterized queries and lab tasks.', 'lab', 'lab', NULL, '2026-10-06 16:06:21', 60, NULL, 2, 'scheduled', '2026-10-03 16:06:21');

-- --------------------------------------------------------

--
-- Table structure for table `collab_group_applications`
--

CREATE TABLE `collab_group_applications` (
  `id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','accepted','rejected','withdrawn') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collab_group_goals`
--

CREATE TABLE `collab_group_goals` (
  `id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `target_date` date DEFAULT NULL,
  `status` enum('active','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `progress` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collab_group_goals`
--

INSERT INTO `collab_group_goals` (`id`, `group_id`, `title`, `description`, `target_date`, `status`, `progress`, `created_by`, `created_at`, `completed_at`) VALUES
(1, 1, 'Complete Web Security Lab Series', 'Finish published web labs together this month.', NULL, 'active', 40, 2, '2026-10-03 16:06:21', NULL),
(2, 6, 'Prepare for university CTF', 'Practice Arena challenges across Web and Forensics.', NULL, 'active', 55, 2, '2026-10-03 16:06:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `collab_group_invitations`
--

CREATE TABLE `collab_group_invitations` (
  `id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `inviter_id` bigint UNSIGNED NOT NULL,
  `invitee_id` bigint UNSIGNED NOT NULL,
  `token` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','accepted','declined','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collab_group_join_requests`
--

CREATE TABLE `collab_group_join_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collab_group_members`
--

CREATE TABLE `collab_group_members` (
  `id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` enum('owner','admin','moderator','mentor','captain','co_captain','member') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `status` enum('active','invited','left','removed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_active_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collab_group_members`
--

INSERT INTO `collab_group_members` (`id`, `group_id`, `user_id`, `role`, `status`, `joined_at`, `last_active_at`) VALUES
(1, 1, 2, 'owner', 'active', '2026-10-03 16:06:21', NULL),
(2, 2, 3, 'owner', 'active', '2026-10-03 16:06:21', NULL),
(3, 3, 5, 'owner', 'active', '2026-10-03 16:06:21', NULL),
(4, 4, 7, 'owner', 'active', '2026-10-03 16:06:21', NULL),
(5, 5, 4, 'owner', 'active', '2026-10-03 16:06:21', NULL),
(6, 6, 2, 'owner', 'active', '2026-10-03 16:06:21', NULL),
(7, 6, 3, 'member', 'active', '2026-10-03 16:06:21', NULL),
(8, 6, 4, 'member', 'active', '2026-10-03 16:06:21', NULL),
(9, 6, 5, 'co_captain', 'active', '2026-10-03 16:06:21', NULL),
(10, 1, 3, 'member', 'active', '2026-10-03 16:06:21', NULL),
(11, 1, 5, 'mentor', 'active', '2026-10-03 16:06:21', NULL),
(12, 3, 2, 'member', 'active', '2026-10-03 16:06:21', NULL),
(13, 3, 4, 'member', 'active', '2026-10-03 16:06:21', NULL),
(14, 7, 3, 'owner', 'active', '2026-10-03 16:06:21', NULL),
(15, 5, 3, 'member', 'active', '2026-10-03 16:07:18', NULL),
(16, 8, 3, 'owner', 'active', '2026-10-03 16:07:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `collab_group_resources`
--

CREATE TABLE `collab_group_resources` (
  `id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `resource_type` enum('link','writeup','lab','challenge','knowledge','note','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'link',
  `url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collab_group_resources`
--

INSERT INTO `collab_group_resources` (`id`, `group_id`, `title`, `description`, `resource_type`, `url`, `related_type`, `related_id`, `created_by`, `created_at`) VALUES
(1, 1, 'SQL Injection writeup', 'Related community writeup', 'writeup', NULL, 'writeup', NULL, 2, '2026-10-03 16:06:21');

-- --------------------------------------------------------

--
-- Table structure for table `collab_group_roles`
--

CREATE TABLE `collab_group_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `required_count` int UNSIGNED NOT NULL DEFAULT '1',
  `filled_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collab_group_roles`
--

INSERT INTO `collab_group_roles` (`id`, `group_id`, `title`, `description`, `required_count`, `filled_count`, `created_at`) VALUES
(1, 7, 'Backend Developer', 'APIs and data pipelines', 1, 0, '2026-10-03 16:06:21'),
(2, 7, 'Security Analyst', 'Detection rules and dashboards', 1, 0, '2026-10-03 16:06:21'),
(3, 7, 'Frontend Developer', 'Dashboard UI', 1, 0, '2026-10-03 16:06:21');

-- --------------------------------------------------------

--
-- Table structure for table `collab_group_skills`
--

CREATE TABLE `collab_group_skills` (
  `group_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collab_group_skills`
--

INSERT INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`, `created_at`) VALUES
(1, 6, 1.00, '2026-10-03 16:06:21'),
(1, 7, 1.00, '2026-10-03 16:06:21'),
(1, 8, 1.00, '2026-10-03 16:06:21'),
(2, 1, 1.00, '2026-10-03 16:06:21'),
(2, 3, 1.00, '2026-10-03 16:06:21'),
(2, 11, 1.00, '2026-10-03 16:06:21'),
(3, 13, 1.00, '2026-10-03 16:06:21'),
(3, 16, 1.00, '2026-10-03 16:06:21'),
(4, 19, 1.00, '2026-10-03 16:06:21'),
(4, 20, 1.00, '2026-10-03 16:06:21'),
(5, 21, 1.00, '2026-10-03 16:06:21'),
(6, 6, 1.00, '2026-10-03 16:06:21'),
(6, 11, 1.00, '2026-10-03 16:06:21'),
(6, 16, 1.00, '2026-10-03 16:06:21'),
(7, 6, 1.00, '2026-10-03 16:06:21'),
(7, 11, 1.00, '2026-10-03 16:06:21'),
(7, 14, 1.00, '2026-10-03 16:06:21'),
(8, 8, 1.00, '2026-10-03 16:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `content_reactions`
--

CREATE TABLE `content_reactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `content_type` enum('writeup','knowledge_article') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_id` bigint UNSIGNED NOT NULL,
  `reaction_type` enum('helpful','clear','practical') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_articles`
--

CREATE TABLE `knowledge_articles` (
  `id` bigint UNSIGNED NOT NULL,
  `author_id` bigint UNSIGNED NOT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `difficulty` enum('beginner','intermediate','advanced','expert') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beginner',
  `status` enum('draft','published','under_review','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('public','community','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_official` tinyint(1) NOT NULL DEFAULT '0',
  `version` int UNSIGNED NOT NULL DEFAULT '1',
  `view_count` int UNSIGNED NOT NULL DEFAULT '0',
  `helpful_count` int UNSIGNED NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_articles`
--

INSERT INTO `knowledge_articles` (`id`, `author_id`, `category_id`, `title`, `slug`, `summary`, `content`, `difficulty`, `status`, `visibility`, `featured`, `is_official`, `version`, `view_count`, `helpful_count`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 7, 1, 'What is SQL Injection?', 'what-is-sql-injection', 'A clear introduction to SQL injection, impact, and prevention with prepared statements.', '## Definition\n\nSQL injection occurs when untrusted input is concatenated into a SQL statement, allowing attackers to alter query logic.\n\n## Classic example\n\n```php\n$query = \"SELECT * FROM users WHERE username = \'\" . $_POST[\"user\"] . \"\' AND password = \'\" . $_POST[\"pass\"] . \"\'\";\n$result = mysqli_query($conn, $query);\n```\n\nAn attacker may supply:\n\n```sql\nadmin\'-- -\n```\n\n## Impact\n\n- Authentication bypass\n- Data exfiltration\n- Data modification or deletion\n\n## Prevention\n\nUse **prepared statements** with bound parameters. Validate input as a secondary layer, not the primary defense.\n\nSee also: student writeup *SQL Injection Beyond the Basics* and OWASP guidance.', 'beginner', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 7, 1, 'Understanding CSRF Tokens', 'understanding-csrf-tokens', 'How cross-site request forgery works and how anti-CSRF tokens protect state-changing requests.', '## The problem\n\nBrowsers automatically attach session cookies. A malicious site can trick a logged-in user into submitting a form to your application.\n\n## Token pattern\n\n1. Server generates a random token stored in the session\n2. Token is embedded in forms or headers\n3. Server rejects state-changing requests without a valid token\n\n```php\n$_SESSION[\"csrf_token\"] = bin2hex(random_bytes(32));\n// In form: hidden input named csrf_token\n```\n\n## Verification\n\nCompare the submitted token to the session value using a timing-safe comparison (`hash_equals` in PHP).\n\n## Complementary controls\n\nSameSite cookies and verifying the Origin/Referer headers add defense in depth.', 'beginner', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 7, 8, 'Linux File Permissions', 'linux-file-permissions', 'Read, write, execute bits, ownership, and common privilege pitfalls.', '## Basics\n\nLinux permissions use owner, group, and other classes with read (r), write (w), and execute (x) bits.\n\n```bash\nls -l /etc/passwd\nchmod 640 secret.conf\nchown root:admins secret.conf\n```\n\n## Special bits\n\nSetuid, setgid, and sticky bit change execution and deletion semantics. Misconfigured setuid binaries are a common privilege-escalation path.\n\n## Practical tip\n\nPrefer least privilege. Avoid world-writable directories on multi-user systems.', 'beginner', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 7, 2, 'TCP Three-Way Handshake', 'tcp-three-way-handshake', 'SYN, SYN-ACK, ACK and why handshake anomalies matter during incident triage.', '## Steps\n\n1. Client → Server: **SYN** (seq = x)\n2. Server → Client: **SYN-ACK** (seq = y, ack = x+1)\n3. Client → Server: **ACK** (ack = y+1)\n\n## Wireshark filter\n\n```bash\ntshark -r capture.pcap -Y \"tcp.flags.syn==1 || tcp.flags.ack==1\"\n```\n\n## Why it matters for security\n\nSYN floods abuse step 1. Half-open connections and unexpected RST packets may indicate scanning or firewall interference.', 'beginner', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 7, 3, 'Introduction to Digital Forensics', 'introduction-to-digital-forensics', 'Core forensic process: identification, preservation, analysis, and reporting.', '## Process overview\n\n1. Identify relevant evidence sources\n2. Preserve integrity (write blockers, hashing)\n3. Analyze with documented methods\n4. Report findings clearly\n\n## Chain of custody\n\nRecord who handled evidence, when, and why. Hash values before and after acquisition.\n\n## Lessons for students\n\nReproducibility matters as much as clever analysis. Always document tools and versions.', 'beginner', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(6, 7, 4, 'Understanding PE Files', 'understanding-pe-files', 'Structure of Windows Portable Executable files and what analysts look for first.', '## PE structure\n\nDOS header, PE signature, COFF header, optional header, section table, and sections (`.text`, `.data`, `.rdata`, …).\n\n## First-pass checks\n\n- Unusual section names or entropy (packing)\n- Suspicious imports (VirtualAlloc, WriteProcessMemory, URLDownloadToFile)\n- Resource anomalies\n\n## Safety\n\nTreat every sample as hostile. Analyze offline. Do not host live malware on the learning platform.', 'intermediate', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(7, 7, 1, 'What is XSS?', 'what-is-xss', 'Cross-site scripting types, impact, and encoding defenses.', '## Definition\n\nXSS lets an attacker run script in another user’s browser within the origin of a vulnerable site.\n\n## Types\n\n- Reflected\n- Stored\n- DOM-based\n\n## Defense\n\nContext-aware output encoding, CSP, and avoiding unsafe sinks such as `innerHTML` with untrusted data.', 'beginner', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(8, 5, 2, 'Introduction to Network Reconnaissance', 'introduction-to-network-reconnaissance', 'Ethical recon concepts: passive vs active techniques and documentation habits.', '## Passive vs active\n\nPassive recon uses public sources. Active recon probes targets directly and must stay within authorization scope.\n\n## Common tools (authorized labs only)\n\n```bash\nnmap -sV -T3 target.lab\n```\n\n## Documentation\n\nRecord scope, timestamps, and commands. Recon notes often become writeups and evidence.', 'beginner', 'published', 'public', 0, 0, 1, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(9, 7, 1, 'SQL Injection คืออะไร? (ฉบับภาษาไทย)', 'what-is-sql-injection-th', 'แนะนำ SQL Injection แบบสั้น เข้าใจง่าย สำหรับนักศึกษาปีต้น', '## ความหมาย\n\nSQL Injection เกิดเมื่อนำค่าจากผู้ใช้ไปต่อเข้า SQL โดยตรง ทำให้ผู้โจมตีเปลี่ยนตรรกะของคิวรีได้\n\n## ตัวอย่างที่ไม่ปลอดภัย\n\n```php\n$query = \"SELECT * FROM users WHERE username = \'\" . $_POST[\"user\"] . \"\'\";\n```\n\n## ผลกระทบ\n\n- ข้ามการล็อกอิน\n- ขโมยข้อมูล\n- แก้หรือลบข้อมูล\n\n## วิธีป้องกัน\n\nใช้ **Prepared Statements** (เช่น PDO `prepare` + `bindValue`/`execute`) และอย่าโชว์ SQL error ให้ผู้ใช้ทั่วไป\n\nอ่านเพิ่ม: writeup *บันทึกแล็บ: SQL Injection ฉบับมือใหม่*', 'beginner', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-06 02:48:05', '2026-10-06 02:48:05', '2026-10-06 02:48:05'),
(10, 7, 1, 'CSRF Token ทำงานอย่างไร?', 'csrf-token-th', 'อธิบาย CSRF และการใช้ Synchronizer Token แบบที่ใช่ใน CySkillShare', '## ปัญหา\n\nเบราว์เซอร์ส่ง session cookie ให้อัตโนมัติ เว็บไม่ดีอาจหลอกให้ผู้ใช้ที่ล็อกอินอยู่ส่งฟอร์มไปยังเว็บเรา\n\n## วิธีแก้ในระบบนี้\n\n1. สร้างโทเคนสุ่มด้วย `random_bytes(32)` เก็บใน session (`_csrf_token`)\n2. ฝังในฟอร์มเป็น `_csrf` ผ่าน `csrf_field()`\n3. ตรวจด้วย `hash_equals()` ถ้าไม่ตรง → HTTP 419\n\n## ข้อควรจำ\n\n- ใช้คู่กับ SameSite cookie\n- Logout แล้วควรหมุนโทเคนใหม่\n- อย่าพึ่ง Origin header อย่างเดียว', 'beginner', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-06 02:48:05', '2026-10-06 02:48:05', '2026-10-06 02:48:05'),
(11, 7, 8, 'สิทธิ์ไฟล์บน Linux เบื้องต้น', 'linux-file-permissions-th', 'อ่าน/เขียน/รัน, เจ้าของกลุ่ม และการตั้งค่าที่พลาดบ่อย', '## พื้นฐาน\n\n```bash\nls -l\nchmod 640 secret.conf\nchown student:student secret.conf\n```\n\n- **600** — เหมาะกับไฟล์ลับของเจ้าของ\n- **644** — ไฟล์ที่คนอื่นอ่านได้\n- **755** — โฟลเดอร์/สคริปต์ที่ต้องรันได้\n\n## ข้อควรระวัง\n\nโฟลเดอร์ world-writable บนเครื่องใช้ร่วมกันเสี่ยงมาก ในแล็บ Linux ให้หา path ที่ตั้งผิดแล้วรายงาน\n\n## ทิปสั้นๆ\n\nใช้สิทธิ์น้อยที่สุดที่ยังทำงานได้ (least privilege)', 'beginner', 'published', 'public', 0, 1, 1, 0, 0, '2026-10-06 02:48:05', '2026-10-06 02:48:05', '2026-10-06 02:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_article_contributors`
--

CREATE TABLE `knowledge_article_contributors` (
  `article_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` enum('author','contributor','reviewer','editor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contributor',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_article_reviews`
--

CREATE TABLE `knowledge_article_reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `article_id` bigint UNSIGNED NOT NULL,
  `reviewer_id` bigint UNSIGNED NOT NULL,
  `status` enum('pending','approved','changes_requested','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `review_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_article_skills`
--

CREATE TABLE `knowledge_article_skills` (
  `article_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_article_skills`
--

INSERT INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`, `created_at`) VALUES
(1, 6, 1.00, '2026-10-03 16:06:20'),
(1, 7, 1.00, '2026-10-03 16:06:20'),
(2, 6, 1.00, '2026-10-03 16:06:20'),
(3, 1, 1.00, '2026-10-03 16:06:20'),
(4, 11, 1.00, '2026-10-03 16:06:20'),
(5, 16, 1.00, '2026-10-03 16:06:20'),
(6, 19, 1.00, '2026-10-03 16:06:20'),
(7, 6, 1.00, '2026-10-03 16:06:20'),
(8, 11, 1.00, '2026-10-03 16:06:20'),
(9, 7, 1.00, '2026-10-06 02:48:05'),
(10, 6, 1.00, '2026-10-06 02:48:05'),
(11, 1, 1.00, '2026-10-06 02:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_article_sources`
--

CREATE TABLE `knowledge_article_sources` (
  `id` bigint UNSIGNED NOT NULL,
  `article_id` bigint UNSIGNED NOT NULL,
  `source_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_article_sources`
--

INSERT INTO `knowledge_article_sources` (`id`, `article_id`, `source_type`, `source_id`, `description`, `external_title`, `external_url`, `created_at`) VALUES
(1, 1, 'external_reference', NULL, 'OWASP SQL Injection', NULL, NULL, '2026-10-03 16:06:20'),
(2, 1, 'writeup', 1, 'Student writeup: SQL Injection Beyond the Basics', NULL, NULL, '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_article_versions`
--

CREATE TABLE `knowledge_article_versions` (
  `id` bigint UNSIGNED NOT NULL,
  `article_id` bigint UNSIGNED NOT NULL,
  `version` int UNSIGNED NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `edited_by` bigint UNSIGNED NOT NULL,
  `change_summary` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_article_versions`
--

INSERT INTO `knowledge_article_versions` (`id`, `article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`, `created_at`) VALUES
(1, 1, 1, '## Definition\n\nSQL injection occurs when untrusted input is concatenated into a SQL statement, allowing attackers to alter query logic.\n\n## Classic example\n\n```php\n$query = \"SELECT * FROM users WHERE username = \'\" . $_POST[\"user\"] . \"\' AND password = \'\" . $_POST[\"pass\"] . \"\'\";\n$result = mysqli_query($conn, $query);\n```\n\nAn attacker may supply:\n\n```sql\nadmin\'-- -\n```\n\n## Impact\n\n- Authentication bypass\n- Data exfiltration\n- Data modification or deletion\n\n## Prevention\n\nUse **prepared statements** with bound parameters. Validate input as a secondary layer, not the primary defense.\n\nSee also: student writeup *SQL Injection Beyond the Basics* and OWASP guidance.', 'What is SQL Injection?', 7, 'Initial published version', '2026-10-03 16:06:20'),
(2, 2, 1, '## The problem\n\nBrowsers automatically attach session cookies. A malicious site can trick a logged-in user into submitting a form to your application.\n\n## Token pattern\n\n1. Server generates a random token stored in the session\n2. Token is embedded in forms or headers\n3. Server rejects state-changing requests without a valid token\n\n```php\n$_SESSION[\"csrf_token\"] = bin2hex(random_bytes(32));\n// In form: hidden input named csrf_token\n```\n\n## Verification\n\nCompare the submitted token to the session value using a timing-safe comparison (`hash_equals` in PHP).\n\n## Complementary controls\n\nSameSite cookies and verifying the Origin/Referer headers add defense in depth.', 'Understanding CSRF Tokens', 7, 'Initial published version', '2026-10-03 16:06:20'),
(3, 3, 1, '## Basics\n\nLinux permissions use owner, group, and other classes with read (r), write (w), and execute (x) bits.\n\n```bash\nls -l /etc/passwd\nchmod 640 secret.conf\nchown root:admins secret.conf\n```\n\n## Special bits\n\nSetuid, setgid, and sticky bit change execution and deletion semantics. Misconfigured setuid binaries are a common privilege-escalation path.\n\n## Practical tip\n\nPrefer least privilege. Avoid world-writable directories on multi-user systems.', 'Linux File Permissions', 7, 'Initial published version', '2026-10-03 16:06:20'),
(4, 4, 1, '## Steps\n\n1. Client → Server: **SYN** (seq = x)\n2. Server → Client: **SYN-ACK** (seq = y, ack = x+1)\n3. Client → Server: **ACK** (ack = y+1)\n\n## Wireshark filter\n\n```bash\ntshark -r capture.pcap -Y \"tcp.flags.syn==1 || tcp.flags.ack==1\"\n```\n\n## Why it matters for security\n\nSYN floods abuse step 1. Half-open connections and unexpected RST packets may indicate scanning or firewall interference.', 'TCP Three-Way Handshake', 7, 'Initial published version', '2026-10-03 16:06:20'),
(5, 5, 1, '## Process overview\n\n1. Identify relevant evidence sources\n2. Preserve integrity (write blockers, hashing)\n3. Analyze with documented methods\n4. Report findings clearly\n\n## Chain of custody\n\nRecord who handled evidence, when, and why. Hash values before and after acquisition.\n\n## Lessons for students\n\nReproducibility matters as much as clever analysis. Always document tools and versions.', 'Introduction to Digital Forensics', 7, 'Initial published version', '2026-10-03 16:06:20'),
(6, 6, 1, '## PE structure\n\nDOS header, PE signature, COFF header, optional header, section table, and sections (`.text`, `.data`, `.rdata`, …).\n\n## First-pass checks\n\n- Unusual section names or entropy (packing)\n- Suspicious imports (VirtualAlloc, WriteProcessMemory, URLDownloadToFile)\n- Resource anomalies\n\n## Safety\n\nTreat every sample as hostile. Analyze offline. Do not host live malware on the learning platform.', 'Understanding PE Files', 7, 'Initial published version', '2026-10-03 16:06:20'),
(7, 7, 1, '## Definition\n\nXSS lets an attacker run script in another user’s browser within the origin of a vulnerable site.\n\n## Types\n\n- Reflected\n- Stored\n- DOM-based\n\n## Defense\n\nContext-aware output encoding, CSP, and avoiding unsafe sinks such as `innerHTML` with untrusted data.', 'What is XSS?', 7, 'Initial published version', '2026-10-03 16:06:20'),
(8, 8, 1, '## Passive vs active\n\nPassive recon uses public sources. Active recon probes targets directly and must stay within authorization scope.\n\n## Common tools (authorized labs only)\n\n```bash\nnmap -sV -T3 target.lab\n```\n\n## Documentation\n\nRecord scope, timestamps, and commands. Recon notes often become writeups and evidence.', 'Introduction to Network Reconnaissance', 5, 'Initial published version', '2026-10-03 16:06:20'),
(9, 9, 1, '## ความหมาย\n\nSQL Injection เกิดเมื่อนำค่าจากผู้ใช้ไปต่อเข้า SQL โดยตรง ทำให้ผู้โจมตีเปลี่ยนตรรกะของคิวรีได้\n\n## ตัวอย่างที่ไม่ปลอดภัย\n\n```php\n$query = \"SELECT * FROM users WHERE username = \'\" . $_POST[\"user\"] . \"\'\";\n```\n\n## ผลกระทบ\n\n- ข้ามการล็อกอิน\n- ขโมยข้อมูล\n- แก้หรือลบข้อมูล\n\n## วิธีป้องกัน\n\nใช้ **Prepared Statements** (เช่น PDO `prepare` + `bindValue`/`execute`) และอย่าโชว์ SQL error ให้ผู้ใช้ทั่วไป\n\nอ่านเพิ่ม: writeup *บันทึกแล็บ: SQL Injection ฉบับมือใหม่*', 'SQL Injection คืออะไร? (ฉบับภาษาไทย)', 7, 'เวอร์ชันภาษาไทยเริ่มต้น', '2026-10-06 02:48:05'),
(10, 10, 1, '## ปัญหา\n\nเบราว์เซอร์ส่ง session cookie ให้อัตโนมัติ เว็บไม่ดีอาจหลอกให้ผู้ใช้ที่ล็อกอินอยู่ส่งฟอร์มไปยังเว็บเรา\n\n## วิธีแก้ในระบบนี้\n\n1. สร้างโทเคนสุ่มด้วย `random_bytes(32)` เก็บใน session (`_csrf_token`)\n2. ฝังในฟอร์มเป็น `_csrf` ผ่าน `csrf_field()`\n3. ตรวจด้วย `hash_equals()` ถ้าไม่ตรง → HTTP 419\n\n## ข้อควรจำ\n\n- ใช้คู่กับ SameSite cookie\n- Logout แล้วควรหมุนโทเคนใหม่\n- อย่าพึ่ง Origin header อย่างเดียว', 'CSRF Token ทำงานอย่างไร?', 7, 'เวอร์ชันภาษาไทยเริ่มต้น', '2026-10-06 02:48:05'),
(11, 11, 1, '## พื้นฐาน\n\n```bash\nls -l\nchmod 640 secret.conf\nchown student:student secret.conf\n```\n\n- **600** — เหมาะกับไฟล์ลับของเจ้าของ\n- **644** — ไฟล์ที่คนอื่นอ่านได้\n- **755** — โฟลเดอร์/สคริปต์ที่ต้องรันได้\n\n## ข้อควรระวัง\n\nโฟลเดอร์ world-writable บนเครื่องใช้ร่วมกันเสี่ยงมาก ในแล็บ Linux ให้หา path ที่ตั้งผิดแล้วรายงาน\n\n## ทิปสั้นๆ\n\nใช้สิทธิ์น้อยที่สุดที่ยังทำงานได้ (least privilege)', 'สิทธิ์ไฟล์บน Linux เบื้องต้น', 7, 'เวอร์ชันภาษาไทยเริ่มต้น', '2026-10-06 02:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

CREATE TABLE `labs` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `learning_objectives` mediumtext COLLATE utf8mb4_unicode_ci,
  `category_id` int UNSIGNED DEFAULT NULL,
  `template_id` int UNSIGNED DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced','expert') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beginner',
  `estimated_minutes` int UNSIGNED NOT NULL DEFAULT '45',
  `status` enum('draft','review','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('public','community','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `author_id` bigint UNSIGNED NOT NULL,
  `version` int UNSIGNED NOT NULL DEFAULT '1',
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `allow_pause` tinyint(1) NOT NULL DEFAULT '0',
  `reset_task_progress` tinyint(1) NOT NULL DEFAULT '1',
  `prerequisite_mode` enum('recommended','required') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recommended',
  `lifetime_minutes` int UNSIGNED NOT NULL DEFAULT '60',
  `cpu_limit` decimal(4,2) NOT NULL DEFAULT '1.00',
  `memory_mb` int UNSIGNED NOT NULL DEFAULT '512',
  `disk_mb` int UNSIGNED NOT NULL DEFAULT '1024',
  `allow_internet` tinyint(1) NOT NULL DEFAULT '0',
  `max_points` int UNSIGNED NOT NULL DEFAULT '100',
  `environment_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'browser',
  `view_count` int UNSIGNED NOT NULL DEFAULT '0',
  `start_count` int UNSIGNED NOT NULL DEFAULT '0',
  `completion_count` int UNSIGNED NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `labs`
--

INSERT INTO `labs` (`id`, `title`, `slug`, `short_description`, `description`, `learning_objectives`, `category_id`, `template_id`, `difficulty`, `estimated_minutes`, `status`, `visibility`, `author_id`, `version`, `thumbnail`, `featured`, `allow_pause`, `reset_task_progress`, `prerequisite_mode`, `lifetime_minutes`, `cpu_limit`, `memory_mb`, `disk_mb`, `allow_internet`, `max_points`, `environment_type`, `view_count`, `start_count`, `completion_count`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'เว็บแอปพลิเคชันที่มีช่องโหว่ (Vulnerable Web App)', 'vulnerable-web-application', 'สำรวจเว็บแล็บจำลอง: หา SQLi วิเคราะห์ล็อก และเสนอวิธีแก้แบบปลอดภัย', '## สถานการณ์\n\nคุณกำลังประเมินเว็บแอปชมรมในวิทยาเขต สภาพแวดล้อมนี้เป็น**จำลอง**ใน CySkillShare ไม่แตะระบบจริงของมหาวิทยาลัย\n\n## เป้าหมาย\n\nทำ reconnaissance หา endpoint ที่มีช่องโหว่ ยืนยันการโจมตีอย่างปลอดภัยในแล็บ วิเคราะห์ access log และเสนอ remediation\n\n## สภาพแวดล้อม\n\nเปิดแผง **Lab Environment** หลังเริ่มแล็บ จะมีค่าเฉพาะอินสแตนซ์ (IP, token) แสดงที่นั่น', '- ระบุ HTTP request ที่น่าสงสัย\n- หา SQL injection อย่างปลอดภัยในแล็บ\n- วิเคราะห์ access log ของเว็บเซิร์ฟเวอร์\n- แนะนำ remediation ด้วย parameterized query', 1, 1, 'intermediate', 45, 'published', 'public', 7, 1, NULL, 1, 0, 1, 'recommended', 60, 1.00, 512, 1024, 0, 100, 'browser', 0, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-06 02:48:05'),
(2, 'ทราฟฟิกเครือข่ายที่น่าสงสัย', 'suspicious-network-traffic', 'วิเคราะห์ทราฟฟิกจำลอง หา IOC และโดเมน C2 ในแล็บ Network', '## สถานการณ์\n\nทีม SOC ได้รับ PCAP/ล็อกจากเหตุการณ์เครือข่าย ภารกิจของคุณคือหาโฮสต์ที่น่าสงสัยและโดเมนที่เกี่ยวข้อง\n\nค่าในแล็บถูกสุ่มต่ออินสแตนซ์ — ดูแผง Lab Environment', '- Identify a suspicious host from traffic summaries\n- Recognize the protocol used for C2-like communication\n- Extract a simple IOC\n- Document findings', 2, 3, 'beginner', 35, 'published', 'public', 7, 1, NULL, 1, 0, 1, 'recommended', 60, 1.00, 512, 1024, 0, 100, 'browser', 0, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-06 02:48:05'),
(3, 'Compromised Workstation', 'compromised-workstation', 'Static forensics triage: timeline, persistence, and suspicious file identification using read-only artifacts.', '## Scenario\n\nYou receive hashed, read-only artifact summaries from a compromised workstation. Do **not** execute any binaries — analyze the provided metadata only.', '- Build a simple timeline from artifact timestamps\n- Identify a persistence mechanism\n- Locate a suspicious filename\n- Write concise findings', 3, 2, 'intermediate', 50, 'published', 'public', 7, 1, NULL, 0, 0, 1, 'recommended', 60, 1.00, 512, 1024, 0, 100, 'browser', 0, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 'Web Server Compromise', 'web-server-compromise', 'Incident response lab: analyze logs, determine initial access, impact, and produce a timeline.', '## Scenario\n\nA club web server shows signs of compromise. Use the simulated IR console to analyze logs and reconstruct the attack path.', '- Identify initial access from logs\n- Trace attacker activity\n- Determine impact scope\n- Produce a short incident timeline', 10, 4, 'advanced', 60, 'published', 'public', 7, 1, NULL, 1, 0, 1, 'recommended', 60, 1.00, 512, 1024, 0, 100, 'browser', 0, 1, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-06 03:17:53'),
(5, 'สืบสวนความปลอดภัยบน Linux', 'linux-security-investigation', 'ตรวจสอบโฮสต์ Linux จำลอง: process, ล็อก auth, สิทธิ์ไฟล์ และ persistence', '## สถานการณ์\n\nจัมป์โฮสต์ Linux ในแล็บมีพฤติกรรมผิดปกติ ใช้คอนโซลจำลองเท่านั้น — ไม่มีสิทธิ์เข้าเซิร์ฟเวอร์จริงของมหาวิทยาลัย\n\n## สิ่งที่ต้องทำ\n\n- ไล่ process ที่ผิดปกติ\n- อ่าน auth log หาบัญชีที่ถูก brute-force\n- หา path ที่ world-writable\n- ส่ง completion flag', '- อ่าน process list และ /proc\n- วิเคราะห์ failed SSH login\n- ประเมินสิทธิ์ไฟล์/โฟลเดอร์\n- สรุปหาหลักฐานเพื่อส่งคำตอบ', 7, 5, 'intermediate', 40, 'published', 'public', 7, 1, NULL, 0, 0, 1, 'recommended', 60, 1.00, 512, 1024, 0, 100, 'browser', 0, 2, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-06 03:10:43');

-- --------------------------------------------------------

--
-- Table structure for table `lab_attempts`
--

CREATE TABLE `lab_attempts` (
  `id` bigint UNSIGNED NOT NULL,
  `progress_id` bigint UNSIGNED NOT NULL,
  `answer_hash` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result` enum('correct','incorrect','partial','manual_review') COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int UNSIGNED NOT NULL DEFAULT '0',
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_attempts`
--

INSERT INTO `lab_attempts` (`id`, `progress_id`, `answer_hash`, `result`, `score`, `submitted_at`) VALUES
(1, 12, 'fda7c1d8f5c8b1b9ea5567c55bab9f99872d20cd028b8370aa60fad25bc74e88', 'incorrect', 0, '2026-10-06 02:41:31'),
(2, 12, 'f4361e3238f7c018c9bd46b3e39d745bfb2eb709658e4f81a21a6eb4fbaf4bc0', 'correct', 20, '2026-10-06 02:41:36'),
(3, 16, '912d4aa44451016d243b501aef8151bb990364b4cc7c0314bfbf8c85c59b76ee', 'correct', 25, '2026-10-06 03:09:31'),
(4, 17, '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'correct', 25, '2026-10-06 03:09:43'),
(5, 18, '86d9b7d20ff2d460969af1b303b9bc536ac4f197124ad2312c823ab93761420c', 'correct', 25, '2026-10-06 03:09:53'),
(6, 19, '59a08034accafe97a34b09ff138870a322f0d60b723a10168571b3fa3521f9c3', 'correct', 25, '2026-10-06 03:10:43'),
(7, 20, 'd4c3e8a11256ab82a4fc72560eb4a2b0e87bad820c290dd9b03616de240aa6db', 'incorrect', 0, '2026-10-06 03:13:30'),
(8, 20, '81f4496c4a42bf27c5c0c294d6b3d6fdac57b09b451c7626fe9f0f4a8421cdfe', 'incorrect', 0, '2026-10-06 03:13:33'),
(9, 20, '0fa499aba8c8ffbe5e3a783601595f78853e54d5dcc46fcbfae275b904e29992', 'correct', 20, '2026-10-06 03:13:38'),
(10, 21, 'cb24bced32c7007516db67547a886101ce3c680afe8ee7e8a3b043b3c5bff4fa', 'correct', 20, '2026-10-06 03:13:45'),
(11, 22, '81f4496c4a42bf27c5c0c294d6b3d6fdac57b09b451c7626fe9f0f4a8421cdfe', 'correct', 20, '2026-10-06 03:13:53'),
(12, 23, '9400f1b21cb527d7fa3d3eabba93557a18ebe7a2ca4e471cfe5e4c5b4ca7f767', 'correct', 15, '2026-10-06 03:13:57'),
(13, 24, 'd5adb315b711c664970f24ad53df14deda10b19f842acc744a061ecb9e9742e7', 'incorrect', 0, '2026-10-06 03:16:50'),
(14, 24, '5bdebdac9783cb49f545771e1f1fcf7279e3e3c5284c6994ecde016dd5e7c90a', 'incorrect', 0, '2026-10-06 03:16:57'),
(15, 24, 'db054c881335f9251e5bfbedf5e03b419202337f8f55863f393d7ad2466d125a', 'incorrect', 0, '2026-10-06 03:17:04'),
(16, 24, '29d8c2ebb179e24fd962967be136d4414529e2899c4691e6b8f8f2faaf01f63b', 'incorrect', 0, '2026-10-06 03:17:24'),
(17, 24, '332a2662e381bcf817fbcc19c14ad2feda854d72c76f6c9da65e4a1a915a4ec8', 'correct', 25, '2026-10-06 03:17:53');

-- --------------------------------------------------------

--
-- Table structure for table `lab_categories`
--

CREATE TABLE `lab_categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_categories`
--

INSERT INTO `lab_categories` (`id`, `name`, `slug`, `description`, `icon`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Web Security', 'web-security', 'Hands-on web application security labs', NULL, 10, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 'Network Security', 'network-security', 'Traffic analysis and network investigation', NULL, 20, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 'Digital Forensics', 'digital-forensics', 'Artifact analysis without executing untrusted code', NULL, 30, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 'Malware Analysis', 'malware-analysis', 'Safe static-analysis oriented labs', NULL, 40, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 'Reverse Engineering', 'reverse-engineering', 'Binary analysis practice', NULL, 50, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(6, 'OSINT', 'osint', 'Open-source intelligence exercises', NULL, 60, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(7, 'Linux', 'linux', 'Linux security investigation labs', NULL, 70, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(8, 'Windows', 'windows', 'Windows security labs', NULL, 80, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(9, 'Cloud Security', 'cloud-security', 'Cloud security practice', NULL, 90, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(10, 'Incident Response', 'incident-response', 'IR investigation labs', NULL, 100, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(11, 'Secure Coding', 'secure-coding', 'Secure development labs', NULL, 110, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(12, 'Cryptography', 'cryptography', 'Applied crypto labs', NULL, 120, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `lab_completions`
--

CREATE TABLE `lab_completions` (
  `id` bigint UNSIGNED NOT NULL,
  `lab_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `instance_id` bigint UNSIGNED NOT NULL,
  `score` int UNSIGNED DEFAULT NULL,
  `required_completed` int UNSIGNED NOT NULL DEFAULT '0',
  `required_total` int UNSIGNED NOT NULL DEFAULT '0',
  `optional_completed` int UNSIGNED NOT NULL DEFAULT '0',
  `hints_used` int UNSIGNED NOT NULL DEFAULT '0',
  `show_on_portfolio` tinyint(1) NOT NULL DEFAULT '1',
  `completed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_completions`
--

INSERT INTO `lab_completions` (`id`, `lab_id`, `user_id`, `instance_id`, `score`, `required_completed`, `required_total`, `optional_completed`, `hints_used`, `show_on_portfolio`, `completed_at`) VALUES
(1, 5, 8, 4, 100, 4, 4, 0, 0, 1, '2026-10-06 03:10:43'),
(2, 4, 8, 5, 100, 5, 5, 0, 0, 1, '2026-10-06 03:17:53');

-- --------------------------------------------------------

--
-- Table structure for table `lab_feedback`
--

CREATE TABLE `lab_feedback` (
  `id` bigint UNSIGNED NOT NULL,
  `lab_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `difficulty_rating` enum('too_easy','appropriate','too_hard') COLLATE utf8mb4_unicode_ci NOT NULL,
  `clarity_rating` enum('poor','okay','good') COLLATE utf8mb4_unicode_ci NOT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_hint_usage`
--

CREATE TABLE `lab_hint_usage` (
  `id` bigint UNSIGNED NOT NULL,
  `instance_id` bigint UNSIGNED NOT NULL,
  `hint_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `penalty_applied` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_instances`
--

CREATE TABLE `lab_instances` (
  `id` bigint UNSIGNED NOT NULL,
  `lab_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `status` enum('queued','provisioning','running','paused','stopping','stopped','expired','failed','destroyed','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `provision_state` enum('queued','provisioning','ready','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `instance_identifier` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `runtime_secrets` json DEFAULT NULL,
  `access_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orchestrator_ref` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `failure_category` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `ready_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `paused_at` timestamp NULL DEFAULT NULL,
  `stopped_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `score` int UNSIGNED DEFAULT NULL,
  `hints_used` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_instances`
--

INSERT INTO `lab_instances` (`id`, `lab_id`, `user_id`, `status`, `provision_state`, `instance_identifier`, `runtime_secrets`, `access_token`, `orchestrator_ref`, `failure_category`, `started_at`, `ready_at`, `last_activity_at`, `expires_at`, `paused_at`, `stopped_at`, `completed_at`, `score`, `hints_used`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 'running', 'ready', '920b0abeaa24fadf6667fb774408c342', '{\"flag\": \"CSK{vulnerable-web-application_ca64865e}\", \"target_ip\": \"10.10.1.38\", \"attacker_ip\": \"10.10.2.38\"}', '252c12fcb2be92e79c1c934667ef3da36a3308a465bc7c2f', 'sim-920b0abeaa24fadf6667fb774408c342', NULL, '2026-10-04 14:35:36', '2026-10-04 14:35:36', '2026-10-04 14:35:36', '2026-10-04 15:35:36', NULL, NULL, NULL, NULL, 0, '2026-10-04 14:35:36', '2026-10-04 14:35:36'),
(2, 5, 8, 'stopped', 'ready', '0d862824af74b8060f837e1859d63985', '{\"flag\": \"CSK{linux-security-investigation_ed231489}\", \"bad_process\": \"kworker-9bbb\", \"world_writable\": \"/opt/lab/shared\", \"ssh_target_user\": \"admin\"}', 'ffd2a5a57b477751f5a4fe44927d861790ec7f77363b0ca3', 'sim-0d862824af74b8060f837e1859d63985', NULL, '2026-10-04 14:40:16', '2026-10-04 14:40:16', '2026-10-04 14:40:17', '2026-10-04 15:40:16', NULL, '2026-10-06 02:41:06', NULL, NULL, 0, '2026-10-04 14:40:16', '2026-10-06 02:41:06'),
(3, 2, 8, 'stopped', 'ready', 'd26bdf9b2df9d5147041dfcc32d1d495', '{\"flag\": \"CSK{suspicious-network-traffic_fa015434}\", \"ioc_domain\": \"update-5f42.lab-c2.invalid\", \"suspect_host\": \"10.10.3.41\"}', 'dc5dbc351df25092681af7c826e417e816056be263eb0d0a', 'sim-d26bdf9b2df9d5147041dfcc32d1d495', NULL, '2026-10-06 02:41:15', '2026-10-06 02:41:15', '2026-10-06 02:41:36', '2026-10-06 03:41:15', NULL, '2026-10-06 02:41:40', NULL, NULL, 0, '2026-10-06 02:41:15', '2026-10-06 02:41:40'),
(4, 5, 8, 'completed', 'ready', 'bd64b167c186f169b42ce71269ae5b27', '{\"flag\": \"CSK{linux-security-investigation_5ceafebd}\", \"bad_process\": \"kworker-b31a\", \"world_writable\": \"/opt/lab/shared\", \"ssh_target_user\": \"admin\"}', '2a617eeeb581664faf6571613afbc28468a0518b52f46cec', 'sim-bd64b167c186f169b42ce71269ae5b27', NULL, '2026-10-06 03:09:19', '2026-10-06 03:09:19', '2026-10-06 03:10:43', '2026-10-06 04:09:19', NULL, NULL, '2026-10-06 03:10:43', 100, 0, '2026-10-06 03:09:19', '2026-10-06 03:10:43'),
(5, 4, 8, 'completed', 'ready', '3ad624a1cd02d0cc9265cd21f2850c29', '{\"flag\": \"CSK{web-server-compromise_6857ccb0}\", \"access_hour\": \"19\", \"webshell_path\": \"/var/www/html/uploads/c1a7b6.php\", \"compromised_user\": \"www-data\"}', '4702474c761009afe5bfeba651c17c82f7bcd5bcad735e55', 'sim-3ad624a1cd02d0cc9265cd21f2850c29', NULL, '2026-10-06 03:13:25', '2026-10-06 03:13:25', '2026-10-06 03:17:53', '2026-10-06 04:13:25', NULL, NULL, '2026-10-06 03:17:53', 100, 0, '2026-10-06 03:13:25', '2026-10-06 03:17:53'),
(6, 3, 8, 'running', 'ready', '9ea5646447731df054fdd7239b246497', '{\"flag\": \"CSK{compromised-workstation_930db399}\", \"malware_name\": \"svchost-f6f2.exe\", \"first_seen_hour\": \"12\"}', 'af780a32a9107bea6337c48916b388b00a1ff18a3f11e699', NULL, NULL, '2026-10-06 03:14:58', NULL, '2026-10-06 03:14:58', '2026-10-06 05:14:58', NULL, NULL, NULL, NULL, 0, '2026-10-06 03:14:58', '2026-10-06 03:14:58'),
(7, 2, 8, 'running', 'ready', '12328f681b903eb545f0262637a60458', '{\"flag\": \"CSK{suspicious-network-traffic_6724f715}\", \"ioc_domain\": \"update-b163.lab-c2.invalid\", \"suspect_host\": \"10.10.3.37\"}', 'b1032e8318bf52dc4a62dcd1e90951a055c84ac01edf1a43', NULL, NULL, '2026-10-06 03:14:58', NULL, '2026-10-06 03:14:58', '2026-10-06 05:14:58', NULL, NULL, NULL, NULL, 0, '2026-10-06 03:14:58', '2026-10-06 03:14:58');

-- --------------------------------------------------------

--
-- Table structure for table `lab_prerequisites`
--

CREATE TABLE `lab_prerequisites` (
  `id` bigint UNSIGNED NOT NULL,
  `lab_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `minimum_level` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_prerequisites`
--

INSERT INTO `lab_prerequisites` (`id`, `lab_id`, `skill_id`, `minimum_level`, `created_at`) VALUES
(1, 1, 6, 1, '2026-10-03 16:06:20'),
(2, 2, 11, 1, '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `lab_progress`
--

CREATE TABLE `lab_progress` (
  `id` bigint UNSIGNED NOT NULL,
  `instance_id` bigint UNSIGNED NOT NULL,
  `task_id` bigint UNSIGNED NOT NULL,
  `status` enum('locked','available','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'locked',
  `attempts` int UNSIGNED NOT NULL DEFAULT '0',
  `best_score` int UNSIGNED NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_progress`
--

INSERT INTO `lab_progress` (`id`, `instance_id`, `task_id`, `status`, `attempts`, `best_score`, `completed_at`, `updated_at`) VALUES
(1, 1, 1, 'available', 0, 0, NULL, '2026-10-04 14:35:36'),
(2, 1, 2, 'locked', 0, 0, NULL, '2026-10-04 14:35:36'),
(3, 1, 3, 'locked', 0, 0, NULL, '2026-10-04 14:35:36'),
(4, 1, 4, 'locked', 0, 0, NULL, '2026-10-04 14:35:36'),
(5, 1, 5, 'locked', 0, 0, NULL, '2026-10-04 14:35:36'),
(6, 1, 6, 'locked', 0, 0, NULL, '2026-10-04 14:35:36'),
(7, 1, 7, 'available', 0, 0, NULL, '2026-10-04 14:35:36'),
(8, 2, 21, 'available', 0, 0, NULL, '2026-10-04 14:40:16'),
(9, 2, 22, 'locked', 0, 0, NULL, '2026-10-04 14:40:16'),
(10, 2, 23, 'locked', 0, 0, NULL, '2026-10-04 14:40:16'),
(11, 2, 24, 'locked', 0, 0, NULL, '2026-10-04 14:40:16'),
(12, 3, 8, 'completed', 2, 20, '2026-10-06 02:41:36', '2026-10-06 02:41:36'),
(13, 3, 9, 'available', 0, 0, NULL, '2026-10-06 02:41:36'),
(14, 3, 10, 'locked', 0, 0, NULL, '2026-10-06 02:41:15'),
(15, 3, 11, 'locked', 0, 0, NULL, '2026-10-06 02:41:15'),
(16, 4, 21, 'completed', 1, 25, '2026-10-06 03:09:31', '2026-10-06 03:09:31'),
(17, 4, 22, 'completed', 1, 25, '2026-10-06 03:09:43', '2026-10-06 03:09:43'),
(18, 4, 23, 'completed', 1, 25, '2026-10-06 03:09:53', '2026-10-06 03:09:53'),
(19, 4, 24, 'completed', 1, 25, '2026-10-06 03:10:43', '2026-10-06 03:10:43'),
(20, 5, 16, 'completed', 3, 20, '2026-10-06 03:13:38', '2026-10-06 03:13:38'),
(21, 5, 17, 'completed', 1, 20, '2026-10-06 03:13:45', '2026-10-06 03:13:45'),
(22, 5, 18, 'completed', 1, 20, '2026-10-06 03:13:53', '2026-10-06 03:13:53'),
(23, 5, 19, 'completed', 1, 15, '2026-10-06 03:13:57', '2026-10-06 03:13:57'),
(24, 5, 20, 'completed', 5, 25, '2026-10-06 03:17:53', '2026-10-06 03:17:53');

-- --------------------------------------------------------

--
-- Table structure for table `lab_services`
--

CREATE TABLE `lab_services` (
  `id` bigint UNSIGNED NOT NULL,
  `lab_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `internal_port` int UNSIGNED DEFAULT NULL,
  `display_port` int UNSIGNED DEFAULT NULL,
  `protocol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'http',
  `environment_config` json DEFAULT NULL,
  `is_student_visible` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_services`
--

INSERT INTO `lab_services` (`id`, `lab_id`, `name`, `service_type`, `template`, `internal_port`, `display_port`, `protocol`, `environment_config`, `is_student_visible`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Web Target', 'web', NULL, 80, 80, 'http', NULL, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 1, 'Access Logs', 'logs', NULL, 8081, 8081, 'http', NULL, 1, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 2, 'PCAP Summary', 'pcap_view', NULL, 80, 80, 'http', NULL, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 3, 'Artifact Browser', 'forensics', NULL, 80, 80, 'http', NULL, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 4, 'IR Console', 'ir_console', NULL, 80, 80, 'http', NULL, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(6, 5, 'Host Console', 'linux_console', NULL, 80, 80, 'http', NULL, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `lab_skills`
--

CREATE TABLE `lab_skills` (
  `lab_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_skills`
--

INSERT INTO `lab_skills` (`lab_id`, `skill_id`, `weight`, `created_at`) VALUES
(1, 6, 1.00, '2026-10-03 16:06:20'),
(1, 7, 1.20, '2026-10-03 16:06:20'),
(1, 24, 0.80, '2026-10-03 16:06:20'),
(2, 11, 1.00, '2026-10-03 16:06:20'),
(3, 13, 0.80, '2026-10-03 16:06:20'),
(3, 16, 1.20, '2026-10-03 16:06:20'),
(4, 1, 0.70, '2026-10-03 16:06:20'),
(4, 6, 0.80, '2026-10-03 16:06:20'),
(4, 13, 1.20, '2026-10-03 16:06:20'),
(5, 1, 1.20, '2026-10-03 16:06:20'),
(5, 13, 0.60, '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `lab_tasks`
--

CREATE TABLE `lab_tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `lab_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_type` enum('question','flag','command_output','multiple_choice','file_analysis','log_analysis','configuration','investigation','report','manual_verification') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'question',
  `display_order` int NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '1',
  `points` int UNSIGNED NOT NULL DEFAULT '10',
  `options_json` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_tasks`
--

INSERT INTO `lab_tasks` (`id`, `lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`, `options_json`, `created_at`, `updated_at`) VALUES
(1, 1, 'ระบุ IP เป้าหมาย', 'identify-target-ip', 'เปิด Lab Environment IP ภายในของเป้าหมายที่ได้รับคืออะไร?', 'investigation', 1, 1, 10, NULL, '2026-10-03 16:06:20', '2026-10-06 02:48:05'),
(2, 1, 'Find the login endpoint', 'find-login-endpoint', 'Which path handles authentication on the target application?', 'multiple_choice', 2, 1, 10, '[{\"id\": \"a\", \"label\": \"/admin\"}, {\"id\": \"b\", \"label\": \"/login\"}, {\"id\": \"c\", \"label\": \"/api/users\"}, {\"id\": \"d\", \"label\": \"/dashboard\"}]', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 1, 'Identify the injectable parameter', 'spot-sqli-param', 'Which request parameter is vulnerable to SQL injection on the login form?', 'question', 3, 1, 15, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 1, 'Retrieve the lab flag', 'capture-flag', 'After confirming the vulnerability in the lab environment, submit the lab flag shown on the success panel.', 'flag', 4, 1, 25, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 1, 'Identify the attacker IP in logs', 'log-source-ip', 'In the Access Logs view, which source IP generated the suspicious login attempts?', 'log_analysis', 5, 1, 15, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(6, 1, 'Recommend remediation', 'remediation', 'What is the primary remediation for this class of vulnerability? Answer with the two-word phrase used in the environment notes (lowercase).', 'question', 6, 1, 15, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(7, 1, 'Optional: short incident note', 'optional-report', 'In one sentence, summarize initial access. This task is optional and uses manual review.', 'report', 7, 0, 10, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(8, 2, 'Identify the suspicious host', 'suspect-host', 'Which internal IP generates unusual outbound connections?', 'investigation', 1, 1, 20, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(9, 2, 'Identify the protocol', 'protocol', 'What protocol carries the suspicious payload? (lowercase abbreviation)', 'question', 2, 1, 20, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(10, 2, 'Extract the IOC domain', 'ioc', 'Submit the suspicious domain observed in the capture summary.', 'investigation', 3, 1, 30, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(11, 2, 'Submit investigation flag', 'flag', 'Submit the investigation completion flag from the environment.', 'flag', 4, 1, 30, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(12, 3, 'Identify persistence', 'persist-mech', 'Which persistence mechanism was used? Answer exactly as shown in the artifact notes (lowercase with hyphen).', 'file_analysis', 1, 1, 25, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(13, 3, 'Find the suspicious filename', 'bad-file', 'What is the suspicious executable filename?', 'investigation', 2, 1, 25, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(14, 3, 'First-seen hour (UTC)', 'first-seen', 'According to the timeline, in which UTC hour (0-23 as two digits) did the suspicious file first appear?', 'question', 3, 1, 20, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(15, 3, 'Forensics flag', 'flag', 'Submit the completion flag from the environment.', 'flag', 4, 1, 30, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(16, 4, 'Initial access vector', 'initial-access', 'What was the initial access vector? (two words, lowercase)', 'log_analysis', 1, 1, 20, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(17, 4, 'Locate the webshell path', 'webshell-path', 'Submit the webshell path shown in the IR console.', 'investigation', 2, 1, 20, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(18, 4, 'Compromised account', 'impact-user', 'Which account was compromised?', 'investigation', 3, 1, 20, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(19, 4, 'Compromise hour (UTC)', 'timeline-hour', 'In which UTC hour (two digits) did initial access occur?', 'question', 4, 1, 15, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(20, 4, 'IR completion flag', 'flag', 'Submit the IR lab flag.', 'flag', 5, 1, 25, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(21, 5, 'ชื่อ process ที่น่าสงสัย', 'bad-process', 'ในรายการ process มีชื่อใดที่ดูเป็นมัลแวร์/ผิดปกติ?', 'investigation', 1, 1, 25, NULL, '2026-10-03 16:06:20', '2026-10-06 02:48:05'),
(22, 5, 'บัญชีที่ถูก brute-force', 'auth-user', 'ชื่อผู้ใช้ใดปรากฏใน failed SSH บ่อยที่สุด?', 'log_analysis', 2, 1, 25, NULL, '2026-10-03 16:06:20', '2026-10-06 02:48:05'),
(23, 5, 'พาธที่ world-writable', 'perm-path', 'พาธใดถูกตั้งสิทธิ์ world-writable อย่างไม่ถูกต้อง?', 'configuration', 3, 1, 25, NULL, '2026-10-03 16:06:20', '2026-10-06 02:48:05'),
(24, 5, 'แฟล็กจบแล็บ Linux', 'flag', 'ส่ง completion flag ของอินสแตนซ์นี้', 'flag', 4, 1, 25, NULL, '2026-10-03 16:06:20', '2026-10-06 02:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `lab_task_dependencies`
--

CREATE TABLE `lab_task_dependencies` (
  `id` bigint UNSIGNED NOT NULL,
  `task_id` bigint UNSIGNED NOT NULL,
  `depends_on_task_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_task_dependencies`
--

INSERT INTO `lab_task_dependencies` (`id`, `task_id`, `depends_on_task_id`, `created_at`) VALUES
(1, 2, 1, '2026-10-03 16:06:20'),
(2, 3, 2, '2026-10-03 16:06:20'),
(3, 4, 3, '2026-10-03 16:06:20'),
(4, 5, 4, '2026-10-03 16:06:20'),
(5, 6, 5, '2026-10-03 16:06:20'),
(6, 9, 8, '2026-10-03 16:06:20'),
(7, 10, 9, '2026-10-03 16:06:20'),
(8, 11, 10, '2026-10-03 16:06:20'),
(9, 13, 12, '2026-10-03 16:06:20'),
(10, 14, 13, '2026-10-03 16:06:20'),
(11, 15, 14, '2026-10-03 16:06:20'),
(12, 17, 16, '2026-10-03 16:06:20'),
(13, 18, 17, '2026-10-03 16:06:20'),
(14, 19, 18, '2026-10-03 16:06:20'),
(15, 20, 19, '2026-10-03 16:06:20'),
(16, 22, 21, '2026-10-03 16:06:20'),
(17, 23, 22, '2026-10-03 16:06:20'),
(18, 24, 23, '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `lab_task_hints`
--

CREATE TABLE `lab_task_hints` (
  `id` bigint UNSIGNED NOT NULL,
  `task_id` bigint UNSIGNED NOT NULL,
  `title` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hint_level` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `penalty` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_task_hints`
--

INSERT INTO `lab_task_hints` (`id`, `task_id`, `title`, `content`, `hint_level`, `penalty`, `created_at`) VALUES
(1, 1, 'Where to look', 'Check the Environment panel header for Target IP.', 1, 3, '2026-10-03 16:06:20'),
(2, 3, 'Form fields', 'Look at the login form field names in the simulated target.', 1, 5, '2026-10-03 16:06:20'),
(3, 5, 'Log filter', 'Look for repeated 401/200 patterns around /login.', 1, 5, '2026-10-03 16:06:20'),
(4, 9, 'Look at ports', 'Watch for queries that do not look like normal name resolution.', 1, 5, '2026-10-03 16:06:20'),
(5, 12, 'Registry', 'Check autorun-related artifact categories.', 1, 5, '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `lab_task_validations`
--

CREATE TABLE `lab_task_validations` (
  `id` bigint UNSIGNED NOT NULL,
  `task_id` bigint UNSIGNED NOT NULL,
  `validation_type` enum('exact','regex','flag','instance_secret','multiple_choice','manual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `validation_config` json NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_task_validations`
--

INSERT INTO `lab_task_validations` (`id`, `task_id`, `validation_type`, `validation_config`, `created_at`, `updated_at`) VALUES
(1, 1, 'instance_secret', '{\"secret_key\": \"target_ip\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 2, 'multiple_choice', '{\"correct_option_ids\": [\"b\"]}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 3, 'exact', '{\"value_hash\": \"16f78a7d6317f102bbd95fc9a4f3ff2e3249287690b8bdad6b7810f82b34ace3\", \"case_sensitive\": false}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 4, 'flag', '{\"secret_key\": \"flag\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 5, 'instance_secret', '{\"secret_key\": \"attacker_ip\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(6, 6, 'exact', '{\"value_hash\": \"fae60eefb2da839840d670d73305614527299eee1270a8500e30eaedf5ad7036\", \"case_sensitive\": false}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(7, 7, 'manual', '{}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(8, 8, 'instance_secret', '{\"secret_key\": \"suspect_host\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(9, 9, 'exact', '{\"value_hash\": \"dd75a9d6fb309c4399fe425cd5f90ff95eba135d6924fb91766ee5d3726b168a\", \"case_sensitive\": false}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(10, 10, 'instance_secret', '{\"secret_key\": \"ioc_domain\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(11, 11, 'flag', '{\"secret_key\": \"flag\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(12, 12, 'exact', '{\"value_hash\": \"eba32383c39522370d98102be229e9a944526073ebd7b1e5a925498a5e4607ca\", \"case_sensitive\": false}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(13, 13, 'instance_secret', '{\"secret_key\": \"malware_name\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(14, 14, 'instance_secret', '{\"secret_key\": \"first_seen_hour\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(15, 15, 'flag', '{\"secret_key\": \"flag\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(16, 16, 'exact', '{\"value_hash\": \"0fa499aba8c8ffbe5e3a783601595f78853e54d5dcc46fcbfae275b904e29992\", \"case_sensitive\": false}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(17, 17, 'instance_secret', '{\"secret_key\": \"webshell_path\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(18, 18, 'instance_secret', '{\"secret_key\": \"compromised_user\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(19, 19, 'instance_secret', '{\"secret_key\": \"access_hour\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(20, 20, 'flag', '{\"secret_key\": \"flag\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(21, 21, 'instance_secret', '{\"secret_key\": \"bad_process\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(22, 22, 'instance_secret', '{\"secret_key\": \"ssh_target_user\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(23, 23, 'instance_secret', '{\"secret_key\": \"world_writable\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(24, 24, 'flag', '{\"secret_key\": \"flag\"}', '2026-10-03 16:06:20', '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `lab_templates`
--

CREATE TABLE `lab_templates` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(140) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `runtime_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'simulated_web',
  `configuration` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_templates`
--

INSERT INTO `lab_templates` (`id`, `name`, `slug`, `description`, `runtime_type`, `configuration`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Single Web App', 'single_web_app', 'One simulated web target via lab gateway', 'simulated_web', '{\"gateway\": \"browser\", \"internet\": false, \"max_containers\": 1}', 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 'Forensics Workspace', 'forensics_workspace', 'Read-only artifact workspace (simulated)', 'simulated_forensics', '{\"gateway\": \"browser\", \"internet\": false}', 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 'Network Analysis', 'network_analysis', 'PCAP / log investigation workspace', 'simulated_network', '{\"gateway\": \"browser\", \"internet\": false}', 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 'Incident Response', 'incident_response', 'IR investigation with logs and timeline tasks', 'simulated_ir', '{\"gateway\": \"browser\", \"internet\": false}', 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 'Linux Investigation', 'linux_privilege_lab', 'Linux host investigation (simulated)', 'simulated_linux', '{\"gateway\": \"browser\", \"internet\": false}', 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `mentors`
--

CREATE TABLE `mentors` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `accepting_requests` tinyint(1) NOT NULL DEFAULT '1',
  `max_mentees` tinyint UNSIGNED NOT NULL DEFAULT '3',
  `preferred_frequency` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_session_length` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `languages` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `communication_style` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_status` enum('unverified','verified','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unverified',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentors`
--

INSERT INTO `mentors` (`id`, `user_id`, `bio`, `accepting_requests`, `max_mentees`, `preferred_frequency`, `preferred_session_length`, `languages`, `communication_style`, `verification_status`, `verified_by`, `verified_at`, `verification_note`, `created_at`, `updated_at`) VALUES
(1, 5, 'I help students with Web Security, Linux, and CTF fundamentals. Weekly 45-minute sessions.', 1, 3, 'weekly', '45 minutes', 'English, Thai', 'Async notes + weekly call', 'verified', 1, '2026-10-03 16:06:21', NULL, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(2, 7, 'Instructor mentor focusing on digital forensics and incident response.', 1, 3, 'weekly', '45 minutes', 'English, Thai', 'Async notes + weekly call', 'verified', 1, '2026-10-03 16:06:21', NULL, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(3, 2, 'Peer mentor for beginners learning web basics and Linux.', 1, 3, 'weekly', '45 minutes', 'English, Thai', 'Async notes + weekly call', 'unverified', NULL, NULL, NULL, '2026-10-03 16:06:21', '2026-10-03 16:06:21');

-- --------------------------------------------------------

--
-- Table structure for table `mentorships`
--

CREATE TABLE `mentorships` (
  `id` bigint UNSIGNED NOT NULL,
  `mentor_id` bigint UNSIGNED NOT NULL,
  `mentee_id` bigint UNSIGNED NOT NULL,
  `status` enum('pending','accepted','declined','cancelled','active','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `learning_goal` text COLLATE utf8mb4_unicode_ci,
  `message` text COLLATE utf8mb4_unicode_ci,
  `preferred_frequency` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_session_length` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentorships`
--

INSERT INTO `mentorships` (`id`, `mentor_id`, `mentee_id`, `status`, `learning_goal`, `message`, `preferred_frequency`, `preferred_session_length`, `started_at`, `ended_at`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'active', 'Reach Intermediate Web Security and solve Medium Arena challenges.', 'I completed beginner labs and want structured weekly guidance.', 'weekly', '45 minutes', '2026-10-03 16:06:21', NULL, '2026-10-03 16:06:21', '2026-10-03 16:06:21'),
(2, 1, 3, 'pending', 'Learn network security fundamentals', 'Looking for weekly guidance', NULL, NULL, NULL, NULL, '2026-10-03 16:07:18', '2026-10-03 16:07:18'),
(3, 1, 8, 'active', 'dsfsfs', 'dfsdf', NULL, NULL, '2026-10-06 03:23:08', NULL, '2026-10-06 03:02:07', '2026-10-06 03:23:08');

-- --------------------------------------------------------

--
-- Table structure for table `mentorship_feedback`
--

CREATE TABLE `mentorship_feedback` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `from_user_id` bigint UNSIGNED NOT NULL,
  `to_user_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentorship_goals`
--

CREATE TABLE `mentorship_goals` (
  `id` bigint UNSIGNED NOT NULL,
  `mentorship_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `target_level` tinyint UNSIGNED DEFAULT NULL,
  `status` enum('active','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `progress` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentorship_goals`
--

INSERT INTO `mentorship_goals` (`id`, `mentorship_id`, `skill_id`, `title`, `description`, `target_level`, `status`, `progress`, `created_at`, `completed_at`) VALUES
(1, 1, 6, 'Reach Intermediate Web Security', 'Complete labs, medium challenges, and a writeup.', 3, 'active', 35, '2026-10-03 16:06:21', NULL),
(2, 3, NULL, 'asdsa', 'asdad', NULL, 'active', 0, '2026-10-06 03:23:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mentorship_sessions`
--

CREATE TABLE `mentorship_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `mentorship_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `scheduled_at` timestamp NOT NULL,
  `duration_minutes` int UNSIGNED NOT NULL DEFAULT '45',
  `meeting_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled','no_show') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentorship_sessions`
--

INSERT INTO `mentorship_sessions` (`id`, `mentorship_id`, `title`, `notes`, `scheduled_at`, `duration_minutes`, `meeting_link`, `status`, `created_at`, `completed_at`) VALUES
(1, 1, 'Web Security kickoff', 'Review SQLi basics and choose next lab.', '2026-10-05 16:06:21', 45, NULL, 'scheduled', '2026-10-03 16:06:21', NULL),
(2, 3, 'sql injection', 'asdada', '2026-10-17 10:23:00', 45, 'https://github.com/Kroekkasit/cyskillshare', 'scheduled', '2026-10-06 03:23:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mentorship_skills`
--

CREATE TABLE `mentorship_skills` (
  `mentorship_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentorship_skills`
--

INSERT INTO `mentorship_skills` (`mentorship_id`, `skill_id`) VALUES
(1, 6),
(1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `mentor_skills`
--

CREATE TABLE `mentor_skills` (
  `mentor_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `preferred_level` tinyint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentor_skills`
--

INSERT INTO `mentor_skills` (`mentor_id`, `skill_id`, `preferred_level`, `created_at`) VALUES
(1, 1, NULL, '2026-10-03 16:06:21'),
(1, 6, NULL, '2026-10-03 16:06:21'),
(1, 7, NULL, '2026-10-03 16:06:21'),
(2, 11, NULL, '2026-10-03 16:06:21'),
(2, 13, NULL, '2026-10-03 16:06:21'),
(2, 16, NULL, '2026-10-03 16:06:21'),
(3, 1, NULL, '2026-10-03 16:06:21'),
(3, 6, NULL, '2026-10-03 16:06:21');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `reference_type`, `reference_id`, `is_read`, `created_at`) VALUES
(1, 3, 'thread_reply', 'mentor1 replied to your discussion.', '“How does CSRF actually work?”', 'thread', 2, 0, '2026-10-03 16:06:19'),
(2, 7, 'best_answer', 'Your reply was marked as the best answer.', '“Understanding prepared statements in PHP”', 'thread', 3, 0, '2026-10-03 16:06:19'),
(3, 4, 'group_member_joined', 'New group member', 'Someone joined “OSINT Beginners”.', 'collab_group', 5, 0, '2026-10-03 16:07:18'),
(4, 5, 'mentorship_request_received', 'Mentorship request', 'A student requested your mentorship.', 'mentorship', 2, 0, '2026-10-03 16:07:18'),
(5, 8, 'thread_reply', 'hello replied to your discussion.', '“hello ! world!”', 'thread', 10, 0, '2026-10-04 10:29:37'),
(6, 9, 'best_answer', 'Your reply was marked as the best answer.', '“hello ! world!”', 'thread', 10, 0, '2026-10-04 10:29:47'),
(7, 8, 'lab_started', 'Lab starting', '“Vulnerable Web Application” is being prepared.', 'lab_instance', 1, 0, '2026-10-04 14:35:36'),
(8, 8, 'lab_ready', 'Lab ready', 'Your lab environment is ready.', 'lab_instance', 1, 0, '2026-10-04 14:35:36'),
(9, 8, 'lab_started', 'Lab starting', '“Linux Security Investigation” is being prepared.', 'lab_instance', 2, 0, '2026-10-04 14:40:16'),
(10, 8, 'lab_ready', 'Lab ready', 'Your lab environment is ready.', 'lab_instance', 2, 0, '2026-10-04 14:40:17'),
(11, 8, 'challenge_solved', 'Challenge solved!', 'You earned 60 Arena points.', 'challenge', 11, 0, '2026-10-06 02:30:52'),
(12, 8, 'skill_level_changed', 'Skill progress: Linux', 'You reached Beginner based on your evidence.', 'skill', 1, 0, '2026-10-06 02:30:52'),
(13, 8, 'challenge_solved', 'Challenge solved!', 'You earned 160 Arena points.', 'challenge', 12, 0, '2026-10-06 02:31:53'),
(14, 8, 'skill_level_changed', 'Skill progress: Linux', 'You reached Developing based on your evidence.', 'skill', 1, 0, '2026-10-06 02:31:53'),
(15, 8, 'skill_level_changed', 'Skill progress: Operating Systems', 'You reached Beginner based on your evidence.', 'skill', 5, 0, '2026-10-06 02:31:53'),
(16, 8, 'challenge_solved', 'Challenge solved!', 'You earned 280 Arena points.', 'challenge', 13, 0, '2026-10-06 02:40:26'),
(17, 8, 'skill_level_changed', 'Skill progress: Malware Analysis', 'You reached Beginner based on your evidence.', 'skill', 19, 0, '2026-10-06 02:40:26'),
(18, 8, 'lab_expired', 'Lab expired', 'Your lab session expired. Progress on tasks is preserved for review where applicable.', 'lab_instance', 2, 0, '2026-10-06 02:40:59'),
(19, 8, 'lab_started', 'Lab starting', '“Suspicious Network Traffic” is being prepared.', 'lab_instance', 3, 0, '2026-10-06 02:41:15'),
(20, 8, 'lab_ready', 'Lab ready', 'Your lab environment is ready.', 'lab_instance', 3, 0, '2026-10-06 02:41:15'),
(21, 5, 'mentorship_request_received', 'Mentorship request', 'A student requested your mentorship.', 'mentorship', 3, 0, '2026-10-06 03:02:07'),
(22, 8, 'skill_level_changed', 'Skill progress: Windows', 'You reached Beginner based on your evidence.', 'skill', 2, 0, '2026-10-06 03:06:00'),
(23, 8, 'lab_started', 'Lab starting', '“สืบสวนความปลอดภัยบน Linux” is being prepared.', 'lab_instance', 4, 0, '2026-10-06 03:09:19'),
(24, 8, 'lab_ready', 'Lab ready', 'Your lab environment is ready.', 'lab_instance', 4, 0, '2026-10-06 03:09:19'),
(25, 8, 'skill_level_changed', 'Skill progress: Linux', 'You reached Intermediate based on your evidence.', 'skill', 1, 0, '2026-10-06 03:10:43'),
(26, 8, 'skill_level_changed', 'Skill progress: Incident Response', 'You reached Beginner based on your evidence.', 'skill', 13, 0, '2026-10-06 03:10:43'),
(27, 8, 'lab_completed', 'Lab completed', 'Great work — required tasks are complete. Consider writing a writeup.', 'lab', 5, 0, '2026-10-06 03:10:43'),
(28, 8, 'lab_started', 'Lab starting', '“Web Server Compromise” is being prepared.', 'lab_instance', 5, 0, '2026-10-06 03:13:25'),
(29, 8, 'lab_ready', 'Lab ready', 'Your lab environment is ready.', 'lab_instance', 5, 0, '2026-10-06 03:13:25'),
(30, 8, 'skill_level_changed', 'Skill progress: Incident Response', 'You reached Developing based on your evidence.', 'skill', 13, 0, '2026-10-06 03:17:53'),
(31, 8, 'lab_completed', 'Lab completed', 'Great work — required tasks are complete. Consider writing a writeup.', 'lab', 4, 0, '2026-10-06 03:17:53'),
(32, 8, 'skill_level_changed', 'Skill progress: Cross-Site Scripting (XSS)', 'You reached Beginner based on your evidence.', 'skill', 8, 0, '2026-10-06 03:18:48'),
(33, 8, 'mentorship_request_accepted', 'Mentorship accepted', 'Your mentorship request was accepted.', 'mentorship', 3, 0, '2026-10-06 03:23:08'),
(34, 8, 'mentorship_session_upcoming', 'Mentorship session scheduled', 'sql injection', 'mentorship', 3, 0, '2026-10-06 03:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `portfolios`
--

CREATE TABLE `portfolios` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `visibility` enum('public','community','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `headline` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` mediumtext COLLATE utf8mb4_unicode_ci,
  `university` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `graduation_year` smallint UNSIGNED DEFAULT NULL,
  `location` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_location` tinyint(1) NOT NULL DEFAULT '0',
  `show_email` tinyint(1) NOT NULL DEFAULT '0',
  `github_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resume_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `theme` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dark_cyber_academy',
  `featured_project_limit` tinyint UNSIGNED NOT NULL DEFAULT '3',
  `sections_json` json DEFAULT NULL,
  `show_challenge_stats` tinyint(1) NOT NULL DEFAULT '1',
  `show_skill_evidence` tinyint(1) NOT NULL DEFAULT '1',
  `show_community_stats` tinyint(1) NOT NULL DEFAULT '1',
  `view_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolios`
--

INSERT INTO `portfolios` (`id`, `user_id`, `is_enabled`, `visibility`, `headline`, `about`, `university`, `program`, `graduation_year`, `location`, `show_location`, `show_email`, `github_url`, `linkedin_url`, `website_url`, `resume_url`, `theme`, `featured_project_limit`, `sections_json`, `show_challenge_stats`, `show_skill_evidence`, `show_community_stats`, `view_count`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'public', 'Cybersecurity Student', 'Third-year Computer Science student at Khon Kaen University with a focus on web application security, secure coding, and hands-on lab work. I document projects from coursework and self-directed practice to demonstrate practical security skills.', 'Khon Kaen University', 'Computer Science', 2027, NULL, 0, 0, 'https://github.com/example/student1', NULL, NULL, NULL, 'dark_cyber_academy', 3, '{\"about\": true, \"links\": true, \"skills\": true, \"projects\": true, \"writeups\": true, \"community\": true, \"education\": true, \"challenges\": true, \"experience\": true, \"certifications\": true}', 1, 1, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 3, 1, 'community', 'Cybersecurity Learner', NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 'dark_cyber_academy', 3, NULL, 1, 1, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 4, 1, 'private', 'Private Portfolio', NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 'dark_cyber_academy', 3, NULL, 1, 1, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 5, 1, 'public', 'Peer Mentor — Cybersecurity', NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 'dark_cyber_academy', 3, NULL, 1, 1, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 9, 1, 'public', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 'dark_cyber_academy', 3, '{\"about\": true, \"links\": true, \"skills\": true, \"projects\": true, \"writeups\": true, \"community\": true, \"education\": true, \"challenges\": true, \"experience\": true, \"certifications\": true}', 1, 1, 1, 0, '2026-10-04 10:27:45', '2026-10-04 10:27:45'),
(6, 8, 1, 'public', 'sdsadsadaa', 'adada', 'kku', 'cy', 2028, 'kkc', 0, 0, 'https://github.com/Kroekkasit/cyskillshare', 'https://github.com/Kroekkasit/cyskillshare', NULL, NULL, 'dark_cyber_academy', 3, '{\"about\": true, \"links\": true, \"skills\": true, \"projects\": true, \"writeups\": true, \"community\": true, \"education\": true, \"challenges\": true, \"experience\": true, \"certifications\": true}', 1, 1, 1, 0, '2026-10-04 14:33:36', '2026-10-06 02:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_analytics_daily`
--

CREATE TABLE `portfolio_analytics_daily` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `metric` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metric_date` date NOT NULL,
  `count` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_certifications`
--

CREATE TABLE `portfolio_certifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issuer` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credential_id` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credential_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `visibility` enum('public','community','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_education`
--

CREATE TABLE `portfolio_education` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `institution` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_year` smallint UNSIGNED DEFAULT NULL,
  `end_year` smallint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_education`
--

INSERT INTO `portfolio_education` (`id`, `user_id`, `institution`, `program`, `field`, `start_year`, `end_year`, `description`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 2, 'Khon Kaen University', 'Computer Science', 'Cybersecurity', 2023, 2027, NULL, 10, '2026-10-03 16:06:20', '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_experience`
--

CREATE TABLE `portfolio_experience` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `organization` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `visibility` enum('public','community','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_featured_skills`
--

CREATE TABLE `portfolio_featured_skills` (
  `user_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `display_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_featured_skills`
--

INSERT INTO `portfolio_featured_skills` (`user_id`, `skill_id`, `display_order`) VALUES
(2, 1, 20),
(2, 3, 30),
(2, 6, 10),
(2, 16, 40),
(8, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_featured_writeups`
--

CREATE TABLE `portfolio_featured_writeups` (
  `user_id` bigint UNSIGNED NOT NULL,
  `writeup_id` bigint UNSIGNED NOT NULL,
  `display_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_featured_writeups`
--

INSERT INTO `portfolio_featured_writeups` (`user_id`, `writeup_id`, `display_order`) VALUES
(2, 1, 1),
(2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_type` enum('security_tool','web_security','network_security','digital_forensics','malware_analysis','reverse_engineering','ctf','automation','research','academic','open_source','home_lab','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `status` enum('planning','in_progress','completed','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planning',
  `publish_status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('public','community','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `repository_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `demo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documentation_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  `view_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `title`, `slug`, `short_description`, `description`, `project_type`, `status`, `publish_status`, `visibility`, `repository_url`, `demo_url`, `documentation_url`, `thumbnail`, `start_date`, `end_date`, `featured`, `display_order`, `view_count`, `created_at`, `updated_at`) VALUES
(1, 2, 'Secure File Upload Lab', 'secure-file-upload-lab', 'A deliberately vulnerable PHP upload lab hardened step-by-step to teach secure file handling, MIME validation, and access control.', '## Overview\n\nThis home-lab web application simulates a student file-sharing portal built with **PHP**, **Apache**, and **MySQL**. The initial version contained classic upload flaws: missing extension checks, predictable storage paths, and weak session handling.\n\n## What I built\n\n- Baseline vulnerable upload endpoint for classroom demos\n- Hardened version with allowlists, content sniffing, and randomized storage\n- `.htaccess` rules to block script execution in upload directories\n- Prepared statements for all database queries\n\n## Key takeaways\n\nUpload handlers are a high-risk boundary. Defense requires layered controls: validation, storage isolation, and least-privilege access — not a single regex on the filename.', 'web_security', 'completed', 'published', 'public', 'https://github.com/example/student1/secure-file-upload-lab', NULL, NULL, NULL, '2025-09-01', '2025-11-15', 1, 10, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 2, 'Network Scanner', 'mini-network-scanner', 'A Python/Scapy-based host discovery and port-scanning tool for learning network reconnaissance fundamentals.', '## Overview\n\nA command-line scanner inspired by coursework on **network reconnaissance**. It performs ARP-based host discovery on local subnets and TCP connect scans against a configurable port list.\n\n## Features\n\n- Live host discovery with Scapy\n- Sequential and threaded connect scans\n- CSV export of open ports and service guesses\n- Safe defaults: rate limiting and explicit target confirmation\n\n## Learning goals\n\nUnderstanding scan types, timing, and noise trade-offs prepares you for both offensive assessments and defensive detection engineering.', 'network_security', 'completed', 'published', 'public', 'https://github.com/example/student1/mini-network-scanner', NULL, NULL, NULL, '2025-06-01', '2025-08-20', 1, 20, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 2, 'Malware Analysis Toolkit', 'malware-analysis-toolkit', 'A Python helper suite for static triage, string extraction, and Ghidra workflow automation in an isolated Linux lab.', '## Overview\n\nCollection of scripts that streamline repetitive steps when triaging suspicious binaries in a **Linux** malware lab.\n\n## Components\n\n- PE/ELF header summarizer\n- YARA rule runner with match reporting\n- Ghidra headless export wrapper for function lists and strings\n- Safe-copy utility that preserves timestamps and hashes\n\n## Notes\n\nAll samples are analyzed in an air-gapped VM. This project documents workflow automation — not a substitute for full dynamic analysis.', 'malware_analysis', 'completed', 'published', 'public', 'https://github.com/example/student1/malware-analysis-toolkit', NULL, NULL, NULL, '2025-01-10', '2025-04-30', 0, 30, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 2, 'Unfinished Security Tool', 'unfinished-tool', 'Work-in-progress automation script — not ready for review.', 'Early prototype for log parsing and alert enrichment. **Draft only** — documentation and tests are incomplete.', 'automation', 'in_progress', 'draft', 'private', NULL, NULL, NULL, NULL, NULL, NULL, 0, 99, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 8, 'my 1st proj', '1stproj', 'asdad', 'asdaada', 'digital_forensics', 'completed', 'published', 'community', 'https://github.com/Kroekkasit/cyskillshare', 'https://github.com/Kroekkasit/cyskillshare', 'https://github.com/Kroekkasit/cyskillshare', NULL, '2026-10-12', '2026-10-29', 0, 1, 0, '2026-10-06 03:04:31', '2026-10-06 03:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `project_challenges`
--

CREATE TABLE `project_challenges` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED NOT NULL,
  `relationship_type` enum('inspired_by','built_after','related_to') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'related_to',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_challenges`
--

INSERT INTO `project_challenges` (`id`, `project_id`, `challenge_id`, `relationship_type`, `created_at`) VALUES
(1, 1, 1, 'related_to', '2026-10-03 16:06:20'),
(2, 1, 3, 'related_to', '2026-10-03 16:06:20'),
(3, 2, 5, 'related_to', '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `project_images`
--

CREATE TABLE `project_images` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `stored_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int UNSIGNED NOT NULL DEFAULT '0',
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_labs`
--

CREATE TABLE `project_labs` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `lab_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_reactions`
--

CREATE TABLE `project_reactions` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `reaction_type` enum('helpful','interesting','impressive') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_skills`
--

CREATE TABLE `project_skills` (
  `project_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `importance` enum('primary','secondary','supporting') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'secondary',
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_skills`
--

INSERT INTO `project_skills` (`project_id`, `skill_id`, `importance`, `weight`, `created_at`) VALUES
(1, 6, 'primary', 1.00, '2026-10-03 16:06:20'),
(1, 10, 'secondary', 0.75, '2026-10-03 16:06:20'),
(1, 24, 'primary', 1.00, '2026-10-03 16:06:20'),
(2, 3, 'primary', 1.00, '2026-10-03 16:06:20'),
(2, 11, 'secondary', 0.80, '2026-10-03 16:06:20'),
(2, 22, 'primary', 1.00, '2026-10-03 16:06:20'),
(3, 19, 'primary', 1.00, '2026-10-03 16:06:20'),
(3, 20, 'primary', 1.00, '2026-10-03 16:06:20'),
(5, 6, 'secondary', 0.70, '2026-10-06 03:04:31'),
(5, 12, 'secondary', 0.70, '2026-10-06 03:04:31'),
(5, 17, 'secondary', 0.70, '2026-10-06 03:04:31'),
(5, 25, 'secondary', 0.70, '2026-10-06 03:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `project_technologies`
--

CREATE TABLE `project_technologies` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `technology` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_technologies`
--

INSERT INTO `project_technologies` (`id`, `project_id`, `technology`, `created_at`) VALUES
(1, 1, 'PHP', '2026-10-03 16:06:20'),
(2, 1, 'Apache', '2026-10-03 16:06:20'),
(3, 1, 'MySQL', '2026-10-03 16:06:20'),
(4, 1, 'Linux', '2026-10-03 16:06:20'),
(5, 2, 'Python', '2026-10-03 16:06:20'),
(6, 2, 'Scapy', '2026-10-03 16:06:20'),
(7, 2, 'Linux', '2026-10-03 16:06:20'),
(8, 3, 'Python', '2026-10-03 16:06:20'),
(9, 3, 'Ghidra', '2026-10-03 16:06:20'),
(10, 3, 'Linux', '2026-10-03 16:06:20'),
(11, 5, 'docker', '2026-10-06 03:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `project_verifications`
--

CREATE TABLE `project_verifications` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `requested_by` bigint UNSIGNED NOT NULL,
  `status` enum('pending','verified','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `verification_note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_writeups`
--

CREATE TABLE `project_writeups` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `writeup_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recruitment_applications`
--

CREATE TABLE `recruitment_applications` (
  `id` bigint UNSIGNED NOT NULL,
  `recruitment_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','accepted','rejected','withdrawn') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recruitment_posts`
--

CREATE TABLE `recruitment_posts` (
  `id` bigint UNSIGNED NOT NULL,
  `creator_id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `looking_for` enum('study','ctf','project','mentor','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `status` enum('open','filled','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recruitment_posts`
--

INSERT INTO `recruitment_posts` (`id`, `creator_id`, `group_id`, `title`, `description`, `looking_for`, `status`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 2, 6, 'Looking for Web Security teammate for university CTF', 'Packet Pirates needs someone comfortable with SQLi/XSS for weekend practice.', 'ctf', 'open', '2026-11-02 16:06:21', '2026-10-03 16:06:21', '2026-10-03 16:06:21');

-- --------------------------------------------------------

--
-- Table structure for table `recruitment_skills`
--

CREATE TABLE `recruitment_skills` (
  `recruitment_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recruitment_skills`
--

INSERT INTO `recruitment_skills` (`recruitment_id`, `skill_id`, `required`) VALUES
(1, 6, 1),
(1, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `id` bigint UNSIGNED NOT NULL,
  `thread_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `parent_reply_id` bigint UNSIGNED DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_best_answer` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`id`, `thread_id`, `user_id`, `parent_reply_id`, `content`, `is_best_answer`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 5, NULL, 'ใช่ครับ — เบราว์เซอร์จะส่ง cookie ตอน POST ข้ามเว็บได้ในหลายเคส\n\n**SameSite=Lax/Strict** ช่วยลด CSRF ได้เยอะ แต่ API กับเบราว์เซอร์เก่ายังควรมี token\n\nแพทเทิร์นที่ใช้บ่อยใน PHP คือเก็บ CSRF token ใน session แล้วตรวจทุก request ที่เปลี่ยนข้อมูล', 1, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(2, 3, 7, NULL, 'Prepared statements คือตัวหลักที่กัน SQL Injection ในโครงสร้างคิวรี\n\nยังควร validate input ตามกฎธุรกิจ (ความยาว, ชนิด, allowlist) แต่ validation **แทน** parameterization ไม่ได้\n\nอย่าเอา input ผู้ใช้ไปต่อสตริง SQL โดยตรง', 1, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(3, 4, 5, NULL, 'คลิกขวาที่แพ็กเก็ต → Follow → TCP Stream แล้วสลับฝั่ง client/server ได้ Export เป็น raw/ASCII ไปใส่ใน writeup ได้เลยครับ', 0, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(4, 9, 5, NULL, '**Hashing** เป็นทางเดียว (เก็บรหัสผ่านด้วย salt/argon2) ส่วน **Encryption** ถอดกลับด้วยกุญแจได้ (TLS, disk encryption)\n\nถ้าต้องเก็บความลับแล้วดึงค่าเดิมทีหลัง → เข้ารหัส ถ้าแค่ตรวจภายหลัง → แฮช', 1, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(5, 10, 9, NULL, 'reply heyyyy', 0, '2026-10-04 10:29:37', '2026-10-04 10:29:50', NULL),
(6, 11, 8, NULL, '<script>alert(\"test xss\")</script>', 0, '2026-10-06 02:19:20', '2026-10-06 02:19:20', NULL),
(7, 13, 5, NULL, 'เริ่มจากแล็บในระบบก่อนเลยครับ ปลอดภัยและมี task ให้ทำทีละขั้น อย่าเอา payload ไปลองกับเว็บจริงเด็ดขาด จากนั้นค่อยดู writeup ภาษาไทย/อังกฤษใน Knowledge Base', 0, '2026-10-06 02:48:05', '2026-10-06 02:48:05', NULL),
(8, 14, 5, NULL, 'ดู parent PID และ cmdline ใน /proc/<pid>/ ครับ ถ้าชื่อคล้าย systemd/kworker แต่ path หรือ env แปลก ให้จดชื่อ process นั้นเป็นคำตอบ task แล้วค่อยไปหา flag ท้ายแล็บ', 0, '2026-10-06 02:48:05', '2026-10-06 02:48:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint UNSIGNED NOT NULL,
  `reporter_id` bigint UNSIGNED NOT NULL,
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` bigint UNSIGNED NOT NULL,
  `reason` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','reviewed','resolved','dismissed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'student', 'Standard student account', '2026-10-03 16:06:19'),
(2, 'mentor', 'Peer mentor', '2026-10-03 16:06:19'),
(3, 'instructor', 'Course instructor', '2026-10-03 16:06:19'),
(4, 'moderator', 'Community moderator', '2026-10-03 16:06:19'),
(5, 'admin', 'Platform administrator', '2026-10-03 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `parent_skill_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(140) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_gated` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `category_id`, `parent_skill_id`, `name`, `slug`, `description`, `icon`, `display_order`, `is_active`, `is_gated`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Linux', 'linux', 'Linux command-line proficiency, file permissions, process management, and system administration fundamentals. Security practitioners rely on Linux daily for analysis, scripting, and server hardening. Strong CLI skills accelerate every other domain in the skill tree.', NULL, 10, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 1, NULL, 'Windows', 'windows', 'Windows architecture, registry, services, event logs, and user account models. Many enterprise environments run Windows endpoints and servers, making platform knowledge essential for blue and purple teams. Understanding Windows internals supports forensics, incident response, and privilege escalation analysis.', NULL, 20, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 1, NULL, 'Networking', 'networking', 'TCP/IP, DNS, HTTP, routing, switching, and network troubleshooting at the packet level. Nearly every attack and defense technique traverses a network, so protocol literacy is non-negotiable. Packet analysis and service enumeration build directly on this foundation.', NULL, 30, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 1, NULL, 'Security Fundamentals', 'security-fundamentals', 'Core principles including the CIA triad, threat modeling, authentication, authorization, and basic cryptography. These concepts frame every technical skill and help you reason about risk systematically. A solid fundamentals base prevents chasing tools without understanding why they matter.', NULL, 40, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 1, NULL, 'Operating Systems', 'operating-systems', 'How kernels, memory, processes, threads, and privilege models work across Linux and Windows. OS internals explain why exploits succeed and how defenders detect abnormal behavior. This skill bridges user-level tools and low-level security analysis.', NULL, 50, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(6, 2, NULL, 'Web Security', 'web-security', 'Broad coverage of web application attack surfaces, HTTP semantics, session management, and the OWASP Top 10. Web apps remain the most common external entry point for attackers. Parent skill for injection, XSS, CSRF, and access-control specializations.', NULL, 10, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(7, 2, 6, 'SQL Injection', 'sql-injection', 'Exploiting unsanitized database queries to bypass authentication, extract data, or modify records. SQLi teaches why parameterized queries and input validation are mandatory, not optional. Practice spans error-based, union-based, and blind injection techniques in controlled labs.', NULL, 10, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(8, 2, 6, 'Cross-Site Scripting (XSS)', 'xss', 'Injecting malicious scripts into web pages viewed by other users to steal sessions or perform actions on their behalf. XSS highlights the gap between server-side trust and browser-side execution. Mitigation requires output encoding, Content Security Policy, and secure cookie flags.', NULL, 20, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(9, 2, 6, 'Cross-Site Request Forgery (CSRF)', 'csrf', 'Tricking authenticated users into submitting unintended requests that the application trusts. CSRF attacks abuse the browser\'s automatic inclusion of session cookies. Defenses include anti-CSRF tokens, SameSite cookies, and verifying request origin.', NULL, 30, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(10, 2, 6, 'Broken Access Control', 'access-control', 'Exploiting flaws in authorization logic to access resources or perform actions beyond your privilege level. IDOR, privilege escalation, and forced browsing are common manifestations. Secure design enforces authorization on every request, not just the UI.', NULL, 40, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(11, 2, NULL, 'Network Security', 'network-security', 'Network-layer attacks, protocol weaknesses, man-in-the-middle scenarios, and packet-level exploitation. Scanning, sniffing, and traffic analysis skills support both offensive assessments and defensive monitoring. Strong networking fundamentals are prerequisite for meaningful progress here.', NULL, 20, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(12, 2, NULL, 'Penetration Testing', 'penetration-testing', 'Structured methodology for authorized security assessments: reconnaissance, exploitation, post-exploitation, and reporting. Pen testers combine technical skills with communication and scope management. Findings must be reproducible, risk-rated, and actionable for stakeholders.', NULL, 30, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(13, 3, NULL, 'Incident Response', 'incident-response', 'Detecting, containing, eradicating, and recovering from security incidents using established playbooks. IR teams balance speed with evidence preservation for later analysis. Effective response reduces dwell time and limits business impact.', NULL, 10, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(14, 3, NULL, 'Security Monitoring', 'security-monitoring', 'SIEM configuration, log aggregation, alert tuning, and continuous visibility across infrastructure. Good monitoring turns raw telemetry into actionable signals without alert fatigue. Correlation rules and baselines help distinguish noise from genuine threats.', NULL, 20, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(15, 3, NULL, 'Threat Detection', 'threat-detection', 'Identifying adversary behavior through signatures, behavioral analytics, and threat intelligence feeds. Detection engineering maps attacker TTPs to observable events in your environment. Continuous improvement closes gaps as threats evolve.', NULL, 30, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(16, 4, NULL, 'Digital Forensics', 'digital-forensics', 'Collecting, preserving, and analyzing digital evidence from disks, logs, and file system artifacts. Chain of custody and integrity verification are as important as technical analysis. Forensic findings often support incident response and legal proceedings.', NULL, 10, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(17, 4, 16, 'Memory Analysis', 'memory-analysis', 'Volatile memory forensics: process dumps, kernel structures, injected code, and rootkit detection in RAM. Memory captures ephemeral evidence that disappears on reboot. Tools like Volatility help reconstruct running system state at capture time.', NULL, 10, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(18, 4, 16, 'Network Forensics', 'network-forensics', 'Reconstructing security events from PCAPs, NetFlow, firewall logs, and proxy records. Network forensics connects endpoints and timelines when disk evidence is unavailable or incomplete. Packet-level detail reveals C2 channels, exfiltration, and lateral movement.', NULL, 20, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(19, 4, NULL, 'Malware Analysis', 'malware-analysis', 'Static and dynamic analysis of suspicious binaries, scripts, and droppers to understand behavior and intent. Analysts extract indicators of compromise for detection rules and incident scoping. Safe lab environments isolate samples from production networks.', NULL, 20, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(20, 4, NULL, 'Reverse Engineering', 'reverse-engineering', 'Disassembly, debugging, and understanding compiled code without access to source. RE skills support malware analysis, vulnerability research, and legacy system audits. Patience and systematic annotation turn opaque binaries into readable logic.', NULL, 30, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(21, 6, NULL, 'OSINT', 'osint', 'Gathering intelligence from publicly available sources: social profiles, DNS records, certificate transparency, and leaked data. OSINT supports threat hunting, due diligence, and pre-engagement reconnaissance. Ethical boundaries and privacy laws always apply.', NULL, 10, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(22, 6, NULL, 'Network Reconnaissance', 'network-recon', 'Host discovery, port scanning, service enumeration, and banner grabbing on target networks. Network recon maps the attack surface before deeper testing begins. Results feed prioritization for vulnerability assessment and penetration testing.', NULL, 20, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(23, 6, NULL, 'Web Reconnaissance', 'web-recon', 'Mapping web applications: directory brute-forcing, technology fingerprinting, and subdomain enumeration. Web recon reveals hidden endpoints, outdated frameworks, and misconfigurations. Passive techniques minimize detection during authorized assessments.', NULL, 30, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(24, 5, NULL, 'Secure Coding', 'secure-coding', 'Writing code that resists injection, XSS, and logic flaws through secure SDLC practices. Developers who understand attacks build safer defaults and fewer hotfixes. Code review, static analysis, and threat modeling integrate security early.', NULL, 10, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(25, 5, NULL, 'Application Security', 'application-security', 'Designing, testing, and hardening applications throughout the development lifecycle. AppSec spans architecture review, DAST/SAST tooling, and secure deployment pipelines. Shifting left reduces cost compared to post-release patching.', NULL, 20, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(26, 5, NULL, 'Cloud Security', 'cloud-security', 'Securing cloud IAM, storage, containers, serverless functions, and shared responsibility models. Misconfigurations—not hypervisor escapes—cause most cloud breaches. Infrastructure-as-code and policy-as-code enforce consistent baselines at scale.', NULL, 30, 1, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `skill_categories`
--

CREATE TABLE `skill_categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_categories`
--

INSERT INTO `skill_categories` (`id`, `name`, `slug`, `description`, `icon`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Foundations', 'foundations', 'Core computing and security concepts every practitioner needs', NULL, 10, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 'Offensive Security', 'offensive-security', 'Ethical hacking, exploitation, and penetration testing skills', NULL, 20, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 'Defensive Security', 'defensive-security', 'Detection, monitoring, and incident response capabilities', NULL, 30, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 'Security Analysis', 'security-analysis', 'Forensics, malware analysis, and reverse engineering', NULL, 40, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 'Security Engineering', 'security-engineering', 'Secure design, coding, and cloud architecture', NULL, 50, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(6, 'Reconnaissance', 'reconnaissance', 'OSINT and pre-engagement information gathering', NULL, 60, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `skill_evidence`
--

CREATE TABLE `skill_evidence` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `evidence_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `strength` tinyint UNSIGNED NOT NULL DEFAULT '2',
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_evidence`
--

INSERT INTO `skill_evidence` (`id`, `user_id`, `skill_id`, `evidence_type`, `source_type`, `source_id`, `title`, `description`, `strength`, `status`, `verified_by`, `verified_at`, `verification_note`, `created_at`) VALUES
(1, 8, 1, 'challenge_solved', 'challenge', 11, 'Linux Permissions', 'Solved Arena challenge (easy)', 1, 'accepted', NULL, NULL, NULL, '2026-10-06 02:30:52'),
(2, 8, 5, 'challenge_solved', 'challenge', 11, 'Linux Permissions', 'Solved Arena challenge (easy)', 1, 'accepted', NULL, NULL, NULL, '2026-10-06 02:30:52'),
(3, 8, 1, 'challenge_solved', 'challenge', 12, 'Process Investigation', 'Solved Arena challenge (medium)', 2, 'accepted', NULL, NULL, NULL, '2026-10-06 02:31:53'),
(4, 8, 5, 'challenge_solved', 'challenge', 12, 'Process Investigation', 'Solved Arena challenge (medium)', 1, 'accepted', NULL, NULL, NULL, '2026-10-06 02:31:53'),
(5, 8, 1, 'challenge_hard_solved', 'challenge', 13, 'Find the Suspicious Process', 'Solved Arena challenge (hard)', 2, 'accepted', NULL, NULL, NULL, '2026-10-06 02:40:26'),
(6, 8, 19, 'challenge_hard_solved', 'challenge', 13, 'Find the Suspicious Process', 'Solved Arena challenge (hard)', 2, 'accepted', NULL, NULL, NULL, '2026-10-06 02:40:26'),
(7, 8, 6, 'project', 'project', 5, 'my 1st proj', 'Project submission (pending verification)', 3, 'pending', NULL, NULL, NULL, '2026-10-06 03:04:31'),
(8, 8, 12, 'project', 'project', 5, 'my 1st proj', 'Project submission (pending verification)', 3, 'pending', NULL, NULL, NULL, '2026-10-06 03:04:31'),
(9, 8, 17, 'project', 'project', 5, 'my 1st proj', 'Project submission (pending verification)', 3, 'pending', NULL, NULL, NULL, '2026-10-06 03:04:31'),
(10, 8, 25, 'project', 'project', 5, 'my 1st proj', 'Project submission (pending verification)', 3, 'pending', NULL, NULL, NULL, '2026-10-06 03:04:31'),
(11, 8, 2, 'writeup', 'writeup', 9, 'my writeupp', 'Technical writeup', 3, 'accepted', NULL, NULL, NULL, '2026-10-06 03:06:00'),
(12, 8, 1, 'lab', 'lab', 5, 'สืบสวนความปลอดภัยบน Linux', 'Completed Cyber Lab (intermediate)', 4, 'accepted', NULL, NULL, NULL, '2026-10-06 03:10:43'),
(13, 8, 13, 'lab', 'lab', 5, 'สืบสวนความปลอดภัยบน Linux', 'Completed Cyber Lab (intermediate)', 2, 'accepted', NULL, NULL, NULL, '2026-10-06 03:10:43'),
(14, 8, 1, 'lab', 'lab', 4, 'Web Server Compromise', 'Completed Cyber Lab (advanced)', 3, 'accepted', NULL, NULL, NULL, '2026-10-06 03:17:53'),
(15, 8, 6, 'lab', 'lab', 4, 'Web Server Compromise', 'Completed Cyber Lab (advanced)', 3, 'accepted', NULL, NULL, NULL, '2026-10-06 03:17:53'),
(16, 8, 13, 'lab', 'lab', 4, 'Web Server Compromise', 'Completed Cyber Lab (advanced)', 5, 'accepted', NULL, NULL, NULL, '2026-10-06 03:17:53'),
(17, 8, 8, 'writeup', 'writeup', 12, 'adadsad', 'Technical writeup', 3, 'accepted', NULL, NULL, NULL, '2026-10-06 03:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `skill_levels`
--

CREATE TABLE `skill_levels` (
  `id` int UNSIGNED NOT NULL,
  `level` tinyint UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minimum_score` int UNSIGNED NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_levels`
--

INSERT INTO `skill_levels` (`id`, `level`, `name`, `description`, `minimum_score`, `display_order`) VALUES
(1, 0, 'Not Started', 'No demonstrated evidence yet', 0, 0),
(2, 1, 'Beginner', 'Foundational exposure with guided practice', 10, 10),
(3, 2, 'Developing', 'Growing competence on routine tasks', 25, 20),
(4, 3, 'Intermediate', 'Independent work on moderate problems', 45, 30),
(5, 4, 'Advanced', 'Strong performance on complex scenarios', 70, 40),
(6, 5, 'Demonstrated', 'Portfolio-ready mastery with peer recognition', 90, 50);

-- --------------------------------------------------------

--
-- Table structure for table `skill_prerequisites`
--

CREATE TABLE `skill_prerequisites` (
  `skill_id` int UNSIGNED NOT NULL,
  `prerequisite_skill_id` int UNSIGNED NOT NULL,
  `minimum_level` tinyint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_prerequisites`
--

INSERT INTO `skill_prerequisites` (`skill_id`, `prerequisite_skill_id`, `minimum_level`) VALUES
(7, 6, 1),
(8, 6, 1),
(9, 6, 1),
(10, 6, 1),
(12, 3, 1),
(12, 4, 1),
(17, 16, 2),
(18, 16, 1);

-- --------------------------------------------------------

--
-- Table structure for table `skill_requirements`
--

CREATE TABLE `skill_requirements` (
  `id` int UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `target_level` tinyint UNSIGNED NOT NULL,
  `evidence_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `minimum_count` int UNSIGNED NOT NULL DEFAULT '1',
  `minimum_difficulty` enum('easy','medium','hard','expert') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_requirements`
--

INSERT INTO `skill_requirements` (`id`, `skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`, `is_required`, `weight`, `description`, `created_at`, `updated_at`) VALUES
(1, 7, 1, 'challenge_solved', 1, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(2, 7, 2, 'challenge_solved', 2, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(3, 7, 2, 'challenge_solved', 1, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(4, 7, 3, 'challenge_solved', 2, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(5, 7, 3, 'writeup', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(6, 7, 3, 'community_best_answer', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(7, 7, 4, 'challenge_solved', 2, 'hard', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(8, 7, 4, 'writeup', 2, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(9, 7, 4, 'project', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(10, 7, 5, 'instructor_verification', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(11, 7, 5, 'project', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(12, 6, 1, 'challenge_solved', 1, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(13, 6, 2, 'challenge_solved', 2, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(14, 6, 2, 'challenge_solved', 1, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(15, 6, 3, 'challenge_solved', 2, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(16, 6, 3, 'writeup', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(17, 6, 4, 'challenge_solved', 2, 'hard', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(18, 6, 4, 'writeup', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(19, 6, 4, 'community_best_answer', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(20, 6, 5, 'instructor_verification', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(21, 6, 5, 'project', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(22, 1, 1, 'challenge_solved', 1, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(23, 1, 2, 'challenge_solved', 2, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(24, 1, 3, 'challenge_solved', 1, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(25, 1, 3, 'lab', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(26, 1, 4, 'challenge_solved', 2, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(27, 1, 4, 'writeup', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(28, 1, 5, 'challenge_solved', 1, 'hard', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(29, 1, 5, 'instructor_verification', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(30, 3, 1, 'challenge_solved', 1, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(31, 3, 2, 'challenge_solved', 2, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(32, 3, 3, 'challenge_solved', 1, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(33, 3, 3, 'lab', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(34, 3, 4, 'challenge_solved', 2, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(35, 3, 4, 'writeup', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(36, 3, 5, 'challenge_solved', 1, 'hard', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(37, 3, 5, 'project', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(38, 16, 1, 'challenge_solved', 1, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(39, 16, 2, 'challenge_solved', 2, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(40, 16, 3, 'challenge_solved', 1, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(41, 16, 3, 'writeup', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(42, 16, 4, 'challenge_solved', 2, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(43, 16, 4, 'project', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(44, 16, 5, 'instructor_verification', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(45, 16, 5, 'writeup', 2, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(46, 21, 1, 'challenge_solved', 1, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(47, 21, 2, 'challenge_solved', 2, 'easy', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(48, 21, 3, 'challenge_solved', 1, 'medium', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(49, 21, 3, 'writeup', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(50, 21, 4, 'challenge_solved', 1, 'hard', 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(51, 21, 4, 'community_best_answer', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(52, 21, 5, 'event_participation', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20'),
(53, 21, 5, 'project', 1, NULL, 1, 1.00, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'xss', 'xss', '2026-10-03 16:06:19'),
(2, 'sql-injection', 'sql-injection', '2026-10-03 16:06:19'),
(3, 'csrf', 'csrf', '2026-10-03 16:06:19'),
(4, 'linux', 'linux', '2026-10-03 16:06:19'),
(5, 'wireshark', 'wireshark', '2026-10-03 16:06:19'),
(6, 'python', 'python', '2026-10-03 16:06:19'),
(7, 'php', 'php', '2026-10-03 16:06:19'),
(8, 'docker', 'docker', '2026-10-03 16:06:19'),
(9, 'malware', 'malware', '2026-10-03 16:06:19'),
(10, 'forensics', 'forensics', '2026-10-03 16:06:19'),
(11, 'networking', 'networking', '2026-10-03 16:06:19'),
(12, 'nmap', 'nmap', '2026-10-03 16:06:19'),
(13, 'powershell', 'powershell', '2026-10-03 16:06:19'),
(14, 'encryption', 'encryption', '2026-10-03 16:06:19'),
(15, 'hashing', 'hashing', '2026-10-03 16:06:19'),
(16, 'ctf', 'ctf', '2026-10-03 16:06:19'),
(17, 'web-security', 'web-security', '2026-10-03 16:06:19'),
(18, 'reverse-engineering', 'reverse-engineering', '2026-10-03 16:06:19'),
(19, 'sql', 'sql', '2026-10-03 16:06:19'),
(20, 'authentication', 'authentication', '2026-10-03 16:06:19'),
(21, 'tcp', 'tcp', '2026-10-03 16:06:19'),
(22, 'pcap', 'pcap', '2026-10-03 16:06:19'),
(23, 'crypto', 'crypto', '2026-10-03 16:06:19'),
(24, 'osint', 'osint', '2026-10-03 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE `threads` (
  `id` bigint UNSIGNED NOT NULL,
  `channel_id` int UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','solved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `views` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `threads`
--

INSERT INTO `threads` (`id`, `channel_id`, `user_id`, `challenge_id`, `title`, `content`, `status`, `is_pinned`, `is_locked`, `views`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 6, NULL, 'ยินดีต้อนรับสู่ CySkillShare / Welcome', 'ยินดีต้อนรับสู่ชุมชนเรียนรู้ไซเบอร์ของวิทยาลัยการคอมพิวเตอร์ มข.\n\nกรุณา:\n- เคารพกันและกัน\n- ห้ามแชร์เฉลยข้อสอบที่ผิดระเบียบวิชาการ\n- คุยเชิงเทคนิคพร้อมหลักฐาน\n- ติดแท็กให้ค้นหาได้ง่าย\n\nถาม → คุย → ช่วย → แก้ → แชร์ความรู้\n\n---\nWelcome to the cybersecurity learning community at College of Computing, KKU.', 'open', 1, 0, 7, '2026-10-03 16:06:19', '2026-10-06 03:56:50', NULL),
(2, 3, 3, NULL, 'CSRF ทำงานยังไงอะ? งงมาก', 'ตอนนี้เรียนเรื่อง CSRF อยู่ครับ งงว่าทำไมเว็บปลอมถึงสั่ง request ไปยังเว็บที่เราล็อกอินอยู่ได้\n\n1. เบราว์เซอร์ส่ง cookie ไปให้ทุกครั้งเลยไหม?\n2. SameSite ช่วยได้แค่ไหน?\n3. ตอนไหนยังต้องใช้ CSRF token?\n\nขอตัวอย่าง PHP แบบเข้าใจง่ายหน่อยได้ไหมครับ ขอบคุณครับ', 'solved', 0, 0, 1, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(3, 3, 2, NULL, 'Prepared Statements ใน PHP ใช้ยังไงให้ปลอดภัย?', 'กำลังทำโปรเจกต์ PHP ใช้ PDO ครับ\n\nใช้ prepared statements อย่างเดียวพอป้องกัน SQL Injection ได้ไหม หรือต้อง validate input ด้วย?\n\n```php\n$stmt = $pdo->prepare(\"SELECT * FROM users WHERE username = ?\");\n$stmt->execute([$username]);\n```\n\nพี่ๆ มี best practice แนะนำไหมครับ?', 'solved', 0, 0, 0, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(4, 4, 3, NULL, 'อ่าน TCP Stream ใน Wireshark ยังไงให้เร็ว?', 'ในแล็บมี packet เยอะมาก อยากรู้วิธี Follow TCP Stream แล้วดึง request/response ออกมาทำรายงานเร็วที่สุดครับ', 'open', 0, 0, 1, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(5, 4, 4, NULL, 'Nmap SYN scan vs TCP connect scan', 'What is the practical difference between `-sS` and `-sT` when scanning lab machines? When would a connect scan be preferred?', 'open', 0, 0, 0, '2026-10-03 16:06:19', '2026-10-03 16:06:19', NULL),
(6, 13, 2, NULL, 'จะเริ่มเล่น CTF ครั้งแรกยังไงดี?', 'อยากลอง CTF เทอมนี้ครับ มือใหม่ควรเริ่มหมวดไหนก่อน? แล้วบน Linux ควรลงเครื่องมืออะไรบ้าง?', 'open', 0, 0, 0, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(7, 11, 3, NULL, 'chmod 600 กับ 644 ต่างกันยังไง? (ไฟล์ลับ)', 'พี่ๆ ช่วยอธิบายหน่อยได้ไหมครับ เวลาเก็บ secret ใน config ควรใช้ chmod 600 หรือ 644? อยากเขียนในรายงานแล็บให้ถูกต้อง', 'open', 0, 0, 0, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(8, 5, 4, NULL, 'How to identify suspicious PowerShell activity?', 'In a DFIR exercise we have Windows event logs. Which Event IDs or command-line patterns usually indicate suspicious PowerShell usage?', 'open', 0, 0, 0, '2026-10-03 16:06:19', '2026-10-03 16:06:19', NULL),
(9, 8, 2, NULL, 'แฮชกับเข้ารหัสต่างกันยังไงครับ?', 'เรียน Cryptography แล้วยังสับสนระหว่าง hashing กับ encryption อยู่ ขอตัวอย่างในสายไซเบอร์หน่อยได้ไหมครับ เช่นเก็บรหัสผ่าน กับ TLS', 'solved', 0, 0, 0, '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(10, 11, 8, NULL, 'hello ! world!', 'asdsadaasasdsadadasdas', 'open', 0, 0, 8, '2026-10-04 10:26:05', '2026-10-04 10:29:50', NULL),
(11, 11, 8, NULL, 'asasasdaad', 'asdadadada', 'open', 0, 0, 2, '2026-10-06 02:18:54', '2026-10-06 02:19:20', NULL),
(12, 11, 2, NULL, 'ส่งงานแล็บ Web Security แล้วได้ feedback ยังไงบ้าง?', 'เพื่อนๆ ที่ส่งแล็บ Vulnerable Web Application ไปแล้ว อาจารย์คอมเมนต์ประเด็นไหนเยอะสุดครับ? อยากเตรียม remediation ให้ครบ', 'open', 0, 0, 1, '2026-10-06 02:48:05', '2026-10-06 02:48:27', NULL),
(13, 3, 3, NULL, 'ขอแนะนำช่องทางฝึก SQLi แบบปลอดภัยหน่อย', 'อยากฝึก SQL Injection แต่กลัวไปยิงระบบจริง มีแล็บใน CySkillShare หรือ DVWA แนะนำไหมคะ เริ่มจากง่ายไปยากยังไงดี', 'open', 0, 0, 0, '2026-10-06 02:48:05', '2026-10-06 02:48:05', NULL),
(14, 13, 4, NULL, 'Linux lab: เจอ process แปลกชื่อ kworker-xxxx', 'ตอนทำ Linux Security Investigation เห็น process ชื่อคล้าย kworker แต่ดูแปลกๆ ต้องไล่ยังไงต่อดีครับ? ใช้ ps กับ /proc พอไหม', 'open', 0, 0, 0, '2026-10-06 02:48:05', '2026-10-06 02:48:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `thread_skills`
--

CREATE TABLE `thread_skills` (
  `thread_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thread_skills`
--

INSERT INTO `thread_skills` (`thread_id`, `skill_id`, `weight`) VALUES
(3, 6, 0.40),
(3, 7, 1.00),
(3, 24, 0.50),
(6, 4, 0.50);

-- --------------------------------------------------------

--
-- Table structure for table `thread_tags`
--

CREATE TABLE `thread_tags` (
  `thread_id` bigint UNSIGNED NOT NULL,
  `tag_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thread_tags`
--

INSERT INTO `thread_tags` (`thread_id`, `tag_id`) VALUES
(3, 2),
(10, 2),
(13, 2),
(2, 3),
(6, 4),
(7, 4),
(14, 4),
(4, 5),
(2, 7),
(3, 7),
(11, 7),
(8, 10),
(4, 11),
(5, 11),
(5, 12),
(8, 13),
(9, 14),
(9, 15),
(6, 16),
(14, 16),
(2, 17),
(3, 17),
(13, 17);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_level` tinyint UNSIGNED DEFAULT NULL,
  `program` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `skills_visibility` enum('public','community','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `show_in_discovery` tinyint(1) NOT NULL DEFAULT '1',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','suspended','banned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `student_id`, `year_level`, `program`, `bio`, `skills_visibility`, `show_in_discovery`, `avatar`, `status`, `created_at`, `updated_at`, `last_login_at`) VALUES
(1, 'admin', 'admin@cyskillshare.local', '$2y$12$pYs5fOxUu6w7bk60eA1E8OjCCrhPjNBipVPoT.QkclyN9410DfMzi', 'System Administrator', NULL, NULL, 'College of Computing', 'Platform admin for CySkillShare development.', 'public', 1, NULL, 'active', '2026-10-03 16:06:19', '2026-10-06 03:25:07', '2026-10-06 03:25:07'),
(2, 'student1', 'student1@kkumail.com', '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q', 'สมชาย ใจดี', '653040001-1', 3, 'Cybersecurity', 'นักศึกษาวิทยาการคอมพิวเตอร์ มข. สนใจ Web Security และ CTF ชอบแชร์โน้ตหลังแล็บ', 'public', 1, NULL, 'active', '2026-10-03 16:06:19', '2026-10-06 02:48:05', '2026-10-06 02:42:04'),
(3, 'student2', 'student2@kkumail.com', '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q', 'พิมพ์ใจ รักเรียน', '653040002-2', 2, 'Cybersecurity', 'ปี 3 สาขาวิทยาการคอมพิวเตอร์ กำลังเรียนวิชา Database and Web Security ค่ะ', 'public', 1, NULL, 'active', '2026-10-03 16:06:19', '2026-10-06 02:48:05', '2026-10-03 16:07:18'),
(4, 'student3', 'student3@kkumail.com', '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q', 'ธนกร เครือข่าย', '653040003-3', 4, 'Computer Science', 'ชอบ Network Forensics กับ Wireshark กำลังฝึกทำ writeup เป็นภาษาไทย', 'public', 1, NULL, 'active', '2026-10-03 16:06:19', '2026-10-06 02:48:05', NULL),
(5, 'mentor1', 'mentor1@cyskillshare.local', '$2y$12$qqJ2B4.sHtBGy23YMuA6gOojg6oAPZd.iQ1MXbcYACL3utNGjQHTm', 'พี่เมนเทอร์ อานนท์', NULL, NULL, 'Cybersecurity', 'Peer mentor ช่วยน้องเรื่อง Linux, Web Security และเตรียมสอบ CTF', 'public', 1, NULL, 'active', '2026-10-03 16:06:19', '2026-10-06 03:22:27', '2026-10-06 03:22:27'),
(6, 'moderator1', 'moderator1@cyskillshare.local', '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q', 'Mod Pilot', NULL, NULL, 'Cybersecurity', 'Community moderator.', 'public', 1, NULL, 'active', '2026-10-03 16:06:19', '2026-10-03 16:06:19', NULL),
(7, 'instructor1', 'instructor1@cyskillshare.local', '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q', 'Dr. Kittipong', NULL, NULL, 'Cybersecurity', 'Instructor at College of Computing, KKU.', 'public', 1, NULL, 'active', '2026-10-03 16:06:19', '2026-10-03 16:07:19', '2026-10-03 16:07:19'),
(8, 'aloha', 'kroekkasit.a@kkumail.com', '$2y$10$nx1NEUam5fsaOlVZnBZ9iuwzcrdFUZB6731g/SwukJxuHy7kscsgK', 'Kroekkasit Aiadkaew', '673380493-5', NULL, NULL, 'asdasa', 'public', 1, NULL, 'active', '2026-10-04 10:25:47', '2026-10-06 02:23:52', '2026-10-06 02:18:45'),
(9, 'hello', 'kroekkasit.x@kkumail.com', '$2y$10$NFCZU46mb9W8wdcL65h3Te4ThhaW5DUSfl6f7I6IqeUnqlcCM0Lwm', 'asopdkap', '673380493-6', NULL, NULL, NULL, 'public', 1, NULL, 'active', '2026-10-04 10:27:10', '2026-10-04 10:27:10', '2026-10-04 10:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `user_blocks`
--

CREATE TABLE `user_blocks` (
  `blocker_id` bigint UNSIGNED NOT NULL,
  `blocked_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` bigint UNSIGNED NOT NULL,
  `role_id` int UNSIGNED NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`) VALUES
(1, 1, '2026-10-03 16:06:19'),
(1, 5, '2026-10-03 16:06:19'),
(2, 1, '2026-10-03 16:06:19'),
(3, 1, '2026-10-03 16:06:19'),
(4, 1, '2026-10-03 16:06:19'),
(5, 1, '2026-10-03 16:06:19'),
(5, 2, '2026-10-03 16:06:19'),
(6, 1, '2026-10-03 16:06:19'),
(6, 4, '2026-10-03 16:06:19'),
(7, 1, '2026-10-03 16:06:19'),
(7, 3, '2026-10-03 16:06:19'),
(8, 1, '2026-10-04 10:25:47'),
(9, 1, '2026-10-04 10:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `user_skills`
--

CREATE TABLE `user_skills` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `current_level` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `progress_score` int UNSIGNED NOT NULL DEFAULT '0',
  `evidence_count` int UNSIGNED NOT NULL DEFAULT '0',
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_skills`
--

INSERT INTO `user_skills` (`id`, `user_id`, `skill_id`, `current_level`, `progress_score`, `evidence_count`, `last_activity_at`, `created_at`, `updated_at`) VALUES
(1, 8, 1, 3, 50, 5, '2026-10-06 03:17:53', '2026-10-06 02:30:52', '2026-10-06 03:17:53'),
(2, 8, 5, 1, 40, 2, '2026-10-06 02:31:53', '2026-10-06 02:30:52', '2026-10-06 02:31:53'),
(6, 8, 19, 1, 60, 1, '2026-10-06 02:40:26', '2026-10-06 02:40:26', '2026-10-06 02:40:26'),
(7, 8, 2, 1, 47, 1, '2026-10-06 03:06:00', '2026-10-06 03:06:00', '2026-10-06 03:06:00'),
(10, 8, 13, 2, 40, 2, '2026-10-06 03:17:53', '2026-10-06 03:10:43', '2026-10-06 03:17:53'),
(12, 8, 6, 0, 0, 1, '2026-10-06 03:17:53', '2026-10-06 03:17:53', '2026-10-06 03:17:53'),
(14, 8, 8, 1, 47, 1, '2026-10-06 03:18:48', '2026-10-06 03:18:48', '2026-10-06 03:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` bigint UNSIGNED NOT NULL,
  `vote_type` enum('up','down') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `user_id`, `target_type`, `target_id`, `vote_type`, `created_at`) VALUES
(1, 5, 'thread', 2, 'up', '2026-10-03 16:06:19'),
(2, 2, 'thread', 2, 'up', '2026-10-03 16:06:19'),
(3, 4, 'thread', 2, 'up', '2026-10-03 16:06:19'),
(4, 5, 'thread', 3, 'up', '2026-10-03 16:06:19'),
(5, 6, 'thread', 3, 'up', '2026-10-03 16:06:19'),
(6, 3, 'thread', 3, 'up', '2026-10-03 16:06:19'),
(7, 8, 'thread', 1, 'up', '2026-10-06 03:39:48');

-- --------------------------------------------------------

--
-- Table structure for table `writeups`
--

CREATE TABLE `writeups` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_format` enum('markdown') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'markdown',
  `difficulty` enum('beginner','intermediate','advanced','expert') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beginner',
  `status` enum('draft','published','archived','under_review') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('public','community','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reading_time` int UNSIGNED NOT NULL DEFAULT '1',
  `view_count` int UNSIGNED NOT NULL DEFAULT '0',
  `helpful_count` int UNSIGNED NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `writeups`
--

INSERT INTO `writeups` (`id`, `user_id`, `category_id`, `title`, `slug`, `short_description`, `content`, `content_format`, `difficulty`, `status`, `visibility`, `cover_image`, `reading_time`, `view_count`, `helpful_count`, `featured`, `published_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, 'SQL Injection Beyond the Basics', 'sql-injection-beyond-the-basics', 'Moving past login bypass: second-order injection, blind techniques, and why prepared statements remain the fix.', '## Overview\n\nAfter completing the **SQL Injection Basics** challenge, I wanted to document the techniques that appear once error messages disappear and queries get more complex.\n\n## Second-order injection\n\nUser input stored safely on insert can still be dangerous when read back into a dynamic query:\n\n```php\n// Registration stores bound input\n$stmt = $pdo->prepare(\"INSERT INTO users (username) VALUES (?)\");\n$stmt->execute([$username]);\n\n// Later, a report builder concatenates without binding\n$query = \"SELECT * FROM logs WHERE user = \'\" . $username . \"\'\";\n```\n\nThe payload may sit dormant until a different code path executes it.\n\n## Blind boolean-based probing\n\nWhen the application returns identical pages for true and false conditions, infer answers one bit at a time:\n\n```sql\nadmin\' AND SUBSTRING((SELECT password FROM users LIMIT 1),1,1)=\'a\'-- -\n```\n\nCompare response length, timing, or subtle markup differences.\n\n## Mitigation checklist\n\n- Parameterized queries everywhere — including ORDER BY and dynamic identifiers where possible\n- Least-privilege DB accounts\n- Avoid displaying raw SQL errors in production\n\n## Lessons learned\n\nNever assume escaped storage equals safe reuse. Trace every place a value re-enters SQL.\n\n## Related community thread\n\nThis writeup complements the discussion on prepared statements in PHP.', 'markdown', 'intermediate', 'published', 'public', NULL, 8, 0, 0, 1, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20', NULL),
(2, 2, 3, 'Memory Analysis with Volatility', 'memory-analysis-with-volatility', 'Triaging a Windows memory dump with Volatility 3 plugins for process, network, and malware artifacts.', '## Lab setup\n\nAcquire a memory image from an isolated analysis VM. Verify integrity before processing:\n\n```bash\nsha256sum suspect-host.raw > suspect-host.raw.sha256\nvol -f suspect-host.raw windows.info\n```\n\n## Process enumeration\n\nIdentify unexpected processes and parent-child relationships:\n\n```bash\nvol -f suspect-host.raw windows.pslist\nvol -f suspect-host.raw windows.pstree\nvol -f suspect-host.raw windows.cmdline\n```\n\n## Network connections\n\nMap suspicious binaries to outbound connections:\n\n```bash\nvol -f suspect-host.raw windows.netscan\n```\n\n## Lessons learned\n\nMemory analysis captures ephemeral artifacts — injected code, decrypted strings, and active C2 sessions — that disk forensics may miss. Document your plugin order and timestamps for reproducibility.', 'markdown', 'intermediate', 'published', 'public', NULL, 6, 0, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20', NULL),
(3, 2, 4, 'Analyzing a Suspicious PE File', 'analyzing-a-suspicious-pe-file', 'Static triage of a Windows PE sample: headers, imports, strings, and packing indicators.', '## Sample information\n\nHash the sample before analysis. Work only inside an isolated lab VM.\n\n```bash\nfile sample.bin\nsha256sum sample.bin\n```\n\n## Static analysis\n\nInspect PE headers, sections, and imports without executing the binary:\n\n```bash\nobjdump -x sample.bin | head\nstrings -n 8 sample.bin | head -n 50\n```\n\n## Behavioral analysis (sandbox notes)\n\nObserve process creation, file drops, and registry persistence. Record IOCs for later hunting.\n\n## Lessons learned\n\nStatic analysis narrows hypotheses; dynamic analysis confirms behavior. Never run unknown samples on a production host.\n\n## References\n\n- Microsoft PE format documentation\n- MITRE ATT&CK persistence techniques', 'markdown', 'advanced', 'published', 'public', NULL, 10, 0, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-03 16:06:20', NULL),
(4, 3, 2, 'Understanding ARP Spoofing', 'understanding-arp-spoofing', 'How ARP poisoning works on a local network and how defenders detect abnormal MAC-IP bindings.', '## Background\n\nARP maps IP addresses to MAC addresses on a LAN. It has no built-in authentication.\n\n## Attack overview\n\nAn attacker claims to own the gateway IP, poisoning neighbor caches so traffic flows through the attacker host.\n\n## Detection\n\nWatch for duplicate IP-to-MAC bindings and sudden gateway MAC changes:\n\n```bash\narp -an\n# Compare against a known-good baseline\n```\n\n## Mitigation\n\n- Dynamic ARP inspection on managed switches\n- Static ARP for critical hosts where practical\n- Network segmentation\n\n## Lessons learned\n\nLayer-2 trust assumptions break in shared networks. Monitoring ARP anomalies is a practical first step.', 'markdown', 'beginner', 'published', 'public', NULL, 5, 3, 0, 0, '2026-10-03 16:06:20', '2026-10-03 16:06:20', '2026-10-04 14:40:52', NULL),
(5, 2, 1, 'Draft: JWT Pitfalls Lab Notes', 'draft-jwt-pitfalls-lab-notes', 'Work-in-progress notes on alg=none and weak HMAC secrets.', '## WIP\n\nNotes from the JWT lab. Not ready for publication.\n\n```http\nPOST /login HTTP/1.1\nHost: example.local\nContent-Type: application/json\n\n{\"username\":\"student\",\"password\":\"...\"}\n```\n\nStill need to add verification steps and screenshots.', 'markdown', 'intermediate', 'draft', 'private', NULL, 3, 0, 0, 0, NULL, '2026-10-03 16:06:20', '2026-10-03 16:06:20', NULL),
(6, 8, NULL, 'asdadasaa', 'sql-injection-writeup', 'sdosjfodjs', 'dsdofiajoifj', 'markdown', 'beginner', 'draft', 'public', NULL, 1, 0, 0, 0, NULL, '2026-10-06 02:25:32', '2026-10-06 02:25:32', NULL),
(7, 3, 1, 'บันทึกแล็บ: SQL Injection ฉบับมือใหม่', 'sql-injection-lab-notes-th', 'สรุปภาษาไทยจากแล็บเว็บที่มีช่องโหว่ — วิธีหาจุด inject และกันด้วย PDO', '## ทำไมถึงสำคัญ\n\nSQL Injection ยังเจอบ่อยในโปรเจกต์นักศึกษา โดยเฉพาะตอนต่อสตริง SQL เอง\n\n## สิ่งที่ลองในแล็บ\n\n1. เปิดหน้า login แล้วลองใส่ `\' OR \'1\'=\'1` แบบควบคุมในแล็บเท่านั้น\n2. สังเกตว่าผลลัพธ์เปลี่ยนเมื่อเงื่อนไขเป็นจริง\n3. เปิด access log หา request ที่ผิดปกติ\n\n## วิธีแก้ที่ถูกต้อง\n\n```php\n$stmt = $pdo->prepare(\'SELECT id FROM users WHERE username = ? AND password_hash = ?\');\n$stmt->execute([$username, $hash]);\n```\n\nอย่าแสดง SQL error ให้ผู้ใช้ทั่วไปเห็น\n\n## สรุปสั้นๆ\n\n- ใช้ prepared statements ทุกจุดที่รับค่าจากผู้ใช้\n- validate เป็นชั้นเสริม ไม่ใช่เกราะหลัก\n- บัญชีฐานข้อมูลควร least privilege\n\nเขียนไว้เป็นโน้ตหลังแล็บ เผื่อเพื่อนปีเดียวกันอ่านเข้าใจง่ายครับ', 'markdown', 'beginner', 'published', 'public', NULL, 5, 0, 0, 0, '2026-10-06 02:48:05', '2026-10-06 02:48:05', '2026-10-06 02:48:05', NULL),
(8, 4, 8, 'โน้ต Linux: สิทธิ์ไฟล์กับ SUID ที่ต้องระวัง', 'linux-permissions-notes-th', 'สรุป chmod/chown และทำไม SUID บนอินเทอร์พรีเตอร์ถึงอันตราย — ภาษาไทย', '## พื้นฐานที่ใช้อยู่ทุกวัน\n\n```bash\nls -l secret.conf\nchmod 600 secret.conf   # เจ้าของอ่าน/เขียนได้อย่างเดียว\nchmod 644 readme.md     # คนอื่นอ่านได้ เหมาะกับไฟล์สาธารณะ\n```\n\nไฟล์ที่มีรหัสผ่านหรือคีย์ ไม่ควรเป็น 644\n\n## SUID\n\nถ้าไบนารีมีบิต SUID ผู้ใช้ที่รันจะได้สิทธิ์เจ้าของไฟล์ชั่วคราว\n\n```bash\nfind / -perm -4000 2>/dev/null\n```\n\nในแล็บ Linux Permissions ให้หา SUID ที่ตั้งผิด แล้วอ่านแฟล็ก\n\n## สิ่งที่จำไว้\n\n- อย่าใส่ SUID ให้สคริปต์หรืออินเทอร์พรีเตอร์มั่ว\n- ใช้ least privilege\n- จดคำสั่งที่ใช้ในรายงานแล็บให้ครบ\n\nหวังว่าเพื่อนๆ จะเอาไปใช้ตอนทำ assignment ได้ครับ', 'markdown', 'beginner', 'published', 'public', NULL, 4, 1, 0, 0, '2026-10-06 02:48:05', '2026-10-06 02:48:05', '2026-10-06 03:58:05', NULL),
(9, 8, 3, 'my writeupp', 'https-github-com-kroekkasit-cyskillshare', 'saddadaasdas', 'ddadasda', 'markdown', 'advanced', 'published', 'community', NULL, 1, 0, 0, 0, '2026-10-06 10:06:00', '2026-10-06 03:06:00', '2026-10-06 03:06:00', NULL),
(10, 8, 8, 'Writeup: สืบสวนความปลอดภัยบน Linux', 'sadadada', 'Lab walkthrough for สืบสวนความปลอดภัยบน Linux', 'asdad', 'markdown', 'intermediate', 'draft', 'public', NULL, 1, 0, 0, 0, NULL, '2026-10-06 03:10:58', '2026-10-06 03:10:58', NULL),
(11, 8, 12, 'Writeup: Web Server Compromise', 'writeup-web-server-compromise', 'Lab walkthrough for Web Server Compromise', 'sadaadsaadad', 'markdown', 'advanced', 'draft', 'public', NULL, 1, 0, 0, 0, NULL, '2026-10-06 03:18:00', '2026-10-06 03:18:00', NULL),
(12, 8, 8, 'adadsad', 'https-github-com-kroekkasit-cyskillshare-2', 'sadadaa', '## Challenge\r\n\r\n**Difficulty:**\r\n**Category:**\r\n\r\n## 1. Reconnaissance\r\n\r\n## 2. Initial Analysis\r\n\r\n## 3. Vulnerability\r\n\r\n## 4. Exploitation\r\n\r\n## 5. Solution\r\n\r\n## 6. Lessons Learned\r\n', 'markdown', 'advanced', 'published', 'public', NULL, 1, 0, 0, 0, '2026-10-06 10:18:48', '2026-10-06 03:18:48', '2026-10-06 03:18:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `writeup_categories`
--

CREATE TABLE `writeup_categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `writeup_categories`
--

INSERT INTO `writeup_categories` (`id`, `name`, `slug`, `description`, `display_order`, `is_active`, `created_at`) VALUES
(1, 'Web Security', 'web-security', NULL, 10, 1, '2026-10-03 16:06:20'),
(2, 'Network Security', 'network-security', NULL, 20, 1, '2026-10-03 16:06:20'),
(3, 'Digital Forensics', 'digital-forensics', NULL, 30, 1, '2026-10-03 16:06:20'),
(4, 'Malware Analysis', 'malware-analysis', NULL, 40, 1, '2026-10-03 16:06:20'),
(5, 'Reverse Engineering', 'reverse-engineering', NULL, 50, 1, '2026-10-03 16:06:20'),
(6, 'OSINT', 'osint', NULL, 60, 1, '2026-10-03 16:06:20'),
(7, 'Cryptography', 'cryptography', NULL, 70, 1, '2026-10-03 16:06:20'),
(8, 'Linux', 'linux', NULL, 80, 1, '2026-10-03 16:06:20'),
(9, 'Windows', 'windows', NULL, 90, 1, '2026-10-03 16:06:20'),
(10, 'Cloud Security', 'cloud-security', NULL, 100, 1, '2026-10-03 16:06:20'),
(11, 'Secure Coding', 'secure-coding', NULL, 110, 1, '2026-10-03 16:06:20'),
(12, 'Incident Response', 'incident-response', NULL, 120, 1, '2026-10-03 16:06:20'),
(13, 'CTF Writeup', 'ctf-writeup', NULL, 130, 1, '2026-10-03 16:06:20'),
(14, 'Tutorial', 'tutorial', NULL, 140, 1, '2026-10-03 16:06:20'),
(15, 'Case Study', 'case-study', NULL, 150, 1, '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `writeup_challenges`
--

CREATE TABLE `writeup_challenges` (
  `writeup_id` bigint UNSIGNED NOT NULL,
  `challenge_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `writeup_challenges`
--

INSERT INTO `writeup_challenges` (`writeup_id`, `challenge_id`, `created_at`) VALUES
(1, 1, '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `writeup_labs`
--

CREATE TABLE `writeup_labs` (
  `writeup_id` bigint UNSIGNED NOT NULL,
  `lab_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `writeup_labs`
--

INSERT INTO `writeup_labs` (`writeup_id`, `lab_id`, `created_at`) VALUES
(1, 1, '2026-10-03 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `writeup_skills`
--

CREATE TABLE `writeup_skills` (
  `writeup_id` bigint UNSIGNED NOT NULL,
  `skill_id` int UNSIGNED NOT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `writeup_skills`
--

INSERT INTO `writeup_skills` (`writeup_id`, `skill_id`, `weight`, `created_at`) VALUES
(1, 6, 1.00, '2026-10-03 16:06:20'),
(1, 7, 1.00, '2026-10-03 16:06:20'),
(1, 24, 1.00, '2026-10-03 16:06:20'),
(2, 16, 1.00, '2026-10-03 16:06:20'),
(2, 19, 1.00, '2026-10-03 16:06:20'),
(3, 19, 1.00, '2026-10-03 16:06:20'),
(3, 20, 1.00, '2026-10-03 16:06:20'),
(4, 11, 1.00, '2026-10-03 16:06:20'),
(6, 7, 1.00, '2026-10-06 02:25:32'),
(7, 7, 1.00, '2026-10-06 02:48:05'),
(8, 1, 1.00, '2026-10-06 02:48:05'),
(9, 2, 1.00, '2026-10-06 03:06:00'),
(12, 8, 1.00, '2026-10-06 03:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `writeup_tags`
--

CREATE TABLE `writeup_tags` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `writeup_tags`
--

INSERT INTO `writeup_tags` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'SQL Injection', 'sql-injection', NULL, '2026-10-03 16:06:20'),
(2, 'XSS', 'xss', NULL, '2026-10-03 16:06:20'),
(3, 'Burp Suite', 'burp-suite', NULL, '2026-10-03 16:06:20'),
(4, 'Nmap', 'nmap', NULL, '2026-10-03 16:06:20'),
(5, 'Wireshark', 'wireshark', NULL, '2026-10-03 16:06:20'),
(6, 'Volatility', 'volatility', NULL, '2026-10-03 16:06:20'),
(7, 'Ghidra', 'ghidra', NULL, '2026-10-03 16:06:20'),
(8, 'Linux', 'linux', NULL, '2026-10-03 16:06:20'),
(9, 'JWT', 'jwt', NULL, '2026-10-03 16:06:20'),
(10, 'PHP', 'php', NULL, '2026-10-03 16:06:20'),
(11, 'Apache', 'apache', NULL, '2026-10-03 16:06:20'),
(12, 'Docker', 'docker', NULL, '2026-10-03 16:06:20'),
(13, 'CSRF', 'csrf', NULL, '2026-10-03 16:06:20'),
(14, 'Networking', 'networking', NULL, '2026-10-03 16:06:20'),
(15, 'Forensics', 'forensics', NULL, '2026-10-03 16:06:20'),
(16, 'Windows', 'windows', NULL, '2026-10-03 16:06:20'),
(17, 'web', 'web', NULL, '2026-10-06 02:25:32');

-- --------------------------------------------------------

--
-- Table structure for table `writeup_tag_map`
--

CREATE TABLE `writeup_tag_map` (
  `writeup_id` bigint UNSIGNED NOT NULL,
  `tag_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `writeup_tag_map`
--

INSERT INTO `writeup_tag_map` (`writeup_id`, `tag_id`) VALUES
(1, 1),
(7, 1),
(1, 3),
(4, 4),
(4, 5),
(2, 6),
(3, 7),
(1, 10),
(7, 10),
(4, 14),
(2, 15),
(2, 16),
(6, 17),
(9, 17),
(12, 17);

-- --------------------------------------------------------

--
-- Table structure for table `writeup_threads`
--

CREATE TABLE `writeup_threads` (
  `writeup_id` bigint UNSIGNED NOT NULL,
  `thread_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `writeup_versions`
--

CREATE TABLE `writeup_versions` (
  `id` bigint UNSIGNED NOT NULL,
  `writeup_id` bigint UNSIGNED NOT NULL,
  `version` int UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `edited_by` bigint UNSIGNED NOT NULL,
  `change_summary` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_index` (`user_id`),
  ADD KEY `activity_logs_action_index` (`action`),
  ADD KEY `activity_logs_created_at_index` (`created_at`);

--
-- Indexes for table `arena_events`
--
ALTER TABLE `arena_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `arena_events_slug_unique` (`slug`),
  ADD KEY `arena_events_status_index` (`status`),
  ADD KEY `arena_events_start_at_index` (`start_at`),
  ADD KEY `arena_events_end_at_index` (`end_at`),
  ADD KEY `arena_events_created_by_index` (`created_by`);

--
-- Indexes for table `arena_event_challenges`
--
ALTER TABLE `arena_event_challenges`
  ADD PRIMARY KEY (`event_id`,`challenge_id`),
  ADD KEY `arena_event_challenges_challenge_id_index` (`challenge_id`);

--
-- Indexes for table `arena_point_transactions`
--
ALTER TABLE `arena_point_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arena_point_transactions_user_id_index` (`user_id`),
  ADD KEY `arena_point_transactions_created_at_index` (`created_at`),
  ADD KEY `arena_point_transactions_challenge_id_index` (`challenge_id`),
  ADD KEY `arena_point_transactions_event_id_index` (`event_id`),
  ADD KEY `arena_point_transactions_hint_id_index` (`hint_id`);

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookmarks_user_target_unique` (`user_id`,`target_type`,`target_id`),
  ADD KEY `bookmarks_target_index` (`target_type`,`target_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_sort_order_index` (`sort_order`);

--
-- Indexes for table `challenges`
--
ALTER TABLE `challenges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `challenges_slug_unique` (`slug`),
  ADD KEY `challenges_category_id_index` (`category_id`),
  ADD KEY `challenges_status_index` (`status`),
  ADD KEY `challenges_difficulty_index` (`difficulty`),
  ADD KEY `challenges_created_at_index` (`created_at`),
  ADD KEY `challenges_is_featured_index` (`is_featured`),
  ADD KEY `challenges_is_active_index` (`is_active`),
  ADD KEY `challenges_author_id_index` (`author_id`);

--
-- Indexes for table `challenge_categories`
--
ALTER TABLE `challenge_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `challenge_categories_slug_unique` (`slug`),
  ADD KEY `challenge_categories_sort_order_index` (`sort_order`),
  ADD KEY `challenge_categories_is_active_index` (`is_active`);

--
-- Indexes for table `challenge_files`
--
ALTER TABLE `challenge_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `challenge_files_challenge_id_index` (`challenge_id`);

--
-- Indexes for table `challenge_hints`
--
ALTER TABLE `challenge_hints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `challenge_hints_challenge_id_index` (`challenge_id`),
  ADD KEY `challenge_hints_challenge_order_index` (`challenge_id`,`hint_order`);

--
-- Indexes for table `challenge_hint_usage`
--
ALTER TABLE `challenge_hint_usage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `challenge_hint_usage_user_hint_unique` (`user_id`,`hint_id`),
  ADD KEY `challenge_hint_usage_challenge_id_index` (`challenge_id`),
  ADD KEY `challenge_hint_usage_hint_id_index` (`hint_id`),
  ADD KEY `challenge_hint_usage_user_id_index` (`user_id`);

--
-- Indexes for table `challenge_skills`
--
ALTER TABLE `challenge_skills`
  ADD PRIMARY KEY (`challenge_id`,`skill_id`),
  ADD KEY `challenge_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `challenge_solves`
--
ALTER TABLE `challenge_solves`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `challenge_solves_challenge_user_unique` (`challenge_id`,`user_id`),
  ADD KEY `challenge_solves_user_id_index` (`user_id`),
  ADD KEY `challenge_solves_solved_at_index` (`solved_at`);

--
-- Indexes for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `challenge_submissions_challenge_id_index` (`challenge_id`),
  ADD KEY `challenge_submissions_user_id_index` (`user_id`),
  ADD KEY `challenge_submissions_submitted_at_index` (`submitted_at`),
  ADD KEY `challenge_submissions_user_challenge_index` (`user_id`,`challenge_id`);

--
-- Indexes for table `challenge_tags`
--
ALTER TABLE `challenge_tags`
  ADD PRIMARY KEY (`challenge_id`,`tag_id`),
  ADD KEY `challenge_tags_tag_id_index` (`tag_id`);

--
-- Indexes for table `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `channels_slug_unique` (`slug`),
  ADD KEY `channels_category_id_index` (`category_id`),
  ADD KEY `channels_sort_order_index` (`sort_order`);

--
-- Indexes for table `collab_groups`
--
ALTER TABLE `collab_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `collab_groups_slug_unique` (`slug`),
  ADD KEY `collab_groups_owner_id_index` (`owner_id`),
  ADD KEY `collab_groups_type_index` (`group_type`),
  ADD KEY `collab_groups_status_index` (`status`),
  ADD KEY `collab_groups_visibility_index` (`visibility`),
  ADD KEY `collab_groups_channel_id_fk` (`channel_id`);
ALTER TABLE `collab_groups` ADD FULLTEXT KEY `collab_groups_ft_search` (`name`,`description`);

--
-- Indexes for table `collab_group_activities`
--
ALTER TABLE `collab_group_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_group_activities_group_id_index` (`group_id`),
  ADD KEY `collab_group_activities_scheduled_at_index` (`scheduled_at`),
  ADD KEY `collab_group_activities_created_by_fk` (`created_by`);

--
-- Indexes for table `collab_group_applications`
--
ALTER TABLE `collab_group_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_group_applications_group_id_index` (`group_id`),
  ADD KEY `collab_group_applications_user_id_index` (`user_id`),
  ADD KEY `collab_group_applications_role_id_fk` (`role_id`),
  ADD KEY `collab_group_applications_reviewed_by_fk` (`reviewed_by`);

--
-- Indexes for table `collab_group_goals`
--
ALTER TABLE `collab_group_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_group_goals_group_id_index` (`group_id`),
  ADD KEY `collab_group_goals_created_by_fk` (`created_by`);

--
-- Indexes for table `collab_group_invitations`
--
ALTER TABLE `collab_group_invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `collab_group_invitations_token_unique` (`token`),
  ADD KEY `collab_group_invitations_invitee_index` (`invitee_id`,`status`),
  ADD KEY `collab_group_invitations_group_id_fk` (`group_id`),
  ADD KEY `collab_group_invitations_inviter_id_fk` (`inviter_id`);

--
-- Indexes for table `collab_group_join_requests`
--
ALTER TABLE `collab_group_join_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_group_join_requests_group_user_index` (`group_id`,`user_id`),
  ADD KEY `collab_group_join_requests_user_id_index` (`user_id`),
  ADD KEY `collab_group_join_requests_status_index` (`status`),
  ADD KEY `collab_group_join_requests_reviewed_by_fk` (`reviewed_by`);

--
-- Indexes for table `collab_group_members`
--
ALTER TABLE `collab_group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `collab_group_members_unique` (`group_id`,`user_id`),
  ADD KEY `collab_group_members_user_id_index` (`user_id`);

--
-- Indexes for table `collab_group_resources`
--
ALTER TABLE `collab_group_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_group_resources_group_id_index` (`group_id`),
  ADD KEY `collab_group_resources_created_by_fk` (`created_by`);

--
-- Indexes for table `collab_group_roles`
--
ALTER TABLE `collab_group_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_group_roles_group_id_index` (`group_id`);

--
-- Indexes for table `collab_group_skills`
--
ALTER TABLE `collab_group_skills`
  ADD PRIMARY KEY (`group_id`,`skill_id`),
  ADD KEY `collab_group_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `content_reactions`
--
ALTER TABLE `content_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `content_reactions_unique` (`user_id`,`content_type`,`content_id`,`reaction_type`),
  ADD KEY `content_reactions_content_index` (`content_type`,`content_id`);

--
-- Indexes for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `knowledge_articles_slug_unique` (`slug`),
  ADD KEY `knowledge_articles_author_id_index` (`author_id`),
  ADD KEY `knowledge_articles_category_id_index` (`category_id`),
  ADD KEY `knowledge_articles_status_index` (`status`),
  ADD KEY `knowledge_articles_featured_index` (`featured`);
ALTER TABLE `knowledge_articles` ADD FULLTEXT KEY `knowledge_articles_ft_search` (`title`,`summary`,`content`);

--
-- Indexes for table `knowledge_article_contributors`
--
ALTER TABLE `knowledge_article_contributors`
  ADD PRIMARY KEY (`article_id`,`user_id`,`role`),
  ADD KEY `knowledge_article_contributors_user_id_fk` (`user_id`);

--
-- Indexes for table `knowledge_article_reviews`
--
ALTER TABLE `knowledge_article_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `knowledge_article_reviews_article_id_index` (`article_id`),
  ADD KEY `knowledge_article_reviews_status_index` (`status`),
  ADD KEY `knowledge_article_reviews_reviewer_id_fk` (`reviewer_id`);

--
-- Indexes for table `knowledge_article_skills`
--
ALTER TABLE `knowledge_article_skills`
  ADD PRIMARY KEY (`article_id`,`skill_id`),
  ADD KEY `knowledge_article_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `knowledge_article_sources`
--
ALTER TABLE `knowledge_article_sources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `knowledge_article_sources_article_id_index` (`article_id`);

--
-- Indexes for table `knowledge_article_versions`
--
ALTER TABLE `knowledge_article_versions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `knowledge_article_versions_unique` (`article_id`,`version`),
  ADD KEY `knowledge_article_versions_edited_by_fk` (`edited_by`);

--
-- Indexes for table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `labs_slug_unique` (`slug`),
  ADD KEY `labs_category_id_index` (`category_id`),
  ADD KEY `labs_status_index` (`status`),
  ADD KEY `labs_visibility_index` (`visibility`),
  ADD KEY `labs_featured_index` (`featured`),
  ADD KEY `labs_author_id_index` (`author_id`),
  ADD KEY `labs_template_id_fk` (`template_id`);
ALTER TABLE `labs` ADD FULLTEXT KEY `labs_ft_search` (`title`,`short_description`,`description`);

--
-- Indexes for table `lab_attempts`
--
ALTER TABLE `lab_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_attempts_progress_id_index` (`progress_id`),
  ADD KEY `lab_attempts_submitted_at_index` (`submitted_at`);

--
-- Indexes for table `lab_categories`
--
ALTER TABLE `lab_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_categories_slug_unique` (`slug`);

--
-- Indexes for table `lab_completions`
--
ALTER TABLE `lab_completions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_completions_user_lab_unique` (`user_id`,`lab_id`),
  ADD KEY `lab_completions_lab_id_index` (`lab_id`),
  ADD KEY `lab_completions_instance_id_index` (`instance_id`);

--
-- Indexes for table `lab_feedback`
--
ALTER TABLE `lab_feedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_feedback_unique` (`lab_id`,`user_id`),
  ADD KEY `lab_feedback_user_id_index` (`user_id`);

--
-- Indexes for table `lab_hint_usage`
--
ALTER TABLE `lab_hint_usage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_hint_usage_unique` (`instance_id`,`hint_id`),
  ADD KEY `lab_hint_usage_user_id_index` (`user_id`),
  ADD KEY `lab_hint_usage_hint_id_fk` (`hint_id`);

--
-- Indexes for table `lab_instances`
--
ALTER TABLE `lab_instances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_instances_identifier_unique` (`instance_identifier`),
  ADD KEY `lab_instances_user_id_index` (`user_id`),
  ADD KEY `lab_instances_lab_id_index` (`lab_id`),
  ADD KEY `lab_instances_status_index` (`status`),
  ADD KEY `lab_instances_expires_at_index` (`expires_at`);

--
-- Indexes for table `lab_prerequisites`
--
ALTER TABLE `lab_prerequisites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_prerequisites_unique` (`lab_id`,`skill_id`),
  ADD KEY `lab_prerequisites_skill_id_index` (`skill_id`);

--
-- Indexes for table `lab_progress`
--
ALTER TABLE `lab_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_progress_unique` (`instance_id`,`task_id`),
  ADD KEY `lab_progress_task_id_index` (`task_id`);

--
-- Indexes for table `lab_services`
--
ALTER TABLE `lab_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_services_lab_id_index` (`lab_id`);

--
-- Indexes for table `lab_skills`
--
ALTER TABLE `lab_skills`
  ADD PRIMARY KEY (`lab_id`,`skill_id`),
  ADD KEY `lab_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `lab_tasks`
--
ALTER TABLE `lab_tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_tasks_lab_slug_unique` (`lab_id`,`slug`),
  ADD KEY `lab_tasks_lab_order_index` (`lab_id`,`display_order`);

--
-- Indexes for table `lab_task_dependencies`
--
ALTER TABLE `lab_task_dependencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_task_dependencies_unique` (`task_id`,`depends_on_task_id`),
  ADD KEY `lab_task_dependencies_depends_index` (`depends_on_task_id`);

--
-- Indexes for table `lab_task_hints`
--
ALTER TABLE `lab_task_hints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_task_hints_task_id_index` (`task_id`);

--
-- Indexes for table `lab_task_validations`
--
ALTER TABLE `lab_task_validations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_task_validations_task_unique` (`task_id`);

--
-- Indexes for table `lab_templates`
--
ALTER TABLE `lab_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_templates_slug_unique` (`slug`);

--
-- Indexes for table `mentors`
--
ALTER TABLE `mentors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mentors_user_id_unique` (`user_id`),
  ADD KEY `mentors_accepting_index` (`accepting_requests`),
  ADD KEY `mentors_verification_index` (`verification_status`),
  ADD KEY `mentors_verified_by_fk` (`verified_by`);

--
-- Indexes for table `mentorships`
--
ALTER TABLE `mentorships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentorships_mentor_id_index` (`mentor_id`),
  ADD KEY `mentorships_mentee_id_index` (`mentee_id`),
  ADD KEY `mentorships_status_index` (`status`);

--
-- Indexes for table `mentorship_feedback`
--
ALTER TABLE `mentorship_feedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mentorship_feedback_unique` (`session_id`,`from_user_id`),
  ADD KEY `mentorship_feedback_from_user_id_fk` (`from_user_id`),
  ADD KEY `mentorship_feedback_to_user_id_fk` (`to_user_id`);

--
-- Indexes for table `mentorship_goals`
--
ALTER TABLE `mentorship_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentorship_goals_mentorship_id_index` (`mentorship_id`),
  ADD KEY `mentorship_goals_skill_id_fk` (`skill_id`);

--
-- Indexes for table `mentorship_sessions`
--
ALTER TABLE `mentorship_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentorship_sessions_mentorship_id_index` (`mentorship_id`),
  ADD KEY `mentorship_sessions_scheduled_at_index` (`scheduled_at`);

--
-- Indexes for table `mentorship_skills`
--
ALTER TABLE `mentorship_skills`
  ADD PRIMARY KEY (`mentorship_id`,`skill_id`),
  ADD KEY `mentorship_skills_skill_id_fk` (`skill_id`);

--
-- Indexes for table `mentor_skills`
--
ALTER TABLE `mentor_skills`
  ADD PRIMARY KEY (`mentor_id`,`skill_id`),
  ADD KEY `mentor_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_index` (`user_id`),
  ADD KEY `notifications_is_read_index` (`is_read`),
  ADD KEY `notifications_user_unread_index` (`user_id`,`is_read`);

--
-- Indexes for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `portfolios_user_id_unique` (`user_id`),
  ADD KEY `portfolios_visibility_index` (`visibility`);

--
-- Indexes for table `portfolio_analytics_daily`
--
ALTER TABLE `portfolio_analytics_daily`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `portfolio_analytics_unique` (`user_id`,`metric`,`metric_date`);

--
-- Indexes for table `portfolio_certifications`
--
ALTER TABLE `portfolio_certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `portfolio_certifications_user_id_index` (`user_id`);

--
-- Indexes for table `portfolio_education`
--
ALTER TABLE `portfolio_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `portfolio_education_user_id_index` (`user_id`);

--
-- Indexes for table `portfolio_experience`
--
ALTER TABLE `portfolio_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `portfolio_experience_user_id_index` (`user_id`);

--
-- Indexes for table `portfolio_featured_skills`
--
ALTER TABLE `portfolio_featured_skills`
  ADD PRIMARY KEY (`user_id`,`skill_id`),
  ADD KEY `portfolio_featured_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `portfolio_featured_writeups`
--
ALTER TABLE `portfolio_featured_writeups`
  ADD PRIMARY KEY (`user_id`,`writeup_id`),
  ADD KEY `portfolio_featured_writeups_writeup_id_fk` (`writeup_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_user_slug_unique` (`user_id`,`slug`),
  ADD KEY `projects_visibility_index` (`visibility`),
  ADD KEY `projects_featured_index` (`featured`),
  ADD KEY `projects_publish_status_index` (`publish_status`),
  ADD KEY `projects_status_index` (`status`),
  ADD KEY `projects_created_at_index` (`created_at`);

--
-- Indexes for table `project_challenges`
--
ALTER TABLE `project_challenges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_challenges_unique` (`project_id`,`challenge_id`),
  ADD KEY `project_challenges_challenge_id_index` (`challenge_id`);

--
-- Indexes for table `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_images_project_id_index` (`project_id`);

--
-- Indexes for table `project_labs`
--
ALTER TABLE `project_labs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_labs_unique` (`project_id`,`lab_id`),
  ADD KEY `project_labs_lab_id_index` (`lab_id`);

--
-- Indexes for table `project_reactions`
--
ALTER TABLE `project_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_reactions_unique` (`project_id`,`user_id`,`reaction_type`),
  ADD KEY `project_reactions_user_id_index` (`user_id`);

--
-- Indexes for table `project_skills`
--
ALTER TABLE `project_skills`
  ADD PRIMARY KEY (`project_id`,`skill_id`),
  ADD KEY `project_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `project_technologies`
--
ALTER TABLE `project_technologies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_technologies_unique` (`project_id`,`technology`),
  ADD KEY `project_technologies_project_id_index` (`project_id`);

--
-- Indexes for table `project_verifications`
--
ALTER TABLE `project_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_verifications_project_id_index` (`project_id`),
  ADD KEY `project_verifications_status_index` (`status`),
  ADD KEY `project_verifications_verified_by_fk` (`verified_by`),
  ADD KEY `project_verifications_requested_by_fk` (`requested_by`);

--
-- Indexes for table `project_writeups`
--
ALTER TABLE `project_writeups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_writeups_unique` (`project_id`,`writeup_id`),
  ADD KEY `project_writeups_writeup_id_index` (`writeup_id`);

--
-- Indexes for table `recruitment_applications`
--
ALTER TABLE `recruitment_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recruitment_applications_unique` (`recruitment_id`,`user_id`),
  ADD KEY `recruitment_applications_user_id_index` (`user_id`),
  ADD KEY `recruitment_applications_reviewed_by_fk` (`reviewed_by`);

--
-- Indexes for table `recruitment_posts`
--
ALTER TABLE `recruitment_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recruitment_posts_creator_id_index` (`creator_id`),
  ADD KEY `recruitment_posts_status_index` (`status`),
  ADD KEY `recruitment_posts_group_id_index` (`group_id`);

--
-- Indexes for table `recruitment_skills`
--
ALTER TABLE `recruitment_skills`
  ADD PRIMARY KEY (`recruitment_id`,`skill_id`),
  ADD KEY `recruitment_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `replies_thread_id_index` (`thread_id`),
  ADD KEY `replies_user_id_index` (`user_id`),
  ADD KEY `replies_created_at_index` (`created_at`),
  ADD KEY `replies_parent_reply_id_index` (`parent_reply_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_status_index` (`status`),
  ADD KEY `reports_target_index` (`target_type`,`target_id`),
  ADD KEY `reports_reporter_id_index` (`reporter_id`),
  ADD KEY `reports_reviewed_by_fk` (`reviewed_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `skills_slug_unique` (`slug`),
  ADD KEY `skills_category_id_index` (`category_id`),
  ADD KEY `skills_parent_skill_id_index` (`parent_skill_id`),
  ADD KEY `skills_display_order_index` (`display_order`);

--
-- Indexes for table `skill_categories`
--
ALTER TABLE `skill_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `skill_categories_slug_unique` (`slug`),
  ADD KEY `skill_categories_display_order_index` (`display_order`);

--
-- Indexes for table `skill_evidence`
--
ALTER TABLE `skill_evidence`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `skill_evidence_unique_source` (`user_id`,`source_type`,`source_id`,`skill_id`),
  ADD KEY `skill_evidence_user_id_index` (`user_id`),
  ADD KEY `skill_evidence_skill_id_index` (`skill_id`),
  ADD KEY `skill_evidence_status_index` (`status`),
  ADD KEY `skill_evidence_evidence_type_index` (`evidence_type`),
  ADD KEY `skill_evidence_created_at_index` (`created_at`),
  ADD KEY `skill_evidence_verified_by_fk` (`verified_by`);

--
-- Indexes for table `skill_levels`
--
ALTER TABLE `skill_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `skill_levels_level_unique` (`level`);

--
-- Indexes for table `skill_prerequisites`
--
ALTER TABLE `skill_prerequisites`
  ADD PRIMARY KEY (`skill_id`,`prerequisite_skill_id`),
  ADD KEY `skill_prerequisites_prereq_index` (`prerequisite_skill_id`);

--
-- Indexes for table `skill_requirements`
--
ALTER TABLE `skill_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `skill_requirements_skill_id_index` (`skill_id`),
  ADD KEY `skill_requirements_target_level_index` (`target_level`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tags_name_unique` (`name`),
  ADD UNIQUE KEY `tags_slug_unique` (`slug`);

--
-- Indexes for table `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `threads_channel_id_index` (`channel_id`),
  ADD KEY `threads_user_id_index` (`user_id`),
  ADD KEY `threads_challenge_id_index` (`challenge_id`),
  ADD KEY `threads_status_index` (`status`),
  ADD KEY `threads_created_at_index` (`created_at`),
  ADD KEY `threads_deleted_at_index` (`deleted_at`),
  ADD KEY `threads_pinned_created_index` (`is_pinned`,`created_at`);

--
-- Indexes for table `thread_skills`
--
ALTER TABLE `thread_skills`
  ADD PRIMARY KEY (`thread_id`,`skill_id`),
  ADD KEY `thread_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `thread_tags`
--
ALTER TABLE `thread_tags`
  ADD PRIMARY KEY (`thread_id`,`tag_id`),
  ADD KEY `thread_tags_tag_id_index` (`tag_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_student_id_unique` (`student_id`),
  ADD KEY `users_status_index` (`status`);

--
-- Indexes for table `user_blocks`
--
ALTER TABLE `user_blocks`
  ADD PRIMARY KEY (`blocker_id`,`blocked_id`),
  ADD KEY `user_blocks_blocked_id_index` (`blocked_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `user_roles_role_id_index` (`role_id`);

--
-- Indexes for table `user_skills`
--
ALTER TABLE `user_skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_skills_user_skill_unique` (`user_id`,`skill_id`),
  ADD KEY `user_skills_skill_id_index` (`skill_id`),
  ADD KEY `user_skills_current_level_index` (`current_level`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `votes_user_target_unique` (`user_id`,`target_type`,`target_id`),
  ADD KEY `votes_target_index` (`target_type`,`target_id`);

--
-- Indexes for table `writeups`
--
ALTER TABLE `writeups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `writeups_user_slug_unique` (`user_id`,`slug`),
  ADD KEY `writeups_user_id_index` (`user_id`),
  ADD KEY `writeups_category_id_index` (`category_id`),
  ADD KEY `writeups_status_index` (`status`),
  ADD KEY `writeups_visibility_index` (`visibility`),
  ADD KEY `writeups_featured_index` (`featured`),
  ADD KEY `writeups_published_at_index` (`published_at`),
  ADD KEY `writeups_deleted_at_index` (`deleted_at`);
ALTER TABLE `writeups` ADD FULLTEXT KEY `writeups_ft_search` (`title`,`short_description`,`content`);

--
-- Indexes for table `writeup_categories`
--
ALTER TABLE `writeup_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `writeup_categories_slug_unique` (`slug`);

--
-- Indexes for table `writeup_challenges`
--
ALTER TABLE `writeup_challenges`
  ADD PRIMARY KEY (`writeup_id`,`challenge_id`),
  ADD KEY `writeup_challenges_challenge_id_index` (`challenge_id`);

--
-- Indexes for table `writeup_labs`
--
ALTER TABLE `writeup_labs`
  ADD PRIMARY KEY (`writeup_id`,`lab_id`),
  ADD KEY `writeup_labs_lab_id_fk` (`lab_id`);

--
-- Indexes for table `writeup_skills`
--
ALTER TABLE `writeup_skills`
  ADD PRIMARY KEY (`writeup_id`,`skill_id`),
  ADD KEY `writeup_skills_skill_id_index` (`skill_id`);

--
-- Indexes for table `writeup_tags`
--
ALTER TABLE `writeup_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `writeup_tags_slug_unique` (`slug`),
  ADD UNIQUE KEY `writeup_tags_name_unique` (`name`);

--
-- Indexes for table `writeup_tag_map`
--
ALTER TABLE `writeup_tag_map`
  ADD PRIMARY KEY (`writeup_id`,`tag_id`),
  ADD KEY `writeup_tag_map_tag_id_index` (`tag_id`);

--
-- Indexes for table `writeup_threads`
--
ALTER TABLE `writeup_threads`
  ADD PRIMARY KEY (`writeup_id`,`thread_id`),
  ADD KEY `writeup_threads_thread_id_index` (`thread_id`);

--
-- Indexes for table `writeup_versions`
--
ALTER TABLE `writeup_versions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `writeup_versions_unique` (`writeup_id`,`version`),
  ADD KEY `writeup_versions_edited_by_index` (`edited_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `arena_events`
--
ALTER TABLE `arena_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `arena_point_transactions`
--
ALTER TABLE `arena_point_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `challenges`
--
ALTER TABLE `challenges`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `challenge_categories`
--
ALTER TABLE `challenge_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `challenge_files`
--
ALTER TABLE `challenge_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `challenge_hints`
--
ALTER TABLE `challenge_hints`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `challenge_hint_usage`
--
ALTER TABLE `challenge_hint_usage`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `challenge_solves`
--
ALTER TABLE `challenge_solves`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `channels`
--
ALTER TABLE `channels`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `collab_groups`
--
ALTER TABLE `collab_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `collab_group_activities`
--
ALTER TABLE `collab_group_activities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `collab_group_applications`
--
ALTER TABLE `collab_group_applications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collab_group_goals`
--
ALTER TABLE `collab_group_goals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `collab_group_invitations`
--
ALTER TABLE `collab_group_invitations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collab_group_join_requests`
--
ALTER TABLE `collab_group_join_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collab_group_members`
--
ALTER TABLE `collab_group_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `collab_group_resources`
--
ALTER TABLE `collab_group_resources`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `collab_group_roles`
--
ALTER TABLE `collab_group_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `content_reactions`
--
ALTER TABLE `content_reactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `knowledge_article_reviews`
--
ALTER TABLE `knowledge_article_reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_article_sources`
--
ALTER TABLE `knowledge_article_sources`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `knowledge_article_versions`
--
ALTER TABLE `knowledge_article_versions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `labs`
--
ALTER TABLE `labs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lab_attempts`
--
ALTER TABLE `lab_attempts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `lab_categories`
--
ALTER TABLE `lab_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lab_completions`
--
ALTER TABLE `lab_completions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lab_feedback`
--
ALTER TABLE `lab_feedback`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_hint_usage`
--
ALTER TABLE `lab_hint_usage`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_instances`
--
ALTER TABLE `lab_instances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lab_prerequisites`
--
ALTER TABLE `lab_prerequisites`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lab_progress`
--
ALTER TABLE `lab_progress`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `lab_services`
--
ALTER TABLE `lab_services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lab_tasks`
--
ALTER TABLE `lab_tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `lab_task_dependencies`
--
ALTER TABLE `lab_task_dependencies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lab_task_hints`
--
ALTER TABLE `lab_task_hints`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lab_task_validations`
--
ALTER TABLE `lab_task_validations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `lab_templates`
--
ALTER TABLE `lab_templates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mentors`
--
ALTER TABLE `mentors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mentorships`
--
ALTER TABLE `mentorships`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mentorship_feedback`
--
ALTER TABLE `mentorship_feedback`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentorship_goals`
--
ALTER TABLE `mentorship_goals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mentorship_sessions`
--
ALTER TABLE `mentorship_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `portfolios`
--
ALTER TABLE `portfolios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `portfolio_analytics_daily`
--
ALTER TABLE `portfolio_analytics_daily`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portfolio_certifications`
--
ALTER TABLE `portfolio_certifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portfolio_education`
--
ALTER TABLE `portfolio_education`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `portfolio_experience`
--
ALTER TABLE `portfolio_experience`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `project_challenges`
--
ALTER TABLE `project_challenges`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_images`
--
ALTER TABLE `project_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_labs`
--
ALTER TABLE `project_labs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_reactions`
--
ALTER TABLE `project_reactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_technologies`
--
ALTER TABLE `project_technologies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `project_verifications`
--
ALTER TABLE `project_verifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_writeups`
--
ALTER TABLE `project_writeups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recruitment_applications`
--
ALTER TABLE `recruitment_applications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recruitment_posts`
--
ALTER TABLE `recruitment_posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `skill_categories`
--
ALTER TABLE `skill_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `skill_evidence`
--
ALTER TABLE `skill_evidence`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `skill_levels`
--
ALTER TABLE `skill_levels`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `skill_requirements`
--
ALTER TABLE `skill_requirements`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `threads`
--
ALTER TABLE `threads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_skills`
--
ALTER TABLE `user_skills`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `writeups`
--
ALTER TABLE `writeups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `writeup_categories`
--
ALTER TABLE `writeup_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `writeup_tags`
--
ALTER TABLE `writeup_tags`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `writeup_versions`
--
ALTER TABLE `writeup_versions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `arena_events`
--
ALTER TABLE `arena_events`
  ADD CONSTRAINT `arena_events_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `arena_event_challenges`
--
ALTER TABLE `arena_event_challenges`
  ADD CONSTRAINT `arena_event_challenges_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `arena_event_challenges_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `arena_events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `arena_point_transactions`
--
ALTER TABLE `arena_point_transactions`
  ADD CONSTRAINT `arena_point_transactions_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `arena_point_transactions_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `arena_events` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `arena_point_transactions_hint_id_fk` FOREIGN KEY (`hint_id`) REFERENCES `challenge_hints` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `arena_point_transactions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenges`
--
ALTER TABLE `challenges`
  ADD CONSTRAINT `challenges_author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `challenges_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `challenge_categories` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `challenge_files`
--
ALTER TABLE `challenge_files`
  ADD CONSTRAINT `challenge_files_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_hints`
--
ALTER TABLE `challenge_hints`
  ADD CONSTRAINT `challenge_hints_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_hint_usage`
--
ALTER TABLE `challenge_hint_usage`
  ADD CONSTRAINT `challenge_hint_usage_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_hint_usage_hint_id_fk` FOREIGN KEY (`hint_id`) REFERENCES `challenge_hints` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_hint_usage_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_skills`
--
ALTER TABLE `challenge_skills`
  ADD CONSTRAINT `challenge_skills_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_solves`
--
ALTER TABLE `challenge_solves`
  ADD CONSTRAINT `challenge_solves_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_solves_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_submissions`
--
ALTER TABLE `challenge_submissions`
  ADD CONSTRAINT `challenge_submissions_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_submissions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_tags`
--
ALTER TABLE `challenge_tags`
  ADD CONSTRAINT `challenge_tags_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_tags_tag_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `channels`
--
ALTER TABLE `channels`
  ADD CONSTRAINT `channels_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `collab_groups`
--
ALTER TABLE `collab_groups`
  ADD CONSTRAINT `collab_groups_channel_id_fk` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `collab_groups_owner_id_fk` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collab_group_activities`
--
ALTER TABLE `collab_group_activities`
  ADD CONSTRAINT `collab_group_activities_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collab_group_activities_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collab_group_applications`
--
ALTER TABLE `collab_group_applications`
  ADD CONSTRAINT `collab_group_applications_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collab_group_applications_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `collab_group_applications_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `collab_group_roles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `collab_group_applications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collab_group_goals`
--
ALTER TABLE `collab_group_goals`
  ADD CONSTRAINT `collab_group_goals_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collab_group_goals_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collab_group_invitations`
--
ALTER TABLE `collab_group_invitations`
  ADD CONSTRAINT `collab_group_invitations_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collab_group_invitations_invitee_id_fk` FOREIGN KEY (`invitee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collab_group_invitations_inviter_id_fk` FOREIGN KEY (`inviter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collab_group_join_requests`
--
ALTER TABLE `collab_group_join_requests`
  ADD CONSTRAINT `collab_group_join_requests_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collab_group_join_requests_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `collab_group_join_requests_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collab_group_members`
--
ALTER TABLE `collab_group_members`
  ADD CONSTRAINT `collab_group_members_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collab_group_members_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collab_group_resources`
--
ALTER TABLE `collab_group_resources`
  ADD CONSTRAINT `collab_group_resources_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collab_group_resources_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collab_group_roles`
--
ALTER TABLE `collab_group_roles`
  ADD CONSTRAINT `collab_group_roles_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collab_group_skills`
--
ALTER TABLE `collab_group_skills`
  ADD CONSTRAINT `collab_group_skills_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collab_group_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `content_reactions`
--
ALTER TABLE `content_reactions`
  ADD CONSTRAINT `content_reactions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  ADD CONSTRAINT `knowledge_articles_author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_articles_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `writeup_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `knowledge_article_contributors`
--
ALTER TABLE `knowledge_article_contributors`
  ADD CONSTRAINT `knowledge_article_contributors_article_id_fk` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_article_contributors_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_article_reviews`
--
ALTER TABLE `knowledge_article_reviews`
  ADD CONSTRAINT `knowledge_article_reviews_article_id_fk` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_article_reviews_reviewer_id_fk` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_article_skills`
--
ALTER TABLE `knowledge_article_skills`
  ADD CONSTRAINT `knowledge_article_skills_article_id_fk` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_article_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_article_sources`
--
ALTER TABLE `knowledge_article_sources`
  ADD CONSTRAINT `knowledge_article_sources_article_id_fk` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_article_versions`
--
ALTER TABLE `knowledge_article_versions`
  ADD CONSTRAINT `knowledge_article_versions_article_id_fk` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_article_versions_edited_by_fk` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `labs`
--
ALTER TABLE `labs`
  ADD CONSTRAINT `labs_author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `labs_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `lab_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `labs_template_id_fk` FOREIGN KEY (`template_id`) REFERENCES `lab_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lab_attempts`
--
ALTER TABLE `lab_attempts`
  ADD CONSTRAINT `lab_attempts_progress_id_fk` FOREIGN KEY (`progress_id`) REFERENCES `lab_progress` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_completions`
--
ALTER TABLE `lab_completions`
  ADD CONSTRAINT `lab_completions_instance_id_fk` FOREIGN KEY (`instance_id`) REFERENCES `lab_instances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_completions_lab_id_fk` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_completions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_feedback`
--
ALTER TABLE `lab_feedback`
  ADD CONSTRAINT `lab_feedback_lab_id_fk` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_feedback_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_hint_usage`
--
ALTER TABLE `lab_hint_usage`
  ADD CONSTRAINT `lab_hint_usage_hint_id_fk` FOREIGN KEY (`hint_id`) REFERENCES `lab_task_hints` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_hint_usage_instance_id_fk` FOREIGN KEY (`instance_id`) REFERENCES `lab_instances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_hint_usage_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_instances`
--
ALTER TABLE `lab_instances`
  ADD CONSTRAINT `lab_instances_lab_id_fk` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_instances_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_prerequisites`
--
ALTER TABLE `lab_prerequisites`
  ADD CONSTRAINT `lab_prerequisites_lab_id_fk` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_prerequisites_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_progress`
--
ALTER TABLE `lab_progress`
  ADD CONSTRAINT `lab_progress_instance_id_fk` FOREIGN KEY (`instance_id`) REFERENCES `lab_instances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_progress_task_id_fk` FOREIGN KEY (`task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_services`
--
ALTER TABLE `lab_services`
  ADD CONSTRAINT `lab_services_lab_id_fk` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_skills`
--
ALTER TABLE `lab_skills`
  ADD CONSTRAINT `lab_skills_lab_id_fk` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_tasks`
--
ALTER TABLE `lab_tasks`
  ADD CONSTRAINT `lab_tasks_lab_id_fk` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_task_dependencies`
--
ALTER TABLE `lab_task_dependencies`
  ADD CONSTRAINT `lab_task_dependencies_depends_fk` FOREIGN KEY (`depends_on_task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_task_dependencies_task_id_fk` FOREIGN KEY (`task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_task_hints`
--
ALTER TABLE `lab_task_hints`
  ADD CONSTRAINT `lab_task_hints_task_id_fk` FOREIGN KEY (`task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_task_validations`
--
ALTER TABLE `lab_task_validations`
  ADD CONSTRAINT `lab_task_validations_task_id_fk` FOREIGN KEY (`task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentors`
--
ALTER TABLE `mentors`
  ADD CONSTRAINT `mentors_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentors_verified_by_fk` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mentorships`
--
ALTER TABLE `mentorships`
  ADD CONSTRAINT `mentorships_mentee_id_fk` FOREIGN KEY (`mentee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentorships_mentor_id_fk` FOREIGN KEY (`mentor_id`) REFERENCES `mentors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentorship_feedback`
--
ALTER TABLE `mentorship_feedback`
  ADD CONSTRAINT `mentorship_feedback_from_user_id_fk` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentorship_feedback_session_id_fk` FOREIGN KEY (`session_id`) REFERENCES `mentorship_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentorship_feedback_to_user_id_fk` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentorship_goals`
--
ALTER TABLE `mentorship_goals`
  ADD CONSTRAINT `mentorship_goals_mentorship_id_fk` FOREIGN KEY (`mentorship_id`) REFERENCES `mentorships` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentorship_goals_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mentorship_sessions`
--
ALTER TABLE `mentorship_sessions`
  ADD CONSTRAINT `mentorship_sessions_mentorship_id_fk` FOREIGN KEY (`mentorship_id`) REFERENCES `mentorships` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentorship_skills`
--
ALTER TABLE `mentorship_skills`
  ADD CONSTRAINT `mentorship_skills_mentorship_id_fk` FOREIGN KEY (`mentorship_id`) REFERENCES `mentorships` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentorship_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentor_skills`
--
ALTER TABLE `mentor_skills`
  ADD CONSTRAINT `mentor_skills_mentor_id_fk` FOREIGN KEY (`mentor_id`) REFERENCES `mentors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentor_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD CONSTRAINT `portfolios_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_analytics_daily`
--
ALTER TABLE `portfolio_analytics_daily`
  ADD CONSTRAINT `portfolio_analytics_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_certifications`
--
ALTER TABLE `portfolio_certifications`
  ADD CONSTRAINT `portfolio_certifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_education`
--
ALTER TABLE `portfolio_education`
  ADD CONSTRAINT `portfolio_education_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_experience`
--
ALTER TABLE `portfolio_experience`
  ADD CONSTRAINT `portfolio_experience_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_featured_skills`
--
ALTER TABLE `portfolio_featured_skills`
  ADD CONSTRAINT `portfolio_featured_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `portfolio_featured_skills_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_featured_writeups`
--
ALTER TABLE `portfolio_featured_writeups`
  ADD CONSTRAINT `portfolio_featured_writeups_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `portfolio_featured_writeups_writeup_id_fk` FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_challenges`
--
ALTER TABLE `project_challenges`
  ADD CONSTRAINT `project_challenges_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_challenges_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_images`
--
ALTER TABLE `project_images`
  ADD CONSTRAINT `project_images_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_labs`
--
ALTER TABLE `project_labs`
  ADD CONSTRAINT `project_labs_lab_id_fk` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_labs_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_reactions`
--
ALTER TABLE `project_reactions`
  ADD CONSTRAINT `project_reactions_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_reactions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_skills`
--
ALTER TABLE `project_skills`
  ADD CONSTRAINT `project_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_technologies`
--
ALTER TABLE `project_technologies`
  ADD CONSTRAINT `project_technologies_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_verifications`
--
ALTER TABLE `project_verifications`
  ADD CONSTRAINT `project_verifications_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_verifications_requested_by_fk` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_verifications_verified_by_fk` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_writeups`
--
ALTER TABLE `project_writeups`
  ADD CONSTRAINT `project_writeups_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_writeups_writeup_id_fk` FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recruitment_applications`
--
ALTER TABLE `recruitment_applications`
  ADD CONSTRAINT `recruitment_applications_recruitment_id_fk` FOREIGN KEY (`recruitment_id`) REFERENCES `recruitment_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recruitment_applications_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `recruitment_applications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recruitment_posts`
--
ALTER TABLE `recruitment_posts`
  ADD CONSTRAINT `recruitment_posts_creator_id_fk` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recruitment_posts_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `recruitment_skills`
--
ALTER TABLE `recruitment_skills`
  ADD CONSTRAINT `recruitment_skills_recruitment_id_fk` FOREIGN KEY (`recruitment_id`) REFERENCES `recruitment_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recruitment_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_parent_reply_id_fk` FOREIGN KEY (`parent_reply_id`) REFERENCES `replies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `replies_thread_id_fk` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `replies_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_reporter_id_fk` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `skill_categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `skills_parent_skill_id_fk` FOREIGN KEY (`parent_skill_id`) REFERENCES `skills` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `skill_evidence`
--
ALTER TABLE `skill_evidence`
  ADD CONSTRAINT `skill_evidence_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `skill_evidence_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `skill_evidence_verified_by_fk` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `skill_prerequisites`
--
ALTER TABLE `skill_prerequisites`
  ADD CONSTRAINT `skill_prerequisites_prereq_fk` FOREIGN KEY (`prerequisite_skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `skill_prerequisites_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skill_requirements`
--
ALTER TABLE `skill_requirements`
  ADD CONSTRAINT `skill_requirements_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `threads`
--
ALTER TABLE `threads`
  ADD CONSTRAINT `threads_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `threads_channel_id_fk` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `threads_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thread_skills`
--
ALTER TABLE `thread_skills`
  ADD CONSTRAINT `thread_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thread_skills_thread_id_fk` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thread_tags`
--
ALTER TABLE `thread_tags`
  ADD CONSTRAINT `thread_tags_tag_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thread_tags_thread_id_fk` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_blocks`
--
ALTER TABLE `user_blocks`
  ADD CONSTRAINT `user_blocks_blocked_id_fk` FOREIGN KEY (`blocked_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_blocks_blocker_id_fk` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_skills`
--
ALTER TABLE `user_skills`
  ADD CONSTRAINT `user_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_skills_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `writeups`
--
ALTER TABLE `writeups`
  ADD CONSTRAINT `writeups_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `writeup_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `writeups_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `writeup_challenges`
--
ALTER TABLE `writeup_challenges`
  ADD CONSTRAINT `writeup_challenges_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `writeup_challenges_writeup_id_fk` FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `writeup_labs`
--
ALTER TABLE `writeup_labs`
  ADD CONSTRAINT `writeup_labs_lab_id_fk` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `writeup_labs_writeup_id_fk` FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `writeup_skills`
--
ALTER TABLE `writeup_skills`
  ADD CONSTRAINT `writeup_skills_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `writeup_skills_writeup_id_fk` FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `writeup_tag_map`
--
ALTER TABLE `writeup_tag_map`
  ADD CONSTRAINT `writeup_tag_map_tag_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `writeup_tags` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `writeup_tag_map_writeup_id_fk` FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `writeup_threads`
--
ALTER TABLE `writeup_threads`
  ADD CONSTRAINT `writeup_threads_thread_id_fk` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `writeup_threads_writeup_id_fk` FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `writeup_versions`
--
ALTER TABLE `writeup_versions`
  ADD CONSTRAINT `writeup_versions_edited_by_fk` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `writeup_versions_writeup_id_fk` FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
