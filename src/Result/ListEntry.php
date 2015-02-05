<?php
namespace SubversionClient\Result;

class ListEntry
{
    private $xml;
    private $commit;
    
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }
    
    public function isDir()
    {
        return 0 === strcasecmp('dir', $this->getType());
    }
    
    public function isFile()
    {
        return 0 === strcasecmp('file', $this->getType());
    }
    
    public function getType()
    {
        return (string)$this->xml->attributes()->kind;
    }
    
    public function getName()
    {
        return (string)$this->xml->name;
    }
    
    public function getSize()
    {
        if (null !== $size = $this->xml->size) {
            return (int)(string)$this->xml->size;
        }
    }
    
    public function getCommit()
    {
        if (is_null($this->commit) && !is_null($this->xml->commit)) {
            $this->commit = new Commit($this->xml->commit);
        }
        
        return $this->commit;
    }
}
