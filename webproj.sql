-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2024 at 11:36 PM
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
-- Database: `webproj`
--

-- --------------------------------------------------------

--
-- Table structure for table `car`
--

CREATE TABLE `car` (
  `car_id` bigint(20) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `car_make` varchar(200) NOT NULL,
  `car_type` varchar(200) NOT NULL,
  `registration_year` int(11) NOT NULL,
  `brief_description` text DEFAULT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `capacity_people` int(11) NOT NULL,
  `capacity_suitcases` int(11) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `fuel_type` enum('petrol','diesel','electric','hybrid') NOT NULL,
  `avg_petroleum_consumption` decimal(10,2) DEFAULT NULL,
  `horsepower` int(11) DEFAULT NULL,
  `length` decimal(10,2) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `gear_type` enum('manual','automatic') NOT NULL,
  `conditions_restrictions` text DEFAULT NULL,
  `photo_filename` varchar(255) DEFAULT NULL,
  `rental_status` enum('available','rented','returning','repair','damaged') NOT NULL DEFAULT 'available',
  `location_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car`
--

INSERT INTO `car` (`car_id`, `car_model`, `car_make`, `car_type`, `registration_year`, `brief_description`, `price_per_day`, `capacity_people`, `capacity_suitcases`, `color`, `fuel_type`, `avg_petroleum_consumption`, `horsepower`, `length`, `width`, `gear_type`, `conditions_restrictions`, `photo_filename`, `rental_status`, `location_id`) VALUES
(1, 'Camry', 'Toyota', 'Sedan', 2020, 'Comfortable sedan suitable for family trips.', 500.00, 5, 2, 'white', 'petrol', 7.50, 180, 4.50, 1.80, 'automatic', 'No smoking, no off-road driving.', 'toyota.png', 'available', 1),
(2, 'Civic', 'Honda', 'Sedan', 2019, 'Compact sedan with good fuel efficiency.', 450.00, 5, 2, 'black', 'petrol', 6.80, 158, 4.60, 1.70, 'automatic', 'No pets allowed.', 'honda.png', 'available', 2),
(53, 'Toyota Camry', 'Toyota', 'Sedan', 2022, 'Comfortable sedan for daily commuting', 800.00, 5, 2, 'Silver', 'petrol', 7.00, 200, 4800.00, 1800.00, 'automatic', 'No smoking allowed', 'toyota_camry.jpg', 'available', 1),
(54, 'Honda CR-V', 'Honda', 'SUV', 2023, 'Spacious SUV for family trips', 600.00, 5, 3, 'Blue', 'petrol', 9.00, 180, 4700.00, 1850.00, 'automatic', 'No pets allowed', 'honda_cr_v.jpg', 'available', 2),
(55, 'Ford Focus', 'Ford', 'Hatchback', 2021, 'Compact hatchback for urban driving', 550.00, 4, 1, 'Red', 'petrol', 6.50, 150, 4500.00, 1700.00, 'manual', 'No off-road driving', 'ford_focus.jpg', 'available', 3),
(56, 'BMW X5', 'BMW', 'SUV', 2023, 'Luxury SUV with premium features', 200.00, 5, 4, 'Black', 'diesel', 8.50, 250, 4900.00, 2000.00, 'automatic', 'No smoking allowed', 'bmw_x5.jpg', 'available', 4),
(57, 'Mercedes-Benz E-Class', 'Mercedes-Benz', 'Sedan', 2022, 'Executive sedan for business trips', 150.00, 4, 2, 'White', 'petrol', 8.00, 220, 4800.00, 1850.00, 'automatic', 'No pets allowed', 'mercedes_e_class.jpg', 'available', 5),
(58, 'Volkswagen Golf', 'Volkswagen', 'Hatchback', 2024, 'Sporty hatchback for urban driving', 700.00, 4, 2, 'Gray', 'petrol', 6.00, 140, 4300.00, 1700.00, 'manual', 'No off-road driving', 'vw_golf.jpg', 'available', 6),
(59, 'Toyota RAV4', 'Toyota', 'SUV', 2023, 'Compact SUV for outdoor adventures', 100.00, 5, 3, 'Green', 'hybrid', 5.50, 180, 4600.00, 1900.00, 'automatic', 'No smoking allowed', 'toyota_rav4.jpg', 'available', 7),
(60, 'Audi A4', 'Audi', 'Sedan', 2022, 'Luxury sedan with advanced technology', 180.00, 4, 2, 'Silver', 'petrol', 7.50, 200, 4700.00, 1800.00, 'automatic', 'No pets allowed', 'audi_a4.jpg', 'available', 3),
(61, 'Ford Explorer', 'Ford', 'SUV', 2023, 'Mid-size SUV with powerful performance', 130.00, 7, 5, 'Blue', 'petrol', 10.00, 280, 5100.00, 2000.00, 'automatic', 'No smoking allowed', 'ford_explorer.jpg', 'available', 5),
(62, 'Hyundai Elantra', 'Hyundai', 'Sedan', 2024, 'Compact sedan with modern design', 600.00, 5, 2, 'Black', 'petrol', 6.50, 170, 4600.00, 1750.00, 'automatic', 'No pets allowed', 'hyundai_elantra.jpg', 'available', 4),
(64, 'M3', 'BMW', 'Sedan', 2022, 'a', 200.00, 4, 4, 'Red', 'petrol', 50.00, 503, 12.00, 12.00, 'manual', 'a', NULL, 'available', 8);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` bigint(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(225) NOT NULL,
  `country` varchar(225) NOT NULL,
  `date_of_birth` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `id_number` varchar(20) NOT NULL,
  `credit_card_number` varchar(16) NOT NULL,
  `credit_card_expiration` date NOT NULL,
  `credit_card_name` varchar(100) NOT NULL,
  `credit_card_bank` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `username`, `password`, `name`, `address`, `city`, `country`, `date_of_birth`, `email`, `telephone`, `id_number`, `credit_card_number`, `credit_card_expiration`, `credit_card_name`, `credit_card_bank`) VALUES
(1, 'john_doe', 'hashed_password', 'John Doe', '789 Oak Street, Birzeit, Palestine', '', '', '1990-05-15', 'john.doe@example.com', '+972-111-222333', '123456789', '1234567890123456', '2025-12-31', 'John Doe', 'BC Bank'),
(2, 'jane_smith', 'hashed_password', 'Jane Smith', '321 Pine Street, Ramallah, Palestine', '', '', '1985-08-20', 'jane.smith@example.com', '+972-333-444555', '987654321', '9876543210987654', '2026-12-31', 'Jane Smith', 'BC Bank'),
(3, 'Ata.Musleh', '$2y$10$kKvjxCxU4jjzYzj19pJpYOLlsQyGCi1OMrQ2N62EJQKUxD0vQYOs2', 'Ata Musleh', 'ra', 'ra', 'palestine', '2004-03-04', 'atamusleh3@gmail.com', '0597332555', '420141020', '456132498651231', '2024-10-24', 'ata musleh', 'palestine'),
(4, 'ata', '$2y$10$v4H./h5KPFnCy.yllV8tK.LDxlfTo4Sl5PqIZW7yuonb2khNqA7p.', 'Ata Musleh', 'ra', 'ra', 'palestine', '2004-03-04', 'atamusleh34@gmail.com', '0597332555', '420141020', '456132498651231', '2024-06-24', 'ata musleh', 'palestine');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `telephone_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `name`, `address`, `telephone_number`) VALUES
(1, 'Birzeit', '123 Main Street, Birzeit, Palestine', '+972-123-456789'),
(2, 'Ramallah', '456 Elm Street, Ramallah, Palestine', '+972-987-654321'),
(3, 'Birzeit', '123 BZU Street, Birzeit, Palestine', '+972-155-467789'),
(4, 'Ramallah', '456 Oak Avenue, Ramallah, Palestine', '+972-123-987654'),
(5, 'Jerusalem', '789 zahra Street, Jerusalem, Palestine', '+972-123-456456'),
(6, 'Bethlehem', '321 Pine Street, Bethlehem, Palestine', '+972-123-789456'),
(7, 'Nablus', '654 Maple Avenue, Nablus, Palestine', '+972-123-654321'),
(8, 'Albireh', 'um alsharayet', '0597332555');

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `manager_id` bigint(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`manager_id`, `username`, `password`, `name`, `email`, `telephone`) VALUES
(1, 'manager1', '$2y$10$micjEsQWcxNRZfWDlUuQGewVfC5VsTNBe9uCv5mgyb2W2fHHZ0mXm', 'Manager One', 'manager1@example.com', '+972-555-666777'),
(2, 'manager2', '$2y$10$ksVfuh23N/WMHRTGUH6squTgzaWhwgYyj45OflXXr4yX8W8GM61ri', 'Manager Two', 'manager2@example.com', '+972-777-888999'),
(3, 'manager', '$2y$10$t/vYu8nfkwgSitdutT1cf.aII2QavIS5IkPpynbUFuxK2soOqbLs6', 'Manager 3', 'managerTEST@gmail.com', '0597332555');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `car_id` bigint(20) NOT NULL,
  `pick_up_date_time` datetime NOT NULL,
  `return_date_time` datetime NOT NULL,
  `pick_up_location_id` bigint(20) NOT NULL,
  `return_location_id` bigint(20) NOT NULL,
  `total_rent_amount` decimal(10,2) NOT NULL,
  `special_requirements` text DEFAULT NULL,
  `payment_details` text DEFAULT NULL,
  `payment_status` varchar(20) NOT NULL,
  `contract_accepted` tinyint(1) NOT NULL,
  `rent_confirmed` tinyint(1) NOT NULL,
  `invoice_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`rental_id`, `customer_id`, `car_id`, `pick_up_date_time`, `return_date_time`, `pick_up_location_id`, `return_location_id`, `total_rent_amount`, `special_requirements`, `payment_details`, `payment_status`, `contract_accepted`, `rent_confirmed`, `invoice_id`) VALUES
(1, 1, 1, '2024-06-15 12:00:00', '2024-06-18 12:00:00', 1, 1, 150.00, 'None', 'Credit card ending in 1234', 'paid', 1, 1, 1234567890),
(2, 2, 2, '2024-06-20 10:00:00', '2024-06-25 10:00:00', 2, 2, 225.00, 'GPS navigation system', 'Credit card ending in 5678', 'paid', 1, 1, 2345678901),
(4, 4, 1, '2024-06-17 00:00:00', '2024-06-20 00:00:00', 1, 1, 1500.00, NULL, NULL, '', 0, 0, 0),
(6, 4, 53, '2024-06-17 00:00:00', '2024-06-20 00:00:00', 1, 3, 2400.00, '', '123456789|3/4/2025|Ata musleh|Visa', '', 1, 1, 9881691589),
(7, 4, 62, '2024-06-17 00:00:00', '2024-06-20 00:00:00', 4, 1, 1800.00, '', '123456789|3/4/2025|Ata musleh|Visa', '', 1, 1, 8489643439),
(8, 4, 1, '2025-06-18 00:00:00', '2025-06-21 00:00:00', 1, 1, 1500.00, '', '123456789|3/4/2025|Ata musleh|Visa', '', 1, 1, 3174187943);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`car_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`manager_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `pick_up_location_id` (`pick_up_location_id`),
  ADD KEY `return_location_id` (`return_location_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `car`
--
ALTER TABLE `car`
  MODIFY `car_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `manager_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `rental_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car`
--
ALTER TABLE `car`
  ADD CONSTRAINT `car_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`);

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `car` (`car_id`),
  ADD CONSTRAINT `rentals_ibfk_3` FOREIGN KEY (`pick_up_location_id`) REFERENCES `location` (`location_id`),
  ADD CONSTRAINT `rentals_ibfk_4` FOREIGN KEY (`return_location_id`) REFERENCES `location` (`location_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
