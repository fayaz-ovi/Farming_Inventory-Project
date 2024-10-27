-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2024 at 07:12 PM
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
-- Database: `farming_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ADMIN_ID` int(11) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `BALANCE` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ADMIN_ID`, `PASSWORD`, `BALANCE`) VALUES
(1, '123', 385.35);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(12) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`, `price`) VALUES
(1, 5, 52, 1, 45.00),
(2, 5, 67, 1, 220.00);

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `checkout_id` int(12) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkout`
--

INSERT INTO `checkout` (`checkout_id`, `user_id`, `product_id`, `quantity`, `total_amount`) VALUES
(1, 8, 77, 3, 630.00);

-- --------------------------------------------------------

--
-- Table structure for table `farmer`
--

CREATE TABLE `farmer` (
  `user_id` int(11) NOT NULL,
  `total_earning` decimal(10,2) DEFAULT NULL,
  `product_sold_count` int(11) DEFAULT 0,
  `bank_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `account_no` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer`
--

INSERT INTO `farmer` (`user_id`, `total_earning`, `product_sold_count`, `bank_name`, `branch`, `account_no`) VALUES
(8, 600.00, 3, 'BRAC Bank', 'mirpur', '12222'),
(0, 3665.00, 16, 'Rupali Bank', '1234', '44453'),
(76, 0.00, 0, 'BRAC Bank', 'mirpur', '12222');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `PRODUCT_ID` int(12) NOT NULL,
  `NAME` varchar(50) NOT NULL,
  `CATEGORY` varchar(50) NOT NULL,
  `QUANTITY` int(11) NOT NULL,
  `RATING` int(11) NOT NULL,
  `REVIEW` varchar(255) DEFAULT NULL,
  `PRICE` decimal(10,2) NOT NULL,
  `photo` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`PRODUCT_ID`, `NAME`, `CATEGORY`, `QUANTITY`, `RATING`, `REVIEW`, `PRICE`, `photo`, `user_id`) VALUES
(1, 'Deshi Peyaj (Local Onion) ± 50 gm', 'Vegetables', 10, 0, NULL, 119.00, 'https://chaldn.com/_mpimage/deshi-peyaj-local-onion-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D52358&q=low&v=1&m=400', 0),
(2, 'Potato Regular (± 50 gm)', 'Vegetables', 10, 0, NULL, 59.00, 'https://chaldn.com/_mpimage/potato-regular-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D81244&q=best&v=1&m=400', 0),
(3, 'Roshun (Garlic Imported) ± 25 gm', 'Vegetables', 10, 0, NULL, 120.00, 'https://chaldn.com/_mpimage/roshun-garlic-imported-25-gm-500-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D89993&q=best&v=1&m=400', 0),
(4, 'Red Tomato ± 25 gm', 'Vegetables', 10, 0, NULL, 80.00, 'https://chaldn.com/_mpimage/red-tomato-25-gm-500-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D64361&q=best&v=1&m=400', 0),
(5, 'Kacha Morich (Green Chilli) ±12 gm', 'Vegetables', 10, 0, NULL, 75.00, 'https://chaldn.com/_mpimage/kacha-morich-green-chilli-12-gm-250-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D28576&q=low&v=1&m=400', 0),
(6, 'Coriander Leaves (Dhonia Pata) ± 10 gm', 'Vegetables', 10, 0, NULL, 30.00, 'https://chaldn.com/_mpimage/coriander-leaves-dhonia-pata-10-gm-100-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D28562&q=low&v=1&m=400', 0),
(7, 'Potol (Pointed Gourd) ± 25 gm', 'Vegetables', 10, 0, NULL, 30.00, 'https://chaldn.com/_mpimage/potol-pointed-gourd-25-gm-500-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D35666&q=best&v=1&m=400', 0),
(8, 'Lal Alu (Red Potato Cardinal) ± 50 gm', 'Vegetables', 10, 0, NULL, 60.00, 'https://chaldn.com/_mpimage/lal-alu-red-potato-cardinal-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D76922&q=low&v=1&m=400', 0),
(9, 'Dheros (Ladies Finger) ± 25 gm', 'Vegetables', 10, 0, NULL, 35.00, 'https://chaldn.com/_mpimage/dheros-ladies-finger-25-gm-500-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D28563&q=low&v=1&m=400', 0),
(10, 'Ada (Imported Ginger) ± 25 gm', 'Vegetables', 5, 0, NULL, 180.00, 'https://chaldn.com/_mpimage/ada-imported-ginger-25-gm-500-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D22301&q=low&v=1&m=400', 0),
(11, 'Lal Peyaj (Onion Red Imported) ± 50 gm', 'Vegetables', 10, 0, NULL, 115.00, 'https://chaldn.com/_mpimage/lal-peyaj-onion-red-imported-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D69767&q=best&v=1&m=400', 0),
(12, 'Deshi Shosha (Local Cucumber) ± 25 gm', 'Vegetables', 10, 0, NULL, 39.00, 'https://chaldn.com/_mpimage/deshi-shosha-local-cucumber-25-gm-500-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D64359&q=best&v=1&m=400', 0),
(13, 'Lal Shak (Red Spinach)', 'Vegetables', 10, 0, NULL, 29.00, 'https://chaldn.com/_mpimage/lal-shak-red-spinach-1-bundle?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D7226&q=low&v=1&m=400', 0),
(14, 'Boro Alu (Big Diamond Potato) ± 50 gm', 'Vegetables', 10, 0, NULL, 60.00, 'https://chaldn.com/_mpimage/boro-alu-big-diamond-potato-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D79694&q=low&v=1&m=400', 0),
(15, 'Misti Kumra Fali (Sweet Pumpkin Slice) ± 40 gm', 'Vegetables', 10, 0, NULL, 49.00, 'https://chaldn.com/_mpimage/misti-kumra-fali-sweet-pumpkin-slice-40-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D28900&q=low&v=1&m=400', 0),
(16, 'Lomba Lebu (Long Lemon)', 'Vegetables', 10, 0, NULL, 29.00, 'https://chaldn.com/_mpimage/lomba-lebu-long-lemon-4-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D64362&q=low&v=1&m=400', 0),
(17, 'Chichinga (Snake Gourd) ± 25 gm', 'Vegetables', 10, 0, NULL, 35.00, 'https://chaldn.com/_mpimage/chichinga-snake-gourd-25-gm-500-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D28583&q=low&v=1&m=400', 0),
(18, 'Kacha Pepe (Green Papaya) ± 70 gm', 'Vegetables', 10, 0, NULL, 70.00, 'https://chaldn.com/_mpimage/kacha-pepe-green-papaya-70-gm-14-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D69930&q=low&v=1&m=400', 0),
(19, 'Kolmi Shak (Water Spinach)', 'Vegetables', 10, 0, NULL, 15.00, 'https://chaldn.com/_mpimage/kolmi-shak-water-spinach-1-bundle?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D28561&q=low&v=1&m=400', 0),
(22, 'Deshi Roshun (Garlic Local) ±25 gm', 'Vegetables', 10, 0, NULL, 119.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=89990', 0),
(23, 'Premium Koi Fish Medium and Deshi Roshun (Combo)', 'Fish', 10, 0, NULL, 369.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=151602', 8),
(24, 'Premium Pangas Fish Headless and Deshi Roshun (Com', 'Fish', 10, 0, NULL, 338.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=151604', 8),
(25, 'Palong Shak (Palong Spinach)', 'Vegetables', 10, 0, NULL, 35.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=28579', 0),
(26, 'Borboti (Long Bean) ± 25 gm', 'Vegetables', 10, 0, NULL, 49.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=22410', 0),
(27, 'Lau (Bottle Gourd)', 'Vegetables', 10, 0, NULL, 85.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=28573', 0),
(28, 'Misti Alu (Sweet Potato) ± 25 gm', 'Vegetables', 10, 0, NULL, 65.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=54835', 0),
(29, 'Lomba Kalo Begun (Long Brinjal Black) ± 25 gm', 'Vegetables', 10, 0, NULL, 50.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=43558', 0),
(30, 'Gol Lebu (Round Lemon)', 'Vegetables', 10, 0, NULL, 35.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=54834', 0),
(31, 'Green Capsicum ± 15 gm', 'Vegetables', 10, 0, NULL, 119.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=28574', 0),
(32, 'Korola (Bitter Gourd) ± 25 gm', 'Vegetables', 10, 0, NULL, 49.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=22416', 0),
(33, 'Beetroot ±25 gm', 'Vegetables', 10, 0, NULL, 90.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=35682', 0),
(34, 'Uchche (Local Bitter Gourd) ± 25 gm', 'Vegetables', 10, 0, NULL, 65.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=15396', 0),
(35, 'Kacha Kola (Banana Green)', 'Vegetables', 10, 0, NULL, 60.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=83135', 0),
(36, 'Pui Shak (Pui Spinach)', 'Vegetables', 10, 0, NULL, 40.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=7071', 0),
(37, 'Jhinga (Ridge Gourd) ±20 gm', 'Vegetables', 10, 0, NULL, 50.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=120815', 0),
(38, 'Dhundhul (Sponge Gourd) ±20 gm', 'Vegetables', 10, 0, NULL, 39.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=77228', 0),
(39, 'Jali Kumra (Water Pumpkin)', 'Vegetables', 10, 0, NULL, 70.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=69932', 0),
(40, 'Sobuj Gol Begun (Round Brinjals Green) ±35 gm', 'Vegetables', 10, 0, NULL, 85.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=28795', 0),
(41, 'Kochur Loti (Stolon Of Taro) ± 25 gm', 'Vegetables', 10, 0, NULL, 50.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=64498', 0),
(42, 'Kochur Mukhi (Taro Roots) ± 25 gm', 'Vegetables', 10, 0, NULL, 50.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=28965', 0),
(43, 'Kakrol (Sweet Bitter Gourd) ±25 gm', 'Vegetables', 10, 0, NULL, 49.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=64496', 0),
(44, 'Kalo Gol Begun (Round Brinjals Black) ±35 gm', 'Vegetables', 10, 0, NULL, 100.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=7893', 0),
(45, 'Pudina Pata (Mint Leaves) ± 10 gm', 'Vegetables', 10, 0, NULL, 49.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=7827', 0),
(46, 'Kochu Shak', 'Vegetables', 10, 0, NULL, 19.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=124245', 0),
(47, 'Kagozi Lebu (Kagozi Lemon)', 'Vegetables', 10, 0, NULL, 30.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=43758', 0),
(48, 'Data Shak (Data Spinach)', 'Vegetables', 10, 0, NULL, 29.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=30176', 0),
(49, 'Lal Alu (Red Potato) ± 25 gm', 'Vegetables', 10, 0, NULL, 50.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=93643', 0),
(50, 'Bombay Chilli', 'Vegetables', 10, 0, NULL, 55.00, 'https://eggyolk.chaldal.com/api/Picture/Raw?pictureId=120812', 0),
(51, 'Shagor Kola (Banana Sagor)', 'Fruits', 10, 1, NULL, 50.00, 'https://chaldn.com/_mpimage/shagor-kola-banana-sagor-4-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D76879&q=best&v=1&m=400', 8),
(52, 'Banana Sobri', 'Fruits', 8, 1, NULL, 45.00, 'https://chaldn.com/_mpimage/banana-sobri-4-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D76878&q=low&v=1&m=400', 8),
(53, 'Bangla Kola', 'Fruits', 10, 1, NULL, 50.00, 'https://chaldn.com/_mpimage/bangla-kola-4-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D79398&q=low&v=1&m=400', 8),
(54, 'Malta ± 50 gm', 'Fruits', 10, 1, NULL, 340.00, 'https://chaldn.com/_mpimage/malta-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D131779&q=best&v=1&m=400', 8),
(55, 'Guava Premium (± 50 gm)', 'Fruits', 10, 1, NULL, 100.00, 'https://chaldn.com/_mpimage/guava-premium-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D67818&q=best&v=1&m=400', 8),
(56, 'Daab (Green Coconut)', 'Fruits', 10, 1, NULL, 150.00, 'https://chaldn.com/_mpimage/daab-green-coconut-1-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D40755&q=best&v=1&m=400', 8),
(57, 'Cherry Pineapple (Cherry Anaros)', 'Fruits', 10, 1, NULL, 59.00, 'https://chaldn.com/_mpimage/cherry-pineapple-cherry-anaros-1-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D77400&q=best&v=1&m=400', 8),
(58, 'Green Apple ± 50 gm', 'Fruits', 10, 1, NULL, 499.00, 'https://chaldn.com/_mpimage/green-apple-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D119065&q=best&v=1&m=400', 8),
(59, 'China Fuji Apple ± 50 gm', 'Fruits', 10, 1, NULL, 320.00, 'https://chaldn.com/_mpimage/china-fuji-apple-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D114752&q=best&v=1&m=400', 8),
(60, 'Shada Nashpati (Pear White)', 'Fruits', 10, 1, NULL, 169.00, 'https://chaldn.com/_mpimage/shada-nashpati-pear-white-2-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D71168&q=best&v=1&m=400', 8),
(61, 'Anaros (Pineapple)', 'Fruits', 8, 1, NULL, 70.00, 'https://chaldn.com/_mpimage/anaros-pineapple-1-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D61358&q=low&v=1&m=400', 8),
(62, 'Gala Apple ± 50 gm', 'Fruits', 10, 1, NULL, 339.00, 'https://chaldn.com/_mpimage/gala-apple-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D114751&q=best&v=1&m=400', 8),
(63, 'Paka Pape ± 50 gm (Thai)', 'Fruits', 10, 1, NULL, 160.00, 'https://chaldn.com/_mpimage/paka-pape-50-gm-thai-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D131627&q=best&v=1&m=400', 8),
(64, 'Dalim (Pomegranate)', 'Fruits', 10, 1, NULL, 339.00, 'https://chaldn.com/_mpimage/dalim-pomegranate-2-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D28589&q=best&v=1&m=400', 8),
(65, 'Komola (Orange) Imported ± 50 gm', 'Fruits', 10, 1, NULL, 360.00, 'https://chaldn.com/_mpimage/komola-orange-imported-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D64292&q=best&v=1&m=400', 8),
(66, 'Narikel (Coconut)', 'Fruits', 10, 1, NULL, 150.00, 'https://chaldn.com/_mpimage/narikel-coconut-1-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D94768&q=best&v=1&m=400', 8),
(67, 'Dragon Fruit Local (± 50 gm)', 'Fruits', 10, 1, NULL, 220.00, 'https://chaldn.com/_mpimage/dragon-fruit-local-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D93407&q=best&v=1&m=400', 8),
(68, 'Lal Angur (Red Grapes) ± 12 gm', 'Fruits', 10, 1, NULL, 140.00, 'https://chaldn.com/_mpimage/lal-angur-red-grapes-12-gm-250-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D26171&q=best&v=1&m=400', 8),
(69, 'Lotkon (Baccaurea Motleyana) ± 50 gm', 'Fruits', 10, 1, NULL, 129.00, 'https://chaldn.com/_mpimage/lotkon-baccaurea-motleyana-50-gm-500-gm?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D61087&q=best&v=1&m=400', 8),
(70, 'Jambura (Pomelo)', 'Fruits', 10, 1, NULL, 100.00, 'https://chaldn.com/_mpimage/jambura-pomelo-1-pcs?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D27679&q=best&v=1&m=400', 8),
(74, 'Aamra ± 50 gm', 'Fruits', 7, 1, NULL, 229.00, 'https://chaldn.com/_mpimage/amra-hog-plum-50-gm-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D123315&q=best&v=1&m=400&webp=1', 8),
(76, 'Cherry Tomato', 'Vegetables', 10, 1, NULL, 199.00, 'https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcRr0_f1C3SO9iQBXhYe7T7dA219xB2crMwPizATiyAPR6Tbz8x5', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `customer_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `userType` enum('customer','farmer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`customer_id`, `username`, `password`, `phone`, `name`, `address`, `userType`) VALUES
(0, 'farmer1@gmail.com', '$2y$10$rMErCHSrj4poNYRqCmPzxuTepfY9AFYLqSBEm8cD0jaCyyn2Y74LO', '01986394944', 'farmer1', 'asdfasdfasd', 'farmer'),
(1, 'faiyaz.islam@g.bracu.ac.bd', '$2y$10$PqEps1VtzjaI9yuTYHE2sOQ/eInpPxF07AOJ1hARChafp5wYK7WcG', '01986777', 'faiyaz Islam', 'asdfasdfasd', 'customer'),
(3, 'islamfaiyaz77', '$2y$10$bjSlM5LzOdUA1yJfsUYNR.jI8g5FW/5dSi.0OlEt0qAXCA6EVqQ36', '01998', 'dasfafa', 'asdfasdfasd', 'customer'),
(4, 'islamfaiyaz777', '$2y$10$wtzlbKnRyPjCWs0mNNxllOx9NsXQZHvLGce86.CyLrg08EsR0zNV.', '019989', 'dasfafa', 'asdfasdfasd', 'customer'),
(5, 'customer2@gmail.com', '$2y$10$gHr85ETk.bFfHMLNSRFsH.Iwst11IV.eKASgCW17/hw0pU3bDMoyG', '12345678999', 'customer', 'asdfasdfasd', 'customer'),
(6, 'customer1@gmail.com', '$2y$10$/j5dESkVVxQfLUN9.lgsMuTGhr8ItbcK02dae3CtQITFVixDkGP1G', '12345678910', 'faiyaz islam', 'asdfasdfasd', 'customer'),
(8, 'farmer2@gmail.com', '$2y$10$tzylvgUaSIdnYtAe/QqkMO15tm5i436fqYCXuSWYMkaVj/hmd9yzO', '01816991589', 'farmer22', 'asdfasdfasd', 'farmer'),
(76, 'farmer4@gmail.com', '$2y$10$OF8QXctopasxuHb3.W2bNuPOGQEHUrMwow05uK8kKxlrmQ5VQWoPK', '01234586728', 'farmer4', 'asdfasdfasd', 'farmer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`checkout_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`PRODUCT_ID`),
  ADD KEY `fk_product_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `checkout_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `PRODUCT_ID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
