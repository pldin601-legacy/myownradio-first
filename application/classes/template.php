<?php

class template
{
    protected $template;
    protected $variables;
    protected $raw;
    
    public function __construct($template)
    {
        if (!file_exists($template))
        {
            throw new Exception("Template not found", null, null);
        }
        $this->reset()->template = file_get_contents($template);
    }
    
    public function addVariable($key, $value, $raw = false)
    {
        $this->variables->{$key} = $value;
        $this->raw->{$key} = $raw;
        return $this;
    }
    
    public function makeDocument()
    {
        $result = preg_replace_callback("/\\$\{(.+?)\}/", function ($match) {
            if (isset($this->variables->{$match[1]}))
            {
                if ($this->raw->{$match[1]} === false)
                {
                    return htmlspecialchars($this->variables->{$match[1]});
                }
                else
                {
                    return $this->variables->{$match[1]};
                }
            }
            else
            {
                return "";
            }
        }, $this->template);
        
        return $result;
    }
    
    public function reset()
    {
        $this->variables = new stdClass();
        $this->raw = new stdClass();
        return $this;
    }
}
