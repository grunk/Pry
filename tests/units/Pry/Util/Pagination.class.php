<?php

/**
 * This file is part of a Prynel's project
 * (c) Olivier Roger <oroger.fr>
 */
namespace tests\units\Pry\Util;

use atoum;


require_once __DIR__. '../../../Pry/Util/Pagination.class.php';
/**
 * Description of Pagination
 *
 * @author Olivier
 */
class Pagination extends atoum
{
    public function testCreate()
    {
        $pager = new \Pry\Util\Pagination(0);
        $var = $pager->create();
        
        $this->boolean($var)->isFalse();
        
        $pager = new \Pry\Util\Pagination(100);
        $var = $pager->create();

        $this->boolean(is_array($var))->isTrue();
        $this->sizeOf($var)->isEqualTo(10);
        $this->boolean(key_exists('encours',$var[1]))->isTrue();
        $this->boolean(key_exists('page',$var[1]))->isTrue();
        $this->integer($var[2]['page'])->isEqualTo(2);
        
    }
    
    public function testCreateParam()
    {
        $pager = new \Pry\Util\Pagination(1000,2);
        $var = $pager->create();
        $this->variable($var[9]['page'])->isEqualTo('...');
        
        $pager = new \Pry\Util\Pagination(1000,2,100,3);
        $var = $pager->create();
        $this->sizeOf($var)->isEqualTo(10);
        $this->boolean($var[3]['encours'])->isTrue();
        $this->boolean($var[1]['encours'])->isFalse();
    }
}
