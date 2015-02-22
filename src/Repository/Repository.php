<?php
namespace FutureSVN\Repository;

use FutureSVN\Shell\Shell;
use FutureSVN\Shell\FutureOutput;

/**
 * @author Josh Di Fabio <joshdifabio@gmail.com>
 */
class Repository
{
    private $shell;
    private $url;
    private $options;
    
    public function __construct(Shell $shell, $url)
    {
        $this->shell = $shell;
        $this->url = rtrim($url, '/');
        $this->options = new \ArrayObject;
    }
    
    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * @param string $username
     * @return static
     */
    public function setUsername($username)
    {
        $this->options['--username'] = $username;
        
        return $this;
    }
    
    /**
     * @param string $password
     * @return static
     */
    public function setPassword($password)
    {
        $this->options['--password'] = $password;
        
        return $this;
    }
    
    /**
     * @return static
     */
    public function enableAuthCache()
    {
        $this->options['--no-auth-cache'] = false;
        
        return $this;
    }
    
    /**
     * @return static
     */
    public function disableAuthCache()
    {
        $this->options['--no-auth-cache'] = true;
        
        return $this;
    }
    
    /**
     * @return static
     */
    public function trustServerCertificates()
    {
        $this->options['--trust-server-cert'] = true;
        
        return $this;
    }
    
    /**
     * @return static
     */
    public function doNotTrustServerCertificates()
    {
        $this->options['--trust-server-cert'] = false;
        
        return $this;
    }
    
    /**
     * @param string $path OPTIONAL
     * @param int $revision OPTIONAL
     * @return Directory
     */
    public function getDirectory($path = '/', $revision = null)
    {
        return new Directory($this, $path, $revision);
    }
    
    /**
     * @param string $path
     * @param int $revision OPTIONAL
     * @return File
     */
    public function getFile($path, $revision = null)
    {
        return new File($this, $path, $revision);
    }
    
    /**
     * @param string $subcommand
     * @param string|array $args
     * @param array $options
     * @return FutureOutput
     */
    public function execute($subcommand, $args = array(), array $options = array())
    {
        return $this->shell->execute(
            $subcommand,
            $args,
            array_merge($this->options->getArrayCopy(), $options)
        );
    }
}
