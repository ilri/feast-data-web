ALTER TABLE `season` CHANGE `updated_by` `updated_by` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `labour_activity` CHANGE `updated_by` `updated_by` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `user` CHANGE `updated_by` `updated_by` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `user` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `user` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;