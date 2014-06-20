<?php

/**
 * This file is part of a Prynel's project
 * (c) Olivier Roger <oroger@prynel.com>
 */

namespace tests\units\Pry\Util;

use atoum;
use \Pry\Util\Strings as str;

require '../../../Pry/Util/Strings.class.php';

/**
 * Description of Strings
 *
 * @author Olivier
 */
class Strings extends atoum
{
    public function testSlashes()
    {
        $this->string(str::slashes("aujourd'hui"))->isEqualTo("aujourd\'hui");
    }
    
    public function testClean()
    {
        $this->string(str::clean("Une phrase testée ! C'est toujours mieux que 0"))->isEqualTo("une_phrase_testee_c_est_toujours_mieux_que_0");
        $this->string(str::clean("&é\"'(-è_çà)=$*ù!:"))->isEqualTo("e_e_cau");
        $this->string(str::clean("&é\"'(-è_çà)=$*ù!:",'-'))->isEqualTo("e-e-cau");
    }
    
    public function testCut()
    {
        $this->string(str::cut("Une phrase longue avec caractère !mù^éç de test", 8))->isEqualTo("Une...");
        $this->string(str::cut("Une phrase longue avec caractère !mù^éç de test", 11))->isEqualTo("Une phrase...");
        $this->string(str::cut("Une phrase longue avec caractère !mù^éç de test", 18,"[...]"))->isEqualTo("Une phrase longue[...]");
    }
    
    public function testGenerate()
    {
        $this->string(str::generate(10))->hasLength(10);
        $this->string(str::generate(38))->hasLength(38);
    }
    
    public function testCamelize()
    {
        $this->string(str::camelize("Bonjour monsieur"))->isEqualTo("bonjourMonsieur");
    }
    
    public function testGeekize()
    {
        $this->string(str::geekize("Bonjour monsieur"))->isEqualTo('b0nj0ur m0n$i3ur');
    }
    
    public function testHasToMuchCaps()
    {
        $this->boolean(str::hasTooMuchCaps("Bonjour monsieur"))->isFalse();
        $this->boolean(str::hasTooMuchCaps("BONJOUR MONSIeur"))->isTrue();
    }
    
    public function testisUpper()
    {
        $this->boolean(str::isUpper("Bonjour monsieur"))->isFalse();
        $this->boolean(str::isUpper("BONJOUR MONSIEUR"))->isTrue();
    }
    
    public function testisLower()
    {
        $this->boolean(str::isLower("bonjour monsieur"))->isTrue();
        $this->boolean(str::isLower("BONJOur MONSIEUR"))->isFalse();
    }
    
    public function testisIP()
    {
        $this->boolean(str::isIp("192.168.1.1"))->isTrue();
        $this->boolean(str::isIp("256.36.2145.2"))->isFalse();
        $this->boolean(str::isIp("192.156.999.2"))->isFalse();
    }
    
    public function testisMAC()
    {
        $this->boolean(str::isMac("AB-CD-12-99-AF-45"))->isTrue();
        $this->boolean(str::isMac("BONJOur MONSIEUR"))->isFalse();
        $this->boolean(str::isMac("ABCD1299AF45"))->isFalse();
        $this->boolean(str::isMac("AB-CD-1G-99-AZ-45"))->isFalse();
        
        $this->boolean(str::isMac("AB:CD:12:99:AF:45",":"))->isTrue();
        $this->boolean(str::isMac("AB-CD-12-99-AF-45",":"))->isFalse();
    }
    
    public function testisMail()
    {
        $this->boolean(str::isMail("user@domain.com"))->isTrue();
        $this->boolean(str::isMail("user@domain.co.uk"))->isTrue();
        $this->boolean(str::isMail("us-er+name@domain.com"))->isTrue();
        $this->boolean(str::isMail("user@domain",false))->isTrue();
        $this->boolean(str::isMail("user@domain"))->isFalse();
        $this->boolean(str::isMail("BONJOur MONSIEUR"))->isFalse();
        $this->boolean(str::isMail("ABCD1299AF45"))->isFalse();
        $this->boolean(str::isMail("test@"))->isFalse();

    }
    
    public function testisComplex()
    {
        $this->boolean(str::isComplex("Ceci est 1 Chaine Complexe !"))->isTrue();
        $this->boolean(str::isComplex("celle ci est trop simple"))->isFalse();
    }
    
    public function testDate2Mysql()
    {
        $this->string(str::date2Mysql("28/12/2014",'d/m/Y'))->isEqualTo('2014-12-28');
        $this->boolean(str::date2Mysql("14|2014|01",'d|Y|m'))->isFalse();
    }
}
