<?php

/**
 * Pry Framework
 * @copyright 2007-2011 Prynel
 * @author Olivier ROGER <oroger.fr>
 * @category Pry
 * @package Net
 */

namespace Pry\Net\Exception;

/**
 * Exception de socket
 * @category Pry
 * @package Net
 * @subpackage Net_Exception
 * @version 1.0
 * @author Olivier ROGER <oroger.fr>
 * @copyright  2007-2012 Prynel
 *
 */
class Socket extends \Exception
{

    private $context;

    public function __construct($message, $code, $file, $line, $context = null)
    {
        parent::__construct($message, $code);

        $this->file    = $file;
        $this->line    = $line;
        $this->context = $context;
    }

}

?>
