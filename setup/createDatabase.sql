
CREATE TABLE `florescence`.`stores` ( 
  `id_store` INT NOT NULL AUTO_INCREMENT , 
  `store_name` VARCHAR(255) NOT NULL , 
  `googlereviews_url` VARCHAR(255) NOT NULL , 
  `tripadvisors_url` VARCHAR(255) NOT NULL , 
  PRIMARY KEY (`id_store`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `florescence`.`sources` (
  `source_id` int(11) NOT NULL,
  `source_name` text DEFAULT NULL,
  PRIMARY KEY (source_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `sources` (`source_id`, `source_name`) VALUES (1, 'Google Reviews');
INSERT INTO `sources` (`source_id`, `source_name`) VALUES (2, 'TripAdvisor');

CREATE TABLE `florescence`.`raw_reviews` (
  `review_id` varchar(255) NOT NULL,
  `source_id` int(11) NOT NULL,
  `store_id` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `retrieval_date` date DEFAULT NULL,
  PRIMARY KEY (review_id, source_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `florescence`.`sentiment_scores` ( 
  `review_id` varchar(255) NOT NULL,
  `source_id` int(11) NOT NULL,
  `negative` float(6,4) DEFAULT NULL,
  `neutral` float(6,4) DEFAULT NULL, 
  `positive` float(6,4) DEFAULT NULL, 
  `compound` float(6,4) DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  PRIMARY KEY (review_id, source_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `florescence`.`adj_noun_pairs` ( 
  `pair_id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` varchar(255) NOT NULL,
  `source_id` int(11) NOT NULL,
  `noun` varchar(255) NOT NULL,
  `adj` varchar(255) NOT NULL,
  PRIMARY KEY (review_id, source_id, pair_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `florescence`.`adj_noun_pairs` ( 
  `pair_id` INT(11) NOT NULL AUTO_INCREMENT, 
  `review_id` VARCHAR(255) NOT NULL, 
  `source_id` INT(11) NOT NULL , 
  `noun` VARCHAR(255) NOT NULL , 
  `adj` VARCHAR(255) NOT NULL , 
  PRIMARY KEY (pair_id, review_id, source_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

