<?php
namespace FutureSVN\Repository;

use FutureSVN\Commit;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
abstract class Node
{
    protected $repo;
    protected $path;
    protected $rev;
    
    public function __construct(Repository $repository, $path = null, $revision = null)
    {
        $this->repo = $repository;
        $this->path = '/' . trim((string)$path, '/');
        $this->rev = $revision;
    }
    
    /**
     * @return boolean
     */
    public function isDirectory()
    {
        return false;
    }
    
    /**
     * @return boolean
     */
    public function isFile()
    {
        return false;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return basename($this->path);
    }
    
    /**
     * @return string
     */
    public function getUrl($withRevision = false)
    {
        return $this->repo->getUrl() . $this->getPath($withRevision);
    }
    
    /**
     * @return string
     */
    public function getPath($withRevision = false)
    {
        return $this->path . ($withRevision && $this->rev ? '@' . $this->rev : '');
    }
    
    /**
     * @return null|int
     */
    public function getRevision()
    {
        return $this->rev;
    }
    
    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repo;
    }
    
    /**
     * @return static
     */
    public function atHead()
    {
        return new static($this->repo, $this->path);
    }
    
    /**
     * @return static
     */
    public function atRevision($revision)
    {
        return new static($this->repo, $this->path, $revision);
    }
    
    /**
     * @return FutureNodeInfo
     */
    public function getInfo()
    {
        $output = $this->repo->execute('info', $this->getUrl(true));
        
        return new FutureNodeInfo($output);
    }
    
    /**
     * @return Commit
     */
    public function copyToHead(
        $destination,
        $commitMessage,
        $createParents = true,
        $ignoreExternals = false
    ) {
        $output = $this->repo->execute(
            'copy',
            array(
                $this->getUrl(true),
                $this->absPath($destination),
            ),
            array(
                '-m' => $commitMessage,
                '--parents' => $createParents,
                '--ignore-externals' => $ignoreExternals,
            )
        );
        
        return new Commit($output);
    }
    
    /**
     * @return Commit
     */
    public function deleteFromHead($commitMessage)
    {
        $output = $this->repo->execute('delete', $this->getUrl(false), array(
            '-m' => $commitMessage,
            '--keep-local' => true,
            '--force' => true,
        ));
        
        return new Commit($output);
    }
    
    protected function absPath($path)
    {
        return '/' === $path{0} ? $path : $this->getPath() . '/' . $path;
    }
}
