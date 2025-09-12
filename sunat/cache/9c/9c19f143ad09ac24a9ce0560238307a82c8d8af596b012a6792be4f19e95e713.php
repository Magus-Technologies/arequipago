<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* voided.html.twig */
class __TwigTemplate_faf007c9870b0cd66cac0d6e7f38d5e937457bc9b3497fb55f5f6d80d5a15a99 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<html>
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
    <style type=\"text/css\">
        ";
        // line 5
        $this->loadTemplate("assets/style.css", "voided.html.twig", 5)->display($context);
        // line 6
        echo "    </style>
</head>
<body class=\"white-bg\">
";
        // line 9
        $context["cp"] = twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 9, $this->source); })()), "company", [], "any", false, false, false, 9);
        // line 10
        $context["fecGen"] = twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 10, $this->source); })()), "fecGeneracion", [], "any", false, false, false, 10), "d/m/Y");
        // line 11
        echo "<table width=\"100%\">
    <tbody><tr>
        <td style=\"padding:30px; !important\">
            <table width=\"100%\" height=\"200px\" border=\"0\" aling=\"center\" cellpadding=\"0\" cellspacing=\"0\">
                <tbody><tr>
                    <td width=\"50%\" height=\"90\" align=\"center\">
                        <span><img src=\"";
        // line 17
        echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\ImageFilter')->toBase64(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 17, $this->source); })()), "system", [], "any", false, false, false, 17), "logo", [], "any", false, false, false, 17)), "html", null, true);
        echo "\" height=\"80\" style=\"text-align:center\" border=\"0\"></span>
                    </td>
                    <td width=\"5%\" height=\"40\" align=\"center\"></td>
                    <td width=\"45%\" rowspan=\"2\" valign=\"bottom\" style=\"padding-left:0\">
                        <div class=\"tabla_borde\">
                            <table width=\"100%\" border=\"0\" height=\"200\" cellpadding=\"6\" cellspacing=\"0\">
                                <tbody><tr><td align=\"center\">
                                    ";
        // line 24
        if (twig_in_filter("RA", twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 24, $this->source); })()), "name", [], "any", false, false, false, 24))) {
            // line 25
            echo "                                        <span style=\"font-family:Tahoma, Geneva, sans-serif; font-size:29px\" text-align=\"center\">COMUNICACIÓN</span>
                                        <br>
                                        <span style=\"font-family:Tahoma, Geneva, sans-serif; font-size:19px\" text-align=\"center\">D E&nbsp;&nbsp;&nbsp;B A J A S</span>
                                    ";
        } else {
            // line 29
            echo "                                        <span style=\"font-family:Tahoma, Geneva, sans-serif; font-size:29px\" text-align=\"center\">RESUMEN DIARIO DE</span>
                                        <br>
                                        <span style=\"font-family:Tahoma, Geneva, sans-serif; font-size:19px\" text-align=\"center\">REVERSIONES</span>
                                    ";
        }
        // line 33
        echo "                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"center\">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"center\">
                                        <span style=\"font-size:15px\" text-align=\"center\">R.U.C.: ";
        // line 42
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["cp"]) || array_key_exists("cp", $context) ? $context["cp"] : (function () { throw new RuntimeError('Variable "cp" does not exist.', 42, $this->source); })()), "ruc", [], "any", false, false, false, 42), "html", null, true);
        echo "</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"center\">
                                        No.: <span>";
        // line 47
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 47, $this->source); })()), "correlativo", [], "any", false, false, false, 47), "html", null, true);
        echo "</span>
                                    </td>
                                </tr>
                                </tbody></table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td valign=\"bottom\" style=\"padding-left:0\">
                        <div class=\"tabla_borde\">
                            <table width=\"96%\" height=\"100%\" border=\"0\" border-radius=\"\" cellpadding=\"9\" cellspacing=\"0\">
                                <tbody><tr>
                                    <td align=\"center\">
                                        <strong><span style=\"font-size:15px\">";
        // line 60
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["cp"]) || array_key_exists("cp", $context) ? $context["cp"] : (function () { throw new RuntimeError('Variable "cp" does not exist.', 60, $this->source); })()), "razonSocial", [], "any", false, false, false, 60), "html", null, true);
        echo "</span></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"left\">
                                        <strong>Dirección: </strong>";
        // line 65
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["cp"]) || array_key_exists("cp", $context) ? $context["cp"] : (function () { throw new RuntimeError('Variable "cp" does not exist.', 65, $this->source); })()), "address", [], "any", false, false, false, 65), "direccion", [], "any", false, false, false, 65), "html", null, true);
        echo "
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"left\">
                                        ";
        // line 70
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 70, $this->source); })()), "user", [], "any", false, false, false, 70), "header", [], "any", false, false, false, 70);
        echo "
                                    </td>
                                </tr>
                                </tbody></table>
                        </div>
                    </td>
                </tr>
                </tbody></table>
            <div class=\"tabla_borde\">
                <table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
                    <tbody><tr>
                        <td width=\"60%\" height=\"15\" align=\"left\"><strong>Fecha de Comunicación:</strong>  ";
        // line 81
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 81, $this->source); })()), "fecComunicacion", [], "any", false, false, false, 81), "d/m/Y"), "html", null, true);
        echo "</td>
                        <td width=\"40%\" height=\"15\" align=\"left\"><strong>Fecha de Generación:</strong>  ";
        // line 82
        echo twig_escape_filter($this->env, (isset($context["fecGen"]) || array_key_exists("fecGen", $context) ? $context["fecGen"] : (function () { throw new RuntimeError('Variable "fecGen" does not exist.', 82, $this->source); })()), "html", null, true);
        echo "</td>
                    </tr>
                    </tbody></table>
            </div><br>
            <div class=\"tabla_borde\">
                <table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
                    <tbody>
                    <tr>
                        <td align=\"center\" class=\"bold\">Fecha</td>
                        <td align=\"center\" class=\"bold\">Tipo de Documento</td>
                        <td align=\"center\" class=\"bold\">Nro. de Documento</td>
                        <td align=\"center\" class=\"bold\">Motivo</td>
                    </tr>
                    ";
        // line 95
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 95, $this->source); })()), "details", [], "any", false, false, false, 95));
        foreach ($context['_seq'] as $context["_key"] => $context["det"]) {
            // line 96
            echo "                        <tr class=\"border_top\">
                            <td align=\"center\">";
            // line 97
            echo twig_escape_filter($this->env, (isset($context["fecGen"]) || array_key_exists("fecGen", $context) ? $context["fecGen"] : (function () { throw new RuntimeError('Variable "fecGen" does not exist.', 97, $this->source); })()), "html", null, true);
            echo "</td>
                            <td align=\"center\">";
            // line 98
            echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog(twig_get_attribute($this->env, $this->source, $context["det"], "tipoDoc", [], "any", false, false, false, 98), "01"), "html", null, true);
            echo "</td>
                            <td align=\"center\">";
            // line 99
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "serie", [], "any", false, false, false, 99), "html", null, true);
            echo "-";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "correlativo", [], "any", false, false, false, 99), "html", null, true);
            echo "</td>
                            <td align=\"center\" width=\"300px\">";
            // line 100
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "desMotivoBaja", [], "any", false, false, false, 100), "html", null, true);
            echo "</td>
                        </tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['det'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 103
        echo "                    </tbody>
                </table></div>
            <br>
            ";
        // line 106
        if ((array_key_exists("max_items", $context) && (1 === twig_compare(twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 106, $this->source); })()), "details", [], "any", false, false, false, 106)), (isset($context["max_items"]) || array_key_exists("max_items", $context) ? $context["max_items"] : (function () { throw new RuntimeError('Variable "max_items" does not exist.', 106, $this->source); })()))))) {
            // line 107
            echo "                <div style=\"page-break-after:always;\"></div>
            ";
        }
        // line 109
        echo "            ";
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["params"] ?? null), "system", [], "any", false, true, false, 109), "hash", [], "any", true, true, false, 109) && twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 109, $this->source); })()), "system", [], "any", false, false, false, 109), "hash", [], "any", false, false, false, 109))) {
            // line 110
            echo "            <div>
                <blockquote>
                    <strong>Resumen:</strong>   ";
            // line 112
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 112, $this->source); })()), "system", [], "any", false, false, false, 112), "hash", [], "any", false, false, false, 112), "html", null, true);
            echo "
                </blockquote>
            </div>
            ";
        }
        // line 116
        echo "        </td>
    </tr>
    </tbody></table>
</body></html>";
    }

    public function getTemplateName()
    {
        return "voided.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  226 => 116,  219 => 112,  215 => 110,  212 => 109,  208 => 107,  206 => 106,  201 => 103,  192 => 100,  186 => 99,  182 => 98,  178 => 97,  175 => 96,  171 => 95,  155 => 82,  151 => 81,  137 => 70,  129 => 65,  121 => 60,  105 => 47,  97 => 42,  86 => 33,  80 => 29,  74 => 25,  72 => 24,  62 => 17,  54 => 11,  52 => 10,  50 => 9,  45 => 6,  43 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "voided.html.twig", "/var/www/html/sunat/vendor/greenter/report/src/Report/Templates/voided.html.twig");
    }
}
