-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 03:14 PM
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
(16, 'C24-0008', '$2y$10$p9b1SsTgA1bAobFVuNiLautxMrics1S8fKpAvKw7fQtlHMQkaDPhS', 'james angelo', 'anadon', '0912386542', 1, 'jamesangelo.anadon@my.smciligan.edu.ph', 'student', '2024-12-07 22:47:00');

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

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `attachment_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(17, 'Linkages and Networking', 3, 'Tracer study of Business Graduates,\r\nEmployability of Graduates');

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
  `evaluator` int(11) NOT NULL,
  `presentation` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `organization` int(11) NOT NULL,
  `mastery` int(11) NOT NULL,
  `ability` int(11) NOT NULL,
  `openness` int(11) NOT NULL,
  `overall_rating` decimal(5,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `date_created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proponents`
--

INSERT INTO `proponents` (`proponent_id`, `tw_form_id`, `firstname`, `lastname`, `date_created`) VALUES
(5, 1, 'krishnah', 'lorejo', '2024-12-11'),
(6, 1, 'Zach Emmanuel', 'Villamor', '2024-12-11');

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
(5, 1, 'Student Attendance Monitoring System', 'This system will provide an automated solution for tracking student attendance in schools or universities. The web-based application allows teachers to easily mark attendance, view attendance history, and generate reports. Students\' attendance records can be accessed and analyzed in real-time, helping to identify trends such as frequent absences. The use of digital attendance management enhances accuracy, saves time, and reduces administrative burden.', 0, '2024-12-11'),
(6, 1, 'Online Exam Management System', 'Rationale:  \r\nThe Online Exam Management System is designed to streamline the process of creating, administering, and grading exams in an academic environment. This system allows instructors to set up and schedule online exams, monitor students’ progress, and provide instant feedback. With features like automated grading and secure exam-taking environments, the system ensures fairness, reduces human error, and provides convenience for both educators and students.', 0, '2024-12-11');

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
  `form_status` enum('pending','approved','rejected','') NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `twform_1`
--

INSERT INTO `twform_1` (`form1_id`, `tw_form_id`, `year_level`, `form_status`, `date_created`, `last_updated`) VALUES
(5, 0, '4', 'pending', '2024-12-08 01:25:07', '2024-12-08 01:25:07'),
(6, 0, '4', 'pending', '2024-12-11 21:44:17', '2024-12-11 21:44:17'),
(7, 1, '4', 'pending', '2024-12-11 21:50:08', '2024-12-11 21:50:08');

-- --------------------------------------------------------

--
-- Table structure for table `twform_2`
--

CREATE TABLE `twform_2` (
  `form2_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `receipt_id` int(11) DEFAULT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date NOT NULL,
  `time` time NOT NULL,
  `place` varchar(255) NOT NULL,
  `form_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `comments` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twform_3`
--

CREATE TABLE `twform_3` (
  `form3_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `eval_id` int(11) DEFAULT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twform_4`
--

CREATE TABLE `twform_4` (
  `form4_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `receipt_id` int(11) DEFAULT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date NOT NULL,
  `time` time NOT NULL,
  `place` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `comments` text DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twform_5`
--

CREATE TABLE `twform_5` (
  `form5_id` int(11) NOT NULL,
  `tw_form_id` int(11) NOT NULL,
  `thesis_title` varchar(255) NOT NULL,
  `defense_date` date NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `comments` text DEFAULT NULL,
  `eval_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tw_forms`
--

CREATE TABLE `tw_forms` (
  `tw_form_id` int(11) NOT NULL,
  `form_type` enum('twform_1','twform_2','twform_3','twform_4','twform_5') NOT NULL,
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
(1, 'twform_1', 1, 4, 1, 2, 9, 14, 'pending', NULL, '2024-12-11', '2024-12-11 13:50:08');

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
  ADD PRIMARY KEY (`eval_id`);

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
  ADD KEY `tw_form_id` (`tw_form_id`);

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
  ADD PRIMARY KEY (`receipt_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `twform_1`
--
ALTER TABLE `twform_1`
  ADD PRIMARY KEY (`form1_id`);

--
-- Indexes for table `twform_2`
--
ALTER TABLE `twform_2`
  ADD PRIMARY KEY (`form2_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `receipt_id` (`receipt_id`);

--
-- Indexes for table `twform_3`
--
ALTER TABLE `twform_3`
  ADD PRIMARY KEY (`form3_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `eval_id` (`eval_id`);

--
-- Indexes for table `twform_4`
--
ALTER TABLE `twform_4`
  ADD PRIMARY KEY (`form4_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `receipt_id` (`receipt_id`);

--
-- Indexes for table `twform_5`
--
ALTER TABLE `twform_5`
  ADD PRIMARY KEY (`form5_id`),
  ADD KEY `tw_form_id` (`tw_form_id`),
  ADD KEY `eval_id` (`eval_id`);

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
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `assigned_panelists`
--
ALTER TABLE `assigned_panelists`
  MODIFY `assigned_panelist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `college_research_agenda`
--
ALTER TABLE `college_research_agenda`
  MODIFY `agenda_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `institutional_research_agenda`
--
ALTER TABLE `institutional_research_agenda`
  MODIFY `ir_agenda_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `proponents`
--
ALTER TABLE `proponents`
  MODIFY `proponent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `proposed_title`
--
ALTER TABLE `proposed_title`
  MODIFY `proposed_title_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `twform_1`
--
ALTER TABLE `twform_1`
  MODIFY `form1_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `twform_2`
--
ALTER TABLE `twform_2`
  MODIFY `form2_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `twform_3`
--
ALTER TABLE `twform_3`
  MODIFY `form3_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `twform_4`
--
ALTER TABLE `twform_4`
  MODIFY `form4_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `twform_5`
--
ALTER TABLE `twform_5`
  MODIFY `form5_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tw_forms`
--
ALTER TABLE `tw_forms`
  MODIFY `tw_form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Constraints for table `proponents`
--
ALTER TABLE `proponents`
  ADD CONSTRAINT `proponents_ibfk_2` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proposed_title`
--
ALTER TABLE `proposed_title`
  ADD CONSTRAINT `proposed_title_ibfk_2` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_2`
--
ALTER TABLE `twform_2`
  ADD CONSTRAINT `twform_2_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `twform_2_ibfk_2` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`receipt_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_3`
--
ALTER TABLE `twform_3`
  ADD CONSTRAINT `twform_3_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `twform_3_ibfk_2` FOREIGN KEY (`eval_id`) REFERENCES `eval_criteria` (`eval_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_4`
--
ALTER TABLE `twform_4`
  ADD CONSTRAINT `twform_4_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `twform_4_ibfk_2` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`receipt_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `twform_5`
--
ALTER TABLE `twform_5`
  ADD CONSTRAINT `twform_5_ibfk_1` FOREIGN KEY (`tw_form_id`) REFERENCES `tw_forms` (`tw_form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `twform_5_ibfk_2` FOREIGN KEY (`eval_id`) REFERENCES `eval_criteria` (`eval_id`);

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
