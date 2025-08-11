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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

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

INSERT INTO ayala (`id`, `contract_no`, `store_name`, `xxx_no`, `dbf_tenant_name`, `dbf_path`, `text_file_path`) VALUES (1, '6000000000024', 'FRITOSS UNIT 20', 'AYA', '2XU - G3', 'C:/AYALA/', 'C:/AYALA/');


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

INSERT INTO branch_details (`branch_id`, `res_id`, `branch_code`, `branch_name`, `branch_desc`, `contact_no`, `delivery_no`, `address`, `base_location`, `currency`, `image`, `inactive`, `tin`, `machine_no`, `bir`, `permit_no`, `serial`, `email`, `website`, `store_open`, `store_close`, `rob_tenant_code`, `rob_path`, `rob_username`, `rob_password`, `accrdn`, `rec_footer`, `pos_footer`) VALUES (1, 1, 'POINTONE0001', 'Fritoss', '', '', '', '2nd Floor Corte Ayala Malls The 30th Pasig City', NULL, 'PHP', 'layout.png', 0, '', '', '0', '', '', '', '', '09:00:00', '06:30:00', '1234', '190.125.220.1', 'mag15836hap', 'maghapex', '43A0085434442014110212', 'THIS SERVES AS YOUR OFFICIAL RECEIPT.<br>', '');


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
  `cashout_id` int(11) NOT NULL,
  `type` varchar(30) DEFAULT NULL,
  `denomination` varchar(150) DEFAULT '0',
  `reference` varchar(150) DEFAULT NULL,
  `total` double DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: cashout_entries
#

DROP TABLE IF EXISTS cashout_entries;

CREATE TABLE `cashout_entries` (
  `cashout_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(45) NOT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `drawer_amount` varchar(255) DEFAULT NULL,
  `count_amount` double DEFAULT NULL,
  `trans_date` datetime NOT NULL,
  PRIMARY KEY (`cashout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: categories
#

DROP TABLE IF EXISTS categories;

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `inactive` int(11) DEFAULT '0',
  PRIMARY KEY (`cat_id`)
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

INSERT INTO charges (`charge_id`, `charge_code`, `charge_name`, `charge_amount`, `absolute`, `no_tax`, `inactive`) VALUES (1, 'SCHG', 'Service Charge', '2', 0, 1, 0);
INSERT INTO charges (`charge_id`, `charge_code`, `charge_name`, `charge_amount`, `absolute`, `no_tax`, `inactive`) VALUES (2, 'DCHG', 'Delivery Charge', '5', 0, 1, 0);
INSERT INTO charges (`charge_id`, `charge_code`, `charge_name`, `charge_amount`, `absolute`, `no_tax`, `inactive`) VALUES (3, 'HFHG', 'Handling Fee', '8', 0, 1, 0);


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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

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
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO coupons (`coupon_id`, `card_no`, `amount`, `expiration`, `inactive`, `sync_id`) VALUES (1, '121212', '100', '2018-01-26', 1, 64);


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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO customers (`cust_id`, `phone`, `email`, `fname`, `mname`, `lname`, `suffix`, `tax_exempt`, `street_no`, `street_address`, `city`, `region`, `zip`, `inactive`, `reg_date`) VALUES (1, '2346', '5765', 'rey', 'c', 'tejada', '', NULL, '656', '565', '5665', '7', '7868', 0, NULL);


#
# TABLE STRUCTURE FOR: customers_bank
#

DROP TABLE IF EXISTS customers_bank;

CREATE TABLE `customers_bank` (
  `bank_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(40) DEFAULT NULL,
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
  `remarks` longtext,
  `sync_id` int(11) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;

INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (1, 1, '2014-10-28', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (3, 3, '2014-10-28', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (4, 4, '2014-10-28', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (5, 5, '2014-10-28', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (14, 6, '2014-10-28', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (15, 1, '2014-10-29', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (16, 3, '2014-10-29', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (17, 4, '2014-10-29', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (18, 5, '2014-10-29', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (19, 6, '2014-10-29', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (22, 1, '2014-10-30', 3);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (23, 3, '2014-10-30', 3);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (24, 5, '2014-10-30', 5);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (25, 1, '2014-10-31', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (26, 3, '2014-10-31', 5);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (27, 4, '2014-10-31', 4);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (28, 5, '2014-10-31', 5);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (29, 6, '2014-10-31', 4);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (30, 1, '2014-11-01', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (31, 3, '2014-11-01', 5);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (32, 4, '2014-11-01', 4);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (33, 5, '2014-11-01', 5);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (34, 6, '2014-11-01', 4);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (35, 1, '2014-11-02', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (36, 4, '2014-11-02', 6);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (37, 6, '2014-11-02', 6);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (38, 5, '2014-11-12', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (39, 6, '2014-11-12', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (40, 5, '2014-11-13', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (41, 6, '2014-11-13', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (42, 5, '2014-11-14', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (43, 6, '2014-11-14', 2);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (44, 16, '2014-11-24', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (45, 17, '2014-11-24', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (46, 18, '2014-11-24', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (47, 16, '2014-11-25', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (48, 17, '2014-11-25', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (49, 18, '2014-11-25', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (50, 16, '2014-11-26', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (51, 17, '2014-11-26', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (52, 18, '2014-11-26', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (53, 16, '2014-11-27', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (54, 17, '2014-11-27', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (55, 18, '2014-11-27', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (56, 16, '2014-11-28', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (57, 17, '2014-11-28', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (58, 18, '2014-11-28', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (59, 16, '2014-11-29', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (60, 17, '2014-11-29', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (61, 18, '2014-11-29', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (62, 16, '2014-11-30', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (63, 17, '2014-11-30', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (64, 18, '2014-11-30', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (65, 19, '2014-11-24', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (66, 19, '2014-11-25', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (67, 19, '2014-11-26', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (68, 19, '2014-11-27', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (69, 19, '2014-11-28', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (70, 19, '2014-11-29', 7);
INSERT INTO dtr_scheduler (`id`, `user_id`, `date`, `dtr_id`) VALUES (71, 19, '2014-11-30', 7);


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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

INSERT INTO dtr_shifts (`id`, `code`, `description`, `time_in`, `break_out`, `break_in`, `time_out`, `break_hours`, `work_hours`, `inactive`, `grace_period`, `timein_grace_period`) VALUES (1, 'RESTDAY', 'Rest Day', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '0', '0', 0, '00:00:00', NULL);
INSERT INTO dtr_shifts (`id`, `code`, `description`, `time_in`, `break_out`, `break_in`, `time_out`, `break_hours`, `work_hours`, `inactive`, `grace_period`, `timein_grace_period`) VALUES (2, 'Shift1', 'restday again', '07:00:00', '11:00:00', '12:00:00', '16:00:00', '1', '9', 0, '00:00:00', '00:30:00');
INSERT INTO dtr_shifts (`id`, `code`, `description`, `time_in`, `break_out`, `break_in`, `time_out`, `break_hours`, `work_hours`, `inactive`, `grace_period`, `timein_grace_period`) VALUES (3, '6PM7AM', '6PM to 7AM', '18:00:00', '00:00:00', '00:00:00', '07:00:00', '0', '13', 0, '00:00:00', '00:00:00');
INSERT INTO dtr_shifts (`id`, `code`, `description`, `time_in`, `break_out`, `break_in`, `time_out`, `break_hours`, `work_hours`, `inactive`, `grace_period`, `timein_grace_period`) VALUES (4, '7AM7PM', '7AM to 7PM', '07:00:00', '00:00:00', '00:00:00', '19:00:00', '0', '12', 0, '00:15:00', '01:00:00');
INSERT INTO dtr_shifts (`id`, `code`, `description`, `time_in`, `break_out`, `break_in`, `time_out`, `break_hours`, `work_hours`, `inactive`, `grace_period`, `timein_grace_period`) VALUES (5, '7PM7AM', '7PM to 7AM', '19:00:00', '00:00:00', '00:00:00', '07:00:00', '0', '-12', 0, '00:15:00', '01:00:00');
INSERT INTO dtr_shifts (`id`, `code`, `description`, `time_in`, `break_out`, `break_in`, `time_out`, `break_hours`, `work_hours`, `inactive`, `grace_period`, `timein_grace_period`) VALUES (6, '7AM4PM', '7AM to 4PM', '07:00:00', '00:00:00', '00:00:00', '16:00:00', '0', '9', 0, '00:15:00', '01:00:00');
INSERT INTO dtr_shifts (`id`, `code`, `description`, `time_in`, `break_out`, `break_in`, `time_out`, `break_hours`, `work_hours`, `inactive`, `grace_period`, `timein_grace_period`) VALUES (7, '9AM10PM', '9AM10PM', '09:00:00', '00:00:00', '00:00:00', '22:00:00', '1', '13', 0, '00:00:00', '00:00:00');


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
  PRIMARY KEY (`gc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (1, '110628', '1000', 1, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (2, '5170005', '500', 1, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (3, '5170004', '500', 1, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (4, '5170003', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (5, '5170002', '500', 1, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (6, '5170001', '500', 1, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (7, '3170001', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (8, '3170002', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (9, '3170003', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (10, '3170004', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (11, '3170005', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (12, '3170006', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (13, '3170007', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (14, '3170008', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (15, '3170009', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (16, '3170010', '500', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (17, 'FXX0901001', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (18, 'FXX0901002', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (19, 'FXX0901003', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (20, 'FXX0901004', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (21, 'FXX0901005', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (22, 'FXX0901006', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (23, 'FXX0901007', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (24, 'FXX0901008', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (25, 'FXX0901009', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (26, 'FXX0901010', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (27, 'FXX0901011', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (28, 'FXX0901012', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (29, 'FXX0901013', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (30, 'FXX0901014', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (31, 'FXX0901015', '200', 1, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (32, 'FXX0901016', '200', 1, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (33, 'FXX0901017', '200', 1, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (34, 'FXX0901018', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (35, 'FXX0901019', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (36, 'FXX0901020', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (37, 'FXX0901021', '200', 1, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (38, 'FXX0901022', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (39, 'FXX0901023', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (40, 'FXX0901024', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (41, 'FXX0901025', '200', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (42, 'FXX1025001', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (43, 'FXX1025002', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (44, 'FXX1025003', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (45, 'FXX1025004', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (46, 'FXX1025005', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (47, 'FXX1025006', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (48, 'FXX1025007', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (49, 'FXX1025008', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (50, 'FXX1025009', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (51, 'FXX1025010', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (52, 'FXX1025011', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (53, 'FXX1025012', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (54, 'FXX1025013', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (55, 'FXX1025014', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (56, 'FXX1025015', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (57, 'FXX1025016', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (58, 'FXX1025017', '100', 0, NULL);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (59, 'FXX1025018', '100', 1, 59);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (60, 'FXX1025019', '100', 1, 57);
INSERT INTO gift_cards (`gc_id`, `card_no`, `amount`, `inactive`, `sync_id`) VALUES (61, 'FXX1025020', '100', 1, NULL);


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
  `img_blob` longblob,
  `datetime` datetime DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

INSERT INTO images (`img_id`, `img_file_name`, `img_path`, `img_ref_id`, `img_tbl`, `img_blob`, `datetime`, `disabled`) VALUES (14, '41.png', 'uploads/users/41.png', 41, 'users', NULL, '2017-02-23 15:33:56', 0);
INSERT INTO images (`img_id`, `img_file_name`, `img_path`, `img_ref_id`, `img_tbl`, `img_blob`, `datetime`, `disabled`) VALUES (16, '39.png', 'uploads/users/39.png', 39, 'users', NULL, '2017-03-13 18:10:28', 0);
INSERT INTO images (`img_id`, `img_file_name`, `img_path`, `img_ref_id`, `img_tbl`, `img_blob`, `datetime`, `disabled`) VALUES (17, 'splash.png', 'uploads/splash/splash.png', NULL, 'splash_images', NULL, NULL, 0);


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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (1, '4423', '321323', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (2, '123', '123', NULL, 'asdasd', 'asd', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (3, '123', '123', NULL, 'asdasd', 'asd', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (4, '4423', 'adwdwdw', NULL, 'dads', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (5, '4423', 'asdasd', NULL, 'dads', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (6, '4423', '465464', NULL, '243', '23434', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (7, '4423', '232', NULL, '243', '23434', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (8, '4423', 'adawdwd', NULL, 'asd', 'adwdw', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (9, '4423', 'adsda', NULL, 'asd', 'adwdw', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (10, '4423', 'assdwd', NULL, '', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (11, '4423', 'adsd', NULL, '', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (12, '4423', 'adsdw', NULL, 'asf', 'sdffdf', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (13, '4423', 'asdwdwd', NULL, 'adsds', 'sdsd', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (14, '4423', 'adswdwd', NULL, '', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (15, '4423', 'adswdwd', NULL, 'fsdf', 'sdfdf', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (16, '4423', 'asdawdwd', NULL, '', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (17, '0017J', '23123', NULL, '', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (18, '1089', '2323', NULL, 'addw', 'awdw', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (19, '1089', 'adwdwd', NULL, 'dawdw', 'dwdawd', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (20, '1089', 'adsd', NULL, 'dawdw', 'dwdawd', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (21, '4423', 'awdwadw', NULL, '', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (22, '4423', 'dad', NULL, '', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (23, '4423', 'sdfe', NULL, '', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (24, '4423', 'awr', NULL, '', '', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (25, '4423', '1231232323', NULL, 'asd', 'adwd', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (26, '4423', '213213', NULL, 'asd', 'adwd', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (27, '4423', 'sdgfdgfg', NULL, 'kl;l', 'l&#039;;l&#039;', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (28, '4423', 'awdd', NULL, 'kl;l', 'l&#039;;l&#039;', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (29, '4423', 'awd', NULL, 'kl;l', 'l&#039;;l&#039;', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (30, '4423', 'adsad', NULL, 'kl;l', 'l&#039;;l&#039;', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (31, '4423', 'adsd', NULL, 'kl;l', 'l&#039;;l&#039;', NULL, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (32, '4423', '7896667', NULL, 'batch001', 'lot0001', 0, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (33, '4423', '789978', NULL, 'batch001', 'lot0001', 0, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (34, '4423', '56757', NULL, 'batch001', 'lot0001', 0, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (35, '4423', '78979', NULL, 'batch001', 'lot0001', 0, NULL, NULL);
INSERT INTO item_serials (`id`, `item_code`, `serial_no`, `trans_date`, `batch_no`, `lot_no`, `is_used`, `person_id`, `reference`) VALUES (36, '4423', '12345', NULL, 'batch001', 'lot0001', 0, NULL, NULL);


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
  `update_date` datetime DEFAULT NULL,
  `inactive` int(11) DEFAULT '0',
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
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

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
  `menu_cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_cat_name` varchar(150) NOT NULL,
  `menu_sched_id` int(11) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  PRIMARY KEY (`menu_cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (1, 'Croquers', 0, '2017-02-01 09:04:06', 1);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (2, 'Batter Balls', 0, '2017-02-01 09:04:06', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (3, 'Sausages', 0, '2017-02-01 09:04:06', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (4, 'Fries', 0, '2017-02-01 09:04:06', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (5, 'Sauces', 0, '2017-02-01 09:04:06', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (6, 'Beers', 0, '2017-02-01 09:04:06', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (7, 'Cider', 0, '2017-02-01 09:04:06', 1);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (8, 'Ciders', 0, '2017-02-01 09:04:06', 1);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (9, 'SOFTDRINKS', 0, '2017-02-01 09:04:06', 1);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (10, 'WATER', 0, '2017-02-01 09:04:06', 1);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (11, 'EXTRA', 0, '2017-02-12 14:55:05', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (12, 'Milkshakes', 0, '2017-03-05 13:05:32', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (13, 'Wings', 0, '2017-04-11 14:33:58', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (14, 'Rice', 0, '2017-04-20 13:02:15', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (15, 'Buy1Get1Booky', 0, '2017-06-07 11:31:37', 1);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (16, 'Fish n\' Chips', 0, '2017-10-11 16:56:39', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (17, 'Floats', 0, '2017-10-11 17:04:19', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (18, 'Beverages', 0, '2017-10-11 17:10:31', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (19, 'Baskets', 0, '2017-10-11 17:21:37', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (20, 'Sandwich', 0, '2017-10-12 14:08:44', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (21, 'BOGO Draft', 0, '2017-11-24 13:43:14', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (22, 'Desserts', 0, '2017-11-24 13:47:01', 0);
INSERT INTO menu_categories (`menu_cat_id`, `menu_cat_name`, `menu_sched_id`, `reg_date`, `inactive`) VALUES (23, 'FOOD PANDA', 0, '2017-12-14 14:36:59', 0);


#
# TABLE STRUCTURE FOR: menu_modifiers
#

DROP TABLE IF EXISTS menu_modifiers;

CREATE TABLE `menu_modifiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `mod_group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;

INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (1, 1, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (2, 2, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (3, 3, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (4, 4, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (5, 5, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (6, 6, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (7, 7, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (8, 8, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (9, 9, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (10, 10, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (11, 11, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (12, 12, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (13, 13, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (14, 14, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (15, 15, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (16, 16, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (17, 17, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (18, 18, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (19, 19, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (20, 20, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (21, 21, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (22, 22, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (23, 23, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (24, 24, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (25, 25, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (26, 26, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (27, 27, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (28, 28, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (29, 29, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (30, 30, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (31, 31, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (32, 32, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (33, 33, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (34, 34, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (35, 35, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (36, 36, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (37, 37, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (38, 38, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (39, 39, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (40, 40, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (41, 41, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (42, 42, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (43, 43, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (44, 44, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (45, 81, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (46, 82, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (47, 83, 4);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (48, 83, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (49, 84, 4);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (50, 84, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (57, 90, 5);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (58, 91, 5);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (59, 92, 5);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (60, 93, 5);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (61, 94, 5);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (65, 98, 7);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (66, 96, 6);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (67, 109, 6);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (68, 110, 6);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (69, 111, 6);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (70, 111, 8);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (71, 112, 10);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (72, 112, 6);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (73, 114, 6);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (74, 115, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (75, 116, 3);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (76, 109, 11);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (77, 110, 11);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (78, 117, 6);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (79, 117, 11);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (80, 118, 6);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (81, 118, 11);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (82, 120, 6);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (83, 127, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (84, 126, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (86, 129, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (87, 130, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (88, 131, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (89, 132, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (90, 133, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (91, 134, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (92, 135, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (93, 136, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (94, 137, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (95, 138, 1);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (96, 139, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (97, 140, 2);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (98, 128, 5);
INSERT INTO menu_modifiers (`id`, `menu_id`, `mod_group_id`) VALUES (99, 125, 5);


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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: menu_schedules
#

DROP TABLE IF EXISTS menu_schedules;

CREATE TABLE `menu_schedules` (
  `menu_sched_id` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(150) NOT NULL,
  `inactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`menu_sched_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: menu_subcategories
#

DROP TABLE IF EXISTS menu_subcategories;

CREATE TABLE `menu_subcategories` (
  `menu_sub_cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_sub_cat_name` varchar(150) NOT NULL,
  `reg_date` datetime DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  PRIMARY KEY (`menu_sub_cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO menu_subcategories (`menu_sub_cat_id`, `menu_sub_cat_name`, `reg_date`, `inactive`) VALUES (1, 'FOOD', '2017-02-01 09:04:06', 0);
INSERT INTO menu_subcategories (`menu_sub_cat_id`, `menu_sub_cat_name`, `reg_date`, `inactive`) VALUES (2, 'BEVERAGE', '2017-02-01 09:04:06', 0);
INSERT INTO menu_subcategories (`menu_sub_cat_id`, `menu_sub_cat_name`, `reg_date`, `inactive`) VALUES (3, 'PROMO', '2017-06-07 11:34:15', 0);


#
# TABLE STRUCTURE FOR: menus
#

DROP TABLE IF EXISTS menus;

CREATE TABLE `menus` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_code` varchar(100) DEFAULT NULL,
  `menu_barcode` varchar(255) DEFAULT NULL,
  `menu_name` varchar(255) DEFAULT NULL,
  `menu_short_desc` varchar(255) DEFAULT NULL,
  `menu_cat_id` int(11) NOT NULL,
  `menu_sub_cat_id` int(11) DEFAULT NULL,
  `menu_sched_id` int(11) DEFAULT '0',
  `cost` double DEFAULT '0',
  `reg_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `no_tax` int(1) DEFAULT '0',
  `free` int(1) DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  `costing` double DEFAULT '0',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=latin1;

INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (1, 'MNU001', 'MNU001', 'Chicken', 'Chicken', 5, 1, 0, '160', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (2, 'MNU002', 'MNU002', 'Chicken w/Fries', 'Chicken with Fries', 1, 1, 0, '190', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (3, 'MNU003', 'MNU003', 'Meatballs', 'Meatballs', 1, 1, 0, '160', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (4, 'MNU004', 'MNU004', 'Meat Balls w/ Fries', 'Meat Balls with Fries', 1, 1, 0, '190', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (5, 'MNU005', 'MNU005', 'Cheese', 'Cheese', 19, 1, 0, '160', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (6, 'MNU006', 'MNU006', 'Cheese w/ Fries', 'Cheese with Fries', 1, 1, 0, '190', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (7, 'MNU007', 'MNU007', 'Oreos', 'Oreos', 2, 1, 0, '150', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (8, 'MNU008', 'MNU008', 'Oreo A la mode', 'Oreo Ala Mode', 2, 1, 0, '170', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (9, 'MNU009', 'MNU009', 'Mars', 'Mars', 2, 1, 0, '150', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (10, 'MNU010', 'MNU010', 'Mars A la mode', 'Mars A la mode', 2, 1, 0, '170', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (11, 'MNU011', 'MNU011', 'Filipinos', 'Filipinos', 2, 1, 0, '120', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (12, 'MNU012', 'MNU012', 'Filipinos w/ Ice Crm', 'Filipinos with Ice Cream', 2, 1, 0, '150', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (13, 'MNU013', 'MNU013', 'Cookie Dough', 'Cookie Dough', 2, 1, 0, '150', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (14, 'MNU014', 'MNU014', 'Cookie A la mode', 'Cookie Dough A la mode', 2, 1, 0, '170', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (15, 'MNU015', 'MNU015', 'Funnel Cake A la mode', 'Funnel Cake A la mode', 22, 1, 0, '180', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (16, 'MNU016', 'MNU016', 'FunnelCake w/ Icream', 'Funnel Cake with Icream', 2, 1, 0, '150', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (17, 'MNU017', 'MNU017', 'Hungarian', 'Hungarian', 3, 1, 0, '190', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (18, 'MNU018', 'MNU018', 'Hungarian Sausages', 'Hungarian Sausages', 3, 1, 0, '220', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (19, 'MNU019', 'MNU019', 'Bavarian', 'Bavarian', 3, 1, 0, '190', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (20, 'MNU020', 'MNU020', 'Bavarian w/ Fries', 'Bavarian w/ Fries', 3, 1, 0, '220', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (21, 'MNU021', 'MNU021', 'Frankfurter', 'Frankfurter', 3, 1, 0, '190', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (22, 'MNU022', 'MNU022', 'Frankfurter Sausages', 'Frankfurter Sausages', 3, 1, 0, '220', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (23, 'MNU023', 'MNU023', 'Italian', 'Italian', 3, 1, 0, '190', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (24, 'MNU024', 'MNU024', 'Italian w/ Fries', 'Italian w/ Fries', 3, 1, 0, '220', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (25, 'MNU025', 'MNU025', 'Pepperoni', 'Pepperoni', 3, 1, 0, '190', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (26, 'MNU026', 'MNU026', 'Pepperoni w/ Fries', 'Pepperoni w/ Fries', 3, 1, 0, '220', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (27, 'MNU027', 'MNU027', 'European', 'European', 3, 1, 0, '190', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (28, 'MNU028', 'MNU028', 'European w/ Fries', 'European w/ Fries', 3, 1, 0, '220', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (29, 'MNU029', 'MNU029', 'Belgian Regular', 'Belgian Regular', 4, 1, 0, '95', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (30, 'MNU030', 'MNU030', 'Belgian Large', 'Belgian Large', 4, 1, 0, '180', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (31, 'MNU031', 'MNU031', 'Twister Regular', 'Twister Regular', 4, 1, 0, '95', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (32, 'MNU032', 'MNU032', 'Twister Large', 'Twister Large', 4, 1, 0, '180', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (33, 'MNU033', 'MNU033', 'Seasoned Wedges', 'Seasoned Wedges', 4, 1, 0, '95', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (34, 'MNU034', 'MNU034', 'Seasoned Wedges  L', 'Seasoned Wedges Large', 4, 1, 0, '180', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (35, 'MNU035', 'MNU035', 'Criss Cut', 'Criss Cut', 4, 1, 0, '95', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (36, 'MNU036', 'MNU036', 'Criss Cut Large', 'Criss-Cut Large', 4, 1, 0, '180', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (37, 'MNU037', 'MNU037', 'House Cut Lattice', 'House Cut Lattice', 4, 1, 0, '95', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (38, 'MNU038', 'MNU038', 'House Cut Lattice L', 'House Cut Lattice Large', 4, 1, 0, '180', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (39, 'MNU039', 'MNU039', 'Pinwheel Wedges', 'Pinwheel Wedges', 4, 1, 0, '95', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (40, 'MNU040', 'MNU040', 'Pinwheel Wedges L', 'Pinwheel Wedges Large', 4, 1, 0, '180', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (41, 'MNU041', 'MNU041', 'Sweet Potato Reg', 'Sweet Potato Reg', 4, 1, 0, '95', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (42, 'MNU042', 'MNU042', 'Sweet Potato Large', 'Sweet Potato Large', 4, 1, 0, '180', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (43, 'MNU043', 'MNU043', 'Hash Brown', 'Hash Brown', 4, 1, 0, '95', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (44, 'MNU044', 'MNU044', 'Hash Brown L', 'Hash Brown Large', 4, 1, 0, '180', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (45, 'MNU045', 'MNU045', 'SOS BBQ', 'BBQ', 5, 1, 1, '20', '2017-02-01 09:04:06', NULL, 0, NULL, 0, NULL);
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (46, 'MNU046', 'MNU046', 'SOS Buffalo ', 'Buffalo ', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (47, 'MNU047', 'MNU047', 'SOS Garlic Aoli', 'Garlic Aoli', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (48, 'MNU048', 'MNU048', 'SOS Honey Mustard', 'Honey Mustard', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (49, 'MNU049', 'MNU049', 'SOS Siracha Mayo', 'Siracha Mayo', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (50, 'MNU050', 'MNU050', 'SOS Choclate Ganache', 'Choclate Ganache', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (51, 'MNU051', 'MNU051', 'SOS Cinnamon', 'Cinnamon', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (52, 'MNU052', 'MNU052', 'Liquid Cheesecake', 'Liquid Cheesecake', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (53, 'MNU053', 'MNU053', 'SOS Matcha Cream', 'Matcha Cream', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (54, 'MNU054', 'MNU054', 'SOS Peanut Butter', 'Peanut Butter', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (55, 'MNU055', 'MNU055', 'SOS Salted Caramel', 'Salted Caramel', 5, 1, 0, '20', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (56, 'MNU056', 'MNU056', 'Draft Beer SML', 'San Mig Light (Draft)', 6, 2, 0, '120', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (57, 'MNU057', 'MNU057', ' Hoegarden ', 'Hoegarden ', 6, 2, 0, '195', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (58, 'MNU058', 'MNU058', 'Hoegarden Rosse', 'Hoegarden Rosse', 6, 2, 0, '195', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (59, 'MNU059', 'MNU059', 'Becks', 'Becks', 6, 2, 0, '135', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (60, 'MNU060', 'MNU060', 'Asahi', 'Asahi', 6, 2, 0, '135', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (61, 'MNU061', 'MNU061', 'Stella Artios', 'Stella Artios', 6, 2, 0, '135', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (62, 'MNU062', 'MNU062', 'Carlsberg', 'Carlsberg', 6, 2, 0, '155', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (63, 'MNU063', 'MNU063', 'Budweiser', 'Budweiser', 6, 2, 0, '155', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (64, 'MNU064', 'MNU064', 'Corona', 'Corona', 6, 2, 0, '155', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (65, 'MNU065', 'MNU065', 'Blue Moon', 'Blue Moon', 6, 2, 0, '175', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (66, 'MNU066', 'MNU066', 'Sapporro', 'Sapporro', 6, 2, 0, '185', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (67, 'MNU067', 'MNU067', 'Pale Pilsen', 'Pale Pilsen', 6, 2, 0, '80', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (68, 'MNU068', 'MNU068', 'San Mig Light', 'San Mig Light', 6, 2, 0, '80', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (69, 'MNU069', 'MNU069', 'San Miguel Premium', 'San Miguel Premium', 6, 2, 0, '100', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (70, 'MNU070', 'MNU070', 'Strongbow Cider', 'Strongbow Cider', 7, 2, 0, '200', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (71, 'MNU071', 'MNU071', 'Maeloc', 'Maeloc', 6, 2, 0, '165', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (72, 'MNU072', 'MNU072', 'Coke ', 'Coke ', 18, 2, 0, '60', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (73, 'MNU073', 'MNU073', 'Coke Zero', 'Coke Zero', 18, 2, 0, '60', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (74, 'MNU074', 'MNU074', 'Coke Light', 'Coke Light', 18, 2, 0, '60', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (75, 'MNU075', 'MNU075', 'Bottled Water', 'Bottled Water', 18, 2, 0, '40', '2017-02-01 09:04:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (76, 'MNU076', 'MNU076', 'Perrier', 'Perrier', 18, 2, 0, '120', '2017-02-01 09:04:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (77, 'MNU001', 'MNU001', 'Chicken', 'Chicken', 1, 1, 0, '160', '2017-02-08 17:18:06', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (78, 'MNU002', 'MNU002', 'Chicken w/ Fries', 'Chicken with Fries', 1, 1, 0, '190', '2017-02-08 17:18:39', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (79, 'M001', 'M001', 'Marinara', 'Marinara', 5, 1, 0, '20', '2017-02-10 15:34:50', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (80, 'IC003', 'IC003', 'ice cream', 'ice cream', 11, 1, 0, '30', '2017-02-12 14:55:58', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (81, 'BB110', 'BB110', 'BBA w/dip', 'assorted batter balls', 2, 1, 0, '120', '2017-02-21 13:04:33', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (82, 'BBA11', 'BBA11', 'bba w/ DIP ICECREAM', 'BATTER BALLS ASSORTTED', 2, 1, 0, '150', '2017-02-21 13:07:59', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (83, 'AB1012', 'AB1012', 'Assorted balls', 'ASSORTED BALLS', 2, 1, 0, '150', '2017-02-24 10:54:12', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (84, 'AB1013', 'AB1013', 'Assorted a la mode', 'Assorted a la mode', 2, 1, 0, '170', '2017-02-24 11:30:28', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (85, 'MILKSHAKE1', 'MILKSHAKE1', 'Milo Milkshake', 'Milo Milkshake', 12, 2, 0, '150', '2017-03-05 13:06:23', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (86, 'MILKSHAKE2', 'MILKSHAKE2', 'OREO MILKSHAKE', 'OREO MILKSHAKE', 12, 2, 0, '150', '2017-03-05 13:06:49', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (87, 'MILKSHAKE3', 'MILKSHAKE3', 'MARS MILKSHAKE', 'MARS MILKSHAKE', 12, 2, 0, '150', '2017-03-05 13:07:05', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (88, 'MILKSHAKE4', 'MILKSHAKE4', 'FILIPINOSMILKSHAKE', 'FILIPINOS MILKSHAKE', 12, 2, 0, '150', '2017-03-05 13:07:21', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (89, 'MILKSHAKE5', 'MILKSHAKE5', 'COOKIE DOUGH MILKSHA', 'COOKIE DOUGH MILKSHAKE', 12, 2, 0, '150', '2017-03-05 13:07:39', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (90, 'WINGS', 'WINGS', 'Garlic Parmesan', 'Garlic Parmesan', 13, 1, 0, '210', '2017-04-11 14:36:35', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (91, 'WINGS', 'WINGS', 'Honey Truffle', 'Honey Truffle', 13, 1, 0, '210', '2017-04-11 14:40:20', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (92, 'WINGS', 'WINGS', 'Salted Egg', 'Salted Egg', 13, 1, 0, '210', '2017-04-11 14:41:04', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (93, 'WINGS', 'WINGS', 'Buffalo Wings', 'Buffalo Wings', 13, 1, 0, '210', '2017-04-11 14:42:35', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (94, 'WINGS', 'WINGS', 'BBQ Wings', 'BBQ Wings', 13, 1, 0, '190', '2017-04-11 14:43:10', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (95, 'Plain Rice', '0', 'Plain Rice', '0', 14, 1, 0, '30', '2017-04-20 13:04:29', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (96, 'Fish n\' Chips ', '0', 'Fish n\' Chips ', 'Fish n\' Chips ', 16, 1, 0, '210', '2017-04-20 14:18:46', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (97, '001122', '001122', 'Fish & Chips SS', 'Fish & Chips ', 1, 1, 0, '210', '2017-04-24 20:30:10', NULL, 1, 0, 1, '210');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (98, 'Fish & Chips CC', '0', 'Fish & Chips CC', 'Fish & Chips CC', 1, 1, 0, '210', '2017-04-25 15:15:18', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (99, 'BOOKYBOGO1', 'BOOKYBOGO1', 'BOOKY OREO BATERBLLS', 'OREO BATTERBALLS FREE', 5, 1, 0, '0', '2017-06-07 11:35:04', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (100, 'BOOKYBOGO2', 'BOOKYBOGO2', 'BOOKY WINGSSALTEGG', 'CHICKEN WINGS SALTED EGG FREE', 15, 1, 0, '0', '2017-06-07 11:36:06', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (101, 'BOOKYBOGO3', 'BOOKYBOGO3', 'BOOKY WING HONEY TRU', 'CHICKEN WINGS HONEY TRUFFLE FREE', 15, 1, 0, '0', '2017-06-07 11:37:01', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (102, '[MILKSHAKE6]', 'MILKSHAKE6', 'Butter Caramel', 'Butter Caramel', 12, 1, 0, '150', '2017-10-11 17:00:30', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (103, '[MILKSHAKE7]', 'MILKSHAKE7', 'Strawberry & Liquid cheesecake', 'Strawberry & Liquid chessecake', 12, 2, 0, '150', '2017-10-11 17:02:04', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (104, '[MIKSHAKE8]', 'MILKSHAKE8', 'Macapuno ', 'Macapuno', 12, 1, 0, '150', '2017-10-11 17:02:49', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (105, '[MILKSHAKE9]', 'MILKSHAKE9', 'Bubblegum', 'Bubblegum', 12, 2, 0, '150', '2017-10-11 17:03:30', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (106, '[FLOAT1]', 'FLOAT1', 'Coke Float', 'Coke Float', 17, 1, 0, '150', '2017-10-11 17:05:39', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (107, '[FLOAT2]', 'FLOAT2', 'Rootbeer Float', 'Rootbeer Float', 17, 2, 0, '150', '2017-10-11 17:06:15', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (108, '[FLOAT3]', 'FLOAT3', 'Orange Float', 'Orange Float', 17, 2, 0, '150', '2017-10-11 17:06:52', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (109, '[BASKETS1]', 'BASKETS1', 'Chicken Fingers', 'Chicken Fingers', 19, 1, 0, '190', '2017-10-11 17:24:26', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (110, '[BASKETS2]', 'BASKETS2', 'Swedish Meatballs', 'Swedish Meatballs', 19, 1, 0, '210', '2017-10-11 17:26:11', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (111, '[BASKETS3]', 'BASKETS3', 'Chicken Popcorn', 'Chicken Popcorn', 19, 1, 0, '190', '2017-10-11 17:28:00', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (112, '[BASKETS3]', 'BASKETS3', 'Double-cheese Balls', 'Double-cheese Balls', 19, 1, 0, '190', '2017-10-11 17:33:35', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (113, '[SANDWICH1]', 'SANDWICH]', 'Sriracha Chicken Sandwich', 'Sriracha Chicken Sandwich', 20, 1, 0, '250', '2017-10-12 14:10:33', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (114, '[SANDWICH2]', 'SANDWICH2', 'SCS w/ Fries', 'SCS w/ Fries', 20, 1, 0, '280', '2017-10-12 14:11:37', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (115, 'Fries', 'Fries', 'Shoestring regular', 'Shoestring Regular', 4, 1, 0, '75', '2017-10-21 20:34:10', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (116, 'Fries', 'Fries', 'Shoesting Large ', 'Shoestring Large', 4, 1, 0, '140', '2017-10-21 20:35:33', NULL, 0, 0, 1, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (117, '[BASKET4]', 'Basket 4', 'Onion Rings', 'Onion Rings', 19, 1, 0, '190', '2017-11-24 13:29:54', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (118, '[BASKETS6]', 'BASKET6', 'Calamari', 'Calamari', 19, 1, 0, '190', '2017-11-24 13:37:43', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (119, '[SANDWICH3]', 'SANDWICH3', 'Fritoss Burger', 'Fritoss Burger', 20, 1, 0, '250', '2017-11-24 13:40:19', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (120, '[SANDWICH4]', 'SANDWICH4', 'Fritoss Burger w/ fries', 'Fritoss Burger w/ fries', 20, 1, 0, '280', '2017-11-24 13:41:58', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (121, 'BOGO', 'BOGO', 'BOGO Draft beer', 'BOGO Draft beer', 21, 2, 0, '120', '2017-11-24 13:44:03', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (122, '[ICEDTEA1]', '[ICEDTEA1]', 'Iced Tea', 'Iced Tea', 18, 2, 0, '60', '2017-11-24 13:45:39', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (123, '[LEMONADE1]', '[LEMONADE1]', 'Lemonade', 'Lemonade', 18, 2, 0, '60', '2017-11-24 13:46:19', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (124, '[DESSERT1]', 'DESSERT1', 'Fritoss Sundae', 'Fritoss Sundae', 22, 1, 0, '180', '2017-11-24 13:50:00', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (125, 'Food panda', 'Food panda', 'BBQ wings', 'BBQ wings', 23, 1, 0, '190', '2017-11-30 14:00:45', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (126, 'Food panda', 'Food panda', 'Cheese coquers ', 'with dip', 23, 1, 0, '160', '2017-12-08 18:49:02', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (127, 'Food panda', 'Food panda', 'Frank with dip', 'frank', 23, 1, 0, '190', '2017-12-08 18:49:52', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (128, 'Food panda', 'Food panda', 'Buffalo Wings', 'Buffalo wings', 23, 1, 0, '190', '2017-12-14 14:45:56', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (129, 'Food panda', 'Food panda', 'Cheese w/ fries', 'Cheese w/ fries', 23, 1, 0, '190', '2017-12-14 14:47:52', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (130, 'Food panda', 'Food panda', 'Hungarian', 'Hungarian', 23, 1, 0, '190', '2017-12-14 14:49:50', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (131, 'Food panda', 'Food panda', 'Mars ', 'Mars', 23, 1, 0, '120', '2017-12-14 14:52:36', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (132, 'Food panda', 'Food panda', 'Mars w/ Ice cream', 'Mars w/ Ice cream', 23, 1, 0, '150', '2017-12-14 14:53:34', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (133, 'Foo panda', 'Food panda', 'Cookie dough', 'Cookie dough', 23, 1, 0, '120', '2017-12-14 14:54:22', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (134, 'Food panda', 'Food panda', 'Cookie w/ Ice cream', 'Cookie w/ Ice cream', 23, 1, 0, '150', '2017-12-14 14:55:28', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (135, 'Food panda', 'Food panda', 'Oreo', 'Oreo', 23, 1, 0, '120', '2017-12-14 14:59:29', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (136, 'Food panda', 'Food panda', 'Oreo w/ Ice cream', 'Oreo w/ Ice cream', 23, 1, 0, '150', '2017-12-14 15:00:13', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (137, 'Food panda', 'Food panda', 'Funnel cake', 'Funnel cake', 23, 1, 0, '120', '2017-12-14 15:00:59', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (138, 'Food panda', 'Food panda', 'Funnel cake w/ Ice cream', 'Funnel cake w/ Ice cream', 23, 1, 0, '150', '2017-12-14 15:01:49', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (139, 'Food panda', 'Food panda', 'Frank w/ fries', 'Frank w/ fries', 23, 1, 0, '220', '2017-12-14 15:04:07', NULL, 0, 0, 0, '0');
INSERT INTO menus (`menu_id`, `menu_code`, `menu_barcode`, `menu_name`, `menu_short_desc`, `menu_cat_id`, `menu_sub_cat_id`, `menu_sched_id`, `cost`, `reg_date`, `update_date`, `no_tax`, `free`, `inactive`, `costing`) VALUES (140, 'Food panda', 'Food panda', 'Hungarian w/ fries', 'Hungarian w/ fries', 23, 1, 0, '220', '2017-12-14 15:05:04', NULL, 0, 0, 0, '0');


#
# TABLE STRUCTURE FOR: modifier_group_details
#

DROP TABLE IF EXISTS modifier_group_details;

CREATE TABLE `modifier_group_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_group_id` int(11) NOT NULL,
  `mod_id` int(11) NOT NULL,
  `default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=latin1;

INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (1, 1, 1, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (2, 1, 2, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (3, 1, 3, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (4, 1, 4, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (5, 1, 5, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (6, 1, 6, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (7, 1, 7, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (8, 1, 8, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (9, 1, 9, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (10, 1, 10, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (11, 1, 11, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (12, 2, 1, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (13, 2, 2, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (14, 2, 3, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (15, 2, 4, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (16, 2, 5, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (17, 2, 6, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (18, 2, 7, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (19, 2, 8, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (20, 2, 9, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (21, 2, 10, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (22, 2, 11, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (23, 2, 14, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (24, 2, 12, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (25, 2, 13, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (26, 3, 14, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (27, 3, 12, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (28, 3, 13, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (29, 3, 3, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (30, 1, 15, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (31, 2, 15, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (32, 4, 16, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (35, 4, 18, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (37, 4, 20, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (39, 4, 22, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (40, 5, 26, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (42, 5, 27, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (43, 5, 12, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (44, 5, 13, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (45, 5, 3, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (46, 5, 14, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (47, 6, 0, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (69, 6, 12, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (70, 6, 13, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (71, 6, 3, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (72, 6, 14, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (73, 6, 1, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (74, 6, 2, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (75, 6, 4, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (76, 6, 5, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (77, 6, 30, 1);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (78, 7, 14, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (79, 7, 13, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (80, 7, 12, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (81, 7, 3, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (82, 7, 31, 1);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (83, 7, 2, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (84, 7, 4, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (85, 7, 5, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (86, 7, 1, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (87, 11, 5, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (88, 11, 1, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (89, 11, 2, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (90, 11, 15, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (91, 11, 4, 0);
INSERT INTO modifier_group_details (`id`, `mod_group_id`, `mod_id`, `default`) VALUES (92, 11, 3, 0);


#
# TABLE STRUCTURE FOR: modifier_groups
#

DROP TABLE IF EXISTS modifier_groups;

CREATE TABLE `modifier_groups` (
  `mod_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `mandatory` int(1) DEFAULT '0',
  `multiple` int(10) DEFAULT '0',
  `inactive` int(1) DEFAULT '0',
  PRIMARY KEY (`mod_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (1, 'With Dip', 0, 12, 0);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (2, 'With Dip & Fries', 0, 16, 0);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (3, 'Flavor', 0, 4, 0);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (4, 'assorted', 0, 10, 0);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (5, 'fries or rice', 0, 8, 0);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (6, 'Shoestring', 0, 20, 0);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (7, 'Crinkle Cut', 0, 20, 1);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (8, 'Sriracha Mayo', 0, 20, 0);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (9, 'Garlic Aioli', 0, 20, 0);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (10, 'Marinara', 0, 20, 0);
INSERT INTO modifier_groups (`mod_group_id`, `name`, `mandatory`, `multiple`, `inactive`) VALUES (11, 'Dips', 0, 20, 0);


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
  `mod_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `cost` double(11,0) DEFAULT '0',
  `has_recipe` int(1) DEFAULT '0',
  `reg_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `inactive` int(1) DEFAULT '0',
  PRIMARY KEY (`mod_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (1, 'Garlic Aioli', '0', 0, '2017-02-09 14:39:35', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (2, 'Buffalo', '0', 0, '2017-02-09 14:39:52', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (3, 'BBQ', '0', 0, '2017-02-09 14:40:11', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (4, 'Honey Mustard', '0', 0, '2017-02-09 14:40:24', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (5, 'Sriracha Mayo', '0', 0, '2017-02-09 14:40:50', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (6, 'Choco Ganache', '0', 0, '2017-02-09 14:41:20', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (7, 'Cinnamon', '0', 0, '2017-02-09 14:41:31', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (8, 'Liquid Cheesecake', '0', 0, '2017-02-09 14:41:48', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (9, 'Matcha Cream', '0', 0, '2017-02-09 14:42:00', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (10, 'Peanut Butter', '0', 0, '2017-02-09 14:42:10', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (11, 'Salted Caramel', '0', 0, '2017-02-09 14:42:25', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (12, 'Cheese', '0', 0, '2017-02-09 14:43:03', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (13, 'Sour Cream', '0', 0, '2017-02-09 14:43:13', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (14, 'Plain', '0', 0, '2017-02-09 14:44:00', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (15, 'Marinara', '0', 0, '2017-02-10 15:29:14', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (16, ' OREOS', '0', 0, '2017-02-24 10:51:03', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (17, ' OREO W/ ICECREAM', '0', 0, '2017-02-24 10:51:21', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (18, ' MARS', '0', 0, '2017-02-24 10:51:50', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (19, ' MARS W/ ICE CREAM', '0', 0, '2017-02-24 10:52:01', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (20, ' FILIPINOS', '0', 0, '2017-02-24 10:52:12', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (21, ' FILIPINOS W/ ICE CRM', '0', 0, '2017-02-24 10:52:28', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (22, ' COOKIE DOUGH', '0', 0, '2017-02-24 10:52:39', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (23, ' COOKIE W/ ICE CREAM', '0', 0, '2017-02-24 10:52:54', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (24, ' FUNNEL CAKE', '0', 0, '2017-02-24 10:53:19', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (25, ' FUNNELCAKE / ICREAM', '0', 0, '2017-02-24 10:53:31', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (26, 'Fries', '0', 0, '2017-04-11 15:07:17', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (27, 'Rice', '0', 0, '2017-04-11 15:11:21', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (28, 'Rice', '0', 0, '2017-04-11 15:11:38', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (29, 'Rice', '0', 0, '2017-04-20 13:00:37', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (30, 'Belgian', '0', 0, '2017-04-25 15:04:48', NULL, 0);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (31, 'Crinkle Cut', '0', 0, '2017-04-25 15:05:02', NULL, 1);
INSERT INTO modifiers (`mod_id`, `name`, `cost`, `has_recipe`, `reg_date`, `update_date`, `inactive`) VALUES (32, 'Belgian', '0', 0, '2017-10-11 16:52:40', NULL, 0);


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
  `read_type` tinyint(2) NOT NULL,
  `read_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `old_total` double DEFAULT NULL,
  `grand_total` double DEFAULT NULL COMMENT 'GT for ZRead only',
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `scope_from` datetime DEFAULT NULL,
  `scope_to` datetime DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (1, 'SNDISC', 'Senior Citizen Discount', '20', 1, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (2, 'PWDISC', 'Person WIth Disability', '20', 1, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (3, 'REG5DISC', '5 Percent DIscount', '5', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (4, 'REG50DISC', '50 Percent Discount', '50', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (5, 'REG10DISC', '10 Percent Discount', '10', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (6, 'REG20DISC', '20 Percent Discount', '20', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (7, 'REG08DISC', '8 Percent Discount', '8', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (8, 'REG12DISC', '12 Percent Discount', '12', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (9, 'REG30DISC', '30 Percent Discount', '30', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (10, 'REG70DISC', '70 Percent Discount', '70', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (11, 'REG40DISC', '40 Percent Discount', '40', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (12, 'REG60DISC', '60 Percent Discount', '60', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (13, 'REG8DISC', '8 Percent Discount', '8', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (14, '15PercentEmpDiscount', '15 Percent Employee Discount', '15', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (15, 'Metrobank20Percent', 'Metrobank 20 Percent', '20', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (16, 'EATIGO10', 'EATIGO 10%', '10', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (17, 'EATIGO20', 'EATIGO 20%', '20', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (18, 'EATIGO30', 'EATIGO 30%', '30', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (19, 'EATIGO40', 'EATIGO 40%', '40', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (20, 'EATIGO50', 'EATIGO 50%', '50', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (21, 'BIGDISH10', 'BIG DISH 10%', '10', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (22, 'BIGDISH20', 'BIG DISH 20%', '20', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (23, 'BIGDISH30', 'BIG DISH 30%', '30', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (24, 'BIGDISH40', 'BIG DISH 40%', '40', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (25, 'BIGDISH50', 'BIG DISH 50%', '50', 0, 0, 0);
INSERT INTO receipt_discounts (`disc_id`, `disc_code`, `disc_name`, `disc_rate`, `no_tax`, `fix`, `inactive`) VALUES (26, '20PrecentDiscount', '20 Percent Discount', '20', 0, 0, 0);


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
  `loyalty_for_amount` double DEFAULT '0',
  `loyalty_to_points` double DEFAULT '0',
  `backup_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

INSERT INTO settings (`id`, `no_of_receipt_print`, `no_of_order_slip_print`, `controls`, `local_tax`, `kitchen_printer_name`, `kitchen_beverage_printer_name`, `kitchen_printer_name_no`, `kitchen_beverage_printer_name_no`, `open_drawer_printer`, `loyalty_for_amount`, `loyalty_to_points`, `backup_path`) VALUES (1, 1, 1, '1=>dine in,6=>takeout,8=>food panda', '0', 'EC Printer EC-PM-530B', 'EC Printer EC-PM-530B', 1, 1, 'CASH DRAWER', '100', '10', 'D:/dine/backup');


#
# TABLE STRUCTURE FOR: shift_entries
#

DROP TABLE IF EXISTS shift_entries;

CREATE TABLE `shift_entries` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `user_id` varchar(45) NOT NULL,
  `trans_date` datetime NOT NULL,
  PRIMARY KEY (`entry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO shift_entries (`entry_id`, `shift_id`, `amount`, `user_id`, `trans_date`) VALUES (1, 1, '5000', '1', '2018-01-25 10:33:21');


#
# TABLE STRUCTURE FOR: shifts
#

DROP TABLE IF EXISTS shifts;

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime DEFAULT NULL,
  `xread_id` int(11) DEFAULT NULL,
  `cashout_id` int(11) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`shift_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO shifts (`shift_id`, `user_id`, `check_in`, `check_out`, `xread_id`, `cashout_id`, `terminal_id`) VALUES (1, 1, '2018-01-25 10:33:21', NULL, NULL, NULL, 1);


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
# TABLE STRUCTURE FOR: stg_customers
#

DROP TABLE IF EXISTS stg_customers;

CREATE TABLE `stg_customers` (
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
  `is_member` tinyint(5) DEFAULT '0',
  `inactive` int(11) DEFAULT '0',
  `reg_date` datetime DEFAULT NULL,
  `debtor_ref` varchar(255) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`cust_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: subcategories
#

DROP TABLE IF EXISTS subcategories;

CREATE TABLE `subcategories` (
  `sub_cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `inactive` int(11) DEFAULT '0',
  PRIMARY KEY (`sub_cat_id`)
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO sync_logs (`sync_id`, `transaction`, `type`, `status`, `migrate_date`, `src_id`, `user_id`, `is_automated`) VALUES (1, 'trans_sales', 'add', 1, '2018-01-25 10:33:40', 1, 1, 0);
INSERT INTO sync_logs (`sync_id`, `transaction`, `type`, `status`, `migrate_date`, `src_id`, `user_id`, `is_automated`) VALUES (2, 'logs', 'add', 1, '2018-01-25 10:33:41', 4, 1, 0);
INSERT INTO sync_logs (`sync_id`, `transaction`, `type`, `status`, `migrate_date`, `src_id`, `user_id`, `is_automated`) VALUES (3, 'trans_sales_menus', 'add', 1, '2018-01-25 10:33:41', 1, 1, 0);
INSERT INTO sync_logs (`sync_id`, `transaction`, `type`, `status`, `migrate_date`, `src_id`, `user_id`, `is_automated`) VALUES (4, 'trans_sales_zero_rated', 'add', 1, '2018-01-25 10:33:41', 1, 1, 0);
INSERT INTO sync_logs (`sync_id`, `transaction`, `type`, `status`, `migrate_date`, `src_id`, `user_id`, `is_automated`) VALUES (5, 'trans_sales_no_tax', 'add', 1, '2018-01-25 10:33:41', 1, 1, 0);
INSERT INTO sync_logs (`sync_id`, `transaction`, `type`, `status`, `migrate_date`, `src_id`, `user_id`, `is_automated`) VALUES (6, 'trans_sales_tax', 'add', 1, '2018-01-25 10:33:42', 1, 1, 0);


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
  `user_id` int(11) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `reg_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `inactive` tinyint(4) DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
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
  `uom` varchar(50) DEFAULT NULL,
  `case` int(1) DEFAULT '0',
  `pack` int(1) DEFAULT '0',
  `price` double DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
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
  PRIMARY KEY (`receiving_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_refs
#

DROP TABLE IF EXISTS trans_refs;

CREATE TABLE `trans_refs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(55) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales
#

DROP TABLE IF EXISTS trans_sales;

CREATE TABLE `trans_sales` (
  `sales_id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile_sales_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `trans_ref` varchar(55) DEFAULT NULL,
  `void_ref` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `customer_id` varchar(11) DEFAULT NULL,
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
  `billed` int(4) DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO trans_sales (`sales_id`, `mobile_sales_id`, `type_id`, `trans_ref`, `void_ref`, `type`, `user_id`, `shift_id`, `terminal_id`, `customer_id`, `total_amount`, `total_paid`, `memo`, `table_id`, `guest`, `datetime`, `update_date`, `paid`, `reason`, `void_user_id`, `printed`, `inactive`, `waiter_id`, `split`, `serve_no`, `billed`, `sync_id`) VALUES (1, NULL, 10, NULL, NULL, 'takeout', 1, 1, 1, NULL, '360', '0', NULL, NULL, '0', '2018-01-25 10:33:34', NULL, 0, NULL, NULL, 0, 0, NULL, 0, 0, 0, 1);


#
# TABLE STRUCTURE FOR: trans_sales_charges
#

DROP TABLE IF EXISTS trans_sales_charges;

CREATE TABLE `trans_sales_charges` (
  `sales_charge_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) DEFAULT NULL,
  `charge_id` int(11) DEFAULT NULL,
  `charge_code` varchar(55) DEFAULT NULL,
  `charge_name` varchar(55) DEFAULT NULL,
  `rate` double DEFAULT NULL,
  `absolute` tinyint(1) DEFAULT '0',
  `amount` double DEFAULT '0',
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_charge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_discounts
#

DROP TABLE IF EXISTS trans_sales_discounts;

CREATE TABLE `trans_sales_discounts` (
  `sales_disc_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_disc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_items
#

DROP TABLE IF EXISTS trans_sales_items;

CREATE TABLE `trans_sales_items` (
  `sales_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) DEFAULT NULL,
  `line_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `no_tax` int(1) DEFAULT '0',
  `remarks` varchar(150) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_local_tax
#

DROP TABLE IF EXISTS trans_sales_local_tax;

CREATE TABLE `trans_sales_local_tax` (
  `sales_local_tax_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_local_tax_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: trans_sales_loyalty_points
#

DROP TABLE IF EXISTS trans_sales_loyalty_points;

CREATE TABLE `trans_sales_loyalty_points` (
  `loyalty_point_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) DEFAULT NULL,
  `card_id` int(11) DEFAULT NULL,
  `code` varchar(150) DEFAULT NULL,
  `cust_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `points` double DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`loyalty_point_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

#
# TABLE STRUCTURE FOR: trans_sales_menu_modifiers
#

DROP TABLE IF EXISTS trans_sales_menu_modifiers;

CREATE TABLE `trans_sales_menu_modifiers` (
  `sales_mod_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `mod_group_id` int(11) DEFAULT NULL,
  `line_id` int(11) DEFAULT NULL,
  `mod_id` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `kitchen_slip_printed` tinyint(1) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_mod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_menus
#

DROP TABLE IF EXISTS trans_sales_menus;

CREATE TABLE `trans_sales_menus` (
  `sales_menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) DEFAULT NULL,
  `line_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `no_tax` int(1) DEFAULT '0',
  `remarks` varchar(150) DEFAULT NULL,
  `kitchen_slip_printed` tinyint(1) DEFAULT NULL,
  `free_user_id` int(11) DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO trans_sales_menus (`sales_menu_id`, `sales_id`, `line_id`, `menu_id`, `price`, `qty`, `discount`, `no_tax`, `remarks`, `kitchen_slip_printed`, `free_user_id`, `sync_id`) VALUES (1, 1, 0, 121, '120', '1', '0', 0, NULL, 1, NULL, 3);
INSERT INTO trans_sales_menus (`sales_menu_id`, `sales_id`, `line_id`, `menu_id`, `price`, `qty`, `discount`, `no_tax`, `remarks`, `kitchen_slip_printed`, `free_user_id`, `sync_id`) VALUES (2, 1, 1, 121, '120', '1', '0', 0, NULL, 1, NULL, 3);
INSERT INTO trans_sales_menus (`sales_menu_id`, `sales_id`, `line_id`, `menu_id`, `price`, `qty`, `discount`, `no_tax`, `remarks`, `kitchen_slip_printed`, `free_user_id`, `sync_id`) VALUES (3, 1, 2, 121, '120', '1', '0', 0, NULL, 1, NULL, 3);


#
# TABLE STRUCTURE FOR: trans_sales_no_tax
#

DROP TABLE IF EXISTS trans_sales_no_tax;

CREATE TABLE `trans_sales_no_tax` (
  `sales_no_tax_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_no_tax_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO trans_sales_no_tax (`sales_no_tax_id`, `sales_id`, `amount`, `sync_id`) VALUES (1, 1, '0', 5);


#
# TABLE STRUCTURE FOR: trans_sales_payments
#

DROP TABLE IF EXISTS trans_sales_payments;

CREATE TABLE `trans_sales_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: trans_sales_tax
#

DROP TABLE IF EXISTS trans_sales_tax;

CREATE TABLE `trans_sales_tax` (
  `sales_tax_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) DEFAULT NULL,
  `name` varchar(55) DEFAULT NULL,
  `rate` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_tax_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO trans_sales_tax (`sales_tax_id`, `sales_id`, `name`, `rate`, `amount`, `sync_id`) VALUES (1, 1, 'VAT', '12', '38.571428571429', 6);


#
# TABLE STRUCTURE FOR: trans_sales_zero_rated
#

DROP TABLE IF EXISTS trans_sales_zero_rated;

CREATE TABLE `trans_sales_zero_rated` (
  `sales_zero_rated_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `sync_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sales_zero_rated_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO trans_sales_zero_rated (`sales_zero_rated_id`, `sales_id`, `amount`, `sync_id`) VALUES (1, 1, '0', 4);


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
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`) VALUES (10, 'sales', '00000001', 98);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`) VALUES (20, 'receivings', 'R000001', NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`) VALUES (30, 'adjustment', 'A000001', NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`) VALUES (11, 'sales void', 'V000001', NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`) VALUES (40, 'customer deposit', 'C000001', NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`) VALUES (50, 'loyalty card', '00000001', NULL);
INSERT INTO trans_types (`type_id`, `name`, `next_ref`, `sync_id`) VALUES (35, 'spoilage', 'S000001', NULL);


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

INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (1, 'pc', 'pieces', '0', NULL, 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (2, 'kg', 'kilograms', '0.0001', 'g', 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (3, 'g', 'gram', '1000', 'kg', 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (6, 'L', 'litres', '0.264172', 'gal', 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (7, 'gal', 'gallons', '3.78541', 'L', 0);
INSERT INTO uom (`id`, `code`, `name`, `num`, `to`, `inactive`) VALUES (8, 'BTL', 'Bot(s)', '0', '', 0);


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
INSERT INTO user_roles (`id`, `role`, `description`, `access`) VALUES (2, 'Manager', 'Manager', 'dashboard,menus,menulist,menucat,menusubcat,menusched,mods,modslist,modgrps,pos_promos,gift_cards,coupons,charges,grecdiscs,gtaxrates,tblmng,denomination,customers,reps,menu_sales_rep,act_receipts,act_logs,drawer_count,rep_history,setup,control,user');
INSERT INTO user_roles (`id`, `role`, `description`, `access`) VALUES (3, 'Employee', 'Employee', 'general_settings,grecdiscs');
INSERT INTO user_roles (`id`, `role`, `description`, `access`) VALUES (4, 'OIC', 'Officer In Charge', 'menus,menulist,menucat,menusubcat,menusched,mods,modslist,modgrps,pos_promos,gift_cards,coupons,charges,grecdiscs,gtaxrates,tblmng,denomination');


#
# TABLE STRUCTURE FOR: users
#

DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', '00001', 'Rey', 'Coloma', 'Tejada', 'Jr.', 1, 'rey.tejada01@gmail.com', 'male', '2014-06-16 14:41:31', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (35, 'jess', '5f4dcc3b5aa765d61d8327deb882cf99', '1234', 'Jess', '', 'Alison', '', 4, '', 'male', '2017-02-01 09:08:26', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (36, 'RICA ', '8f65678b767fa183b6c720ac9e519d9d', '170523', 'Rica michelle ', 'Sadie', 'Bernardo', '', 2, '', 'female', '2017-02-02 11:32:46', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (37, 'DALE', '762c212d7dd05d674c85427915ed3a77', '11261993', 'John dale', 'Casilao', 'Casilao', '', 3, '', 'male', '2017-02-02 11:33:58', 1);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (38, 'Rona', '5e4a1ee11ea2616f7108174077332b82', '050400', 'Ronalee', 'Alviar', 'Perol', '', 3, '', 'female', '2017-02-02 11:35:16', 1);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (39, 'PEONY', '1d52b73c02c47625ebde9f0222c82ede', '092288', 'Peony Marie', 'Caagao', 'Milano', '', 2, '', 'female', '2017-02-02 11:36:41', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (40, 'Paul', '378a96202c444ea2e334762e507c28dd', '030195', 'johnpaul', 'Roldan', 'Tagayo', '', 3, 'johnpaultagayon@yahoo.com', 'male', '2017-02-02 11:38:13', 1);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (41, 'Ropert', 'f41c774599a9de153527e6bd70453466', '31160', 'Ropert', 'Reyes', 'Mejos', '', 2, 'ropert09@gmail.com', 'male', '2017-02-02 11:39:17', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (42, 'mon', '07556022d44dd36bf61f1021b006b0ae', '112916', 'mon alexis', 'iglesia', 'lanoza', '', 3, '', 'male', '2017-02-02 11:40:41', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (43, 'RYS', 'c8758b517083196f05ac29810b924aca', '0721', 'Rys', 'A', 'Goleta', '', 2, '', 'female', '2017-02-02 11:42:05', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (44, 'micah', 'd175014555960d7a224a53dcfdad8517', '01301992', 'johna micah', 'abejuela', 'soriano', '', 3, 'johna_micah@yahoo.com', 'female', '2017-02-15 11:17:38', 1);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (45, 'Ruzel', 'c60d060b946d6dd6145dcbad5c4ccf6f', '1118', 'Ruzel', '', 'Camposano', '', 1, '', 'male', '2017-05-18 11:41:43', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (46, 'POS ADMIN', '21d510d02139a803005e15547d6b034e', '0817', 'Jess', 'R', 'Alison', '', 1, '', 'male', '2017-05-18 11:42:28', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (47, 'Abigael', 'e10adc3949ba59abbe56e057f20f883e', '123456', 'Abigael', 'tolentino', 'Cariño', '', 3, 'carinoabigael1996@gmail.com', 'female', '2017-07-02 11:25:33', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (48, 'Zedric', 'c01cae62a7e4766f188dde1499599cb7', '030798', 'zedric', 'dizer', 'quinto', '', 3, '', 'male', '2017-07-02 15:37:43', 1);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (49, 'ferrie', 'dce7981e1931e7a8affec37004687355', '2103', 'bryan ferrie', 'malabanan', 'vergara', '', 3, 'b.vergara32@yahoo.com', 'male', '2017-07-02 15:39:45', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (50, 'krischian', '15ff681aecdde6810a7ad487dd923f70', '083015', 'krischian jonar', 'cuyos', 'egamino', '', 3, '', 'male', '2017-07-02 17:40:07', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (51, 'Ian', 'e99977cfbef4f5b1ab727aa4339929bb', '000000', 'Ian Christopher', 'Agusin', 'Laforteza', '', 3, 'topherlaforteza@yahoo.com', 'male', '2017-07-04 18:16:38', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (52, 'JaycePablo', '8d10f19c2e75c036b59011869e5e3cac', 'Pablo051890', 'Jayce Bryan', 'Apostol', 'Pablo', '', 2, 'JayceBryanPablo@gmail.com', 'male', '2017-08-12 15:54:52', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (53, 'Ruvi', '2fbc15ace31829639182540e97ad0a21', '062595', 'Ruvi ', 'Gatoc', 'Castro', '', 3, '', 'female', '2017-08-22 17:56:14', 0);
INSERT INTO users (`id`, `username`, `password`, `pin`, `fname`, `mname`, `lname`, `suffix`, `role`, `email`, `gender`, `reg_date`, `inactive`) VALUES (54, 'Patricia', '8dc61c6114d55b7ace9a12ed1158e9be', '062112', 'Patricia Isabel', 'Perez', 'Maghirang', '', 3, '', 'female', '2017-10-02 13:17:31', 0);


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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

