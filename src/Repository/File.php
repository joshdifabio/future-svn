<?php
namespace FutureSVN\Repository;

use FutureProcess\FutureStream;

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
     * @return FutureStream
     */
    public function getContents()
    {
        return $this->repo->execute('cat', $this->getUrl(true))
            ->getStream(1);
    }
}
