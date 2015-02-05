<?php
namespace SubversionClient\Command;

class Assembler
{
    private $commandPrefix;
    
    public function __construct($commandPrefix = 'svn')
    {
        if (!$commandPrefix) {
            throw new \LogicException('No command prefix was specified');
        }
        
        $this->commandPrefix = $commandPrefix;
    }
    
    public function assemble($subcommand, array $args = array(), array $options = array())
    {
        if (!$subcommand) {
            throw new \LogicException('No subcommand was specified');
        }
        
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
    
    private function assembleArgs(array $args)
    {
        $keys = array_keys($args);
        
        if ($keys !== array_flip($keys)) {
            throw new \LogicException(
                'Arguments must be zero-indexed and consecutively numbered'
            );
        }
        
        $argsString = '';
        
        foreach ($args as $value) {
            $argsString .= ' "' . addslashes($value) . '"';
        }
        
        return $argsString;
    }
    
    private function assembleOptions(array $options)
    {
        $optionsString = '';
        
        foreach ($options as $name => $value) {
            if (false === $value) {
                continue;
            }
            
            if (1 == strlen($name)) {
                $optionsString .= ' -';
            } else {
                $optionsString .= ' --';
            }
            
            $optionsString .= $name;
            
            if (true !== $value) {
                $optionsString .= '="' . addslashes($value) . '"';
            }
        }
        
        return $optionsString;
    }
}
