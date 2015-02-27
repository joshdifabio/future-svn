<?php
namespace FutureSVN;

use React\Promise\PromiseInterface;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class FutureCommit
{
    private $dataWaitFn;
    private $promise;
    private $data;
    private $error;
    private static $emptyData = array(
        'revision' => null,
        'author' => null,
        'date' => null,
    );
    
    public function __construct(PromiseInterface $dataPromise, callable $dataWaitFn = null)
    {
        $data = &$this->data;
        $error = &$this->error;
        $emptyData = self::$emptyData;
        $that = $this;
        $this->promise = $dataPromise->then(
            function ($_data) use (&$data, $emptyData, $that) {
                $data = $_data ?: $emptyData;
                return $that;
            },
            function (\Exception $_error) use (&$error) {
                $error = $_error;
                throw $error;
            }
        );
        
        $this->dataWaitFn = $dataWaitFn ?: function () {};
    }
    
    /**
     * @return null|int
     */
    public function getRevision()
    {
        $this->wait();
        
        return $this->data['revision'];
    }
    
    /**
     * @return null|string
     */
    public function getAuthor()
    {
        $this->wait();
        
        return $this->data['author'];
    }
    
    /**
     * @return null|\DateTime
     */
    public function getDate()
    {
        $this->wait();
        
        return $this->data['date'];
    }
    
    /**
     * @param double $timeout
     * @return $this
     */
    public function wait($timeout = null)
    {
        call_user_func($this->dataWaitFn, $timeout);
        
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
