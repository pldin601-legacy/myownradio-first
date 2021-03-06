<?php

class validUniqueList extends validVariable
{
    public function __construct($data)
    {
        if (!preg_match("/^[0-9A-Za-z]{8}(,[0-9A-Za-z]{8})*$/", $data))
        {
            throw new validException("Invalid tracks list");
        }
        $this->variable = $data;
    }
    
    public function count()
    {
        return count(explode(",", $this->variable));
    }

    public function getArray()
    {
        return explode(",", $this->variable);
    }
}
