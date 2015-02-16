<?php
namespace FutureSVN\Repository;

use FutureSVN\Shell\Shell;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class Factory
{
    private $shell;
    
    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }
    
    /**
     * @return Repository
     */
    public function createRepository($url)
    {
        return new Repository($this->shell, $url);
    }
}
