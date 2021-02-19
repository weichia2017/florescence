CREATE TABLE `florescence`.`google_reviews` (
  `review_id` varchar(255) NOT NULL PRIMARY KEY,
  `store_id` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `n_review_user` int(11) DEFAULT NULL,
  `retrieval_date` date DEFAULT NULL,
  `n_photo_user` int(11) DEFAULT NULL,
  `url_user` varchar(255) DEFAULT NULL,
  `relative_date` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `florescence`.`tripadvisor_reviews` (
  `review_id` varchar(255) NOT NULL PRIMARY KEY,
  `store_id` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `n_review_user` int(11) DEFAULT NULL,
  `retrieval_date` date DEFAULT NULL,
  `review_title` text DEFAULT NULL,
  `value_rating` int(11) DEFAULT NULL,
  `atmosphere_rating` int(11) DEFAULT NULL,
  `service_rating` int(11) DEFAULT NULL,
  `food_rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `florescence`.`stores` ( 
  `id_store` INT NOT NULL AUTO_INCREMENT , 
  `store_name` VARCHAR(255) NOT NULL , 
  `googlereviews_url` VARCHAR(255) NOT NULL , 
  `tripadvisors_url` VARCHAR(255) NOT NULL , 
  PRIMARY KEY (`id_store`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `florescence`.`sentiment_scores` ( 
  `review_id` varchar(255) NOT NULL PRIMARY KEY,
  `negative` DECIMAL DEFAULT NULL,
  `neutral` DECIMAL DEFAULT NULL, 
  `positive` DECIMAL DEFAULT NULL, 
  `compound` DECIMAL DEFAULT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Base Stores
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Praelum Wine Bistro', 'https://www.google.com/maps/place/Praelum+Wine+Bistro/@1.2793238,103.8430118,15z/data=!4m2!3m1!1s0x0:0x49dd8f5ea10e0dc8?sa=X&ved=2ahUKEwiAzI6l9ertAhVUU30KHaMpBuAQ_BIwCnoECBYQBQ', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d5264234-Reviews-Praelum_Wine_Bistro-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('BTM Mussels & Bar', 'https://www.google.com/maps/place/BTM+Mussels+%26+Bar/@1.2793024,103.8407799,17z/data=!3m1!4b1!4m5!3m4!1s0x31da198e0899b8f1:0xae833c61844c8f6f!8m2!3d1.279297!4d103.8429686', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d20733849-Reviews-BTM_Mussels_Bar-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Prawn Noodle Bar', 'https://www.google.com/maps/place/Prawn+Noodle+Bar/@1.2793327,103.8428106,15z/data=!4m2!3m1!1s0x0:0x8767f6d85d38145?sa=X&ved=2ahUKEwjXr5L6oevtAhUKOSsKHer7CIoQ_BIwCnoECBcQBQ', '');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('SG Taps', 'https://www.google.com/maps/place/SG+Taps/@1.2791423,103.8408758,17z/data=!3m1!4b1!4m5!3m4!1s0x31da196d6ff4666f:0xeda4d802176bc7f0!8m2!3d1.2791378!4d103.8427163', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d15049965-Reviews-SG_Taps-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('BarCelona', 'https://www.google.com/maps/place/BAR-CELONA+(sg)/@1.279153,103.8408758,17z/data=!4m8!1m2!2m1!1sBarCelona!3m4!1s0x31da196d6d50166b:0x451cd03ad9233b0d!8m2!3d1.2784898!4d103.8424823', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d12235604-Reviews-Bar_Celona-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('FLOR Patisserie', 'https://www.google.com/maps/place/Flor+P%C3%A2tisserie/@1.2791798,103.8343098,15z/data=!4m8!1m2!2m1!1sFLOR+Patisserie!3m4!1s0x0:0xe67d5d41695067c0!8m2!3d1.2793583!4d103.8430712', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d5424910-Reviews-Flor_Patisserie-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Rhubarb', 'https://www.google.com/maps/place/Rhubarb/@1.279378,103.8408649,17z/data=!3m1!4b1!4m5!3m4!1s0x31da196d66892a49:0xa556409c36f74a38!8m2!3d1.2793726!4d103.8430536', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d7060741-Reviews-Rhubarb-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Xiao Ya Tou', 'https://www.google.com/maps/place/Xiao+Ya+Tou/@1.279378,103.8408649,17z/data=!4m5!3m4!1s0x31da196d65c00b71:0x7f750b208fa73684!8m2!3d1.279257!4d103.8428968', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d11802385-Reviews-Xiao_Ya_Tou-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Aperitivo', 'https://www.google.com/maps/place/Aperitivo/@1.2780911,103.8403833,17z/data=!4m8!1m2!2m1!1sAperitivo!3m4!1s0x31da191802567d53:0x58e33eb05b99a129!8m2!3d1.2792575!4d103.8428113', '');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Fotia@Duxton', 'https://www.google.com/maps/place/Fotia/@1.2780804,103.8403833,17z/data=!3m1!4b1!4m5!3m4!1s0x31da190cf12ed1f7:0xb03ef9330f935f18!8m2!3d1.278075!4d103.842572', '');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Alba 1836 Wine Bar & Restaurant', '', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d7254819-Reviews-Alba_1836_Wine_Bar_Restaurant-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('L\'Entrecôte The Steak & Fries Bistro', 'https://www.google.com/maps/place/L\'Entrec%C3%B4te+The+Steak+%26+Fries+Bistro/@1.2784569,103.8422722,19z/data=!3m1!4b1!4m5!3m4!1s0x31da196d12fa5169:0x8920c824893e5c79!8m2!3d1.2784556!4d103.8428194', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d3377772-Reviews-L_Entrecote_The_Steak_Fries_Bistro-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Kitchen Kumars', 'https://www.google.com/maps/place/Kitchen+Kumars/@1.2785261,103.8423165,19z/data=!3m1!4b1!4m5!3m4!1s0x31da1932922e76ed:0xcd9ffad00ac6783a!8m2!3d1.2785248!4d103.8428637', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d17676105-Reviews-Kitchen_Kumars-Singapore.html');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Parallel coffee roasters', 'https://www.google.com/maps/place/Parallel+Coffee+Roasters+(Duxton+Hill)/@1.278592,103.842847,15z/data=!4m2!3m1!1s0x0:0x92b985bf24286375?sa=X&ved=2ahUKEwjbstfk9urtAhUJcCsKHRydCsEQ_BIwCnoECBsQBQ', '');
INSERT INTO `stores` (`store_name`, `googlereviews_url`, `tripadvisors_url`) VALUES ('Latteria mozzarella bar', 'https://www.google.com/maps/place/Latteria+Mozzarella+Bar/@1.2786122,103.8407543,17z/data=!3m1!4b1!4m5!3m4!1s0x31da196d6cb6855f:0xf193e727fc263c6d!8m2!3d1.2786068!4d103.842943', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d3136129-Reviews-Latteria_Mozzarella_Bar-Singapore.html');
