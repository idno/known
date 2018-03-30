ALTER TABLE `config` ADD COLUMN `publish_status` varchar(255) NOT NULL DEFAULT 'published';
ALTER TABLE `entities` ADD COLUMN `publish_status` varchar(255) NOT NULL DEFAULT 'published';
ALTER TABLE `reader` ADD COLUMN `publish_status` varchar(255) NOT NULL DEFAULT 'published';
ALTER TABLE `config` ADD KEY `publish_status` (`publish_status`);
ALTER TABLE `entities` ADD KEY `publish_status` (`publish_status`);
ALTER TABLE `reader` ADD KEY `publish_status` (`publish_status`);
UPDATE `versions` SET `value` = '2016102601' WHERE `label` = 'schema';
