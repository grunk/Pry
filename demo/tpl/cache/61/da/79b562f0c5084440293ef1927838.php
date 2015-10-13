<?php

/* test.html */
class __TwigTemplate_61da79b562f0c5084440293ef1927838 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>
        <link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" media=\"screen\"/>
        <script type=\"text/javascript\" src=\"script.js\"></script>
        <title>TODO supply a title</title>
    </head>
    <body>
        <p>
            TODO write content ";
        // line 11
        echo twig_escape_filter($this->env, (isset($context["hello"]) ? $context["hello"] : null), "html", null, true);
        echo "
\t\t\t
        </p>
    </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "test.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 11,  19 => 1,);
    }
}
