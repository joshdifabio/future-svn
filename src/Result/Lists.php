<?php
namespace SubversionClient\Result;

use FutureProcess\FutureResult as ProcessResult;

class Lists extends AbstractResult implements \IteratorAggregate
{
    private $processResult;
    private $timeout;
    private $lists;
    
    public function __construct(ProcessResult $processResult, $timeout = 30)
    {
        parent::__construct($processResult);
        
        $this->processResult = $processResult;
        $this->timeout = $timeout;
    }
    
    public function getIterator()
    {
        if (is_null($this->lists)) {
            $this->wait($this->timeout);
            $output = $this->processResult->getStreamContents(1);
            
            $lists = array();
            
            foreach (new \SimpleXMLIterator($output) as $listXml) {
                $lists[] = new List_($listXml);
            }
            
            $this->lists = new \ArrayIterator($lists);
        }
        
        return $this->lists;
    }
}
