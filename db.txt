CREATE TABLE `googlereviews` (
  `id_review` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `relative_date` varchar(45) DEFAULT NULL,
  `retrieval_date` date DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `n_review_user` int(11) DEFAULT NULL,
  `n_photo_user` int(11) DEFAULT NULL,
  `url_user` varchar(255) DEFAULT NULL,
  `store` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

