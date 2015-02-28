<?php
namespace FutureSVN\Repository;

use FutureSVN\FutureCommit;
use FutureSVN\Shell\FutureOutput;
use FutureSVN\XMLParser;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
abstract class Node
{
    const FILE = 'file';
    const DIRECTORY = 'dir';
    
    protected $repo;
    protected $path;
    protected $rev;
    protected $lastCommit;
    
    public function __construct(
        Repository $repository,
        $path = null,
        $revision = null,
        FutureCommit $lastCommit = null
    ) {
        $this->repo = $repository;
        $this->path = '/' . trim((string)$path, '/');
        $this->rev = $revision;
        $this->lastCommit = $lastCommit;
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
        return $this->path . ($withRevision && $this->rev ? '?p=' . $this->rev : '');
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
        return $this->create(null);
    }
    
    /**
     * @return static
     */
    public function atRevision($revision)
    {
        return $this->create($revision);
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
    
    /**
     * @return FutureCommit
     */
    public function getLastCommit()
    {
        if (!$this->rev || !$this->lastCommit) {
            $this->lastCommit = $this->doGetLastCommit();
        }
        
        return $this->lastCommit;
    }
    
    protected function absPath($path)
    {
        return '/' === $path{0} ? $path : $this->getPath() . '/' . $path;
    }
    
    private function doGetLastCommit()
    {
        $output = $this->repo->execute('info', $this->getUrl(true), array('--xml' => true));
        
        return new FutureCommit(
            $output->then(function (FutureOutput $output) {
                if ($infoXml = $output->getStreamContents()) {
                    return XMLParser::parseInfoForCommit(new \SimpleXMLElement($infoXml));
                }
            }),
            function ($timeout = null) use ($output) {
                $output->wait($timeout);
                if ($infoXml = $output->getStreamContents()) {
                    return XMLParser::parseInfoForCommit(new \SimpleXMLElement($infoXml));
                }
            }
        );
    }
    
    private function create($rev)
    {
        if ($this->lastCommit && (!$rev || $rev == $this->lastCommit->getRevision())) {
            $lastCommit = $this->lastCommit;
        } else {
            $lastCommit = null;
        }
        
        return $this->isDirectory() ? 
            new Directory($this->repo, $this->path, $rev, $this->lastCommit) :
            new File($this->repo, $this->path, $rev, $this->lastCommit);
    }
}
