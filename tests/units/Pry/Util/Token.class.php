<?php

/**
 * This file is part of a Prynel's project
 * (c) Olivier Roger <oroger.fr>
 */
namespace tests\units\Pry\Util;

use atoum;
use \Pry\Util\Token as tk;

require '../../../Pry/Util/Token.class.php';
session_start();
/**
 * Description of Token
 *
 * @author Olivier
 */
class Token extends atoum
{
    public function testGenToken()    
    {
        $this->string(tk::genToken(10))->isEqualTo($_SESSION['csrf_protect']['token']);
    }
    
    public function testGetToken()
    {
        $var = tk::genToken(10);
        $this->string(tk::getToken())->isEqualTo($var);
    }
    
    public function testGetTTL()
    {
        tk::genToken(10);
        $ttl = 10 * 60 + time();
        $this->integer($ttl)->isEqualTo(tk::getTTL());
    }
    
    public function testCheckToken()
    {
        $token = tk::genToken();
        $_REQUEST['csrf_protect'] = $token;
        $this->boolean(tk::checkToken())->isTrue;
    }
}