<?php
namespace FutureSVN\Repository;

use FutureSVN\Shell\FutureOutput;
use FutureSVN\FutureCommit;
use React\Promise\FulfilledPromise;
use FutureSVN\XMLParser;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class FutureNodeCollection implements \IteratorAggregate
{
    const DIRS_ONLY = 'dir';
    const FILES_ONLY = 'file';
    
    private $repo;
    private $futureOutput;
    private $promise;
    private $nodes;
    private $typeFilter;
    
    public function __construct(
        Repository $repository,
        FutureOutput $futureOutput,
        $typeFilter = null
    ) {
        $this->repo = $repository;
        $this->futureOutput = $futureOutput;
        $this->typeFilter = $typeFilter;
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
                    if ('./' === $listEntryXml->name) {
                        continue;
                    }
                    
                    if ($this->typeFilter && $this->typeFilter !== (string)$listEntryXml->attributes()->kind) {
                        continue;
                    }
                    
                    $nodes[] = $this->createNode($listPath, $listEntryXml);
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
    
    private function createNode($listPath, $listEntryXml)
    {
        $nodePath = $listPath . '/' . $listEntryXml->name;
        
        $commit = new FutureCommit(new FulfilledPromise(
            XMLParser::parseCommit($listEntryXml->commit)
        ));
        
        if (!strcasecmp('dir', $listEntryXml->attributes()->kind)) {
            $node = new Directory($this->repo, $nodePath, $commit->getRevision(), $commit);
        } else {
            $node = new File($this->repo, $nodePath, $commit->getRevision(), $commit);
        }
        
        return $node;
    }
}
