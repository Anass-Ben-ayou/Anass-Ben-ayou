-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 22 mai 2026 à 23:07
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecomerce-lampes`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id_avis` bigint(20) UNSIGNED NOT NULL,
  `note` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `date_avis` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_client` bigint(20) UNSIGNED NOT NULL,
  `id_produit` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id_avis`, `note`, `commentaire`, `date_avis`, `id_client`, `id_produit`, `created_at`, `updated_at`) VALUES
(1, 5, 'gooooooooooooooooooooooooooooooooooooooooooo', '2026-05-22 19:52:14', 3, 1, '2026-05-22 19:52:14', '2026-05-22 19:52:14');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-3a1330b0d4ef00bcfe467f8889728b58', 'i:1;', 1777401481),
('laravel-cache-3a1330b0d4ef00bcfe467f8889728b58:timer', 'i:1777401481;', 1777401481),
('laravel-cache-7862a10136e5fc28eaa6997163aae811', 'i:1;', 1777405390),
('laravel-cache-7862a10136e5fc28eaa6997163aae811:timer', 'i:1777405390;', 1777405390),
('laravel-cache-90c94be25f981a521f3aa5dd1e49c556', 'i:1;', 1777911390),
('laravel-cache-90c94be25f981a521f3aa5dd1e49c556:timer', 'i:1777911390;', 1777911390),
('laravel-cache-b95cd370efcdece21569bbc0a6e262f3', 'i:2;', 1777911960),
('laravel-cache-b95cd370efcdece21569bbc0a6e262f3:timer', 'i:1777911960;', 1777911960),
('laravel-cache-products.best_sellers', 'O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:6:{i:0;a:20:{s:2:\"id\";i:1;s:10:\"id_produit\";i:1;s:4:\"name\";s:23:\"Lustre cristal prestige\";s:3:\"nom\";s:23:\"Lustre cristal prestige\";s:4:\"slug\";s:23:\"lustre-cristal-prestige\";s:11:\"description\";s:254:\"Lustre cristal prestige est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 1 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Lustre cristal prestige est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Mod\";s:5:\"price\";d:1599;s:4:\"prix\";d:1599;s:9:\"old_price\";d:1899;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:54:\"https://solarlight.ma/produits/lustre-cristal-prestige\";s:5:\"stock\";i:5;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:7:\"Lustres\";s:8:\"finition\";s:8:\"noir mat\";s:5:\"usage\";s:23:\"salon et salle a manger\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:1;a:20:{s:2:\"id\";i:2;s:10:\"id_produit\";i:2;s:4:\"name\";s:21:\"Lustre moderne or mat\";s:3:\"nom\";s:21:\"Lustre moderne or mat\";s:4:\"slug\";s:21:\"lustre-moderne-or-mat\";s:11:\"description\";s:252:\"Lustre moderne or mat est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 2 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Lustre moderne or mat est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Model\";s:5:\"price\";d:1399;s:4:\"prix\";d:1399;s:9:\"old_price\";d:1699;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1484101403633-562f891dc89a?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1484101403633-562f891dc89a?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:52:\"https://solarlight.ma/produits/lustre-moderne-or-mat\";s:5:\"stock\";i:9;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:7:\"Lustres\";s:8:\"finition\";s:13:\"laiton brosse\";s:5:\"usage\";s:23:\"salon et salle a manger\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:2;a:20:{s:2:\"id\";i:3;s:10:\"id_produit\";i:3;s:4:\"name\";s:30:\"Lustre contemporain 5 lumieres\";s:3:\"nom\";s:30:\"Lustre contemporain 5 lumieres\";s:4:\"slug\";s:30:\"lustre-contemporain-5-lumieres\";s:11:\"description\";s:261:\"Lustre contemporain 5 lumieres est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 3 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Lustre contemporain 5 lumieres est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espac\";s:5:\"price\";d:1299;s:4:\"prix\";d:1299;s:9:\"old_price\";d:1499;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:61:\"https://solarlight.ma/produits/lustre-contemporain-5-lumieres\";s:5:\"stock\";i:12;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:7:\"Lustres\";s:8:\"finition\";s:12:\"verre opalin\";s:5:\"usage\";s:23:\"salon et salle a manger\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:3;a:20:{s:2:\"id\";i:4;s:10:\"id_produit\";i:4;s:4:\"name\";s:25:\"Lustre cascade verre fume\";s:3:\"nom\";s:25:\"Lustre cascade verre fume\";s:4:\"slug\";s:25:\"lustre-cascade-verre-fume\";s:11:\"description\";s:256:\"Lustre cascade verre fume est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 4 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Lustre cascade verre fume est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. M\";s:5:\"price\";d:1799;s:4:\"prix\";d:1799;s:9:\"old_price\";d:2099;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:56:\"https://solarlight.ma/produits/lustre-cascade-verre-fume\";s:5:\"stock\";i:15;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:7:\"Lustres\";s:8:\"finition\";s:13:\"chrome satine\";s:5:\"usage\";s:23:\"salon et salle a manger\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:4;a:20:{s:2:\"id\";i:5;s:10:\"id_produit\";i:5;s:4:\"name\";s:28:\"Lustre design noir et laiton\";s:3:\"nom\";s:28:\"Lustre design noir et laiton\";s:4:\"slug\";s:28:\"lustre-design-noir-et-laiton\";s:11:\"description\";s:259:\"Lustre design noir et laiton est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 5 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Lustre design noir et laiton est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces\";s:5:\"price\";d:1499;s:4:\"prix\";d:1499;s:9:\"old_price\";d:1699;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:59:\"https://solarlight.ma/produits/lustre-design-noir-et-laiton\";s:5:\"stock\";i:18;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:7:\"Lustres\";s:8:\"finition\";s:11:\"blanc sable\";s:5:\"usage\";s:23:\"salon et salle a manger\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:5;a:20:{s:2:\"id\";i:6;s:10:\"id_produit\";i:6;s:4:\"name\";s:18:\"Lustre anneaux LED\";s:3:\"nom\";s:18:\"Lustre anneaux LED\";s:4:\"slug\";s:18:\"lustre-anneaux-led\";s:11:\"description\";s:249:\"Lustre anneaux LED est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 6 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Lustre anneaux LED est un Lustres soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 6\";s:5:\"price\";d:1899;s:4:\"prix\";d:1899;s:9:\"old_price\";d:2299;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:49:\"https://solarlight.ma/produits/lustre-anneaux-led\";s:5:\"stock\";i:21;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:7:\"Lustres\";s:8:\"finition\";s:8:\"noir mat\";s:5:\"usage\";s:23:\"salon et salle a manger\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:1;s:12:\"id_categorie\";i:1;s:4:\"name\";s:7:\"Lustres\";s:3:\"nom\";s:7:\"Lustres\";s:4:\"slug\";s:7:\"lustres\";s:11:\"description\";s:54:\"Lustres elegants pour salon, salle a manger et entree.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1777912567),
('laravel-cache-products.featured', 'O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:5:{i:0;a:20:{s:2:\"id\";i:16;s:10:\"id_produit\";i:16;s:4:\"name\";s:23:\"Suspension trio moderne\";s:3:\"nom\";s:23:\"Suspension trio moderne\";s:4:\"slug\";s:23:\"suspension-trio-moderne\";s:11:\"description\";s:258:\"Suspension trio moderne est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 6 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Suspension trio moderne est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.\";s:5:\"price\";d:879;s:4:\"prix\";d:879;s:9:\"old_price\";d:999;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:54:\"https://solarlight.ma/produits/suspension-trio-moderne\";s:5:\"stock\";i:21;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:8:\"noir mat\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:1;a:20:{s:2:\"id\";i:17;s:10:\"id_produit\";i:17;s:4:\"name\";s:25:\"Suspension laiton minimal\";s:3:\"nom\";s:25:\"Suspension laiton minimal\";s:4:\"slug\";s:25:\"suspension-laiton-minimal\";s:11:\"description\";s:260:\"Suspension laiton minimal est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 7 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Suspension laiton minimal est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espace\";s:5:\"price\";d:649;s:4:\"prix\";d:649;s:9:\"old_price\";d:799;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:56:\"https://solarlight.ma/produits/suspension-laiton-minimal\";s:5:\"stock\";i:24;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:13:\"laiton brosse\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:2;a:20:{s:2:\"id\";i:18;s:10:\"id_produit\";i:18;s:4:\"name\";s:26:\"Suspension scandinave bois\";s:3:\"nom\";s:26:\"Suspension scandinave bois\";s:4:\"slug\";s:26:\"suspension-scandinave-bois\";s:11:\"description\";s:261:\"Suspension scandinave bois est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 8 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Suspension scandinave bois est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espac\";s:5:\"price\";d:589;s:4:\"prix\";d:589;s:9:\"old_price\";d:699;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:57:\"https://solarlight.ma/produits/suspension-scandinave-bois\";s:5:\"stock\";i:8;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:12:\"verre opalin\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:3;a:20:{s:2:\"id\";i:19;s:10:\"id_produit\";i:19;s:4:\"name\";s:26:\"Suspension opaline premium\";s:3:\"nom\";s:26:\"Suspension opaline premium\";s:4:\"slug\";s:26:\"suspension-opaline-premium\";s:11:\"description\";s:261:\"Suspension opaline premium est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 9 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Suspension opaline premium est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espac\";s:5:\"price\";d:729;s:4:\"prix\";d:729;s:9:\"old_price\";d:859;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:57:\"https://solarlight.ma/produits/suspension-opaline-premium\";s:5:\"stock\";i:11;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:13:\"chrome satine\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:4;a:20:{s:2:\"id\";i:20;s:10:\"id_produit\";i:20;s:4:\"name\";s:22:\"Suspension cuisine LED\";s:3:\"nom\";s:22:\"Suspension cuisine LED\";s:4:\"slug\";s:22:\"suspension-cuisine-led\";s:11:\"description\";s:258:\"Suspension cuisine LED est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 10 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:159:\"Suspension cuisine LED est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.\";s:5:\"price\";d:519;s:4:\"prix\";d:519;s:9:\"old_price\";d:629;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:53:\"https://solarlight.ma/produits/suspension-cuisine-led\";s:5:\"stock\";i:14;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:11:\"blanc sable\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1777913598),
('laravel-cache-products.nouveautes', 'O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:8:{i:0;a:20:{s:2:\"id\";i:16;s:10:\"id_produit\";i:16;s:4:\"name\";s:23:\"Suspension trio moderne\";s:3:\"nom\";s:23:\"Suspension trio moderne\";s:4:\"slug\";s:23:\"suspension-trio-moderne\";s:11:\"description\";s:258:\"Suspension trio moderne est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 6 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Suspension trio moderne est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.\";s:5:\"price\";d:879;s:4:\"prix\";d:879;s:9:\"old_price\";d:999;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:54:\"https://solarlight.ma/produits/suspension-trio-moderne\";s:5:\"stock\";i:21;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:8:\"noir mat\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:1;a:20:{s:2:\"id\";i:17;s:10:\"id_produit\";i:17;s:4:\"name\";s:25:\"Suspension laiton minimal\";s:3:\"nom\";s:25:\"Suspension laiton minimal\";s:4:\"slug\";s:25:\"suspension-laiton-minimal\";s:11:\"description\";s:260:\"Suspension laiton minimal est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 7 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Suspension laiton minimal est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espace\";s:5:\"price\";d:649;s:4:\"prix\";d:649;s:9:\"old_price\";d:799;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:56:\"https://solarlight.ma/produits/suspension-laiton-minimal\";s:5:\"stock\";i:24;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:13:\"laiton brosse\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:2;a:20:{s:2:\"id\";i:18;s:10:\"id_produit\";i:18;s:4:\"name\";s:26:\"Suspension scandinave bois\";s:3:\"nom\";s:26:\"Suspension scandinave bois\";s:4:\"slug\";s:26:\"suspension-scandinave-bois\";s:11:\"description\";s:261:\"Suspension scandinave bois est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 8 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Suspension scandinave bois est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espac\";s:5:\"price\";d:589;s:4:\"prix\";d:589;s:9:\"old_price\";d:699;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:57:\"https://solarlight.ma/produits/suspension-scandinave-bois\";s:5:\"stock\";i:8;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:12:\"verre opalin\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:3;a:20:{s:2:\"id\";i:19;s:10:\"id_produit\";i:19;s:4:\"name\";s:26:\"Suspension opaline premium\";s:3:\"nom\";s:26:\"Suspension opaline premium\";s:4:\"slug\";s:26:\"suspension-opaline-premium\";s:11:\"description\";s:261:\"Suspension opaline premium est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 9 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Suspension opaline premium est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espac\";s:5:\"price\";d:729;s:4:\"prix\";d:729;s:9:\"old_price\";d:859;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:57:\"https://solarlight.ma/produits/suspension-opaline-premium\";s:5:\"stock\";i:11;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:13:\"chrome satine\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:4;a:20:{s:2:\"id\";i:20;s:10:\"id_produit\";i:20;s:4:\"name\";s:22:\"Suspension cuisine LED\";s:3:\"nom\";s:22:\"Suspension cuisine LED\";s:4:\"slug\";s:22:\"suspension-cuisine-led\";s:11:\"description\";s:258:\"Suspension cuisine LED est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 10 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:159:\"Suspension cuisine LED est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.\";s:5:\"price\";d:519;s:4:\"prix\";d:519;s:9:\"old_price\";d:629;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:53:\"https://solarlight.ma/produits/suspension-cuisine-led\";s:5:\"stock\";i:14;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:11:\"Suspensions\";s:8:\"finition\";s:11:\"blanc sable\";s:5:\"usage\";s:21:\"ilot, table et entree\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:2;s:12:\"id_categorie\";i:2;s:4:\"name\";s:11:\"Suspensions\";s:3:\"nom\";s:11:\"Suspensions\";s:4:\"slug\";s:11:\"suspensions\";s:11:\"description\";s:64:\"Suspensions design pour creer une ambiance moderne et lumineuse.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:5;a:20:{s:2:\"id\";i:21;s:10:\"id_produit\";i:21;s:4:\"name\";s:24:\"Applique murale LED slim\";s:3:\"nom\";s:24:\"Applique murale LED slim\";s:4:\"slug\";s:24:\"applique-murale-led-slim\";s:11:\"description\";s:257:\"Applique murale LED slim est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 1 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:159:\"Applique murale LED slim est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.\";s:5:\"price\";d:289;s:4:\"prix\";d:289;s:9:\"old_price\";d:349;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1540932239986-30128078f3c5?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:55:\"https://solarlight.ma/produits/applique-murale-led-slim\";s:5:\"stock\";i:6;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:9:\"Appliques\";s:8:\"finition\";s:8:\"noir mat\";s:5:\"usage\";s:30:\"couloir, tete de lit et sejour\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:3;s:12:\"id_categorie\";i:3;s:4:\"name\";s:9:\"Appliques\";s:3:\"nom\";s:9:\"Appliques\";s:4:\"slug\";s:9:\"appliques\";s:11:\"description\";s:48:\"Appliques murales decoratives et fonctionnelles.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:3;s:12:\"id_categorie\";i:3;s:4:\"name\";s:9:\"Appliques\";s:3:\"nom\";s:9:\"Appliques\";s:4:\"slug\";s:9:\"appliques\";s:11:\"description\";s:48:\"Appliques murales decoratives et fonctionnelles.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:6;a:20:{s:2:\"id\";i:22;s:10:\"id_produit\";i:22;s:4:\"name\";s:25:\"Applique noire orientable\";s:3:\"nom\";s:25:\"Applique noire orientable\";s:4:\"slug\";s:25:\"applique-noire-orientable\";s:11:\"description\";s:258:\"Applique noire orientable est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 2 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Applique noire orientable est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.\";s:5:\"price\";d:319;s:4:\"prix\";d:319;s:9:\"old_price\";d:389;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1517142089942-ba376ce32a2e?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:56:\"https://solarlight.ma/produits/applique-noire-orientable\";s:5:\"stock\";i:9;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:9:\"Appliques\";s:8:\"finition\";s:13:\"laiton brosse\";s:5:\"usage\";s:30:\"couloir, tete de lit et sejour\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:3;s:12:\"id_categorie\";i:3;s:4:\"name\";s:9:\"Appliques\";s:3:\"nom\";s:9:\"Appliques\";s:4:\"slug\";s:9:\"appliques\";s:11:\"description\";s:48:\"Appliques murales decoratives et fonctionnelles.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:3;s:12:\"id_categorie\";i:3;s:4:\"name\";s:9:\"Appliques\";s:3:\"nom\";s:9:\"Appliques\";s:4:\"slug\";s:9:\"appliques\";s:11:\"description\";s:48:\"Appliques murales decoratives et fonctionnelles.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}i:7;a:20:{s:2:\"id\";i:23;s:10:\"id_produit\";i:23;s:4:\"name\";s:23:\"Applique laiton chambre\";s:3:\"nom\";s:23:\"Applique laiton chambre\";s:4:\"slug\";s:23:\"applique-laiton-chambre\";s:11:\"description\";s:256:\"Applique laiton chambre est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 3 de notre collection avec installation simple et rendu lumineux confortable au quotidien.\";s:17:\"short_description\";s:160:\"Applique laiton chambre est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. M\";s:5:\"price\";d:359;s:4:\"prix\";d:359;s:9:\"old_price\";d:429;s:5:\"image\";s:92:\"https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80\";s:9:\"image_url\";s:92:\"https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=80\";s:11:\"product_url\";s:54:\"https://solarlight.ma/produits/applique-laiton-chambre\";s:5:\"stock\";i:12;s:6:\"status\";s:6:\"active\";s:14:\"specifications\";a:4:{s:9:\"categorie\";s:9:\"Appliques\";s:8:\"finition\";s:12:\"verre opalin\";s:5:\"usage\";s:30:\"couloir, tete de lit et sejour\";s:8:\"garantie\";s:5:\"2 ans\";}s:8:\"category\";a:6:{s:2:\"id\";i:3;s:12:\"id_categorie\";i:3;s:4:\"name\";s:9:\"Appliques\";s:3:\"nom\";s:9:\"Appliques\";s:4:\"slug\";s:9:\"appliques\";s:11:\"description\";s:48:\"Appliques murales decoratives et fonctionnelles.\";}s:9:\"categorie\";a:6:{s:2:\"id\";i:3;s:12:\"id_categorie\";i:3;s:4:\"name\";s:9:\"Appliques\";s:3:\"nom\";s:9:\"Appliques\";s:4:\"slug\";s:9:\"appliques\";s:11:\"description\";s:48:\"Appliques murales decoratives et fonctionnelles.\";}s:12:\"note_moyenne\";i:0;s:11:\"nombre_avis\";i:0;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1777912567);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_categorie`, `nom`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Lustres', 'lustres', 'Lustres elegants pour salon, salle a manger et entree.', '2026-05-04 14:48:45', '2026-05-04 14:48:45'),
(2, 'Suspensions', 'suspensions', 'Suspensions design pour creer une ambiance moderne et lumineuse.', '2026-05-04 14:48:45', '2026-05-04 14:48:45'),
(3, 'Appliques', 'appliques', 'Appliques murales decoratives et fonctionnelles.', '2026-05-04 14:48:45', '2026-05-04 14:48:45'),
(4, 'Lampadaires', 'lampadaires', 'Lampadaires contemporains pour sejour, chambre et bureau.', '2026-05-04 14:48:45', '2026-05-04 14:48:45'),
(5, 'Lampes a poser', 'lampes-a-poser', 'Lampes de table et lampes de chevet pour tous les styles.', '2026-05-04 14:48:45', '2026-05-04 14:48:45'),
(6, 'Spots', 'spots', 'Spots LED pour plafonds, couloirs et espaces techniques.', '2026-05-04 14:48:45', '2026-05-04 14:48:45'),
(7, 'Applique solaire', 'applique-solaire-luminaire-solaire', 'Categorie importee depuis le catalogue PDF Solar4Life.', '2026-05-22 19:26:55', '2026-05-22 19:26:55'),
(8, 'Potelet et borne solaire', 'potelet-borne-solaire-luminaire-eclairage', 'Categorie importee depuis le catalogue PDF Solar4Life.', '2026-05-22 19:26:55', '2026-05-22 19:26:55'),
(9, 'Projecteur solaire', 'projecteur-solaire-solar', 'Categorie importee depuis le catalogue PDF Solar4Life.', '2026-05-22 19:26:55', '2026-05-22 19:26:55'),
(10, 'Projecteur solaire luminaire', 'projecteur-solaire-luminaire-solaire', 'Categorie importee depuis le catalogue PDF Solar4Life.', '2026-05-22 19:26:55', '2026-05-22 19:26:55'),
(11, 'Spot encastrable solaire', 'spot-encastrable-solaire-luminaire-solaire', 'Categorie importee depuis le catalogue PDF Solar4Life.', '2026-05-22 19:26:55', '2026-05-22 19:26:55'),
(12, 'Piquet solaire', 'piquet-solaire-luminaire-solaire', 'Categorie importee depuis le catalogue PDF Solar4Life.', '2026-05-22 19:26:55', '2026-05-22 19:26:55'),
(13, 'Luminaire urbain solaire', 'solaire-luminaire-urbain-collectivite-luminaire-solaire', 'Categorie importee depuis le catalogue PDF Solar4Life.', '2026-05-22 19:26:55', '2026-05-22 19:26:55'),
(14, 'Guirlande solaire', 'guirlande-solaire-luminaire-solaire', 'Categorie importee depuis le catalogue PDF Solar4Life.', '2026-05-22 19:26:55', '2026-05-22 19:26:55'),
(15, 'Kit photovoltaique plugplay', 'kit-photovoltaique-plugplay', 'Categorie importee depuis le catalogue PDF Solar4Life.', '2026-05-22 19:26:55', '2026-05-22 19:26:55');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id_client` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `api_token` varchar(64) DEFAULT NULL,
  `telephone` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'client',
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom`, `prenom`, `email`, `mot_de_passe`, `api_token`, `telephone`, `role`, `date_inscription`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Super', 'admin@lampes.ma', '$2y$12$7XMEcAF2/KvUZrWTTS7YDubfO0C/fa0Ee/pzafXWyJnDODv4x7fZ2', NULL, '0600000000', 'admin', '2026-04-27 17:22:06', NULL, '2026-04-27 17:22:06', '2026-05-04 15:24:22'),
(2, 'Dupont', 'Jean', 'jean.dupont@email.com', '$2y$12$Qryar2FT8Rfftz3Q/ZodduJ88Ls.Fwuu1Y2DQhiSBEw0s9p6ZZokK', NULL, '0612345678', 'user', '2026-04-27 17:22:06', NULL, '2026-04-27 17:22:06', '2026-04-27 17:22:06'),
(3, 'Martin', 'Marie', 'marie.martin@email.com', '$2y$12$Ow9T0VgXOg1sGFLZ.6p5LuVEv3jJRXMBqGgy5Kt1wFJ4gkLnyPAIS', 'cd0f01dcc60c4bd3d23b61cb9a3dbd63e72b9d5901113dd0ef94195c3ea8035c', '0687654321', 'user', '2026-04-27 17:22:07', NULL, '2026-04-27 17:22:07', '2026-05-22 19:23:28'),
(4, 'Test', 'User', 'test554528059@mail.com', '$2y$12$pvr6y3YEj5ZjugwOLtWPEuMEgcicB.r.OXmnqIme3WwgYUPntZacW', 'ab2182722e7d6e3e9a0470db7a5c27b49cd88890bf4fd8e513efe51639f89d13', '0612345678', 'user', '2026-05-04 13:21:08', NULL, '2026-05-04 13:21:08', '2026-05-04 13:21:08'),
(5, 'ben ayou', 'anas', 'anass@gmail.com', '$2y$12$0k4580Fo1vmBCrIjf17.Ke5HC6fZRQp0dmjkrSlbkqrv23b29fDzO', 'e3b8edbbc87c930a5b88e457987024b42b953b2423851c333fd08a12cf20a15f', '0682271058', 'user', '2026-05-04 15:25:12', NULL, '2026-05-04 15:25:12', '2026-05-04 15:25:12');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` bigint(20) UNSIGNED NOT NULL,
  `date_commande` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut` enum('en_attente','payee','expediee','livree','annulee') NOT NULL DEFAULT 'en_attente',
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `total` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'MAD',
  `id_client` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `date_commande`, `statut`, `payment_status`, `total`, `currency`, `id_client`, `created_at`, `updated_at`) VALUES
(1, '2026-05-04 13:26:08', 'en_attente', 'pending', 1599.00, 'MAD', 1, '2026-05-04 13:26:08', '2026-05-04 13:26:08'),
(2, '2026-05-04 15:16:33', 'payee', 'pending', 1599.00, 'MAD', 1, '2026-05-04 15:16:33', '2026-05-04 15:16:33'),
(3, '2026-05-22 19:42:36', 'en_attente', 'pending', 1468.00, 'MAD', 3, '2026-05-22 19:42:36', '2026-05-22 19:42:36'),
(4, '2026-05-22 19:43:29', 'en_attente', 'pending', 1599.00, 'MAD', 3, '2026-05-22 19:43:29', '2026-05-22 19:43:29'),
(5, '2026-05-22 19:44:23', 'en_attente', 'pending', 1599.00, 'MAD', 3, '2026-05-22 19:44:23', '2026-05-22 19:44:23'),
(6, '2026-05-22 19:47:59', 'en_attente', 'pending', 1599.00, 'MAD', 3, '2026-05-22 19:47:59', '2026-05-22 19:47:59'),
(7, '2026-05-22 19:51:41', 'en_attente', 'pending', 1599.00, 'MAD', 3, '2026-05-22 19:51:41', '2026-05-22 19:51:41');

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id_contact_message` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ligne_commandes`
--

CREATE TABLE `ligne_commandes` (
  `id_ligne_commande` bigint(20) UNSIGNED NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `id_commande` bigint(20) UNSIGNED NOT NULL,
  `id_produit` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ligne_commandes`
--

INSERT INTO `ligne_commandes` (`id_ligne_commande`, `quantite`, `prix_unitaire`, `id_commande`, `id_produit`, `created_at`, `updated_at`) VALUES
(1, 1, 1599.00, 1, 1, '2026-05-04 13:26:08', '2026-05-04 13:26:08'),
(2, 1, 1599.00, 2, 1, '2026-05-04 15:16:33', '2026-05-04 15:16:33'),
(3, 1, 879.00, 3, 16, '2026-05-22 19:42:36', '2026-05-22 19:42:36'),
(4, 1, 589.00, 3, 18, '2026-05-22 19:42:36', '2026-05-22 19:42:36'),
(5, 1, 1599.00, 4, 1, '2026-05-22 19:43:29', '2026-05-22 19:43:29'),
(6, 1, 1599.00, 5, 1, '2026-05-22 19:44:23', '2026-05-22 19:44:23'),
(7, 1, 1599.00, 6, 1, '2026-05-22 19:47:59', '2026-05-22 19:47:59'),
(8, 1, 1599.00, 7, 1, '2026-05-22 19:51:41', '2026-05-22 19:51:41');

-- --------------------------------------------------------

--
-- Structure de la table `ligne_paniers`
--

CREATE TABLE `ligne_paniers` (
  `id_ligne_panier` bigint(20) UNSIGNED NOT NULL,
  `quantite` int(11) NOT NULL,
  `id_panier` bigint(20) UNSIGNED NOT NULL,
  `id_produit` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ligne_paniers`
--

INSERT INTO `ligne_paniers` (`id_ligne_panier`, `quantite`, `id_panier`, `id_produit`, `created_at`, `updated_at`) VALUES
(5, 2, 3, 20, '2026-05-04 15:48:44', '2026-05-04 15:49:18');

-- --------------------------------------------------------

--
-- Structure de la table `livraisons`
--

CREATE TABLE `livraisons` (
  `id_livraison` bigint(20) UNSIGNED NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `code_postal` varchar(255) NOT NULL,
  `pays` varchar(255) NOT NULL,
  `statut` enum('en_attente','expedie','livre') NOT NULL DEFAULT 'en_attente',
  `id_commande` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `livraisons`
--

INSERT INTO `livraisons` (`id_livraison`, `adresse`, `ville`, `code_postal`, `pays`, `statut`, `id_commande`, `created_at`, `updated_at`) VALUES
(1, 'APT 3 IMM 3 LOT EL BARAKA HAY MLY YOUSSEF TAZA', 'Taza', '35000', 'Maroc', 'en_attente', 1, '2026-05-04 13:26:08', '2026-05-04 13:26:08'),
(2, 'APT 3 IMM 3 LOT EL BARAKA HAY MLY YOUSSEF TAZA', 'Taza', '35000', 'Maroc', 'en_attente', 2, '2026-05-04 15:16:33', '2026-05-04 15:16:33'),
(3, 'APT 3 IMM 3 LOT EL BARAKA HAY MLY YOUSSEF TAZA', 'Taza', '35000', 'Maroc', 'en_attente', 3, '2026-05-22 19:42:36', '2026-05-22 19:42:36'),
(4, 'APT 3 IMM 3 LOT EL BARAKA HAY MLY YOUSSEF TAZA', 'Taza', '35000', 'Maroc', 'en_attente', 4, '2026-05-22 19:43:29', '2026-05-22 19:43:29'),
(5, 'APT 3 IMM 3 LOT EL BARAKA HAY MLY YOUSSEF TAZA', 'Taza', '35000', 'Maroc', 'en_attente', 5, '2026-05-22 19:44:23', '2026-05-22 19:44:23'),
(6, 'APT 3 IMM 3 LOT EL BARAKA HAY MLY YOUSSEF TAZA', 'Taza', '35000', 'Maroc', 'en_attente', 6, '2026-05-22 19:47:59', '2026-05-22 19:47:59'),
(7, 'APT 3 IMM 3 LOT EL BARAKA HAY MLY YOUSSEF TAZA', 'Taza', '35000', 'Maroc', 'en_attente', 7, '2026-05-22 19:51:41', '2026-05-22 19:51:41');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_27_180410_create_clients_table', 1),
(5, '2026_04_27_180412_create_categories_table', 1),
(6, '2026_04_27_180413_create_produits_table', 1),
(7, '2026_04_27_180414_create_paniers_table', 1),
(8, '2026_04_27_180416_create_ligne_paniers_table', 1),
(9, '2026_04_27_180418_create_commandes_table', 1),
(10, '2026_04_27_180420_create_ligne_commandes_table', 1),
(11, '2026_04_27_180421_create_paiements_table', 1),
(12, '2026_04_27_180423_create_livraisons_table', 1),
(13, '2026_04_27_180424_create_avis_table', 1),
(14, '2026_04_27_200000_add_api_token_to_clients_table', 2),
(15, '2026_05_04_000001_add_reference_externe_to_paiements_table', 3),
(16, '2026_05_04_000002_create_pending_checkouts_table', 3),
(17, '2026_05_04_160000_add_solar4life_fields_to_catalog_tables', 4),
(18, '2026_05_04_180000_create_contact_messages_table', 5),
(19, '2026_05_04_180100_create_site_reviews_table', 5),
(20, '2026_05_05_193500_add_gallery_images_to_produits_table', 6),
(21, '2026_05_06_120000_add_role_to_clients_table', 6),
(22, '2026_05_06_123000_normalize_client_roles', 6),
(23, '2026_05_06_124000_ensure_default_admin_role', 6),
(24, '2026_05_06_130000_add_secure_payment_metadata_to_paiements_table', 6),
(25, '2026_05_06_130100_add_payment_status_to_commandes_table', 6),
(26, '2026_05_06_170000_extend_pending_checkouts_for_real_gateways', 6),
(27, '2026_05_06_170100_add_currency_to_commandes_and_gateway_response_to_paiements', 6),
(28, '2026_05_06_180000_create_password_reset_codes_table', 6);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id_paiement` bigint(20) UNSIGNED NOT NULL,
  `date_paiement` timestamp NOT NULL DEFAULT current_timestamp(),
  `montant` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'MAD',
  `methode` enum('carte','paypal','virement','livraison') NOT NULL,
  `payment_gateway` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_token` varchar(255) DEFAULT NULL,
  `statut` enum('en_attente','valide','echoue') NOT NULL DEFAULT 'en_attente',
  `payment_status` varchar(255) DEFAULT NULL,
  `card_brand` varchar(255) DEFAULT NULL,
  `card_last4` varchar(4) DEFAULT NULL,
  `card_country` varchar(2) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `reference_externe` varchar(255) DEFAULT NULL,
  `id_commande` bigint(20) UNSIGNED NOT NULL,
  `id_client` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id_paiement`, `date_paiement`, `montant`, `currency`, `methode`, `payment_gateway`, `transaction_id`, `payment_token`, `statut`, `payment_status`, `card_brand`, `card_last4`, `card_country`, `gateway_response`, `reference_externe`, `id_commande`, `id_client`, `created_at`, `updated_at`) VALUES
(1, '2026-05-04 13:26:08', 1599.00, 'MAD', 'livraison', NULL, NULL, NULL, 'en_attente', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-05-04 13:26:08', '2026-05-04 13:26:08'),
(2, '2026-05-04 15:16:33', 1599.00, 'MAD', 'carte', NULL, NULL, NULL, 'valide', NULL, NULL, NULL, NULL, NULL, 'demo_KWD2GE5H9COK', 2, NULL, '2026-05-04 15:16:33', '2026-05-04 15:16:33'),
(3, '2026-05-22 19:42:36', 1468.00, 'MAD', 'livraison', NULL, NULL, NULL, 'en_attente', NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, '2026-05-22 19:42:36', '2026-05-22 19:42:36'),
(4, '2026-05-22 19:43:29', 1599.00, 'MAD', 'livraison', NULL, NULL, NULL, 'en_attente', NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, '2026-05-22 19:43:29', '2026-05-22 19:43:29'),
(5, '2026-05-22 19:44:23', 1599.00, 'MAD', 'livraison', NULL, NULL, NULL, 'en_attente', NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, '2026-05-22 19:44:23', '2026-05-22 19:44:23'),
(6, '2026-05-22 19:47:59', 1599.00, 'MAD', 'livraison', NULL, NULL, NULL, 'en_attente', NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL, '2026-05-22 19:47:59', '2026-05-22 19:47:59'),
(7, '2026-05-22 19:51:41', 1599.00, 'MAD', 'livraison', NULL, NULL, NULL, 'en_attente', NULL, NULL, NULL, NULL, NULL, NULL, 7, NULL, '2026-05-22 19:51:41', '2026-05-22 19:51:41');

-- --------------------------------------------------------

--
-- Structure de la table `paniers`
--

CREATE TABLE `paniers` (
  `id_panier` bigint(20) UNSIGNED NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_client` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `paniers`
--

INSERT INTO `paniers` (`id_panier`, `date_creation`, `id_client`, `created_at`, `updated_at`) VALUES
(1, '2026-04-28 18:25:21', 1, '2026-04-28 18:25:21', '2026-04-28 18:25:21'),
(2, '2026-05-04 13:21:08', 4, '2026-05-04 13:21:08', '2026-05-04 13:21:08'),
(3, '2026-05-04 15:25:12', 5, '2026-05-04 15:25:12', '2026-05-04 15:25:12'),
(4, '2026-05-22 19:23:46', 3, '2026-05-22 19:23:46', '2026-05-22 19:23:46');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_codes`
--

CREATE TABLE `password_reset_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pending_checkouts`
--

CREATE TABLE `pending_checkouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stripe_session_id` varchar(255) NOT NULL,
  `payment_gateway` varchar(255) DEFAULT NULL,
  `gateway_session_id` varchar(255) DEFAULT NULL,
  `gateway_reference` varchar(255) DEFAULT NULL,
  `id_client` bigint(20) UNSIGNED NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `gateway_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_payload`)),
  `expires_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id_produit` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `short_description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `image_url` varchar(255) NOT NULL,
  `gallery_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery_images`)),
  `image` varchar(255) DEFAULT NULL,
  `product_url` varchar(255) DEFAULT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `id_categorie` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `nom`, `slug`, `description`, `short_description`, `prix`, `old_price`, `stock`, `status`, `image_url`, `gallery_images`, `image`, `product_url`, `specifications`, `id_categorie`, `created_at`, `updated_at`) VALUES
(1, 'Applique Solaire Austin Detecteur Mouvement Ip44 5W 4000K 350Lm Blanc', 'applique-solaire-austin-detecteur_mouvement-ip44-5w-4000k-350lm-blanc', '', '', 1599.00, 1899.00, 10, 'active', '/catalog-import/pdf-products/product-002.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-002.jpg\",\"\\/catalog-import\\/pdf-products\\/product-005.jpg\",\"\\/catalog-import\\/pdf-products\\/product-007.jpg\",\"\\/catalog-import\\/pdf-products\\/product-008.jpg\",\"\\/catalog-import\\/pdf-products\\/product-020.jpg\"]', '/catalog-import/pdf-products/product-002.jpg', 'https://solar4life.fr/produit/applique-solaire-austin-detecteur_mouvement-ip44-5w-4000k-350lm-blanc', '{\"categorie\":\"Lustres\",\"finition\":\"noir mat\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(2, 'Applique Solaire Butterfly Detecteur De Mouvement Ip65 3W 4000K 220Lm Noir', 'applique-solaire-butterfly-detecteur-de-mouvement-ip65-3w-4000k-220lm-noir', '', '', 1399.00, 1699.00, 10, 'active', '/catalog-import/pdf-products/product-003.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-003.jpg\",\"\\/catalog-import\\/pdf-products\\/product-006.jpg\",\"\\/catalog-import\\/pdf-products\\/product-009.jpg\",\"\\/catalog-import\\/pdf-products\\/product-010.jpg\"]', '/catalog-import/pdf-products/product-003.jpg', 'https://solar4life.fr/produit/applique-solaire-butterfly-detecteur-de-mouvement-ip65-3w-4000k-220lm-noir', '{\"categorie\":\"Lustres\",\"finition\":\"laiton brosse\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(3, 'Potelet Solaire Bastide', 'potelet-solaire-bastide', '', '', 1299.00, 1499.00, 12, 'active', '', '[]', '', 'https://solar4life.fr/produit/potelet-solaire-bastide', '{\"categorie\":\"Lustres\",\"finition\":\"verre opalin\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 8, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(4, 'Projecteur Solaire Summer Detecteur Ip65 8W 4000K 500Lm Noir', 'projecteur-solaire-summer-detecteur-ip65-8w-4000k-500lm-noir', '', '', 1799.00, 2099.00, 15, 'active', '/catalog-import/pdf-products/product-021.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-021.jpg\",\"\\/catalog-import\\/pdf-products\\/product-022.jpg\",\"\\/catalog-import\\/pdf-products\\/product-028.jpg\",\"\\/catalog-import\\/pdf-products\\/product-029.jpg\"]', '/catalog-import/pdf-products/product-021.jpg', 'https://solar4life.fr/produit/projecteur-solaire-summer-detecteur-ip65-8w-4000k-500lm-noir', '{\"categorie\":\"Lustres\",\"finition\":\"chrome satine\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(5, 'Proj Solaire Sigma 6W 4000K 400Lm Noir', 'proj-solaire-sigma-6w-4000k-400lm-noir', '', '', 1499.00, 1699.00, 18, 'active', '/catalog-import/pdf-products/product-023.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-023.jpg\",\"\\/catalog-import\\/pdf-products\\/product-024.jpg\",\"\\/catalog-import\\/pdf-products\\/product-025.jpg\",\"\\/catalog-import\\/pdf-products\\/product-026.jpg\",\"\\/catalog-import\\/pdf-products\\/product-033.jpg\",\"\\/catalog-import\\/pdf-products\\/product-034.jpg\",\"\\/catalog-import\\/pdf-products\\/product-035.jpg\",\"\\/catalog-import\\/pdf-products\\/product-036.jpg\",\"\\/catalog-import\\/pdf-products\\/product-037.jpg\",\"\\/catalog-import\\/pdf-products\\/product-038.jpg\",\"\\/catalog-import\\/pdf-products\\/product-039.jpg\",\"\\/catalog-import\\/pdf-products\\/product-041.jpg\",\"\\/catalog-import\\/pdf-products\\/product-042.jpg\",\"\\/catalog-import\\/pdf-products\\/product-046.jpg\",\"\\/catalog-import\\/pdf-products\\/product-047.jpg\",\"\\/catalog-import\\/pdf-products\\/product-048.jpg\",\"\\/catalog-import\\/pdf-products\\/product-049.jpg\",\"\\/catalog-import\\/pdf-products\\/product-050.jpg\",\"\\/catalog-import\\/pdf-products\\/product-051.jpg\",\"\\/catalog-import\\/pdf-products\\/product-052.jpg\",\"\\/catalog-import\\/pdf-products\\/product-053.jpg\",\"\\/catalog-import\\/pdf-products\\/product-054.jpg\",\"\\/catalog-import\\/pdf-products\\/product-055.jpg\",\"\\/catalog-import\\/pdf-products\\/product-056.jpg\",\"\\/catalog-import\\/pdf-products\\/product-058.jpg\",\"\\/catalog-import\\/pdf-products\\/product-059.jpg\",\"\\/catalog-import\\/pdf-products\\/product-060.jpg\",\"\\/catalog-import\\/pdf-products\\/product-063.jpg\"]', '/catalog-import/pdf-products/product-023.jpg', 'https://solar4life.fr/produit/proj-solaire-sigma-6w-4000k-400lm-noir', '{\"categorie\":\"Lustres\",\"finition\":\"blanc sable\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(6, 'Projecteur Solaire Falcon Detecteur De Mouvement Ip44 15W 4000K 1000Lm Noir', 'projecteur-solaire-falcon-detecteur_de_mouvement-ip44-15w-4000k-1000lm-noir', '', '', 1899.00, 2299.00, 21, 'active', '/catalog-import/pdf-products/product-030.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-030.jpg\",\"\\/catalog-import\\/pdf-products\\/product-031.jpg\"]', '/catalog-import/pdf-products/product-030.jpg', 'https://solar4life.fr/produit/projecteur-solaire-falcon-detecteur_de_mouvement-ip44-15w-4000k-1000lm-noir', '{\"categorie\":\"Lustres\",\"finition\":\"noir mat\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(7, 'Projecteur Solaire Led 15W 4000K 1000Lm Noir', 'projecteur-solaire-led-15w-4000k-1000lm-noir', '', '', 999.00, 1199.00, 24, 'active', '/catalog-import/pdf-products/product-040.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-040.jpg\"]', '/catalog-import/pdf-products/product-040.jpg', 'https://solar4life.fr/produit/projecteur-solaire-led-15w-4000k-1000lm-noir', '{\"categorie\":\"Lustres\",\"finition\":\"laiton brosse\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(8, 'Proj Solaire Falcon Detect Ir 15W 4000K 1000Lm Noir', 'proj-solaire-falcon-detect-ir-15w-4000k-1000lm-noir', '', '', 1699.00, 1999.00, 10, 'active', '/catalog-import/pdf-products/product-043.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-043.jpg\",\"\\/catalog-import\\/pdf-products\\/product-044.jpg\"]', '/catalog-import/pdf-products/product-043.jpg', 'https://solar4life.fr/produit/proj-solaire-falcon-detect-ir-15w-4000k-1000lm-noir', '{\"categorie\":\"Lustres\",\"finition\":\"verre opalin\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(9, 'Projecteur Solaire Trusty 9 Detecteur Mouvement Ip44 9W 6000K 600Lm Noir', 'projecteur-solaire-trusty-9-detecteur_mouvement-ip44-9w-6000k-600lm-noir', '', '', 1199.00, 1399.00, 11, 'active', '/catalog-import/pdf-products/product-061.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-061.jpg\"]', '/catalog-import/pdf-products/product-061.jpg', 'https://solar4life.fr/produit/projecteur-solaire-trusty-9-detecteur_mouvement-ip44-9w-6000k-600lm-noir', '{\"categorie\":\"Lustres\",\"finition\":\"chrome satine\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(10, 'Projecteur Solaire Trusty 9 Detecteur Mouvement Ip44 9W 6000K 600Lm Blanc', 'projecteur-solaire-trusty-9-detecteur_mouvement-ip44-9w-6000k-600lm-blanc', '', '', 2399.00, 2699.00, 14, 'active', '/catalog-import/pdf-products/product-062.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-062.jpg\"]', '/catalog-import/pdf-products/product-062.jpg', 'https://solar4life.fr/produit/projecteur-solaire-trusty-9-detecteur_mouvement-ip44-9w-6000k-600lm-blanc', '{\"categorie\":\"Lustres\",\"finition\":\"blanc sable\",\"usage\":\"salon et salle a manger\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(11, 'Piquet Solaire Flame 2W 1500K Noir Solar4Life', 'piquet-solaire-flame-2w-1500k-noir-solar4life', '', '', 799.00, 949.00, 10, 'active', '/catalog-import/pdf-products/product-064.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-064.jpg\",\"\\/catalog-import\\/pdf-products\\/product-065.jpg\",\"\\/catalog-import\\/pdf-products\\/product-066.jpg\"]', '/catalog-import/pdf-products/product-064.jpg', 'https://solar4life.fr/produit/piquet-solaire-flame-2w-1500k-noir-solar4life', '{\"categorie\":\"Suspensions\",\"finition\":\"noir mat\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(12, 'Applique Solaire Austin Detect Ir 5W 4000K 350Lm Blanc', 'applique-solaire-austin-detect-ir-5w-4000k-350lm-blanc', '', '', 459.00, 559.00, 10, 'active', '/catalog-import/pdf-products/product-071.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-071.jpg\",\"\\/catalog-import\\/pdf-products\\/product-072.jpg\",\"\\/catalog-import\\/pdf-products\\/product-073.jpg\"]', '/catalog-import/pdf-products/product-071.jpg', 'https://solar4life.fr/produit/applique-solaire-austin-detect-ir-5w-4000k-350lm-blanc', '{\"categorie\":\"Suspensions\",\"finition\":\"laiton brosse\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(13, 'Applique Solaire Bastide', 'applique-solaire-bastide', '', '', 699.00, 849.00, 12, 'active', '/catalog-import/pdf-products/product-074.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-074.jpg\",\"\\/catalog-import\\/pdf-products\\/product-075.jpg\",\"\\/catalog-import\\/pdf-products\\/product-076.jpg\"]', '/catalog-import/pdf-products/product-074.jpg', 'https://solar4life.fr/produit/applique-solaire-bastide', '{\"categorie\":\"Suspensions\",\"finition\":\"verre opalin\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(14, 'Piquet Solaire Flame 2W 1500K Noir', 'piquet-solaire-flame-2w-1500k-noir', '', '', 999.00, 1199.00, 15, 'active', '/catalog-import/pdf-products/product-078.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-078.jpg\",\"\\/catalog-import\\/pdf-products\\/product-083.jpg\",\"\\/catalog-import\\/pdf-products\\/product-084.jpg\",\"\\/catalog-import\\/pdf-products\\/product-087.jpg\",\"\\/catalog-import\\/pdf-products\\/product-088.jpg\",\"\\/catalog-import\\/pdf-products\\/product-091.jpg\"]', '/catalog-import/pdf-products/product-078.jpg', 'https://solar4life.fr/produit/piquet-solaire-flame-2w-1500k-noir', '{\"categorie\":\"Suspensions\",\"finition\":\"chrome satine\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(15, 'Kit 3 Piquets Solaires Pico 4W 6000K 280Lm Noir', 'kit-3-piquets-solaires-pico-4w-6000k-280lm-noir', '', '', 549.00, 649.00, 18, 'active', '/catalog-import/pdf-products/product-081.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-081.jpg\",\"\\/catalog-import\\/pdf-products\\/product-085.jpg\",\"\\/catalog-import\\/pdf-products\\/product-089.jpg\"]', '/catalog-import/pdf-products/product-081.jpg', 'https://solar4life.fr/produit/kit-3-piquets-solaires-pico-4w-6000k-280lm-noir', '{\"categorie\":\"Suspensions\",\"finition\":\"blanc sable\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:45', '2026-05-22 20:02:00'),
(16, 'Piquet Solaire Fox 3W Ip65 Detecteur 4000K 200Lm Noir', 'piquet-solaire-fox-3w-ip65-detecteur-4000k-200lm-noir', '', '', 879.00, 999.00, 20, 'active', '/catalog-import/pdf-products/product-080.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-080.jpg\"]', '/catalog-import/pdf-products/product-080.jpg', 'https://solar4life.fr/produit/piquet-solaire-fox-3w-ip65-detecteur-4000k-200lm-noir', '{\"categorie\":\"Suspensions\",\"finition\":\"noir mat\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(17, 'Piquet Solaire Iota 8W 4000K 500Lm Noir Ip65', 'piquet-solaire-iota-8w-4000k-500lm-noir-ip65', '', '', 649.00, 799.00, 24, 'active', '', '[]', '', 'https://solar4life.fr/produit/piquet-solaire-iota-8w-4000k-500lm-noir-ip65', '{\"categorie\":\"Suspensions\",\"finition\":\"laiton brosse\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(18, 'Streetlight Solaire Panneau Solaire Beamer13 Ip65 13W 4000K 850Lm Noir', 'streetlight-solaire-panneau-solaire-beamer13-ip65-13w-4000k-850lm-noir', '', '', 589.00, 699.00, 10, 'active', '', '[]', '', 'https://solar4life.fr/produit/streetlight-solaire-panneau-solaire-beamer13-ip65-13w-4000k-850lm-noir', '{\"categorie\":\"Suspensions\",\"finition\":\"verre opalin\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(19, 'Projecteur Solaire Beamer 24W Ip65 24W 4000K 1600Lm Noir', 'projecteur-solaire-beamer-24w-ip65-24w-4000k-1600lm-noir', '', '', 729.00, 859.00, 11, 'active', '/catalog-import/pdf-products/product-093.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-093.jpg\"]', '/catalog-import/pdf-products/product-093.jpg', 'https://solar4life.fr/produit/projecteur-solaire-beamer-24w-ip65-24w-4000k-1600lm-noir', '{\"categorie\":\"Suspensions\",\"finition\":\"chrome satine\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(20, 'Suspension cuisine LED', 'suspension-cuisine-led', 'Suspension cuisine LED est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 10 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Suspension cuisine LED est un Suspensions soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 519.00, 629.00, 14, 'inactive', '/catalog-import/pdf-products/product-020.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-020.jpg\",\"\\/catalog-import\\/pdf-products\\/product-080.jpg\"]', '/catalog-import/pdf-products/product-020.jpg', 'https://solarlight.ma/produits/suspension-cuisine-led', '{\"categorie\":\"Suspensions\",\"finition\":\"blanc sable\",\"usage\":\"ilot, table et entree\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(21, 'Applique murale LED slim', 'applique-murale-led-slim', 'Applique murale LED slim est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 1 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique murale LED slim est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 289.00, 349.00, 6, 'inactive', '/catalog-import/pdf-products/product-021.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-021.jpg\",\"\\/catalog-import\\/pdf-products\\/product-081.jpg\"]', '/catalog-import/pdf-products/product-021.jpg', 'https://solarlight.ma/produits/applique-murale-led-slim', '{\"categorie\":\"Appliques\",\"finition\":\"noir mat\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(22, 'Applique noire orientable', 'applique-noire-orientable', 'Applique noire orientable est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 2 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique noire orientable est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 319.00, 389.00, 9, 'inactive', '/catalog-import/pdf-products/product-022.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-022.jpg\",\"\\/catalog-import\\/pdf-products\\/product-082.jpg\"]', '/catalog-import/pdf-products/product-022.jpg', 'https://solarlight.ma/produits/applique-noire-orientable', '{\"categorie\":\"Appliques\",\"finition\":\"laiton brosse\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(23, 'Applique laiton chambre', 'applique-laiton-chambre', 'Applique laiton chambre est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 3 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique laiton chambre est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. M', 359.00, 429.00, 12, 'inactive', '/catalog-import/pdf-products/product-023.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-023.jpg\",\"\\/catalog-import\\/pdf-products\\/product-083.jpg\"]', '/catalog-import/pdf-products/product-023.jpg', 'https://solarlight.ma/produits/applique-laiton-chambre', '{\"categorie\":\"Appliques\",\"finition\":\"verre opalin\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(24, 'Applique verre satine', 'applique-verre-satine', 'Applique verre satine est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 4 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique verre satine est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Mod', 269.00, 329.00, 15, 'inactive', '/catalog-import/pdf-products/product-024.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-024.jpg\",\"\\/catalog-import\\/pdf-products\\/product-084.jpg\"]', '/catalog-import/pdf-products/product-024.jpg', 'https://solarlight.ma/produits/applique-verre-satine', '{\"categorie\":\"Appliques\",\"finition\":\"chrome satine\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(25, 'Applique duo salon', 'applique-duo-salon', 'Applique duo salon est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 5 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique duo salon est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele', 399.00, 469.00, 18, 'inactive', '/catalog-import/pdf-products/product-025.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-025.jpg\",\"\\/catalog-import\\/pdf-products\\/product-085.jpg\"]', '/catalog-import/pdf-products/product-025.jpg', 'https://solarlight.ma/produits/applique-duo-salon', '{\"categorie\":\"Appliques\",\"finition\":\"blanc sable\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(26, 'Applique lecture pivotante', 'applique-lecture-pivotante', 'Applique lecture pivotante est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 6 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique lecture pivotante est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces', 249.00, 299.00, 21, 'inactive', '/catalog-import/pdf-products/product-026.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-026.jpg\",\"\\/catalog-import\\/pdf-products\\/product-086.jpg\"]', '/catalog-import/pdf-products/product-026.jpg', 'https://solarlight.ma/produits/applique-lecture-pivotante', '{\"categorie\":\"Appliques\",\"finition\":\"noir mat\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(27, 'Applique couloir moderne', 'applique-couloir-moderne', 'Applique couloir moderne est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 7 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique couloir moderne est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 219.00, 279.00, 24, 'inactive', '/catalog-import/pdf-products/product-027.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-027.jpg\",\"\\/catalog-import\\/pdf-products\\/product-087.jpg\"]', '/catalog-import/pdf-products/product-027.jpg', 'https://solarlight.ma/produits/applique-couloir-moderne', '{\"categorie\":\"Appliques\",\"finition\":\"laiton brosse\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(28, 'Applique hotel premium', 'applique-hotel-premium', 'Applique hotel premium est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 8 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique hotel premium est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Mo', 429.00, 499.00, 8, 'inactive', '/catalog-import/pdf-products/product-028.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-028.jpg\",\"\\/catalog-import\\/pdf-products\\/product-088.jpg\"]', '/catalog-import/pdf-products/product-028.jpg', 'https://solarlight.ma/produits/applique-hotel-premium', '{\"categorie\":\"Appliques\",\"finition\":\"verre opalin\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(29, 'Applique geometrique or', 'applique-geometrique-or', 'Applique geometrique or est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 9 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique geometrique or est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. M', 339.00, 399.00, 11, 'inactive', '/catalog-import/pdf-products/product-029.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-029.jpg\",\"\\/catalog-import\\/pdf-products\\/product-089.jpg\"]', '/catalog-import/pdf-products/product-029.jpg', 'https://solarlight.ma/produits/applique-geometrique-or', '{\"categorie\":\"Appliques\",\"finition\":\"chrome satine\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(30, 'Applique ambiance douce', 'applique-ambiance-douce', 'Applique ambiance douce est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 10 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Applique ambiance douce est un Appliques soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. M', 279.00, 329.00, 14, 'inactive', '/catalog-import/pdf-products/product-030.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-030.jpg\",\"\\/catalog-import\\/pdf-products\\/product-090.jpg\"]', '/catalog-import/pdf-products/product-030.jpg', 'https://solarlight.ma/produits/applique-ambiance-douce', '{\"categorie\":\"Appliques\",\"finition\":\"blanc sable\",\"usage\":\"couloir, tete de lit et sejour\",\"garantie\":\"2 ans\"}', 10, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(31, 'Lampadaire arc luxe', 'lampadaire-arc-luxe', 'Lampadaire arc luxe est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 1 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire arc luxe est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Mod', 1199.00, 1399.00, 6, 'inactive', '/catalog-import/pdf-products/product-031.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-031.jpg\",\"\\/catalog-import\\/pdf-products\\/product-091.jpg\"]', '/catalog-import/pdf-products/product-031.jpg', 'https://solarlight.ma/produits/lampadaire-arc-luxe', '{\"categorie\":\"Lampadaires\",\"finition\":\"noir mat\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 8, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(32, 'Lampadaire noir mat', 'lampadaire-noir-mat', 'Lampadaire noir mat est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 2 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire noir mat est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Mod', 749.00, 899.00, 9, 'inactive', '/catalog-import/pdf-products/product-032.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-032.jpg\",\"\\/catalog-import\\/pdf-products\\/product-092.jpg\"]', '/catalog-import/pdf-products/product-032.jpg', 'https://solarlight.ma/produits/lampadaire-noir-mat', '{\"categorie\":\"Lampadaires\",\"finition\":\"laiton brosse\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 8, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(33, 'Lampadaire tripode bois', 'lampadaire-tripode-bois', 'Lampadaire tripode bois est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 3 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire tripode bois est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 899.00, 1049.00, 12, 'inactive', '/catalog-import/pdf-products/product-033.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-033.jpg\",\"\\/catalog-import\\/pdf-products\\/product-093.jpg\"]', '/catalog-import/pdf-products/product-033.jpg', 'https://solarlight.ma/produits/lampadaire-tripode-bois', '{\"categorie\":\"Lampadaires\",\"finition\":\"verre opalin\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 8, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(34, 'Lampadaire lecture LED', 'lampadaire-lecture-led', 'Lampadaire lecture LED est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 4 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire lecture LED est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 629.00, 749.00, 15, 'inactive', '/catalog-import/pdf-products/product-034.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-034.jpg\",\"\\/catalog-import\\/pdf-products\\/product-094.jpg\"]', '/catalog-import/pdf-products/product-034.jpg', 'https://solarlight.ma/produits/lampadaire-lecture-led', '{\"categorie\":\"Lampadaires\",\"finition\":\"chrome satine\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 8, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(35, 'Lampadaire design salon', 'lampadaire-design-salon', 'Lampadaire design salon est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 5 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire design salon est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 979.00, 1149.00, 18, 'inactive', '/catalog-import/pdf-products/product-035.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-035.jpg\",\"\\/catalog-import\\/pdf-products\\/product-095.jpg\"]', '/catalog-import/pdf-products/product-035.jpg', 'https://solarlight.ma/produits/lampadaire-design-salon', '{\"categorie\":\"Lampadaires\",\"finition\":\"blanc sable\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 8, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(36, 'Lampadaire courbe dore', 'lampadaire-courbe-dore', 'Lampadaire courbe dore est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 6 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire courbe dore est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 1099.00, 1299.00, 21, 'inactive', '/catalog-import/pdf-products/product-036.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-036.jpg\",\"\\/catalog-import\\/pdf-products\\/product-096.jpg\"]', '/catalog-import/pdf-products/product-036.jpg', 'https://solarlight.ma/produits/lampadaire-courbe-dore', '{\"categorie\":\"Lampadaires\",\"finition\":\"noir mat\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(37, 'Lampadaire textile beige', 'lampadaire-textile-beige', 'Lampadaire textile beige est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 7 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire textile beige est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces', 689.00, 799.00, 24, 'inactive', '/catalog-import/pdf-products/product-037.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-037.jpg\",\"\\/catalog-import\\/pdf-products\\/product-097.jpg\"]', '/catalog-import/pdf-products/product-037.jpg', 'https://solarlight.ma/produits/lampadaire-textile-beige', '{\"categorie\":\"Lampadaires\",\"finition\":\"laiton brosse\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(38, 'Lampadaire minimal LED', 'lampadaire-minimal-led', 'Lampadaire minimal LED est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 8 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire minimal LED est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 839.00, 959.00, 8, 'inactive', '/catalog-import/pdf-products/product-038.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-038.jpg\",\"\\/catalog-import\\/pdf-products\\/product-098.jpg\"]', '/catalog-import/pdf-products/product-038.jpg', 'https://solarlight.ma/produits/lampadaire-minimal-led', '{\"categorie\":\"Lampadaires\",\"finition\":\"verre opalin\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(39, 'Lampadaire opaline premium', 'lampadaire-opaline-premium', 'Lampadaire opaline premium est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 9 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire opaline premium est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espac', 929.00, 1049.00, 11, 'inactive', '/catalog-import/pdf-products/product-039.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-039.jpg\",\"\\/catalog-import\\/pdf-products\\/product-099.jpg\"]', '/catalog-import/pdf-products/product-039.jpg', 'https://solarlight.ma/produits/lampadaire-opaline-premium', '{\"categorie\":\"Lampadaires\",\"finition\":\"chrome satine\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(40, 'Lampadaire chambre cosy', 'lampadaire-chambre-cosy', 'Lampadaire chambre cosy est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 10 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampadaire chambre cosy est un Lampadaires soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 599.00, 699.00, 14, 'inactive', '/catalog-import/pdf-products/product-040.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-040.jpg\",\"\\/catalog-import\\/pdf-products\\/product-100.jpg\"]', '/catalog-import/pdf-products/product-040.jpg', 'https://solarlight.ma/produits/lampadaire-chambre-cosy', '{\"categorie\":\"Lampadaires\",\"finition\":\"blanc sable\",\"usage\":\"coin lecture et salon\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(41, 'Lampe de chevet opaline', 'lampe-de-chevet-opaline', 'Lampe de chevet opaline est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 1 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe de chevet opaline est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espac', 299.00, 359.00, 6, 'inactive', '/catalog-import/pdf-products/product-041.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-041.jpg\",\"\\/catalog-import\\/pdf-products\\/product-042.jpg\"]', '/catalog-import/pdf-products/product-041.jpg', 'https://solarlight.ma/produits/lampe-de-chevet-opaline', '{\"categorie\":\"Lampes a poser\",\"finition\":\"noir mat\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(42, 'Lampe de bureau noire', 'lampe-de-bureau-noire', 'Lampe de bureau noire est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 2 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe de bureau noire est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces', 249.00, 299.00, 9, 'inactive', '/catalog-import/pdf-products/product-042.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-042.jpg\",\"\\/catalog-import\\/pdf-products\\/product-043.jpg\"]', '/catalog-import/pdf-products/product-042.jpg', 'https://solarlight.ma/produits/lampe-de-bureau-noire', '{\"categorie\":\"Lampes a poser\",\"finition\":\"laiton brosse\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(43, 'Lampe de table laiton', 'lampe-de-table-laiton', 'Lampe de table laiton est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 3 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe de table laiton est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces', 379.00, 449.00, 12, 'inactive', '/catalog-import/pdf-products/product-043.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-043.jpg\",\"\\/catalog-import\\/pdf-products\\/product-044.jpg\"]', '/catalog-import/pdf-products/product-043.jpg', 'https://solarlight.ma/produits/lampe-de-table-laiton', '{\"categorie\":\"Lampes a poser\",\"finition\":\"verre opalin\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(44, 'Lampe vintage atelier', 'lampe-vintage-atelier', 'Lampe vintage atelier est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 4 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe vintage atelier est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces', 329.00, 389.00, 15, 'inactive', '/catalog-import/pdf-products/product-044.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-044.jpg\",\"\\/catalog-import\\/pdf-products\\/product-045.jpg\"]', '/catalog-import/pdf-products/product-044.jpg', 'https://solarlight.ma/produits/lampe-vintage-atelier', '{\"categorie\":\"Lampes a poser\",\"finition\":\"chrome satine\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(45, 'Lampe boule verre fume', 'lampe-boule-verre-fume', 'Lampe boule verre fume est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 5 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe boule verre fume est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espace', 419.00, 499.00, 18, 'inactive', '/catalog-import/pdf-products/product-045.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-045.jpg\",\"\\/catalog-import\\/pdf-products\\/product-046.jpg\"]', '/catalog-import/pdf-products/product-045.jpg', 'https://solarlight.ma/produits/lampe-boule-verre-fume', '{\"categorie\":\"Lampes a poser\",\"finition\":\"blanc sable\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(46, 'Lampe ceramique blanche', 'lampe-ceramique-blanche', 'Lampe ceramique blanche est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 6 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe ceramique blanche est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espac', 289.00, 349.00, 21, 'inactive', '/catalog-import/pdf-products/product-046.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-046.jpg\",\"\\/catalog-import\\/pdf-products\\/product-047.jpg\"]', '/catalog-import/pdf-products/product-046.jpg', 'https://solarlight.ma/produits/lampe-ceramique-blanche', '{\"categorie\":\"Lampes a poser\",\"finition\":\"noir mat\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(47, 'Lampe de nuit tactile', 'lampe-de-nuit-tactile', 'Lampe de nuit tactile est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 7 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe de nuit tactile est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces', 199.00, 249.00, 24, 'inactive', '/catalog-import/pdf-products/product-047.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-047.jpg\",\"\\/catalog-import\\/pdf-products\\/product-048.jpg\"]', '/catalog-import/pdf-products/product-047.jpg', 'https://solarlight.ma/produits/lampe-de-nuit-tactile', '{\"categorie\":\"Lampes a poser\",\"finition\":\"laiton brosse\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(48, 'Lampe marbre et or', 'lampe-marbre-et-or', 'Lampe marbre et or est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 8 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe marbre et or est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. M', 459.00, 529.00, 8, 'inactive', '/catalog-import/pdf-products/product-048.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-048.jpg\",\"\\/catalog-import\\/pdf-products\\/product-049.jpg\"]', '/catalog-import/pdf-products/product-048.jpg', 'https://solarlight.ma/produits/lampe-marbre-et-or', '{\"categorie\":\"Lampes a poser\",\"finition\":\"verre opalin\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 13, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(49, 'Lampe ambiance salon', 'lampe-ambiance-salon', 'Lampe ambiance salon est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 9 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe ambiance salon est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces.', 279.00, 329.00, 11, 'inactive', '/catalog-import/pdf-products/product-049.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-049.jpg\",\"\\/catalog-import\\/pdf-products\\/product-050.jpg\"]', '/catalog-import/pdf-products/product-049.jpg', 'https://solarlight.ma/produits/lampe-ambiance-salon', '{\"categorie\":\"Lampes a poser\",\"finition\":\"chrome satine\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(50, 'Lampe LED rechargeable', 'lampe-led-rechargeable', 'Lampe LED rechargeable est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 10 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Lampe LED rechargeable est un Lampes a poser soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espace', 229.00, 289.00, 14, 'inactive', '/catalog-import/pdf-products/product-050.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-050.jpg\",\"\\/catalog-import\\/pdf-products\\/product-051.jpg\"]', '/catalog-import/pdf-products/product-050.jpg', 'https://solarlight.ma/produits/lampe-led-rechargeable', '{\"categorie\":\"Lampes a poser\",\"finition\":\"blanc sable\",\"usage\":\"chevet, bureau et console\",\"garantie\":\"2 ans\"}', 8, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(51, 'Spot LED blanc chaud', 'spot-led-blanc-chaud', 'Spot LED blanc chaud est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 1 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot LED blanc chaud est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 1', 89.00, 109.00, 6, 'inactive', '/catalog-import/pdf-products/product-051.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-051.jpg\",\"\\/catalog-import\\/pdf-products\\/product-052.jpg\"]', '/catalog-import/pdf-products/product-051.jpg', 'https://solarlight.ma/produits/spot-led-blanc-chaud', '{\"categorie\":\"Spots\",\"finition\":\"noir mat\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 9, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(52, 'Spot encastrable noir', 'spot-encastrable-noir', 'Spot encastrable noir est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 2 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot encastrable noir est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele', 99.00, 119.00, 9, 'inactive', '/catalog-import/pdf-products/product-052.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-052.jpg\",\"\\/catalog-import\\/pdf-products\\/product-053.jpg\"]', '/catalog-import/pdf-products/product-052.jpg', 'https://solarlight.ma/produits/spot-encastrable-noir', '{\"categorie\":\"Spots\",\"finition\":\"laiton brosse\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 10, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(53, 'Spot orientable plafond', 'spot-orientable-plafond', 'Spot orientable plafond est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 3 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot orientable plafond est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Model', 129.00, 149.00, 12, 'inactive', '/catalog-import/pdf-products/product-053.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-053.jpg\",\"\\/catalog-import\\/pdf-products\\/product-054.jpg\"]', '/catalog-import/pdf-products/product-053.jpg', 'https://solarlight.ma/produits/spot-orientable-plafond', '{\"categorie\":\"Spots\",\"finition\":\"verre opalin\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 11, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(54, 'Spot cuisine LED', 'spot-cuisine-led', 'Spot cuisine LED est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 4 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot cuisine LED est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 4 de', 109.00, 129.00, 15, 'inactive', '/catalog-import/pdf-products/product-054.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-054.jpg\",\"\\/catalog-import\\/pdf-products\\/product-055.jpg\"]', '/catalog-import/pdf-products/product-054.jpg', 'https://solarlight.ma/produits/spot-cuisine-led', '{\"categorie\":\"Spots\",\"finition\":\"chrome satine\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 12, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(55, 'Spot salle de bain IP65', 'spot-salle-de-bain-ip65', 'Spot salle de bain IP65 est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 5 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot salle de bain IP65 est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Model', 139.00, 159.00, 18, 'inactive', '/catalog-import/pdf-products/product-055.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-055.jpg\",\"\\/catalog-import\\/pdf-products\\/product-056.jpg\"]', '/catalog-import/pdf-products/product-055.jpg', 'https://solarlight.ma/produits/spot-salle-de-bain-ip65', '{\"categorie\":\"Spots\",\"finition\":\"blanc sable\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 13, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(56, 'Spot rail moderne', 'spot-rail-moderne', 'Spot rail moderne est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 6 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot rail moderne est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 6 de', 179.00, 219.00, 21, 'inactive', '/catalog-import/pdf-products/product-056.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-056.jpg\",\"\\/catalog-import\\/pdf-products\\/product-057.jpg\"]', '/catalog-import/pdf-products/product-056.jpg', 'https://solarlight.ma/produits/spot-rail-moderne', '{\"categorie\":\"Spots\",\"finition\":\"noir mat\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 14, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(57, 'Spot mini encastre', 'spot-mini-encastre', 'Spot mini encastre est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 7 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot mini encastre est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 7 d', 79.00, 99.00, 24, 'inactive', '/catalog-import/pdf-products/product-057.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-057.jpg\",\"\\/catalog-import\\/pdf-products\\/product-058.jpg\"]', '/catalog-import/pdf-products/product-057.jpg', 'https://solarlight.ma/produits/spot-mini-encastre', '{\"categorie\":\"Spots\",\"finition\":\"laiton brosse\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 15, '2026-05-04 14:48:46', '2026-05-22 20:02:00');
INSERT INTO `produits` (`id_produit`, `nom`, `slug`, `description`, `short_description`, `prix`, `old_price`, `stock`, `status`, `image_url`, `gallery_images`, `image`, `product_url`, `specifications`, `id_categorie`, `created_at`, `updated_at`) VALUES
(58, 'Spot duo directionnel', 'spot-duo-directionnel', 'Spot duo directionnel est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 8 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot duo directionnel est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele', 189.00, 229.00, 8, 'inactive', '/catalog-import/pdf-products/product-058.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-058.jpg\",\"\\/catalog-import\\/pdf-products\\/product-059.jpg\"]', '/catalog-import/pdf-products/product-058.jpg', 'https://solarlight.ma/produits/spot-duo-directionnel', '{\"categorie\":\"Spots\",\"finition\":\"verre opalin\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(59, 'Spot salon premium', 'spot-salon-premium', 'Spot salon premium est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 9 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot salon premium est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 9 d', 159.00, 189.00, 11, 'inactive', '/catalog-import/pdf-products/product-059.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-059.jpg\",\"\\/catalog-import\\/pdf-products\\/product-060.jpg\"]', '/catalog-import/pdf-products/product-059.jpg', 'https://solarlight.ma/produits/spot-salon-premium', '{\"categorie\":\"Spots\",\"finition\":\"chrome satine\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:46', '2026-05-22 20:02:00'),
(60, 'Spot basse consommation', 'spot-basse-consommation', 'Spot basse consommation est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Modele 10 de notre collection avec installation simple et rendu lumineux confortable au quotidien.', 'Spot basse consommation est un Spots soigneusement selectionne pour apporter une lumiere elegante, une finition premium et un style moderne a vos espaces. Model', 95.00, 115.00, 14, 'inactive', '/catalog-import/pdf-products/product-060.jpg', '[\"\\/catalog-import\\/pdf-products\\/product-060.jpg\",\"\\/catalog-import\\/pdf-products\\/product-061.jpg\"]', '/catalog-import/pdf-products/product-060.jpg', 'https://solarlight.ma/produits/spot-basse-consommation', '{\"categorie\":\"Spots\",\"finition\":\"blanc sable\",\"usage\":\"plafond et circulation\",\"garantie\":\"2 ans\"}', 7, '2026-05-04 14:48:46', '2026-05-22 20:02:00');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('EfiYcw5jY8gbb3WYZB7ALH2eEiSfpQwque2DFapp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWDhjUFFOZTdDMlNwSUljQWFlbUtJYk5CWnExeDJBVHN3N1pTY0U4ZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777388958),
('MBWnXPkaSFzGcdLxVQu2ctl2HYMRyDvBz4jyBdg8', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicVJXM1RXNWJXaTBENmFTMDZyTGlzbkRCVzBlc0VKaTRmUFJXd3Y3ciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777314148);

-- --------------------------------------------------------

--
-- Structure de la table `site_reviews`
--

CREATE TABLE `site_reviews` (
  `id_site_review` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `review_date` date DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `site_reviews`
--

INSERT INTO `site_reviews` (`id_site_review`, `customer_name`, `email`, `rating`, `comment`, `review_date`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 'Nadia B.', NULL, 5, 'Tres belle experience. Les lampes sont elegantes, faciles a installer et rendent super bien sur la terrasse.', '2026-04-22', 1, '2026-05-04 15:44:40', '2026-05-04 15:44:40'),
(2, 'Youssef A.', NULL, 4, 'Livraison rapide et produits conformes aux photos. J aime beaucoup la finition et l ambiance lumineuse.', '2026-04-09', 1, '2026-05-04 15:44:40', '2026-05-04 15:44:40'),
(3, 'Salma E.', NULL, 5, 'Une boutique propre, moderne et rassurante. Le service client a ete reactif du debut a la fin.', '2026-03-27', 1, '2026-05-04 15:44:40', '2026-05-04 15:44:40');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id_avis`),
  ADD KEY `avis_id_client_foreign` (`id_client`),
  ADD KEY `avis_id_produit_foreign` (`id_produit`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categorie`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id_client`),
  ADD UNIQUE KEY `clients_email_unique` (`email`),
  ADD UNIQUE KEY `clients_api_token_unique` (`api_token`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `commandes_id_client_foreign` (`id_client`);

--
-- Index pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id_contact_message`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ligne_commandes`
--
ALTER TABLE `ligne_commandes`
  ADD PRIMARY KEY (`id_ligne_commande`),
  ADD KEY `ligne_commandes_id_commande_foreign` (`id_commande`),
  ADD KEY `ligne_commandes_id_produit_foreign` (`id_produit`);

--
-- Index pour la table `ligne_paniers`
--
ALTER TABLE `ligne_paniers`
  ADD PRIMARY KEY (`id_ligne_panier`),
  ADD KEY `ligne_paniers_id_panier_foreign` (`id_panier`),
  ADD KEY `ligne_paniers_id_produit_foreign` (`id_produit`);

--
-- Index pour la table `livraisons`
--
ALTER TABLE `livraisons`
  ADD PRIMARY KEY (`id_livraison`),
  ADD KEY `livraisons_id_commande_foreign` (`id_commande`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id_paiement`),
  ADD UNIQUE KEY `paiements_reference_externe_unique` (`reference_externe`),
  ADD KEY `paiements_id_commande_foreign` (`id_commande`),
  ADD KEY `paiements_id_client_foreign` (`id_client`);

--
-- Index pour la table `paniers`
--
ALTER TABLE `paniers`
  ADD PRIMARY KEY (`id_panier`),
  ADD KEY `paniers_id_client_foreign` (`id_client`);

--
-- Index pour la table `password_reset_codes`
--
ALTER TABLE `password_reset_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_reset_codes_email_index` (`email`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `pending_checkouts`
--
ALTER TABLE `pending_checkouts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pending_checkouts_stripe_session_id_unique` (`stripe_session_id`),
  ADD KEY `pending_checkouts_id_client_foreign` (`id_client`),
  ADD KEY `pending_checkouts_gateway_session_id_index` (`gateway_session_id`),
  ADD KEY `pending_checkouts_gateway_reference_index` (`gateway_reference`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`),
  ADD UNIQUE KEY `produits_slug_unique` (`slug`),
  ADD UNIQUE KEY `produits_product_url_unique` (`product_url`),
  ADD KEY `produits_id_categorie_foreign` (`id_categorie`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `site_reviews`
--
ALTER TABLE `site_reviews`
  ADD PRIMARY KEY (`id_site_review`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id_avis` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categorie` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id_client` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id_contact_message` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ligne_commandes`
--
ALTER TABLE `ligne_commandes`
  MODIFY `id_ligne_commande` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `ligne_paniers`
--
ALTER TABLE `ligne_paniers`
  MODIFY `id_ligne_panier` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `livraisons`
--
ALTER TABLE `livraisons`
  MODIFY `id_livraison` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id_paiement` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `paniers`
--
ALTER TABLE `paniers`
  MODIFY `id_panier` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `password_reset_codes`
--
ALTER TABLE `password_reset_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pending_checkouts`
--
ALTER TABLE `pending_checkouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `site_reviews`
--
ALTER TABLE `site_reviews`
  MODIFY `id_site_review` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_id_client_foreign` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE,
  ADD CONSTRAINT `avis_id_produit_foreign` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_id_client_foreign` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ligne_commandes`
--
ALTER TABLE `ligne_commandes`
  ADD CONSTRAINT `ligne_commandes_id_commande_foreign` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `ligne_commandes_id_produit_foreign` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ligne_paniers`
--
ALTER TABLE `ligne_paniers`
  ADD CONSTRAINT `ligne_paniers_id_panier_foreign` FOREIGN KEY (`id_panier`) REFERENCES `paniers` (`id_panier`) ON DELETE CASCADE,
  ADD CONSTRAINT `ligne_paniers_id_produit_foreign` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `livraisons`
--
ALTER TABLE `livraisons`
  ADD CONSTRAINT `livraisons_id_commande_foreign` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_id_client_foreign` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE SET NULL,
  ADD CONSTRAINT `paiements_id_commande_foreign` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paniers`
--
ALTER TABLE `paniers`
  ADD CONSTRAINT `paniers_id_client_foreign` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE;

--
-- Contraintes pour la table `pending_checkouts`
--
ALTER TABLE `pending_checkouts`
  ADD CONSTRAINT `pending_checkouts_id_client_foreign` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_id_categorie_foreign` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
