<?php
namespace SubversionClient;

use FutureProcess\Shell;

class Client
{
    private $shell;
    
    public function __construct(Shell $shell, $commandPrefix = 'svn')
    {
        $this->shell = $shell;
    }
    
    public function startProcess($subcommand, array $args = array(), array $options = array())
    {
        
    }
}
