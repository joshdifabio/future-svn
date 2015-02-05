<?php
namespace SubversionClient\Result;

use FutureProcess\FutureResult as ProcessResult;
use React\Promise\RejectedPromise;

abstract class AbstractResult
{
    protected $error;
    
    private $processResult;
    private $promise;
    
    public function __construct(ProcessResult $processResult)
    {
        $this->processResult = $processResult;
        
        $error = &$this->error;
        $that = $this;
        $this->promise = $processResult->then(
            function (ProcessResult $processResult) use (&$error, $that) {
                if (0 !== $processResult->getExitCode()) {
                    $error = new \RuntimeException($processResult->getStreamContents(2));
                    return new RejectedPromise($error);
                }
                
                return $that;
            },
            function (\Exception $_error) use (&$error) {
                $error = $_error;
                
                return new RejectedPromise($_error);
            }
        );
    }
    
    public function wait($timeout = null)
    {
        $this->processResult->wait($timeout);
        
        if ($this->error) {
            throw $this->error;
        }
        
        return $this;
    }
    
    public function then($onFulfilled = null, $onError = null, $onProgress = null)
    {
        return $this->promise->then($onFulfilled, $onError, $onProgress);
    }
}
