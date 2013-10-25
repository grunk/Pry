<?php

/**
 * Pry Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * 
 */

namespace Pry\Form\Element;

use Pry\Form\Field;

/**
 *
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>     
 *
 */
class Html extends Field
{

    private $html;

    public function __construct($html, $form)
    {
        $this->html = $html;
    }

    public function __toString()
    {
        $html = $this->html;
        return $html;
    }

}