<?php

class Comments extends Mysql {

    /**
     * Create comment
     * @param $comment
     * @param $authorId
     * @return $this
     */
    public function createComment($comment, $authorId) {
        $req = parent::_getConnection()->prepare("INSERT INTO comments (comment, author, date_public) VALUES (?, ?, ?)");
        $req->execute(array($comment, $authorId, time()));
        return $this;
    }

    /**
     * Read comment
     * @param $commentId
     * @return bool|mixed
     */
    public function readComments($commentId) {
        $req = parent::_getConnection()->prepare("SELECT * FROM comments WHERE id = ?");
        $req->execute(array($commentId));
        if ($req->rowCount() > 0) {
            return $req->fetch();
        }
        return false;
    }

    /**
     * Delete comment
     * @param $commentId
     * @return bool
     */
    public function deleteComment($commentId) {
        $req = parent::_getConnection()->prepare("DELETE FROM comments WHERE id = ?");
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
    public function updateComment($comment, $commentId) {
        $req = parent::_getConnection()->prepare("UPDATE comments SET comment = ? WHERE id = ?");
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
     * @return $this
     */
    public function signalComment($commentId, $authorId) {
        $req = parent::_getConnection()->prepare("INSERT INTO signal (commentId, flagmanId, date_signal) VALUES (?, ?, ?)");
        $req->execute(array($commentId, $authorId, time()));
        return $this;
    }
}