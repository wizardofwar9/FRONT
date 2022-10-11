-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 06 oct. 2022 à 09:15
-- Version du serveur : 8.0.30
-- Version de PHP : 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `voitures`
--

-- --------------------------------------------------------

--
-- Structure de la table `voiture`
--

CREATE TABLE `voiture` (
  `Id_Voiture` int NOT NULL,
  `Marques` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `modele` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Annee_modele` int DEFAULT NULL,
  `Mise_en_circulation` date DEFAULT NULL,
  `Type_de_vehicule` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Kilometrage` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Carburant` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `couleur` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nombre_de_porte` int DEFAULT NULL,
  `nombre_de_place` int DEFAULT NULL,
  `puissance_fiscale` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `puissance_din` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `permis` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `critaire` int DEFAULT NULL,
  `boite_de_vitesse` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Id_Prix_` int DEFAULT NULL,
  `Id_Vendeur` int DEFAULT NULL,
  `Id_Description` int NOT NULL,
  `Id_Localisation` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `voiture`
--

INSERT INTO `voiture` (`Id_Voiture`, `Marques`, `modele`, `Annee_modele`, `Mise_en_circulation`, `Type_de_vehicule`, `Kilometrage`, `Carburant`, `couleur`, `nombre_de_porte`, `nombre_de_place`, `puissance_fiscale`, `puissance_din`, `permis`, `critaire`, `boite_de_vitesse`, `Id_Prix_`, `Id_Vendeur`, `Id_Description`, `Id_Localisation`) VALUES
(1, 'BMW', 'serie3', 2001, '2022-09-05', 'break', '541000', 'plutonium', 'un joli vert', 15, 2, '5', '200', 'permis B', 1, 'auto', 5, 3, 1, 4),
(2, 'renault', 'serie 5', 2005, '2022-10-02', 'loisir', '20', 'ce qu il reste', 'bleu presque rouge', 5, 4, '4', '65', 'B', 3, 'manuelle', 1, 1, 5, 5),
(3, 'citroen', 'fusee', 1412, '2031-10-03', 'serie', '0', 'graisse a firtes', 'gris souris', 3, 5, '5', '120', 'permis B', 1, 'manuelle', 3, 2, 3, 5),
(4, 'audi', 'roquette', 1785, '2022-10-02', 'serie', '20000', 'diesel', 'tkt', 5, 2, '5', '65', 'permis B', 2, 'auto', 4, 1, 4, 2),
(5, 'audi', 'fusee', 1492, '2022-10-04', 'serie', '145000', 'essence', 'jaune canariri', 5, 4, '5', '95', 'permis B', 3, 'manuelle', 5, 2, 2, 3);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD PRIMARY KEY (`Id_Voiture`),
  ADD UNIQUE KEY `Id_Description` (`Id_Description`),
  ADD KEY `Id_Prix_` (`Id_Prix_`),
  ADD KEY `Id_Vendeur` (`Id_Vendeur`),
  ADD KEY `Id_Localisation` (`Id_Localisation`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD CONSTRAINT `voiture_ibfk_1` FOREIGN KEY (`Id_Prix_`) REFERENCES `prix` (`Id_Prix_`),
  ADD CONSTRAINT `voiture_ibfk_2` FOREIGN KEY (`Id_Vendeur`) REFERENCES `vendeur` (`Id_Vendeur`),
  ADD CONSTRAINT `voiture_ibfk_3` FOREIGN KEY (`Id_Description`) REFERENCES `description` (`Id_Description`),
  ADD CONSTRAINT `voiture_ibfk_4` FOREIGN KEY (`Id_Localisation`) REFERENCES `localisation` (`Id_Localisation`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
