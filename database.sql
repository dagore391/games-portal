CREATE TABLE `company` (
	`id` INT UNSIGNED NOT NULL,
	`name` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `company_uk-name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `language` (
	`tag` CHAR(5) NOT NULL,
	`name` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`tag`),
	UNIQUE KEY `language_uk-name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `language` (`tag`, `name`) VALUES ('es_ES', 'Español');
INSERT INTO `language` (`tag`, `name`) VALUES ('en_EN', 'English');

CREATE TABLE `platform` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`tag` CHAR(3) NOT NULL,
	`name` VARCHAR(50) NOT NULL,
	`release_date` DATE DEFAULT NULL,
	`colour` VARCHAR(20) DEFAULT NULL,
	`featured` BIT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY `platform_uk-tag` (`tag`),
	UNIQUE KEY `platform_uk-name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `platform` (`id`, `tag`, `name`, `release_date`, `colour`, `featured`) VALUES
(1, 'nsw', 'Nintendo Switch', '2017-03-03', 'red', 1),
(2, 'xbo', 'Xbox One', '2012-11-22', 'green2', 1),
(3, '360', 'Xbox 360', '2005-12-02', 'green', 0),
(4, 'ps5', 'PlayStation 5', '2020-11-19', 'blue', 1);

CREATE TABLE `game` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`gamegroup` BIGINT UNSIGNED,
	`platform` INT UNSIGNED NOT NULL,
	`name` VARCHAR(100) DEFAULT NULL,
	`resume` TEXT DEFAULT NULL,
	`release_day` TINYINT(2),
	`release_month` TINYINT(2),
	`release_year` SMALLINT(4),
	`release_price` VARCHAR(16) DEFAULT NULL,
	`sources` TEXT NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `game_uk-game-gamegroup-platform` (`gamegroup`, `platform`),
	FOREIGN KEY `game_fk-platform` (`platform`) REFERENCES `platform` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `game` (`id`, `gamegroup`, `platform`, `name`, `resume`, `release_year`, `release_month`, `release_day`, `release_price`) VALUES
(1, NULL, 1, 'The Legend of Zelda: Breath of the Wild', NULL, '2017', '03', '03', '69,99 €'),
(2, NULL, 1, 'Super Mario Odyssey', NULL, '2017', '10', '27', '59,99 €'),
(3, NULL, 1, 'FIFA 21: Legacy Edition', NULL, '2020', '10', '09', '49,99 €'),
(4, NULL, 1, 'Wonder Boy: Asha in Monster World', NULL, '2021', '05', '28', '34,99 €'),
(5, NULL, 1, 'Pokémon Escudo', NULL, '2019', '11', '15', '59,99 €'),
(6, NULL, 1, 'Pokémon Espada', NULL, '2019', '11', '15', '59,99 €'),
(7, NULL, 1, 'Balan Wonderworld', NULL, '2021', '03', '26', '59,99 €'),
(8, NULL, 1, 'Tony Hawk\'s Pro Skater 1 + 2', NULL, '2021', '06', '25', '44,99 €'),
(9, NULL, 1, 'Alex Kidd in Miracle World DX', NULL, '2021', '06', '22', '39,99 €'),
(10, NULL, 1, 'Super Mario 3D All-Stars', NULL, '2020', '09', '18', '59,99 €'),
(11, NULL, 1, 'Pokémon: Let\'s Go, Eevee!', NULL, '2018', '11', '16', '59,99 €'),
(12, NULL, 1, 'Pokémon: Let\'s Go, Pikachu!', NULL, '2018', '11', '16', '59,99 €'),
(13, NULL, 1, 'Immortals Fenyx Rising', NULL, '2020', '12', '03', '59,99 €'),
(14, NULL, 1, '1-2-Switch', NULL, '2017', '03', '03', '49,99 €'),
(15, NULL, 1, 'Fast RMX', NULL, '2017', '03', '03', '19,99 €'),
(16, NULL, 1, 'Mario Kart 8 Deluxe', NULL, '2017', '04', '28', '59,99 €'),
(17, NULL, 1, 'Super Bomberman R', NULL, '2017', '03', '03', '49,99 €'),
(18, NULL, 1, 'I am Setsuna', NULL, '2017', '03', '03', '39,99 €'),
(19, NULL, 2, 'Halo Infinite', NULL, '2021', '12', '08', '69,99 €'),
(20, NULL, 2, 'Dungeons & Dragons: Dark Alliance', NULL, '2021', '06', '22', '59,99 €'),
(21, NULL, 2, 'Dragon Ball Z: Kakarot', NULL, '2020', '01', '17', '69,99 €'),
(22, NULL, 2, 'Red Dead Redemption II', NULL, '2018', '10', '26', '69,99 €'),
(23, NULL, 3, 'Kameo: Elements of Power', NULL, '2005', '12', '02', '59,99 €'),
(24, NULL, 3, 'Ace Combat 6: Fires of Liberation', NULL, '2007', '11', '23', '69,99 €'),
(25, NULL, 3, 'Alan Wake', NULL, '2010', '05', '14', '64,99 €'),
(26, NULL, 4, 'Ratchet & Clank: Una dimensión aparte', NULL, '2021', '06', '11', '79,99 €');

CREATE TABLE `game_company` (
	`game` BIGINT UNSIGNED NOT NULL,
	`company` INT UNSIGNED NOT NULL,
	`category` ENUM('PUBLISHER','DEVELOPER') NOT NULL,
	PRIMARY KEY (`game`,`company`,`category`),
	FOREIGN KEY `game_company_fk-company` (`company`) REFERENCES `company` (`id`),
	FOREIGN KEY `game_company_fk-game` (`game`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `metagroup` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL,
	`tag` VARCHAR(100) NOT NULL,
	`relevance` TINYINT(2) UNSIGNED NOT NULL,
	`infotype` ENUM('TEXT', 'PNG') NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `metagroup_uk-name` (`name`),
	UNIQUE KEY `metagroup_uk-tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `metagroup` (`id`, `name`, `tag`, `relevance`, `infotype`) VALUES
(1, 'Géneros', 'genres', 100, 'TEXT'),
(2, 'Número de jugadores', 'players', 25, 'TEXT'),
(3, 'Calificación de contenido', 'content-rating', 25, 'PNG'),
(4, 'Idioma de los textos', 'text-lang', 25, 'TEXT'),
(5, 'Idioma de las voces', 'sound-lang', 25, 'TEXT'),
(6, 'Temáticas', 'themes', 50, 'TEXT');

CREATE TABLE `metadata` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`value` VARCHAR(200) NOT NULL,
	`group` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `metadata_uk-value-group` (`value`,`group`),
	FOREIGN KEY `metadata_fk-group` (`group`) REFERENCES `metagroup` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `game_metadata` (
	`game` BIGINT UNSIGNED NOT NULL,
	`metadata` INT(20) UNSIGNED NOT NULL,
	PRIMARY KEY (`game`,`metadata`),
	FOREIGN KEY `game_metadata_fk-metadata` (`metadata`) REFERENCES `metadata` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `member` (
	`id` BIGINT UNSIGNED NOT NULL,
	`username` VARCHAR(20) NOT NULL,
	`password` VARCHAR(128) NOT NULL,
	`email` VARCHAR(100) NOT NULL,
	`salt` VARCHAR(16) NOT NULL,
	`language` CHAR(5) NOT NULL default 'es_ES',
	`myprofile_visivility` ENUM('ALL', 'FRIENDS', 'NONE') DEFAULT 'ALL' NOT NULL,
	`myprofile_publish` ENUM('ALL', 'FRIENDS', 'NONE') DEFAULT 'ALL' NOT NULL,
	`sendme_messages` ENUM('ALL', 'FRIENDS', 'NONE') DEFAULT 'ALL' NOT NULL,
	`sendme_requests` BIT(1) DEFAULT 1 NOT NULL,
	`inbox_max` TINYINT UNSIGNED NOT NULL DEFAULT 25,
	`comments_number` INT UNSIGNED NOT NULL DEFAULT 0,
	`posts_number` INT UNSIGNED NOT NULL DEFAULT 0,
	`account_state` ENUM('ACTIVED', 'BANNED', 'DEACTIVATED', 'LOCKED') DEFAULT 'DEACTIVATED' NOT NULL,
	`account_group` ENUM('USER', 'WRITER', 'MODERATOR', 'MANAGER', 'ADMIN') DEFAULT 'USER' NOT NULL,
	`account_creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`last_login` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`activation_code` VARCHAR(8),
	PRIMARY KEY (`id`),
	UNIQUE KEY `member_uk-username` (`username`),
	UNIQUE KEY `member_uk-email` (`email`),
	FOREIGN KEY `member_fk-language` (`language`) REFERENCES `language` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `member` (`id`, `username`, `password`, `email`, `salt`, `account_state`, `account_group`) VALUES
(1, 'admin', '02651a36781acf3c9c584b7ff5879643de77291d11ab5f868f8f7eef27836f10', 'admin@localhost', 'M61Qclk5B819dZ10', 'ACTIVED', 'ADMIN');

CREATE TABLE `login_history` (
	`login_ip` VARBINARY(8) NOT NULL,
	`login_member` VARCHAR(32) NOT NULL,
	`login_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`login_result` BIT(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `entry` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`category` ENUM('articles', 'faqs', 'news', 'previews', 'reviews', 'rumours') NOT NULL,
	`title` VARCHAR(200) NOT NULL,
	`resume` VARCHAR(500) NOT NULL DEFAULT '',
	`content` TEXT NOT NULL DEFAULT '',
	`published` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`author` BIGINT UNSIGNED NOT NULL,
	`game` BIGINT UNSIGNED,
	`platform` INT UNSIGNED,
	`score` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	FOREIGN KEY `entry_fk-author` (`author`) REFERENCES `member` (`id`),
	FOREIGN KEY `entry_fk-game` (`game`) REFERENCES `game` (`id`),
	FOREIGN KEY `entry_fk-platform` (`platform`) REFERENCES `platform` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `entry_comment` (
	`id` BIGINT UNSIGNED NOT NULL,
	`content` TEXT DEFAULT NULL,
	`published` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`entry` BIGINT UNSIGNED NOT NULL,
	`author` BIGINT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY `entry_comment_fk-entry` (`entry`) REFERENCES `entry` (`id`),
	FOREIGN KEY `entry_comment_fk-author` (`author`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `member_game` (
	`member` BIGINT UNSIGNED NOT NULL,
	`game` BIGINT UNSIGNED NOT NULL,
	`category` ENUM('COLLECTION', 'FAVORITE', 'WISHLIST') NOT NULL,
	PRIMARY KEY (`member`, `game`, `category`),
	FOREIGN KEY `member_game_fk-member` (`member`) REFERENCES `member` (`id`),
	FOREIGN KEY `member_game_fk-game` (`game`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `member_message` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`member_from` BIGINT UNSIGNED NOT NULL,
	`member_to` BIGINT UNSIGNED NOT NULL,
	`subject` VARCHAR(100) NOT NULL,
	`message` TEXT NOT NULL DEFAULT '',
	`send_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`is_deleted` BIT(1) NOT NULL DEFAULT 0,
	`is_read` BIT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	FOREIGN KEY `member_message_fk-member_from` (`member_from`) REFERENCES `member` (`id`),
	FOREIGN KEY `member_message_fk-member_to` (`member_to`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `member_relation` (
	`member1` BIGINT UNSIGNED NOT NULL,
	`member2` BIGINT UNSIGNED NOT NULL,
	`state` ENUM('FRIENDSHIP', 'LOCKED', 'REQUEST') NOT NULL DEFAULT 'REQUEST',
	`relation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`member1`, `member2`),
	FOREIGN KEY `member_relation_fk-member1` (`member1`) REFERENCES `member` (`id`),
	FOREIGN KEY `member_relation_fk-member2` (`member2`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `member_report` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`message_id` BIGINT UNSIGNED NOT NULL,
	`message_content` TEXT NOT NULL,
	`manager_member` BIGINT UNSIGNED,
	`informer_member` BIGINT UNSIGNED NOT NULL,
	`reported_member` BIGINT UNSIGNED NOT NULL,
	`state` ENUM('APPROVED', 'PENDING', 'REJECTED') NOT NULL DEFAULT 'PENDING',
	`type` ENUM('COMMENT', 'POST') NOT NULL,
	`sending_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `member_report_uk-message_id-informer_member-reported_member-type` (`message_id`, `informer_member`, `reported_member`, `type`),
	FOREIGN KEY `member_report_fk-manager_member` (`manager_member`) REFERENCES `member` (`id`),
	FOREIGN KEY `member_report_fk-informer_member` (`informer_member`) REFERENCES `member` (`id`),
	FOREIGN KEY `member_report_fk-reported_member` (`reported_member`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `game_score` (
	`member` BIGINT UNSIGNED NOT NULL,
	`game` BIGINT UNSIGNED NOT NULL,
	`score` TINYINT(1) UNSIGNED NOT NULL,
	PRIMARY KEY (`member`, `game`),
	FOREIGN KEY `game_score_fk-member` (`member`) REFERENCES `member` (`id`),
	FOREIGN KEY `game_score_fk-game` (`game`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `achievement` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(100) NOT NULL,
	`description` VARCHAR(500) NOT NULL,
	`value` VARCHAR(32) NOT NULL,
	`published` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`author` BIGINT UNSIGNED NOT NULL,
	`game` BIGINT UNSIGNED,
	PRIMARY KEY (`id`),
	FOREIGN KEY `achievement_fk-author` (`author`) REFERENCES `member` (`id`),
	FOREIGN KEY `achievement_fk-game` (`game`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cheat` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(200) NOT NULL,
	`description` TEXT NOT NULL DEFAULT '',
	`published` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`author` BIGINT UNSIGNED NOT NULL,
	`game` BIGINT UNSIGNED,
	PRIMARY KEY (`id`),
	FOREIGN KEY `cheat_fk-author` (`author`) REFERENCES `member` (`id`),
	FOREIGN KEY `cheat_fk-game` (`game`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `forum_category` (
	`id` INT UNSIGNED NOT NULL,
	`title` VARCHAR(100) NOT NULL,
	`description` VARCHAR(500) NOT NULL,
	`position` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `forum_category_uk-title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `forum_category` (`id`, `title`, `description`, `position`) VALUES
(1, 'Categoría 1', 'Categoría de prueba.', 1);

CREATE TABLE `forum` (
	`id` INT UNSIGNED NOT NULL,
	`title` VARCHAR(100) NOT NULL,
	`description` VARCHAR(500) NOT NULL,
	`category` INT UNSIGNED NOT NULL,
	`parent_forum` INT UNSIGNED,
	`who_can_read` ENUM('GUEST', 'REGISTER', 'USER', 'WRITER', 'MODERATOR', 'MANAGER', 'ADMIN') NOT NULL DEFAULT 'REGISTER',
	`who_can_write` ENUM('GUEST', 'REGISTER', 'USER', 'WRITER', 'MODERATOR', 'MANAGER', 'ADMIN') NOT NULL DEFAULT 'REGISTER',
	`position` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `forum_uk-title-category` (`title`, `category`),
	FOREIGN KEY `forum_fk-category` (`category`) REFERENCES `forum_category` (`id`),
	FOREIGN KEY `forum_fk-parent_forum` (`parent_forum`) REFERENCES `forum` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `forum` (`id`, `title`, `description`, `category`, `parent_forum`, `who_can_read`, `who_can_write`, `position`) VALUES
(1, 'Foro 1', 'Foro de prueba.', 1, null, 'GUEST', 'REGISTER', 1);

CREATE TABLE `forum_post` (
	`id` BIGINT UNSIGNED NOT NULL,
	`title` VARCHAR(200) NOT NULL,
	`content` TEXT NOT NULL DEFAULT '',
	`published` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`author` BIGINT UNSIGNED NOT NULL,
	`forum` INT UNSIGNED NOT NULL,
	`topic` BIGINT UNSIGNED,
	`is_closed` BIT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	FOREIGN KEY `forum_post_fk-author` (`author`) REFERENCES `member` (`id`),
	FOREIGN KEY `forum_post_fk-forum` (`forum`) REFERENCES `forum` (`id`),
	FOREIGN KEY `forum_post_fk-topic` (`topic`) REFERENCES `forum_post` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `forum_post` (`id`, `title`, `content`, `author`, `forum`, `topic`) VALUES
(1, 'Presentación', 'Hola, soy el administrador del sitio.', 1, 1, null);

CREATE TABLE `security_token` (
	`client_ip` VARBINARY(16) NOT NULL,
	`client_browser_hash` VARCHAR(128) NOT NULL,
	`content_hash` VARCHAR(128) NOT NULL,
	`expiration_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`request_page` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`client_ip`, `request_page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `visitor` (
	`id` BIGINT UNSIGNED NOT NULL,
	`client_ip` VARBINARY(16) NOT NULL,
	`client_browser_hash` VARCHAR(128) NOT NULL,
	`visit_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`request_page` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `country` (
	`id` INT UNSIGNED NOT NULL,
	`iso_code` CHAR(3) NOT NULL,
	`name` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `country_uk-iso_code` (`iso_code`),
	UNIQUE KEY `country_uk-name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `media_type` (
	`id` INT UNSIGNED NOT NULL,
	`name` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `media_type_uk-name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `media` (
	`id` INT UNSIGNED NOT NULL,
	`name` VARCHAR(100) NOT NULL,
	`type` INT UNSIGNED NOT NULL,
	`country` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `media_type_uk-name` (`name`),
	FOREIGN KEY `media_type_fk-type` (`type`) REFERENCES `media_type` (`id`),
	FOREIGN KEY `media_type_fk-country` (`country`) REFERENCES `country` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `media_score` (
	`media` INT UNSIGNED NOT NULL,
	`game` BIGINT UNSIGNED NOT NULL,
	`score` TINYINT(1) UNSIGNED NOT NULL,
	`description` VARCHAR(500) NOT NULL,
	PRIMARY KEY (`media`, `game`, `description`),
	FOREIGN KEY `media_score_fk-media` (`media`) REFERENCES `media` (`id`),
	FOREIGN KEY `media_score_fk-game` (`game`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `page` (
	`path` VARCHAR(200) NOT NULL,
	`title` VARCHAR(200) NOT NULL,
	`content` TEXT NOT NULL DEFAULT '',
	`published` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`author` BIGINT UNSIGNED NOT NULL,
	PRIMARY KEY (`path`),
	FOREIGN KEY `page_fk-author` (`author`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `page` (`path`, `title`, `content`, `author`) VALUES
('cookies', 'Cookies', '<b>Página en construcción...</b>', 1),
('policies', 'Políticas de privacidad', '<b>Página en construcción...</b>', 1),
('rules', 'Normas del sitio', '<b>Página en construcción...</b>', 1);
