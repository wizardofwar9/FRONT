-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 08 sep. 2022 à 10:41
-- Version du serveur : 5.7.33
-- Version de PHP : 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `customersexo`
--

-- --------------------------------------------------------

--
-- Structure de la table `customers`
--

CREATE TABLE `customers` (
  `CustomerID` int(11) NOT NULL,
  `CustomerName` varchar(255) NOT NULL,
  `ContactName` varchar(255) NOT NULL,
  `Adress` varchar(255) NOT NULL,
  `City` varchar(255) NOT NULL,
  `postalCode` varchar(25) NOT NULL,
  `Country` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `customers`
--

INSERT INTO `customers` (`CustomerID`, `CustomerName`, `ContactName`, `Adress`, `City`, `postalCode`, `Country`) VALUES
(1, 'Aflreds utterkiste', 'Maria Anders', 'Obere Str.57', 'Berlin', '12209', ''),
(2, 'Ana Trujillo \r\nEmparedados y\r\n helados', 'Ana Trujillo', 'Avda.de la\r\nConstitucion\r\n2222', 'Mexico\r\nD.F.', '05021', ''),
(3, 'Antonio Moreno\r\nTaquera', 'Antonio\r\nMoreno', 'Mataderos\r\n2312', 'Mexico\r\nD.F.', '05023', ''),
(4, 'Around the Horn', 'Thomas Hardy', '120 Hanover\r\nSq.', 'London', 'WA1 DP', ''),
(5, 'Berglunds\r\nsnabbkop', 'Chritina\r\nBerglund', 'Berguvssagen\r\n8', 'Lulea', '5-958 22', ''),
(6, 'New York Community Bancorp, Inc.', 'Farlie', '86 Bluejay Avenue', 'Oberá', '3361', 'Argentina'),
(11, 'Hawaiian Electric Industries, Inc.', 'Ives', '1910 Acker Lane', 'Kinna', '511 58', 'Sweden'),
(13, 'Himax Technologies, Inc.', 'Fayette', '73424 Ridgeway Pass', 'Manukau City', '2246', 'New Zealand'),
(15, 'Vantage Energy Acquisition Corp.', 'Neel', '1 Memorial Hill', 'Libacao', '5602', 'Philippines'),
(16, 'American National Insurance Company', 'Louie', '4 Victoria Way', 'Orenburg', '460999', 'Russia'),
(18, 'Blackrock MuniYield Pennsylvania Quality Fund', 'Arnold', '21 Ludington Hill', 'Benito Juarez', '31540', 'Mexico'),
(19, 'General Cable Corporation', 'Clo', '0975 Sheridan Park', 'Gryazovets', '162002', 'Russia'),
(20, 'New Germany Fund, Inc. (The)', 'Isabeau', '7340 Scofield Road', 'Lethbridge', 'T1K', 'Canada'),
(22, 'RADA Electronic Industries Ltd.', 'Carmencita', '14 Lakewood Alley', 'Nueve de Julio', '3606', 'Argentina'),
(23, 'Discovery Communications, Inc.', 'Hynda', '1613 Jana Terrace', 'Tosno', '187003', 'Russia'),
(28, 'Lincoln Electric Holdings, Inc.', 'Olin', '0399 Stang Avenue', 'Dulian', '3110', 'Philippines'),
(29, 'Qiagen N.V.', 'Jeremie', '1061 Katie Alley', 'Gaspar', '89110-000', 'Brazil'),
(31, 'Infinity Pharmaceuticals, Inc.', 'Laurianne', '6 Beilfuss Junction', 'Orlando', '32825', 'United States'),
(33, 'Asia Pacific Fund, Inc. (The)', 'Jehu', '20 Fairfield Center', 'Leleque', '9213', 'Argentina'),
(35, 'TC PipeLines, LP', 'Shell', '28 Monument Alley', 'Urazovo', '309975', 'Russia'),
(36, 'Howard Hughes Corporation (The)', 'Hunt', '887 Nevada Avenue', 'Tuscaloosa', '35487', 'United States'),
(37, 'Diebold Nixdorf Incorporated', 'Meade', '8193 Hallows Parkway', 'Kota Bharu', '15540', 'Malaysia'),
(39, 'GAIN Capital Holdings, Inc.', 'Fancie', '05 Elmside Place', 'Mapiripán', '943057', 'Colombia'),
(43, 'Matson, Inc.', 'Leshia', '69 Morrow Center', 'Skulsk', '62-560', 'Poland'),
(44, 'Texas Capital Bancshares, Inc.', 'Talbert', '8 Cody Court', 'Sao Hai', '18160', 'Thailand'),
(46, 'Genworth Financial Inc', 'Deloris', '99 Annamark Court', 'Boco', '2425-405', 'Portugal'),
(48, 'Andina Acquisition Corp. II', 'Doralynne', '00 Brown Terrace', 'Buenavista', '8601', 'Philippines');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`CustomerID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `customers`
--
ALTER TABLE `customers`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
