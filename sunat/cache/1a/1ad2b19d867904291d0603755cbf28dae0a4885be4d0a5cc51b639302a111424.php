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

/* summary.html.twig */
class __TwigTemplate_b0a0b4c56c96f4f52991d931502fcfa9ced492301abc47ca7db73ea2ff146fcf extends Template
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
        $this->loadTemplate("assets/style.css", "summary.html.twig", 5)->display($context);
        // line 6
        echo "        .nmarg{margin: 0}
    </style>
</head>
<body class=\"white-bg\">
";
        // line 10
        $context["cp"] = twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 10, $this->source); })()), "company", [], "any", false, false, false, 10);
        // line 11
        $context["fecGen"] = twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 11, $this->source); })()), "fecGeneracion", [], "any", false, false, false, 11), "d/m/Y");
        // line 12
        echo "<table width=\"100%\">
    <tbody><tr>
        <td style=\"padding:30px; !important\">
            <table width=\"100%\" height=\"200px\" border=\"0\" aling=\"center\" cellpadding=\"0\" cellspacing=\"0\">
                <tbody><tr>
                    <td width=\"50%\" height=\"90\" align=\"center\">
                        <span><img src=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\ImageFilter')->toBase64(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 18, $this->source); })()), "system", [], "any", false, false, false, 18), "logo", [], "any", false, false, false, 18)), "html", null, true);
        echo "\" height=\"80\" style=\"text-align:center\" border=\"0\"></span>
                    </td>
                    <td width=\"5%\" height=\"40\" align=\"center\"></td>
                    <td width=\"45%\" rowspan=\"2\" valign=\"bottom\" style=\"padding-left:0\">
                        <div class=\"tabla_borde\">
                            <table width=\"100%\" border=\"0\" height=\"200\" cellpadding=\"6\" cellspacing=\"0\">
                                <tbody><tr>
                                    <td align=\"center\">
                                        <span style=\"font-family:Tahoma, Geneva, sans-serif; font-size:29px\" text-align=\"center\">RESUMEN DIARIO DE</span>
                                        <br>
                                        <span style=\"font-family:Tahoma, Geneva, sans-serif; font-size:19px\" text-align=\"center\">BOLETAS DE VENTA</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"center\">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"center\">
                                        <span style=\"font-size:15px\" text-align=\"center\">R.U.C.: ";
        // line 38
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["cp"]) || array_key_exists("cp", $context) ? $context["cp"] : (function () { throw new RuntimeError('Variable "cp" does not exist.', 38, $this->source); })()), "ruc", [], "any", false, false, false, 38), "html", null, true);
        echo "</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"center\">
                                        No.: <span>";
        // line 43
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 43, $this->source); })()), "correlativo", [], "any", false, false, false, 43), "html", null, true);
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
        // line 56
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["cp"]) || array_key_exists("cp", $context) ? $context["cp"] : (function () { throw new RuntimeError('Variable "cp" does not exist.', 56, $this->source); })()), "razonSocial", [], "any", false, false, false, 56), "html", null, true);
        echo "</span></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"left\">
                                        <strong>Dirección: </strong>";
        // line 61
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["cp"]) || array_key_exists("cp", $context) ? $context["cp"] : (function () { throw new RuntimeError('Variable "cp" does not exist.', 61, $this->source); })()), "address", [], "any", false, false, false, 61), "direccion", [], "any", false, false, false, 61), "html", null, true);
        echo "
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"left\">
                                        ";
        // line 66
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 66, $this->source); })()), "user", [], "any", false, false, false, 66), "header", [], "any", false, false, false, 66);
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
                        <td width=\"60%\" height=\"15\" align=\"left\"><strong>Fecha de Emisión del Resumen:</strong>  ";
        // line 77
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 77, $this->source); })()), "fecResumen", [], "any", false, false, false, 77), "d/m/Y"), "html", null, true);
        echo "</td>
                        <td width=\"40%\" height=\"15\" align=\"left\"><strong>Fecha de Generación:</strong>  ";
        // line 78
        echo twig_escape_filter($this->env, (isset($context["fecGen"]) || array_key_exists("fecGen", $context) ? $context["fecGen"] : (function () { throw new RuntimeError('Variable "fecGen" does not exist.', 78, $this->source); })()), "html", null, true);
        echo "</td>
                    </tr>
                    <tr>
                        <td width=\"60%\" align=\"left\"><strong>Moneda: </strong>  ";
        // line 81
        echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog("PEN", "021"), "html", null, true);
        echo " </td>
                        <td width=\"40%\" align=\"left\"></td>
                    </tr>
                    </tbody></table>
            </div><br>
            <div class=\"tabla_borde\">
                <table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
                    <tbody>
                    <tr>
                        <td align=\"center\" class=\"bold\">Documento</td>
                        <td align=\"center\" class=\"bold\">Condición</td>
                        <td align=\"center\" class=\"bold\">Impuestos</td>
                        <td align=\"center\" class=\"bold\">Totales</td>
                        <td align=\"center\" class=\"bold\">Imp. Total</td>
                    </tr>
                    ";
        // line 96
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 96, $this->source); })()), "details", [], "any", false, false, false, 96));
        foreach ($context['_seq'] as $context["_key"] => $context["det"]) {
            // line 97
            echo "                        <tr class=\"border_top\">
                            <td>
                                <p class=\"nmarg\"><strong>";
            // line 99
            echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog(twig_get_attribute($this->env, $this->source, $context["det"], "tipoDoc", [], "any", false, false, false, 99), "01"), "html", null, true);
            echo "</strong>  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "serieNro", [], "any", false, false, false, 99), "html", null, true);
            echo "</p>
                                ";
            // line 100
            if (twig_get_attribute($this->env, $this->source, $context["det"], "docReferencia", [], "any", false, false, false, 100)) {
                // line 101
                echo "                                ";
                $context["ref"] = twig_get_attribute($this->env, $this->source, $context["det"], "docReferencia", [], "any", false, false, false, 101);
                // line 102
                echo "                                <p class=\"nmarg\"><strong>DOC. REF.</strong>  ";
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog(twig_get_attribute($this->env, $this->source, (isset($context["ref"]) || array_key_exists("ref", $context) ? $context["ref"] : (function () { throw new RuntimeError('Variable "ref" does not exist.', 102, $this->source); })()), "tipoDoc", [], "any", false, false, false, 102), "01"), "html", null, true);
                echo "  ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["ref"]) || array_key_exists("ref", $context) ? $context["ref"] : (function () { throw new RuntimeError('Variable "ref" does not exist.', 102, $this->source); })()), "nroDoc", [], "any", false, false, false, 102), "html", null, true);
                echo "</p>
                                ";
            }
            // line 104
            echo "                            </td>
                            <td align=\"center\">";
            // line 105
            echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog(twig_get_attribute($this->env, $this->source, $context["det"], "estado", [], "any", false, false, false, 105), "19"), "html", null, true);
            echo "</td>
                            <td>
                                <p class=\"nmarg\"><strong>IGV</strong>  ";
            // line 107
            echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "mtoIGV", [], "any", false, false, false, 107)), "html", null, true);
            echo "</p>
                                ";
            // line 108
            if (twig_get_attribute($this->env, $this->source, $context["det"], "mtoISC", [], "any", false, false, false, 108)) {
                // line 109
                echo "                                <p class=\"nmarg\"><strong>ISC</strong>  ";
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "mtoISC", [], "any", false, false, false, 109)), "html", null, true);
                echo "</p>
                                ";
            }
            // line 111
            echo "                                ";
            if (twig_get_attribute($this->env, $this->source, $context["det"], "mtoIcbper", [], "any", false, false, false, 111)) {
                // line 112
                echo "                                <p class=\"nmarg\"><strong>ICBPER</strong>  ";
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "mtoIcbper", [], "any", false, false, false, 112)), "html", null, true);
                echo "</p>
                                ";
            }
            // line 114
            echo "                                ";
            if (twig_get_attribute($this->env, $this->source, $context["det"], "mtoOtrosTributos", [], "any", false, false, false, 114)) {
                // line 115
                echo "                                <p class=\"nmarg\"><strong>Otros Tributos</strong>  ";
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "mtoOtrosTributos", [], "any", false, false, false, 115)), "html", null, true);
                echo "</p>
                                ";
            }
            // line 117
            echo "                                ";
            if (twig_get_attribute($this->env, $this->source, $context["det"], "mtoOtrosCargos", [], "any", false, false, false, 117)) {
                // line 118
                echo "                                <p class=\"nmarg\"><strong>Otros Cargos</strong>  ";
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "mtoOtrosCargos", [], "any", false, false, false, 118)), "html", null, true);
                echo "</p>
                                ";
            }
            // line 120
            echo "                            </td>
                            <td>
                                ";
            // line 122
            if (twig_get_attribute($this->env, $this->source, $context["det"], "mtoOperGravadas", [], "any", false, false, false, 122)) {
                // line 123
                echo "                                <p class=\"nmarg\"><strong>Gravadas</strong>  ";
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "mtoOperGravadas", [], "any", false, false, false, 123)), "html", null, true);
                echo "</p>
                                ";
            }
            // line 125
            echo "                                ";
            if (twig_get_attribute($this->env, $this->source, $context["det"], "mtoOperInafectas", [], "any", false, false, false, 125)) {
                // line 126
                echo "                                <p class=\"nmarg\"><strong>Inafectas</strong>  ";
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "mtoOperInafectas", [], "any", false, false, false, 126)), "html", null, true);
                echo "</p>
                                ";
            }
            // line 128
            echo "                                ";
            if (twig_get_attribute($this->env, $this->source, $context["det"], "mtoOperExoneradas", [], "any", false, false, false, 128)) {
                // line 129
                echo "                                <p class=\"nmarg\"><strong>Exoneradas</strong>  ";
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "mtoOperExoneradas", [], "any", false, false, false, 129)), "html", null, true);
                echo "</p>
                                ";
            }
            // line 131
            echo "                                ";
            if (twig_get_attribute($this->env, $this->source, $context["det"], "mtoOperGratuitas", [], "any", false, false, false, 131)) {
                // line 132
                echo "                                <p class=\"nmarg\"><strong>Gratuitas</strong>  ";
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "mtoOperGratuitas", [], "any", false, false, false, 132)), "html", null, true);
                echo "</p>
                                ";
            }
            // line 134
            echo "                            </td>
                            <td align=\"center\">";
            // line 135
            echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "total", [], "any", false, false, false, 135)), "html", null, true);
            echo "
                                ";
            // line 136
            if ((twig_get_attribute($this->env, $this->source, $context["det"], "percepcion", [], "any", false, false, false, 136) && twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["det"], "percepcion", [], "any", false, false, false, 136), "mto", [], "any", false, false, false, 136))) {
                // line 137
                echo "                                    ";
                $context["perc"] = twig_get_attribute($this->env, $this->source, $context["det"], "percepcion", [], "any", false, false, false, 137);
                echo "<br>
                                    <p class=\"nmarg\"><strong>Percepción</strong>  ";
                // line 138
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, (isset($context["perc"]) || array_key_exists("perc", $context) ? $context["perc"] : (function () { throw new RuntimeError('Variable "perc" does not exist.', 138, $this->source); })()), "mto", [], "any", false, false, false, 138)), "html", null, true);
                echo "</p>
                                    <p class=\"nmarg\"><strong>Total Pagar</strong>  ";
                // line 139
                echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, (isset($context["perc"]) || array_key_exists("perc", $context) ? $context["perc"] : (function () { throw new RuntimeError('Variable "perc" does not exist.', 139, $this->source); })()), "mtoTotal", [], "any", false, false, false, 139)), "html", null, true);
                echo "</p>
                                ";
            }
            // line 141
            echo "                            </td>
                        </tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['det'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 144
        echo "                    </tbody>
                </table></div>
            <br>
            ";
        // line 147
        if ((array_key_exists("max_items", $context) && (1 === twig_compare(twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 147, $this->source); })()), "details", [], "any", false, false, false, 147)), (isset($context["max_items"]) || array_key_exists("max_items", $context) ? $context["max_items"] : (function () { throw new RuntimeError('Variable "max_items" does not exist.', 147, $this->source); })()))))) {
            // line 148
            echo "                <div style=\"page-break-after:always;\"></div>
            ";
        }
        // line 150
        echo "            ";
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["params"] ?? null), "system", [], "any", false, true, false, 150), "hash", [], "any", true, true, false, 150) && twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 150, $this->source); })()), "system", [], "any", false, false, false, 150), "hash", [], "any", false, false, false, 150))) {
            // line 151
            echo "                <div>
                    <blockquote>
                        <strong>Resumen:</strong>   ";
            // line 153
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 153, $this->source); })()), "system", [], "any", false, false, false, 153), "hash", [], "any", false, false, false, 153), "html", null, true);
            echo "
                    </blockquote>
                </div>
            ";
        }
        // line 157
        echo "        </td>
    </tr>
    </tbody></table>
</body></html>";
    }

    public function getTemplateName()
    {
        return "summary.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  337 => 157,  330 => 153,  326 => 151,  323 => 150,  319 => 148,  317 => 147,  312 => 144,  304 => 141,  299 => 139,  295 => 138,  290 => 137,  288 => 136,  284 => 135,  281 => 134,  275 => 132,  272 => 131,  266 => 129,  263 => 128,  257 => 126,  254 => 125,  248 => 123,  246 => 122,  242 => 120,  236 => 118,  233 => 117,  227 => 115,  224 => 114,  218 => 112,  215 => 111,  209 => 109,  207 => 108,  203 => 107,  198 => 105,  195 => 104,  187 => 102,  184 => 101,  182 => 100,  176 => 99,  172 => 97,  168 => 96,  150 => 81,  144 => 78,  140 => 77,  126 => 66,  118 => 61,  110 => 56,  94 => 43,  86 => 38,  63 => 18,  55 => 12,  53 => 11,  51 => 10,  45 => 6,  43 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "summary.html.twig", "/var/www/html/sunat/vendor/greenter/report/src/Report/Templates/summary.html.twig");
    }
}
