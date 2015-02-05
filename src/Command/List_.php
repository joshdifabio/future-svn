<?php
namespace SubversionClient\Command;

use SubversionClient\Result\Lists;

class List_ extends AbstractCommand
{
    protected $subcommand = 'list';
    
    public function execute()
    {
        $process = $this->startProcess(array(
            'xml' => true,
            'verbose' => false,
            'non-interactive' => true,
        ));
        
        return new Lists($process->getResult());
    }
    
    public function getTargets()
    {
        return $this->args;
    }
    
    public function setTargets(array $targets)
    {
        return $this->args = $targets;
    }
    
    public function addTarget($target)
    {
        $this->args[] = $target;
        
        return $this;
    }
    
    public function getDepth()
    {
        return $this->getOption('depth');
    }
    
    public function setDepth($depth)
    {
        return $this->setOption('depth', $depth);
    }
    
    public function isIncremental()
    {
        return $this->getOption('incremental', false);
    }
    
    public function setIncremental($incremental)
    {
        return $this->setOption('incremental', $incremental);
    }
    
    public function isRecursive()
    {
        return $this->getOption('recursive', false);
    }
    
    public function setRecursive($recursive)
    {
        return $this->setOption('recursive', $recursive);
    }
    
    public function getRevision()
    {
        return $this->getOption('revision');
    }
    
    public function setRevision($revision)
    {
        return $this->setOption('revision', $revision);
    }
    
    public function isVerbose()
    {
        return $this->getOption('verbose', false);
    }
    
    public function setVerbose($verbose)
    {
        return $this->setOption('verbose', $verbose);
    }
    
    public function isXml()
    {
        return $this->getOption('xml', false);
    }
    
    public function setXml($xml)
    {
        return $this->setOption('xml', $xml);
    }
}
