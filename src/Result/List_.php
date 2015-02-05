<?php
namespace SubversionClient\Result;

class List_ implements \IteratorAggregate
{
    private $xml;
    private $entries;
    
    public function __construct(\SimpleXMLIterator $xml)
    {
        $this->xml = $xml;
    }
    
    public function getPath()
    {
        return (string)$this->xml->attributes()->path;
    }
    
    public function getIterator()
    {
        if (is_null($this->entries)) {
            $entries = array();
            
            foreach ($this->xml as $listEntryXml) {
                $entries[] = new ListEntry($listEntryXml);
            }
            
            $this->entries = new \ArrayIterator($entries);
        }
        
        return $this->entries;
    }
}
