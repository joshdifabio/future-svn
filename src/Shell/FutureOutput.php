<?php
namespace FutureSVN\Shell;

use FutureProcess\FutureResult;
use React\Promise\PromiseInterface;
use FutureSVN\Exception\RuntimeException;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class FutureOutput
{
    private $futureResult;
    private $promise;
    private $error;
    
    public function __construct(FutureResult $futureResult)
    {
        $this->futureResult = $futureResult;
        
        $that = $this;
        $error = &$this->error;
        $this->promise = $futureResult->then(
            function (FutureResult $result) use ($that, &$error) {
                if (0 === $result->getExitCode()) {
                    return $that;
                }
                
                throw $error = new RuntimeException($result->getStreamContents(2));
            },
            function (\Exception $_error) use (&$error) {
                throw $error = $_error;
            }
        );
    }
    
    /**
     * @return string
     */
    public function getContents()
    {
        $this->wait();
        
        return $this->futureResult->getStreamContents(1);
    }
    
    /**
     * @return resource
     */
    public function getStream()
    {
        $this->wait();
        
        return $this->futureResult->getStream(1)->getResource();
    }
    
    /**
     * @param double $timeout OPTIONAL
     * @return static
     */
    public function wait($timeout = null)
    {
        $this->futureResult->wait($timeout);
        
        if ($this->error) {
            throw $this->error;
        }
        
        return $this;
    }
    
    /**
     * @return PromiseInterface
     */
    public function promise()
    {
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
        return $this->promise->then($onFulfilled, $onError, $onProgress);
    }
}
