<?php
namespace FutureSVN\Repository;

use FutureSVN\Shell\Shell as SVNShell;
use FutureProcess\Shell as ProcessShell;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    private $repositoryFactory;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        $shell = new SVNShell(new ProcessShell);
        $this->repositoryFactory = new Factory($shell);
    }
    
    public function testList()
    {
        $url = 'http://svn.apache.org/repos/asf/spamassassin/';
        $repository = $this->repositoryFactory->createRepository($url);
        
        $nodes = $repository->getDirectory('branches/3.0')->getNodes();
        foreach ($nodes as $node) {
            echo "{$node->getUrl(true)}\n";
        }
    }
}
