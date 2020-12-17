-- MySQL dump 10.13  Distrib 8.0.18-9, for Linux (x86_64)
--
-- Host: localhost    Database: structure
-- ------------------------------------------------------
-- Server version	8.0.18-9

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!50717 SELECT COUNT(*) INTO @rocksdb_has_p_s_session_variables FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'performance_schema' AND TABLE_NAME = 'session_variables' */;
/*!50717 SET @rocksdb_get_is_supported = IF (@rocksdb_has_p_s_session_variables, 'SELECT COUNT(*) INTO @rocksdb_is_supported FROM performance_schema.session_variables WHERE VARIABLE_NAME=\'rocksdb_bulk_load\'', 'SELECT 0') */;
/*!50717 PREPARE s FROM @rocksdb_get_is_supported */;
/*!50717 EXECUTE s */;
/*!50717 DEALLOCATE PREPARE s */;
/*!50717 SET @rocksdb_enable_bulk_load = IF (@rocksdb_is_supported, 'SET SESSION rocksdb_bulk_load = 1', 'SET @rocksdb_dummy_bulk_load = 0') */;
/*!50717 PREPARE s FROM @rocksdb_enable_bulk_load */;
/*!50717 EXECUTE s */;
/*!50717 DEALLOCATE PREPARE s */;

--
-- Table structure for table `agriculture_system_type`
--

DROP TABLE IF EXISTS `agriculture_system_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agriculture_system_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `replaced_by_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_agriculture_system_type_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_agriculture_system_type_id_user` (`id_user`),
  CONSTRAINT `fkey_agriculture_system_type_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_agriculture_system_type_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `agriculture_system_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `animal_category`
--

DROP TABLE IF EXISTS `animal_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `animal_category` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_animal_species` int(4) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_animal_category_id_animal_species` (`id_animal_species`) USING BTREE,
  KEY `fkey_animal_category_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_animal_category_id_user` (`id_user`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_animal_category_id_animal_species` FOREIGN KEY (`id_animal_species`) REFERENCES `animal_species` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_animal_category_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_animal_category_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `animal_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `animal_species`
--

DROP TABLE IF EXISTS `animal_species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `animal_species` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `replaced_by_id` int(11) unsigned DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_animal_species_id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `animal_type`
--

DROP TABLE IF EXISTS `animal_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `animal_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_animal_category` int(11) unsigned NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `lactating` tinyint(1) DEFAULT NULL,
  `dairy` tinyint(1) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `id_gender` int(11) unsigned DEFAULT NULL,
  `weight_lower_limit` decimal(10,2) DEFAULT NULL,
  `weight_upper_limit` decimal(10,2) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `keep_private` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_animal_type_id_animal_category` (`id_animal_category`) USING BTREE,
  KEY `fkey_animal_type_id_gender` (`id_gender`),
  KEY `fkey_animal_type_id_user` (`id_user`),
  KEY `fkey_animal_type_replaced_by_id` (`replaced_by_id`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_animal_type_id_animal_category` FOREIGN KEY (`id_animal_category`) REFERENCES `animal_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_animal_type_id_gender` FOREIGN KEY (`id_gender`) REFERENCES `gender` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_animal_type_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_animal_type_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `animal_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_registration`
--

DROP TABLE IF EXISTS `app_registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_registration` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_first` varchar(255) DEFAULT NULL,
  `name_last` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id_organization_type` int(4) unsigned DEFAULT NULL,
  `random_identifier` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `version` decimal(5,2) DEFAULT NULL,
  `hard_drive_volume_serial` varchar(255) DEFAULT NULL,
  `concatenated_identifier` varchar(255) DEFAULT NULL,
  `app_path` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `organization_name` varchar(255) DEFAULT NULL,
  `id_country` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_app_registration_id_organization_type` (`id_organization_type`),
  KEY `fkey_app_registration_id_user` (`id_user`),
  KEY `fkey_app_registration_replaced_by_id` (`replaced_by_id`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_app_registration_id_organization_type` FOREIGN KEY (`id_organization_type`) REFERENCES `organization_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_app_registration_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_app_registration_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `app_registration` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `community_type`
--

DROP TABLE IF EXISTS `community_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_type` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `replaced_by_id` (`replaced_by_id`),
  KEY `fkey_community_replaced_by_id` (`replaced_by_id`) USING BTREE,
  KEY `fkey_community_type_id_user` (`id_user`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_community_type_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_community_type_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `community_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consolidation_audit`
--

DROP TABLE IF EXISTS `consolidation_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consolidation_audit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `table_name` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `row_id` int(10) unsigned NOT NULL,
  `old_value` varchar(255) NOT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_name` (`table_name`,`field_name`,`row_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coop_membership`
--

DROP TABLE IF EXISTS `coop_membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coop_membership` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `name_free_entry` text,
  `membership_count_male` int(11) DEFAULT NULL,
  `membership_count_female` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_coop_id_respondent` (`id_respondent`),
  KEY `fkey_coop_id_user` (`id_user`),
  KEY `fkey_coop_replaced_by_id` (`replaced_by_id`),
  CONSTRAINT `fkey_coop_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_coop_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_coop_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `coop_membership` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_commodity`
--

DROP TABLE IF EXISTS `core_commodity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `core_commodity` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_core_commodity_id_user` (`id_user`),
  KEY `fkey_core_commodity_replaced_by_id` (`replaced_by_id`),
  CONSTRAINT `fkey_core_commodity_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_core_commodity_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `core_commodity` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_context_attribute`
--

DROP TABLE IF EXISTS `core_context_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `core_context_attribute` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `prompt` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `id_core_context_attribute_type` int(4) unsigned DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `replaced_by_id` (`replaced_by_id`),
  KEY `fkey_core_content_attribute_id_core_context_attribute_type` (`id_core_context_attribute_type`),
  KEY `fkey_core_content_attribute_id_user` (`id_user`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_core_content_attribute_id_core_context_attribute_type` FOREIGN KEY (`id_core_context_attribute_type`) REFERENCES `core_context_attribute_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_core_content_attribute_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_core_context_attribute_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `core_context_attribute` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_context_attribute_score`
--

DROP TABLE IF EXISTS `core_context_attribute_score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `core_context_attribute_score` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_techfit_scale` int(4) DEFAULT NULL,
  `id_techfit_assessment` int(8) unsigned DEFAULT NULL,
  `id_core_context_attribute` int(8) unsigned DEFAULT NULL,
  `id_core_context_attribute_score_calc_method` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_core_context_attribute_score_id_score_calc_method` (`id_core_context_attribute_score_calc_method`),
  KEY `fkey_core_context_attribute_score_id_techfit_assessment` (`id_techfit_assessment`),
  KEY `fkey_core_context_attribute_score_id_techfit_scale` (`id_techfit_scale`),
  KEY `fkey_core_context_attribute_score_id_user` (`id_user`),
  KEY `fkey_core_context_attribute_score_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_core_context_attribute_score_id_core_context_attribute` (`id_core_context_attribute`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_core_context_attribute_score_id_core_context_attribute` FOREIGN KEY (`id_core_context_attribute`) REFERENCES `core_context_attribute` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_core_context_attribute_score_id_techfit_assessment` FOREIGN KEY (`id_techfit_assessment`) REFERENCES `techfit_assessment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_core_context_attribute_score_id_techfit_scale` FOREIGN KEY (`id_techfit_scale`) REFERENCES `techfit_scale` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_core_context_attribute_score_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_core_context_attribute_score_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `core_context_attribute_score` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2201 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_context_attribute_score_calc_method`
--

DROP TABLE IF EXISTS `core_context_attribute_score_calc_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `core_context_attribute_score_calc_method` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_core_context_attribute_score_calc_method_replaced_by_id` (`replaced_by_id`) USING BTREE,
  KEY `fkey_core_context_attribute_score_calc_method_id_user` (`id_user`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_core_context_attribute_score_calc_method_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_core_context_attribute_score_calc_method_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `core_context_attribute_score_calc_method` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_context_attribute_type`
--

DROP TABLE IF EXISTS `core_context_attribute_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `core_context_attribute_type` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `id_currency` int(255) unsigned NOT NULL,
  `id_world_region` int(255) unsigned NOT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_country_id_currency` (`id_currency`),
  KEY `fkey_country_id_world_region` (`id_world_region`),
  CONSTRAINT `fkey_country_id_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_country_id_world_region` FOREIGN KEY (`id_world_region`) REFERENCES `world_region` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crop_cultivation`
--

DROP TABLE IF EXISTS `crop_cultivation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_cultivation` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `id_crop_type` int(8) unsigned NOT NULL,
  `cultivated_land` decimal(10,2) DEFAULT NULL,
  `id_unit_area` int(8) unsigned NOT NULL,
  `annual_yield` decimal(10,2) DEFAULT NULL,
  `id_unit_mass_weight` int(8) unsigned NOT NULL,
  `percent_fed` int(4) DEFAULT NULL,
  `percent_burned` int(4) DEFAULT NULL,
  `percent_mulched` int(4) DEFAULT NULL,
  `percent_sold` int(4) DEFAULT NULL,
  `percent_other` int(4) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_crop_cultivation_id_respondent` (`id_respondent`),
  KEY `fkey_crop_cultivation_id_user` (`id_user`),
  KEY `fkey_crop_cultivation_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_crop_cultivation_id_unit_mass_weight` (`id_unit_mass_weight`),
  KEY `fkey_crop_cultivation_id_unit_area` (`id_unit_area`),
  KEY `fkey_crop_cultiavtion_id_crop_type` (`id_crop_type`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_crop_cultiavtion_id_crop_type` FOREIGN KEY (`id_crop_type`) REFERENCES `crop_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_crop_cultivation_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_crop_cultivation_id_unit_area` FOREIGN KEY (`id_unit_area`) REFERENCES `unit_area` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_crop_cultivation_id_unit_mass_weight` FOREIGN KEY (`id_unit_mass_weight`) REFERENCES `unit_mass_weight` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_crop_cultivation_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_crop_cultivation_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `crop_cultivation` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4057 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `crop_residue_stats`
--

DROP TABLE IF EXISTS `crop_residue_stats`;
/*!50001 DROP VIEW IF EXISTS `crop_residue_stats`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `crop_residue_stats` AS SELECT 
 1 AS `crop_id`,
 1 AS `respondent_id`,
 1 AS `crop_residue_dm`,
 1 AS `ratio_me`,
 1 AS `ratio_cp`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `crop_type`
--

DROP TABLE IF EXISTS `crop_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_type` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `harvest_index` decimal(10,7) DEFAULT NULL,
  `content_percent_dry_matter` decimal(10,2) DEFAULT NULL,
  `content_metabolisable_energy` decimal(10,6) DEFAULT NULL,
  `content_crude_protein` decimal(10,6) DEFAULT NULL,
  `user_citation` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT '0',
  `updated_at` datetime(6) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_crop_type_id_user` (`id_user`),
  KEY `fkey_crop_type_replaced_by_id` (`replaced_by_id`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_crop_type_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_crop_type_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `crop_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `cultivated_fodder_stats`
--

DROP TABLE IF EXISTS `cultivated_fodder_stats`;
/*!50001 DROP VIEW IF EXISTS `cultivated_fodder_stats`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `cultivated_fodder_stats` AS SELECT 
 1 AS `cultivated_fodder_id`,
 1 AS `respondent_id`,
 1 AS `cultivated_fodder_dm`,
 1 AS `ratio_me`,
 1 AS `ratio_cp`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currency` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `default_usd_exchange_rate` decimal(10,5) NOT NULL,
  `abbreviation` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `decision`
--

DROP TABLE IF EXISTS `decision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `decision` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `id_decision_type` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_decision_id_decision_type` (`id_decision_type`),
  CONSTRAINT `fkey_decision_id_decision_type` FOREIGN KEY (`id_decision_type`) REFERENCES `decision_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `decision_making_by_household`
--

DROP TABLE IF EXISTS `decision_making_by_household`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `decision_making_by_household` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `id_decision` int(10) unsigned DEFAULT NULL,
  `id_gender_group` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_dmbh_id_decision` (`id_decision`),
  KEY `fkey_dmbh_id_gender_group` (`id_gender_group`),
  KEY `fkey_dmbh_id_respondent` (`id_respondent`),
  KEY `fkey_dmbh_id_user` (`id_user`),
  KEY `fkey_dmbh_replaced_by_id` (`replaced_by_id`),
  CONSTRAINT `fkey_dmbh_id_decision` FOREIGN KEY (`id_decision`) REFERENCES `decision` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_dmbh_id_gender_group` FOREIGN KEY (`id_gender_group`) REFERENCES `gender_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_dmbh_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_dmbh_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_dmbh_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `decision_making_by_household` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `decision_type`
--

DROP TABLE IF EXISTS `decision_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `decision_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `export_coop_membership`
--

DROP TABLE IF EXISTS `export_coop_membership`;
/*!50001 DROP VIEW IF EXISTS `export_coop_membership`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_coop_membership` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `coop_membership_id`,
 1 AS `coop_membership_name_free_entry`,
 1 AS `coop_membership_membership_count_male`,
 1 AS `coop_membership_membership_count_female`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_core_context_attribute_score`
--

DROP TABLE IF EXISTS `export_core_context_attribute_score`;
/*!50001 DROP VIEW IF EXISTS `export_core_context_attribute_score`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_core_context_attribute_score` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_count`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `techfit_scale_number`,
 1 AS `core_context_attribute_score_id`,
 1 AS `core_context_attribute_id`,
 1 AS `core_context_attribute_prompt`,
 1 AS `core_context_attribute_type_id`,
 1 AS `core_context_attribute_type_description`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_crop_cultivation`
--

DROP TABLE IF EXISTS `export_crop_cultivation`;
/*!50001 DROP VIEW IF EXISTS `export_crop_cultivation`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_crop_cultivation` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `crop_cultivation_id`,
 1 AS `crop_type_id`,
 1 AS `crop_type_name`,
 1 AS `crop_type_harvest_index`,
 1 AS `crop_type_content_percent_dry_matter`,
 1 AS `crop_type_content_metabolisable_energy`,
 1 AS `crop_type_content_crude_protein`,
 1 AS `unit_area_id`,
 1 AS `unit_area_name`,
 1 AS `unit_area_conversion_ha`,
 1 AS `unit_mass_weight_id`,
 1 AS `unit_mass_weight_name`,
 1 AS `unit_mass_weight_conversion_kg`,
 1 AS `crop_cultivation_cultivated_land`,
 1 AS `crop_cultivation_cultivated_land_ha`,
 1 AS `crop_cultivation_annual_yield`,
 1 AS `crop_cultivation_percent_fed`,
 1 AS `crop_cultivation_percent_burned`,
 1 AS `crop_cultivation_percent_mulched`,
 1 AS `crop_cultivation_percent_sold`,
 1 AS `crop_cultivation_percent_other`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_decision_making_by_household`
--

DROP TABLE IF EXISTS `export_decision_making_by_household`;
/*!50001 DROP VIEW IF EXISTS `export_decision_making_by_household`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_decision_making_by_household` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `decision_making_by_household_id`,
 1 AS `decision_id`,
 1 AS `decision_description`,
 1 AS `decision_type_id`,
 1 AS `decision_type_description`,
 1 AS `gender_group_description`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_feed_labor_division`
--

DROP TABLE IF EXISTS `export_feed_labor_division`;
/*!50001 DROP VIEW IF EXISTS `export_feed_labor_division`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_feed_labor_division` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `feed_labor_division_id`,
 1 AS `feed_labor_type_id`,
 1 AS `feed_labor_type_description`,
 1 AS `labor_division_group_description`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_feed_source_availability`
--

DROP TABLE IF EXISTS `export_feed_source_availability`;
/*!50001 DROP VIEW IF EXISTS `export_feed_source_availability`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_feed_source_availability` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `feed_source_availability_id`,
 1 AS `feed_availability`,
 1 AS `feed_source_id`,
 1 AS `feed_source_description`,
 1 AS `month_id`,
 1 AS `month_name`,
 1 AS `percentage`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_focus_group`
--

DROP TABLE IF EXISTS `export_focus_group`;
/*!50001 DROP VIEW IF EXISTS `export_focus_group`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_focus_group` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `focus_group_threshold_large_farm_ha`,
 1 AS `focus_group_threshold_small_farm_ha`,
 1 AS `focus_group_percent_households_landless`,
 1 AS `focus_group_percent_households_small`,
 1 AS `focus_group_percent_households_medium`,
 1 AS `focus_group_percent_households_large`,
 1 AS `focus_group_percent_credit_formal`,
 1 AS `focus_group_percent_credit_informal`,
 1 AS `focus_group_household_percent_migrating`,
 1 AS `focus_group_percent_reproduction_bull`,
 1 AS `focus_group_percent_reproduction_ai`,
 1 AS `focus_group_percent_processing_female`,
 1 AS `focus_group_percent_processing_male`,
 1 AS `focus_group_percent_processing_overall`,
 1 AS `focus_group_market_avg_distance_km`,
 1 AS `focus_group_market_avg_cost_travel`,
 1 AS `focus_group_partner_organization`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_focus_group_monthly_statistics`
--

DROP TABLE IF EXISTS `export_focus_group_monthly_statistics`;
/*!50001 DROP VIEW IF EXISTS `export_focus_group_monthly_statistics`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_focus_group_monthly_statistics` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `focus_group_monthly_statistics_id`,
 1 AS `month_id`,
 1 AS `month_name`,
 1 AS `season_id`,
 1 AS `season_name`,
 1 AS `rainfall`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_fodder_crop_cultivation`
--

DROP TABLE IF EXISTS `export_fodder_crop_cultivation`;
/*!50001 DROP VIEW IF EXISTS `export_fodder_crop_cultivation`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_fodder_crop_cultivation` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `currency_id`,
 1 AS `currency_name`,
 1 AS `currency_default_usd_exchange_rate`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `fodder_crop_cultivation_id`,
 1 AS `fodder_crop_type_id`,
 1 AS `fodder_crop_type_name`,
 1 AS `fodder_crop_type_annual_dry_matter_per_hectare`,
 1 AS `fodder_crop_type_content_metabolisable_energy`,
 1 AS `fodder_crop_type_content_crude_protein`,
 1 AS `unit_area_id`,
 1 AS `unit_area_name`,
 1 AS `unit_area_conversion_ha`,
 1 AS `fodder_crop_cultivation_cultivated_land`,
 1 AS `fodder_crop_cultivation_cultiavted_land_ha`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_income_activity`
--

DROP TABLE IF EXISTS `export_income_activity`;
/*!50001 DROP VIEW IF EXISTS `export_income_activity`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_income_activity` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `income_activity_id`,
 1 AS `income_activity_type_id`,
 1 AS `income_activity_type_description`,
 1 AS `income_activity_category_id`,
 1 AS `income_activity_category_description`,
 1 AS `income_activity_percent_of_hh_income`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_labour_activity`
--

DROP TABLE IF EXISTS `export_labour_activity`;
/*!50001 DROP VIEW IF EXISTS `export_labour_activity`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_labour_activity` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `currency_id`,
 1 AS `currency_name`,
 1 AS `currency_default_usd_exchange_rate`,
 1 AS `labour_activity_id`,
 1 AS `labour_activity_description`,
 1 AS `labour_activity_daily_rate_female`,
 1 AS `labour_activity_daily_rate_male`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_livestock_holding`
--

DROP TABLE IF EXISTS `export_livestock_holding`;
/*!50001 DROP VIEW IF EXISTS `export_livestock_holding`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_livestock_holding` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `livestock_holding_id`,
 1 AS `animal_type_id`,
 1 AS `animal_type_description`,
 1 AS `animal_category_id`,
 1 AS `animal_category_description`,
 1 AS `animal_species_id`,
 1 AS `animal_species_description`,
 1 AS `animal_type_lactating`,
 1 AS `animal_type_dairy`,
 1 AS `animal_gender_id`,
 1 AS `animal_gender_description`,
 1 AS `livestock_holding_dominant_breed`,
 1 AS `livestock_holding_average_weight`,
 1 AS `livestock_holding_headcount`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_livestock_sale`
--

DROP TABLE IF EXISTS `export_livestock_sale`;
/*!50001 DROP VIEW IF EXISTS `export_livestock_sale`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_livestock_sale` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `currency_id`,
 1 AS `currency_name`,
 1 AS `currency_default_usd_exchange_rate`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `livestock_sale_id`,
 1 AS `livestock_sale_category_id`,
 1 AS `animal_species_id`,
 1 AS `animal_species_description`,
 1 AS `animal_gender_id`,
 1 AS `animal_gender_description`,
 1 AS `livestock_sale_number_sold`,
 1 AS `livestock_sale_approximate_weight`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_project_site`
--

DROP TABLE IF EXISTS `export_project_site`;
/*!50001 DROP VIEW IF EXISTS `export_project_site`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_project_site` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `project_description`,
 1 AS `project_partner_organization`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_major_region`,
 1 AS `site_world_region`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country_name`,
 1 AS `site_community_type`,
 1 AS `site_grazing_metabolisable_energy`,
 1 AS `site_grazing_crude_protein_percentage`,
 1 AS `site_collected_fodder_metabolisable_energy`,
 1 AS `site_collected_fodder_crude_protein_percentage`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_purchased_feed`
--

DROP TABLE IF EXISTS `export_purchased_feed`;
/*!50001 DROP VIEW IF EXISTS `export_purchased_feed`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_purchased_feed` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `currency_id`,
 1 AS `currency_name`,
 1 AS `currency_default_usd_exchange_rate`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `purchased_feed_id`,
 1 AS `purchased_feed_type_id`,
 1 AS `purchased_feed_type_name`,
 1 AS `purchased_feed_type_content_percent_dry_matter`,
 1 AS `purchased_feed_type_content_metabolisable_energy`,
 1 AS `purchased_feed_type_content_crude_protein`,
 1 AS `unit_mass_weight_id`,
 1 AS `unit_mass_weight_name`,
 1 AS `unit_mass_weight_conversion_kg`,
 1 AS `purchased_feed_purchases_per_year`,
 1 AS `purchased_feed_currency_id`,
 1 AS `purchased_feed_currency_name`,
 1 AS `purchased_feed_currency_default_usd_exchange_rate`,
 1 AS `purchased_feed_quantity_purchased`,
 1 AS `purchased_feed_unit_price`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_respondent`
--

DROP TABLE IF EXISTS `export_respondent`;
/*!50001 DROP VIEW IF EXISTS `export_respondent`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_respondent` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `currency_id`,
 1 AS `currency_name`,
 1 AS `currency_default_usd_exchange_rate`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_interview_date`,
 1 AS `respondent_age`,
 1 AS `respondent_gender_id`,
 1 AS `respondent_gender_description`,
 1 AS `respondent_head_of_household_is_respondent`,
 1 AS `respondent_head_of_household_age`,
 1 AS `respondent_head_of_household_occupation`,
 1 AS `respondent_organization_affiliation`,
 1 AS `respondent_community`,
 1 AS `community_type_id`,
 1 AS `community_type_description`,
 1 AS `respondent_country_id`,
 1 AS `respondent_country_name`,
 1 AS `landholding_category_id`,
 1 AS `landholding_category_description`,
 1 AS `unit_area_id`,
 1 AS `unit_area_name`,
 1 AS `respondent_land_under_cultivation`,
 1 AS `respondent_diet_percent_collected_fodder`,
 1 AS `respondent_diet_percent_grazing`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_respondent_monthly_statistics`
--

DROP TABLE IF EXISTS `export_respondent_monthly_statistics`;
/*!50001 DROP VIEW IF EXISTS `export_respondent_monthly_statistics`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_respondent_monthly_statistics` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `currency_id`,
 1 AS `currency_name`,
 1 AS `currency_default_usd_exchange_rate`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `respondent_monthly_statistics_id`,
 1 AS `month_id`,
 1 AS `month_name`,
 1 AS `respondent_monthly_statistics_milk_average_yield`,
 1 AS `respondent_monthly_statistics_milk_average_price_litre`,
 1 AS `respondent_monthly_statistics_milk_retained_for_household`,
 1 AS `respondent_monthly_statistics_market_price_cattle`,
 1 AS `respondent_monthly_statistics_market_price_sheep`,
 1 AS `respondent_monthly_statistics_market_price_goat`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `export_womens_income_activity`
--

DROP TABLE IF EXISTS `export_womens_income_activity`;
/*!50001 DROP VIEW IF EXISTS `export_womens_income_activity`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `export_womens_income_activity` AS SELECT 
 1 AS `export_time`,
 1 AS `user_id`,
 1 AS `uploaded_at`,
 1 AS `private`,
 1 AS `excluded`,
 1 AS `project_id`,
 1 AS `project_title`,
 1 AS `site_id`,
 1 AS `site_name`,
 1 AS `site_world_region_id`,
 1 AS `site_world_region_name`,
 1 AS `site_country_id`,
 1 AS `site_country`,
 1 AS `focus_group_id`,
 1 AS `respondent_id`,
 1 AS `respondent_head_of_household_gender_id`,
 1 AS `respondent_head_of_household_gender`,
 1 AS `focus_group_meeting_date_time`,
 1 AS `focus_group_community`,
 1 AS `focus_group_households`,
 1 AS `focus_group_households_average_members`,
 1 AS `womens_income_activity_id`,
 1 AS `income_activity_type_id`,
 1 AS `income_activity_type_description`,
 1 AS `income_activity_category_id`,
 1 AS `income_activity_category_description`,
 1 AS `womens_income_activity_pct_womens_income`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `feed_labor_division`
--

DROP TABLE IF EXISTS `feed_labor_division`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feed_labor_division` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_feed_labor_type` int(10) unsigned DEFAULT NULL,
  `id_labor_division_group` int(10) unsigned DEFAULT NULL,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_fld_id_feed_labor_type` (`id_feed_labor_type`),
  KEY `fkey_fld_id_labor_division_group` (`id_labor_division_group`),
  KEY `fkey_fld_id_respondent` (`id_respondent`),
  KEY `fkey_fld_id_user` (`id_user`),
  KEY `fkey_fld_replaced_by_id` (`replaced_by_id`),
  CONSTRAINT `fkey_fld_id_feed_labor_type` FOREIGN KEY (`id_feed_labor_type`) REFERENCES `feed_labor_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_fld_id_labor_division_group` FOREIGN KEY (`id_labor_division_group`) REFERENCES `labor_division_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_fld_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_fld_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_fld_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `feed_labor_division` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feed_labor_type`
--

DROP TABLE IF EXISTS `feed_labor_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feed_labor_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feed_source`
--

DROP TABLE IF EXISTS `feed_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feed_source` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `id_site` int(8) unsigned DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `list_order` int(4) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_feed_source_id_site` (`id_site`),
  KEY `fkey_feed_source_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_feed_source_id_user` (`id_user`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_feed_source_id_site` FOREIGN KEY (`id_site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_feed_source_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_feed_source_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `feed_source` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feed_source_availability`
--

DROP TABLE IF EXISTS `feed_source_availability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feed_source_availability` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_feed_source` int(4) unsigned DEFAULT NULL,
  `id_month` int(4) DEFAULT NULL,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `contribution` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_feed_source_availability_id_user` (`id_user`),
  KEY `fkey_feed_source_availability_id_respondent` (`id_respondent`),
  KEY `fkey_feed_source_availability_id_month` (`id_month`),
  KEY `fkey_feed_source_availability_replaced_by_id` (`replaced_by_id`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_feed_source_availability_id_month` FOREIGN KEY (`id_month`) REFERENCES `month` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_feed_source_availability_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_feed_source_availability_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_feed_source_availability_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `feed_source_availability` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=162410 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `feed_source_values_all`
--

DROP TABLE IF EXISTS `feed_source_values_all`;
/*!50001 DROP VIEW IF EXISTS `feed_source_values_all`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `feed_source_values_all` AS SELECT 
 1 AS `month_order`,
 1 AS `name_of_month`,
 1 AS `order_of_month`,
 1 AS `resource_type`,
 1 AS `numerical_value_raw`,
 1 AS `focus_group_id`,
 1 AS `site_id`,
 1 AS `country_id`,
 1 AS `project_id`,
 1 AS `world_region_id`,
 1 AS `user_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `feed_source_values_user`
--

DROP TABLE IF EXISTS `feed_source_values_user`;
/*!50001 DROP VIEW IF EXISTS `feed_source_values_user`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `feed_source_values_user` AS SELECT 
 1 AS `month_order`,
 1 AS `name_of_month`,
 1 AS `order_of_month`,
 1 AS `resource_type`,
 1 AS `numerical_value_raw`,
 1 AS `focus_group_id`,
 1 AS `site_id`,
 1 AS `country_id`,
 1 AS `project_id`,
 1 AS `world_region_id`,
 1 AS `user_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `fg_feed_availability`
--

DROP TABLE IF EXISTS `fg_feed_availability`;
/*!50001 DROP VIEW IF EXISTS `fg_feed_availability`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `fg_feed_availability` AS SELECT 
 1 AS `id_focus_group`,
 1 AS `id_site`,
 1 AS `order_of_month`,
 1 AS `resource_type`,
 1 AS `numerical_value_raw`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `focus_group`
--

DROP TABLE IF EXISTS `focus_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `focus_group` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_site` int(8) unsigned DEFAULT NULL,
  `meeting_date_time` datetime(6) DEFAULT NULL,
  `participant_count_male` int(4) DEFAULT NULL,
  `participant_count_female` int(4) DEFAULT NULL,
  `venue_name` varchar(255) DEFAULT NULL,
  `community_type` varchar(255) DEFAULT NULL,
  `gps_latitude_degrees` int(4) DEFAULT NULL,
  `gps_longitude_degrees` int(4) DEFAULT NULL,
  `threshold_large_farm_ha` decimal(10,2) DEFAULT NULL,
  `threshold_small_farm_ha` decimal(10,2) DEFAULT NULL,
  `percent_households_landless` int(4) DEFAULT NULL,
  `percent_households_small` int(4) DEFAULT NULL,
  `percent_households_medium` int(4) DEFAULT NULL,
  `percent_households_large` int(4) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `major_region` varchar(255) DEFAULT NULL,
  `minor_region` varchar(255) DEFAULT NULL,
  `sub_region` varchar(255) DEFAULT NULL,
  `community` varchar(255) DEFAULT NULL,
  `id_community_type` int(4) unsigned DEFAULT NULL,
  `partner_organization` varchar(255) DEFAULT NULL,
  `other_attendees` varchar(255) DEFAULT NULL,
  `id_unit_area` int(4) unsigned DEFAULT NULL,
  `households_total` int(4) DEFAULT NULL,
  `households_average_members` int(4) DEFAULT NULL,
  `household_percent_migrating` int(4) DEFAULT NULL,
  `percent_credit_informal` int(4) DEFAULT NULL,
  `percent_credit_formal` int(4) DEFAULT NULL,
  `percent_reproduction_ai` int(4) DEFAULT NULL,
  `percent_reproduction_bull` int(4) DEFAULT NULL,
  `percent_processing_male` int(4) DEFAULT NULL,
  `percent_processing_female` int(4) DEFAULT NULL,
  `percent_processing_overall` int(4) DEFAULT NULL,
  `market_avg_distance_km` int(4) DEFAULT NULL,
  `market_avg_cost_travel` decimal(10,2) DEFAULT NULL,
  `gps_latitude_minutes` int(4) DEFAULT NULL,
  `gps_longitude_minutes` int(4) DEFAULT NULL,
  `gps_latitude_seconds` int(4) DEFAULT NULL,
  `gps_longitude_seconds` int(4) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  `id_gender` int(10) unsigned DEFAULT NULL,
  `percent_hh_landless_women` int(11) DEFAULT NULL,
  `percent_hh_small_women` int(11) DEFAULT NULL,
  `percent_hh_medium_women` int(11) DEFAULT NULL,
  `percent_hh_large_women` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_focus_group_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_focus_group_id_user` (`id_user`),
  KEY `fkey_focus_group_id_unit_area` (`id_unit_area`),
  KEY `fkey_focus_group_id_community_type` (`id_community_type`),
  KEY `fkey_focus_group_id_site` (`id_site`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  KEY `fkey_focus_group_id_gender` (`id_gender`),
  CONSTRAINT `fkey_focus_group_id_community_type` FOREIGN KEY (`id_community_type`) REFERENCES `community_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_focus_group_id_gender` FOREIGN KEY (`id_gender`) REFERENCES `gender` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_focus_group_id_site` FOREIGN KEY (`id_site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_focus_group_id_unit_area` FOREIGN KEY (`id_unit_area`) REFERENCES `unit_area` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_focus_group_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_focus_group_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `focus_group` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `focus_group_monthly_statistics`
--

DROP TABLE IF EXISTS `focus_group_monthly_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `focus_group_monthly_statistics` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_focus_group` int(8) unsigned DEFAULT NULL,
  `id_month` int(4) DEFAULT NULL,
  `id_season` int(8) unsigned DEFAULT NULL,
  `id_scale_zero_five` int(4) unsigned DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(4) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_focus_group_monthly_statistics_id_user` (`id_user`),
  KEY `fkey_focus_group_monthly_statistics_id_month` (`id_month`),
  KEY `fkey_focus_group_id_season` (`id_season`),
  KEY `fkey_focus_group_id_scale_zero_five` (`id_scale_zero_five`),
  KEY `fkey_focus_group_monthly_statistics_replaced_by_id` (`replaced_by_id`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_focus_group_id_scale_zero_five` FOREIGN KEY (`id_scale_zero_five`) REFERENCES `scale_zero_five` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_focus_group_id_season` FOREIGN KEY (`id_season`) REFERENCES `season` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_focus_group_monthly_statistics_id_month` FOREIGN KEY (`id_month`) REFERENCES `month` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_focus_group_monthly_statistics_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_focus_group_monthly_statistics_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `focus_group_monthly_statistics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2629 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fodder_crop_cultivation`
--

DROP TABLE IF EXISTS `fodder_crop_cultivation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fodder_crop_cultivation` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `id_fodder_crop_type` int(8) unsigned DEFAULT NULL,
  `cultivated_land` decimal(10,2) DEFAULT NULL,
  `id_unit_area` int(4) unsigned DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_fodder_crop_cultivation_id_user` (`id_user`),
  KEY `fkey_fodder_crop_cultivation_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_fodder_crop_cultivation_id_unit_area` (`id_unit_area`),
  KEY `fkey_fodder_crop_cultivation_id_fodder_crop_type` (`id_fodder_crop_type`),
  KEY `fkey_fodder_crop_cultivation_id_respondent` (`id_respondent`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_fodder_crop_cultivation_id_fodder_crop_type` FOREIGN KEY (`id_fodder_crop_type`) REFERENCES `fodder_crop_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_fodder_crop_cultivation_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_fodder_crop_cultivation_id_unit_area` FOREIGN KEY (`id_unit_area`) REFERENCES `unit_area` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_fodder_crop_cultivation_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_fodder_crop_cultivation_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `fodder_crop_cultivation` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=928 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fodder_crop_type`
--

DROP TABLE IF EXISTS `fodder_crop_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fodder_crop_type` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `annual_dry_matter_per_hectare` decimal(10,2) DEFAULT NULL,
  `content_metabolisable_energy` decimal(10,6) DEFAULT NULL,
  `content_crude_protein` decimal(10,2) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `user_citation` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(4) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_fodder_crop_type_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_fodder_crop_type_id_user` (`id_user`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_fodder_crop_type_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_fodder_crop_type_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `fodder_crop_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gender`
--

DROP TABLE IF EXISTS `gender`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gender` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gender_group`
--

DROP TABLE IF EXISTS `gender_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gender_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `income_activity`
--

DROP TABLE IF EXISTS `income_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `income_activity` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `id_income_activity_type` int(8) unsigned DEFAULT NULL,
  `percent_of_hh_income` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  `id_gender_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_income_activity_id_respondent` (`id_respondent`),
  KEY `fkey_income_activity_id_income_activity_type` (`id_income_activity_type`),
  KEY `fkey_income_activity_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_income_activity_id_user` (`id_user`),
  KEY `private_idx` (`keep_private`),
  KEY `exclude_idx` (`exclude`),
  KEY `fkey_income_activity_id_gender_group` (`id_gender_group`),
  CONSTRAINT `fkey_income_activity_id_gender_group` FOREIGN KEY (`id_gender_group`) REFERENCES `gender_group` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_income_activity_id_income_activity_type` FOREIGN KEY (`id_income_activity_type`) REFERENCES `income_activity_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_income_activity_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_income_activity_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_income_activity_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `income_activity` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5213 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `income_activity_category`
--

DROP TABLE IF EXISTS `income_activity_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `income_activity_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `replaced_by_id` int(10) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_income_activity_category_id_user` (`id_user`),
  KEY `fkey_income_activity_category_replaced_by_id` (`replaced_by_id`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_income_activity_category_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_income_activity_category_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `income_activity_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `income_activity_type`
--

DROP TABLE IF EXISTS `income_activity_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `income_activity_type` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_income_activity_category` int(4) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_income_activity_type_id_income_activity_category` (`id_income_activity_category`),
  KEY `fkey_income_activity_type_id_user` (`id_user`),
  KEY `fkey_income_activity_type_replaced_by_id` (`replaced_by_id`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_income_activity_type_id_income_activity_category` FOREIGN KEY (`id_income_activity_category`) REFERENCES `income_activity_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_income_activity_type_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_income_activity_type_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `income_activity_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `intervention`
--

DROP TABLE IF EXISTS `intervention`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intervention` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_intervention_id_user` (`id_user`),
  KEY `fkey_intervention_replaced_by_id` (`replaced_by_id`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_intervention_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_intervention_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `intervention` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `interview_respondents_all`
--

DROP TABLE IF EXISTS `interview_respondents_all`;
/*!50001 DROP VIEW IF EXISTS `interview_respondents_all`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `interview_respondents_all` AS SELECT 
 1 AS `number_of`,
 1 AS `focus_group_id`,
 1 AS `site_id`,
 1 AS `country_id`,
 1 AS `project_id`,
 1 AS `world_region_id`,
 1 AS `user_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `interview_respondents_user`
--

DROP TABLE IF EXISTS `interview_respondents_user`;
/*!50001 DROP VIEW IF EXISTS `interview_respondents_user`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `interview_respondents_user` AS SELECT 
 1 AS `number_of`,
 1 AS `focus_group_id`,
 1 AS `site_id`,
 1 AS `country_id`,
 1 AS `project_id`,
 1 AS `world_region_id`,
 1 AS `user_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `labor_division_group`
--

DROP TABLE IF EXISTS `labor_division_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `labor_division_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labour_activity`
--

DROP TABLE IF EXISTS `labour_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `labour_activity` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_focus_group` int(8) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `daily_rate_female` decimal(10,2) DEFAULT NULL,
  `daily_rate_male` decimal(10,2) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` datetime(6) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_labour_activity_id_user` (`id_user`),
  KEY `fkey_labour_activity_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_labour_activity_id_focus_group` (`id_focus_group`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_labour_activity_id_focus_group` FOREIGN KEY (`id_focus_group`) REFERENCES `focus_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_labour_activity_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_labour_activity_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `labour_activity` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=420 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `landholding_category`
--

DROP TABLE IF EXISTS `landholding_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landholding_category` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `livestock_holding`
--

DROP TABLE IF EXISTS `livestock_holding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestock_holding` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `id_animal_type` int(8) unsigned DEFAULT NULL,
  `average_weight` decimal(10,2) DEFAULT NULL,
  `headcount` int(4) DEFAULT NULL,
  `dominant_breed` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_livestock_holding_id_respondent` (`id_respondent`),
  KEY `fkey_livestock_holding_id_animal_type` (`id_animal_type`),
  KEY `fkey_livestock_holding_id_user` (`id_user`),
  KEY `fkey_livestock_holding_replaced_by_id` (`replaced_by_id`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_livestock_holding_id_animal_type` FOREIGN KEY (`id_animal_type`) REFERENCES `animal_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_livestock_holding_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_livestock_holding_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_livestock_holding_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `livestock_holding` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7256 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `livestock_sale`
--

DROP TABLE IF EXISTS `livestock_sale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestock_sale` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `id_livestock_sale_category` int(4) unsigned DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `number_sold` int(8) DEFAULT NULL,
  `approximate_weight` int(8) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_livestock_sale_id_respondent` (`id_respondent`),
  KEY `fkey_livestock_sale_id_user` (`id_user`),
  KEY `fkey_livestock_sale_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_livestock_sale_id_livestock_sale_category` (`id_livestock_sale_category`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_livestock_sale_id_livestock_sale_category` FOREIGN KEY (`id_livestock_sale_category`) REFERENCES `livestock_sale_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_livestock_sale_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_livestock_sale_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_livestock_sale_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `livestock_sale` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14216 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `livestock_sale_category`
--

DROP TABLE IF EXISTS `livestock_sale_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestock_sale_category` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_animal_species` int(8) unsigned DEFAULT NULL,
  `id_gender` int(4) unsigned DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_livestock_sale_category_id_user` (`id_user`),
  KEY `fkey_livestock_sale_category_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_livestock_sale_category_id_animal_species` (`id_animal_species`),
  KEY `fkey_livestock_sale_category_id_gender` (`id_gender`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_livestock_sale_category_id_animal_species` FOREIGN KEY (`id_animal_species`) REFERENCES `animal_species` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_livestock_sale_category_id_gender` FOREIGN KEY (`id_gender`) REFERENCES `gender` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_livestock_sale_category_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_livestock_sale_category_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `livestock_sale_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `month`
--

DROP TABLE IF EXISTS `month`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `month` (
  `id` int(4) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `days` int(4) DEFAULT NULL,
  `ordering` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` datetime DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ordering_idx` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `nutrition_stats_output`
--

DROP TABLE IF EXISTS `nutrition_stats_output`;
/*!50001 DROP VIEW IF EXISTS `nutrition_stats_output`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `nutrition_stats_output` AS SELECT 
 1 AS `respondent_id`,
 1 AS `crop_residue_dm`,
 1 AS `crop_residue_me`,
 1 AS `crop_residue_cp`,
 1 AS `cultivated_fodder_dm`,
 1 AS `cultivated_fodder_me`,
 1 AS `cultivated_fodder_cp`,
 1 AS `purchased_feed_dm`,
 1 AS `purchased_feed_me`,
 1 AS `purchased_feed_cp`,
 1 AS `grazing_dm`,
 1 AS `grazing_me`,
 1 AS `grazing_cp`,
 1 AS `collected_fodder_dm`,
 1 AS `collected_fodder_me`,
 1 AS `collected_fodder_cp`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `nutrition_stats_stage`
--

DROP TABLE IF EXISTS `nutrition_stats_stage`;
/*!50001 DROP VIEW IF EXISTS `nutrition_stats_stage`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `nutrition_stats_stage` AS SELECT 
 1 AS `respondent_id`,
 1 AS `grazing_percentage`,
 1 AS `collected_fodder_percentage`,
 1 AS `dm_crop_residue`,
 1 AS `me_crop_residue`,
 1 AS `cp_crop_residue`,
 1 AS `dm_cultivated_fodder`,
 1 AS `me_cultivated_fodder`,
 1 AS `cp_cultivated_fodder`,
 1 AS `dm_purchased_feed`,
 1 AS `me_purchased_feed`,
 1 AS `cp_purchased_feed`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `nutrition_stats_stage2`
--

DROP TABLE IF EXISTS `nutrition_stats_stage2`;
/*!50001 DROP VIEW IF EXISTS `nutrition_stats_stage2`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `nutrition_stats_stage2` AS SELECT 
 1 AS `respondent_id`,
 1 AS `crop_residue_dm`,
 1 AS `cultivated_fodder_dm`,
 1 AS `purchased_feed_dm`,
 1 AS `grazing_dm`,
 1 AS `collected_fodder_dm`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `organization_type`
--

DROP TABLE IF EXISTS `organization_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organization_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_organization_type_id_user` (`id_user`),
  KEY `fkey_organization_type_replaced_by_id` (`replaced_by_id`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_organization_type_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_organization_type_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `organization_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_project_type` int(4) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `id_world_region` int(4) unsigned DEFAULT NULL,
  `id_country` int(8) unsigned DEFAULT NULL,
  `start_date` datetime(6) DEFAULT NULL,
  `end_date` datetime(6) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `partner_organization` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_project_id_country` (`id_country`),
  KEY `fkey_project_id_project_type` (`id_project_type`),
  KEY `fkey_project_id_user` (`id_user`),
  KEY `fkey_project_id_world_region` (`id_world_region`),
  KEY `fkey_project_replaced_by_id` (`replaced_by_id`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_project_id_country` FOREIGN KEY (`id_country`) REFERENCES `country` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_project_id_project_type` FOREIGN KEY (`id_project_type`) REFERENCES `project_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_project_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_project_id_world_region` FOREIGN KEY (`id_world_region`) REFERENCES `world_region` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_project_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `project` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_type`
--

DROP TABLE IF EXISTS `project_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchased_feed`
--

DROP TABLE IF EXISTS `purchased_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchased_feed` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `quantity_purchased` decimal(10,2) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `id_unit_mass_weight` int(8) unsigned DEFAULT NULL,
  `id_purchased_feed_type` int(8) unsigned DEFAULT NULL,
  `purchases_per_year` int(4) DEFAULT NULL,
  `id_currency` int(8) unsigned DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_purchased_feed_id_respondent` (`id_respondent`),
  KEY `fkey_purchased_feed_id_user` (`id_user`),
  KEY `fkey_purchased_feed_id_purchased_feed_type` (`id_purchased_feed_type`),
  KEY `fkey_purchased_feed_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_purchased_feed_currency` (`id_currency`),
  KEY `fkey_purchased_feed_id_unit_mass_weight` (`id_unit_mass_weight`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_purchased_feed_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_purchased_feed_id_purchased_feed_type` FOREIGN KEY (`id_purchased_feed_type`) REFERENCES `purchased_feed_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_purchased_feed_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_purchased_feed_id_unit_mass_weight` FOREIGN KEY (`id_unit_mass_weight`) REFERENCES `unit_mass_weight` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_purchased_feed_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_purchased_feed_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `purchased_feed` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2198 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `purchased_feed_stats`
--

DROP TABLE IF EXISTS `purchased_feed_stats`;
/*!50001 DROP VIEW IF EXISTS `purchased_feed_stats`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `purchased_feed_stats` AS SELECT 
 1 AS `purchased_feed_id`,
 1 AS `respondent_id`,
 1 AS `purchased_feed_dm`,
 1 AS `ratio_me`,
 1 AS `ratio_cp`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `purchased_feed_type`
--

DROP TABLE IF EXISTS `purchased_feed_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchased_feed_type` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `content_percent_dry_matter` decimal(10,2) DEFAULT NULL,
  `content_metabolisable_energy` decimal(10,6) DEFAULT NULL,
  `content_crude_protein` decimal(10,2) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `user_citation` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_purchased_feed_type_id_user` (`id_user`),
  KEY `fkey_purchased_feed_type_replaced_by_id` (`replaced_by_id`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_purchased_feed_type_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_purchased_feed_type_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `purchased_feed_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=245 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `rainfall`
--

DROP TABLE IF EXISTS `rainfall`;
/*!50001 DROP VIEW IF EXISTS `rainfall`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `rainfall` AS SELECT 
 1 AS `id_focus_group`,
 1 AS `order_of_month`,
 1 AS `resource_type`,
 1 AS `numerical_value_raw`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `hidden` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `modified` datetime(6) DEFAULT NULL,
  `modified_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `respondent`
--

DROP TABLE IF EXISTS `respondent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `respondent` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_focus_group` int(8) unsigned DEFAULT NULL,
  `interview_date` datetime(6) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `age` int(4) DEFAULT NULL,
  `id_gender` int(4) unsigned DEFAULT NULL,
  `head_of_household_is_respondent` tinyint(1) DEFAULT NULL,
  `head_of_household_name` varchar(255) DEFAULT NULL,
  `head_of_household_age` int(4) DEFAULT NULL,
  `id_gender_head_of_household` int(4) unsigned DEFAULT NULL,
  `community` varchar(255) DEFAULT NULL,
  `id_community_type` int(4) unsigned DEFAULT NULL,
  `sub_region` varchar(255) DEFAULT NULL,
  `minor_region` varchar(255) DEFAULT NULL,
  `major_region` varchar(255) DEFAULT NULL,
  `id_country` int(8) unsigned DEFAULT NULL,
  `gps_latitude` decimal(10,6) DEFAULT NULL,
  `gps_longitude` decimal(10,6) DEFAULT NULL,
  `id_landholding_category` int(4) unsigned DEFAULT NULL,
  `land_under_cultivation` decimal(10,2) DEFAULT NULL,
  `head_of_household_occupation` varchar(255) DEFAULT NULL,
  `diet_percent_collected_fodder` int(4) DEFAULT NULL,
  `diet_percent_grazing` int(4) DEFAULT NULL,
  `organization_affiliation` varchar(255) DEFAULT NULL,
  `id_unit_area` int(4) unsigned DEFAULT NULL,
  `diet_percent_cultivated_fodder` int(4) DEFAULT NULL,
  `diet_percent_purchased_feed` int(4) DEFAULT NULL,
  `diet_percent_crop_residue` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `id_user` int(4) unsigned DEFAULT NULL,
  `replaced_by_id` int(4) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `female_managed` tinyint(1) DEFAULT '0',
  `validate_no_coop` tinyint(1) DEFAULT '0',
  `land_under_control` decimal(14,5) DEFAULT NULL,
  `land_ownership_joint` decimal(14,5) DEFAULT NULL,
  `land_ownership_female` decimal(14,5) DEFAULT NULL,
  `land_ownership_male` decimal(14,5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_respondent_id_community_type` (`id_community_type`),
  KEY `fkey_respondent_id_country` (`id_country`),
  KEY `fkey_respondent_id_gender` (`id_gender`),
  KEY `fkey_respondent_id_landholding_category` (`id_landholding_category`),
  KEY `fkey_respondent_id_unit_area` (`id_unit_area`),
  KEY `fkey_respondent_id_user` (`id_user`),
  KEY `fkey_respondent_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_respondent_id_focus_group` (`id_focus_group`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_respondent_id_community_type` FOREIGN KEY (`id_community_type`) REFERENCES `community_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_id_country` FOREIGN KEY (`id_country`) REFERENCES `country` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_id_focus_group` FOREIGN KEY (`id_focus_group`) REFERENCES `focus_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_id_gender` FOREIGN KEY (`id_gender`) REFERENCES `gender` (`id`),
  CONSTRAINT `fkey_respondent_id_landholding_category` FOREIGN KEY (`id_landholding_category`) REFERENCES `landholding_category` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_id_unit_area` FOREIGN KEY (`id_unit_area`) REFERENCES `unit_area` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `respondent` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1998 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `respondent_monthly_statistics`
--

DROP TABLE IF EXISTS `respondent_monthly_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `respondent_monthly_statistics` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `id_month` int(4) DEFAULT NULL,
  `milk_average_yield` decimal(10,2) DEFAULT NULL,
  `milk_average_price_litre` decimal(10,2) DEFAULT NULL,
  `milk_retained_for_household` decimal(10,2) DEFAULT NULL,
  `market_price_cattle` decimal(10,2) DEFAULT NULL,
  `market_price_sheep` decimal(10,2) DEFAULT NULL,
  `market_price_goat` decimal(10,2) DEFAULT NULL,
  `id_scale_zero_ten` int(4) unsigned DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_respondent_monthly_statistics_id_respondent` (`id_respondent`),
  KEY `fkey_respondent_monthly_statistics_replaced_by_id` (`replaced_by_id`) USING BTREE,
  KEY `fkey_respondent_monthly_statistics_id_month` (`id_month`),
  KEY `fkey_respondent_monthly_statistics_id_scale_zero_ten` (`id_scale_zero_ten`),
  KEY `fkey_respondent_monthly_statistics_id_user` (`id_user`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_respondent_monthly_statistics_id_month` FOREIGN KEY (`id_month`) REFERENCES `month` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_monthly_statistics_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_monthly_statistics_id_scale_zero_ten` FOREIGN KEY (`id_scale_zero_ten`) REFERENCES `scale_zero_ten` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_monthly_statistics_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_respondent_monthly_statistics_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `respondent_monthly_statistics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23967 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scale_zero_five`
--

DROP TABLE IF EXISTS `scale_zero_five`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scale_zero_five` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `number` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scale_zero_ten`
--

DROP TABLE IF EXISTS `scale_zero_ten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scale_zero_ten` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `number` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `season`
--

DROP TABLE IF EXISTS `season`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `season` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `id_focus_group` int(8) unsigned DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` datetime DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_season_id_user` (`id_user`),
  KEY `fkey_season_replaced_by_id` (`replaced_by_id`),
  KEY `fkey_season_id_focus_group` (`id_focus_group`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_season_id_focus_group` FOREIGN KEY (`id_focus_group`) REFERENCES `focus_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_season_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_season_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `season` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=462 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site`
--

DROP TABLE IF EXISTS `site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_project` int(8) DEFAULT NULL,
  `id_country` int(8) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `community` varchar(255) DEFAULT NULL,
  `sub_region` varchar(255) DEFAULT NULL,
  `minor_region` varchar(255) DEFAULT NULL,
  `major_region` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `id_community_type` int(4) unsigned DEFAULT NULL,
  `id_currency` int(8) unsigned DEFAULT NULL,
  `grazing_metabolisable_energy` decimal(10,6) DEFAULT NULL,
  `grazing_crude_protein_percentage` int(4) DEFAULT NULL,
  `collected_fodder_metabolisable_energy` decimal(10,6) DEFAULT NULL,
  `collected_fodder_crude_protein_percentage` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_site_id_project` (`id_project`),
  KEY `fkey_site_id_community_type` (`id_community_type`),
  KEY `fkey_site_id_country` (`id_country`),
  KEY `fkey_site_id_currency` (`id_currency`),
  KEY `fkey_site_id_user` (`id_user`),
  KEY `fkey_site_replaced_by_id` (`replaced_by_id`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_site_id_community_type` FOREIGN KEY (`id_community_type`) REFERENCES `community_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_site_id_country` FOREIGN KEY (`id_country`) REFERENCES `country` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_site_id_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_site_id_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_site_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_site_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `site` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_country`
--

DROP TABLE IF EXISTS `system_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_country` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime(6) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `modified` datetime(6) NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `world_region_id` int(4) unsigned NOT NULL,
  `org_id` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_system_country_world_region_id` (`world_region_id`),
  KEY `fkey_system_country_org_id` (`org_id`),
  CONSTRAINT `system_country_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `org` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `system_country_ibfk_2` FOREIGN KEY (`world_region_id`) REFERENCES `system_world_region` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_country_major_region`
--

DROP TABLE IF EXISTS `system_country_major_region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_country_major_region` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime(6) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `modified` datetime(6) NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  `country_id` int(8) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `abbreviation` varchar(50) DEFAULT NULL,
  `org_id` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_system_country_major_region_country_id` (`country_id`),
  KEY `fkey_system_country_major_region_org_id` (`org_id`),
  CONSTRAINT `system_country_major_region_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `system_country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `system_country_major_region_ibfk_2` FOREIGN KEY (`org_id`) REFERENCES `org` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_setting`
--

DROP TABLE IF EXISTS `system_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_setting` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime(6) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `modified` datetime(6) NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  `setting` varchar(255) DEFAULT NULL,
  `value` text,
  `org_id` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_org_setting_org_id` (`org_id`),
  CONSTRAINT `system_setting_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `org` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_token_type`
--

DROP TABLE IF EXISTS `system_token_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_token_type` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(50) DEFAULT NULL,
  `org_id` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_system_token_type_id` (`org_id`),
  CONSTRAINT `system_token_type_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `org` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_world_region`
--

DROP TABLE IF EXISTS `system_world_region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_world_region` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime(6) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `modified` datetime(6) NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `org_id` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_system_world_region_org_id` (`org_id`),
  CONSTRAINT `system_world_region_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `org` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `techfit_assessment`
--

DROP TABLE IF EXISTS `techfit_assessment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `techfit_assessment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_focus_group` int(8) DEFAULT NULL,
  `id_core_commodity` int(8) unsigned DEFAULT NULL,
  `id_agriculture_system_type` int(4) unsigned DEFAULT NULL,
  `lease_price_ha_land` decimal(10,2) DEFAULT NULL,
  `percent_land_fallow` int(4) DEFAULT NULL,
  `percent_land_cash_crop` int(4) DEFAULT NULL,
  `percent_land_subsistence` int(4) DEFAULT NULL,
  `percent_land_fodder` int(4) DEFAULT NULL,
  `labour_daily_cost_max` decimal(10,2) DEFAULT NULL,
  `labour_daily_cost_min` decimal(10,2) DEFAULT NULL,
  `percent_land_cultivated` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `uploaded_at` date DEFAULT NULL,
  `id_user` int(8) DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `replaced_by_id` int(8) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `id_site` int(10) unsigned DEFAULT NULL,
  `weight_core_feed_issue` decimal(10,6) DEFAULT NULL,
  `weight_commodity` decimal(10,6) DEFAULT NULL,
  `weight_fs` decimal(10,6) DEFAULT NULL,
  `weight_context_attribute` decimal(10,6) DEFAULT NULL,
  `weight_impact` decimal(10,6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_techfit_assessment_id_core_commodity` (`id_core_commodity`),
  KEY `fkey_techfit_assessment_id_agriculture_system_type` (`id_agriculture_system_type`),
  KEY `exclude_idx` (`exclude`),
  KEY `private_idx` (`keep_private`),
  KEY `fkey_techfit_assessment_id_site` (`id_site`),
  CONSTRAINT `fkey_techfit_assessment_id_agriculture_system_type` FOREIGN KEY (`id_agriculture_system_type`) REFERENCES `agriculture_system_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_techfit_assessment_id_core_commodity` FOREIGN KEY (`id_core_commodity`) REFERENCES `core_commodity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_techfit_assessment_id_site` FOREIGN KEY (`id_site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `techfit_scale`
--

DROP TABLE IF EXISTS `techfit_scale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `techfit_scale` (
  `id` int(11) NOT NULL,
  `number` int(4) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unit_area`
--

DROP TABLE IF EXISTS `unit_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `id_unit_type` int(4) unsigned DEFAULT NULL,
  `conversion_ha` decimal(10,6) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fkey_unit_area_id_unit_type` (`id_unit_type`),
  KEY `fkey_unit_area_id_user` (`id_user`),
  KEY `fkey_unit_area_replaced_by_id` (`replaced_by_id`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_unit_area_id_unit_type` FOREIGN KEY (`id_unit_type`) REFERENCES `unit_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_unit_area_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_unit_area_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `unit_area` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unit_mass_weight`
--

DROP TABLE IF EXISTS `unit_mass_weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_mass_weight` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `id_unit_type` int(4) unsigned DEFAULT NULL,
  `conversion_kg` decimal(10,6) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime(6) DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fkey_unit_mass_weight_id_unit_type` (`id_unit_type`),
  KEY `fkey_unit_mass_weight_id_user` (`id_user`),
  KEY `fkey_unit_mass_weight_replaced_by_id` (`replaced_by_id`),
  KEY `private_idx` (`keep_private`),
  CONSTRAINT `fkey_unit_mass_weight_id_unit_type` FOREIGN KEY (`id_unit_type`) REFERENCES `unit_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_unit_mass_weight_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_unit_mass_weight_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `unit_mass_weight` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unit_type`
--

DROP TABLE IF EXISTS `unit_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` datetime(6) DEFAULT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_approval_status_id` int(4) DEFAULT NULL,
  `name_salutation_id` int(4) DEFAULT NULL,
  `name_first` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `name_middle` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `name_last` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_email_confirmed` tinyint(1) DEFAULT NULL,
  `contact_telephone` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_skype` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_mobile` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_address_1` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_address_2` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_country_id` int(8) DEFAULT NULL,
  `contact_region_major` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_region_minor` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_region_subregion` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_postal_code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_city_town` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `affiliation` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `position_title` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `gender_id` int(4) unsigned DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `id_user_approval_status` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_user_id_user_approval_status` (`id_user_approval_status`),
  KEY `fkey_user_gender_id` (`gender_id`),
  CONSTRAINT `fkey_user_gender_id` FOREIGN KEY (`gender_id`) REFERENCES `gender` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_user_id_user_approval_status` FOREIGN KEY (`id_user_approval_status`) REFERENCES `user_approval_status` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_approval_status`
--

DROP TABLE IF EXISTS `user_approval_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_approval_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `modified` datetime NOT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `org_id` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_gender`
--

DROP TABLE IF EXISTS `user_gender`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_gender` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime(6) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `modified` datetime(6) NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL,
  `org_id` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_user_gender_org_id` (`org_id`),
  CONSTRAINT `user_gender_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `org` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_salutation`
--

DROP TABLE IF EXISTS `user_salutation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_salutation` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime(6) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `modified` datetime(6) NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL,
  `org_id` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_user_salutation_org_id` (`org_id`),
  CONSTRAINT `user_salutation_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `org` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_token`
--

DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_token` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(8) unsigned NOT NULL,
  `token_type_id` int(4) unsigned NOT NULL,
  `token` text NOT NULL,
  `created` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `modified` datetime(6) DEFAULT NULL,
  `modified_by` varchar(255) DEFAULT NULL,
  `org_id` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_user_token_user_id` (`user_id`),
  KEY `fkey_user_token_token_type_id` (`token_type_id`),
  KEY `fkey_user_token_org_id` (`org_id`),
  CONSTRAINT `user_token_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `org` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_token_ibfk_2` FOREIGN KEY (`token_type_id`) REFERENCES `system_token_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `user_token_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `womens_income_activity`
--

DROP TABLE IF EXISTS `womens_income_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `womens_income_activity` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_respondent` int(8) unsigned DEFAULT NULL,
  `id_income_activity_type` int(8) unsigned DEFAULT NULL,
  `pct_womens_income` int(4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `id_user` int(8) unsigned DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  `replaced_by_id` int(8) unsigned DEFAULT NULL,
  `keep_private` tinyint(1) DEFAULT NULL,
  `exclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkey_wia_id_income_activity_type` (`id_income_activity_type`),
  KEY `fkey_wia_id_respondent` (`id_respondent`),
  KEY `fkey_wia_id_user` (`id_user`),
  KEY `fkey_wia_replaced_by_id` (`replaced_by_id`),
  CONSTRAINT `fkey_wia_id_income_activity_type` FOREIGN KEY (`id_income_activity_type`) REFERENCES `income_activity_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fkey_wia_id_respondent` FOREIGN KEY (`id_respondent`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkey_wia_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkey_wia_replaced_by_id` FOREIGN KEY (`replaced_by_id`) REFERENCES `womens_income_activity` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world_region`
--

DROP TABLE IF EXISTS `world_region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `world_region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `canonical_data` tinyint(1) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `crop_residue_stats`
--

/*!50001 DROP VIEW IF EXISTS `crop_residue_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `crop_residue_stats` AS select `crop_cultivation`.`id` AS `crop_id`,`crop_cultivation`.`id_respondent` AS `respondent_id`,ifnull((((((((`crop_cultivation`.`annual_yield` * `unit_mass_weight`.`conversion_kg`) / `crop_type`.`harvest_index`) - (`crop_cultivation`.`annual_yield` * 'unit_mass_weight')) * `crop_cultivation`.`percent_fed`) * 0.01) * `crop_type`.`content_percent_dry_matter`) * 0.01),0) AS `crop_residue_dm`,`crop_type`.`content_metabolisable_energy` AS `ratio_me`,(`crop_type`.`content_crude_protein` * 0.01) AS `ratio_cp` from ((`crop_cultivation` left join `unit_mass_weight` on((`unit_mass_weight`.`id` = `crop_cultivation`.`id_unit_mass_weight`))) left join `crop_type` on((`crop_type`.`id` = `crop_cultivation`.`id_crop_type`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `cultivated_fodder_stats`
--

/*!50001 DROP VIEW IF EXISTS `cultivated_fodder_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `cultivated_fodder_stats` AS select `fodder_crop_cultivation`.`id` AS `cultivated_fodder_id`,`fodder_crop_cultivation`.`id_respondent` AS `respondent_id`,ifnull(((`fodder_crop_cultivation`.`cultivated_land` * `unit_area`.`conversion_ha`) * `fodder_crop_type`.`annual_dry_matter_per_hectare`),0) AS `cultivated_fodder_dm`,`fodder_crop_type`.`content_metabolisable_energy` AS `ratio_me`,(`fodder_crop_type`.`content_crude_protein` * 0.01) AS `ratio_cp` from ((`fodder_crop_cultivation` left join `unit_area` on((`unit_area`.`id` = `fodder_crop_cultivation`.`id_unit_area`))) left join `fodder_crop_type` on((`fodder_crop_type`.`id` = `fodder_crop_cultivation`.`id_fodder_crop_type`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_coop_membership`
--

/*!50001 DROP VIEW IF EXISTS `export_coop_membership`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `export_coop_membership` AS select now() AS `export_time`,`coop_membership`.`id_user` AS `user_id`,`coop_membership`.`uploaded_at` AS `uploaded_at`,`coop_membership`.`keep_private` AS `private`,`coop_membership`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`coop_membership`.`id` AS `coop_membership_id`,`coop_membership`.`name_free_entry` AS `coop_membership_name_free_entry`,`coop_membership`.`membership_count_male` AS `coop_membership_membership_count_male`,`coop_membership`.`membership_count_female` AS `coop_membership_membership_count_female` from (((((((`coop_membership` left join `respondent` on((`respondent`.`id` = `coop_membership`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_core_context_attribute_score`
--

/*!50001 DROP VIEW IF EXISTS `export_core_context_attribute_score`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_core_context_attribute_score` AS select now() AS `export_time`,`core_context_attribute_score`.`id_user` AS `user_id`,`core_context_attribute_score`.`uploaded_at` AS `uploaded_at`,`core_context_attribute_score`.`keep_private` AS `private`,`core_context_attribute_score`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,count(distinct `respondent`.`id`) AS `respondent_count`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`techfit_scale`.`number` AS `techfit_scale_number`,`core_context_attribute_score`.`id` AS `core_context_attribute_score_id`,`core_context_attribute`.`id` AS `core_context_attribute_id`,`core_context_attribute`.`prompt` AS `core_context_attribute_prompt`,`core_context_attribute_type`.`id` AS `core_context_attribute_type_id`,`core_context_attribute_type`.`description` AS `core_context_attribute_type_description` from ((((((((((`core_context_attribute_score` left join `techfit_assessment` on((`techfit_assessment`.`id` = `core_context_attribute_score`.`id_techfit_assessment`))) left join `focus_group` on((`focus_group`.`id` = `techfit_assessment`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `respondent` on((`respondent`.`id_focus_group` = `focus_group`.`id`))) left join `techfit_scale` on((`techfit_scale`.`id` = `core_context_attribute_score`.`id_techfit_scale`))) left join `core_context_attribute` on((`core_context_attribute`.`id` = `core_context_attribute_score`.`id_core_context_attribute`))) left join `core_context_attribute_type` on((`core_context_attribute_type`.`id` = `core_context_attribute`.`id_core_context_attribute_type`))) group by `core_context_attribute_score`.`id`,`techfit_assessment`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_crop_cultivation`
--

/*!50001 DROP VIEW IF EXISTS `export_crop_cultivation`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_crop_cultivation` AS select now() AS `export_time`,`crop_cultivation`.`id_user` AS `user_id`,`crop_cultivation`.`uploaded_at` AS `uploaded_at`,`crop_cultivation`.`keep_private` AS `private`,`crop_cultivation`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`crop_cultivation`.`id` AS `crop_cultivation_id`,`crop_type`.`id` AS `crop_type_id`,`crop_type`.`name` AS `crop_type_name`,`crop_type`.`harvest_index` AS `crop_type_harvest_index`,`crop_type`.`content_percent_dry_matter` AS `crop_type_content_percent_dry_matter`,`crop_type`.`content_metabolisable_energy` AS `crop_type_content_metabolisable_energy`,`crop_type`.`content_crude_protein` AS `crop_type_content_crude_protein`,`unit_area`.`id` AS `unit_area_id`,`unit_area`.`name` AS `unit_area_name`,`unit_area`.`conversion_ha` AS `unit_area_conversion_ha`,`unit_mass_weight`.`id` AS `unit_mass_weight_id`,`unit_mass_weight`.`name` AS `unit_mass_weight_name`,`unit_mass_weight`.`conversion_kg` AS `unit_mass_weight_conversion_kg`,`crop_cultivation`.`cultivated_land` AS `crop_cultivation_cultivated_land`,format((`crop_cultivation`.`cultivated_land` * `unit_area`.`conversion_ha`),5) AS `crop_cultivation_cultivated_land_ha`,`crop_cultivation`.`annual_yield` AS `crop_cultivation_annual_yield`,coalesce(`crop_cultivation`.`percent_fed`,0) AS `crop_cultivation_percent_fed`,coalesce(`crop_cultivation`.`percent_burned`,0) AS `crop_cultivation_percent_burned`,coalesce(`crop_cultivation`.`percent_mulched`,0) AS `crop_cultivation_percent_mulched`,coalesce(`crop_cultivation`.`percent_sold`,0) AS `crop_cultivation_percent_sold`,coalesce(`crop_cultivation`.`percent_other`,0) AS `crop_cultivation_percent_other` from ((((((((((`crop_cultivation` left join `respondent` on((`respondent`.`id` = `crop_cultivation`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `crop_type` on((`crop_type`.`id` = `crop_cultivation`.`id_crop_type`))) left join `unit_area` on((`unit_area`.`id` = `crop_cultivation`.`id_unit_area`))) left join `unit_mass_weight` on((`unit_mass_weight`.`id` = `crop_cultivation`.`id_unit_mass_weight`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_decision_making_by_household`
--

/*!50001 DROP VIEW IF EXISTS `export_decision_making_by_household`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `export_decision_making_by_household` AS select now() AS `export_time`,`decision_making_by_household`.`id_user` AS `user_id`,`decision_making_by_household`.`uploaded_at` AS `uploaded_at`,`decision_making_by_household`.`keep_private` AS `private`,`decision_making_by_household`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`decision_making_by_household`.`id` AS `decision_making_by_household_id`,`decision`.`id` AS `decision_id`,`decision`.`description` AS `decision_description`,`decision_type`.`id` AS `decision_type_id`,`decision_type`.`description` AS `decision_type_description`,`gender_group`.`description` AS `gender_group_description` from ((((((((((`decision_making_by_household` left join `respondent` on((`respondent`.`id` = `decision_making_by_household`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `gender_group` on((`gender_group`.`id` = `decision_making_by_household`.`id_gender_group`))) left join `decision` on((`decision`.`id` = `decision_making_by_household`.`id_decision`))) left join `decision_type` on((`decision_type`.`id` = `decision`.`id_decision_type`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_feed_labor_division`
--

/*!50001 DROP VIEW IF EXISTS `export_feed_labor_division`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `export_feed_labor_division` AS select now() AS `export_time`,`feed_labor_division`.`id_user` AS `user_id`,`feed_labor_division`.`uploaded_at` AS `uploaded_at`,`feed_labor_division`.`keep_private` AS `private`,`feed_labor_division`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`feed_labor_division`.`id` AS `feed_labor_division_id`,`feed_labor_type`.`id` AS `feed_labor_type_id`,`feed_labor_type`.`description` AS `feed_labor_type_description`,`labor_division_group`.`description` AS `labor_division_group_description` from (((((((((`feed_labor_division` left join `respondent` on((`respondent`.`id` = `feed_labor_division`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `labor_division_group` on((`labor_division_group`.`id` = `feed_labor_division`.`id_labor_division_group`))) left join `feed_labor_type` on((`feed_labor_type`.`id` = `feed_labor_division`.`id_feed_labor_type`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_feed_source_availability`
--

/*!50001 DROP VIEW IF EXISTS `export_feed_source_availability`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_feed_source_availability` AS select now() AS `export_time`,`feed_source_availability`.`id_user` AS `user_id`,`feed_source_availability`.`uploaded_at` AS `uploaded_at`,`feed_source_availability`.`keep_private` AS `private`,`feed_source_availability`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`feed_source_availability`.`id` AS `feed_source_availability_id`,`scale_zero_ten`.`number` AS `feed_availability`,`feed_source`.`id` AS `feed_source_id`,`feed_source`.`description` AS `feed_source_description`,`month`.`id` AS `month_id`,`month`.`name` AS `month_name`,coalesce(`feed_source_availability`.`contribution`,0) AS `percentage` from (((((((((((`feed_source_availability` left join `respondent` on((`respondent`.`id` = `feed_source_availability`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `respondent_monthly_statistics` on(((`respondent_monthly_statistics`.`id_respondent` = `feed_source_availability`.`id_respondent`) and (`respondent_monthly_statistics`.`id_month` = `feed_source_availability`.`id_month`)))) left join `scale_zero_ten` on((`scale_zero_ten`.`id` = `respondent_monthly_statistics`.`id_scale_zero_ten`))) left join `month` on((`month`.`id` = `feed_source_availability`.`id_month`))) left join `feed_source` on((`feed_source`.`id` = `feed_source_availability`.`id_feed_source`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_focus_group`
--

/*!50001 DROP VIEW IF EXISTS `export_focus_group`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_focus_group` AS select now() AS `export_time`,`focus_group`.`id_user` AS `user_id`,`focus_group`.`uploaded_at` AS `uploaded_at`,`focus_group`.`keep_private` AS `private`,`focus_group`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`focus_group`.`threshold_large_farm_ha` AS `focus_group_threshold_large_farm_ha`,`focus_group`.`threshold_small_farm_ha` AS `focus_group_threshold_small_farm_ha`,`focus_group`.`percent_households_landless` AS `focus_group_percent_households_landless`,`focus_group`.`percent_households_small` AS `focus_group_percent_households_small`,`focus_group`.`percent_households_medium` AS `focus_group_percent_households_medium`,`focus_group`.`percent_households_large` AS `focus_group_percent_households_large`,`focus_group`.`percent_credit_formal` AS `focus_group_percent_credit_formal`,`focus_group`.`percent_credit_informal` AS `focus_group_percent_credit_informal`,`focus_group`.`household_percent_migrating` AS `focus_group_household_percent_migrating`,`focus_group`.`percent_reproduction_bull` AS `focus_group_percent_reproduction_bull`,`focus_group`.`percent_reproduction_ai` AS `focus_group_percent_reproduction_ai`,`focus_group`.`percent_processing_female` AS `focus_group_percent_processing_female`,`focus_group`.`percent_processing_male` AS `focus_group_percent_processing_male`,`focus_group`.`percent_processing_overall` AS `focus_group_percent_processing_overall`,`focus_group`.`market_avg_distance_km` AS `focus_group_market_avg_distance_km`,`focus_group`.`market_avg_cost_travel` AS `focus_group_market_avg_cost_travel`,`focus_group`.`partner_organization` AS `focus_group_partner_organization` from ((((`focus_group` left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_focus_group_monthly_statistics`
--

/*!50001 DROP VIEW IF EXISTS `export_focus_group_monthly_statistics`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_focus_group_monthly_statistics` AS select now() AS `export_time`,`focus_group_monthly_statistics`.`id_user` AS `user_id`,`focus_group_monthly_statistics`.`uploaded_at` AS `uploaded_at`,`focus_group_monthly_statistics`.`keep_private` AS `private`,`focus_group_monthly_statistics`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`focus_group_monthly_statistics`.`id` AS `focus_group_monthly_statistics_id`,`month`.`id` AS `month_id`,`month`.`name` AS `month_name`,`season`.`id` AS `season_id`,`season`.`name` AS `season_name`,`scale_zero_five`.`number` AS `rainfall` from ((((((((`focus_group_monthly_statistics` left join `focus_group` on((`focus_group`.`id` = `focus_group_monthly_statistics`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `month` on((`month`.`id` = `focus_group_monthly_statistics`.`id_month`))) left join `season` on((`season`.`id` = `focus_group_monthly_statistics`.`id_season`))) left join `scale_zero_five` on((`scale_zero_five`.`id` = `focus_group_monthly_statistics`.`id_scale_zero_five`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_fodder_crop_cultivation`
--

/*!50001 DROP VIEW IF EXISTS `export_fodder_crop_cultivation`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_fodder_crop_cultivation` AS select now() AS `export_time`,`fodder_crop_cultivation`.`id_user` AS `user_id`,`fodder_crop_cultivation`.`uploaded_at` AS `uploaded_at`,`fodder_crop_cultivation`.`keep_private` AS `private`,`fodder_crop_cultivation`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`currency`.`id` AS `currency_id`,`currency`.`name` AS `currency_name`,`currency`.`default_usd_exchange_rate` AS `currency_default_usd_exchange_rate`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`fodder_crop_cultivation`.`id` AS `fodder_crop_cultivation_id`,`fodder_crop_type`.`id` AS `fodder_crop_type_id`,`fodder_crop_type`.`name` AS `fodder_crop_type_name`,`fodder_crop_type`.`annual_dry_matter_per_hectare` AS `fodder_crop_type_annual_dry_matter_per_hectare`,`fodder_crop_type`.`content_metabolisable_energy` AS `fodder_crop_type_content_metabolisable_energy`,`fodder_crop_type`.`content_crude_protein` AS `fodder_crop_type_content_crude_protein`,`unit_area`.`id` AS `unit_area_id`,`unit_area`.`name` AS `unit_area_name`,`unit_area`.`conversion_ha` AS `unit_area_conversion_ha`,`fodder_crop_cultivation`.`cultivated_land` AS `fodder_crop_cultivation_cultivated_land`,format((`fodder_crop_cultivation`.`cultivated_land` * `unit_area`.`conversion_ha`),5) AS `fodder_crop_cultivation_cultiavted_land_ha` from ((((((((((`fodder_crop_cultivation` left join `respondent` on((`respondent`.`id` = `fodder_crop_cultivation`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `currency` on((`currency`.`id` = `site`.`id_currency`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `fodder_crop_type` on((`fodder_crop_type`.`id` = `fodder_crop_cultivation`.`id_fodder_crop_type`))) left join `unit_area` on((`unit_area`.`id` = `fodder_crop_cultivation`.`id_unit_area`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_income_activity`
--

/*!50001 DROP VIEW IF EXISTS `export_income_activity`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_income_activity` AS select now() AS `export_time`,`income_activity`.`id_user` AS `user_id`,`income_activity`.`uploaded_at` AS `uploaded_at`,`income_activity`.`keep_private` AS `private`,`income_activity`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`income_activity`.`id` AS `income_activity_id`,`income_activity_type`.`id` AS `income_activity_type_id`,`income_activity_type`.`description` AS `income_activity_type_description`,`income_activity_category`.`id` AS `income_activity_category_id`,`income_activity_category`.`description` AS `income_activity_category_description`,`income_activity`.`percent_of_hh_income` AS `income_activity_percent_of_hh_income` from (((((((((`income_activity` left join `respondent` on((`respondent`.`id` = `income_activity`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `income_activity_type` on((`income_activity_type`.`id` = `income_activity`.`id_income_activity_type`))) left join `income_activity_category` on((`income_activity_category`.`id` = `income_activity_type`.`id_income_activity_category`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_labour_activity`
--

/*!50001 DROP VIEW IF EXISTS `export_labour_activity`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_labour_activity` AS select now() AS `export_time`,`labour_activity`.`id_user` AS `user_id`,`labour_activity`.`uploaded_at` AS `uploaded_at`,`labour_activity`.`keep_private` AS `private`,`labour_activity`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`currency`.`id` AS `currency_id`,`currency`.`name` AS `currency_name`,`currency`.`default_usd_exchange_rate` AS `currency_default_usd_exchange_rate`,`labour_activity`.`id` AS `labour_activity_id`,`labour_activity`.`description` AS `labour_activity_description`,`labour_activity`.`daily_rate_female` AS `labour_activity_daily_rate_female`,`labour_activity`.`daily_rate_male` AS `labour_activity_daily_rate_male` from ((((((`labour_activity` left join `focus_group` on((`focus_group`.`id` = `labour_activity`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `currency` on((`currency`.`id` = `site`.`id_currency`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_livestock_holding`
--

/*!50001 DROP VIEW IF EXISTS `export_livestock_holding`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_livestock_holding` AS select now() AS `export_time`,`livestock_holding`.`id_user` AS `user_id`,`livestock_holding`.`uploaded_at` AS `uploaded_at`,`livestock_holding`.`keep_private` AS `private`,`livestock_holding`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`livestock_holding`.`id` AS `livestock_holding_id`,`animal_type`.`id` AS `animal_type_id`,`animal_type`.`description` AS `animal_type_description`,`animal_category`.`id` AS `animal_category_id`,`animal_category`.`description` AS `animal_category_description`,`animal_species`.`id` AS `animal_species_id`,`animal_species`.`description` AS `animal_species_description`,`animal_type`.`lactating` AS `animal_type_lactating`,`animal_type`.`dairy` AS `animal_type_dairy`,`animal_gender`.`id` AS `animal_gender_id`,`animal_gender`.`description` AS `animal_gender_description`,`livestock_holding`.`dominant_breed` AS `livestock_holding_dominant_breed`,`livestock_holding`.`average_weight` AS `livestock_holding_average_weight`,`livestock_holding`.`headcount` AS `livestock_holding_headcount` from (((((((((((`livestock_holding` left join `respondent` on((`respondent`.`id` = `livestock_holding`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `animal_type` on((`animal_type`.`id` = `livestock_holding`.`id_animal_type`))) left join `animal_category` on((`animal_category`.`id` = `animal_type`.`id_animal_category`))) left join `animal_species` on((`animal_species`.`id` = `animal_category`.`id_animal_species`))) left join `gender` `animal_gender` on((`animal_gender`.`id` = `animal_type`.`id_gender`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_livestock_sale`
--

/*!50001 DROP VIEW IF EXISTS `export_livestock_sale`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_livestock_sale` AS select now() AS `export_time`,`livestock_sale`.`id_user` AS `user_id`,`livestock_sale`.`uploaded_at` AS `uploaded_at`,`livestock_sale`.`keep_private` AS `private`,`livestock_sale`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`currency`.`id` AS `currency_id`,`currency`.`name` AS `currency_name`,`currency`.`default_usd_exchange_rate` AS `currency_default_usd_exchange_rate`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`livestock_sale`.`id` AS `livestock_sale_id`,`livestock_sale_category`.`id` AS `livestock_sale_category_id`,`animal_species`.`id` AS `animal_species_id`,`animal_species`.`description` AS `animal_species_description`,`animal_gender`.`id` AS `animal_gender_id`,`animal_gender`.`description` AS `animal_gender_description`,`livestock_sale`.`number_sold` AS `livestock_sale_number_sold`,`livestock_sale`.`approximate_weight` AS `livestock_sale_approximate_weight` from (((((((((((`livestock_sale` left join `respondent` on((`respondent`.`id` = `livestock_sale`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `currency` on((`currency`.`id` = `site`.`id_currency`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `livestock_sale_category` on((`livestock_sale_category`.`id` = `livestock_sale`.`id_livestock_sale_category`))) left join `animal_species` on((`animal_species`.`id` = `livestock_sale_category`.`id_animal_species`))) left join `gender` `animal_gender` on((`animal_gender`.`id` = `livestock_sale_category`.`id_gender`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_project_site`
--

/*!50001 DROP VIEW IF EXISTS `export_project_site`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_project_site` AS select now() AS `export_time`,`project`.`id_user` AS `user_id`,`project`.`uploaded_at` AS `uploaded_at`,`project`.`keep_private` AS `private`,`project`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`project`.`description` AS `project_description`,`project`.`partner_organization` AS `project_partner_organization`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`site`.`major_region` AS `site_major_region`,`world_region`.`name` AS `site_world_region`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country_name`,`community_type`.`description` AS `site_community_type`,`site`.`grazing_metabolisable_energy` AS `site_grazing_metabolisable_energy`,`site`.`grazing_crude_protein_percentage` AS `site_grazing_crude_protein_percentage`,`site`.`collected_fodder_metabolisable_energy` AS `site_collected_fodder_metabolisable_energy`,`site`.`collected_fodder_crude_protein_percentage` AS `site_collected_fodder_crude_protein_percentage` from ((((`project` left join `site` on((`site`.`id_project` = `project`.`id`))) left join `country` on((`site`.`id_country` = `country`.`id`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `community_type` on((`site`.`id_community_type` = `community_type`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_purchased_feed`
--

/*!50001 DROP VIEW IF EXISTS `export_purchased_feed`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_purchased_feed` AS select now() AS `export_time`,`purchased_feed`.`id_user` AS `user_id`,`purchased_feed`.`uploaded_at` AS `uploaded_at`,`purchased_feed`.`keep_private` AS `private`,`purchased_feed`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`currency`.`id` AS `currency_id`,`currency`.`name` AS `currency_name`,`currency`.`default_usd_exchange_rate` AS `currency_default_usd_exchange_rate`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`purchased_feed`.`id` AS `purchased_feed_id`,`purchased_feed_type`.`id` AS `purchased_feed_type_id`,`purchased_feed_type`.`name` AS `purchased_feed_type_name`,`purchased_feed_type`.`content_percent_dry_matter` AS `purchased_feed_type_content_percent_dry_matter`,`purchased_feed_type`.`content_metabolisable_energy` AS `purchased_feed_type_content_metabolisable_energy`,`purchased_feed_type`.`content_crude_protein` AS `purchased_feed_type_content_crude_protein`,`unit_mass_weight`.`id` AS `unit_mass_weight_id`,`unit_mass_weight`.`name` AS `unit_mass_weight_name`,`unit_mass_weight`.`conversion_kg` AS `unit_mass_weight_conversion_kg`,`purchased_feed`.`purchases_per_year` AS `purchased_feed_purchases_per_year`,`purchased_feed_currency`.`id` AS `purchased_feed_currency_id`,`purchased_feed_currency`.`name` AS `purchased_feed_currency_name`,`purchased_feed_currency`.`default_usd_exchange_rate` AS `purchased_feed_currency_default_usd_exchange_rate`,`purchased_feed`.`quantity_purchased` AS `purchased_feed_quantity_purchased`,`purchased_feed`.`unit_price` AS `purchased_feed_unit_price` from (((((((((((`purchased_feed` left join `respondent` on((`respondent`.`id` = `purchased_feed`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `currency` on((`currency`.`id` = `site`.`id_currency`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `purchased_feed_type` on((`purchased_feed_type`.`id` = `purchased_feed`.`id_purchased_feed_type`))) left join `unit_mass_weight` on((`unit_mass_weight`.`id` = `purchased_feed`.`id_unit_mass_weight`))) left join `currency` `purchased_feed_currency` on((`purchased_feed_currency`.`id` = `site`.`id_currency`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_respondent`
--

/*!50001 DROP VIEW IF EXISTS `export_respondent`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_respondent` AS select now() AS `export_time`,`respondent`.`id_user` AS `user_id`,`respondent`.`uploaded_at` AS `uploaded_at`,`respondent`.`keep_private` AS `private`,`respondent`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`currency`.`id` AS `currency_id`,`currency`.`name` AS `currency_name`,`currency`.`default_usd_exchange_rate` AS `currency_default_usd_exchange_rate`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`respondent`.`interview_date` AS `respondent_interview_date`,`respondent`.`age` AS `respondent_age`,`respondent_gender`.`id` AS `respondent_gender_id`,`respondent_gender`.`description` AS `respondent_gender_description`,`respondent`.`head_of_household_is_respondent` AS `respondent_head_of_household_is_respondent`,`respondent`.`head_of_household_age` AS `respondent_head_of_household_age`,`respondent`.`head_of_household_occupation` AS `respondent_head_of_household_occupation`,`respondent`.`organization_affiliation` AS `respondent_organization_affiliation`,`respondent`.`community` AS `respondent_community`,`community_type`.`id` AS `community_type_id`,`community_type`.`description` AS `community_type_description`,`respondent_country`.`id` AS `respondent_country_id`,`respondent_country`.`name` AS `respondent_country_name`,`landholding_category`.`id` AS `landholding_category_id`,`landholding_category`.`description` AS `landholding_category_description`,`unit_area`.`id` AS `unit_area_id`,`unit_area`.`name` AS `unit_area_name`,`respondent`.`land_under_cultivation` AS `respondent_land_under_cultivation`,`respondent`.`diet_percent_collected_fodder` AS `respondent_diet_percent_collected_fodder`,`respondent`.`diet_percent_grazing` AS `respondent_diet_percent_grazing` from ((((((((((((`respondent` left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `gender` `respondent_gender` on((`respondent_gender`.`id` = `respondent`.`id_gender`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `currency` on((`currency`.`id` = `site`.`id_currency`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `community_type` on((`community_type`.`id` = `respondent`.`id_community_type`))) left join `country` `respondent_country` on((`respondent_country`.`id` = `respondent`.`id_country`))) left join `landholding_category` on((`landholding_category`.`id` = `respondent`.`id_landholding_category`))) left join `unit_area` on((`unit_area`.`id` = `respondent`.`id_unit_area`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_respondent_monthly_statistics`
--

/*!50001 DROP VIEW IF EXISTS `export_respondent_monthly_statistics`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `export_respondent_monthly_statistics` AS select now() AS `export_time`,`respondent_monthly_statistics`.`id_user` AS `user_id`,`respondent_monthly_statistics`.`uploaded_at` AS `uploaded_at`,`respondent_monthly_statistics`.`keep_private` AS `private`,`respondent_monthly_statistics`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`currency`.`id` AS `currency_id`,`currency`.`name` AS `currency_name`,`currency`.`default_usd_exchange_rate` AS `currency_default_usd_exchange_rate`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`respondent_monthly_statistics`.`id` AS `respondent_monthly_statistics_id`,`month`.`id` AS `month_id`,`month`.`name` AS `month_name`,`respondent_monthly_statistics`.`milk_average_yield` AS `respondent_monthly_statistics_milk_average_yield`,`respondent_monthly_statistics`.`milk_average_price_litre` AS `respondent_monthly_statistics_milk_average_price_litre`,`respondent_monthly_statistics`.`milk_retained_for_household` AS `respondent_monthly_statistics_milk_retained_for_household`,`respondent_monthly_statistics`.`market_price_cattle` AS `respondent_monthly_statistics_market_price_cattle`,`respondent_monthly_statistics`.`market_price_sheep` AS `respondent_monthly_statistics_market_price_sheep`,`respondent_monthly_statistics`.`market_price_goat` AS `respondent_monthly_statistics_market_price_goat` from (((((((((`respondent_monthly_statistics` left join `respondent` on((`respondent`.`id` = `respondent_monthly_statistics`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `currency` on((`currency`.`id` = `site`.`id_currency`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `month` on((`month`.`id` = `respondent_monthly_statistics`.`id_month`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `export_womens_income_activity`
--

/*!50001 DROP VIEW IF EXISTS `export_womens_income_activity`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `export_womens_income_activity` AS select now() AS `export_time`,`womens_income_activity`.`id_user` AS `user_id`,`womens_income_activity`.`uploaded_at` AS `uploaded_at`,`womens_income_activity`.`keep_private` AS `private`,`womens_income_activity`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`project`.`title` AS `project_title`,`site`.`id` AS `site_id`,`site`.`name` AS `site_name`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country`,`focus_group`.`id` AS `focus_group_id`,`respondent`.`id` AS `respondent_id`,`respondent`.`id_gender_head_of_household` AS `respondent_head_of_household_gender_id`,`gender`.`description` AS `respondent_head_of_household_gender`,`focus_group`.`meeting_date_time` AS `focus_group_meeting_date_time`,`focus_group`.`community` AS `focus_group_community`,`focus_group`.`households_total` AS `focus_group_households`,`focus_group`.`households_average_members` AS `focus_group_households_average_members`,`womens_income_activity`.`id` AS `womens_income_activity_id`,`income_activity_type`.`id` AS `income_activity_type_id`,`income_activity_type`.`description` AS `income_activity_type_description`,`income_activity_category`.`id` AS `income_activity_category_id`,`income_activity_category`.`description` AS `income_activity_category_description`,`womens_income_activity`.`pct_womens_income` AS `womens_income_activity_pct_womens_income` from (((((((((`womens_income_activity` left join `respondent` on((`respondent`.`id` = `womens_income_activity`.`id_respondent`))) left join `gender` on((`gender`.`id` = `respondent`.`id_gender_head_of_household`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `project` on((`project`.`id` = `site`.`id_project`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `world_region` on((`world_region`.`id` = `country`.`id_world_region`))) left join `income_activity_type` on((`income_activity_type`.`id` = `womens_income_activity`.`id_income_activity_type`))) left join `income_activity_category` on((`income_activity_category`.`id` = `income_activity_type`.`id_income_activity_category`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `feed_source_values_all`
--

/*!50001 DROP VIEW IF EXISTS `feed_source_values_all`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `feed_source_values_all` AS select `month`.`ordering` AS `month_order`,`month`.`name` AS `name_of_month`,`month`.`ordering` AS `order_of_month`,`feed_source`.`description` AS `resource_type`,((ifnull(`feed_source_availability`.`contribution`,0) * `scale_zero_ten`.`number`) * 0.1) AS `numerical_value_raw`,`focus_group`.`id` AS `focus_group_id`,`site`.`id` AS `site_id`,`site`.`id_country` AS `country_id`,`site`.`id_project` AS `project_id`,`country`.`id_world_region` AS `world_region_id`,`respondent`.`id_user` AS `user_id` from (((((((((`respondent` join `feed_source_availability` on((`respondent`.`id` = `feed_source_availability`.`id_respondent`))) join `month` on((`month`.`id` = `feed_source_availability`.`id_month`))) join `respondent_monthly_statistics` on(((`respondent_monthly_statistics`.`id_respondent` = `respondent`.`id`) and (`respondent_monthly_statistics`.`id_month` = `feed_source_availability`.`id_month`)))) join `scale_zero_ten` on((`respondent_monthly_statistics`.`id_scale_zero_ten` = `scale_zero_ten`.`id`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `focus_group_monthly_statistics` on((`focus_group_monthly_statistics`.`id_month` = `month`.`id`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `feed_source` on((`feed_source_availability`.`id_feed_source` = `feed_source`.`id`))) where (((`respondent`.`exclude` is null) or (`respondent`.`exclude` = 0)) and ((`respondent`.`keep_private` is null) or (`respondent`.`keep_private` = 0) or (`respondent`.`uploaded_at` < (curdate() - interval 1 year)))) group by `respondent`.`id`,`month`.`ordering`,`feed_source`.`description` order by `month`.`ordering`,`feed_source`.`description` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `feed_source_values_user`
--

/*!50001 DROP VIEW IF EXISTS `feed_source_values_user`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `feed_source_values_user` AS select `month`.`ordering` AS `month_order`,`month`.`name` AS `name_of_month`,`month`.`ordering` AS `order_of_month`,`feed_source`.`description` AS `resource_type`,((ifnull(`feed_source_availability`.`contribution`,0) * `scale_zero_ten`.`number`) * 0.1) AS `numerical_value_raw`,`focus_group`.`id` AS `focus_group_id`,`site`.`id` AS `site_id`,`site`.`id_country` AS `country_id`,`site`.`id_project` AS `project_id`,`country`.`id_world_region` AS `world_region_id`,`respondent`.`id_user` AS `user_id` from (((((((((`respondent` join `feed_source_availability` on((`respondent`.`id` = `feed_source_availability`.`id_respondent`))) join `month` on((`month`.`id` = `feed_source_availability`.`id_month`))) join `respondent_monthly_statistics` on(((`respondent_monthly_statistics`.`id_respondent` = `respondent`.`id`) and (`respondent_monthly_statistics`.`id_month` = `feed_source_availability`.`id_month`)))) join `scale_zero_ten` on((`respondent_monthly_statistics`.`id_scale_zero_ten` = `scale_zero_ten`.`id`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `focus_group_monthly_statistics` on((`focus_group_monthly_statistics`.`id_month` = `month`.`id`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `country` on((`country`.`id` = `site`.`id_country`))) left join `feed_source` on((`feed_source_availability`.`id_feed_source` = `feed_source`.`id`))) where ((`respondent`.`exclude` is null) or (`respondent`.`exclude` = 0)) group by `respondent`.`id`,`month`.`ordering`,`feed_source`.`description` order by `month`.`ordering`,`feed_source`.`description` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `fg_feed_availability`
--

/*!50001 DROP VIEW IF EXISTS `fg_feed_availability`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `fg_feed_availability` AS select `focus_group`.`id` AS `id_focus_group`,`focus_group`.`id_site` AS `id_site`,`month`.`ordering` AS `order_of_month`,`feed_source`.`description` AS `resource_type`,avg(((ifnull(`feed_source_availability`.`contribution`,0) * `scale_zero_ten`.`number`) * 0.1)) AS `numerical_value_raw` from ((((((((`feed_source_availability` join `respondent` on((`respondent`.`id` = `feed_source_availability`.`id_respondent`))) join `month` on((`month`.`id` = `feed_source_availability`.`id_month`))) join `respondent_monthly_statistics` on((`respondent_monthly_statistics`.`id_respondent` = `respondent`.`id`))) join `scale_zero_ten` on((`respondent_monthly_statistics`.`id_scale_zero_ten` = `scale_zero_ten`.`id`))) left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `focus_group_monthly_statistics` on((`focus_group_monthly_statistics`.`id_month` = `month`.`id`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `feed_source` on((`feed_source_availability`.`id_feed_source` = `feed_source`.`id`))) group by `focus_group`.`id`,`month`.`id`,`feed_source`.`description` order by NULL */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `interview_respondents_all`
--

/*!50001 DROP VIEW IF EXISTS `interview_respondents_all`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `interview_respondents_all` AS select count(0) AS `number_of`,`focus_group`.`id` AS `focus_group_id`,`site`.`id` AS `site_id`,`site`.`id_country` AS `country_id`,`site`.`id_project` AS `project_id`,`country`.`id_world_region` AS `world_region_id`,`respondent`.`id_user` AS `user_id` from (((`respondent` left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `country` on((`country`.`id` = `site`.`id_country`))) where (((`respondent`.`exclude` is null) or (`respondent`.`exclude` = 0)) and ((`respondent`.`keep_private` is null) or (`respondent`.`keep_private` = 0) or (`respondent`.`uploaded_at` < (curdate() - interval 1 year)))) group by `focus_group`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `interview_respondents_user`
--

/*!50001 DROP VIEW IF EXISTS `interview_respondents_user`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `interview_respondents_user` AS select count(0) AS `number_of`,`focus_group`.`id` AS `focus_group_id`,`site`.`id` AS `site_id`,`site`.`id_country` AS `country_id`,`site`.`id_project` AS `project_id`,`country`.`id_world_region` AS `world_region_id`,`respondent`.`id_user` AS `user_id` from (((`respondent` left join `focus_group` on((`focus_group`.`id` = `respondent`.`id_focus_group`))) left join `site` on((`site`.`id` = `focus_group`.`id_site`))) left join `country` on((`country`.`id` = `site`.`id_country`))) where ((`respondent`.`exclude` is null) or (`respondent`.`exclude` = 0)) group by `focus_group`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `nutrition_stats_output`
--

/*!50001 DROP VIEW IF EXISTS `nutrition_stats_output`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `nutrition_stats_output` AS select `nutrition_stats_stage2`.`respondent_id` AS `respondent_id`,`nutrition_stats_stage`.`dm_crop_residue` AS `crop_residue_dm`,`nutrition_stats_stage`.`me_crop_residue` AS `crop_residue_me`,`nutrition_stats_stage`.`cp_crop_residue` AS `crop_residue_cp`,`nutrition_stats_stage`.`dm_cultivated_fodder` AS `cultivated_fodder_dm`,`nutrition_stats_stage`.`me_cultivated_fodder` AS `cultivated_fodder_me`,`nutrition_stats_stage`.`cp_cultivated_fodder` AS `cultivated_fodder_cp`,`nutrition_stats_stage`.`dm_purchased_feed` AS `purchased_feed_dm`,`nutrition_stats_stage`.`me_purchased_feed` AS `purchased_feed_me`,`nutrition_stats_stage`.`cp_purchased_feed` AS `purchased_feed_cp`,`nutrition_stats_stage2`.`grazing_dm` AS `grazing_dm`,(`nutrition_stats_stage2`.`grazing_dm` * `site`.`grazing_metabolisable_energy`) AS `grazing_me`,((`nutrition_stats_stage2`.`grazing_dm` * `site`.`grazing_crude_protein_percentage`) * 0.01) AS `grazing_cp`,`nutrition_stats_stage2`.`collected_fodder_dm` AS `collected_fodder_dm`,(`nutrition_stats_stage2`.`collected_fodder_dm` * `site`.`collected_fodder_metabolisable_energy`) AS `collected_fodder_me`,((`nutrition_stats_stage2`.`collected_fodder_dm` * `site`.`collected_fodder_crude_protein_percentage`) * 0.01) AS `collected_fodder_cp` from ((((`respondent` left join `nutrition_stats_stage2` on((`nutrition_stats_stage2`.`respondent_id` = `respondent`.`id`))) left join `nutrition_stats_stage` on((`nutrition_stats_stage`.`respondent_id` = `respondent`.`id`))) left join `focus_group` on((`respondent`.`id_focus_group` = `focus_group`.`id`))) left join `site` on((`focus_group`.`id_site` = `site`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `nutrition_stats_stage`
--

/*!50001 DROP VIEW IF EXISTS `nutrition_stats_stage`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `nutrition_stats_stage` AS select `respondent`.`id` AS `respondent_id`,(ifnull(`respondent`.`diet_percent_grazing`,0) * 0.01) AS `grazing_percentage`,(ifnull(`respondent`.`diet_percent_collected_fodder`,0) * 0.01) AS `collected_fodder_percentage`,ifnull(sum(`crop_residue_stats`.`crop_residue_dm`),0) AS `dm_crop_residue`,ifnull(sum((`crop_residue_stats`.`crop_residue_dm` * `crop_residue_stats`.`ratio_me`)),0) AS `me_crop_residue`,ifnull(sum((`crop_residue_stats`.`crop_residue_dm` * `crop_residue_stats`.`ratio_cp`)),0) AS `cp_crop_residue`,ifnull(sum(`cultivated_fodder_stats`.`cultivated_fodder_dm`),0) AS `dm_cultivated_fodder`,ifnull(sum((`cultivated_fodder_stats`.`cultivated_fodder_dm` * `cultivated_fodder_stats`.`ratio_me`)),0) AS `me_cultivated_fodder`,ifnull(sum((`cultivated_fodder_stats`.`cultivated_fodder_dm` * `cultivated_fodder_stats`.`ratio_cp`)),0) AS `cp_cultivated_fodder`,ifnull(sum(`purchased_feed_stats`.`purchased_feed_dm`),0) AS `dm_purchased_feed`,ifnull(sum((`purchased_feed_stats`.`purchased_feed_dm` * `purchased_feed_stats`.`ratio_me`)),0) AS `me_purchased_feed`,ifnull(sum((`purchased_feed_stats`.`purchased_feed_dm` * `purchased_feed_stats`.`ratio_cp`)),0) AS `cp_purchased_feed` from (((`respondent` left join `crop_residue_stats` on((`respondent`.`id` = `crop_residue_stats`.`respondent_id`))) left join `purchased_feed_stats` on((`respondent`.`id` = `purchased_feed_stats`.`respondent_id`))) left join `cultivated_fodder_stats` on((`respondent`.`id` = `cultivated_fodder_stats`.`respondent_id`))) group by `respondent`.`id` order by `respondent`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `nutrition_stats_stage2`
--

/*!50001 DROP VIEW IF EXISTS `nutrition_stats_stage2`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `nutrition_stats_stage2` AS select `nutrition_stats_stage`.`respondent_id` AS `respondent_id`,`nutrition_stats_stage`.`dm_crop_residue` AS `crop_residue_dm`,`nutrition_stats_stage`.`dm_cultivated_fodder` AS `cultivated_fodder_dm`,`nutrition_stats_stage`.`dm_purchased_feed` AS `purchased_feed_dm`,((`nutrition_stats_stage`.`grazing_percentage` * ((`nutrition_stats_stage`.`dm_crop_residue` + `nutrition_stats_stage`.`dm_cultivated_fodder`) + `nutrition_stats_stage`.`dm_purchased_feed`)) / (1 - (`nutrition_stats_stage`.`grazing_percentage` + `nutrition_stats_stage`.`collected_fodder_percentage`))) AS `grazing_dm`,((`nutrition_stats_stage`.`collected_fodder_percentage` * ((`nutrition_stats_stage`.`dm_crop_residue` + `nutrition_stats_stage`.`dm_cultivated_fodder`) + `nutrition_stats_stage`.`dm_purchased_feed`)) / (1 - (`nutrition_stats_stage`.`grazing_percentage` + `nutrition_stats_stage`.`collected_fodder_percentage`))) AS `collected_fodder_dm` from `nutrition_stats_stage` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `purchased_feed_stats`
--

/*!50001 DROP VIEW IF EXISTS `purchased_feed_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `purchased_feed_stats` AS select `purchased_feed`.`id` AS `purchased_feed_id`,`purchased_feed`.`id_respondent` AS `respondent_id`,ifnull(((((`purchased_feed`.`quantity_purchased` * `unit_mass_weight`.`conversion_kg`) * `purchased_feed`.`purchases_per_year`) * `purchased_feed_type`.`content_percent_dry_matter`) * 0.01),0) AS `purchased_feed_dm`,`purchased_feed_type`.`content_metabolisable_energy` AS `ratio_me`,(`purchased_feed_type`.`content_crude_protein` * 0.01) AS `ratio_cp` from ((`purchased_feed` left join `unit_mass_weight` on((`unit_mass_weight`.`id` = `purchased_feed`.`id_unit_mass_weight`))) left join `purchased_feed_type` on((`purchased_feed_type`.`id` = `purchased_feed`.`id_purchased_feed_type`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `rainfall`
--

/*!50001 DROP VIEW IF EXISTS `rainfall`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sonata_admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `rainfall` AS select `focus_group_monthly_statistics`.`id_focus_group` AS `id_focus_group`,`month`.`ordering` AS `order_of_month`,'Rainfall' AS `resource_type`,`scale_zero_five`.`number` AS `numerical_value_raw` from ((`focus_group_monthly_statistics` left join `month` on((`focus_group_monthly_statistics`.`id_month` = `month`.`id`))) left join `scale_zero_five` on((`focus_group_monthly_statistics`.`id_scale_zero_five` = `scale_zero_five`.`id`))) where (`scale_zero_five`.`number` is not null) group by `focus_group_monthly_statistics`.`id_focus_group`,`month`.`ordering` order by NULL */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50112 SET @disable_bulk_load = IF (@is_rocksdb_supported, 'SET SESSION rocksdb_bulk_load = @old_rocksdb_bulk_load', 'SET @dummy_rocksdb_bulk_load = 0') */;
/*!50112 PREPARE s FROM @disable_bulk_load */;
/*!50112 EXECUTE s */;
/*!50112 DEALLOCATE PREPARE s */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-05-01  3:15:08
