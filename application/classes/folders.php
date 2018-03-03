<?php

class folders
{
    /* Path generators */
    static function getStreamCoversPath() {
        $root = config::getSetting("content", "heap_folder");
        $folder = config::getSetting("content", "stream_covers");

        if($root === null || $folder === null) {
            return null;
        }

        return new File(sprintf("%s/%s", $root, $folder));
    }

    static function getStreamCoversUrlPath() {
        $root = config::getSetting("content", "http_heap_folder");
        $folder = config::getSetting("content", "stream_covers");

        return sprintf("%s/%s", $root, $folder);
    }

    static function getStreamCoverPath($stream_id, $local = true) {
        $data = sprintf("/covers/stream%d.png", $stream_id);
        if($local)
        {
            $data = config::getSetting("content", "content_folder") . $data;
        }
        return $data;
    }
    
    static function getUserPicturePath($user_id, $local = true)
    {
        $data = sprintf("/avatars/userpicture%d.png", $user_id);
        if($local)
        {
            $data = config::getSetting("content", "content_folder") . $data;
        }
        return $data;
    }

    /* Dirs existence checks */
    static function getStreamCover($stream, $local = true)
    {
        if(file_exists(self::getStreamCoverPath($stream['sid'], true)))
        {
            return self::getStreamCoverPath($stream['sid'], $local);
        }
        
        if(file_exists(self::getUserPicturePath($stream['uid'], true)))
        {
            return self::getUserPicturePath($stream['uid'], $local);
        }
        
        return false;
    }
    
    static function getUserPicture($user, $local = true)
    {
        if(file_exists(self::getUserPicturePath($user['uid'], true)))
        {
            return self::getUserPicturePath($user['uid'], $local);
        }
        
        return false;
    }
}