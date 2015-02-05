<?php
namespace SubversionClient\Command;

interface Command
{
    public function startProcess();
    
    public function assemble();
}
