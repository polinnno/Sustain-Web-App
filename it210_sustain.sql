-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 13, 2023 at 05:42 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `it210_sustain`
--

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` varchar(3) NOT NULL,
  `sender_id` varchar(6) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `sender_id`, `message`, `sent_at`) VALUES
('6SM', 'W595FL', 'Your website is great, but I have a suggestion. It would be awesome if you could add a feature that allows users to track their volunteer hours.', '2023-10-03 15:52:30'),
('VOL', 'W595FL', 'The website seems to be loading quite slowly for me. Is there anything that can be done to improve its speed?', '2023-10-03 15:53:08'),
('X22', 'W595FL', 'I have a general question about your website and how it works. Can you provide some guidance or direct me to an FAQ section?', '2023-10-03 15:55:00'),
('ZCC', 'W595FL', 'Your website looks fantastic on desktop, but I had some trouble navigating it on my mobile phone. Any plans for a mobile-friendly version?', '2023-10-03 15:53:23');

-- --------------------------------------------------------

--
-- Table structure for table `participation`
--

CREATE TABLE `participation` (
  `user_id` varchar(6) NOT NULL,
  `project_id` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participation`
--

INSERT INTO `participation` (`user_id`, `project_id`) VALUES
('6M2AQR', '5NS6'),
('B6QRLE', 'C3D4'),
('CQCP3W', 'U1V2'),
('EG0RWD', 'JNNA'),
('FRZPE5', '5NS6'),
('IPY1WD', '5NS6'),
('W9S4VV', '5NS6'),
('YQ3NKO', '5NS6');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` varchar(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` char(200) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `organizer_id` varchar(6) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `title`, `description`, `start_date`, `end_date`, `place`, `organizer_id`, `image`) VALUES
('5NS6', 'Community Cleanup', 'Join us in cleaning up our local park and make our community a cleaner place!', '2023-09-09', '2023-09-09', 'Belgrade, Hyde Park', 'CQCP3W', '13.jpg'),
('5T5D', 'Animal Shelter Assistance', 'Volunteer at the local animal shelter and provide care and love to animals in need. We are proud to partner with Animal Compassion Alliance and Paws for a Cause.', '2023-09-10', '2023-09-10', 'City Animal Shelter, Belgrade', 'CQCP3W', '01.jpg'),
('A1B2', 'Clean Belgrade Parks', 'Join us in cleaning and beautifying parks across Belgrade. Help us make our city greener and cleaner.', '2023-12-15', '2023-12-21', 'Central Park, Belgrade, Serbia', 'EG0RWD', '2.2.jpg'),
('C3D4', 'Youth Education Program', 'Volunteer to teach and mentor underprivileged youth in Belgrade. Make a difference in their lives through education.', '2023-12-18', '2023-12-24', 'Local Community Center, Belgrade, Serbia', 'EG0RWD', '20.jpg'),
('C9D0', 'Nature Trail Maintenance', 'Assist in maintaining and improving hiking and nature trails around Belgrade. Ensure safe and enjoyable experiences for outdoor enthusiasts.', '2024-01-16', '2024-01-22', 'Fruška Gora National Park, Belgrade, Serbia', 'EG0RWD', '05.jpg'),
('E5F6', 'Food Drive for the Homeless', 'Help us collect and distribute food to the homeless in Belgrade during the holiday season.', '2023-12-20', '2023-12-26', 'Belgrade City Center, Serbia', 'EG0RWD', '19.jpg'),
('G7H8', 'Environmental Cleanup Crew', 'Join our team to clean up litter and promote environmental awareness in various Belgrade neighborhoods.', '2023-12-22', '2023-12-28', 'Various locations in Belgrade, Serbia', 'EG0RWD', '10.jpg'),
('JNNA', 'Elderly Care Initiative', 'Visit and assist elderly residents in nursing homes, bringing joy and companionship. This program is run in collaboration with ElderCare Support Services and Seniors Matter Most.', '2023-12-11', '2023-12-11', 'Local Nursing Homes', 'CQCP3W', '02.jpg'),
('K1L2', 'Children\'s Holiday Party', 'Help organize a holiday party for children in need. Games, gifts, and fun await at this joyful event.', '2023-12-28', '2024-01-03', 'Community Center, New Belgrade, Serbia', 'EG0RWD', '22.jpg'),
('M3N4', 'Animal Shelter Assistance', 'Volunteer at a local animal shelter in Belgrade. Care for animals and help with daily tasks.', '2023-12-30', '2024-01-05', 'Belgrade Animal Shelter, Serbia', 'EG0RWD', '5.jpg'),
('O5P6', 'Street Art Beautification', 'Join our project to create colorful street art murals in Belgrade. Add vibrancy to the city\'s streets.', '2024-01-02', '2024-01-08', 'Various streets in Belgrade, Serbia', 'EG0RWD', '24.jpg'),
('Q7R8', 'Community Garden Cultivation', 'Get your hands dirty and help cultivate a community garden in Belgrade. Grow fresh produce for the neighborhood.', '2024-01-04', '2024-01-10', 'Belgrade Community Garden, Serbia', 'EG0RWD', '3.jpg'),
('QYJ4', 'Environmental Conservation', 'Contribute to preserving our environment by planting trees and cleaning up rivers. This project is done in collaboration with Green Horizon Environmental Group and Watershed Watchers.', '2023-11-05', '2023-11-06', 'Belgrade', 'CQCP3W', '12.jpg'),
('RAWP', 'Homeless Shelter Support', 'Help run shelters for the homeless and provide warmth and support to those in need. Supported by ShelterCare Alliance and Helping Hands Outreach.', '2023-11-13', '2023-11-15', 'City Homeless Shelter, Belgrade', 'CQCP3W', '09.jpg'),
('RF34', 'Meals for the Homeless', 'Help us provide warm meals to the homeless in our city. We\'ll be cooking and distributing nutritious meals to those in need, spreading kindness and nourishment.', '2023-11-01', '2023-11-01', 'Belgrade', '3OF6D6', '8.jpg'),
('U1V2', 'Wildlife Habitat Restoration', 'Join us in restoring natural habitats for wildlife in Belgrade\'s outskirts. Help preserve native flora and fauna.', '2024-01-08', '2024-01-14', 'Vojvodina Nature Reserve, Belgrade, Serbia', 'EG0RWD', '25.jpg'),
('W3X4', 'Adopt a River Cleanup', 'Participate in our initiative to clean up the banks of the Sava River in Belgrade. Remove trash and debris to protect the river ecosystem.', '2024-01-10', '2024-01-16', 'Sava River, Belgrade, Serbia', 'EG0RWD', '7.jpg'),
('Y5Z6', 'Animal Care at Belgrade Zoo', 'Volunteer at Belgrade Zoo to assist with animal care and enrichment activities. Help create a safe and enriching environment for the animals.', '2024-01-12', '2024-01-18', 'Belgrade Zoo, Serbia', 'EG0RWD', '23.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `project_id` varchar(4) NOT NULL,
  `skill_name` enum('Writing','Teaching','Event Planning','Communication','Technical','Leadership','Artistic','Language','Hands-on Tasks','Nature','Other') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`project_id`, `skill_name`) VALUES
('5NS6', 'Hands-on Tasks'),
('5T5D', 'Technical'),
('5T5D', 'Hands-on Tasks'),
('A1B2', 'Hands-on Tasks'),
('A1B2', 'Nature'),
('C3D4', 'Teaching'),
('C3D4', 'Leadership'),
('C9D0', 'Nature'),
('E5F6', 'Event Planning'),
('E5F6', 'Hands-on Tasks'),
('G7H8', 'Hands-on Tasks'),
('G7H8', 'Nature'),
('JNNA', 'Teaching'),
('JNNA', 'Communication'),
('K1L2', 'Event Planning'),
('K1L2', 'Leadership'),
('M3N4', 'Hands-on Tasks'),
('M3N4', 'Other'),
('O5P6', 'Technical'),
('O5P6', 'Artistic'),
('Q7R8', 'Hands-on Tasks'),
('Q7R8', 'Nature'),
('QYJ4', 'Hands-on Tasks'),
('QYJ4', 'Nature'),
('RAWP', 'Communication'),
('RAWP', 'Hands-on Tasks'),
('RF34', 'Technical'),
('RF34', 'Hands-on Tasks'),
('U1V2', 'Hands-on Tasks'),
('U1V2', 'Nature'),
('W3X4', 'Hands-on Tasks'),
('W3X4', 'Nature'),
('Y5Z6', 'Technical'),
('Y5Z6', 'Hands-on Tasks'),
('Y5Z6', 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`) VALUES
(51, 'mary.stewart@email.com'),
(50, 'michael.scott@office.com'),
(1, 'polly@gmail.com'),
(45, 'story@gmai.com');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('organiser','volunteer') NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `last_name`, `email`, `password`, `role`, `image`) VALUES
('3OF6D6', 'polina', 'kor', 'polkorams@gmail.com', '$2y$10$7SsVYFXUSAEqcHXOho0Y.utVzURi3IyQqOEcjiHWhjB.osdhBqZcu', 'organiser', NULL),
('6M2AQR', 'Galya', 'Ivanova', 'galina@gmail.com', '$2y$10$tJRViQ4xiteq03MIQ.TtDOibSA0tyroRoDFnjZzOFo4pJweHkisYe', 'volunteer', NULL),
('B6QRLE', 'Galya', 'Ivanova', 'ga@gmail.com', '$2y$10$37GkJiNTCHhDZNWokvPDI.bCa4F/VacL8HdTrCWpPRD32qifC0JHO', 'volunteer', '26.jpg'),
('CQCP3W', 'Mary', 'Stewart', 'mary.stewart@email.com', '$2y$10$HMLajR9hHA34phkd9rL/tuqIzrDrxs0D7k.izGHX6Wf.8xrKiEw.u', 'organiser', '2.jpg'),
('EG0RWD', 'Hubert', 'Brown', 'hubert@gmail.com', '$2y$10$Pzc.32qS2BasHSkt1pJY3.gW1wFL.2Dg1xtoX8UHq0XVzcUCBb7MG', 'organiser', '11.jpg'),
('FRZPE5', 'Galya', 'theSecond', 'hubertina@gmail.com', '$2y$10$mXR8XZrT6DQ1irTn0JUKdenp6JsVhes.OMBsSrJIOfW81Qg6EtvZG', 'volunteer', NULL),
('IPY1WD', 'Hubert', 'bb', 'gg@gmail.com', '$2y$10$wiiXYs/6WwE.I33fnTs69eJa.eyJGyBNiQWVZXxOe6DgHHhRBOS5S', 'volunteer', '21.jpg'),
('W595FL', 'Polina', 'Korepanova', 'polly@gmail.com', '$2y$10$ApMQ8xLPSXm.ciRYKUWDUO6X3wGmocEOtCtcTetJAN9XT25dS0/Ni', 'organiser', '12.jpg'),
('W9S4VV', 'Galya', 'Brown', 'paaaaac@gmail.com', '$2y$10$SE2033uf2kGOKsTTOta9d.yDgrQjGIvMWq8LACUpd.z4DtGMoCrQO', 'volunteer', 'user-media\\21.jpg'),
('YQ3NKO', 'polina', 'kor', 'pkpsasc@gmail.com', '$2y$10$rO6goUhkEO407LALTE8GYu6V1CbZih0vPUG1xaoQ0nMDfHsng.UFS', 'organiser', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`user_id`,`project_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`project_id`,`skill_name`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`);

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
