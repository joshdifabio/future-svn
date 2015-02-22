<?php
namespace FutureSVN\Repository;

use FutureSVN\Shell\FutureOutput;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class FutureNodeCollection implements \IteratorAggregate
{
    private $repo;
    private $futureOutput;
    private $promise;
    private $nodes;
    
    public function __construct(Repository $repository, FutureOutput $futureOutput)
    {
        $this->repo = $repository;
        $this->futureOutput = $futureOutput;
    }
    
    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        if (is_null($this->nodes)) {
            $nodes = array();

            $listsXmlString = $this->futureOutput->getStreamContents();
            $listsXml = new \SimpleXMLIterator($listsXmlString);
            foreach ($listsXml as $listXml) {
                $listPath = $this->getPath((string)$listXml->attributes()->path);
                foreach ($listXml as $listEntryXml) {
                    $nodePath = $listPath . '/' . $listEntryXml->name;
                    $revision = (int)(string)$listEntryXml->commit->attributes()->revision;
                    if (!strcasecmp('dir', $listEntryXml->attributes()->kind)) {
                        $nodes[] = $this->repo->getDirectory($nodePath, $revision);
                    } else {
                        $nodes[] = $this->repo->getFile($nodePath, $revision);
                    }
                }
            }
            
            $this->nodes = $nodes;
        }
        
        return new \ArrayIterator($this->nodes);
    }
    
    public function wait($timeout = null)
    {
        $this->futureOutput->wait($timeout);
        
        return $this;
    }
    
    public function promise()
    {
        if (!$this->promise) {
            $that = $this;
            $this->promise = $this->futureOutput->then(function () use ($that) {
                return $that;
            });
        }
        
        return $this->promise;
    }
    
    public function then($onFulfilled = null, $onError = null, $onProgress = null)
    {
        return $this->promise()->then($onFulfilled, $onError, $onProgress);
    }
    
    private function getPath($url)
    {
        $repoUrl = rtrim($this->repo->getUrl(), '/');
        
        return trim(substr($url, strlen($repoUrl)), '/');
    }
}
