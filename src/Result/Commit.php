<?php
namespace SubversionClient\Result;

class Commit
{
    private $xml;
    private $date;
    
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }
    
    public function getRevision()
    {
        if (null !== $revision = $this->xml->attributes()->revision) {
            return (int)(string)$revision;
        }
    }
    
    public function getAuthor()
    {
        if (null !== $author = $this->xml->author) {
            return (string)$author;
        }
    }
    
    public function getDate()
    {
        if (is_null($this->date) && null !== $date = $this->xml->date) {
            $this->date = new \DateTime((string)$date);
        }
        
        return $this->date;
    }
}
