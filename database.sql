--
-- SQL Schema for کافه دنیس (Cafe Denis) Website
-- This file should be imported into your MySQL database via phpMyAdmin.
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `cafe_denis_db` (Example name, you can use any name)
--

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--
-- This table stores all the items for the digital menu.
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_persian_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_persian_ci NOT NULL,
  `price` int(11) NOT NULL,
  `category` enum('نوشیدنی گرم','نوشیدنی سرد','شیک‌ها','آیتم‌های ویژه') COLLATE utf8mb4_persian_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- This table stores the login credentials for the admin panel.
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_persian_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_persian_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `users`
--
-- Default user:
-- Username: admin
-- Password: mrdoctor11228!
-- The password stored here is a secure hash.
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$If6F.j8iGtjS9.E0aJ5RpeD4aRAB3FmGkE5fnYtYg2mGk0iQ2.x3u');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD KEY `category` (`category`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `username_2` (`username`);
COMMIT;
