DROP TABLE IF EXISTS `games`;

CREATE TABLE `games` (
  `gameId` INT(10) AUTO_INCREMENT PRIMARY KEY,
  `teamId` VARCHAR(20) NOT NULL,
  `channelId` VARCHAR(20) NOT NULL,
  `creatorName` VARCHAR(40) NOT NULL,
  `opponentName` VARCHAR(40) NOT NULL,
  `nextMoverName` VARCHAR(40) NOT NULL,
  `winnerName` VARCHAR(40),
  `status` TINYINT(1) NOT NULL,
  INDEX(`channelId`),
  INDEX(`status`)
);
