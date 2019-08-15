<?php

namespace tests\units\Pry\Util;
use atoum;

require_once __DIR__. '../../../Pry/Util/Environment.php';

/**
 * Test class for Environment class.
 *
 * @author Olivier
 */
class Environment extends atoum {
    
    public function testIs64Bits()
    {
        $_64b = \Pry\Util\Environment::is64Bits();
        
        $this->variable($_64b)->isEqualTo(true);
    }

}