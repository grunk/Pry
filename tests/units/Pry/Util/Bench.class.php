<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace tests\units\Pry\Util;

use atoum;


require '../../../Pry/Util/Bench.class.php';

/**
 * Test class for Bench.class.
 *
 * @author Olivier
 */
class Bench extends atoum {
    
    public function testStart()
    {
        $bench = new \Pry\Util\Bench;
        $start = $bench->start();
        
        $this->variable($start)->isNotNull();
        $this->float($start)->isGreaterThan(0.0);
    }
    
    public function testAdd_flag()
    {
        $bench = new \Pry\Util\Bench;
        $bench->start();
        usleep(500);
        $inter = $bench->add_flag('test');
        
        $this->float($inter)->isGreaterThan(0.0);
    }
    
    public function testStop()
    {
        $bench = new \Pry\Util\Bench;
        $start = $bench->start();
        usleep(500);
        $end = $bench->stop();
        
        $this->float($end)
                ->isGreaterThan(0.0)
                ->isGreaterThan($start);
    }
    
    public function testResult()
    {
        $bench = new \Pry\Util\Bench;
        $bench->start();
        usleep(500);
        $inter = $bench->add_flag('test');
        usleep(500);
        $inter = $bench->add_flag('test2');
        $bench->stop();
        $result = $bench->result();
        
        $this->array($result)
                ->isNotEmpty()
                ->hasKeys(array('test','test2','total'));
        
        $this->float($result['total'])->isGreaterThan(0.0);
    }

}