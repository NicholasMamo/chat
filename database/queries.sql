--home - away from fixtures
SELECT `A`.`team_name` AS `Home`, `B`.`team_name` AS `Away`
FROM `pratos_teams` AS `A`, `pratos_teams` AS `B`, `pratos_fixtures`
WHERE `A`.`team_id` = `pratos_fixtures`.`fixture_home` AND `B`.`team_id` = `fixture_away`;

--group messages by minute
SELECT COUNT(*), CAST(`message_timestamp`/60 AS INT) AS `TIME`
FROM `pratos_messages`
GROUP BY `TIME`

--get messages from the past 1 day
SELECT `message_content`, `message_timestamp`, `is_spoiler`
FROM `pratos_messages`
WHERE `message_timestamp` > UNIX_TIMESTAMP(NOW()) - 60 * 60 * 24 * 1