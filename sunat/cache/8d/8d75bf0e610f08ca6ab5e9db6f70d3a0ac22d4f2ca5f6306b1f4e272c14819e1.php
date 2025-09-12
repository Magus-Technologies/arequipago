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

/* perception.xml.twig */
class __TwigTemplate_d5e4da265b198d3a307bade06a8e94f4694794963f7b3b4f6a14df938708bb07 extends Template
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
        ob_start(function () { return ''; });
        // line 2
        echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<Perception xmlns=\"urn:sunat:names:specification:ubl:peru:schema:xsd:Perception-1\" xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\" xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\" xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\" xmlns:ext=\"urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2\" xmlns:sac=\"urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1\">
\t<ext:UBLExtensions>
\t\t<ext:UBLExtension>
\t\t\t<ext:ExtensionContent/>
\t\t</ext:UBLExtension>
\t</ext:UBLExtensions>
\t<cbc:UBLVersionID>2.0</cbc:UBLVersionID>
\t<cbc:CustomizationID>1.0</cbc:CustomizationID>
    ";
        // line 11
        $context["emp"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "company", [], "any", false, false, false, 11);
        // line 12
        echo "\t<cac:Signature>
\t\t<cbc:ID>";
        // line 13
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 13);
        echo "</cbc:ID>
\t\t<cac:SignatoryParty>
\t\t\t<cac:PartyIdentification>
\t\t\t\t<cbc:ID>";
        // line 16
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 16);
        echo "</cbc:ID>
\t\t\t</cac:PartyIdentification>
\t\t\t<cac:PartyName>
\t\t\t\t<cbc:Name><![CDATA[";
        // line 19
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 19);
        echo "]]></cbc:Name>
\t\t\t</cac:PartyName>
\t\t</cac:SignatoryParty>
\t\t<cac:DigitalSignatureAttachment>
\t\t\t<cac:ExternalReference>
\t\t\t\t<cbc:URI>#GREENTER-SIGN</cbc:URI>
\t\t\t</cac:ExternalReference>
\t\t</cac:DigitalSignatureAttachment>
\t</cac:Signature>
\t<cbc:ID>";
        // line 28
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "serie", [], "any", false, false, false, 28);
        echo "-";
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "correlativo", [], "any", false, false, false, 28);
        echo "</cbc:ID>
\t<cbc:IssueDate>";
        // line 29
        echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fechaEmision", [], "any", false, false, false, 29), "Y-m-d");
        echo "</cbc:IssueDate>
\t<cbc:IssueTime>";
        // line 30
        echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fechaEmision", [], "any", false, false, false, 30), "H:i:s");
        echo "</cbc:IssueTime>
\t<cac:AgentParty>
\t\t<cac:PartyIdentification>
\t\t\t<cbc:ID schemeID=\"6\">";
        // line 33
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 33);
        echo "</cbc:ID>
\t\t</cac:PartyIdentification>
\t\t<cac:PartyName>
\t\t\t<cbc:Name><![CDATA[";
        // line 36
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "nombreComercial", [], "any", false, false, false, 36);
        echo "]]></cbc:Name>
\t\t</cac:PartyName>
        ";
        // line 38
        $context["addr"] = twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "address", [], "any", false, false, false, 38);
        // line 39
        echo "\t\t<cac:PostalAddress>
\t\t\t<cbc:ID>";
        // line 40
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 40);
        echo "</cbc:ID>
\t\t\t<cbc:StreetName><![CDATA[";
        // line 41
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "direccion", [], "any", false, false, false, 41);
        echo "]]></cbc:StreetName>
\t\t\t<cbc:CityName>";
        // line 42
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "departamento", [], "any", false, false, false, 42);
        echo "</cbc:CityName>
\t\t\t<cbc:CountrySubentity>";
        // line 43
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "provincia", [], "any", false, false, false, 43);
        echo "</cbc:CountrySubentity>
\t\t\t<cbc:District>";
        // line 44
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "distrito", [], "any", false, false, false, 44);
        echo "</cbc:District>
\t\t\t<cac:Country>
\t\t\t\t<cbc:IdentificationCode>";
        // line 46
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "codigoPais", [], "any", false, false, false, 46);
        echo "</cbc:IdentificationCode>
\t\t\t</cac:Country>
\t\t</cac:PostalAddress>
\t\t<cac:PartyLegalEntity>
\t\t\t<cbc:RegistrationName><![CDATA[";
        // line 50
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 50);
        echo "]]></cbc:RegistrationName>
\t\t</cac:PartyLegalEntity>
\t</cac:AgentParty>
\t<cac:ReceiverParty>
\t\t<cac:PartyIdentification>
\t\t\t<cbc:ID schemeID=\"";
        // line 55
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "proveedor", [], "any", false, false, false, 55), "tipoDoc", [], "any", false, false, false, 55);
        echo "\">";
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "proveedor", [], "any", false, false, false, 55), "numDoc", [], "any", false, false, false, 55);
        echo "</cbc:ID>
\t\t</cac:PartyIdentification>
\t\t<cac:PartyLegalEntity>
\t\t\t<cbc:RegistrationName><![CDATA[";
        // line 58
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "proveedor", [], "any", false, false, false, 58), "rznSocial", [], "any", false, false, false, 58);
        echo "]]></cbc:RegistrationName>
\t\t</cac:PartyLegalEntity>
\t</cac:ReceiverParty>
\t<sac:SUNATPerceptionSystemCode>";
        // line 61
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "regimen", [], "any", false, false, false, 61);
        echo "</sac:SUNATPerceptionSystemCode>
\t<sac:SUNATPerceptionPercent>";
        // line 62
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tasa", [], "any", false, false, false, 62)]);
        echo "</sac:SUNATPerceptionPercent>
    ";
        // line 63
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "observacion", [], "any", false, false, false, 63)) {
            // line 64
            echo "\t<cbc:Note><![CDATA[";
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "observacion", [], "any", false, false, false, 64);
            echo "]]></cbc:Note>
\t";
        }
        // line 66
        echo "\t<cbc:TotalInvoiceAmount currencyID=\"PEN\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "impPercibido", [], "any", false, false, false, 66)]);
        echo "</cbc:TotalInvoiceAmount>
\t<sac:SUNATTotalCashed currencyID=\"PEN\">";
        // line 67
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "impCobrado", [], "any", false, false, false, 67)]);
        echo "</sac:SUNATTotalCashed>
\t";
        // line 68
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "details", [], "any", false, false, false, 68));
        foreach ($context['_seq'] as $context["_key"] => $context["det"]) {
            // line 69
            echo "\t<sac:SUNATPerceptionDocumentReference>
\t\t<cbc:ID schemeID=\"";
            // line 70
            echo twig_get_attribute($this->env, $this->source, $context["det"], "tipoDoc", [], "any", false, false, false, 70);
            echo "\">";
            echo twig_get_attribute($this->env, $this->source, $context["det"], "numDoc", [], "any", false, false, false, 70);
            echo "</cbc:ID>
\t\t<cbc:IssueDate>";
            // line 71
            echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "fechaEmision", [], "any", false, false, false, 71), "Y-m-d");
            echo "</cbc:IssueDate>
\t\t<cbc:TotalInvoiceAmount currencyID=\"";
            // line 72
            echo twig_get_attribute($this->env, $this->source, $context["det"], "moneda", [], "any", false, false, false, 72);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["det"], "impTotal", [], "any", false, false, false, 72)]);
            echo "</cbc:TotalInvoiceAmount>
\t\t";
            // line 73
            if (twig_get_attribute($this->env, $this->source, $context["det"], "cobros", [], "any", false, false, false, 73)) {
                // line 74
                echo "        ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["det"], "cobros", [], "any", false, false, false, 74));
                $context['loop'] = [
                  'parent' => $context['_parent'],
                  'index0' => 0,
                  'index'  => 1,
                  'first'  => true,
                ];
                if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                    $length = count($context['_seq']);
                    $context['loop']['revindex0'] = $length - 1;
                    $context['loop']['revindex'] = $length;
                    $context['loop']['length'] = $length;
                    $context['loop']['last'] = 1 === $length;
                }
                foreach ($context['_seq'] as $context["_key"] => $context["cob"]) {
                    // line 75
                    echo "\t\t<cac:Payment>
\t\t\t<cbc:ID>";
                    // line 76
                    echo twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, false, 76);
                    echo "</cbc:ID>
\t\t\t<cbc:PaidAmount currencyID=\"";
                    // line 77
                    echo twig_get_attribute($this->env, $this->source, $context["cob"], "moneda", [], "any", false, false, false, 77);
                    echo "\">";
                    echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["cob"], "importe", [], "any", false, false, false, 77)]);
                    echo "</cbc:PaidAmount>
\t\t\t<cbc:PaidDate>";
                    // line 78
                    echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["cob"], "fecha", [], "any", false, false, false, 78), "Y-m-d");
                    echo "</cbc:PaidDate>
\t\t</cac:Payment>
        ";
                    ++$context['loop']['index0'];
                    ++$context['loop']['index'];
                    $context['loop']['first'] = false;
                    if (isset($context['loop']['length'])) {
                        --$context['loop']['revindex0'];
                        --$context['loop']['revindex'];
                        $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cob'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 81
                echo "\t\t";
            }
            // line 82
            echo "\t\t";
            if (((twig_get_attribute($this->env, $this->source, $context["det"], "impPercibido", [], "any", false, false, false, 82) && twig_get_attribute($this->env, $this->source, $context["det"], "impCobrar", [], "any", false, false, false, 82)) && twig_get_attribute($this->env, $this->source, $context["det"], "fechaPercepcion", [], "any", false, false, false, 82))) {
                // line 83
                echo "\t\t<sac:SUNATPerceptionInformation>
\t\t\t<sac:SUNATPerceptionAmount currencyID=\"PEN\">";
                // line 84
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["det"], "impPercibido", [], "any", false, false, false, 84)]);
                echo "</sac:SUNATPerceptionAmount>
\t\t\t<sac:SUNATPerceptionDate>";
                // line 85
                echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "fechaPercepcion", [], "any", false, false, false, 85), "Y-m-d");
                echo "</sac:SUNATPerceptionDate>
\t\t\t<sac:SUNATNetTotalCashed currencyID=\"PEN\">";
                // line 86
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["det"], "impCobrar", [], "any", false, false, false, 86)]);
                echo "</sac:SUNATNetTotalCashed>
            ";
                // line 87
                if (twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 87)) {
                    // line 88
                    echo "\t\t\t<cac:ExchangeRate>
\t\t\t\t<cbc:SourceCurrencyCode>";
                    // line 89
                    echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 89), "monedaRef", [], "any", false, false, false, 89);
                    echo "</cbc:SourceCurrencyCode>
\t\t\t\t<cbc:TargetCurrencyCode>";
                    // line 90
                    echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 90), "monedaObj", [], "any", false, false, false, 90);
                    echo "</cbc:TargetCurrencyCode>
\t\t\t\t<cbc:CalculationRate>";
                    // line 91
                    echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 91), "factor", [], "any", false, false, false, 91), 6]);
                    echo "</cbc:CalculationRate>
\t\t\t\t<cbc:Date>";
                    // line 92
                    echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 92), "fecha", [], "any", false, false, false, 92), "Y-m-d");
                    echo "</cbc:Date>
\t\t\t</cac:ExchangeRate>
            ";
                }
                // line 95
                echo "\t\t</sac:SUNATPerceptionInformation>
\t\t";
            }
            // line 97
            echo "\t</sac:SUNATPerceptionDocumentReference>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['det'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 99
        echo "</Perception>
";
        $___internal_5579364456b0afd0e5848d6be20c63198232a125706b00ca18501d7547f2d574_ = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 1
        echo twig_spaceless($___internal_5579364456b0afd0e5848d6be20c63198232a125706b00ca18501d7547f2d574_);
    }

    public function getTemplateName()
    {
        return "perception.xml.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  318 => 1,  314 => 99,  307 => 97,  303 => 95,  297 => 92,  293 => 91,  289 => 90,  285 => 89,  282 => 88,  280 => 87,  276 => 86,  272 => 85,  268 => 84,  265 => 83,  262 => 82,  259 => 81,  242 => 78,  236 => 77,  232 => 76,  229 => 75,  211 => 74,  209 => 73,  203 => 72,  199 => 71,  193 => 70,  190 => 69,  186 => 68,  182 => 67,  177 => 66,  171 => 64,  169 => 63,  165 => 62,  161 => 61,  155 => 58,  147 => 55,  139 => 50,  132 => 46,  127 => 44,  123 => 43,  119 => 42,  115 => 41,  111 => 40,  108 => 39,  106 => 38,  101 => 36,  95 => 33,  89 => 30,  85 => 29,  79 => 28,  67 => 19,  61 => 16,  55 => 13,  52 => 12,  50 => 11,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "perception.xml.twig", "/var/www/html/sunat/vendor/greenter/xml/src/Xml/Templates/perception.xml.twig");
    }
}
