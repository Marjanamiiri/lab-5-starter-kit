<?php

namespace src\Repositories;

require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../Models/Post.php';

use src\Models\Post as Post;

use mysqli_sql_exception;

class PostRepository extends Repository {

	/**
	 * @return Post[]
	 */
	public function getAllPosts(): array {
		$sqlStatement = $this->mysqlConnection->prepare("SELECT * FROM posts;");
		$sqlStatement->execute();
		$resultSet = $sqlStatement->get_result();

		$posts = [];
		while ($row = $resultSet->fetch_assoc()) {
			$posts[] = new Post($row);
		}

		return $posts;
	}

	/**
	 * @param int $id
	 * @return Post|false Post object if it was found, false otherwise
	 */
	public function getPostById(int $id): Post|false {
		$sqlStatement = $this->mysqlConnection->prepare('SELECT id, title, body, created_at, updated_at FROM posts WHERE id = ?');
		$sqlStatement->bind_param('i', $id);
		$sqlStatement->execute();
		$resultSet = $sqlStatement->get_result();
		if ($resultSet->num_rows === 1) {
			return (new Post($resultSet->fetch_assoc()));
		}
		return false;
	}

	/**
	 * @param string $title
	 * @param string $body
	 * @return Post|false the newly created Post object, or false in the case of a failure to save or retrieve the new record
	 */
	public function savePost(string $title, string $body): Post|false {
		$sqlStatement = $this->mysqlConnection->prepare("INSERT INTO posts (id, title, body, created_at, updated_at) VALUES(NULL, ?, ?, ?, NULL);"); // values are: id, title, body, created_at, updated_at
		$createdAt = date('Y-m-d H:i:s');
		$sqlStatement->bind_param('sss', $title, $body, $createdAt);
		try {
			$success = $sqlStatement->execute();
			if ($success) {
				$postId = $this->mysqlConnection->insert_id;
				return $this->getPostById($postId);
			}
			return false;
		} catch (mysqli_sql_exception) {
			return false;
		}
	}

	/**
	 * @param int $id
	 * @param string $title
	 * @param string $body
	 * @return bool
	 */
	public function updatePost(int $id, string $title, string $body): bool {
		$sqlStatement = $this->mysqlConnection->prepare("UPDATE posts SET title = ?, body = ?, updated_at = ? WHERE id = ?");
		$updatedAt = date('Y-m-d H:i:s');
		$sqlStatement->bind_param('sssi', $title, $body, $updatedAt, $id);
		return $sqlStatement->execute();
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function deletePostById(int $id): bool {
		$sqlStatement = $this->mysqlConnection->prepare("DELETE FROM posts WHERE id = ?;");
		$sqlStatement->bind_param('i', $id);
		return $sqlStatement->execute();
	}

}
