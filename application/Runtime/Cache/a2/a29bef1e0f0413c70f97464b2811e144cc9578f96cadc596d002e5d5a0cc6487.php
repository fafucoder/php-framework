<?php

/* index.html */
class __TwigTemplate_0760cf15e1e8aa8965cb90b4e9e478aa0c83755e485b39d0aca5414775748d08 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "{\$name}
";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function getDebugInfo()
    {
        return array (  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "index.html", "/var/www/html/miniphp/Application/Views/layout/index.html");
    }
}
