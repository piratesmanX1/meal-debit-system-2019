-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 24, 2019 at 04:27 PM
-- Server version: 5.7.21
-- PHP Version: 5.6.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `meal_debit_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the admin list.',
  `first_name` varchar(255) NOT NULL COMMENT 'User''s first name.',
  `last_name` varchar(255) NOT NULL COMMENT 'User''s last name.',
  `gender` char(1) NOT NULL COMMENT 'User''s gender.',
  `profile_image` text COMMENT 'User''s profile image.',
  `admin_approved` int(11) DEFAULT NULL COMMENT 'Admin''s ID that approved the Admin.',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID of the user account.',
  PRIMARY KEY (`admin_id`) USING BTREE,
  KEY `user_id` (`user_id`),
  KEY `admin_approved` (`admin_approved`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `first_name`, `last_name`, `gender`, `profile_image`, `admin_approved`, `user_id`) VALUES
(2, 'J_', 'DEFALT', '0', NULL, 1, 1),
(3, 'Admin', 'Test', '1', NULL, 1, 54);

-- --------------------------------------------------------

--
-- Table structure for table `balance_record`
--

DROP TABLE IF EXISTS `balance_record`;
CREATE TABLE IF NOT EXISTS `balance_record` (
  `balance_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the balance record.',
  `update_amount` decimal(15,2) DEFAULT NULL COMMENT 'The total amount of the balance being updated, by reduction through transaction, or increment through top-up.',
  `update_date` datetime NOT NULL COMMENT 'The date of the update being made.',
  `update_method` char(1) NOT NULL COMMENT 'The methods of the update being made, such as through top-up, or through reduction from transaction. (0 = Top Up, 1 = Transaction)',
  `balance_amount` decimal(15,2) DEFAULT NULL COMMENT 'The amount of balance updated from the top-up. (Will first take the data from the student table: balance, to prevent any miscalculation.)',
  `user_id` int(11) DEFAULT NULL COMMENT 'The ID of the user account that process the changes. (Note: If the it''s cashier who processed the transaction, it will still the user_id being affected by.)',
  PRIMARY KEY (`balance_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `balance_record`
--

INSERT INTO `balance_record` (`balance_id`, `update_amount`, `update_date`, `update_method`, `balance_amount`, `user_id`) VALUES
(4, '-280.00', '2019-04-17 23:22:17', '1', '920.00', 52),
(5, '-575.00', '2019-04-18 08:41:00', '1', '345.00', 52),
(7, '-26.10', '2019-04-18 12:58:40', '1', '318.90', 52),
(8, '65.00', '2019-04-19 05:25:44', '0', '453.90', 52),
(9, '110.00', '2019-04-19 05:26:24', '0', '130.00', 56),
(10, '-159.00', '2019-04-19 08:43:13', '1', '294.90', 52),
(11, '-126.10', '2019-04-20 12:08:10', '1', '168.80', 52),
(12, '150.00', '2019-04-20 12:11:00', '0', '280.00', 56),
(13, '-25.00', '2019-04-23 17:13:00', '1', '143.80', 52),
(14, '-9.20', '2019-04-23 17:52:19', '1', '134.60', 52),
(15, '-15.30', '2019-04-23 18:02:56', '1', '119.30', 52),
(16, '-15.00', '2019-04-23 18:03:28', '1', '104.30', 52),
(17, '100.00', '2019-04-25 00:18:13', '0', '204.30', 52),
(20, '100.00', '2019-04-25 00:26:09', '0', '100.00', 58);

-- --------------------------------------------------------

--
-- Table structure for table `cashier`
--

DROP TABLE IF EXISTS `cashier`;
CREATE TABLE IF NOT EXISTS `cashier` (
  `cashier_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the cashier list.',
  `first_name` varchar(255) NOT NULL COMMENT 'User''s first name.',
  `last_name` varchar(255) NOT NULL COMMENT 'User''s last name.',
  `gender` char(1) NOT NULL COMMENT 'User''s gender.',
  `dob` datetime NOT NULL COMMENT 'User''s day of birth.',
  `profile_image` text COMMENT 'User''s profile image.',
  `admin_approved` int(11) DEFAULT NULL COMMENT 'Record of which admin approved the registration of the account. (Its a user_id.)',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID of the user account.',
  PRIMARY KEY (`cashier_id`) USING BTREE,
  KEY `user_id` (`user_id`),
  KEY `admin_approved` (`admin_approved`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cashier`
--

INSERT INTO `cashier` (`cashier_id`, `first_name`, `last_name`, `gender`, `dob`, `profile_image`, `admin_approved`, `user_id`) VALUES
(1, 'Cashier', 'Test', '2', '1991-11-11 14:22:00', NULL, 1, 53),
(2, 'Ooi', 'JJJ', '1', '1999-02-22 14:22:00', NULL, 1, 55),
(3, 'BEN', 'GAYY', '2', '1998-12-22 14:22:00', NULL, 1, 57),
(4, 'TEST', 'testt', '1', '1999-12-12 14:22:00', NULL, 1, 59);

-- --------------------------------------------------------

--
-- Table structure for table `meal`
--

DROP TABLE IF EXISTS `meal`;
CREATE TABLE IF NOT EXISTS `meal` (
  `meal_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the meal.',
  `active` char(1) NOT NULL COMMENT 'It determines the meal is whether active, or not.',
  `meal_brand_id` int(11) DEFAULT NULL COMMENT 'The brand''s ID of the meal.',
  `meal_name` varchar(255) DEFAULT NULL COMMENT 'The name of the meal.',
  `meal_image` text COMMENT 'The image of the meal.',
  `meal_details` varchar(2555) NOT NULL COMMENT 'The meal''s details.',
  `meal_price` decimal(15,2) NOT NULL COMMENT 'The price of the meal.',
  `meal_quantity` decimal(60,0) NOT NULL COMMENT 'The meal''s quantity.',
  `meal_additional_quantity` decimal(60,0) NOT NULL COMMENT 'The meal''s remaining quantity. Recorded to avoid confusion and to calculate precisely the number of the products being sold during the month.',
  `meal_default_quantity` decimal(60,0) NOT NULL COMMENT 'The default quantity of the meal, which will be restock-ed every month, aka added to the quantity of the meal.',
  `admin_id` int(11) DEFAULT NULL COMMENT 'The Admin that registered the meal. (It''s a user_id.)',
  PRIMARY KEY (`meal_id`),
  UNIQUE KEY `meal_name` (`meal_name`) USING BTREE,
  KEY `admin_id` (`admin_id`),
  KEY `meal_brand_id` (`meal_brand_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meal`
--

INSERT INTO `meal` (`meal_id`, `active`, `meal_brand_id`, `meal_name`, `meal_image`, `meal_details`, `meal_price`, `meal_quantity`, `meal_additional_quantity`, `meal_default_quantity`, `admin_id`) VALUES
(1, '1', 1, 'Detroit Pizza', '/APU/SDP/image/m1.jpg', 'The brand has chosen to pair waffles with its Extra Crispy variety in the form of tenders, bone-in chicken, or a Hot Honey filet for the sandwich version. Testing', '14.00', '29', '10', '20', 1),
(2, '1', 1, 'Pineapple Pizza', '/APU/SDP/image/m2.jpeg', 'A pizza topped with tomato sauce, cheese, pineapple, and back bacon or ham.', '11.00', '39', '25', '15', 1),
(3, '1', 2, 'Anadama Bread', '/APU/SDP/image/m6.jpg', 'A sweet, cornmeal and molasses based bread.', '6.00', '80', '40', '40', 1),
(4, '1', 2, 'Anpan', '/APU/SDP/image/m7.jpg', 'Filled, usually with red bean paste, or with white beans, sesame, or chestnut.', '8.00', '75', '35', '40', 1),
(5, '1', 1, 'Authentic Neapolitan Pizza ', '/APU/SDP/image/m3.jpg', 'Made with San Marzano tomatoes, grown on the volcanic plains south of Mount Vesuvius, and mozzarella di bufala Campana, made with milk from water buffalo raised in the marshlands of Campania and Lazio.', '19.00', '46', '22', '30', 1),
(6, '1', 1, 'Pizza Quattro Formaggi', '/APU/SDP/image/m4.jpg', 'The \"pizza ai quattro formaggi\" at La Porchetta, Chalk Farm Road.', '30.00', '34', '15', '20', 1),
(7, '1', 1, 'Chicago-style Pizza', '/APU/SDP/image/m5.jpg', 'hicago-style pizza is pizza prepared according to several different styles developed in Chicago.', '15.00', '34', '10', '25', 1),
(8, '1', 2, 'Banana Bread', '/APU/SDP/image/m8.jpg', 'Banana bread is a type of bread made from mashed bananas. It is often a moist, sweet, cake-like quick bread; however, there are some banana bread recipes that are traditional-style raised breads.', '6.50', '45', '15', '30', 1),
(9, '1', 2, 'Toast', '/APU/SDP/image/m9.jpg', 'Toast is a form of bread that has been browned by exposure to radiant heat.', '4.50', '50', '20', '30', 1),
(10, '1', 2, 'Sourdough', '/APU/SDP/image/m10.jpg', 'Sourdough bread is made by the fermentation of dough using naturally occurring lactobacilli and yeast.', '6.50', '45', '15', '30', 1),
(11, '1', 3, 'Kentucky Fried Chicken & Waffle', '/APU/SDP/image/m11.jpg', 'The brand has chosen to pair waffles with its Extra Crispy variety in the form of tenders, bone-in chicken, or a Hot Honey filet for the sandwich version', '14.00', '31', '11', '20', 4),
(12, '1', 3, 'KFC Ghost Pepper', '/APU/SDP/image/m12.jpg', 'Inspired by the flavours of Ghost Pepper, which is known as one of the worldï¿½s hottest chili pepper, this latest innovation is set to send a fiery heat thrill that grows with every bite.', '18.00', '21', '3', '25', 4),
(13, '1', 3, 'Air Fryer Chicken Tenders', '/APU/SDP/image/m13.jpg', 'The first uses almonds as a base and the nut-free version makes a simple swap for ground flax seeds.', '8.00', '18', '2', '25', 4),
(14, '1', 3, 'Honey BBQ Sandwich', '/APU/SDP/image/m14.jpg', 'Country-breaded, all-white chicken tenders smothered in sweet honey BBQ sauce, melted cheddar cheese, applewood-smoked bacon and Ranch dressing on freshly grilled sourdough bread.', '15.00', '20', '6', '20', 4),
(15, '1', 3, 'KFC Famous Bowl Snack Size', '/APU/SDP/image/m15.jpg', 'KFC Famous Bowl are layers of flavor with mashed potatoes topped with sweet corn, bite-sized crispy chicken, home-style gravy and our three cheese blend.', '7.00', '26', '2', '25', 4),
(16, '1', 4, 'McDonald Hamburger', '/APU/SDP/image/m16.jpg', 'A hamburger, beefburger or burger is a sandwich consisting of one or more cooked patties of ground meat, usually beef, placed inside a sliced bread roll or bun. ', '8.00', '25', '5', '20', 4),
(17, '1', 4, 'McDonald Big Mac', '/APU/SDP/image/m17.jpg', 'The Big Mac is a hamburger sold by international fast food restaurant chain McDonald\'s. ', '9.30', '16', '2', '15', 4),
(18, '1', 4, 'McDonald Quarter Pounder', '/APU/SDP/image/m18.jpg', 'The Quarter Pounder is a hamburger sold by international fast food chain McDonald\'s, so named for containing a patty with a precooked weight of a quarter of a pound (113.4 g).', '8.60', '28', '8', '20', 4),
(19, '1', 4, 'McDonald Big N\' Tasty', '/APU/SDP/image/m19.jpg', 'The Big N\' Tasty consists of a seasoned quarter-pound (4 oz, 113.4 g) beef meat patty with ketchup, mayonnaise, slivered onions, two dill pickle slices, leaf lettuce, and one tomato slice on a sesame seed bun.', '7.20', '30', '5', '25', 4),
(20, '1', 4, 'McDonald Cheeseburger', '/APU/SDP/image/m20.jpg', 'A cheeseburger is a hamburger topped with cheese. Traditionally, the slice of cheese is placed on top of the meat patty, but the burger can include variations in structure, ingredients and composition. The cheese is typically added to the cooking hamburger patty shortly before serving, which allows the cheese to melt.', '7.80', '26', '6', '20', 4),
(21, '1', 5, 'PAPPA ABC', '/APU/SDP/image/m21.jpg', '“Ais Batu Campur” or mixed shaved ice is the Malaysian fix for our tropical heat. A wonderful concoction of shaved ice and a myriad of toppings—cendol jelly, red beans, cincau, peanuts, and sweetcorn—with a shower of gula melaka, syrup, and condensed milk to sweeten the deal.', '12.80', '50', '25', '25', 4),
(22, '1', 5, 'PAPPA ABC Special with Ice Cream', '/APU/SDP/image/m22.jpg', 'Take your regular ABC experience up a notch as we pamper you with our dessert master. A mountain of toppings—cendol jelly, red beans, cincau, peanuts, longan, and sweetcorn—with all things sweet, what’s not to love?', '15.80', '54', '30', '25', 1),
(23, '1', 5, 'PapaRich Asam Laksa', '/APU/SDP/image/m23.jpg', 'Thick rice noodles served in aromatic fish-based tamarind broth with shredded pineapple, lettuce, red chilli, cucumber, onion and ginger flower. Topped with shrimp paste and mint leaves.', '10.30', '69', '45', '25', 1),
(24, '1', 5, 'PapaRich Banana Split Ice Cream Desires', '/APU/SDP/image/m24.jpg', 'Three handsome scoops of ice cream, sliced bananas, a drizzle of chocolate and strawberry sauces finished off beautifully with whipped cream. Now, who says money can’t buy happiness?', '15.30', '74', '50', '25', 1),
(25, '1', 5, 'PapaRich Beancurd Sheet Roll', '/APU/SDP/image/m25.jpg', '-', '7.20', '70', '45', '25', 4),
(26, '1', 6, 'BurgerKing Whopper with Cheese', '/APU/SDP/image/m26.png', '-', '9.20', '44', '20', '30', 4),
(27, '1', 6, 'BurgerKing Long Chicken', '/APU/SDP/image/m27.png', 'Delightfully tasty crispy chicken fillet topped with shredded lettuce and creamy mayo, and served on a 7” sesame seed bun-made extra long because we know you don\'t want the delicious bites to end so quickly.', '8.50', '33', '13', '25', 4),
(28, '1', 6, 'BurgerKing Fish\'N Crisp Sandwich', '/APU/SDP/image/m28.png', 'Score a catch of marvelous flavors of your tastebuds with this delightful burger. A succulent fish fillet is embraced by a whole slice of American cheese, and then topped with tangy tartar sauce to accentuate the fish\'s flavor and it\'s all served between a toasted sesame bun.', '9.50', '25', '11', '20', 4),
(29, '1', 6, 'BurgerKing Mushroom Veggie Burger', '/APU/SDP/image/m29.png', 'Here\'s an option for our Veggie-loving friends! Our Mushroom Veggie Burger features our famous chunky mushroom in special sauce combination that compliments the burger so well, it\'s like a party in your mouth.', '7.80', '46', '18', '30', 4),
(30, '1', 6, 'BurgerKing Salad', '/APU/SDP/image/m30.png', 'Something light on the tummy but super packed with great nutrients. Come enjoy our salad bowl with special sauce on its own or swap it with fries when you order your next BK meal!', '10.80', '0', '30', '20', 4),
(34, '0', NULL, 'testing nia', '/APU/SDP/image/02.png', 'asqweqweqweqwe', '25.25', '8', '0', '8', 1);

-- --------------------------------------------------------

--
-- Table structure for table `meal_brand`
--

DROP TABLE IF EXISTS `meal_brand`;
CREATE TABLE IF NOT EXISTS `meal_brand` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the meal''s brand.',
  `brand_image` text COMMENT 'Picture of the Brand.',
  `brand_name` varchar(255) NOT NULL COMMENT 'Name of the brand.',
  `registered_date` datetime DEFAULT NULL COMMENT 'Record of the brand being registered.',
  `admin_id` int(11) DEFAULT NULL COMMENT 'The Admin that registered the brand. (Its a user_id.)',
  `active` char(1) NOT NULL COMMENT 'It determines the active of the brand. If its inactive, all the meal which under the brand will be deactivated by the system as well.',
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `brand_name` (`brand_name`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meal_brand`
--

INSERT INTO `meal_brand` (`brand_id`, `brand_image`, `brand_name`, `registered_date`, `admin_id`, `active`) VALUES
(1, '/APU/SDP/image/b1.jpg', 'Pizzahut', '2019-03-25 15:16:15', 1, '1'),
(2, '/APU/SDP/image/b2.jpg', 'Gardenia', '2019-03-25 15:31:15', 1, '1'),
(3, '/APU/SDP/image/b3.jpg', 'KFC', '2019-03-01 15:31:15', 1, '1'),
(4, '/APU/SDP/image/b4.jpg', 'McDonald', '2019-02-12 15:31:15', 1, '1'),
(5, '/APU/SDP/image/b5.jpg', 'PapaRich', '2019-01-11 15:31:15', 1, '1'),
(6, '/APU/SDP/image/b6.jpg', 'BurgerKing', '2019-01-11 15:31:15', 1, '1'),
(8, NULL, 'mamamia!a', '2019-04-08 06:12:03', 1, '0');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_report`
--

DROP TABLE IF EXISTS `monthly_report`;
CREATE TABLE IF NOT EXISTS `monthly_report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the monthly report.',
  `report_name` varchar(255) NOT NULL COMMENT 'Name of the monthly report.',
  `meal_id` int(11) DEFAULT NULL COMMENT 'The meal''s ID.',
  `meal_name` varchar(2555) NOT NULL COMMENT 'The meal''s name.',
  `meal_cost` decimal(15,2) NOT NULL COMMENT 'The meal''s cost for each.',
  `meal_quantity_total` decimal(60,0) NOT NULL COMMENT 'The total quantity of the meal being sold.',
  `meal_brand` int(11) DEFAULT NULL COMMENT 'The meal''s brand. (Its a brand_id.)',
  `meal_cost_total` decimal(15,2) NOT NULL COMMENT 'The meal''s total cost, which is the price of the total quantity about the product being sold',
  `month_report` char(2) NOT NULL COMMENT 'The month of the report.',
  `year_report` char(5) NOT NULL COMMENT 'The year of the report.',
  `generated_time` datetime NOT NULL COMMENT 'The time of the report being generated.',
  PRIMARY KEY (`report_id`),
  KEY `meal_id` (`meal_id`) USING BTREE,
  KEY `meal_brand` (`meal_brand`)
) ENGINE=InnoDB AUTO_INCREMENT=914 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `monthly_report`
--

INSERT INTO `monthly_report` (`report_id`, `report_name`, `meal_id`, `meal_name`, `meal_cost`, `meal_quantity_total`, `meal_brand`, `meal_cost_total`, `month_report`, `year_report`, `generated_time`) VALUES
(884, 'March', 1, 'Regular Pizza', '15.00', '13', 1, '345.00', '3', '2019', '2019-03-31 00:41:26'),
(885, 'March', 2, 'Pineapple Pizza', '12.00', '10', 1, '420.00', '3', '2019', '2019-03-31 00:41:26'),
(886, 'March', 3, 'Anadama Bread', '6.00', '20', 2, '360.00', '3', '2019', '2019-03-31 00:41:26'),
(887, 'March', 4, 'Anpan', '8.00', '15', 2, '400.00', '3', '2019', '2019-03-31 00:41:26'),
(888, 'March', 5, 'Authentic Neapolitan Pizza ', '19.00', '18', 1, '760.00', '3', '2019', '2019-03-31 00:41:26'),
(889, 'March', 6, 'Pizza Quattro Formaggi', '30.00', '8', 1, '690.00', '3', '2019', '2019-03-31 00:41:26'),
(890, 'March', 7, 'Chicago-style Pizza', '15.00', '20', 1, '450.00', '3', '2019', '2019-03-31 00:41:26'),
(891, 'March', 8, 'Banana Bread', '6.50', '20', 2, '227.50', '3', '2019', '2019-03-31 00:41:26'),
(892, 'March', 9, 'Toast', '4.50', '20', 2, '180.00', '3', '2019', '2019-03-31 00:41:26'),
(893, 'March', 10, 'Sourdough', '6.50', '17', 2, '208.00', '3', '2019', '2019-03-31 00:41:26'),
(894, 'March', 11, 'Kentucky Fried Chicken & Waffles', '14.00', '16', 3, '378.00', '3', '2019', '2019-03-31 00:41:26'),
(895, 'March', 12, 'KFC Ghost Pepper', '18.00', '24', 3, '486.00', '3', '2019', '2019-03-31 00:41:26'),
(896, 'March', 13, 'Air Fryer Chicken Tenders', '8.00', '33', 3, '280.00', '3', '2019', '2019-03-31 00:41:26'),
(897, 'March', 14, 'Honey BBQ Sandwich', '15.00', '16', 3, '330.00', '3', '2019', '2019-03-31 00:41:26'),
(898, 'March', 15, 'KFC Famous Bowl Snack Size', '7.00', '28', 3, '210.00', '3', '2019', '2019-03-31 00:41:26'),
(899, 'March', 16, 'McDonald Hamburger', '8.00', '22', 4, '216.00', '3', '2019', '2019-03-31 00:41:26'),
(900, 'March', 17, 'McDonald Big Mac', '9.30', '22', 4, '223.20', '3', '2019', '2019-03-31 00:41:26'),
(901, 'March', 18, 'McDonald Quarter Pounder', '8.60', '14', 4, '189.20', '3', '2019', '2019-03-01 00:41:26'),
(902, 'March', 19, 'McDonald Big N\' Tasty', '7.20', '21', 4, '187.20', '3', '2019', '2019-03-31 00:41:26'),
(903, 'March', 20, 'McDonald Cheeseburger', '7.80', '22', 4, '218.40', '3', '2019', '2019-03-31 00:41:26'),
(904, 'March', 21, 'PAPPA ABC', '12.80', '12', 5, '473.60', '3', '2019', '2019-03-31 00:41:26'),
(905, 'March', 22, 'PAPPA ABC Special with Ice Cream', '15.80', '10', 5, '632.00', '3', '2019', '2019-03-31 00:41:26'),
(906, 'March', 23, 'PapaRich Asam Laksa', '10.30', '10', 5, '566.50', '3', '2019', '2019-03-31 00:41:26'),
(907, 'March', 24, 'PapaRich Banana Split Ice Cream Desires', '15.30', '5', 5, '841.50', '3', '2019', '2019-03-31 00:41:26'),
(908, 'March', 25, 'PapaRich Beancurd Sheet Roll', '7.20', '15', 5, '432.00', '3', '2019', '2019-03-31 00:41:26'),
(909, 'March', 26, 'BurgerKing Whopper with Cheese', '9.20', '25', 6, '414.00', '3', '2019', '2019-03-31 00:41:26'),
(910, 'March', 27, 'BurgerKing Long Chicken', '8.50', '17', 6, '255.00', '3', '2019', '2019-03-31 00:41:26'),
(911, 'March', 28, 'BurgerKing Fish\'N Crisp Sandwich', '9.50', '17', 6, '266.00', '3', '2019', '2019-03-31 00:41:26'),
(912, 'March', 29, 'BurgerKing Mushroom Veggie Burger', '7.80', '14', 6, '249.60', '3', '2019', '2019-03-31 00:41:26'),
(913, 'March', 30, 'BurgerKing Salad', '10.80', '5', 6, '378.00', '3', '2019', '2019-03-31 00:41:26');

-- --------------------------------------------------------

--
-- Table structure for table `registration_list`
--

DROP TABLE IF EXISTS `registration_list`;
CREATE TABLE IF NOT EXISTS `registration_list` (
  `registration_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the registration list.',
  `username` varchar(255) NOT NULL COMMENT 'The registering account''s username.',
  `password` varchar(2555) NOT NULL COMMENT 'The registering account''s password.',
  `verification_code` char(8) NOT NULL COMMENT 'The registering account''s verification code, to have the admin check whether the account is legitimate or not.',
  `account_id` char(6) NOT NULL COMMENT 'User''s ID which consists in the system, to be verified by the Admin during registration, i.e. TP049999.',
  `first_name` varchar(255) NOT NULL COMMENT 'The registering user''s first name.',
  `last_name` varchar(255) NOT NULL COMMENT 'The registering user''s last name.',
  `gender` char(1) NOT NULL COMMENT 'The registering user''s gender.',
  `dob` datetime NOT NULL COMMENT 'The registering user''s day of birth.',
  `email` varchar(255) NOT NULL COMMENT 'The registering user''s email.',
  `verified` char(1) DEFAULT NULL COMMENT 'It determines whether the user has been registered or not.',
  `image_profile` text COMMENT 'The registering user''s profile image.',
  `apply_date` datetime DEFAULT NULL COMMENT 'The date that the user apply for the registration.',
  PRIMARY KEY (`registration_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `verification_code` (`verification_code`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `registration_list`
--

INSERT INTO `registration_list` (`registration_id`, `username`, `password`, `verification_code`, `account_id`, `first_name`, `last_name`, `gender`, `dob`, `email`, `verified`, `image_profile`, `apply_date`) VALUES
(20, 'admintest123', 'b3c00f68d00ea143bb1c3583a43cb524', '22222222', '000002', 'Admin', 'Test', '1', '1989-02-22 14:22:00', 'admintest123@gmail.com', '1', NULL, '2019-04-02 05:10:54'),
(22, 'cashiertest123', 'b3c00f68d00ea143bb1c3583a43cb524', '44444444', '111112', 'Cashier', 'Test', '2', '1991-11-11 14:22:00', 'cashiertest123@gmail.com', '1', NULL, '2019-04-02 05:12:59'),
(24, 'studenttest123', 'b3c00f68d00ea143bb1c3583a43cb524', '66666666', '222223', 'Student', 'Test', '1', '1989-12-31 14:22:00', 'studentest123@gmail.com', '1', NULL, '2019-04-02 05:14:44'),
(25, 'bengay123', 'b3c00f68d00ea143bb1c3583a43cb524', 'VKd44XXX', '123121', 'BEN', 'GAY', '2', '1998-12-22 14:22:00', 'gaygay123@gaymail.com', '1', NULL, '2019-04-08 12:39:44'),
(26, 'piratesmanX1', 'b3c00f68d00ea143bb1c3583a43cb524', '33333333', '123312', 'Ooi', 'JJJ', '1', '1999-02-22 14:22:00', 'bean123@gmail.com', '1', NULL, '2019-04-10 11:24:57'),
(27, 'student1234', 'b3c00f68d00ea143bb1c3583a43cb524', '2329TKmt', '231312', 'Student', 'Test', '1', '1998-11-22 14:22:00', 'student123@gmail.com', '1', NULL, '2019-04-10 11:38:03'),
(28, 'testingsystem', 'b3c00f68d00ea143bb1c3583a43cb524', '55555555', '213123', 'Testing', 'System', '2', '1998-02-13 14:22:00', 'testingsystem@gmail.com', '1', NULL, '2019-04-19 07:02:47'),
(29, 'testtest123', 'b3c00f68d00ea143bb1c3583a43cb524', 'JX67X4t5', 'AVss12', 'TEST', 'tsetst', '1', '1999-12-12 14:22:00', 'testing4896@gmail.com', '1', NULL, '2019-04-24 23:44:52'),
(30, 'studentbois123', 'b3c00f68d00ea143bb1c3583a43cb524', '0nQNelSF', '0nQNel', 'STUDENT', 'IAM', '0', '1988-12-12 00:12:00', 'studenttest4123@gmail.com', '1', NULL, '2019-04-25 00:24:18');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the student list.',
  `first_name` varchar(255) NOT NULL COMMENT 'User''s first name.',
  `last_name` varchar(255) NOT NULL COMMENT 'User''s last name.',
  `gender` char(1) NOT NULL COMMENT 'User''s gender.',
  `dob` datetime NOT NULL COMMENT 'User''s day of birth.',
  `profile_image` text COMMENT 'User''s profile image.',
  `balance` decimal(15,2) DEFAULT NULL COMMENT 'User''s balance.',
  `access_code` varchar(2555) DEFAULT NULL COMMENT 'Account''s access code, aka a code for the master control of the account for the parents if the user is a student. It enables the parent to do something that a user can''t do, such as deactivate the account.',
  `decrypted_access_code` char(8) DEFAULT NULL COMMENT 'In concept the system will automatically send to the student''s parents of their access code once its registered into the system, but since we are using localhost and we''ve no such functionality, we''ve to figured a way to know what''s the student''s access code as the student''s access code when registered into the system will encrypted with MD5 format, therefore there''s no way to know the actual access code, unless storing in here.',
  `admin_approved` int(11) DEFAULT NULL COMMENT 'Record of which admin approved the registration of the account. (Its a user_id.)',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID of the user account.',
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `access_code` (`access_code`),
  KEY `user_id` (`user_id`),
  KEY `admin_approved` (`admin_approved`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `first_name`, `last_name`, `gender`, `dob`, `profile_image`, `balance`, `access_code`, `decrypted_access_code`, `admin_approved`, `user_id`) VALUES
(12, 'Studentss', 'Testss', '0', '1989-12-31 14:22:00', '/APU/SDP/image/01.gif', '204.30', '118fd8f2f3a9586a909c886dd338348e', '1yH4xMqi', 1, 52),
(13, 'Student', 'Test', '1', '1998-11-22 14:22:00', NULL, '305.00', 'ff823b4d24d1c7c7dbf323eef3537c09', 'oAe4dBbt', 1, 56),
(14, 'Testing', 'System', '2', '1998-02-13 14:22:00', NULL, '100.00', '9ce93f944a53ea6acb2838e6115b1b16', 'VNJ83wvf', 1, 58),
(15, 'STUDENT', 'IAM', '0', '1988-12-12 00:12:00', NULL, '100.00', '9ca0dd7be2b98ff5232201e09982f562', '3udaYtsS', 1, 60);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_record`
--

DROP TABLE IF EXISTS `transaction_record`;
CREATE TABLE IF NOT EXISTS `transaction_record` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the transaction records.',
  `meal_id` int(11) DEFAULT NULL COMMENT 'ID of the meal.',
  `meal_brand_id` int(11) DEFAULT NULL COMMENT 'ID of the meal''s brand.',
  `meal_quantity_cart` decimal(60,0) NOT NULL COMMENT 'The amount of quantity that being bought by the user.',
  `order_id` int(11) DEFAULT NULL COMMENT 'ID of the order that''s related with.',
  PRIMARY KEY (`transaction_id`),
  KEY `meal_id` (`meal_id`),
  KEY `meal_brand_id` (`meal_brand_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction_record`
--

INSERT INTO `transaction_record` (`transaction_id`, `meal_id`, `meal_brand_id`, `meal_quantity_cart`, `order_id`) VALUES
(153, 15, 3, '1', 272),
(154, 14, 3, '5', 272),
(155, 13, 3, '9', 272),
(156, 12, 3, '7', 272),
(170, 30, 6, '50', 283),
(171, 29, 6, '1', 283),
(172, 28, 6, '1', 283),
(173, 27, 6, '1', 283),
(174, 26, 6, '1', 283),
(193, 23, 5, '1', 291),
(194, 22, 5, '1', 291),
(197, 17, 4, '16', 301),
(198, 18, 4, '1', 301),
(203, 7, 1, '1', 308),
(204, 5, 1, '6', 308),
(205, 6, 1, '1', 308),
(223, 26, 6, '4', 335),
(224, 27, 6, '4', 335),
(225, 28, 6, '5', 335),
(226, 29, 6, '1', 335),
(231, 1, 1, '1', 341),
(232, 2, 1, '1', 341),
(233, 26, 6, '1', 342),
(235, 24, 5, '1', 344),
(236, 14, 3, '1', 345);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the user account.',
  `username` varchar(255) NOT NULL COMMENT 'User''s username.',
  `password` varchar(2555) NOT NULL COMMENT 'User''s password.',
  `email` varchar(255) NOT NULL COMMENT 'User''s email address.',
  `account_id` char(6) NOT NULL COMMENT 'User''s ID which consists in the system, to be verified by the Admin during registration, i.e. TP049999.',
  `account_code` varchar(2555) NOT NULL COMMENT 'User''s account code is to verify and identify the user''s process, i.e. for students its for confirming their transaction; for cashier its for validating the transaction the moment of confirmation of transaction; and for admin its to identify themselves during the process like registering a user, adding a meal or brand, etc.',
  `decrypted_account_code` char(8) DEFAULT NULL COMMENT 'In concept the system will automatically send to the users of their account code once its registered into the system, but since we are using localhost and we''ve no such functionality, we''ve to figured a way to know what''s the account code as the account code when registered into the system will encrypted with MD5 format, therefore there''s no way to know the actual account code, unless storing in here.',
  `active` char(1) NOT NULL COMMENT 'To determine whether the account is still active, or not.',
  `status` char(1) NOT NULL COMMENT 'To determine the status of the account, i.e. Cashier, Admin, or normal user.',
  `last_login` datetime DEFAULT NULL COMMENT 'Record of the account''s last login.',
  `registered_date` datetime DEFAULT NULL COMMENT 'Record of the account''s registered date.',
  `verification_code` char(8) DEFAULT NULL COMMENT 'The code that being used by the user to verify the registration of the account, and the status of it.',
  `suspended_reason` varchar(2555) DEFAULT NULL COMMENT 'The statements of reasons about the account''s suspension.',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `account_id` (`account_id`) USING BTREE,
  UNIQUE KEY `account_code` (`account_code`) USING BTREE,
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `verification_code` (`verification_code`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `email`, `account_id`, `account_code`, `decrypted_account_code`, `active`, `status`, `last_login`, `registered_date`, `verification_code`, `suspended_reason`) VALUES
(1, 'admin123', 'b3c00f68d00ea143bb1c3583a43cb524', 'defalt123@gmail.com', '044444', 'dd4b21e9ef71e1291183a46b913ae6f2', '00000000', '1', '2', '2019-04-25 00:24:26', '2018-10-10 18:55:53', '00000000', NULL),
(52, 'studenttest123', 'b3c00f68d00ea143bb1c3583a43cb524', 'studentest123@gmail.com', '222223', '4afe96c1455541a16f9280625db4154a', 'MxUfaEZb', '1', '0', '2019-04-23 17:52:35', '2018-11-11 02:27:58', '66666666', 'Account Disabled due to account being disabled by an Admin, ID: 1. For more information please do contact with the Administrator.'),
(53, 'cashiertest123', 'b3c00f68d00ea143bb1c3583a43cb524', 'cashiertest123@gmail.com', '111112', 'e082337fc26e5e631dd98bab91967db8', 'TXvrihfn', '1', '1', '2019-04-23 18:11:44', '2018-12-12 23:21:53', '44444444', 'Account owner disabled the account by himself on 20/04/19 12:36 PM'),
(54, 'admintest123', 'b3c00f68d00ea143bb1c3583a43cb524', 'admintest123@gmail.com', '000002', 'be14c0f67e0f307ea2f577d808174a41', 'Ph7edljR', '1', '2', NULL, '2019-01-01 21:43:31', '22222222', 'Account Disabled due to Verification Code being disabled by an Admin, ID: 1. For more information please do contact with the Administrator.'),
(55, 'piratesmanX1', 'b3c00f68d00ea143bb1c3583a43cb524', 'bean123@gmail.com', '123312', '7fda8a6d2e69fcb8346a42554f56fd87', 'EqKNnrjz', '1', '1', NULL, '2019-02-02 11:25:43', '33333333', NULL),
(56, 'student1234', 'b3c00f68d00ea143bb1c3583a43cb524', 'student123@gmail.com', '231312', '6c996ccfcf6317e0619d0e6fe98e26d1', 'mkbYMA3l', '1', '0', NULL, '2019-03-03 11:46:43', '2329TKmt', ''),
(57, 'bengay123', 'b3c00f68d00ea143bb1c3583a43cb524', 'gaygay123@gaymail.com', '123121', '054fe7f09ffb977c9e69ceeed82c82de', 'bT9fqPk3', '1', '1', NULL, '2019-04-04 11:53:14', 'VKd44XXX', NULL),
(58, 'testingsystem', 'b3c00f68d00ea143bb1c3583a43cb524', 'testingsystem@gmail.com', '213123', '44429bf76d431f566214a7823c47d2ac', 'RDOTGpEb', '1', '0', NULL, '2019-04-20 12:29:34', '55555555', NULL),
(59, 'testtest123', 'b3c00f68d00ea143bb1c3583a43cb524', 'testing4896@gmail.com', 'AVss12', '854109cf374787e952dd4292067d0362', '4OHvP59h', '1', '1', NULL, '2019-04-24 23:45:22', 'JX67X4t5', NULL),
(60, 'studentbois123', 'b3c00f68d00ea143bb1c3583a43cb524', 'studenttest4123@gmail.com', '0nQNel', '14ae8297a45522f9b56d64a3c0c779f7', 'D97tGIZQ', '1', '0', NULL, '2019-04-25 00:24:38', '0nQNelSF', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_order`
--

DROP TABLE IF EXISTS `user_order`;
CREATE TABLE IF NOT EXISTS `user_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the order of the user.',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID of the user that owns the order.',
  `transaction_date` datetime DEFAULT NULL COMMENT 'The date of the transaction being made.',
  `total_price` decimal(15,2) DEFAULT NULL COMMENT 'The total cost of the meal of the order.',
  `paid` char(1) DEFAULT NULL COMMENT 'To determine whether the order has been paid or not.',
  `brand_id` int(11) DEFAULT NULL COMMENT 'To determine the order belongs to which brand. (In our concept every user can only buy the meal specifically from each brand/shop.)',
  `cashier_id` int(11) DEFAULT NULL COMMENT 'ID of the cashier who responsible for making the order for the user.',
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `brand_id` (`brand_id`),
  KEY `cashier_id` (`cashier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=348 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_order`
--

INSERT INTO `user_order` (`order_id`, `user_id`, `transaction_date`, `total_price`, `paid`, `brand_id`, `cashier_id`) VALUES
(272, 52, '2019-04-17 23:22:17', '280.00', '1', 3, 53),
(283, 52, '2019-04-18 08:41:00', '575.00', '1', 6, 53),
(291, 52, '2019-04-18 12:58:40', '26.10', '1', 5, 53),
(308, 52, '2019-04-19 08:43:13', '159.00', '1', 1, 53),
(335, 52, '2019-04-20 12:08:10', '126.10', '1', 6, 53),
(341, 52, '2019-04-23 17:13:00', '25.00', '1', 1, 53),
(342, 52, '2019-04-23 17:52:19', '9.20', '1', 6, 53),
(344, 52, '2019-04-23 18:02:56', '15.30', '1', 5, 53),
(345, 52, '2019-04-23 18:03:28', '15.00', '1', 3, 53);

-- --------------------------------------------------------

--
-- Table structure for table `verification_code`
--

DROP TABLE IF EXISTS `verification_code`;
CREATE TABLE IF NOT EXISTS `verification_code` (
  `verification_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID about the verification code.',
  `verification_code` char(8) DEFAULT NULL COMMENT 'Verification Code is to verify the user''s legitimacy and status like Cashier or normal user.',
  `code_active` char(1) NOT NULL COMMENT 'To determine the code is still active or not, if its not, then the account under the code will be disabled, and the code can''t be used as well.',
  `code_used` char(1) NOT NULL COMMENT 'To check whether the code has been used or not.',
  `code_status` char(1) NOT NULL COMMENT 'To determine the account''s status which under the verification code.',
  `used_date` datetime DEFAULT NULL COMMENT 'The time where the code being used.',
  `registered_date` datetime DEFAULT NULL COMMENT 'The time where the code being registered.',
  `registered_admin_id` int(11) DEFAULT NULL COMMENT 'The Admin''s ID which registered the code.',
  `user_id_code` int(11) DEFAULT NULL COMMENT 'The User''s ID which using the verification code.',
  PRIMARY KEY (`verification_id`),
  UNIQUE KEY `verification_code` (`verification_code`),
  KEY `user_id_code` (`user_id_code`),
  KEY `registered_admin_id` (`registered_admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `verification_code`
--

INSERT INTO `verification_code` (`verification_id`, `verification_code`, `code_active`, `code_used`, `code_status`, `used_date`, `registered_date`, `registered_admin_id`, `user_id_code`) VALUES
(1, '00000000', '1', '1', '2', '2019-04-03 02:09:36', '2019-03-21 18:55:18', 1, 1),
(15, '11111111', '1', '0', '2', '2019-04-02 05:09:41', '2019-03-21 18:55:18', 1, NULL),
(17, '33333333', '1', '1', '1', '2019-04-10 11:24:57', '2019-03-21 18:55:18', 1, 55),
(18, '44444444', '1', '1', '1', '2019-04-02 05:12:59', '2019-03-21 18:55:18', 1, 53),
(19, '55555555', '1', '1', '0', '2019-04-19 07:02:47', '2019-03-21 18:55:18', 1, 58),
(23, '66666666', '1', '1', '0', '2019-01-27 18:55:18', '2019-01-11 15:31:15', 1, 52),
(24, '22222222', '1', '1', '2', '2019-04-02 05:10:54', '2019-03-21 18:55:18', 1, 54),
(25, 'VKd44XXX', '1', '1', '1', '2019-04-08 12:39:44', '2019-04-03 05:42:38', 1, 57),
(26, '2329TKmt', '1', '1', '0', '2019-04-10 11:38:03', '2019-04-10 11:29:40', 1, NULL),
(27, 'JX67X4t5', '1', '1', '1', '2019-04-24 23:44:52', '2019-04-24 23:43:05', 1, 59),
(28, '0nQNelSF', '1', '1', '0', '2019-04-25 00:24:18', '2019-04-25 00:23:21', 1, 60);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `admin_ibfk_2` FOREIGN KEY (`admin_approved`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `balance_record`
--
ALTER TABLE `balance_record`
  ADD CONSTRAINT `balance_record_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `cashier`
--
ALTER TABLE `cashier`
  ADD CONSTRAINT `cashier_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `cashier_ibfk_2` FOREIGN KEY (`admin_approved`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `meal`
--
ALTER TABLE `meal`
  ADD CONSTRAINT `meal_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `meal_ibfk_3` FOREIGN KEY (`meal_brand_id`) REFERENCES `meal_brand` (`brand_id`);

--
-- Constraints for table `meal_brand`
--
ALTER TABLE `meal_brand`
  ADD CONSTRAINT `meal_brand_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `monthly_report`
--
ALTER TABLE `monthly_report`
  ADD CONSTRAINT `monthly_report_ibfk_1` FOREIGN KEY (`meal_id`) REFERENCES `meal` (`meal_id`),
  ADD CONSTRAINT `monthly_report_ibfk_2` FOREIGN KEY (`meal_brand`) REFERENCES `meal_brand` (`brand_id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `student_ibfk_2` FOREIGN KEY (`admin_approved`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `transaction_record`
--
ALTER TABLE `transaction_record`
  ADD CONSTRAINT `transaction_record_ibfk_1` FOREIGN KEY (`meal_id`) REFERENCES `meal` (`meal_id`),
  ADD CONSTRAINT `transaction_record_ibfk_2` FOREIGN KEY (`meal_brand_id`) REFERENCES `meal_brand` (`brand_id`),
  ADD CONSTRAINT `transaction_record_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `user_order` (`order_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`verification_code`) REFERENCES `verification_code` (`verification_code`);

--
-- Constraints for table `user_order`
--
ALTER TABLE `user_order`
  ADD CONSTRAINT `user_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `user_order_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `meal_brand` (`brand_id`),
  ADD CONSTRAINT `user_order_ibfk_3` FOREIGN KEY (`cashier_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `verification_code`
--
ALTER TABLE `verification_code`
  ADD CONSTRAINT `verification_code_ibfk_1` FOREIGN KEY (`user_id_code`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `verification_code_ibfk_2` FOREIGN KEY (`registered_admin_id`) REFERENCES `user` (`user_id`);

DELIMITER $$
--
-- Events
--
DROP EVENT `restock_meal`$$
CREATE DEFINER=`root`@`localhost` EVENT `restock_meal` ON SCHEDULE EVERY 1 MONTH STARTS '2019-01-31 23:59:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Automatically update the database when the meal restock-ed.' DO UPDATE meal 
SET meal_additional_quantity = meal.meal_quantity, meal_quantity = (meal.meal_quantity + meal.meal_default_quantity)$$

DROP EVENT `generate_report`$$
CREATE DEFINER=`root`@`localhost` EVENT `generate_report` ON SCHEDULE EVERY 1 MONTH STARTS '2019-01-31 23:58:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Automatically generates Monthly Report at the end of the month.' DO INSERT INTO monthly_report
(report_name, meal_id, meal_name, meal_cost, meal_quantity_total, meal_brand, meal_cost_total, month_report, year_report, generated_time)
SELECT MONTHNAME(CURRENT_TIMESTAMP()), meal_id, meal_name, meal_price, ((meal_additional_quantity + meal_default_quantity) - meal_quantity),
meal_brand_id, (((meal_additional_quantity + meal_default_quantity) - meal_quantity) * meal_price), MONTH(CURRENT_TIMESTAMP()), YEAR(CURRENT_TIMESTAMP()), CURRENT_TIMESTAMP()
FROM meal$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
