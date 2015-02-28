<?php
namespace FutureSVN\Repository;

use FutureSVN\Exception\LogicException;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class Directory extends Node
{
    public function isDirectory()
    {
        return true;
    }
    
    /**
     * @return Directory
     */
    public function getDirectory($path)
    {
        return $this->repo->getDirectory($this->absPath($path), $this->rev);
    }
    
    /**
     * @return File
     */
    public function getFile($path)
    {
        return $this->repo->getFile($this->absPath($path), $this->rev);
    }
 
    /**
     * @return FutureNodeCollection
     */
    public function getFilesAndDirectories($recursive = false)
    {
        $output = $this->repo->execute('list', $this->getUrl(true), array(
            '--depth' => $recursive ? 'infinity' : 'immediates',
            '--xml' => true,
        ));
        
        return new FutureNodeCollection($this->repo, $output);
    }
    
    /**
     * @return FutureNodeCollection
     */
    public function getDirectories($recursive = false)
    {
        $output = $this->repo->execute('list', $this->getUrl(true), array(
            '--depth' => $recursive ? 'infinity' : 'immediates',
            '--xml' => true,
        ));
        
        return new FutureNodeCollection($this->repo, $output, FutureNodeCollection::DIRS_ONLY);
    }
    
    /**
     * @return FutureNodeCollection
     */
    public function getFiles($recursive = false)
    {
        $output = $this->repo->execute('list', $this->getUrl(true), array(
            '--depth' => $recursive ? 'infinity' : 'files',
            '--xml' => true,
        ));
        
        return new FutureNodeCollection($this->repo, $output, FutureNodeCollection::FILES_ONLY);
    }
    
    /**
     * @return FutureCheckoutDirectory
     */
    public function checkout($targetPath)
    {
        throw new LogicException('Not implemented');
    }
}
