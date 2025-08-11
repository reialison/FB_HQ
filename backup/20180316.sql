#
# TABLE STRUCTURE FOR: araneta
#

DROP TABLE IF EXISTS araneta;

CREATE TABLE `araneta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lessee_name` varchar(20) DEFAULT NULL,
  `lessee_no` varchar(20) DEFAULT NULL,
  `space_code` varchar(20) DEFAULT '',
  `file_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

INSERT INTO araneta (`id`, `lessee_name`, `lessee_no`, `space_code`, `file_path`) VALUES (1, 'HAPCHAN', '30436', '141040', 'C:/ARANETA');


#
# TABLE STRUCTURE FOR: ayala
#

DROP TABLE IF EXISTS ayala;

CREATE TABLE `ayala` (
  `id` int(11) NOT NULL,
  `contract_no` varchar(150) DEFAULT NULL,
  `store_name` varchar(150) DEFAULT NULL,
  `xxx_no` varchar(150) DEFAULT NULL,
  `dbf_tenant_name` varchar(150) DEFAULT NULL,
  `dbf_path` varchar(150) DEFAULT NULL,
  `text_file_path` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO ayala (`id`, `contract_no`, `store_name`, `xxx_no`, `dbf_tenant_name`, `dbf_path`, `text_file_path`) VALUES (1, '6000000000025', 'BARCINO UNIT 20', 'AYA', 'BARCINO UNIT 20', 'C:/AYALA/', 'C:/AYALA/');


#
# TABLE STRUCTURE FOR: batch_etl
#

DROP TABLE IF EXISTS batch_etl;

CREATE TABLE `batch_etl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: branch_details
#

DROP TABLE IF EXISTS branch_details;

CREATE TABLE `branch_details` (
  `branch_id` int(11) NOT NULL AUTO_INCREMENT,
  `res_id` int(11) DEFAULT NULL,
  `branch_code` varchar(255) DEFAULT NULL,
  `branch_name` varchar(55) DEFAULT NULL,
  `branch_desc` varchar(150) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `delivery_no` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `base_location` varchar(100) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `inactive` tinyint(4) DEFAULT '0',
  `tin` varchar(255) DEFAULT NULL,
  `machine_no` varchar(255) DEFAULT NULL,
  `bir` varchar(255) DEFAULT NULL,
  `permit_no` varchar(255) DEFAULT NULL,
  `serial` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `store_open` time DEFAULT NULL,
  `store_close` time DEFAULT NULL,
  `rob_tenant_code` varchar(150) DEFAULT NULL,
  `rob_path` varchar(150) DEFAULT NULL,
  `rob_username` varchar(150) DEFAULT NULL,
  `rob_password` varchar(150) DEFAULT NULL,
  `accrdn` varchar(150) DEFAULT NULL,
  `rec_footer` varchar(255) DEFAULT NULL,
  `pos_footer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO branch_details (`branch_id`, `res_id`, `branch_code`, `branch_name`, `branch_desc`, `contact_no`, `delivery_no`, `address`, `base_location`, `currency`, `image`, `inactive`, `tin`, `machine_no`, `bir`, `permit_no`, `serial`, `email`, `website`, `store_open`, `store_close`, `rob_tenant_code`, `rob_path`, `rob_username`, `rob_password`, `accrdn`, `rec_footer`, `pos_footer`) VALUES (1, 1, 'POINTONE0001', 'Barcino Wine Resto Bar', 'Tarraco Group Inc.', '', '', 'Ayala 30th Ortigas', NULL, 'PHP', 'layout.jpg', 0, '006-884-753-008', '17030814080609288', '0', 'FP032017-043-0116631-00008', 'P1BRCN001', '', '', '09:00:00', '06:30:00', '1234', '190.125.220.1', 'mag15836hap', 'maghapex', '43A0085434442014110212', 'This serves as your Official Receipt.<br>Thank you and Please come again.', '');


#
# TABLE STRUCTURE FOR: branch_menus
#

DROP TABLE IF EXISTS branch_menus;

CREATE TABLE `branch_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `menu_code` varchar(15) DEFAULT NULL,
  `menu_name` varchar(25) DEFAULT NULL,
  `reg_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: cashout_details
#

DROP TABLE IF EXISTS cashout_details;

CREATE TABLE `cashout_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cashout_detail_id` int(11) DEFAULT NULL,
  `cashout_id` int(11) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `denomination` varchar(150) DEFAULT '0',
  `reference` varchar(150) DEFAULT NULL,
  `total` double DEFAULT '0',
  `pos_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: cashout_entries
#

DROP TABLE IF EXISTS cashout_entries;

CREATE TABLE `cashout_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cashout_id` int(11) NOT NULL,
  `user_id` varchar(45) NOT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `drawer_amount` varchar(255) DEFAULT NULL,
  `count_amount` double DEFAULT NULL,
  `trans_date` datetime NOT NULL,
  `pos_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: categories
#

DROP TABLE IF EXISTS categories;

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `inactive` int(11) DEFAULT '0',
  `master_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: charges
#

DROP TABLE IF EXISTS charges;

CREATE TABLE `charges` (
  `charge_id` int(11) NOT NULL AUTO_INCREMENT,
  `charge_code` varchar(22) DEFAULT NULL,
  `charge_name` varchar(55) DEFAULT NULL,
  `charge_amount` double DEFAULT NULL,
  `absolute` tinyint(1) DEFAULT '0',
  `no_tax` tinyint(1) DEFAULT '0',
  `inactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`charge_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO charges (`charge_id`, `charge_code`, `charge_name`, `charge_amount`, `absolute`, `no_tax`, `inactive`) VALUES (1, 'SCHG', 'Service Charge', '10', 0, 1, 0);
INSERT INTO charges (`charge_id`, `charge_code`, `charge_name`, `charge_amount`, `absolute`, `no_tax`, `inactive`) VALUES (2, 'DCHG', 'Delivery Charge', '5', 0, 1, 0);
INSERT INTO charges (`charge_id`, `charge_code`, `charge_name`, `charge_amount`, `absolute`, `no_tax`, `inactive`) VALUES (3, 'HFHG', 'Handling Fee', '10', 0, 1, 0);


#
# TABLE STRUCTURE FOR: company
#

DROP TABLE IF EXISTS company;

CREATE TABLE `company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) DEFAULT NULL,
  `contact_no` varchar(55) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `tin` varchar(100) DEFAULT NULL,
  `fiscal_year` int(11) DEFAULT NULL,
  `theme` varchar(55) DEFAULT 'blue',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: conversation_messages
#

DROP TABLE IF EXISTS conversation_messages;

CREATE TABLE `conversation_messages` (
  `con_msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `con_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `msg` longtext,
  `file` longblob,
  `datetime` datetime DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`con_msg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (1, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus dictum sapien eget nunc viverra, nec consequat lectus hendrerit. Etiam vel ante gravida, pellentesque ex nec, pretium neque. Pellentesque finibus purus diam, ac condimentum augue fermentum et. Ut neque nisi, hendrerit id laoreet fermentum, condimentum at dolor. Pellentesque aliquet tellus quis ullamcorper maximus. Donec finibus lectus sem, id pulvinar lectus pulvinar ut. ', NULL, '2015-05-06 10:57:25', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (3, 3, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus dictum sapien eget nunc viverra, nec consequat lectus hendrerit. Etiam vel ante gravida, pellentesque ex nec, pretium neque. Pellentesque finibus purus diam, ac condimentum augue fermentum et. Ut neque nisi, hendrerit id laoreet fermentum, condimentum at dolor. Pellentesque aliquet tellus quis ullamcorper maximus. Donec finibus lectus sem, id pulvinar lectus pul', NULL, '2015-05-06 12:28:55', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (4, 3, 1, 'tristique, odio id scelerisque sollicitudin, diam massa lobortis enim, in faucibus nisi leo at dui. Proin ornare eleifend risus, ut condimentum metus porttitor non. Donec', NULL, '2015-05-06 12:34:46', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (5, 1, 1, 'asdas asd ', NULL, '2015-05-06 12:40:24', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (6, 1, 1, 'asda dsa asd asd ', NULL, '2015-05-06 12:47:25', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (7, 3, 1, ' asd asd asd ', NULL, '2015-05-06 12:47:58', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (8, 3, 1, ' asd  asd asd asd asd ', NULL, '2015-05-06 12:48:08', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (9, 3, 1, ' asd asd ', NULL, '2015-05-06 12:48:41', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (10, 3, 1, ' asd asd ', NULL, '2015-05-06 12:49:17', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (11, 3, 1, ' asd asd  asd asd ', NULL, '2015-05-06 12:49:25', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (12, 3, 1, ' asd asd  asd asd  asd ', NULL, '2015-05-06 12:49:38', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (13, 3, 1, 'asd  sa s a', NULL, '2015-05-06 12:49:45', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (14, 3, 1, ' asd asd ', NULL, '2015-05-06 12:50:16', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (15, 3, 1, 'asd asd asd ', NULL, '2015-05-06 12:50:54', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (16, 3, 1, 'asd asd asd ', NULL, '2015-05-06 12:52:55', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (17, 3, 3, 'asd asd a dsa asd ', NULL, '2015-05-06 12:53:10', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (18, 3, 1, ' asd asd asd ', NULL, '2015-05-06 12:53:41', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (19, 3, 3, 'da sda sd asd ', NULL, '2015-05-06 12:54:41', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (20, 3, 1, 'asd asd asd 1 123 123 asd asd ', NULL, '2015-05-06 12:55:39', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (21, 3, 1, '12 asd asd asd asd ', NULL, '2015-05-06 12:56:41', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (22, 3, 1, 'asd asd asd asd ', NULL, '2015-05-06 13:07:57', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (23, 3, 1, 'asd asd asd asd ', NULL, '2015-05-06 13:07:58', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (24, 3, 1, 'asd asd asd ', NULL, '2015-05-06 13:13:55', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (25, 3, 1, 'sd asd asd ', NULL, '2015-05-06 13:14:13', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (26, 3, 1, 'asd asd asd  asd ', NULL, '2015-05-06 13:14:31', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (27, 3, 1, 'sad asd asd 1  asd asd ', NULL, '2015-05-06 13:14:48', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (28, 1, 1, 'a sd asd asd ', NULL, '2015-05-06 13:23:07', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (29, 1, 1, 'sdas  asd asd asd ', NULL, '2015-05-06 13:23:11', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (30, 1, 1, ' asd 213 sd asd ', NULL, '2015-05-06 13:23:16', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (31, 3, 1, ' asd 12 asd asd ', NULL, '2015-05-06 13:23:20', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (32, 3, 1, ' 3 qwe asd asd 13 123 ', NULL, '2015-05-06 13:23:25', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (33, 3, 1, ' asd asd 123 123 123 ', NULL, '2015-05-06 13:23:35', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (34, 1, 1, ' 123 12 3asd asd 123 ', NULL, '2015-05-06 13:24:05', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (35, 3, 1, '123 123 asd as 123 ', NULL, '2015-05-06 13:24:09', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (36, 3, 1, '13 12 asd 12 3123 asd ', NULL, '2015-05-06 13:25:35', 0);
INSERT INTO conversation_messages (`con_msg_id`, `con_id`, `user_id`, `msg`, `file`, `datetime`, `inactive`) VALUES (37, 1, 1, ' 123 123 ad 123 12 3asd ', NULL, '2015-05-06 13:25:56', 0);


#
# TABLE STRUCTURE FOR: conversations
#

DROP TABLE IF EXISTS conversations;

CREATE TABLE `conversations` (
  `con_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_a` int(11) DEFAULT NULL,
  `user_b` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`con_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO conversations (`con_id`, `user_a`, `user_b`, `datetime`, `inactive`) VALUES (1, 1, 2, '2015-05-06 10:57:25', 0);
INSERT INTO conversations (`con_id`, `user_a`, `user_b`, `datetime`, `inactive`) VALUES (3, 1, 3, '2015-05-06 12:28:55', 0);


#
# TABLE STRUCTURE FOR: coupons
#

DROP TABLE IF EXISTS coupons;

CREATE TABLE `coupons` (
  `coupon_id` int(10) NOT NULL AUTO_INCREMENT,
  `card_no` varchar(100) DEFAULT NULL,
  `amount` double DEFAULT '0',
  `expiration` date DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: currencies
#

DROP TABLE IF EXISTS currencies;

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency` varchar(22) DEFAULT NULL,
  `currency_desc` varchar(55) DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO currencies (`id`, `currency`, `currency_desc`, `inactive`) VALUES (1, 'PHP', 'Philippine Peso', 0);
INSERT INTO currencies (`id`, `currency`, `currency_desc`, `inactive`) VALUES (2, 'USD', 'US Dollars', 0);
INSERT INTO currencies (`id`, `currency`, `currency_desc`, `inactive`) VALUES (3, 'YEN', 'Japanese Yen', 0);


#
# TABLE STRUCTURE FOR: currency_details
#

DROP TABLE IF EXISTS currency_details;

CREATE TABLE `currency_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` varchar(45) NOT NULL,
  `desc` varchar(60) NOT NULL,
  `value` double NOT NULL,
  `img` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: customer_address
#

DROP TABLE IF EXISTS customer_address;

CREATE TABLE `customer_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cust_id` int(11) NOT NULL,
  `street_no` varchar(55) NOT NULL,
  `street_address` varchar(55) NOT NULL,
  `city` varchar(55) NOT NULL,
  `region` varchar(55) NOT NULL,
  `zip` varchar(55) NOT NULL,
  `base_location` varchar(100) NOT NULL,
  PRIMARY KEY (`id`,`cust_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: customers
#

DROP TABLE IF EXISTS customers;

CREATE TABLE `customers` (
  `cust_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `fname` varchar(55) DEFAULT NULL,
  `mname` varchar(55) DEFAULT NULL,
  `lname` varchar(55) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `tax_exempt` tinyint(1) DEFAULT NULL,
  `street_no` varchar(55) DEFAULT NULL,
  `street_address` varchar(55) DEFAULT NULL,
  `city` varchar(55) DEFAULT NULL,
  `region` varchar(55) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `inactive` int(11) DEFAULT '0',
  `reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`cust_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: customers_bank
#

DROP TABLE IF EXISTS customers_bank;

CREATE TABLE `customers_bank` (
  `bank_id` int(11) NOT NULL,
  `payment` tinyint(4) DEFAULT '0',
  `cust_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `amount_type` varchar(11) DEFAULT NULL,
  `card_no` varchar(50) DEFAULT NULL,
  `card_type` varchar(50) DEFAULT NULL,
  `approval_code` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(40) DEFAULT NULL,
  `remarks` longtext,
  PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: denominations
#

DROP TABLE IF EXISTS denominations;

CREATE TABLE `denominations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(60) NOT NULL,
  `value` double NOT NULL,
  `img` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (1, 'One Thousand', '1000', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (2, 'Five Hundreds', '500', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (3, 'Two Hundreds', '200', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (4, 'One Hundreds', '100', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (5, 'Fifty', '50', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (6, 'Twenty', '20', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (7, 'Ten', '10', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (8, 'Five', '5', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (9, 'One', '1', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (10, 'Twenty Five Cents', '0.25', NULL);
INSERT INTO denominations (`id`, `desc`, `value`, `img`) VALUES (11, 'Ten Cents', '0.1', NULL);


#
# TABLE STRUCTURE FOR: dtr_scheduler
#

DROP TABLE IF EXISTS dtr_scheduler;

CREATE TABLE `dtr_scheduler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `dtr_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: dtr_shifts
#

DROP TABLE IF EXISTS dtr_shifts;

CREATE TABLE `dtr_shifts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(35) NOT NULL DEFAULT '',
  `description` varchar(50) DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `break_out` time DEFAULT NULL,
  `break_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `break_hours` double DEFAULT NULL,
  `work_hours` double DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  `grace_period` time DEFAULT NULL,
  `timein_grace_period` time DEFAULT NULL,
  PRIMARY KEY (`id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: eton
#

DROP TABLE IF EXISTS eton;

CREATE TABLE `eton` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_code` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO eton (`id`, `tenant_code`, `file_path`) VALUES (1, 'ABCD1234', 'C:/ETON');


#
# TABLE STRUCTURE FOR: gift_cards
#

DROP TABLE IF EXISTS gift_cards;

CREATE TABLE `gift_cards` (
  `gc_id` int(10) NOT NULL AUTO_INCREMENT,
  `card_no` varchar(100) DEFAULT NULL,
  `amount` double DEFAULT '0',
  `inactive` tinyint(1) DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`gc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`, `master_id`) VALUES (1, '110628', '1000', 1, 45, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`, `master_id`) VALUES (2, '789111222000', '599', 0, NULL, NULL);


#
# TABLE STRUCTURE FOR: images
#

DROP TABLE IF EXISTS images;

CREATE TABLE `images` (
  `img_id` int(11) NOT NULL AUTO_INCREMENT,
  `img_file_name` longtext,
  `img_path` longtext,
  `img_ref_id` int(11) DEFAULT NULL,
  `img_tbl` varchar(50) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: item_moves
#

DROP TABLE IF EXISTS item_moves;

CREATE TABLE `item_moves` (
  `move_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(55) DEFAULT NULL,
  `loc_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `uom` varchar(10) DEFAULT NULL,
  `case_qty` double DEFAULT NULL,
  `pack_qty` double DEFAULT NULL,
  `curr_item_qty` double DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`move_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: item_serials
#

DROP TABLE IF EXISTS item_serials;

CREATE TABLE `item_serials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` varchar(255) NOT NULL DEFAULT '',
  `serial_no` varchar(255) NOT NULL DEFAULT '',
  `trans_date` date DEFAULT NULL,
  `batch_no` varchar(255) DEFAULT NULL,
  `lot_no` varchar(255) DEFAULT NULL,
  `is_used` tinyint(4) DEFAULT '0',
  `person_id` int(255) DEFAULT '0',
  `reference` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`,`item_code`,`serial_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# TABLE STRUCTURE FOR: item_types
#

DROP TABLE IF EXISTS item_types;

CREATE TABLE `item_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO item_types (`id`, `type`) VALUES (1, 'Not For Resale');
INSERT INTO item_types (`id`, `type`) VALUES (2, 'For Resale');


#
# TABLE STRUCTURE FOR: items
#

DROP TABLE IF EXISTS items;

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(255) DEFAULT NULL,
  `code` varchar(25) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `cat_id` int(11) NOT NULL,
  `subcat_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `uom` varchar(22) NOT NULL,
  `cost` double NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT '1',
  `no_per_pack` double DEFAULT '0',
  `no_per_pack_uom` varchar(50) DEFAULT NULL,
  `no_per_case` double(255,0) DEFAULT '0',
  `reorder_qty` double DEFAULT '0',
  `max_qty` double DEFAULT '0',
  `memo` varchar(255) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `inactive` int(11) DEFAULT '0',
  `master_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: locations
#

DROP TABLE IF EXISTS locations;

CREATE TABLE `locations` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `loc_code` varchar(22) DEFAULT NULL,
  `loc_name` varchar(55) DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  PRIMARY KEY (`loc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: loyalty_cards
#

DROP TABLE IF EXISTS loyalty_cards;

CREATE TABLE `loyalty_cards` (
  `card_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `cust_id` int(11) DEFAULT NULL,
  `points` double(10,0) DEFAULT '0',
  `reg_user_id` int(11) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

INSERT INTO loyalty_cards (`card_id`, `code`, `cust_id`, `points`, `reg_user_id`, `reg_date`, `inactive`, `sync_id`, `master_id`) VALUES (1, '00000001', 1, '2760', 1, '2016-11-09 16:33:49', 0, NULL, NULL);


#
# TABLE STRUCTURE FOR: master_logs
#

DROP TABLE IF EXISTS master_logs;

CREATE TABLE `master_logs` (
  `master_id` int(11) NOT NULL AUTO_INCREMENT,
  `terminal_id` varchar(250) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `src_id` text,
  `transaction` varchar(250) DEFAULT NULL,
  `type` varchar(250) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_automated` tinyint(1) DEFAULT '0',
  `migrate_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `record_count` int(11) DEFAULT NULL,
  `sender_ip_address` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`master_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: megamall
#

DROP TABLE IF EXISTS megamall;

CREATE TABLE `megamall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `br_code` varchar(20) DEFAULT NULL,
  `tenant_no` varchar(20) DEFAULT NULL,
  `class_code` varchar(20) DEFAULT '',
  `trade_code` varchar(20) DEFAULT NULL,
  `outlet_no` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: menu_categories
#

DROP TABLE IF EXISTS menu_categories;

CREATE TABLE `menu_categories` (
  `menu_cat_id` int(11) NOT NULL,
  `menu_cat_name` varchar(150) NOT NULL,
  `menu_sched_id` int(11) DEFAULT NULL,
  `branch_code` varchar(120) DEFAULT NULL,
  `terminal_id` varchar(120) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  `master_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `branch_code`, `terminal_id`, `reg_date`, `inactive`, `master_id`) VALUES (0, 'For master', 0, NULL, NULL, '2018-03-15 16:31:53', 0, NULL);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `branch_code`, `terminal_id`, `reg_date`, `inactive`, `master_id`) VALUES (0, 'For master', 0, NULL, NULL, NULL, 0, NULL);


#
# TABLE STRUCTURE FOR: menu_modifiers
#

DROP TABLE IF EXISTS menu_modifiers;

CREATE TABLE `menu_modifiers` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `mod_group_id` int(11) NOT NULL,
  `branch_code` varchar(120) DEFAULT NULL,
  `terminal_id` varchar(120) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: menu_recipe
#

DROP TABLE IF EXISTS menu_recipe;

CREATE TABLE `menu_recipe` (
  `recipe_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `uom` varchar(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `cost` double NOT NULL,
  PRIMARY KEY (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: menu_schedule_details
#

DROP TABLE IF EXISTS menu_schedule_details;

CREATE TABLE `menu_schedule_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_sched_id` int(11) NOT NULL,
  `day` varchar(22) NOT NULL,
  `time_on` time NOT NULL,
  `time_off` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

INSERT INTO menu_schedule_details (`id`, `menu_sched_id`, `day`, `time_on`, `time_off`) VALUES (8, 1, 'mon', '10:00:00', '03:00:00');
INSERT INTO menu_schedule_details (`id`, `menu_sched_id`, `day`, `time_on`, `time_off`) VALUES (10, 1, 'tue', '10:00:00', '03:00:00');
INSERT INTO menu_schedule_details (`id`, `menu_sched_id`, `day`, `time_on`, `time_off`) VALUES (11, 1, 'wed', '10:00:00', '03:00:00');
INSERT INTO menu_schedule_details (`id`, `menu_sched_id`, `day`, `time_on`, `time_off`) VALUES (12, 1, 'thu', '10:00:00', '03:00:00');
INSERT INTO menu_schedule_details (`id`, `menu_sched_id`, `day`, `time_on`, `time_off`) VALUES (13, 1, 'fri', '10:00:00', '03:00:00');
INSERT INTO menu_schedule_details (`id`, `menu_sched_id`, `day`, `time_on`, `time_off`) VALUES (14, 1, 'sat', '10:00:00', '03:00:00');
INSERT INTO menu_schedule_details (`id`, `menu_sched_id`, `day`, `time_on`, `time_off`) VALUES (15, 1, 'sun', '10:00:00', '03:00:00');


#
# TABLE STRUCTURE FOR: menu_schedules
#

DROP TABLE IF EXISTS menu_schedules;

CREATE TABLE `menu_schedules` (
  `menu_sched_id` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(150) NOT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`menu_sched_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO menu_schedules (`menu_sched_id`, `desc`, `inactive`) VALUES (1, 'Regular Schedule', 0);


#
# TABLE STRUCTURE FOR: menu_subcategories
#

DROP TABLE IF EXISTS menu_subcategories;

CREATE TABLE `menu_subcategories` (
  `menu_sub_cat_id` int(11) NOT NULL,
  `menu_sub_cat_name` varchar(150) NOT NULL,
  `reg_date` datetime DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  `master_id` int(11) DEFAULT NULL,
  `branch_code` varchar(120) DEFAULT NULL,
  `terminal_id` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO menu_subcategories (`menu_sub_cat_id`, `menu_sub_cat_name`, `reg_date`, `inactive`, `master_id`, `branch_code`, `terminal_id`) VALUES (0, 'For apprentice', '2018-03-15 16:32:05', 0, NULL, NULL, NULL);
INSERT INTO menu_subcategories (`menu_sub_cat_id`, `menu_sub_cat_name`, `reg_date`, `inactive`, `master_id`, `branch_code`, `terminal_id`) VALUES (0, 'For apprentice', NULL, 0, NULL, NULL, NULL);


#
# TABLE STRUCTURE FOR: menus
#

DROP TABLE IF EXISTS menus;

CREATE TABLE `menus` (
  `menu_id` int(11) NOT NULL,
  `branch_code` varchar(120) NOT NULL,
  `terminal_id` varchar(120) NOT NULL,
  `menu_code` varchar(100) DEFAULT NULL,
  `menu_barcode` varchar(255) DEFAULT NULL,
  `menu_name` varchar(255) DEFAULT NULL,
  `menu_short_desc` varchar(255) DEFAULT NULL,
  `menu_cat_id` int(11) NOT NULL,
  `menu_sub_cat_id` int(11) DEFAULT NULL,
  `menu_sched_id` int(11) DEFAULT '0',
  `cost` double DEFAULT '0',
  `reg_date` datetime DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `no_tax` int(1) DEFAULT '0',
  `free` int(1) DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  `costing` double DEFAULT '0',
  `master_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO menus (`menu_id`, `branch_code`, `terminal_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`, `master_id`) VALUES (12, '', '', 'p100012', 'p100010001', 'A good Test master', 'Test Master', 0, 0, 0, '120', '2018-03-15 16:32:13', '2018-03-15 16:32:36', 0, 0, 0, '100', NULL);


#
# TABLE STRUCTURE FOR: modifier_group_details
#

DROP TABLE IF EXISTS modifier_group_details;

CREATE TABLE `modifier_group_details` (
  `id` int(11) NOT NULL,
  `mod_group_id` int(11) NOT NULL,
  `mod_id` int(11) NOT NULL,
  `terminal_id` varchar(120) DEFAULT NULL,
  `branch_code` varchar(120) DEFAULT NULL,
  `default` tinyint(1) DEFAULT '0',
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: modifier_groups
#

DROP TABLE IF EXISTS modifier_groups;

CREATE TABLE `modifier_groups` (
  `mod_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `mandatory` int(1) DEFAULT '0',
  `multiple` int(10) DEFAULT '0',
  `terminal_id` varchar(120) DEFAULT NULL,
  `branch_code` varchar(120) DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`mod_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: modifier_recipe
#

DROP TABLE IF EXISTS modifier_recipe;

CREATE TABLE `modifier_recipe` (
  `mod_recipe_id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `uom` varchar(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `cost` double NOT NULL,
  PRIMARY KEY (`mod_recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: modifiers
#

DROP TABLE IF EXISTS modifiers;

CREATE TABLE `modifiers` (
  `mod_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `cost` double(11,0) DEFAULT '0',
  `has_recipe` int(1) DEFAULT '0',
  `reg_date` datetime DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `inactive` int(1) DEFAULT '0',
  `master_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: ortigas
#

DROP TABLE IF EXISTS ortigas;

CREATE TABLE `ortigas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_code` varchar(10) DEFAULT NULL,
  `sales_type` varchar(5) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: ortigas_read_details
#

DROP TABLE IF EXISTS ortigas_read_details;

CREATE TABLE `ortigas_read_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zread_id` int(11) DEFAULT NULL,
  `read_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `old_total` double DEFAULT NULL,
  `grand_total` double DEFAULT NULL COMMENT 'GT for ZRead only',
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `scope_from` datetime DEFAULT NULL,
  `scope_to` datetime DEFAULT NULL,
  `no_tax` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: promo_discount_items
#

DROP TABLE IF EXISTS promo_discount_items;

CREATE TABLE `promo_discount_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `promo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: promo_discount_schedule
#

DROP TABLE IF EXISTS promo_discount_schedule;

CREATE TABLE `promo_discount_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_id` int(11) NOT NULL,
  `day` varchar(22) NOT NULL,
  `time_on` time NOT NULL,
  `time_off` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: promo_discounts
#

DROP TABLE IF EXISTS promo_discounts;

CREATE TABLE `promo_discounts` (
  `promo_id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_code` varchar(22) DEFAULT NULL,
  `promo_name` varchar(55) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `absolute` tinyint(4) DEFAULT '0',
  `reg_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `inactive` int(11) DEFAULT '0',
  PRIMARY KEY (`promo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: promo_free
#

DROP TABLE IF EXISTS promo_free;

CREATE TABLE `promo_free` (
  `pf_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `has_menu_id` varchar(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `sched_id` int(11) DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`pf_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO promo_free (`pf_id`, `name`, `description`, `has_menu_id`, `amount`, `sched_id`, `inactive`) VALUES (1, 'Free Pork Siomai D', 'Free Pork Siomai D', '34', '1000', 1, 0);


#
# TABLE STRUCTURE FOR: promo_free_menus
#

DROP TABLE IF EXISTS promo_free_menus;

CREATE TABLE `promo_free_menus` (
  `pf_menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `pf_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  PRIMARY KEY (`pf_menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO promo_free_menus (`pf_menu_id`, `pf_id`, `menu_id`, `qty`) VALUES (2, 1, 12, 1);


#
# TABLE STRUCTURE FOR: read_details
#

DROP TABLE IF EXISTS read_details;

CREATE TABLE `read_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `read_id` int(11) DEFAULT NULL,
  `read_type` tinyint(2) NOT NULL,
  `read_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `old_total` double DEFAULT NULL,
  `grand_total` double DEFAULT NULL COMMENT 'GT for ZRead only',
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `scope_from` datetime DEFAULT NULL,
  `scope_to` datetime DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `ctr` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# TABLE STRUCTURE FOR: reasons
#

DROP TABLE IF EXISTS reasons;

CREATE TABLE `reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `ref_name` varchar(150) DEFAULT NULL,
  `reason` longtext,
  `trans_id` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: receipt_discounts
#

DROP TABLE IF EXISTS receipt_discounts;

CREATE TABLE `receipt_discounts` (
  `disc_id` int(11) NOT NULL AUTO_INCREMENT,
  `disc_code` varchar(22) DEFAULT NULL,
  `disc_name` varchar(100) DEFAULT NULL,
  `disc_rate` double DEFAULT NULL,
  `no_tax` int(1) DEFAULT '0',
  `fix` int(1) DEFAULT '0',
  `inactive` int(1) DEFAULT '0',
  PRIMARY KEY (`disc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (1, 'SNDISC', 'Senior Citizen Discount', '20', 1, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (2, 'PWDISC', 'Person WIth Disability', '20', 1, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (3, 'REG15DISC', '15 Percent DIscount', '15', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (4, 'REG50DISC', '50 Percent Discount', '50', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (5, 'DIPLOMAT', 'DIPLOMAT', '12', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (6, 'REG20DISC', '20 Percent Discount', '20', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (7, 'CDV', '15 % cdv Take Out', '15', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (8, '50HH', '50% HAPPY HOUR', '50', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (9, 'REG30DISC', '30 Percent Discount', '30', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (10, 'REG70DISC', '70 Percent Discount', '70', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (11, 'REG25DISC', '25  CDVBDAYMONTH', '25', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (12, 'REG60DISC', '60 Percent Discount', '60', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (13, '20HH', '20% HAPPY HOUR', '20', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (14, '10clubdevino', '10 Club de Vino', '10', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (15, '20Clubdevino', '20 Club de Vino', '20', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (16, '50PHH', '50%HAPPY HOUR', '50', 0, 0, 1);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (17, '20HH2', '20%HAPPYHOUR', '20', 0, 0, 1);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (18, '15PCDVTAKEOUT', '15%CDVTAKEOUT', '15', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (19, 'LaCamara10PercentDisco', 'La Camara 10 percent', '10', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (20, 'TasteofCoastalTicket', 'Taste of Coastal Ticket', '2300', 0, 1, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (21, 'DIPLOMAT', 'DIPLOMAT', '12', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (22, 'SIGNPRIVILEGDES', 'SIGN PRIVILEGDES  100 percent', '100', 0, 0, 0);


#
# TABLE STRUCTURE FOR: restaurant_branch_tables
#

DROP TABLE IF EXISTS restaurant_branch_tables;

CREATE TABLE `restaurant_branch_tables` (
  `tbl_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `name` varchar(55) DEFAULT NULL,
  `top` int(11) DEFAULT '0',
  `left` int(11) DEFAULT '0',
  PRIMARY KEY (`tbl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: rob_files
#

DROP TABLE IF EXISTS rob_files;

CREATE TABLE `rob_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) DEFAULT NULL,
  `file` varchar(150) DEFAULT NULL,
  `print` double DEFAULT '0',
  `inactive` tinyint(4) DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: settings
#

DROP TABLE IF EXISTS settings;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_of_receipt_print` int(11) DEFAULT NULL,
  `no_of_order_slip_print` int(11) DEFAULT NULL,
  `controls` varchar(150) DEFAULT NULL,
  `local_tax` double(5,0) DEFAULT '0',
  `kitchen_printer_name` varchar(150) DEFAULT NULL,
  `kitchen_beverage_printer_name` varchar(150) DEFAULT NULL,
  `kitchen_printer_name_no` int(11) DEFAULT '0',
  `kitchen_beverage_printer_name_no` int(11) DEFAULT '0',
  `open_drawer_printer` varchar(150) DEFAULT NULL,
  `loyalty_for_amount` double DEFAULT NULL,
  `loyalty_to_points` double DEFAULT NULL,
  `backup_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

INSERT INTO settings (`id`, `no_of_receipt_print`, `no_of_order_slip_print`, `controls`, `local_tax`, `kitchen_printer_name`, `kitchen_beverage_printer_name`, `kitchen_printer_name_no`, `kitchen_beverage_printer_name_no`, `open_drawer_printer`, `loyalty_for_amount`, `loyalty_to_points`, `backup_path`) VALUES (1, 1, 0, '1=>dine in,2=>delivery,5=>pickup,6=>takeout,8=>food panda', '0', '', '', 1, 0, 'CASH DRAWER', '100', '10', 'D:/dine/backup');


#
# TABLE STRUCTURE FOR: shift_entries
#

DROP TABLE IF EXISTS shift_entries;

CREATE TABLE `shift_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `user_id` varchar(45) NOT NULL,
  `trans_date` datetime NOT NULL,
  `pos_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO shift_entries (`id`, `entry_id`, `shift_id`, `amount`, `user_id`, `trans_date`, `pos_id`) VALUES (1, 0, 1, '200', '1', '2018-03-15 15:54:18', NULL);


#
# TABLE STRUCTURE FOR: shifts
#

DROP TABLE IF EXISTS shifts;

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime DEFAULT NULL,
  `xread_id` int(11) DEFAULT NULL,
  `cashout_id` int(11) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO shifts (`id`, `shift_id`, `user_id`, `check_in`, `check_out`, `xread_id`, `cashout_id`, `terminal_id`, `pos_id`) VALUES (1, 0, 1, '2018-03-15 15:54:18', NULL, NULL, NULL, 1, NULL);


#
# TABLE STRUCTURE FOR: stalucia
#

DROP TABLE IF EXISTS stalucia;

CREATE TABLE `stalucia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

INSERT INTO stalucia (`id`, `tenant_code`) VALUES (1, '123');


#
# TABLE STRUCTURE FOR: subcategories
#

DROP TABLE IF EXISTS subcategories;

CREATE TABLE `subcategories` (
  `sub_cat_id` int(11) NOT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `inactive` int(11) DEFAULT '0',
  `master_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: suppliers
#

DROP TABLE IF EXISTS suppliers;

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `inactive` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO suppliers (`supplier_id`, `name`, `address`, `contact_no`, `reg_date`, `memo`, `inactive`) VALUES (1, 'Supplier 1', 'Cubao, Q.C.', '02 999 99 99', '2016-03-11 10:51:33', 'chinese Supplier', '0');


#
# TABLE STRUCTURE FOR: sync_logs
#

DROP TABLE IF EXISTS sync_logs;

CREATE TABLE `sync_logs` (
  `sync_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction` varchar(250) DEFAULT NULL,
  `type` varchar(250) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `migrate_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `src_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_automated` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`sync_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: sync_types
#

DROP TABLE IF EXISTS sync_types;

CREATE TABLE `sync_types` (
  `sync_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `sync_type` varchar(100) NOT NULL,
  PRIMARY KEY (`sync_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO sync_types (`sync_type_id`, `sync_type`) VALUES (1, 'local to main');
INSERT INTO sync_types (`sync_type_id`, `sync_type`) VALUES (2, 'main to local');


#
# TABLE STRUCTURE FOR: table_activity
#

DROP TABLE IF EXISTS table_activity;

CREATE TABLE `table_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tbl_id` int(11) DEFAULT NULL,
  `pc_id` int(11) DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: tables
#

DROP TABLE IF EXISTS tables;

CREATE TABLE `tables` (
  `tbl_id` int(11) NOT NULL AUTO_INCREMENT,
  `capacity` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `name` varchar(55) DEFAULT NULL,
  `top` int(11) DEFAULT '0',
  `left` int(11) DEFAULT '0',
  `inactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`tbl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=latin1;

INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (92, 4, NULL, 'Tbl 1', 38, 838, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (93, 4, NULL, 'Tbl 3', 38, 758, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (94, 4, NULL, 'Tbl 2', 136, 837, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (95, 4, NULL, 'Tbl 5', 136, 757, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (96, 4, NULL, 'Tbl 6', 38, 682, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (97, 4, NULL, 'Tbl 7', 38, 602, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (98, 4, NULL, 'TBL 8', 38, 525, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (99, 4, NULL, 'Tbl 9', 38, 446, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (100, 4, NULL, 'Tbl 11', 42, 267, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (101, 4, NULL, 'Tbl 12', 38, 191, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (102, 4, NULL, 'Tbl 14', 38, 111, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (103, 4, NULL, 'Tbl 19', 128, 109, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (104, 4, NULL, 'Tbl 17', 128, 187, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (105, 4, NULL, 'Tbl 15', 128, 265, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (106, 4, NULL, 'Tbl 20', 175, 109, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (107, 4, NULL, 'Tbl 18', 175, 187, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (108, 4, NULL, 'Tbl 16', 175, 265, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (109, 4, NULL, 'Tbl 23', 236, 111, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (110, 4, NULL, 'Tbl 22', 236, 191, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (111, 4, NULL, 'Tbl 21', 236, 268, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (112, 4, NULL, 'Tbl 24', 350, 230, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (113, 4, NULL, 'AL 4', 46, 12, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (114, 4, NULL, 'AL 3', 136, 13, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (115, 4, NULL, 'AL 2', 236, 12, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (116, 4, NULL, 'AL 1', 398, 13, 0);
INSERT INTO tables (`tbl_id`, `capacity`, `status`, `name`, `top`, `left`, `inactive`) VALUES (117, 4, NULL, 'Tbl 10', 234, 385, 0);


#
# TABLE STRUCTURE FOR: tablesold
#

DROP TABLE IF EXISTS tablesold;

CREATE TABLE `tablesold` (
  `tbl_id` int(11) NOT NULL AUTO_INCREMENT,
  `capacity` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `name` varchar(55) DEFAULT NULL,
  `top` int(11) DEFAULT '0',
  `left` int(11) DEFAULT '0',
  PRIMARY KEY (`tbl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: tax_rates
#

DROP TABLE IF EXISTS tax_rates;

CREATE TABLE `tax_rates` (
  `tax_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) DEFAULT NULL,
  `rate` double DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`tax_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO tax_rates (`tax_id`, `name`, `rate`, `inactive`) VALUES (1, 'VAT', '12', 0);


#
# TABLE STRUCTURE FOR: terminals
#

DROP TABLE IF EXISTS terminals;

CREATE TABLE `terminals` (
  `terminal_id` int(11) NOT NULL AUTO_INCREMENT,
  `terminal_code` varchar(60) NOT NULL,
  `branch_code` varchar(55) DEFAULT NULL,
  `terminal_name` varchar(120) DEFAULT NULL,
  `ip` varchar(60) DEFAULT NULL,
  `comp_name` varchar(60) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `inactive` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`terminal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

INSERT INTO terminals (`terminal_id`, `terminal_code`, `branch_code`, `terminal_name`, `ip`, `comp_name`, `reg_date`, `update_date`, `inactive`) VALUES (1, 'T00001', 'ELRGB', 'Terminal 1', '192.168.254.101', 'TERMINAL1', '2014-09-11 12:45:45', NULL, 0);


#
# TABLE STRUCTURE FOR: trans_adjustment_details
#

DROP TABLE IF EXISTS trans_adjustment_details;

CREATE TABLE `trans_adjustment_details` (
  `adjustment_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `case` int(1) DEFAULT '0',
  `pack` int(1) DEFAULT '0',
  `from_loc` int(11) DEFAULT NULL,
  `to_loc` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`adjustment_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_adjustments
#

DROP TABLE IF EXISTS trans_adjustments;

CREATE TABLE `trans_adjustments` (
  `adjustment_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `inactive` tinyint(4) DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_receiving_details
#

DROP TABLE IF EXISTS trans_receiving_details;

CREATE TABLE `trans_receiving_details` (
  `receiving_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `receiving_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `case` int(1) DEFAULT '0',
  `pack` int(1) DEFAULT '0',
  `price` double DEFAULT NULL,
  `uom` varchar(50) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`receiving_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_receivings
#

DROP TABLE IF EXISTS trans_receivings;

CREATE TABLE `trans_receivings` (
  `receiving_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `trans_date` date DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `inactive` tinyint(4) DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`receiving_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_refs
#

DROP TABLE IF EXISTS trans_refs;

CREATE TABLE `trans_refs` (
  `id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(55) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales
#

DROP TABLE IF EXISTS trans_sales;

CREATE TABLE `trans_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_code` varchar(250) DEFAULT NULL,
  `sales_id` int(11) NOT NULL,
  `mobile_sales_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(55) DEFAULT NULL,
  `void_ref` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` double DEFAULT NULL,
  `total_paid` double DEFAULT '0',
  `memo` varchar(255) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `guest` double(11,0) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `paid` int(1) DEFAULT '0',
  `reason` varchar(255) DEFAULT NULL,
  `void_user_id` int(11) DEFAULT NULL,
  `printed` tinyint(1) DEFAULT '0',
  `inactive` tinyint(4) DEFAULT '0',
  `waiter_id` int(11) DEFAULT NULL,
  `split` int(11) DEFAULT '0',
  `serve_no` int(11) DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `billed` int(4) DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_charges
#

DROP TABLE IF EXISTS trans_sales_charges;

CREATE TABLE `trans_sales_charges` (
  `sales_charge_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `charge_id` int(11) DEFAULT NULL,
  `charge_code` varchar(55) DEFAULT NULL,
  `charge_name` varchar(55) DEFAULT NULL,
  `rate` double DEFAULT NULL,
  `absolute` tinyint(1) DEFAULT '0',
  `amount` double DEFAULT '0',
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_discounts
#

DROP TABLE IF EXISTS trans_sales_discounts;

CREATE TABLE `trans_sales_discounts` (
  `sales_disc_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `disc_id` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `disc_code` varchar(55) DEFAULT NULL,
  `disc_rate` double DEFAULT NULL,
  `name` varchar(55) DEFAULT NULL,
  `bday` datetime DEFAULT NULL,
  `code` varchar(55) DEFAULT NULL,
  `guest` int(11) DEFAULT NULL,
  `items` varchar(55) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `no_tax` tinyint(4) DEFAULT '0',
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_items
#

DROP TABLE IF EXISTS trans_sales_items;

CREATE TABLE `trans_sales_items` (
  `sales_item_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `line_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `no_tax` int(1) DEFAULT '0',
  `remarks` varchar(150) DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_local_tax
#

DROP TABLE IF EXISTS trans_sales_local_tax;

CREATE TABLE `trans_sales_local_tax` (
  `sales_local_tax_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: trans_sales_loyalty_points
#

DROP TABLE IF EXISTS trans_sales_loyalty_points;

CREATE TABLE `trans_sales_loyalty_points` (
  `loyalty_point_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `card_id` int(11) DEFAULT NULL,
  `code` varchar(150) DEFAULT NULL,
  `cust_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT '0',
  `points` double DEFAULT '0',
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: trans_sales_menu_modifiers
#

DROP TABLE IF EXISTS trans_sales_menu_modifiers;

CREATE TABLE `trans_sales_menu_modifiers` (
  `sales_mod_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `mod_group_id` int(11) DEFAULT NULL,
  `line_id` int(11) DEFAULT NULL,
  `mod_id` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `kitchen_slip_printed` tinyint(1) DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_menus
#

DROP TABLE IF EXISTS trans_sales_menus;

CREATE TABLE `trans_sales_menus` (
  `sales_menu_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `line_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `no_tax` int(1) DEFAULT '0',
  `remarks` varchar(150) DEFAULT NULL,
  `kitchen_slip_printed` tinyint(1) DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `free_user_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_no_tax
#

DROP TABLE IF EXISTS trans_sales_no_tax;

CREATE TABLE `trans_sales_no_tax` (
  `sales_no_tax_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_payments
#

DROP TABLE IF EXISTS trans_sales_payments;

CREATE TABLE `trans_sales_payments` (
  `payment_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `to_pay` double DEFAULT NULL,
  `reference` varchar(55) DEFAULT NULL,
  `card_type` varchar(55) DEFAULT NULL,
  `card_number` varchar(30) DEFAULT NULL,
  `approval_code` varchar(15) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_tax
#

DROP TABLE IF EXISTS trans_sales_tax;

CREATE TABLE `trans_sales_tax` (
  `sales_tax_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `name` varchar(55) DEFAULT NULL,
  `rate` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_zero_rated
#

DROP TABLE IF EXISTS trans_sales_zero_rated;

CREATE TABLE `trans_sales_zero_rated` (
  `sales_zero_rated_id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `pos_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_spoilage
#

DROP TABLE IF EXISTS trans_spoilage;

CREATE TABLE `trans_spoilage` (
  `spoil_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `trans_date` date DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `inactive` tinyint(4) DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`spoil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_spoilage_details
#

DROP TABLE IF EXISTS trans_spoilage_details;

CREATE TABLE `trans_spoilage_details` (
  `spoil_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `spoil_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `uom` varchar(0) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`spoil_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_types
#

DROP TABLE IF EXISTS trans_types;

CREATE TABLE `trans_types` (
  `type_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `next_ref` varchar(45) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`, `master_id`) VALUES (10, 'sales', '00000001', 85, NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`, `master_id`) VALUES (20, 'receivings', 'R000001', NULL, NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`, `master_id`) VALUES (30, 'adjustment', 'A000001', NULL, NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`, `master_id`) VALUES (11, 'sales void', 'V000001', 44, NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`, `master_id`) VALUES (40, 'customer deposit', 'C000001', 139, NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`, `master_id`) VALUES (50, 'loyalty card', '00000002', 33, NULL);


#
# TABLE STRUCTURE FOR: trans_voids
#

DROP TABLE IF EXISTS trans_voids;

CREATE TABLE `trans_voids` (
  `void_id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_type` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `reg_user` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  `master_id` int(11) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`void_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: uom
#

DROP TABLE IF EXISTS uom;

CREATE TABLE `uom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(22) DEFAULT NULL,
  `name` varchar(55) DEFAULT NULL,
  `num` double DEFAULT '0',
  `to` varchar(22) DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (1, 'ml', 'Mililiter', '0', NULL, 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (2, 'gm', 'Gram', '0', NULL, 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (3, 'pc', 'Piece', '0', NULL, 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (4, 'can', 'Can', '0', '0', 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (5, 'bottle', 'Bottle', '0', '0', 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (6, 'kilo', 'Kilo', '0', '0', 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (7, 'pack', 'Pack', '0', '0', 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (8, 'Serving', 'serving', '0', '0', 0);


#
# TABLE STRUCTURE FOR: updates
#

DROP TABLE IF EXISTS updates;

CREATE TABLE `updates` (
  `ctr` int(11) NOT NULL AUTO_INCREMENT,
  `query` longtext,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`ctr`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: user_roles
#

DROP TABLE IF EXISTS user_roles;

CREATE TABLE `user_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `access` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO user_roles (`id`, `role`, `description`, `access`) VALUES (1, 'Administrator ', 'System Administrator', 'all');
INSERT INTO user_roles (`id`, `role`, `description`, `access`) VALUES (2, 'Manager', 'Manager', 'cashier,customers,gift_cards,trans,receiving,adjustment,items,list,item_inv,menu,menulist,menucat,menusched,mods,modslist,modgrps,dtr,shifts,scheduler,general_settings,gcategories,gsubcategories,guom,promos,gsuppliers,gcustomers,gtaxrates,grecdiscs,gterminals,gcurrencies,greferences,glocations,tblmng,setup,send_to_rob,control,user');
INSERT INTO user_roles (`id`, `role`, `description`, `access`) VALUES (3, 'Employee', 'Employee', 'cashier');
INSERT INTO user_roles (`id`, `role`, `description`, `access`) VALUES (4, 'OIC', 'Officer In Charge', NULL);


#
# TABLE STRUCTURE FOR: users
#

DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pin` varchar(55) DEFAULT NULL,
  `fname` varchar(55) DEFAULT NULL,
  `mname` varchar(55) DEFAULT NULL,
  `lname` varchar(55) DEFAULT NULL,
  `suffix` varchar(55) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `gender` varchar(11) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `inactive` int(11) DEFAULT '0',
  `terminal_id` int(11) DEFAULT NULL,
  `branch_code` varchar(250) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `master_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`, `terminal_id`, `branch_code`, `datetime`, `master_id`, `sync_id`) VALUES (1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, NULL, NULL, NULL, NULL, 1, '', NULL, NULL, 0, NULL, NULL, '2018-03-15 15:49:05', NULL, NULL);


#
# TABLE STRUCTURE FOR: vistamall
#

DROP TABLE IF EXISTS vistamall;

CREATE TABLE `vistamall` (
  `id` int(11) NOT NULL DEFAULT '0',
  `stall_code` varchar(50) DEFAULT NULL,
  `sales_dep` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO vistamall (`id`, `stall_code`, `sales_dep`, `file_path`) VALUES (1, '12345678', '00', 'C:/VISTAMALL');


