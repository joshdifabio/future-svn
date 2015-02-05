<?php
namespace SubversionClient\Command;

use FutureProcess\Shell;

abstract class AbstractCommand implements Command
{
    protected $subcommand;
    
    private $assembler;
    private $shell;
    protected $args;
    private $options;
    private $workingDirectory;
    
    public function __construct(Assembler $assembler, Shell $shell)
    {
        $this->assembler = $assembler;
        $this->shell = $shell;
        $this->args = new \ArrayObject;
        $this->options = new \ArrayObject;
        
        if (is_null($this->subcommand)) {
            throw new \LogicException('No subcommand name is set');
        }
    }
    
    public function startProcess(array $overrideOptions = array())
    {
        return $this->shell->startProcess(
            $this->assemble($overrideOptions),
            array(
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w'),
            ),
            $this->workingDirectory
        );
    }
    
    public function assemble(array $overrideOptions = array())
    {
        return $this->assembler->assemble(
            $this->subcommand,
            $this->args->getArrayCopy(),
            array_merge(
                $this->options->getArrayCopy(),
                $overrideOptions
            )
        );
    }
    
    public function getConfigDir()
    {
        return $this->getOption('config-dir');
    }
    
    public function setConfigDir($configDir)
    {
        return $this->setOption('config-dir', $configDir);
    }
    
    public function isAuthCacheEnabled()
    {
        return !$this->getOption('no-auth-cache', false);
    }
    
    public function setAuthCacheEnabled($authCacheEnabled)
    {
        return $this->setOption('no-auth-cache', !$authCacheEnabled);
    }
    
    public function isNonInteractive()
    {
        return $this->getOption('non-interactive', false);
    }
    
    public function setNonInteractive($nonInteractive)
    {
        return $this->setOption('non-interactive', $nonInteractive);
    }
    
    public function getPassword()
    {
        return $this->getOption('password');
    }
    
    public function setPassword($password)
    {
        return $this->setOption('password', $password);
    }
    
    public function isTrustingOfServerCerts()
    {
        return $this->getOption('trust-server-cert', false);
    }
    
    public function setTrustingOfServerCerts($trustingOfServerCerts)
    {
        return $this->setOption('trust-server-cert', $trustingOfServerCerts);
    }
    
    public function getUsername()
    {
        return $this->getOption('username');
    }
    
    public function setUsername($username)
    {
        return $this->setOption('username', $username);
    }
    
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }
    
    public function setWorkingDirectory($workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
        
        return $this;
    }
    
    protected function getArg($index, $default = null)
    {
        return !$this->args->offsetExists($index) ? $default : $this->args[$index];
    }
    
    protected function setArg($index, $value)
    {
        $this->args[$index] = $value;
        
        return $this;
    }
    
    protected function unsetArg($index)
    {
        unset($this->args[$index]);
        
        return $this;
    }
    
    protected function getOption($name, $default = null)
    {
        return !$this->options->offsetExists($name) ? $default : $this->options[$name];
    }
    
    protected function setOption($name, $value)
    {
        $this->options[$name] = $value;
        
        return $this;
    }
    
    protected function unsetOption($name)
    {
        unset($this->options[$name]);
        
        return $this;
    }
}
