-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2025 at 10:01 PM
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
  `is_active` tinyint(1) DEFAULT 1,
  `user_type` enum('student','dean','panelist','rmo_staff','research_adviser','chairman') NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`user_id`, `username`, `password`, `firstname`, `lastname`, `contact`, `department_id`, `smc_email`, `is_active`, `user_type`, `date_created`) VALUES
(9, 'C24-0001', '$2y$10$/2dVicSFs7RykcN8M83U8Osh./ZkRM1c3774FW7MioZg.sY4gdlj.', 'nor janah', 'mimbisa', '09123456789', 1, 'norjanah.mimbisa@my.smciligan.edu.ph', 1, 'student', '2024-12-05 00:18:10'),
(10, 'C24-0002', '$2y$10$ZcLz3/48v8w4t1064reln.u/sMwGhdcd7GMBDI4Knd/0s3h1YrGxW', 'Edsel', 'Monterola', '0987123542', 1, 'edsel.monterola@my.smciligan.edu.ph', 1, 'dean', '2024-12-05 01:15:33'),
(11, 'C24-0003', '$2y$10$bezbWiwRbVzuV./ERaZDuOkh2Z5pEWR3fXeh9sb5M7Cbbi7OSRSXe', 'Mj', 'Suarez', '0987123252', 1, 'mj.suarez@my.smciligan.edu.ph', 1, 'panelist', '2024-12-05 01:17:27'),
(12, 'C24-0004', '$2y$10$Wg5jYvydrQszeMR1pEQ7/OktfWNv5YZ17ujvs.ru9r3WpkUZz2AW.', 'Aniceto', 'Naval', '0912387965', 4, 'aniceto.naval@my.smciligan.edu.ph', 1, 'rmo_staff', '2024-12-05 04:31:51'),
(13, 'C24-0005', '$2y$10$YIMoU49fBofaZF2sDIcg6.AxrD1m3IadlqYdqtoiXjYkmRKwSEfda', 'Jerome', 'Abilay', '0912386542', 1, 'jerome.abilay@my.smciligan.edu.ph', 1, 'panelist', '2024-12-07 21:41:55'),
(14, 'C24-0006', '$2y$10$OflrgJAyHfHQOjbAVJxp1.L4J2wtVKi5pI0lQbHrlaz7Lk.TcgKPu', 'Paul Arman', 'Durango', '0912386542', 1, 'paularman.durango@my.smciligan.edu.ph', 1, 'panelist', '2024-12-07 21:42:44'),
(15, 'C24-0007', '$2y$10$IjNrcxbWoqgur93qP3SQgOxJWk1ZaSNdAxCk.0H4.EINavajZdSsa', 'Leonie', 'Abilay', '096556467', 1, 'leonie.abilay@my.smciligan.edu.ph', 1, 'panelist', '2024-12-07 21:49:36'),
(16, 'C24-0008', '$2y$10$p9b1SsTgA1bAobFVuNiLautxMrics1S8fKpAvKw7fQtlHMQkaDPhS', 'james angelo', 'anadon', '0912386542', 1, 'jamesangelo.anadon@my.smciligan.edu.ph', 1, 'student', '2024-12-07 22:47:00'),
(17, 'C24-0009', '$2y$10$1Kn9B9nDr20A/xgmn7FOHun3tDxbbK5Jjg/IxJq7P957CPSMXKN5q', 'Veldin', 'Talorete', '091234567891', 1, 'veldin.talorete@my.smciligan.edu.ph', 1, 'panelist', '2024-12-30 23:07:36'),
(20, 'C25-0001', '$2y$10$adoRKnYFMzZDQhjcv4R/KetC.GrxqOmm/rN8cvVeQkkdw13/LEMhS', 'Jane ', 'Zaportiza', '0987265341', 2, 'jane.zaportiza@my.smciligan.edu.ph', 1, 'dean', '2025-01-01 19:52:14'),
(21, 'C25-0002', '$2y$10$shb6iY1bGBZMEwoXMSqup.CgnbJnLsYij0I9gLdZj6WvZE7WnvU2q', 'Jayrton Manuel', 'Gutib', '091234567654', 2, 'jayrtonmanuel.gutib@my.smciligan.edu.ph', 1, 'student', '2025-01-01 19:59:54'),
(22, 'C25-0003', '$2y$10$OFlUEnzDdPsWZHynZVu/tum/uJwl/DRqYKf0/Y6qvRu7XY8OWUDvK', 'Ayna', 'Salic', '098761234561', 2, 'ayna.salic@my.smciligan.edu.ph', 1, 'student', '2025-01-01 20:00:45'),
(23, 'C25-0004', '$2y$10$a9cws7PH9lYk4ZB20AoA5.X6FabmPucloAdKQNaLi.rb0kznfS.fi', 'LEONARDO', 'DOTARO', '098712345612', 2, 'leonardo.dotaro@my.smciligan.edu.ph', 1, 'panelist', '2025-01-01 20:03:03'),
(24, 'C25-0005', '$2y$10$1YuM6yJ7C7vEx99TTwvayeMDjl5.z.AMZ/0tnp2UDsm8aL2bhI5UO', 'STEPHANIE', 'VISITACION', '098712344325', 2, 'stephanie.visitacion@my.smciligan.edu.ph', 1, 'panelist', '2025-01-01 20:03:36'),
(27, 'C25-0006', '$2y$10$Knhr0Wz3yuWZBRvx.qudkuZGo5y1eHNIp9vOSpJdG89j2QVbGLbL2', 'Jerome ', 'Abilay', '0912388889', 1, 'jerome.abilay@my.smciligan.edu.ph', 1, 'research_adviser', '2025-02-02 03:31:26'),
(28, 'C25-0007', '$2y$10$g/bZpLgNorqgtJqWvh/Rwur1.ugGwHREoz8khoqaO3rY5Es4uJyoS', 'Paul Arman', 'Durango', '09123861245', 1, 'paularman.durango@my.smciligan.edu.ph', 1, 'research_adviser', '2025-02-02 03:41:59'),
(29, 'C25-0008', '$2y$10$1khTvRmOGuYzgQ9QrYive.OxIZcj83HgmXM70KVewdrsFlX3OJtJW', 'Mary Jane', 'Layasan', '09066653988', 1, 'maryjane.layasan@my.smciligan.edu.ph', 1, 'research_adviser', '2025-02-02 03:43:04'),
(30, 'C25-0009', '$2y$10$9A7G/KSiimqyYC7JmpGDi..c65uFrvl4H9CbHh7XCjxUBrPvOgWXu', 'Maria Fe', 'Bahinting', '0912386542', 1, 'mariafe.bahinting@my.smciligan.edu.ph', 1, 'chairman', '2025-02-02 03:45:16');

-- --------------------------------------------------------

--
-- Table structure for table `assigned_chairman`
--

CREATE TABLE `assigned_chairman` (
  `chairman_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `is_selected` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assigned_chairman`
--

INSERT INTO `assigned_chairman` (`chairman_id`, `user_id`, `tw_form_id`, `is_selected`, `date_created`) VALUES
(1, 30, 37, 1, '2025-02-03 01:25:45'),
(2, 30, 33, 1, '2025-02-02 22:29:49'),
(3, 30, 40, 1, '2025-02-03 01:50:13');

-- --------------------------------------------------------

--
-- Table structure for table `assigned_panelists`
--

CREATE TABLE `assigned_panelists` (
  `assigned_panelist_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_selected` tinyint(11) DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assigned_panelists`
--

INSERT INTO `assigned_panelists` (`assigned_panelist_id`, `tw_form_id`, `user_id`, `is_selected`, `date_created`) VALUES
(50, 33, 15, 1, '2025-02-02 21:31:00'),
(51, 33, 13, 1, '2025-02-02 21:31:00'),
(52, 33, 17, 1, '2025-02-02 21:31:00'),
(56, 37, 11, 1, '2025-02-03 01:25:45'),
(57, 37, 15, 1, '2025-02-03 01:25:45'),
(58, 37, 17, 1, '2025-02-03 01:25:45'),
(62, 40, 15, 1, '2025-02-03 01:50:13'),
(63, 40, 11, 1, '2025-02-03 01:50:13'),
(64, 40, 17, 1, '2025-02-03 01:50:13');

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
(9, 37, 'CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', 'proposal_manuscript', '../uploads/manuscripts/CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', '2025-02-02 23:39:54'),
(10, 40, 'CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', 'final Defense manuscript', '../uploads/manuscripts/CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', '2025-02-03 00:29:50'),
(11, 41, 'CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', 'Approval for Binding', '../uploads/manuscripts/CAPSTONE2-ILIGAN- CSWD INVENTORY MANAGEMENT AND MONITORING WITH DECISION SUPPORT.pdf', '2025-02-03 04:22:55');

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
  `department_name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `logo`, `date_created`) VALUES
(1, 'College of Computer Studies', '../uploads/dept_logo/1738434606_CCS Logo 2.0.png', '2025-02-01 22:07:43'),
(2, 'College of Engineering', NULL, '2025-02-01 22:07:43'),
(3, 'College of Business Administration and Accountancy', NULL, '2025-02-01 22:07:43'),
(4, 'Research Management Office', NULL, '2025-02-01 22:07:43');

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
(7, 40, 15, 14, 20, 10, 16, 15, 8, 83.00, 89.00, 'mana', '2025-02-03 03:20:36'),
(8, 37, 15, 15, 20, 9, 18, 17, 7, 86.00, 91.00, 'oki', '2025-02-03 03:32:01');

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
(1, 'Health and Wellbeing for Vulnerable Communities', 'Addressing SDG 3 (Good Health and Well-being), this theme focuses on developing social programs that improve health outcomes for marginalized populations. Research could involve community-based healthcare initiatives, mental health interventions, and policies to reduce health disparities, particularly in response to the current needs highlighted by Philippine government agencies.'),
(2, 'Education for Sustainable Development and Social Transformation', 'Research under this theme would focus on innovating pedagogy and curricula that align with Ignacian Marian traditions, particularly targeting SDG 4 (Quality Educa- tion). Studies could explore interdisciplinary approaches that integrate ethics, social justice, and sustainability into education, aiming to foster a sense of global citizenship and service to the common good.'),
(5, 'Inclusive Economic Growth and Poverty Alleviation', 'This theme is in line with SDG 1 (No Poverty) and SDG 8 (Decent Work and Economic Growth), ex- amining ways to create economic opportunities that are inclusive and sustainable. Areas of research focus may include, but are not confined to, models of social entrepreneurship, programs for livelihood development, and educational trajectories that culminate in sustainable employment, with particular attention to the context of the Philippines.'),
(7, 'Partnerships for Environmental Stewardship and Innovation', '                        Linking to SDG17 (Partnerships for the Goals) and SDG 13 (Climate Action), research could focus on forming responsible partnerships that promote environmental sustainability. Topics can include green technology development, conservation efforts, and sustainable urban planning, leveraging collaborations between academia, industry, and government.'),
(8, 'Social Justice and Ethical Governance', 'In alignment with SDG 16 (Peace, Justice, and Strong Institutions), this theme would explore the intersection of social justice with governance. Research could examine the role of educational institutions in promoting ethical leadership, transparency in government, and policies that uphold human rights and welfare, particularly examining the Philippine governance context and its challenges.');

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
(45, 33, NULL, 'krishnah', 'lorejo', '2025-02-02'),
(46, 33, NULL, 'Zach Emmanuel', 'Villamor', '2025-02-02'),
(49, 35, 25, 'Krishnah', 'Lorejo', '2025-02-02'),
(50, 35, 26, 'Zach Emmanuel', 'Villamor', '2025-02-02'),
(53, 39, 29, 'Krishnah', 'Lorejo', '2025-02-03'),
(54, 39, 30, 'Zach Emmanuel', 'Villamor', '2025-02-03'),
(55, 41, NULL, 'Nor Janah', 'Mimbisa', '2025-02-03'),
(56, 41, NULL, 'James Angelo', 'Anadon', '2025-02-03');

-- --------------------------------------------------------

--
-- Table structure for table `proposed_title`
--

CREATE TABLE `proposed_title` (
  `proposed_title_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `title_name` text NOT NULL,
  `rationale` text NOT NULL,
  `remarks` text DEFAULT NULL,
  `is_selected` tinyint(11) DEFAULT 0,
  `date_created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proposed_title`
--

INSERT INTO `proposed_title` (`proposed_title_id`, `tw_form_id`, `title_name`, `rationale`, `remarks`, `is_selected`, `date_created`) VALUES
(30, 33, 'title 1', 'rationale 1', 'okay rani sya', 1, '2025-02-02'),
(31, 33, 'title 2', 'rationale 2', 'existing naman ni', 0, '2025-02-02'),
(32, 33, 'title 3', 'rationale 3', NULL, 0, '2025-02-02');

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
(25, 35, '679f_Inventory-Diagram-DFD1-welfare.drawio.png', '09782', '2025-02-03', '2025-02-02 22:59:12'),
(26, 35, '679f_Inventory-Diagram-ERD.drawio.png', '09783', '2025-02-04', '2025-02-02 22:59:12'),
(29, 39, '679f_Inventory-Diagram-DFD1-welfare.drawio.png', '09787', '2025-02-04', '2025-02-03 00:18:22'),
(30, 39, '679f_Inventory-Diagram-ERD.drawio.png', '09789', '2025-02-04', '2025-02-03 00:18:22');

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
(12, 33, '4th Year', '2025-02-02 19:54:38', '2025-02-02 19:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `twform_2`
--

CREATE TABLE `twform_2` (
  `form2_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_2`
--

INSERT INTO `twform_2` (`form2_id`, `tw_form_id`, `thesis_title`, `defense_date`, `time`, `place`, `comments`, `date_created`, `last_updated`) VALUES
(14, 35, 'title 1', '2025-02-10', '08:00:00', 'RMO office', NULL, '2025-02-02 22:59:12', '2025-02-03 01:00:40');

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
  `defense_date` date DEFAULT NULL,
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
(5, 37, 9, NULL, 'Iligan city social welfare and development (cswd) office inventory management and monitoring system with decision support', '2025-02-11', '10:32:00', 'NetLab', NULL, 'graded', '2025-02-03');

-- --------------------------------------------------------

--
-- Table structure for table `twform_4`
--

CREATE TABLE `twform_4` (
  `form4_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `date_submitted` date NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_4`
--

INSERT INTO `twform_4` (`form4_id`, `tw_form_id`, `thesis_title`, `defense_date`, `time`, `place`, `date_submitted`, `last_updated`) VALUES
(3, 39, 'title 1', '2025-02-17', '10:41:00', 'networking lab', '2025-02-03', '2025-02-03 01:41:41');

-- --------------------------------------------------------

--
-- Table structure for table `twform_5`
--

CREATE TABLE `twform_5` (
  `form5_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `status` enum('pending','graded') NOT NULL,
  `last_updated` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_5`
--

INSERT INTO `twform_5` (`form5_id`, `tw_form_id`, `student_id`, `thesis_title`, `defense_date`, `time`, `place`, `status`, `last_updated`) VALUES
(3, 40, 9, 'iligan city social welfare and development (CSWD) office inventory management and monitoring system with decision support', '2025-02-17', '08:44:00', 'rmo office', 'graded', '2025-02-03');

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
(2, 41, 'iligan city social welfare and development (CSWD) office inventory management and monitoring system with decision support', NULL, NULL, NULL, '2025-02-03 04:22:55', '2025-02-03 04:22:55');

-- --------------------------------------------------------

--
-- Table structure for table `twform_6_compliance`
--

CREATE TABLE `twform_6_compliance` (
  `compliance_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `document_name` enum('Certificate of Conformity Status','Certificate of Data Gathering','Certificate of Similarity','CV of Certification from Data Analyst','Article Submitted to Repository') NOT NULL,
  `is_checked` tinyint(1) DEFAULT 0,
  `checked_by` int(11) DEFAULT NULL,
  `checked_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `attachment` varchar(255) DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tw_forms`
--

INSERT INTO `tw_forms` (`tw_form_id`, `form_type`, `ir_agenda_id`, `col_agenda_id`, `department_id`, `course_id`, `user_id`, `research_adviser_id`, `overall_status`, `comments`, `attachment`, `submission_date`, `last_updated`) VALUES
(33, 'twform_1', 2, 4, 1, 2, 9, 29, 'approved', 'tw 1 ', 'twform1_1738497278_679f5cfec3196.pdf', '2025-02-02', '2025-02-02 16:39:40'),
(35, 'twform_2', 2, 4, 1, 1, 9, 29, 'pending', NULL, 'twform1_1738508352_679f8840ad665.pdf', '2025-02-02', '2025-02-02 14:59:12'),
(37, 'twform_3', 8, 4, 1, 1, 9, 27, 'pending', NULL, 'twform3_1738510794_679f91ca12c2e.docx', '2025-02-02', '2025-02-02 15:39:54'),
(39, 'twform_4', 2, 4, 1, 1, 9, 29, 'pending', NULL, 'twform4_1738513102_679f9ace2c744.jpeg', '2025-02-03', '2025-02-02 16:18:22'),
(40, 'twform_5', 8, 4, 1, 1, 9, 27, 'approved', NULL, 'twform5_1738513790_679f9d7eecd8a.docx', '2025-02-03', '2025-02-02 20:00:32'),
(41, 'twform_6', 8, 4, 1, 1, 9, 27, 'pending', NULL, 'twform6_1738527775_679fd41f69950.jpeg', '2025-02-03', '2025-02-02 20:22:55');

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
-- Indexes for table `assigned_chairman`
--
ALTER TABLE `assigned_chairman`
  ADD PRIMARY KEY (`chairman_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tw_form_id` (`tw_form_id`);

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
  ADD KEY `twform_6_ibfk_1` (`tw_form_id`);

--
-- Indexes for table `twform_6_compliance`
--
ALTER TABLE `twform_6_compliance`
  ADD PRIMARY KEY (`compliance_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `checked_by` (`checked_by`);

--
-- Indexes for table `tw_forms`
--
ALTER TABLE `tw_forms`
  ADD PRIMARY KEY (`tw_form_id`),
  ADD KEY `ir_agenda_id` (`ir_agenda_id`),
  ADD KEY `col_agenda_id` (`col_agenda_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `research_adviser_id` (`research_adviser_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `assigned_chairman`
--
ALTER TABLE `assigned_chairman`
  MODIFY `chairman_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `assigned_panelists`
--
ALTER TABLE `assigned_panelists`
  MODIFY `assigned_panelist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `institutional_research_agenda`
--
ALTER TABLE `institutional_research_agenda`
  MODIFY `ir_agenda_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `proponents`
--
ALTER TABLE `proponents`
  MODIFY `proponent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `proposed_title`
--
ALTER TABLE `proposed_title`
  MODIFY `proposed_title_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `twform_1`
--
ALTER TABLE `twform_1`
  MODIFY `form1_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `twform_2`
--
ALTER TABLE `twform_2`
  MODIFY `form2_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `twform_3`
--
ALTER TABLE `twform_3`
  MODIFY `form3_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `twform_4`
--
ALTER TABLE `twform_4`
  MODIFY `form4_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `twform_5`
--
ALTER TABLE `twform_5`
  MODIFY `form5_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `twform_6`
--
ALTER TABLE `twform_6`
  MODIFY `form6_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `twform_6_compliance`
--
ALTER TABLE `twform_6_compliance`
  MODIFY `compliance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tw_forms`
--
ALTER TABLE `tw_forms`
  MODIFY `tw_form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assigned_chairman`
--
ALTER TABLE `assigned_chairman`
  ADD CONSTRAINT `assigned_chairman_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assigned_chairman_ibfk_2` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `twform_6_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_6_compliance`
--
ALTER TABLE `twform_6_compliance`
  ADD CONSTRAINT `twform_6_compliance_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `twform_6` (`tw_form_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `twform_6_compliance_ibfk_2` FOREIGN KEY (`checked_by`) REFERENCES `accounts` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `tw_forms`
--
ALTER TABLE `tw_forms`
  ADD CONSTRAINT `tw_forms_ibfk_1` FOREIGN KEY (`ir_agenda_id`) REFERENCES `institutional_research_agenda` (`ir_agenda_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tw_forms_ibfk_2` FOREIGN KEY (`col_agenda_id`) REFERENCES `college_research_agenda` (`agenda_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tw_forms_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tw_forms_ibfk_4` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tw_forms_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tw_forms_ibfk_6` FOREIGN KEY (`research_adviser_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
