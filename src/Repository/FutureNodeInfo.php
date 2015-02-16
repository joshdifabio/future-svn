<?php
namespace FutureSVN\Repository;

use FutureSVN\Shell\FutureOutput;
use React\Promise\PromiseInterface;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class FutureNodeInfo extends \ArrayAccess
{
    private $futureOutput;
    private $promise;
    
    public function __construct(FutureOutput $futureOutput)
    {
        $this->futureOutput = $futureOutput;
    }
    
    /**
     * @return \DateTime
     */
    public function getLastChangeTime()
    {
        $this->wait();
    }
    
    /**
     * @return int
     */
    public function getLastChangeRevision()
    {
        $this->wait();
    }
    
    /**
     * @return string
     */
    public function getLastChangeAuthor()
    {
        $this->wait();
    }
    
    /**
     * @param double $timeout
     * @return static
     */
    public function wait($timeout = null)
    {
        $this->futureOutput->wait($timeout);
        
        return $this;
    }
    
    /**
     * @return PromiseInterface
     */
    public function promise()
    {
        if (!$this->promise) {
            $that = $this;
            $this->promise = $this->futureOutput->promise()->then(function () use ($that) {
                return $that;
            });
        }
        
        return $this->promise;
    }
    
    /**
     * @param callable $onFulfilled
     * @param callable $onError
     * @param callable $onProgress
     * @return PromiseInterface
     */
    public function then($onFulfilled = null, $onError = null, $onProgress = null)
    {
        return $this->promise()->then($onFulfilled, $onError, $onProgress);
    }
}
