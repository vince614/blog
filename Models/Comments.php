<?php

class Comments extends Mysql
{

    /**
     * Create comment
     * @param $comment
     * @param $authorId
     * @param $ticketId
     */
    public function createComment($comment, $authorId, $ticketId)
    {
        $req = Mysql::_getConnection()->prepare("INSERT INTO comments (comment, ticketId, authorId, date_public) VALUES (?, ?, ?, ?)");
        $req->execute(array($comment, (int)$ticketId, (int)$authorId, time()));
    }

    /**
     * Fetch comments
     * @param $ticketId
     * @return bool|mixed
     */
    public function fetchComments($ticketId)
    {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM comments WHERE ticketId = ?");
        $req->execute(array($ticketId));
        if ($req->rowCount() > 0) {
            return $req->fetchAll();
        }
        return false;
    }

    /**
     * Delete comment
     * @param $commentId
     * @return bool
     */
    public function deleteComment($commentId)
    {
        $req = Mysql::_getConnection()->prepare("DELETE FROM comments WHERE id = ?");
        $req->execute(array($commentId));
        if ($req->rowCount() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Update comment
     * @param $comment
     * @param $commentId
     * @return bool
     */
    public function updateComment($comment, $commentId)
    {
        $req = Mysql::_getConnection()->prepare("UPDATE comments SET comment = ? WHERE id = ?");
        $req->execute(array($comment, $commentId));
        if ($req->rowCount() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Signal comment
     * @param $commentId
     * @param $authorId
     * @return string
     */
    public function signalComment($commentId, $authorId)
    {
        /** @var PDO $pdo */
        $pdo = Mysql::_getConnection();

        $req = $pdo->prepare("SELECT * FROM signal WHERE commentId = ? AND authorId = ?");
        $req->execute(array($commentId, $authorId));
        if ($req->rowCount() == 0) {
            $req = $pdo->prepare("INSERT INTO signal (commentId, authorId, date_signal) VALUES (?, ?, ?)");
            $req->execute(array((int) $commentId, (int) $authorId, time()));
        } else {
            return 'Vous avez déjà signalé ce commentaire';
        }
    }
}