<?php
namespace models;
use PDO;

class Comment extends Base
{
    public function add($content,$blog_id)
    {
        $stmt = self::$pdo->prepare("INSERT INTO comments (content,blog_id,user_id) VALUES(?,?,?)");
        $stmt->execute([
            $content,
            $blog_id,
            $_SESSION['id'],
        ]);
    }

    public function getComments($blogId)
    {
        $sql = "SELECT c.* , u.email , u.avatar 
                FROM comments c 
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.blog_id = ?
                ORDER BY c.id DESC";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([$blogId]);
        // var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}