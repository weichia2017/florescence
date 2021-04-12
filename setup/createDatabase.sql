CREATE TABLE `florescence`.`sources` (
  `source_id` int(11) NOT NULL,
  `source_name` text DEFAULT NULL,
  PRIMARY KEY (source_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `sources` (`source_id`, `source_name`) VALUES (1, 'Google Reviews');
INSERT INTO `sources` (`source_id`, `source_name`) VALUES (2, 'TripAdvisor');

CREATE TABLE `florescence`.`roads` (
  `road_id` INT NOT NULL,
  `road_name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`road_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roads` (`road_id`, `road_name`) VALUES (1, 'Duxton Hill');
INSERT INTO `roads` (`road_id`, `road_name`) VALUES (2, 'Duxton Road');

CREATE TABLE `florescence`.`stores` (
  `store_id` INT NOT NULL AUTO_INCREMENT ,
  `store_name` VARCHAR(255) NOT NULL ,
  `googlereviews_url` VARCHAR(255) NOT NULL ,
  `tripadvisors_url` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id_store`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

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
  `processed_date` date DEFAULT NULL,
  PRIMARY KEY (review_id, source_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `florescence`.`adj_noun_pairs` (
  `pair_id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` varchar(255) NOT NULL,
  `source_id` int(11) NOT NULL,
  `noun` varchar(255) NOT NULL,
  `adj` varchar(255) NOT NULL,
  `processed_date` date DEFAULT NULL,
  PRIMARY KEY (review_id, source_id, pair_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (1,'Praelum Wine Bistro', 'https://www.google.com/maps/place/Praelum+Wine+Bistro/@1.2793238,103.8430118,15z/data=!4m2!3m1!1s0x0:0x49dd8f5ea10e0dc8?sa=X&ved=2ahUKEwiAzI6l9ertAhVUU30KHaMpBuAQ_BIwCnoECBYQBQ', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d5264234-Reviews-Praelum_Wine_Bistro-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (2,'BTM Mussels & Bar', 'https://www.google.com/maps/place/BTM+Mussels+%26+Bar/@1.2793024,103.8407799,17z/data=!3m1!4b1!4m5!3m4!1s0x31da198e0899b8f1:0xae833c61844c8f6f!8m2!3d1.279297!4d103.8429686', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d20733849-Reviews-BTM_Mussels_Bar-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (3,'Prawn Noodle Bar', 'https://www.google.com/maps/place/Prawn+Noodle+Bar/@1.2793327,103.8428106,15z/data=!4m2!3m1!1s0x0:0x8767f6d85d38145?sa=X&ved=2ahUKEwjXr5L6oevtAhUKOSsKHer7CIoQ_BIwCnoECBcQBQ', '', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (4,'SG Taps', 'https://www.google.com/maps/place/SG+Taps/@1.2791423,103.8408758,17z/data=!3m1!4b1!4m5!3m4!1s0x31da196d6ff4666f:0xeda4d802176bc7f0!8m2!3d1.2791378!4d103.8427163', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d15049965-Reviews-SG_Taps-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (5,'BarCelona', 'https://www.google.com/maps/place/BAR-CELONA+(sg)/@1.279153,103.8408758,17z/data=!4m8!1m2!2m1!1sBarCelona!3m4!1s0x31da196d6d50166b:0x451cd03ad9233b0d!8m2!3d1.2784898!4d103.8424823', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d12235604-Reviews-Bar_Celona-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (6,'FLOR Patisserie', 'https://www.google.com/maps/place/Flor+P%C3%A2tisserie/@1.2791798,103.8343098,15z/data=!4m8!1m2!2m1!1sFLOR+Patisserie!3m4!1s0x0:0xe67d5d41695067c0!8m2!3d1.2793583!4d103.8430712', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d5424910-Reviews-Flor_Patisserie-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (7,'Rhubarb', 'https://www.google.com/maps/place/Rhubarb/@1.279378,103.8408649,17z/data=!3m1!4b1!4m5!3m4!1s0x31da196d66892a49:0xa556409c36f74a38!8m2!3d1.2793726!4d103.8430536', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d7060741-Reviews-Rhubarb-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (8,'Xiao Ya Tou', 'https://www.google.com/maps/place/Xiao+Ya+Tou/@1.279378,103.8408649,17z/data=!4m5!3m4!1s0x31da196d65c00b71:0x7f750b208fa73684!8m2!3d1.279257!4d103.8428968', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d11802385-Reviews-Xiao_Ya_Tou-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (9,'Aperitivo', 'https://www.google.com/maps/place/Aperitivo/@1.2780911,103.8403833,17z/data=!4m8!1m2!2m1!1sAperitivo!3m4!1s0x31da191802567d53:0x58e33eb05b99a129!8m2!3d1.2792575!4d103.8428113', '', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (10,'Fotia@Duxton', 'https://www.google.com/maps/place/Fotia/@1.2780804,103.8403833,17z/data=!3m1!4b1!4m5!3m4!1s0x31da190cf12ed1f7:0xb03ef9330f935f18!8m2!3d1.278075!4d103.842572', '', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (11,'Alba 1836 Wine Bar & Restaurant', '', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d7254819-Reviews-Alba_1836_Wine_Bar_Restaurant-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (12,"L\'Entrecôte The Steak & Fries Bistro", "https://www.google.com/maps/place/L\'Entrec%C3%B4te+The+Steak+%26+Fries+Bistro/@1.2784569,103.8422722,19z/data=!3m1!4b1!4m5!3m4!1s0x31da196d12fa5169:0x8920c824893e5c79!8m2!3d1.2784556!4d103.8428194', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d3377772-Reviews-L_Entrecote_The_Steak_Fries_Bistro-Singapore.html", 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (13,'Kitchen Kumars', 'https://www.google.com/maps/place/Kitchen+Kumars/@1.2785261,103.8423165,19z/data=!3m1!4b1!4m5!3m4!1s0x31da1932922e76ed:0xcd9ffad00ac6783a!8m2!3d1.2785248!4d103.8428637', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d17676105-Reviews-Kitchen_Kumars-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (14,'Parallel coffee roasters', 'https://www.google.com/maps/place/Parallel+Coffee+Roasters+(Duxton+Hill)/@1.278592,103.842847,15z/data=!4m2!3m1!1s0x0:0x92b985bf24286375?sa=X&ved=2ahUKEwjbstfk9urtAhUJcCsKHRydCsEQ_BIwCnoECBsQBQ', '', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (15,'Latteria mozzarella bar', 'https://www.google.com/maps/place/Latteria+Mozzarella+Bar/@1.2786122,103.8407543,17z/data=!3m1!4b1!4m5!3m4!1s0x31da196d6cb6855f:0xf193e727fc263c6d!8m2!3d1.2786068!4d103.842943', 'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d3136129-Reviews-Latteria_Mozzarella_Bar-Singapore.html', 1);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (16,"Lombardo\'s","https://www.google.com.sg/maps/place/Lombardo's+Burgers+Singapore/@1.2797794,103.8412183,17z/data=!3m1!4b1!4m5!3m4!1s0x31da19d6bfa27e17:0x6ab2ce2590b63600!8m2!3d1.2797794!4d103.8434123",'https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d18917196-Reviews-Lombardo_s_Burger_Singapore-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (17,"Sharky\'s","https://www.google.com.sg/maps/place/Sharky's+@+Duxton/@1.2958732,103.8594123,14z/data=!4m8!1m2!2m1!1sSharky's!3m4!1s0x31da194a1c41f363:0xbf90d813f70c8f2c!8m2!3d1.279666!4d103.8433822",'',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (18,"JEFFO\'S","https://www.google.com.sg/maps/place/JEFFO'S/@1.2796655,103.8412607,17z/data=!4m5!3m4!1s0x31da196d5e4ccc09:0xdc03767d978e0766!8m2!3d1.2796601!4d103.8434547","https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d21676687-Reviews-Jeffo_s-Singapore.html",2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (19,'Mitsu','https://www.google.com.sg/maps/place/Mitsu+Sushi+Bar/@1.2794823,103.8412016,17z/data=!3m1!4b1!4m5!3m4!1s0x31da196d60ad477d:0xac8d080816191775!8m2!3d1.2794823!4d103.8433956','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d13897811-Reviews-Mitsu_sushi_bar-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (20,'Raku Raku@Duxton','https://www.google.com.sg/maps/place/居酒屋+楽楽+@+Duxton/@1.2791832,103.8411645,17z/data=!3m1!4b1!4m5!3m4!1s0x31da196d678b2d67:0x62195c3f0c7a9c63!8m2!3d1.2791832!4d103.8433585','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d5425471-Reviews-Raku_Raku_Japanese_Dining_Duxton-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (21,'Pince & Pints','https://www.google.com.sg/maps/place/Pince+%26+Pints/@1.2791078,103.8428616,18z/data=!3m1!4b1!4m5!3m4!1s0x31da196d42a02d39:0x537a70a669d99384!8m2!3d1.2791078!4d103.8435139','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d7125626-Reviews-Pince_and_Pints_Duxton-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (22,'Bottoms Pub & Cafe',"https://www.google.com.sg/maps/place/Bottom's+Pub/@1.2785539,103.8411947,17z/data=!4m8!1m2!2m1!1sBottoms+Pub+%26+Cafe!3m4!1s0x31da196d3fe64cb7:0x4640e9bda84e7671!8m2!3d1.2784772!4d103.8433852",'',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (23,'Katsumata','https://www.google.com.sg/maps/place/NIKU+KATSUMATA/@1.2784302,103.8411681,17z/data=!3m1!4b1!4m5!3m4!1s0x31da196d157e2467:0xd93745a72d9c5e44!8m2!3d1.2784302!4d103.8433621','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d11718315-Reviews-Niku_Katsumata-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (24,'Etna Italian Restaurant','https://www.google.com.sg/maps/place/Etna+Italian+Restaurant+(Duxton)/@1.2957715,103.868791,14z/data=!4m8!1m2!2m1!1sEtna+Italian+Restaurant!3m4!1s0x0:0x29b988298918f79a!8m2!3d1.2783206!4d103.8433367','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d2253857-Reviews-Etna_Italian_Restaurant-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (25,'RAPPU Handrow Bar','https://www.google.com.sg/maps/place/RAPPU+Handroll+Bar/@1.2782247,103.841164,17z/data=!3m1!4b1!4m5!3m4!1s0x31da19243447fb49:0x940be84d8fc0e7f5!8m2!3d1.2782247!4d103.843358','',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (26,'Monte Russia restaurant','https://www.google.com.sg/maps/place/Monte+Risaia/@-0.1188992,106.1677734,7.12z/data=!4m8!1m2!2m1!1sMonte+Russia+restaurant!3m4!1s0x0:0x5ce35135c3927bef!8m2!3d1.2777414!4d103.8434601','',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (27,'Kreams','https://www.google.com.sg/maps/search/Kreams/@1.2777274,103.844169,19z','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d10520898-Reviews-Kream_Beer-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (28,'Wa Don-Don','https://www.google.com.sg/maps/place/大阪焼肉+Wa+Don-Don+Singapore+2nd/@1.2779226,103.8425996,19z/data=!3m1!4b1!4m5!3m4!1s0x31da19ecec89de0f:0x1b0593b1687be41d!8m2!3d1.2779226!4d103.8431481','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d17627436-Reviews-Wa_Don_Don_Singapore-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (29,'Fung Kee','https://www.google.com.sg/maps/place/FUNG+KEE+HOTDOGS/@1.2780188,103.8424079,19z/data=!3m1!4b1!4m5!3m4!1s0x31da197da27d0c71:0x7a4f03cd35d451d2!8m2!3d1.2780188!4d103.8429564','',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (30,'Kagura Sake','https://www.google.com.sg/maps/place/神楽酒+Kagura+Sake+Japanese+Dining+%26+Bar/@1.2780163,103.8424979,19z/data=!3m1!4b1!4m5!3m4!1s0x31da196d1757a07d:0x6c33bf3d06c92565!8m2!3d1.2780163!4d103.8430464','',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (31,'Candour Coffee','https://www.google.com.sg/maps/place/Candour+Coffee/@1.2781459,103.8425025,19z/data=!3m1!4b1!4m5!3m4!1s0x31da19a578bb11cd:0x718477f988f4a014!8m2!3d1.2781459!4d103.843051','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d10374342-Reviews-Candour_Coffee-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (32,'Rakki Bowl','https://www.google.com.sg/maps/place/Rakki+Bowl/@1.2781778,103.8424692,19z/data=!3m1!4b1!4m5!3m4!1s0x31da18d9b3f3745f:0x352cc9d6ba30a2a1!8m2!3d1.2781778!4d103.8430177','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d15432821-Reviews-Rakki_Bowl-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (33,'Going Places','https://www.google.com.sg/maps/place/Going+Places+Karaoke+Pub/@1.3019393,103.8679567,14z/data=!4m8!1m2!2m1!1sGoing+Places!3m4!1s0x0:0xb50f242def8b250b!8m2!3d1.2782187!4d103.8430685','',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (34,'Miyu','https://www.google.com.sg/maps/place/Miyu/@1.2782491,103.840926,17z/data=!3m1!4b1!4m5!3m4!1s0x31da19672bd47b59:0xc6602d89ebcca962!8m2!3d1.2782491!4d103.84312','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d21029348-Reviews-Miyu-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (35,'Monument Lifestyle','https://www.google.com.sg/maps/place/Monument+Lifestyle/@1.2785539,103.8411947,17z/data=!4m8!1m2!2m1!1sBottoms+Pub+%26+Cafe!3m4!1s0x0:0x6edff17ec4173f64!8m2!3d1.2782991!4d103.8430826','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d15290047-Reviews-Monument_Lifestyle_Cafe-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (36,'Restaurant JAG','https://www.google.com.sg/maps/place/Restaurant+JAG/@1.2782996,103.8409748,17z/data=!3m1!4b1!4m5!3m4!1s0x31da1952073eff29:0x2d6764446a838a2a!8m2!3d1.2782996!4d103.8431688','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d15584268-Reviews-Restaurant_JAG-Singapore.html',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (37,'Yellowpot','https://www.google.com.sg/maps/place/Yellow+Pot+Tanjong+Pagar/@1.2786983,103.840996,17z/data=!3m1!4b1!4m5!3m4!1s0x31da1959f5c7fc3f:0x113344a3343f235!8m2!3d1.2786983!4d103.84319','',2);
INSERT INTO `stores` (`store_id`,`store_name`, `googlereviews_url`, `tripadvisors_url`, `road_id`) VALUES (38,'Kilo Kitchen','https://www.google.com.sg/maps/place/Kilo+Kitchen+(Singapore)/@1.2794847,103.8410555,17z/data=!3m1!4b1!4m5!3m4!1s0x31da19e191a06275:0x5a6ae47a29bd00c1!8m2!3d1.2794847!4d103.8432495','https://www.tripadvisor.com.sg/Restaurant_Review-g294265-d12073254-Reviews-Kilo_Kitchen-Singapore.html',2);

CREATE TABLE `florescence`.`users` (
  `user_id` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `password` VARCHAR(255) NOT NULL ,
  `active` BOOLEAN NOT NULL ,
  `admin` BOOLEAN NOT NULL ,
  `store_id` INT NULL ,
  PRIMARY KEY (`user_id`),
  UNIQUE (`email`)
) ENGINE = InnoDB;
