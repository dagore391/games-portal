<?php
namespace app\model;

use app\config\Paths;
use app\data\Database;
use app\file\SecureImport;

class ForumModel {
	public static function getById(int $forumId) : ?object {
		return Database::getInstance()->query(
			'SELECT
				`forum`.`id` AS `forum_id`,
				`forum`.`title` AS `forum_title`,
				`forum`.`description` AS `forum_description`
			FROM
				`forum`
			WHERE
				`forum`.`id` = ?
			LIMIT 1;',
			[$forumId],
			[\PDO::PARAM_INT]
		)->firstSanitize();
	}
	
	public static function getTopMessagesMembers(int $limit) : array {
		$results = Database::getInstance()->query('SELECT
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`,
				COUNT(`member`.`id`) AS `member_totalmessages`
			FROM
				`forum_post`
			LEFT JOIN
				`member`
			ON
				`forum_post`.`author` = `member`.`id`
			GROUP BY
				`member_id`, `member_username`
			ORDER BY
				`member_totalmessages` DESC, `member_username` ASC
			LIMIT ?;',
			[$limit], [\PDO::PARAM_INT]
		)->resultsSanitize();
		foreach($results as $row) {
			$row->member_avatar = SecureImport::fixFileResourcePath(
				Paths::UMEMBERAVATAR . $row->member_username . '.jpg',
				Paths::UMEMBER . 'no-avatar.jpg'
			);
		}
		return $results;
	}
	
	public static function getTotalPostsByMember(int $memberId) : int {
		return Database::getInstance()->query('SELECT `id` FROM `forum_post` WHERE `author` = ?;', [$memberId], [\PDO::PARAM_INT])->count();
	}
	
	public static function getLimitLatestPostsByMember(int $memberId, int $limit) : array {
		return Database::getInstance()->query(
			'SELECT
				`forum_post`.`id` as `post_id`,
				`forum_post`.`title` as `post_title`,
				`forum_post`.`topic` as `post_topic`,
				`forum_post`.`published` as `post_published`,
				`forum_post`.`forum` AS `post_forum`,
				`forum`.`id` AS `forum_id`,
				`forum`.`title` AS `forum_title`
			FROM
				`forum_post`
			LEFT JOIN
				`forum`
			ON
				`forum_post`.`forum` = `forum`.`id`
			WHERE
				`forum_post`.`author` = ?
			ORDER BY
				`forum_post`.`published` DESC
			LIMIT ?;',
			[$memberId, $limit],
			[\PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getTotalTopicsByForum(int $forumId) : int {
		return Database::getInstance()->query('SELECT `id` FROM `forum_post` WHERE `forum` = ? AND `topic` IS NULL;', [$forumId], [\PDO::PARAM_INT])->count();
	}
	
	public static function getByParentForum(?int $parentForumId) : array {
		$values = $parentForumId === null ? [] : [$parentForumId];
		$types = $parentForumId === null ? [] : [\PDO::PARAM_INT];
		$forumsTemp = Database::getInstance()->query(
			"SELECT
				`forum`.`id` AS `forum_id`,
				`forum`.`title` AS `forum_title`,
				`forum`.`description` AS `forum_description`,
				`forum_category`.`id` AS `category_id`,
				`forum_category`.`title` AS `category_title`,
				`forum_category`.`description` AS `category_description`
			FROM
				`forum`
			RIGHT JOIN
				`forum_category`
			ON
				`forum`.`category` = `forum_category`.`id`
			WHERE
				" . (is_null($parentForumId) || $parentForumId <= 0 ? "`forum`.`parent_forum` IS NULL" : "`forum`.`parent_forum` = ?" ) . "
			ORDER BY
				`forum_category`.`position` ASC,
				`forum`.`position` ASC;",
			$values,
			$types
		)->resultsSanitize();
		$results = [];
		foreach($forumsTemp as $forum) {
			if(!isset($results[$forum->category_id])) {
				$results[$forum->category_id] = [
					'id' => $forum->category_id,
					'title' => $forum->category_title,
					'description' => $forum->category_description
				];
			}
			$results[$forum->category_id]['forums'][$forum->forum_id] = [
				'id' => $forum->forum_id,
				'title' => $forum->forum_title,
				'description' => $forum->forum_description,
				'posts' => $forum->forum_id !== null ? self::getPosts($forum->forum_id) : [],
				'last_post' => $forum->forum_id !== null ? self::getLastPost($forum->forum_id) : []
			];
		}
		return $results;
	}
	
	public static function getPosts(int $forumId) : array {
		return Database::getInstance()->query(
			'SELECT
				`forum_post`.`id` AS `post_id`,
				`forum_post`.`title` AS `post_title`,
				`forum_post`.`content` AS `post_content`,
				`forum_post`.`published` AS `post_published`,
				`forum_post`.`forum` AS `post_forum`,
				`forum_post`.`is_closed` AS `post_is_closed`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`
			FROM
				`forum_post`
			LEFT JOIN
				`member`
			ON
				`forum_post`.`author` = `member`.`id`
			WHERE
				`forum_post`.`forum` = ?;',
			[$forumId],
			[\PDO::PARAM_INT]
		)->resultsSanitize(['post_content']);
	}
	
	public static function getTheLatestPostForEachTopics(int $limit) : array {
		return Database::getInstance()->query(
			'SELECT
				`fp`.`id` AS `post_id`,
				`fp`.`title` AS `post_title`,
				`fp`.`content` AS `post_content`,
				`fp`.`published` AS `post_published`,
				`fp`.`topic` AS `post_topic`,
				`fp`.`is_closed` AS `post_is_closed`,
				`fp`.`forum` AS `post_forum`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`
			FROM
				`forum_post` `fp`
			LEFT JOIN
				`member`
			ON
				`fp`.`author` = `member`.`id`
			WHERE
				`fp`.`topic` IS NOT NULL
			AND
				`fp`.`published` = (
					SELECT
						MAX(`forum_post`.`published`)
					FROM
						`forum_post`
					WHERE
						`forum_post`.`topic` = `fp`.`topic`
				)
			ORDER BY
				`fp`.`published` DESC
			LIMIT ?;',
			[$limit],
			[\PDO::PARAM_INT]
			)->resultsSanitize(['post_content']);
	}
	
	public static function getLatestPosts(int $limit) : array {
		return Database::getInstance()->query(
			"SELECT
				`forum_post`.`id` AS `post_id`,
				`forum_post`.`title` AS `post_title`,
				`forum_post`.`content` AS `post_content`,
				`forum_post`.`published` AS `post_published`,
				`forum_post`.`topic` AS `post_topic`,
				`forum_post`.`is_closed` AS `post_is_closed`,
				`forum_post`.`forum` AS `post_forum`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`
			FROM
				`forum_post`
			LEFT JOIN
				`member`
			ON
				`forum_post`.`author` = `member`.`id`
			WHERE
				`forum_post`.`topic` IS NOT NULL
			ORDER BY
				`forum_post`.`published` DESC
			LIMIT ?;",
			[$limit],
			[\PDO::PARAM_INT]
		)->resultsSanitize(['post_content']);
	}
	
	public static function getLimitTopics(int $forumId, int $start, int $limit) : array {
		return Database::getInstance()->query(
			"SELECT
				`forum_post`.`id` AS `topic_id`,
				`forum_post`.`title` AS `topic_title`,
				`forum_post`.`content` AS `topic_content`,
				`forum_post`.`published` AS `topic_published`,
				`forum_post`.`forum` AS `topic_forum`,
				`forum_post`.`is_closed` AS `topic_is_closed`,
				(SELECT COUNT(`id`) FROM `forum_post` WHERE `topic` = `topic_id`) AS `topic_total_posts`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`
			FROM
				`forum_post`
			LEFT JOIN
				`member`
			ON
				`forum_post`.`author` = `member`.`id`
			WHERE
				`forum_post`.`topic` IS NULL
			AND
				`forum_post`.`forum` = ?
			LIMIT ?, ?;",
			[$forumId, $start, $limit],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize(['topic_content']);
	}
	
	public static function getTopicById(int $id) : ?object {
		$topicObject = Database::getInstance()->query(
			"SELECT
				`forum_post`.`id` AS `topic_id`,
				`forum_post`.`title` AS `topic_title`,
				`forum_post`.`content` AS `topic_content`,
				`forum_post`.`published` AS `topic_published`,
				`forum_post`.`is_closed` AS `topic_is_closed`,
				`forum`.`id` AS `forum_id`,
				`forum`.`title` AS `forum_title`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`,
				`member`.`account_group` AS `member_account_group`
			FROM
				`forum_post`
			LEFT JOIN
				`member`
			ON
				`forum_post`.`author` = `member`.`id`
			LEFT JOIN
				`forum`
			ON
				`forum_post`.`forum` = `forum`.`id`
			WHERE
				`forum_post`.`topic` IS NULL
			AND
				`forum_post`.`id` = ?
			LIMIT 1;",
			[$id],
			[\PDO::PARAM_INT]
		)->firstSanitize(['topic_content']);
		if(!is_null($topicObject)) {
			$topicObject->member_avatar = SecureImport::fixFileResourcePath(
				Paths::UMEMBERAVATAR . $topicObject->member_username . '.jpg',
				Paths::UMEMBER . 'no-avatar.jpg'
			);
		}
		return $topicObject;
	}
	
	public static function getUserLatestPost(int $userId) : ?object {
		$postObject = Database::getInstance()->query(
			"SELECT
				`forum_post`.`id` AS `post_id`,
				`forum_post`.`title` AS `post_title`,
				`forum_post`.`content` AS `post_content`,
				`forum_post`.`published` AS `post_published`,
				`forum_post`.`is_closed` AS `post_is_closed`,
				`forum`.`id` AS `forum_id`,
				`forum`.`title` AS `forum_title`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`,
				`member`.`account_group` AS `member_account_group`
			FROM
				`forum_post`
			LEFT JOIN
				`member`
			ON
				`forum_post`.`author` = `member`.`id`
			LEFT JOIN
				`forum`
			ON
				`forum_post`.`forum` = `forum`.`id`
			WHERE
				`member`.`id` = ?
			ORDER BY
				`forum_post`.`published` DESC
			LIMIT 1;",
			[$userId],
			[\PDO::PARAM_INT]
			)->firstSanitize(['post_content']);
			if($postObject !== null) {
				$postObject->member_avatar = SecureImport::fixFileResourcePath(
					Paths::UMEMBERAVATAR . $postObject->member_username . '.jpg',
					Paths::UMEMBER . 'no-avatar.jpg'
				);
			}
			return $postObject;
	}
	
	public static function getTopicLimitPosts(int $id, int $start, int $limit) : array {
		$results = Database::getInstance()->query(
			"SELECT
				`forum_post`.`id` AS `post_id`,
				`forum_post`.`title` AS `post_title`,
				`forum_post`.`content` AS `post_content`,
				`forum_post`.`published` AS `post_published`,
				`forum_post`.`forum` AS `post_forum`,
				`forum_post`.`is_closed` AS `post_is_closed`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`,
				`member`.`account_group` AS `member_account_group`
			FROM
				`forum_post`
			LEFT JOIN
				`member`
			ON
				`forum_post`.`author` = `member`.`id`
			WHERE
				`forum_post`.`topic` = ?
			LIMIT ?, ?;",
			[$id, $start, $limit],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize(['post_content']);
		foreach($results as $row) {
			$row->member_avatar = SecureImport::fixFileResourcePath(
				Paths::UMEMBERAVATAR . $row->member_username . '.jpg',
				Paths::UMEMBER . 'no-avatar.jpg'
			);
		}
		return $results;
	}
	
	public static function getTotalTopicPosts(int $id) : int {
		return Database::getInstance()->query('SELECT `id` FROM `forum_post` WHERE `topic` = ?;', [$id], [\PDO::PARAM_INT])->count();
	}
	
	public static function getLatestTopics(int $limit) : array {
		return Database::getInstance()->query(
			"SELECT
				`forum_post`.`id` AS `topic_id`,
				`forum_post`.`title` AS `topic_title`,
				`forum_post`.`content` AS `topic_content`,
				`forum_post`.`published` AS `topic_published`,
				`forum_post`.`forum` AS `topic_forum`,
				`forum_post`.`is_closed` AS `topic_is_closed`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`
			FROM
				`forum_post`
			LEFT JOIN
				`member`
			ON
				`forum_post`.`author` = `member`.`id`
			WHERE
				`forum_post`.`topic` IS NULL
			ORDER BY
				`forum_post`.`published` DESC
			LIMIT ?;",
			[$limit],
			[\PDO::PARAM_INT]
		)->resultsSanitize(['topic_content']);
	}
	
	public static function getLastPost(int $forumId) : ?object {
		return Database::getInstance()->query(
			"SELECT
				`forum_post`.`id` AS `post_id`,
				`forum_post`.`title` AS `post_title`,
				`forum_post`.`content` AS `post_content`,
				`forum_post`.`published` AS `post_published`,
				`forum_post`.`forum` AS `post_forum`,
				`forum_post`.`is_closed` AS `post_is_closed`,
				`forum_post`.`topic` AS `post_topic`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`
			FROM
				`forum_post`
			LEFT JOIN
				`member`
			ON
				`forum_post`.`author` = `member`.`id`
			WHERE
				`forum_post`.`forum` = ?
			ORDER BY
				`forum_post`.`published` DESC,
				`forum_post`.`id` ASC
			LIMIT 1;",
			[$forumId],
			[\PDO::PARAM_INT]
		)->firstSanitize(['post_content']);
	}
}
