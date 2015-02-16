<?php
namespace FutureSVN;

use FutureSVN\Shell\FutureOutput;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class Commit
{
    private $futureOutput;
    
    public function __construct(FutureOutput $futureOutput)
    {
        $this->futureOutput = $futureOutput;
    }
    
    public function wait($timeout = null)
    {
        $this->futureOutput->wait($timeout);
        
        return $this;
    }
}
