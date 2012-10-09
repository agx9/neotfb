ALTER TABLE `user` ADD COLUMN `FbEmail` VARCHAR(100) NULL AFTER `FbUserId`;


ALTER TABLE `user`     ADD COLUMN `Status` TINYINT(2) NULL AFTER `AccessToken`;


INSERT INTO `config`(`Id`,`ConfigKey`,`Value`) VALUES ( NULL,'APP_PAGE_NO_OF_ROWS_IN_TABLE','20');

INSERT INTO `config`(`Id`,`ConfigKey`,`Value`) VALUES ( NULL,'APP_URL','http://neofbapp.com');

ALTER TABLE `fb_groups`     ADD COLUMN `UserId` INT(11) NULL AFTER `Status`;

ALTER TABLE `fb_posts`     ADD COLUMN `UserId` INT(11) NULL AFTER `Status`;




ALTER TABLE `post_queue` ADD COLUMN `UserId` INT(11) NULL AFTER `Status`;



INSERT INTO `config`(`Id`,`ConfigKey`,`Value`) VALUES ( NULL,'APP_RESOURCE_PATH',NULL);