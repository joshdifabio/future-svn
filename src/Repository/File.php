<?php
namespace FutureSVN\Repository;

use FutureSVN\Shell\FutureOutput;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class File extends Node
{
    /**
     * @return boolean
     */
    public function isFile()
    {
        return true;
    }
    
    /**
     * @return Directory
     */
    public function getDirectory()
    {
        return $this->repo->getDirectory(dirname($this->path), $this->rev);
    }
    
    /**
     * @return FutureOutput
     */
    public function getContents()
    {
        return $this->repo->execute('cat', $this->getUrl(true));
    }
}
