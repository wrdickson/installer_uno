<?php

$drop_accounts_sql = "DROP TABLE IF EXISTS `accounts`";

$create_accounts_sql = "CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` text NOT NULL,
  `permission` int(11) NOT NULL,
  `roles` json NOT NULL,
  `email` varchar(144) NOT NULL,
  `registered` datetime NOT NULL,
  `last_activity` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_customers_sql = "DROP TABLE IF EXISTS `customers`";

$create_customers_sql = "CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(144) NOT NULL,
  `first_name` varchar(144) NOT NULL,
  `email` varchar(144) NOT NULL DEFAULT '',
  `phone` varchar(45) NOT NULL DEFAULT '',
  `address_1` varchar(144) NOT NULL DEFAULT '',
  `address_2` varchar(144) NOT NULL DEFAULT '',
  `city` varchar(144) NOT NULL DEFAULT '',
  `region` varchar(144) NOT NULL DEFAULT '',
  `country` varchar(144) NOT NULL DEFAULT '',
  `postal_code` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_folios_sql = "DROP TABLE IF EXISTS `folios`";

$create_folios_sql = "CREATE TABLE `folios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer` int(11) NOT NULL,
  PRIMARY KEY (`id`,`customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_options_sql = "DROP TABLE IF EXISTS `options`";

$create_options_sql = "CREATE TABLE `options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(144) NOT NULL,
  `option_value` longtext  NOT NULL,
  `autoload` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_payment_types_sql = "DROP TABLE IF EXISTS `payment_types`";

$create_payment_types_sql = "CREATE TABLE `payment_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_title` varchar(45) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_payments_sql = "DROP TABLE IF EXISTS `payments`";

$create_payments_sql = "CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folio` int(11) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `posted_by` int(11) NOT NULL,
  `datetime_posted` datetime NOT NULL,
  `total` decimal(18,2) NOT NULL,
  PRIMARY KEY (`id`,`folio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_reservations_sql = "DROP TABLE IF EXISTS `reservations`";

$create_reservations_sql = "CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folio` int(11) NOT NULL,
  `is_assigned` tinyint(4) NOT NULL,
  `space_type_pref` int(11) NOT NULL,
  `space_id` int(11) NOT NULL,
  `space_code` json NOT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `people` int(11) NOT NULL,
  `beds` int(11) NOT NULL,
  `history` json NOT NULL,
  `status` int(11) NOT NULL,
  `notes` json NOT NULL,
  PRIMARY KEY (`id`,`folio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_root_spaces_sql = "DROP TABLE IF EXISTS `root_spaces`";

$create_root_spaces_sql = "CREATE TABLE `root_spaces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `space_type` int(11) NOT NULL,
  `title` varchar(45) NOT NULL,
  `child_of` int(11) NOT NULL,
  `show_children` tinyint(4) NOT NULL,
  `people` int(11) NOT NULL,
  `beds` int(11) NOT NULL,
  `display_order` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `is_unassigned` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$insert_unassigned_space_sql = "INSERT INTO `root_spaces` (`space_type`, `title`, `child_of`, `show_children`, `people`, `beds`, `display_order`, `is_active`, `is_unassigned`) VALUES ('0', 'Not Assigned', '0', '0', '1000000', '1000000', '1', '1', '1');"; 

$drop_sale_items_sql = "DROP TABLE IF EXISTS `sale_items`";

$create_sale_items_sql = "CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folio` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `sale_type` int(11) NOT NULL,
  `posted_by` int(11) NOT NULL,
  `description` tinytext NOT NULL,
  `sale_datetime` datetime NOT NULL,
  `sale_quantity` int(11) NOT NULL,
  `sale_price` decimal(18,2) NOT NULL,
  `sale_subtotal` decimal(18,2) NOT NULL,
  `sale_tax` decimal(18,2) NOT NULL,
  `sale_total` decimal(18,2) NOT NULL,
  `tax_types` json NOT NULL,
  `tax_spread` json NOT NULL,
  PRIMARY KEY (`id`,`folio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_sale_type_groups_sql = "DROP TABLE IF EXISTS `sale_type_groups`";

$create_sale_type_groups_sql = "CREATE TABLE `sale_type_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `display_order` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_sale_types_sql = "DROP TABLE IF EXISTS `sale_types`";

$create_sale_types_sql = "CREATE TABLE `sale_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `tax_types` json NOT NULL,
  `sale_type_group` int(11) NOT NULL,
  `is_fixed_price` tinyint(4) NOT NULL,
  `fixed_price` decimal(18,2) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_space_types_sql = "DROP TABLE IF EXISTS `space_types`";

$create_space_types_sql = "CREATE TABLE `space_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$drop_tax_types_sql = "DROP TABLE IF EXISTS `tax_types`";

$create_tax_types_sql = "CREATE TABLE `tax_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_title` varchar(45) NOT NULL,
  `is_current` tinyint(4) NOT NULL,
  `tax_rate` decimal(19,4) NOT NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
