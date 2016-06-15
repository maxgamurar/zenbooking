SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `rooms` (
  `ID` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `qty` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `rooms` (`ID`, `name`, `price`, `qty`) VALUES
(1, 'Single', '1000.00', 5),
(2, 'Double', '2000.00', 5);

CREATE TABLE `room_bookings` (
  `ID` int(10) UNSIGNED NOT NULL,
  `room_ID` int(10) UNSIGNED NOT NULL,
  `book_date` date NOT NULL,
  `qty` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `rooms`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `room_ID` (`room_ID`);


ALTER TABLE `rooms`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `room_bookings`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;