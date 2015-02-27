<?php
namespace FutureSVN\Repository;

use FutureSVN\Shell\Shell as SVNShell;
use FutureProcess\Shell as ProcessShell;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    private $processShell;
    private $repositoryFactory;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        $this->processShell = new ProcessShell;
        $shell = new SVNShell($this->processShell);
        $this->repositoryFactory = new Factory($shell);
    }
    
    public function testList()
    {
        $url = 'http://svn.apache.org/repos/asf/spamassassin/';
        $repository = $this->repositoryFactory->createRepository($url);
        
        $nodes = $repository->getDirectory('branches')->getNodes();
        foreach ($nodes as $node) {
            if ($node->isDirectory()) {
                $lastCommit = $node->getLastCommit();
                $rev = $lastCommit->getRevision();
                $author = $lastCommit->getAuthor();
                $time = $lastCommit->getDate()->format(\DateTime::RFC1123);
                echo $node->getUrl() . " r$rev by $author at $time\n";
            }
        }
    }
    
    public function testGetLastCommit()
    {
        $url = 'http://svn.apache.org/repos/asf/spamassassin/';
        $repository = $this->repositoryFactory->createRepository($url);
        
        echo "Last rev: {$repository->getDirectory('branches')->getLastCommit()->getRevision()}\n";
    }
}
