<?php
namespace SubversionClient;

use FutureProcess\Shell;
use SubversionClient\Command\List_ as ListCommand;
use SubversionClient\Command\Assembler;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testList()
    {
        $listCommand = new ListCommand(new Assembler('svn'), new Shell);
        $listCommand->setNonInteractive(true);
        $listCommand->addTarget('http://svn.apache.org/repos/asf/spamassassin/trunk/');
        $listCommand->addTarget('http://svn.apache.org/repos/asf/spamassassin/trunk/tools');
        
        foreach ($listCommand->execute() as $list) {
            echo "\n{$list->getPath()}\n";
            echo str_repeat('-', strlen($list->getPath())) . "\n";
            foreach ($list as $listEntry) {
                echo "{$listEntry->getName()}\n";
            }
        }
    }
}
