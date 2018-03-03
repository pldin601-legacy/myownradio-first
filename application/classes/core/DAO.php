<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.10.14
 * Time: 17:33
 */

class DAO extends Model {
    use Singleton;

    private $visitor;

    public function DAO() {
        $this->visitor = Visitor::getInstance();
    }

    /**
     * @param $stream_id
     * @param bool $read_only
     * @return radioStream
     * @throws streamException
     */
    public function getStream($stream_id, $read_only = true) {

        $hash = $this->database->query_single_row("SELECT * FROM `r_streams` WHERE (:sid != '') AND (`sid` = :sid OR `permalink` = :sid)", array('sid' => $stream_id));

        if($hash === null) {
            throw new streamException("Stream not exists", 1002);
        }

        return new radioStream($hash, $read_only);

    }

    /**
     * @return ArrayObject with stream objects
     * @throws Exception Illegal arguments
     */
    public function getAllStreams() {

        $streams = new ArrayObject();

        if (func_num_args() === 0) {
            $query = "SELECT * FORM r_streams WHERE 1";
        } elseif (func_num_args() === 2) {
            $a = func_get_arg(0);
            $b = func_get_arg(1);
            $query = "SELECT * FROM r_streams WHERE 1 LIMIT $a, $b";
        } else {
            throw new Exception("Illegal arguments");
        }

        foreach($this->database->query($query) as $hash) {
            $streams->append(new radioStream($hash, true));
        }

        return $streams;

    }
}