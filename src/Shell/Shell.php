<?php
namespace FutureSVN\Shell;

use FutureProcess\Shell as ProcessShell;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class Shell
{
    private $futureShell;
    private $commandPrefix;
    private $descriptorSpec;
    
    public function __construct(ProcessShell $futureShell, $commandPrefix = 'svn')
    {
        $this->futureShell = $futureShell;
        $this->commandPrefix = $commandPrefix;
        $this->descriptorSpec = array(
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );
    }
    
    public function execute($subcommand, $args = array(), array $options = array())
    {
        $process = $this->futureShell->startProcess(
            $this->assembleCommand($subcommand, $args, $options),
            $this->descriptorSpec
        );
        
        return new FutureOutput($process->getResult());
    }
    
    private function assembleCommand($subcommand, $args, array $options)
    {
        $parts = array(
            $this->commandPrefix,
            $subcommand,
        );
        
        if ($argsString = $this->assembleArgs($args)) {
            $parts[] = $argsString;
        }
        
        if ($optionsString = $this->assembleOptions($options)) {
            $parts[] = $optionsString;
        }
        
        return implode(' ', $parts);
    }
    
    private function assembleArgs($args)
    {
        $args = is_array($args) ? $args : array($args);
                
        $argStrings = array();
        
        foreach ($args as $value) {
            $argStrings[] = '"' . addslashes($value) . '"';
        }
        
        return implode(' ', $argStrings);
    }
    
    private function assembleOptions(array $options)
    {
        $optionStrings = '';
        
        foreach ($options as $name => $value) {
            if (false !== $value) {
                $optionStrings[] = $name . (true === $value ? '' : '="' . addslashes($value) . '"');
            }
        }
        
        return implode(' ', $optionStrings);
    }
}
