/*
Navicat MySQL Data Transfer

Source Server         : OxoAwards
Source Server Version : 50546
Source Host           : 192.168.1.222:3306
Source Database       : awards

Target Server Type    : MYSQL
Target Server Version : 50546
File Encoding         : 65001

Date: 2016-03-29 17:51:32
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for billing_entries_categories
-- ----------------------------
DROP TABLE IF EXISTS `billing_entries_categories`;
CREATE TABLE `billing_entries_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `billing_id` int(10) unsigned NOT NULL,
  `entry_id` int(10) unsigned DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `Billing_id` (`billing_id`) USING BTREE,
  KEY `Entry_id` (`entry_id`) USING BTREE,
  KEY `Category_id` (`category_id`) USING BTREE,
  CONSTRAINT `Billing_FK` FOREIGN KEY (`billing_id`) REFERENCES `billings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Category_FK` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `Entry_FK` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for billings
-- ----------------------------
DROP TABLE IF EXISTS `billings`;
CREATE TABLE `billings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `contest_id` int(10) unsigned NOT NULL,
  `transaction_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `method` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `payment_data` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL,
  `error` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` decimal(9,2) NOT NULL,
  `currency` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `User_id` (`user_id`) USING BTREE,
  KEY `Contest_id` (`contest_id`) USING BTREE,
  CONSTRAINT `Billing_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Billing_User_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `contest_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `order` tinyint(3) NOT NULL DEFAULT '0',
  `image` text COLLATE utf8_unicode_ci,
  `final` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `trans` text COLLATE utf8_unicode_ci,
  `price` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `Castegory_Contest_FK_idx` (`contest_id`) USING BTREE,
  KEY `parent_id_UNIQUE` (`parent_id`) USING BTREE,
  KEY `template_id_FK` (`template_id`) USING BTREE,
  CONSTRAINT `Category_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Category_Parent_id` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `Category_Template_FK` FOREIGN KEY (`template_id`) REFERENCES `entry_metadata_templates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2953 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for category_config_type
-- ----------------------------
DROP TABLE IF EXISTS `category_config_type`;
CREATE TABLE `category_config_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inscription_type_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `CategoriesInscriptionTypesConfig_InscriptionTypes_FK` (`inscription_type_id`) USING BTREE,
  KEY `CategoriesInscriptionTypesConfig_Categories_FK` (`category_id`) USING BTREE,
  CONSTRAINT `CategoriesInscriptionTypesConfig_Categories_FK` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CategoriesInscriptionTypesConfig_InscriptionTypes_FK` FOREIGN KEY (`inscription_type_id`) REFERENCES `inscription_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for clients
-- ----------------------------
DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  `image` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for contest_assets
-- ----------------------------
DROP TABLE IF EXISTS `contest_assets`;
CREATE TABLE `contest_assets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `type` tinyint(2) unsigned NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `content` longtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ContestAssets_Contest_FK` (`contest_id`) USING BTREE,
  CONSTRAINT `ContestRepo_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for contest_file_log
-- ----------------------------
DROP TABLE IF EXISTS `contest_file_log`;
CREATE TABLE `contest_file_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contest_file_id` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `msg` varchar(250) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for contest_file_versions
-- ----------------------------
DROP TABLE IF EXISTS `contest_file_versions`;
CREATE TABLE `contest_file_versions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `format_id` int(10) unsigned DEFAULT NULL,
  `contest_file_id` int(10) unsigned NOT NULL,
  `extension` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `sizes` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `duration` decimal(12,2) DEFAULT NULL,
  `source` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `percentage` float(5,2) DEFAULT NULL,
  `eta` time DEFAULT NULL,
  `description` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cdn_status` tinyint(3) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FileVersion_Format_FK_idx` (`format_id`) USING BTREE,
  KEY `FileVersion_File_FK_idx` (`contest_file_id`) USING BTREE,
  CONSTRAINT `FileVersion_Contest_File_FK` FOREIGN KEY (`contest_file_id`) REFERENCES `contest_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FileVersion_Format_FK` FOREIGN KEY (`format_id`) REFERENCES `formats` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=392 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for contest_files
-- ----------------------------
DROP TABLE IF EXISTS `contest_files`;
CREATE TABLE `contest_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `contest_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `tech_status` tinyint(1) NOT NULL DEFAULT '0',
  `thumbs` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `File_Code_Unique` (`code`) USING BTREE,
  KEY `File_Contest_FK_idx` (`contest_id`) USING BTREE,
  KEY `File_User_FK_idx` (`user_id`) USING BTREE,
  CONSTRAINT `ContestFile_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ContestFile_User_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for contest_formats
-- ----------------------------
DROP TABLE IF EXISTS `contest_formats`;
CREATE TABLE `contest_formats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `format_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ContestFormat_Contest_FK_idx` (`contest_id`) USING BTREE,
  KEY `ContestFormat_Format_FK_idx` (`format_id`) USING BTREE,
  CONSTRAINT `ContestFormat_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ContestFormat_Format_FK` FOREIGN KEY (`format_id`) REFERENCES `formats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for contests
-- ----------------------------
DROP TABLE IF EXISTS `contests`;
CREATE TABLE `contests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `template` text COLLATE utf8_unicode_ci,
  `limits` text COLLATE utf8_unicode_ci,
  `sizes` text COLLATE utf8_unicode_ci,
  `billing` text COLLATE utf8_unicode_ci,
  `single_category` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `public` tinyint(1) NOT NULL,
  `start_at` datetime NOT NULL,
  `finish_at` datetime NOT NULL,
  `inscription_public` tinyint(1) NOT NULL,
  `inscription_start_at` datetime DEFAULT NULL,
  `inscription_deadline1_at` datetime DEFAULT NULL,
  `inscription_deadline2_at` datetime DEFAULT NULL,
  `voters_public` tinyint(1) NOT NULL,
  `voters_start_at` datetime DEFAULT NULL,
  `voters_deadline1_at` datetime DEFAULT NULL,
  `voters_deadline2_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `User_FK_idx` (`user_id`) USING BTREE,
  CONSTRAINT `Contest_User_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for entries
-- ----------------------------
DROP TABLE IF EXISTS `entries`;
CREATE TABLE `entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  `error` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `Entry_Contest_FK_idx` (`contest_id`) USING BTREE,
  KEY `User_FK_idx` (`user_id`) USING BTREE,
  CONSTRAINT `Entry_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Entry_User_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22219 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for entry_categories
-- ----------------------------
DROP TABLE IF EXISTS `entry_categories`;
CREATE TABLE `entry_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `entry_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `EntryCategories_Entry_FK` (`entry_id`) USING BTREE,
  KEY `EntryCategories_Category_FK` (`category_id`) USING BTREE,
  CONSTRAINT `EntryCategories_Category_FK` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `EntryCategories_Entry_FK` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1544 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for entry_log
-- ----------------------------
DROP TABLE IF EXISTS `entry_log`;
CREATE TABLE `entry_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `msg` varchar(250) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for entry_metadata_config_template
-- ----------------------------
DROP TABLE IF EXISTS `entry_metadata_config_template`;
CREATE TABLE `entry_metadata_config_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_metadata_field_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `order` tinyint(1) NOT NULL DEFAULT '0',
  `config` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `entry_metadata_value_id` (`entry_metadata_field_id`) USING BTREE,
  KEY `entry_metadata_template_id` (`template_id`) USING BTREE,
  CONSTRAINT `ContestMDConfigMD` FOREIGN KEY (`entry_metadata_field_id`) REFERENCES `entry_metadata_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `EntryMDConfigTemplate` FOREIGN KEY (`template_id`) REFERENCES `entry_metadata_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=697 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for entry_metadata_fields
-- ----------------------------
DROP TABLE IF EXISTS `entry_metadata_fields`;
CREATE TABLE `entry_metadata_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `label` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `trans` text COLLATE utf8_unicode_ci,
  `type` tinyint(2) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `order` int(4) NOT NULL DEFAULT '0',
  `config` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ContestMD_Contest_FK_idx` (`contest_id`) USING BTREE,
  CONSTRAINT `ContestMD_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=292 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for entry_metadata_files
-- ----------------------------
DROP TABLE IF EXISTS `entry_metadata_files`;
CREATE TABLE `entry_metadata_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_file_id` int(10) unsigned NOT NULL,
  `entry_metadata_value_id` int(10) unsigned NOT NULL,
  `order` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `MetadataHasFile_ContestFile_FK` (`contest_file_id`) USING BTREE,
  KEY `entry_metadata_value_id` (`entry_metadata_value_id`) USING BTREE,
  CONSTRAINT `entry_metadata_files_ibfk_1` FOREIGN KEY (`entry_metadata_value_id`) REFERENCES `entry_metadata_values` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `MetadataHasFile_ContestFile_FK` FOREIGN KEY (`contest_file_id`) REFERENCES `contest_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for entry_metadata_templates
-- ----------------------------
DROP TABLE IF EXISTS `entry_metadata_templates`;
CREATE TABLE `entry_metadata_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `trans` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `IncriptionTypes_Contest_FK` (`contest_id`) USING BTREE,
  CONSTRAINT `entry_metadata_templates_ibfk_1` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for entry_metadata_values
-- ----------------------------
DROP TABLE IF EXISTS `entry_metadata_values`;
CREATE TABLE `entry_metadata_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) unsigned NOT NULL,
  `entry_metadata_field_id` int(10) unsigned NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `Metadata_Entry_FK_idx` (`entry_id`) USING BTREE,
  KEY `Metadata_Field_FK_idx` (`entry_metadata_field_id`) USING BTREE,
  CONSTRAINT `Metadata_Entry_FK` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Metadata_Field_FK` FOREIGN KEY (`entry_metadata_field_id`) REFERENCES `entry_metadata_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12814 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for formats
-- ----------------------------
DROP TABLE IF EXISTS `formats`;
CREATE TABLE `formats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(1) NOT NULL,
  `extension` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `command` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for inscription_metadata_config_type
-- ----------------------------
DROP TABLE IF EXISTS `inscription_metadata_config_type`;
CREATE TABLE `inscription_metadata_config_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inscription_metadata_field_id` int(10) unsigned NOT NULL,
  `inscription_type_id` int(10) unsigned NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `order` tinyint(1) NOT NULL DEFAULT '0',
  `config` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ContestsIncriptionMetadataConfig_InscriptionTypes_FK` (`inscription_type_id`) USING BTREE,
  KEY `IncriptionMetadataConfig_MetadataId` (`inscription_metadata_field_id`) USING BTREE,
  CONSTRAINT `IncriptionMetadataConfig_InscriptionTypes_FK` FOREIGN KEY (`inscription_type_id`) REFERENCES `inscription_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `IncriptionMetadataConfig_MetadataField_FK` FOREIGN KEY (`inscription_metadata_field_id`) REFERENCES `inscription_metadata_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for inscription_metadata_fields
-- ----------------------------
DROP TABLE IF EXISTS `inscription_metadata_fields`;
CREATE TABLE `inscription_metadata_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `role` tinyint(1) NOT NULL,
  `label` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `trans` text COLLATE utf8_unicode_ci,
  `type` tinyint(2) NOT NULL,
  `order` tinyint(1) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `config` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ContestIncriptionMd_Contest_FK_idx` (`contest_id`) USING BTREE,
  CONSTRAINT `ContestIncriptionMd_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for inscription_metadata_values
-- ----------------------------
DROP TABLE IF EXISTS `inscription_metadata_values`;
CREATE TABLE `inscription_metadata_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inscription_id` int(10) unsigned NOT NULL,
  `inscription_metadata_field_id` int(10) unsigned NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `InscriptionMD_Inscription_idx` (`inscription_id`) USING BTREE,
  KEY `inscription_metadata_field_id` (`inscription_metadata_field_id`) USING BTREE,
  CONSTRAINT `InscriptionMD_Inscription_FK` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inscription_metadata_values_ibfk_1` FOREIGN KEY (`inscription_metadata_field_id`) REFERENCES `inscription_metadata_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for inscription_types
-- ----------------------------
DROP TABLE IF EXISTS `inscription_types`;
CREATE TABLE `inscription_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `role` tinyint(1) NOT NULL,
  `name` varchar(200) NOT NULL,
  `trans` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `public` tinyint(1) DEFAULT '0',
  `start_at` datetime DEFAULT NULL,
  `deadline1_at` datetime DEFAULT NULL,
  `deadline2_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IncriptionTypes_Contest_FK` (`contest_id`) USING BTREE,
  CONSTRAINT `IncriptionTypes_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for inscriptions
-- ----------------------------
DROP TABLE IF EXISTS `inscriptions`;
CREATE TABLE `inscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `contest_id` int(10) unsigned NOT NULL,
  `inscription_type_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(320) COLLATE utf8_unicode_ci NOT NULL,
  `role` tinyint(2) NOT NULL DEFAULT '0',
  `permits` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_at` datetime DEFAULT NULL,
  `deadline1_at` datetime DEFAULT NULL,
  `deadline2_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Inscription_User_FK_idx` (`user_id`) USING BTREE,
  KEY `Inscription_Contest_FK_idx` (`contest_id`) USING BTREE,
  KEY `Inscription_Type_id` (`inscription_type_id`) USING BTREE,
  CONSTRAINT `Inscription_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Inscription_Type_id` FOREIGN KEY (`inscription_type_id`) REFERENCES `inscription_types` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `Inscription_User_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for invitations
-- ----------------------------
DROP TABLE IF EXISTS `invitations`;
CREATE TABLE `invitations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `email` varchar(320) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `sent` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `contest_id` (`contest_id`) USING BTREE,
  CONSTRAINT `invitations_ibfk_1` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for password_reminders
-- ----------------------------
DROP TABLE IF EXISTS `password_reminders`;
CREATE TABLE `password_reminders` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` text COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  UNIQUE KEY `sessions_id_unique` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for slides
-- ----------------------------
DROP TABLE IF EXISTS `slides`;
CREATE TABLE `slides` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `description` text,
  `image` varchar(64) DEFAULT NULL,
  `link` varchar(256) DEFAULT NULL,
  `linkText` varchar(256) DEFAULT NULL,
  `class` varchar(32) DEFAULT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(320) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `verify_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `verified` tinyint(1) DEFAULT '0',
  `super` tinyint(1) DEFAULT '0',
  `last_seen_at` timestamp DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for users_services
-- ----------------------------
DROP TABLE IF EXISTS `users_services`;
CREATE TABLE `users_services` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `service` varchar(32) NOT NULL,
  `service_id` varchar(128) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `fk_User_id` (`user_id`) USING BTREE,
  KEY `fk_Service` (`service`) USING BTREE,
  KEY `fk_Service_id` (`service_id`) USING BTREE,
  CONSTRAINT `fk_User_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for votes
-- ----------------------------
DROP TABLE IF EXISTS `votes`;
CREATE TABLE `votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `voting_user_id` int(10) unsigned NOT NULL,
  `entry_category_id` int(10) unsigned NOT NULL,
  `vote` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `Vote_VotingUser_FK_idx` (`voting_user_id`) USING BTREE,
  KEY `Vote_Entry_FK_idx` (`entry_category_id`) USING BTREE,
  CONSTRAINT `Vote_EntryCategory_FK` FOREIGN KEY (`entry_category_id`) REFERENCES `entry_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Vote_VotingUser_FK` FOREIGN KEY (`voting_user_id`) REFERENCES `voting_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for voting_categories
-- ----------------------------
DROP TABLE IF EXISTS `voting_categories`;
CREATE TABLE `voting_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `voting_session_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `vote_type` tinyint(1) NOT NULL,
  `vote_config` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `VotingCategory_VotingSession_FK_idx` (`voting_session_id`) USING BTREE,
  KEY `VotingCategory_Category_FK_idx` (`category_id`) USING BTREE,
  CONSTRAINT `VotingCategory_Category_FK` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `VotingCategory_VotingSession_FK` FOREIGN KEY (`voting_session_id`) REFERENCES `voting_sessions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for voting_sessions
-- ----------------------------
DROP TABLE IF EXISTS `voting_sessions`;
CREATE TABLE `voting_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(10) unsigned NOT NULL,
  `code` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `config` text COLLATE utf8_unicode_ci,
  `vote_type` tinyint(2) NOT NULL,
  `start_at` datetime NOT NULL,
  `finish_at` datetime NOT NULL,
  `finish_at2` datetime DEFAULT NULL,
  `public` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `VotingSession_Contest_FK_idx` (`contest_id`) USING BTREE,
  CONSTRAINT `VotingSession_Contest_FK` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for voting_users
-- ----------------------------
DROP TABLE IF EXISTS `voting_users`;
CREATE TABLE `voting_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inscription_id` int(10) unsigned NOT NULL,
  `voting_session_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `VotingUser_Inscription_FK_idx` (`inscription_id`) USING BTREE,
  KEY `VotingUser_Voting_FK_idx` (`voting_session_id`) USING BTREE,
  CONSTRAINT `VotingUser_Inscription_FK` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `VotingUser_VotingSession_FK` FOREIGN KEY (`voting_session_id`) REFERENCES `voting_sessions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;
