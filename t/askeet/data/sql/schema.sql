# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.

SET FOREIGN_KEY_CHECKS = 0;
# -----------------------------------------------------------------------
# ask_question 
# -----------------------------------------------------------------------
DROP TABLE IF EXISTS `ask_question`;

CREATE TABLE `ask_question`(
    `id` INTEGER NOT NULL AUTO_INCREMENT, 
    `user_id` INTEGER , 
    `title` TEXT , 
    `stripped_title` TEXT , 
    `body` TEXT , 
    `html_body` TEXT , 
    `interested_users` INTEGER default 0 , 
    `reports` INTEGER default 0 , 
    `created_at` DATETIME , 
    `updated_at` DATETIME ,  
    PRIMARY KEY(`id`),  
    INDEX `ask_question_FI_1` (`user_id`), 
    CONSTRAINT `ask_question_FK_1` 
      FOREIGN KEY (`user_id`)
      REFERENCES `ask_user` (`id`)
)
Type=InnoDB;
# -----------------------------------------------------------------------
# ask_answer 
# -----------------------------------------------------------------------
DROP TABLE IF EXISTS `ask_answer`;

CREATE TABLE `ask_answer`(
    `id` INTEGER NOT NULL AUTO_INCREMENT, 
    `question_id` INTEGER , 
    `user_id` INTEGER , 
    `body` TEXT , 
    `html_body` TEXT , 
    `relevancy_up` INTEGER default 0 , 
    `relevancy_down` INTEGER default 0 , 
    `reports` INTEGER default 0 , 
    `created_at` DATETIME ,  
    PRIMARY KEY(`id`),  
    INDEX `ask_answer_FI_1` (`question_id`), 
    CONSTRAINT `ask_answer_FK_1` 
      FOREIGN KEY (`question_id`)
      REFERENCES `ask_question` (`id`)
      ON DELETE CASCADE, 
    INDEX `ask_answer_FI_2` (`user_id`), 
    CONSTRAINT `ask_answer_FK_2` 
      FOREIGN KEY (`user_id`)
      REFERENCES `ask_user` (`id`)
)
Type=InnoDB;
# -----------------------------------------------------------------------
# ask_user 
# -----------------------------------------------------------------------
DROP TABLE IF EXISTS `ask_user`;

CREATE TABLE `ask_user`(
    `id` INTEGER NOT NULL AUTO_INCREMENT, 
    `nickname` VARCHAR(50) , 
    `first_name` VARCHAR(100) , 
    `last_name` VARCHAR(100) , 
    `email` VARCHAR(100) , 
    `sha1_password` VARCHAR(40) , 
    `salt` VARCHAR(32) , 
    `has_paypal` INTEGER default 0 , 
    `want_to_be_moderator` INTEGER default 0 , 
    `is_moderator` INTEGER default 0 , 
    `is_administrator` INTEGER default 0 , 
    `deletions` INTEGER default 0 , 
    `created_at` DATETIME ,  
    PRIMARY KEY(`id`))
Type=InnoDB;
# -----------------------------------------------------------------------
# ask_interest 
# -----------------------------------------------------------------------
DROP TABLE IF EXISTS `ask_interest`;

CREATE TABLE `ask_interest`(
    `question_id` INTEGER NOT NULL , 
    `user_id` INTEGER NOT NULL , 
    `created_at` DATETIME ,  
    PRIMARY KEY(`question_id`,`user_id`),  
    CONSTRAINT `ask_interest_FK_1` 
      FOREIGN KEY (`question_id`)
      REFERENCES `ask_question` (`id`)
      ON DELETE CASCADE, 
    INDEX `ask_interest_FI_2` (`user_id`), 
    CONSTRAINT `ask_interest_FK_2` 
      FOREIGN KEY (`user_id`)
      REFERENCES `ask_user` (`id`)
)
Type=InnoDB;
# -----------------------------------------------------------------------
# ask_relevancy 
# -----------------------------------------------------------------------
DROP TABLE IF EXISTS `ask_relevancy`;

CREATE TABLE `ask_relevancy`(
    `answer_id` INTEGER NOT NULL , 
    `user_id` INTEGER NOT NULL , 
    `score` INTEGER , 
    `created_at` DATETIME ,  
    PRIMARY KEY(`answer_id`,`user_id`),  
    CONSTRAINT `ask_relevancy_FK_1` 
      FOREIGN KEY (`answer_id`)
      REFERENCES `ask_answer` (`id`)
      ON DELETE CASCADE, 
    INDEX `ask_relevancy_FI_2` (`user_id`), 
    CONSTRAINT `ask_relevancy_FK_2` 
      FOREIGN KEY (`user_id`)
      REFERENCES `ask_user` (`id`)
)
Type=InnoDB;
# -----------------------------------------------------------------------
# ask_question_tag 
# -----------------------------------------------------------------------
DROP TABLE IF EXISTS `ask_question_tag`;

CREATE TABLE `ask_question_tag`(
    `question_id` INTEGER NOT NULL , 
    `user_id` INTEGER NOT NULL , 
    `created_at` DATETIME , 
    `tag` VARCHAR(100) , 
    `normalized_tag` VARCHAR(100) NOT NULL ,  
    PRIMARY KEY(`question_id`,`user_id`,`normalized_tag`),  
    KEY `normalized_tag_index` (`normalized_tag`),  
    CONSTRAINT `ask_question_tag_FK_1` 
      FOREIGN KEY (`question_id`)
      REFERENCES `ask_question` (`id`)
      ON DELETE CASCADE, 
    INDEX `ask_question_tag_FI_2` (`user_id`), 
    CONSTRAINT `ask_question_tag_FK_2` 
      FOREIGN KEY (`user_id`)
      REFERENCES `ask_user` (`id`)
)
Type=InnoDB;
# -----------------------------------------------------------------------
# ask_search_index 
# -----------------------------------------------------------------------
DROP TABLE IF EXISTS `ask_search_index`;

CREATE TABLE `ask_search_index`(
    `question_id` INTEGER , 
    `word` VARCHAR(255) , 
    `weight` INTEGER ,  
    KEY `word_index` (`word`),  
    INDEX `ask_search_index_FI_1` (`question_id`), 
    CONSTRAINT `ask_search_index_FK_1` 
      FOREIGN KEY (`question_id`)
      REFERENCES `ask_question` (`id`)
      ON DELETE CASCADE)
Type=InnoDB;
# -----------------------------------------------------------------------
# ask_report_question 
# -----------------------------------------------------------------------
DROP TABLE IF EXISTS `ask_report_question`;

CREATE TABLE `ask_report_question`(
    `question_id` INTEGER NOT NULL , 
    `user_id` INTEGER NOT NULL , 
    `created_at` DATETIME ,  
    PRIMARY KEY(`question_id`,`user_id`),  
    CONSTRAINT `ask_report_question_FK_1` 
      FOREIGN KEY (`question_id`)
      REFERENCES `ask_question` (`id`)
      ON DELETE CASCADE, 
    INDEX `ask_report_question_FI_2` (`user_id`), 
    CONSTRAINT `ask_report_question_FK_2` 
      FOREIGN KEY (`user_id`)
      REFERENCES `ask_user` (`id`)
)
Type=InnoDB;
# -----------------------------------------------------------------------
# ask_report_answer 
# -----------------------------------------------------------------------
DROP TABLE IF EXISTS `ask_report_answer`;

CREATE TABLE `ask_report_answer`(
    `answer_id` INTEGER NOT NULL , 
    `user_id` INTEGER NOT NULL , 
    `created_at` DATETIME ,  
    PRIMARY KEY(`answer_id`,`user_id`),  
    CONSTRAINT `ask_report_answer_FK_1` 
      FOREIGN KEY (`answer_id`)
      REFERENCES `ask_answer` (`id`)
      ON DELETE CASCADE, 
    INDEX `ask_report_answer_FI_2` (`user_id`), 
    CONSTRAINT `ask_report_answer_FK_2` 
      FOREIGN KEY (`user_id`)
      REFERENCES `ask_user` (`id`)
)
Type=InnoDB;
  
  
  
  
  
  
  
  
  
# This restores the fkey checks, after having unset them
# in database-start.tpl

SET FOREIGN_KEY_CHECKS = 1;

