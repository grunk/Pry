<?php

namespace tests\units\Pry\Util;

use atoum;

require_once __DIR__. '../../../Pry/Util/CommandLineBuilder.php';

/**
 * Test class for CommandLineBuilder.class.
 *
 * @author Olivier
 */
class CommandLineBuilder extends atoum
{

    public function testSetCommand()
    {
        $cb = new \Pry\Util\CommandLineBuilder();
        $cb->setCommand('svn');
        $str = $cb->get();
        
        $this->string($str)->isEqualTo('svn');
    }
    
    public function testSetOptionChar()
    {
        $cb = new \Pry\Util\CommandLineBuilder();
        $cb->setOptionChar('=');
        $cb->addOption('test');
        $str = $cb->get();
        
        $this->string($str)->isEqualTo(' =test');
    }
    
    public function testSetLongOptionChar()
    {
        $cb = new \Pry\Util\CommandLineBuilder();
        $cb->setLongOptionChar('==');
        $cb->addLongOption('test');
        $str = $cb->get();
        
        $this->string($str)->isEqualTo(' ==test');
    }
    
    public function testAddParameter()
    {
        $cb = new \Pry\Util\CommandLineBuilder();
        $cb->setCommand('svn');
        $cb->addParameter('log');
        $str = $cb->get();
        
        $this->string($str)->isEqualTo('svn log');
    }
    
    public function testAddOption()
    {
        $cb = new \Pry\Util\CommandLineBuilder();
        $cb->setCommand('svn');
        $cb->addOption('test');
        $str = $cb->get();
        $this->string($str)->isEqualTo('svn -test');
        $cb->addOption('test2','haha');
        $str = $cb->get();
        
        $this->string($str)->isEqualTo('svn -test -test2 haha');
    }
    
    public function testAddLongOption()
    {
        $cb = new \Pry\Util\CommandLineBuilder();
        $cb->setCommand('svn');
        $cb->addLongOption('test','tester');
        $str = $cb->get();
        $this->string($str)->isEqualTo('svn --test=tester');
        $cb->addLongOption('test2','haha');
        $str = $cb->get();
        
        $this->string($str)->isEqualTo('svn --test=tester --test2=haha');
    }
    
    public function testClear()
    {
        $cb = new \Pry\Util\CommandLineBuilder();
        $cb->setCommand('svn');
        $cb->addParameter('log');
        $cb->addOption('test');
        $cb->addLongOption('user','toto');
        $cb->clear();
        $str = $cb->get();
        
        $this->string($str)->isEmpty('');
    }
    
    public function testGet()
    {
        $cb = new \Pry\Util\CommandLineBuilder();
        $cb->setCommand('svn');
        $cb->addParameter('log');
        $cb->addParameter('http://192.168.1.1/test/trunk');
        $cb->addOption('test');
        $cb->addOption('test2','data');
        $cb->addLongOption('username','oroger');
        
        $this->string($cb->get())->isEqualTo('svn log http://192.168.1.1/test/trunk -test -test2 data --username=oroger');
    }
}