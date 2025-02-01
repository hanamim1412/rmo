-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2025 at 07:55 PM
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
-- Database: `rmo`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `user_id` int(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `contact` varchar(13) NOT NULL,
  `department_id` int(11) NOT NULL,
  `smc_email` varchar(255) NOT NULL,
  `user_type` enum('student','dean','panelist','rmo_staff','subject_advisor') NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`user_id`, `username`, `password`, `firstname`, `lastname`, `contact`, `department_id`, `smc_email`, `user_type`, `date_created`) VALUES
(9, 'C24-0001', '$2y$10$/2dVicSFs7RykcN8M83U8Osh./ZkRM1c3774FW7MioZg.sY4gdlj.', 'nor janah', 'mimbisa', '09123456789', 1, 'norjanah.mimbisa@my.smciligan.edu.ph', 'student', '2024-12-05 00:18:10'),
(10, 'C24-0002', '$2y$10$ZcLz3/48v8w4t1064reln.u/sMwGhdcd7GMBDI4Knd/0s3h1YrGxW', 'Edsel', 'Monterola', '0987123542', 1, 'edsel.monterola@my.smciligan.edu.ph', 'dean', '2024-12-05 01:15:33'),
(11, 'C24-0003', '$2y$10$bezbWiwRbVzuV./ERaZDuOkh2Z5pEWR3fXeh9sb5M7Cbbi7OSRSXe', 'Mj', 'Suarez', '0987123252', 1, 'mj.suarez@my.smciligan.edu.ph', 'panelist', '2024-12-05 01:17:27'),
(12, 'C24-0004', '$2y$10$Wg5jYvydrQszeMR1pEQ7/OktfWNv5YZ17ujvs.ru9r3WpkUZz2AW.', 'Aniceto', 'Naval', '0912387965', 4, 'aniceto.naval@my.smciligan.edu.ph', 'rmo_staff', '2024-12-05 04:31:51'),
(13, 'C24-0005', '$2y$10$YIMoU49fBofaZF2sDIcg6.AxrD1m3IadlqYdqtoiXjYkmRKwSEfda', 'Jerome', 'Abilay', '0912386542', 1, 'jerome.abilay@my.smciligan.edu.ph', 'panelist', '2024-12-07 21:41:55'),
(14, 'C24-0006', '$2y$10$OflrgJAyHfHQOjbAVJxp1.L4J2wtVKi5pI0lQbHrlaz7Lk.TcgKPu', 'Paul Arman', 'Durango', '0912386542', 1, 'paularman.durango@my.smciligan.edu.ph', 'panelist', '2024-12-07 21:42:44'),
(15, 'C24-0007', '$2y$10$IjNrcxbWoqgur93qP3SQgOxJWk1ZaSNdAxCk.0H4.EINavajZdSsa', 'Leonie', 'Abilay', 'Cajes', 1, 'leonie.abilay@my.smciligan.edu.ph', 'panelist', '2024-12-07 21:49:36'),
(16, 'C24-0008', '$2y$10$p9b1SsTgA1bAobFVuNiLautxMrics1S8fKpAvKw7fQtlHMQkaDPhS', 'james angelo', 'anadon', '0912386542', 1, 'jamesangelo.anadon@my.smciligan.edu.ph', 'student', '2024-12-07 22:47:00'),
(17, 'C24-0009', '$2y$10$1Kn9B9nDr20A/xgmn7FOHun3tDxbbK5Jjg/IxJq7P957CPSMXKN5q', 'Veldin', 'Talorete', '091234567891', 1, 'veldin.talorete@my.smciligan.edu.ph', 'panelist', '2024-12-30 23:07:36'),
(18, 'C24-0010', '$2y$10$MpLt0WhdBU1ViYAA0amsOOQ1Ie44jPNpD5GkjsHG6gGbIUd83X8r6', 'Marites Fe', 'Bahinting', '098765432128', 1, 'maritesfe.bahinting@my.smciligan.edu.ph', 'subject_advisor', '2024-12-30 23:08:45'),
(19, 'C24-0011', '$2y$10$hr.XiOg0JZ9Kmm0jwhDh5.AsGB4cdP7uANl.y/loikr8PWefCz5T6', 'Marites Fe', 'Bahinting', '09876543219', 1, 'maritesfe.bahinting@my.smciligan.edu.ph', 'panelist', '2024-12-30 23:10:01'),
(20, 'C25-0001', '$2y$10$adoRKnYFMzZDQhjcv4R/KetC.GrxqOmm/rN8cvVeQkkdw13/LEMhS', 'Jane ', 'Zaportiza', '0987265341', 2, 'jane.zaportiza@my.smciligan.edu.ph', 'dean', '2025-01-01 19:52:14'),
(21, 'C25-0002', '$2y$10$shb6iY1bGBZMEwoXMSqup.CgnbJnLsYij0I9gLdZj6WvZE7WnvU2q', 'Jayrton Manuel', 'Gutib', '091234567654', 2, 'jayrtonmanuel.gutib@my.smciligan.edu.ph', 'student', '2025-01-01 19:59:54'),
(22, 'C25-0003', '$2y$10$OFlUEnzDdPsWZHynZVu/tum/uJwl/DRqYKf0/Y6qvRu7XY8OWUDvK', 'Ayna', 'Salic', '098761234561', 2, 'ayna.salic@my.smciligan.edu.ph', 'student', '2025-01-01 20:00:45'),
(23, 'C25-0004', '$2y$10$a9cws7PH9lYk4ZB20AoA5.X6FabmPucloAdKQNaLi.rb0kznfS.fi', 'LEONARDO', 'DOTARO', '098712345612', 2, 'leonardo.dotaro@my.smciligan.edu.ph', 'panelist', '2025-01-01 20:03:03'),
(24, 'C25-0005', '$2y$10$1YuM6yJ7C7vEx99TTwvayeMDjl5.z.AMZ/0tnp2UDsm8aL2bhI5UO', 'STEPHANIE', 'VISITACION', '098712344325', 2, 'stephanie.visitacion@my.smciligan.edu.ph', 'panelist', '2025-01-01 20:03:36'),
(25, 'C25-0006', '$2y$10$Ji/BJgvW3kumgFUdZrqg5eHheUuURXjL5LNfZoDGVj9k7MOV1dgV.', 'VELDIN', 'TALORETE JR.', '098765432128', 2, 'veldin.taloretejr.@my.smciligan.edu.ph', 'panelist', '2025-01-01 20:12:40'),
(26, 'C25-0007', '$2y$10$NIikUJk.7h37i96wttigG.CM.Sugfs9ocWOv6bXNHlq8iXRUtcfnS', 'Marites Fe', 'Bahinting', '09876509876', 2, 'maritesfe.bahinting@my.smciligan.edu.ph', 'panelist', '2025-01-01 20:13:16');

-- --------------------------------------------------------

--
-- Table structure for table `assigned_panelists`
--

CREATE TABLE `assigned_panelists` (
  `assigned_panelist_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_selected` tinyint(11) DEFAULT 1,
  `comments` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assigned_panelists`
--

INSERT INTO `assigned_panelists` (`assigned_panelist_id`, `tw_form_id`, `user_id`, `is_selected`, `comments`, `date_created`) VALUES
(1, 23, 13, 1, 'tw form1', '2024-12-30 23:25:44'),
(2, 23, 19, 1, 'tw form1', '2024-12-30 23:25:44'),
(3, 23, 15, 1, 'tw form1', '2024-12-30 23:25:44'),
(4, 23, 11, 1, 'tw form1', '2024-12-30 23:25:44'),
(5, 21, 13, 1, 'tw form 3', '2024-12-30 23:27:35'),
(6, 21, 15, 1, 'tw form 3', '2024-12-30 23:27:35'),
(7, 21, 11, 1, 'tw form 3', '2024-12-30 23:27:35'),
(8, 21, 19, 1, 'tw form 3', '2024-12-30 23:27:35'),
(9, 27, 13, 1, 'tw form 5', '2024-12-30 23:29:01'),
(10, 27, 15, 1, 'tw form 5', '2024-12-30 23:29:01'),
(11, 27, 19, 1, 'tw form 5', '2024-12-30 23:29:01'),
(12, 27, 11, 1, 'tw form 5', '2024-12-30 23:29:01'),
(13, 28, 24, 1, 'computer engineering nga Thesis twform 1', '2025-01-01 20:16:11'),
(14, 28, 25, 1, 'computer engineering nga Thesis twform 1', '2025-01-01 20:16:11'),
(15, 28, 23, 1, 'computer engineering nga Thesis twform 1', '2025-01-01 20:16:11'),
(16, 28, 26, 1, 'computer engineering nga Thesis twform 1', '2025-01-01 20:16:11'),
(17, 29, 23, 1, 'twform 3 college of engineering BS ComEng', '2025-01-01 20:20:57'),
(18, 29, 24, 1, 'twform 3 college of engineering BS ComEng', '2025-01-01 20:20:57'),
(19, 29, 25, 1, 'twform 3 college of engineering BS ComEng', '2025-01-01 20:20:57'),
(20, 29, 26, 1, 'twform 3 college of engineering BS ComEng', '2025-01-01 20:20:57');

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `attachment_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`attachment_id`, `tw_form_id`, `file_name`, `purpose`, `file_path`, `upload_date`) VALUES
(2, 21, 'CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', 'proposal_manuscript', '../uploads/manuscripts/CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', '2024-12-30 01:30:22'),
(4, 27, 'CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', 'final Defense manuscript', '../uploads/manuscripts/CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', '2024-12-30 22:43:50'),
(5, 29, 'SmartLaptopCoolingPadWithTemperatureMonitoringForHardbound.pdf', 'proposal_manuscript', '../uploads/manuscripts/SmartLaptopCoolingPadWithTemperatureMonitoringForHardbound.pdf', '2025-01-01 20:18:23'),
(6, 31, 'REVISED Chapter 5.pdf', 'Approval for Binding', '../uploads/manuscripts/REVISED Chapter 5.pdf', '2025-01-15 01:14:16');

-- --------------------------------------------------------

--
-- Table structure for table `college_research_agenda`
--

CREATE TABLE `college_research_agenda` (
  `agenda_id` int(11) NOT NULL,
  `agenda_name` varchar(255) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_research_agenda`
--

INSERT INTO `college_research_agenda` (`agenda_id`, `agenda_name`, `department_id`, `description`) VALUES
(4, 'Software Development', 1, 'Mobile computing systems,\r\nSoftware extensions or plug-ins,\r\nExpert systems'),
(5, 'Foundations of Computer Science', 1, 'Automata and Formal Languages,\r\nData Structures and Algorithm Design and Analysis, Web Semantics'),
(6, 'Multimedia Systems', 1, 'Game development, E-learning systems, Interactive systems'),
(7, 'IT Management', 1, 'IT Strategic Plan for sufficiently complex enterprise,\r\nIT Security Analysis, Planning, and Implementation'),
(8, 'Artificial Intelligence', 1, 'Expert Systems, Neural Networks'),
(9, 'Robotics', 1, 'Obstacle Avoidance, Cooperative Transport'),
(10, 'Sustainable Infrastructure Solutions', 2, 'Structural Design,\r\nMaterial Development and Testing,\r\nConstruction Methods and Project Management'),
(11, 'Competitive Industries', 2, 'ICT, Electronics and Semiconductor,\r\nBig Data Analytics,\r\nArtificial Intelligence'),
(12, 'Intelligent Transport Solutions', 2, 'Intelligent vehicle-to-vehicle connectivity and information sharing of speed, lane changing, and potential intersection crash warning data for safe vehicle driving,\r\n\r\nDigital infrastructure needs assessment for internet of vehicles implementation,\r\n\r\nCommuters and public utility vehicle information systems'),
(13, 'Human Security', 2, 'Biosecurity, Cybersecurity'),
(14, 'Warning and Communication of Information', 2, 'Warning Communication (Geological, Hydro-meteorological, Climate-related hazards, and Impacts),\r\nImpact-based/risk-based modelling and forecasting'),
(15, 'Environmental Issues and Economic Development', 3, 'Poverty Alleviation, \r\nIncome Generating Projects, \r\nImpact of Environmental Laws to business'),
(16, 'Marketing and Management', 3, 'Marketing of Community Products,\r\nTime Management of Business Students,\r\nWork Life Balance'),
(17, 'Linkages and Networking', 3, 'Tracer study of Business Graduates,\r\nEmployability of Graduates'),
(18, 'Robotics', 2, 'Obstacle Avoidance, Cooperative Transport');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `department_id`) VALUES
(1, 'Bachelor of Science in Information Technology', 1),
(2, 'Bachelor of Science in Computer Science', 1),
(3, 'Bachelor of Information Systems', 1),
(4, 'Bachelor of Science in Civil Engineering', 2),
(5, 'Bachelor of Science in Computer Engineering', 2),
(6, 'Bachelor of Science in Electrical Engineering', 2);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(1, 'College of Computer Studies'),
(2, 'College of Engineering'),
(3, 'College of Business Administration and Accountancy'),
(4, 'Research Management Office');

-- --------------------------------------------------------

--
-- Table structure for table `eval_criteria`
--

CREATE TABLE `eval_criteria` (
  `eval_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `presentation` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `organization` int(11) NOT NULL,
  `mastery` int(11) NOT NULL,
  `ability` int(11) NOT NULL,
  `openness` int(11) NOT NULL,
  `overall_rating` decimal(5,2) NOT NULL,
  `percentage` decimal(10,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eval_criteria`
--

INSERT INTO `eval_criteria` (`eval_id`, `tw_form_id`, `evaluator_id`, `presentation`, `content`, `organization`, `mastery`, `ability`, `openness`, `overall_rating`, `percentage`, `remarks`, `date_created`) VALUES
(4, 21, 11, 10, 17, 8, 17, 18, 8, 78.00, 86.00, 'oki', '2024-12-31 01:34:13'),
(5, 21, 15, 10, 20, 7, 17, 18, 6, 78.00, 86.00, 'leonie ni, mana evaluated', '2024-12-31 02:53:17'),
(6, 29, 24, 10, 18, 7, 18, 17, 6, 76.00, 85.00, 'twform 3 mana grado', '2025-01-01 20:51:17');

-- --------------------------------------------------------

--
-- Table structure for table `institutional_research_agenda`
--

CREATE TABLE `institutional_research_agenda` (
  `ir_agenda_id` int(11) NOT NULL,
  `ir_agenda_name` varchar(255) NOT NULL,
  `sub_areas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `institutional_research_agenda`
--

INSERT INTO `institutional_research_agenda` (`ir_agenda_id`, `ir_agenda_name`, `sub_areas`) VALUES
(1, 'Program/Curricular Studies', '1. Curriculum Development and Evaluation.\r\n2. Cohort Studies\r\n3. Test Development and Validation\r\n4. Curriculum Benchmarking\r\n5. Best Practices in Instruction\r\n6. Universal Design for Learning'),
(2, 'Education', '1. Teaching Strategies\r\n2. Quality of Delivery of Instruction\r\n3. Faculty Efficiency and Effectiveness\r\n4. Assessment of Educational programs\r\n5. Technology and Education\r\n'),
(5, 'Institutional Development ', '1. Accreditation and other Quality Assurance systems\r\n2. Equivalency');

-- --------------------------------------------------------

--
-- Table structure for table `proponents`
--

CREATE TABLE `proponents` (
  `proponent_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `receipt_id` int(11) DEFAULT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `date_created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proponents`
--

INSERT INTO `proponents` (`proponent_id`, `tw_form_id`, `receipt_id`, `firstname`, `lastname`, `date_created`) VALUES
(21, 23, NULL, 'Nor Janah', 'Mimbisa', '2024-12-30'),
(22, 23, NULL, 'James Angelo', 'Anadon', '2024-12-30'),
(23, 24, 15, 'Nor Janah', 'Mimbisa', '2024-12-30'),
(24, 24, 16, 'James Angelo', 'Anadon', '2024-12-30'),
(25, 25, 17, 'Nor Janah', 'Mimbisa', '2024-12-30'),
(26, 25, 18, 'James Angelo', 'Anadon', '2024-12-30'),
(29, 30, 19, 'Jayrton Manuel', 'Gutib', '2025-01-01'),
(30, 30, 20, 'Steven John', 'Cuizon', '2025-01-01'),
(31, 28, NULL, 'Jayrton Manuel', 'Gutib', '2025-01-03'),
(32, 28, NULL, 'Steven John', 'Cuizon', '2025-01-03'),
(33, 31, NULL, 'Nor Janah', 'Mimbisa', '2025-01-15'),
(34, 31, NULL, 'James Angelo', 'Anadon', '2025-01-15');

-- --------------------------------------------------------

--
-- Table structure for table `proposed_title`
--

CREATE TABLE `proposed_title` (
  `proposed_title_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `title_name` text NOT NULL,
  `rationale` text NOT NULL,
  `is_selected` tinyint(11) DEFAULT 0,
  `date_created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proposed_title`
--

INSERT INTO `proposed_title` (`proposed_title_id`, `tw_form_id`, `title_name`, `rationale`, `is_selected`, `date_created`) VALUES
(9, 23, 'ILIGAN CITY SOCIAL WELFARE AND DEVELOPMENT INVENTORY MANAGEMENT AND MONITORING SYSTEM WITH DECISION SUPPORT', 'The Iligan City Social Welfare and Development Office (SWDO) is responsible for managing and distributing resources to individuals and communities in need. The absence of a streamlined inventory management and monitoring system can lead to inefficiencies, such as resource misallocation, delays in service delivery, and poor decision-making.\r\nThis system aims to enhance the SWDO\'s operations by digitizing inventory processes, providing real-time monitoring of resources, and integrating a decision support system (DSS) to aid in analyzing trends and forecasting needs. By doing so, the system will ensure transparency, accountability, and data-driven decision-making, ultimately improving the welfare services offered to the city\'s vulnerable population.', 1, '2024-12-30'),
(10, 23, 'Smart Watch for Elderly with Dementia', 'Elderly individuals with dementia often face challenges such as memory loss, disorientation, and difficulty in performing daily activities, making them vulnerable to accidents and wandering. Caregivers also experience stress and difficulty in continuously monitoring their well-being.\r\n\r\nThe Smart Watch for Elderly with Dementia is designed to address these issues by incorporating features such as GPS tracking, fall detection, health monitoring (e.g., heart rate, blood pressure), and medication reminders. These functionalities provide caregivers with real-time updates and alerts, ensuring the safety and health of the elderly while allowing them greater independence. This innovation offers a practical, technology-driven solution to enhance the quality of life for both individuals with dementia and their caregivers.', 0, '2024-12-30'),
(13, 28, '                            SMART LAPTOP COOLING PAD WITH TEMPERATURE MONITORING                        ', '                            This study aims to address overheating issues in laptops by developing a smart cooling pad equipped with a real-time temperature monitoring system. The innovation ensures efficient heat management, prolongs device lifespan, and enhances user experience, particularly for prolonged usage in demanding environments.                        ', 0, '2025-01-03'),
(14, 28, '                            ROBOTIC MANIPULATOR FOR ELDER CARE                        ', '                            This research focuses on creating a robotic manipulator designed to assist elderly individuals with daily activities, promoting independence and improving quality of life. By integrating advanced automation and ergonomic design, the solution aims to address challenges in elder care with precision, safety, and reliability.                        ', 0, '2025-01-03');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `receipt_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `receipt_img` varchar(255) DEFAULT NULL,
  `receipt_num` varchar(20) NOT NULL,
  `date_paid` date NOT NULL,
  `date_submitted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`receipt_id`, `tw_form_id`, `receipt_img`, `receipt_num`, `date_paid`, `date_submitted`) VALUES
(15, 24, '6772_All_inventory_report_3_1734154837.pdf', '09871', '2024-12-17', '2024-12-30 22:32:05'),
(16, 24, '6772_Resume.pdf', '09872', '2024-12-18', '2024-12-30 22:32:05'),
(17, 25, '6772_IEEE_Reference_Guide.pdf', '09892', '2024-12-24', '2024-12-30 22:33:21'),
(18, 25, '6772_TW4-Form-Approval-for-Oral-Defense.pdf', '09895', '2024-12-26', '2024-12-30 22:33:21'),
(19, 30, '6775_SM102718460-2-3.jpg', '09876', '2024-12-30', '2025-01-01 22:22:51'),
(20, 30, '6775_system-testing.jpg', '09878', '2024-12-30', '2025-01-01 22:22:51');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `date_generated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twform_1`
--

CREATE TABLE `twform_1` (
  `form1_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_1`
--

INSERT INTO `twform_1` (`form1_id`, `tw_form_id`, `year_level`, `date_created`, `last_updated`) VALUES
(9, 23, '4', '2024-12-30 22:30:28', '2024-12-30 22:30:28'),
(10, 28, '4th year', '2025-01-01 20:10:52', '2025-01-03 00:55:29');

-- --------------------------------------------------------

--
-- Table structure for table `twform_2`
--

CREATE TABLE `twform_2` (
  `form2_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date NOT NULL,
  `time` time NOT NULL,
  `place` varchar(255) NOT NULL,
  `comments` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_2`
--

INSERT INTO `twform_2` (`form2_id`, `tw_form_id`, `thesis_title`, `defense_date`, `time`, `place`, `comments`, `date_created`, `last_updated`) VALUES
(11, 24, 'Iligan City Social Welfare and Development Inventory Management and Monitoring System with Decision Support', '2025-01-04', '10:31:00', 'RMO conference', NULL, '2024-12-30 22:32:05', '2024-12-30 22:32:05'),
(12, 30, 'SMART LAPTOP COOLING PAD WITH TEMPERATURE MONITORING', '2025-01-09', '11:22:00', 'RMO conference', NULL, '2025-01-01 22:22:51', '2025-01-01 22:22:51');

-- --------------------------------------------------------

--
-- Table structure for table `twform_3`
--

CREATE TABLE `twform_3` (
  `form3_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `eval_id` int(11) DEFAULT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date NOT NULL,
  `time` time DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `status` enum('pending','graded') NOT NULL,
  `last_updated` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_3`
--

INSERT INTO `twform_3` (`form3_id`, `tw_form_id`, `student_id`, `eval_id`, `thesis_title`, `defense_date`, `time`, `place`, `comments`, `status`, `last_updated`) VALUES
(2, 21, 9, NULL, 'ILIGAN CITY SOCIAL WELFARE AND DEVELOPMENT OFFICE INVENTORY MANAGEMENT AND MONITORING SYSTEM WITH DECISION SUPPORT', '2025-01-06', '09:28:00', 'RMO conference', NULL, 'pending', '2024-12-31'),
(3, 29, 21, NULL, 'SMART LAPTOP COOLING PAD WITH TEMPERATURE MONITORING', '2025-01-12', '08:18:00', 'RMO conference', NULL, 'pending', '2025-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `twform_4`
--

CREATE TABLE `twform_4` (
  `form4_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date NOT NULL,
  `time` time NOT NULL,
  `place` varchar(255) NOT NULL,
  `date_submitted` date NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_4`
--

INSERT INTO `twform_4` (`form4_id`, `tw_form_id`, `thesis_title`, `defense_date`, `time`, `place`, `date_submitted`, `last_updated`) VALUES
(1, 25, 'Iligan City Social Welfare and Development Inventory Management and Monitoring System with Decision Support', '2025-01-05', '11:33:00', 'RMO conference', '2024-12-30', '2024-12-30 22:33:21');

-- --------------------------------------------------------

--
-- Table structure for table `twform_5`
--

CREATE TABLE `twform_5` (
  `form5_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date NOT NULL,
  `time` time NOT NULL,
  `place` varchar(255) NOT NULL,
  `status` enum('pending','graded') NOT NULL,
  `last_updated` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_5`
--

INSERT INTO `twform_5` (`form5_id`, `tw_form_id`, `student_id`, `thesis_title`, `defense_date`, `time`, `place`, `status`, `last_updated`) VALUES
(2, 27, 9, 'Iligan City Social Welfare and Development Inventory Management and Monitoring System with Decision Support', '2025-01-05', '12:43:00', 'RMO conference', 'pending', '2024-12-30');

-- --------------------------------------------------------

--
-- Table structure for table `twform_6`
--

CREATE TABLE `twform_6` (
  `form6_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `statistician` varchar(100) DEFAULT NULL,
  `editor` varchar(100) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_6`
--

INSERT INTO `twform_6` (`form6_id`, `tw_form_id`, `thesis_title`, `statistician`, `editor`, `comments`, `date_created`, `last_updated`) VALUES
(1, 31, 'ILIGAN CITY SOCIAL WELFARE AND DEVELOPMENT OFFICE INVENTORY MANAGEMENT AND MONITORING SYSTEM WITH DECISION SUPPORT', NULL, NULL, NULL, '2025-01-15 01:14:16', '2025-01-15 01:14:16');

-- --------------------------------------------------------

--
-- Table structure for table `tw_forms`
--

CREATE TABLE `tw_forms` (
  `tw_form_id` int(11) NOT NULL,
  `form_type` enum('twform_1','twform_2','twform_3','twform_4','twform_5','twform_6') NOT NULL,
  `ir_agenda_id` int(11) NOT NULL,
  `col_agenda_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `research_adviser_id` int(11) NOT NULL,
  `overall_status` enum('pending','approved','rejected','') NOT NULL,
  `comments` text DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tw_forms`
--

INSERT INTO `tw_forms` (`tw_form_id`, `form_type`, `ir_agenda_id`, `col_agenda_id`, `department_id`, `course_id`, `user_id`, `research_adviser_id`, `overall_status`, `comments`, `submission_date`, `last_updated`) VALUES
(21, 'twform_3', 1, 4, 1, 1, 9, 13, 'approved', 'to be evaluated', '2024-12-30', '2025-01-01 12:55:45'),
(23, 'twform_1', 1, 4, 1, 1, 9, 13, 'approved', 'oki', '2024-12-30', '2024-12-30 15:26:43'),
(24, 'twform_2', 1, 4, 1, 1, 9, 13, 'approved', NULL, '2024-12-30', '2024-12-30 15:26:59'),
(25, 'twform_4', 1, 4, 1, 1, 9, 13, 'approved', NULL, '2024-12-30', '2024-12-30 15:27:08'),
(27, 'twform_5', 1, 4, 1, 1, 9, 13, 'approved', NULL, '2024-12-30', '2024-12-30 14:45:51'),
(28, 'twform_1', 1, 10, 2, 5, 21, 24, 'pending', 'oki computer engineering ni', '2025-01-01', '2025-01-02 16:55:29'),
(29, 'twform_3', 1, 9, 2, 5, 21, 24, 'pending', 'twform 3 ni sa college of comEng', '2025-01-01', '2025-01-01 12:20:18'),
(30, 'twform_2', 1, 18, 2, 5, 21, 24, 'pending', NULL, '2025-01-01', '2025-01-01 14:22:51'),
(31, 'twform_6', 1, 4, 1, 1, 9, 13, 'pending', NULL, '2025-01-15', '2025-01-14 17:14:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `assigned_panelists`
--
ALTER TABLE `assigned_panelists`
  ADD PRIMARY KEY (`assigned_panelist_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `assigned_panelists_ibfk_2` (`user_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `tw_form_id` (`tw_form_id`);

--
-- Indexes for table `college_research_agenda`
--
ALTER TABLE `college_research_agenda`
  ADD PRIMARY KEY (`agenda_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `courses_ibfk_1` (`department_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `eval_criteria`
--
ALTER TABLE `eval_criteria`
  ADD PRIMARY KEY (`eval_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `evaluator_id` (`evaluator_id`);

--
-- Indexes for table `institutional_research_agenda`
--
ALTER TABLE `institutional_research_agenda`
  ADD PRIMARY KEY (`ir_agenda_id`);

--
-- Indexes for table `proponents`
--
ALTER TABLE `proponents`
  ADD PRIMARY KEY (`proponent_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `receipt_id` (`receipt_id`);

--
-- Indexes for table `proposed_title`
--
ALTER TABLE `proposed_title`
  ADD PRIMARY KEY (`proposed_title_id`),
  ADD KEY `tw_form_id` (`tw_form_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`receipt_id`),
  ADD KEY `tw_form_id` (`tw_form_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `twform_1`
--
ALTER TABLE `twform_1`
  ADD PRIMARY KEY (`form1_id`),
  ADD KEY `tw_form_id` (`tw_form_id`);

--
-- Indexes for table `twform_2`
--
ALTER TABLE `twform_2`
  ADD PRIMARY KEY (`form2_id`),
  ADD KEY `tw_form_id` (`tw_form_id`);

--
-- Indexes for table `twform_3`
--
ALTER TABLE `twform_3`
  ADD PRIMARY KEY (`form3_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `eval_id` (`eval_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `twform_4`
--
ALTER TABLE `twform_4`
  ADD PRIMARY KEY (`form4_id`),
  ADD KEY `tw_form_id` (`tw_form_id`);

--
-- Indexes for table `twform_5`
--
ALTER TABLE `twform_5`
  ADD PRIMARY KEY (`form5_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `twform_5_ibfk_2` (`student_id`);

--
-- Indexes for table `twform_6`
--
ALTER TABLE `twform_6`
  ADD PRIMARY KEY (`form6_id`),
  ADD KEY `tw_form_id` (`tw_form_id`);

--
-- Indexes for table `tw_forms`
--
ALTER TABLE `tw_forms`
  ADD PRIMARY KEY (`tw_form_id`),
  ADD KEY `ir_agenda_id` (`ir_agenda_id`),
  ADD KEY `col_agenda_id` (`col_agenda_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `assigned_panelists`
--
ALTER TABLE `assigned_panelists`
  MODIFY `assigned_panelist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `college_research_agenda`
--
ALTER TABLE `college_research_agenda`
  MODIFY `agenda_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `eval_criteria`
--
ALTER TABLE `eval_criteria`
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `institutional_research_agenda`
--
ALTER TABLE `institutional_research_agenda`
  MODIFY `ir_agenda_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `proponents`
--
ALTER TABLE `proponents`
  MODIFY `proponent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `proposed_title`
--
ALTER TABLE `proposed_title`
  MODIFY `proposed_title_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `twform_1`
--
ALTER TABLE `twform_1`
  MODIFY `form1_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `twform_2`
--
ALTER TABLE `twform_2`
  MODIFY `form2_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `twform_3`
--
ALTER TABLE `twform_3`
  MODIFY `form3_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `twform_4`
--
ALTER TABLE `twform_4`
  MODIFY `form4_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `twform_5`
--
ALTER TABLE `twform_5`
  MODIFY `form5_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `twform_6`
--
ALTER TABLE `twform_6`
  MODIFY `form6_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tw_forms`
--
ALTER TABLE `tw_forms`
  MODIFY `tw_form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assigned_panelists`
--
ALTER TABLE `assigned_panelists`
  ADD CONSTRAINT `assigned_panelists_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assigned_panelists_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `college_research_agenda`
--
ALTER TABLE `college_research_agenda`
  ADD CONSTRAINT `college_research_agenda_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `eval_criteria`
--
ALTER TABLE `eval_criteria`
  ADD CONSTRAINT `eval_criteria_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eval_criteria_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proponents`
--
ALTER TABLE `proponents`
  ADD CONSTRAINT `proponents_ibfk_2` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `proponents_ibfk_3` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`receipt_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proposed_title`
--
ALTER TABLE `proposed_title`
  ADD CONSTRAINT `proposed_title_ibfk_2` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_1`
--
ALTER TABLE `twform_1`
  ADD CONSTRAINT `twform_1_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_2`
--
ALTER TABLE `twform_2`
  ADD CONSTRAINT `twform_2_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_3`
--
ALTER TABLE `twform_3`
  ADD CONSTRAINT `twform_3_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `twform_3_ibfk_2` FOREIGN KEY (`eval_id`) REFERENCES `eval_criteria` (`eval_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `twform_3_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_4`
--
ALTER TABLE `twform_4`
  ADD CONSTRAINT `twform_4_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_5`
--
ALTER TABLE `twform_5`
  ADD CONSTRAINT `twform_5_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `twform_5_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_6`
--
ALTER TABLE `twform_6`
  ADD CONSTRAINT `twform_6_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`);

--
-- Constraints for table `tw_forms`
--
ALTER TABLE `tw_forms`
  ADD CONSTRAINT `tw_forms_ibfk_1` FOREIGN KEY (`ir_agenda_id`) REFERENCES `institutional_research_agenda` (`ir_agenda_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tw_forms_ibfk_2` FOREIGN KEY (`col_agenda_id`) REFERENCES `college_research_agenda` (`agenda_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tw_forms_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tw_forms_ibfk_4` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tw_forms_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
