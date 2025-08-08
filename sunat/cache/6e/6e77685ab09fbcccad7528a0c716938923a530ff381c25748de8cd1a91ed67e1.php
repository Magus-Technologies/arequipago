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

/* notadb2.0.xml.twig */
class __TwigTemplate_cc8b27253cb91c3dc9e19cd319d778bd94787ef17002d427c7e34fb3f5e9f0e2 extends Template
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
<DebitNote xmlns=\"urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2\" xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\" xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\" xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\" xmlns:ext=\"urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2\" xmlns:sac=\"urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1\">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
                <sac:AdditionalInformation>
                    ";
        // line 8
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperGravadas", [], "any", false, false, false, 8)) {
            // line 9
            echo "                        <sac:AdditionalMonetaryTotal>
                            <cbc:ID>1001</cbc:ID>
                            <cbc:PayableAmount currencyID=\"";
            // line 11
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 11);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperGravadas", [], "any", false, false, false, 11)]);
            echo "</cbc:PayableAmount>
                        </sac:AdditionalMonetaryTotal>
                    ";
        }
        // line 14
        echo "                    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperInafectas", [], "any", false, false, false, 14)) {
            // line 15
            echo "                        <sac:AdditionalMonetaryTotal>
                            <cbc:ID>1002</cbc:ID>
                            <cbc:PayableAmount currencyID=\"";
            // line 17
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 17);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperInafectas", [], "any", false, false, false, 17)]);
            echo "</cbc:PayableAmount>
                        </sac:AdditionalMonetaryTotal>
                    ";
        }
        // line 20
        echo "                    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperExoneradas", [], "any", false, false, false, 20)) {
            // line 21
            echo "                        <sac:AdditionalMonetaryTotal>
                            <cbc:ID>1003</cbc:ID>
                            <cbc:PayableAmount currencyID=\"";
            // line 23
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 23);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperExoneradas", [], "any", false, false, false, 23)]);
            echo "</cbc:PayableAmount>
                        </sac:AdditionalMonetaryTotal>
                    ";
        }
        // line 26
        echo "                    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "perception", [], "any", false, false, false, 26)) {
            // line 27
            echo "                        ";
            $context["perc"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "perception", [], "any", false, false, false, 27);
            // line 28
            echo "                        <sac:AdditionalMonetaryTotal>
                            <cbc:ID schemeID=\"";
            // line 29
            echo twig_get_attribute($this->env, $this->source, ($context["perc"] ?? null), "codReg", [], "any", false, false, false, 29);
            echo "\">2001</cbc:ID>
                            <sac:ReferenceAmount currencyID=\"PEN\">";
            // line 30
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["perc"] ?? null), "mtoBase", [], "any", false, false, false, 30)]);
            echo "</sac:ReferenceAmount>
                            <cbc:PayableAmount currencyID=\"PEN\">";
            // line 31
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["perc"] ?? null), "mto", [], "any", false, false, false, 31)]);
            echo "</cbc:PayableAmount>
                            <sac:TotalAmount currencyID=\"PEN\">";
            // line 32
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["perc"] ?? null), "mtoTotal", [], "any", false, false, false, 32)]);
            echo "</sac:TotalAmount>
                        </sac:AdditionalMonetaryTotal>
                        <sac:AdditionalProperty>
                            <cbc:ID>2000</cbc:ID>
                            <cbc:Value>COMPROBANTE DE PERCEPCION</cbc:Value>
                        </sac:AdditionalProperty>
                    ";
        }
        // line 39
        echo "                    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperGratuitas", [], "any", false, false, false, 39)) {
            // line 40
            echo "                        <sac:AdditionalMonetaryTotal>
                            <cbc:ID>1004</cbc:ID>
                            <cbc:PayableAmount currencyID=\"";
            // line 42
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 42);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperGratuitas", [], "any", false, false, false, 42)]);
            echo "</cbc:PayableAmount>
                        </sac:AdditionalMonetaryTotal>
                    ";
        }
        // line 45
        echo "                    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "legends", [], "any", false, false, false, 45));
        foreach ($context['_seq'] as $context["_key"] => $context["leg"]) {
            // line 46
            echo "                        <sac:AdditionalProperty>
                            <cbc:ID>";
            // line 47
            echo twig_get_attribute($this->env, $this->source, $context["leg"], "code", [], "any", false, false, false, 47);
            echo "</cbc:ID>
                            <cbc:Value>";
            // line 48
            echo twig_get_attribute($this->env, $this->source, $context["leg"], "value", [], "any", false, false, false, 48);
            echo "</cbc:Value>
                        </sac:AdditionalProperty>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['leg'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 51
        echo "                </sac:AdditionalInformation>
            </ext:ExtensionContent>
        </ext:UBLExtension>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
    <cbc:CustomizationID>1.0</cbc:CustomizationID>
    <cbc:ID>";
        // line 60
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "serie", [], "any", false, false, false, 60);
        echo "-";
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "correlativo", [], "any", false, false, false, 60);
        echo "</cbc:ID>
    <cbc:IssueDate>";
        // line 61
        echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fechaEmision", [], "any", false, false, false, 61), "Y-m-d");
        echo "</cbc:IssueDate>
    <cbc:IssueTime>";
        // line 62
        echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fechaEmision", [], "any", false, false, false, 62), "H:i:s");
        echo "</cbc:IssueTime>
    <cbc:DocumentCurrencyCode>";
        // line 63
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 63);
        echo "</cbc:DocumentCurrencyCode>
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>";
        // line 65
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "numDocfectado", [], "any", false, false, false, 65);
        echo "</cbc:ReferenceID>
        <cbc:ResponseCode>";
        // line 66
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "codMotivo", [], "any", false, false, false, 66);
        echo "</cbc:ResponseCode>
        <cbc:Description>";
        // line 67
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "desMotivo", [], "any", false, false, false, 67);
        echo "</cbc:Description>
    </cac:DiscrepancyResponse>
    <cac:BillingReference>
        <cac:InvoiceDocumentReference>
            <cbc:ID>";
        // line 71
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "numDocfectado", [], "any", false, false, false, 71);
        echo "</cbc:ID>
            <cbc:DocumentTypeCode>";
        // line 72
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipDocAfectado", [], "any", false, false, false, 72);
        echo "</cbc:DocumentTypeCode>
        </cac:InvoiceDocumentReference>
    </cac:BillingReference>
    ";
        // line 75
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "relDocs", [], "any", false, false, false, 75)) {
            // line 76
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "relDocs", [], "any", false, false, false, 76));
            foreach ($context['_seq'] as $context["_key"] => $context["rel"]) {
                // line 77
                echo "    <cac:AdditionalDocumentReference>
        <cbc:ID>";
                // line 78
                echo twig_get_attribute($this->env, $this->source, $context["rel"], "nroDoc", [], "any", false, false, false, 78);
                echo "</cbc:ID>
        <cbc:DocumentTypeCode>";
                // line 79
                echo twig_get_attribute($this->env, $this->source, $context["rel"], "tipoDoc", [], "any", false, false, false, 79);
                echo "</cbc:DocumentTypeCode>
    </cac:AdditionalDocumentReference>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rel'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 82
            echo "    ";
        }
        // line 83
        echo "    ";
        $context["emp"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "company", [], "any", false, false, false, 83);
        // line 84
        echo "    <cac:Signature>
        <cbc:ID>";
        // line 85
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 85);
        echo "</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>";
        // line 88
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 88);
        echo "</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[";
        // line 91
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 91);
        echo "]]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#GREENTER-SIGN</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cbc:CustomerAssignedAccountID>";
        // line 101
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 101);
        echo "</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>6</cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PartyName>
                <cbc:Name><![CDATA[";
        // line 105
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "nombreComercial", [], "any", false, false, false, 105);
        echo "]]></cbc:Name>
            </cac:PartyName>
            ";
        // line 107
        $context["addr"] = twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "address", [], "any", false, false, false, 107);
        // line 108
        echo "            <cac:PostalAddress>
                <cbc:ID>";
        // line 109
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 109);
        echo "</cbc:ID>
                <cbc:StreetName><![CDATA[";
        // line 110
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "direccion", [], "any", false, false, false, 110);
        echo "]]></cbc:StreetName>
                <cbc:CityName>";
        // line 111
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "departamento", [], "any", false, false, false, 111);
        echo "</cbc:CityName>
                <cbc:CountrySubentity>";
        // line 112
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "provincia", [], "any", false, false, false, 112);
        echo "</cbc:CountrySubentity>
                <cbc:District>";
        // line 113
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "distrito", [], "any", false, false, false, 113);
        echo "</cbc:District>
                <cac:Country>
                    <cbc:IdentificationCode>";
        // line 115
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "codigoPais", [], "any", false, false, false, 115);
        echo "</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[";
        // line 119
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 119);
        echo "]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    ";
        // line 123
        $context["client"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "client", [], "any", false, false, false, 123);
        // line 124
        echo "    <cac:AccountingCustomerParty>
        <cbc:CustomerAssignedAccountID>";
        // line 125
        echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "numDoc", [], "any", false, false, false, 125);
        echo "</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>";
        // line 126
        echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "tipoDoc", [], "any", false, false, false, 126);
        echo "</cbc:AdditionalAccountID>
        <cac:Party>
            ";
        // line 128
        if (twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "address", [], "any", false, false, false, 128)) {
            // line 129
            echo "            ";
            $context["addr"] = twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "address", [], "any", false, false, false, 129);
            // line 130
            echo "            <cac:PostalAddress>
                <cbc:ID>";
            // line 131
            echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 131);
            echo "</cbc:ID>
                <cbc:StreetName><![CDATA[";
            // line 132
            echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "direccion", [], "any", false, false, false, 132);
            echo "]]></cbc:StreetName>
                <cac:Country>
                    <cbc:IdentificationCode>";
            // line 134
            echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "codigoPais", [], "any", false, false, false, 134);
            echo "</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            ";
        }
        // line 138
        echo "            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[";
        // line 139
        echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "rznSocial", [], "any", false, false, false, 139);
        echo "]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    ";
        // line 143
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoISC", [], "any", false, false, false, 143)) {
            // line 144
            echo "    ";
            $context["iscT"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoISC", [], "any", false, false, false, 144)]);
            // line 145
            echo "    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"";
            // line 146
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 146);
            echo "\">";
            echo ($context["iscT"] ?? null);
            echo "</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID=\"";
            // line 148
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 148);
            echo "\">";
            echo ($context["iscT"] ?? null);
            echo "</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>2000</cbc:ID>
                    <cbc:Name>ISC</cbc:Name>
                    <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    ";
        }
        // line 159
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoIGV", [], "any", false, false, false, 159)) {
            // line 160
            echo "    ";
            $context["igvT"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoIGV", [], "any", false, false, false, 160)]);
            // line 161
            echo "    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"";
            // line 162
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 162);
            echo "\">";
            echo ($context["igvT"] ?? null);
            echo "</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID=\"";
            // line 164
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 164);
            echo "\">";
            echo ($context["igvT"] ?? null);
            echo "</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    ";
        }
        // line 175
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "sumOtrosCargos", [], "any", false, false, false, 175)) {
            // line 176
            echo "    ";
            $context["othT"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "sumOtrosCargos", [], "any", false, false, false, 176)]);
            // line 177
            echo "    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"";
            // line 178
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 178);
            echo "\">";
            echo ($context["othT"] ?? null);
            echo "</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID=\"";
            // line 180
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 180);
            echo "\">";
            echo ($context["othT"] ?? null);
            echo "</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>9999</cbc:ID>
                    <cbc:Name>OTROS</cbc:Name>
                    <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    ";
        }
        // line 191
        echo "    <cac:RequestedMonetaryTotal>
        <cbc:ChargeTotalAmount currencyID=\"";
        // line 192
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 192);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [((twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOtrosTributos", [], "any", true, true, false, 192)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOtrosTributos", [], "any", false, false, false, 192), 0)) : (0))]);
        echo "</cbc:ChargeTotalAmount>
        <cbc:PayableAmount currencyID=\"";
        // line 193
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 193);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoImpVenta", [], "any", false, false, false, 193)]);
        echo "</cbc:PayableAmount>
    </cac:RequestedMonetaryTotal>
    ";
        // line 195
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "details", [], "any", false, false, false, 195));
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
        foreach ($context['_seq'] as $context["_key"] => $context["detail"]) {
            // line 196
            echo "    <cac:DebitNoteLine>
        <cbc:ID>";
            // line 197
            echo twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, false, 197);
            echo "</cbc:ID>
        <cbc:DebitedQuantity unitCode=\"";
            // line 198
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "unidad", [], "any", false, false, false, 198);
            echo "\">";
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "cantidad", [], "any", false, false, false, 198);
            echo "</cbc:DebitedQuantity>
        <cbc:LineExtensionAmount currencyID=\"";
            // line 199
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 199);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorVenta", [], "any", false, false, false, 199)]);
            echo "</cbc:LineExtensionAmount>
        <cac:PricingReference>
            ";
            // line 201
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorGratuito", [], "any", false, false, false, 201)) {
                // line 202
                echo "            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID=\"";
                // line 203
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 203);
                echo "\">";
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorGratuito", [], "any", false, false, false, 203)]);
                echo "</cbc:PriceAmount>
                <cbc:PriceTypeCode>02</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
            ";
            } else {
                // line 207
                echo "            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID=\"";
                // line 208
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 208);
                echo "\">";
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoPrecioUnitario", [], "any", false, false, false, 208)]);
                echo "</cbc:PriceAmount>
                <cbc:PriceTypeCode>01</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
            ";
            }
            // line 212
            echo "        </cac:PricingReference>
        ";
            // line 213
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "isc", [], "any", false, false, false, 213)) {
                // line 214
                echo "        ";
                $context["isc"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "isc", [], "any", false, false, false, 214)]);
                // line 215
                echo "        <cac:TaxTotal>
            <cbc:TaxAmount currencyID=\"";
                // line 216
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 216);
                echo "\">";
                echo ($context["isc"] ?? null);
                echo "</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID=\"";
                // line 218
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 218);
                echo "\">";
                echo ($context["isc"] ?? null);
                echo "</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:TierRange>";
                // line 220
                echo twig_get_attribute($this->env, $this->source, $context["detail"], "tipSisIsc", [], "any", false, false, false, 220);
                echo "</cbc:TierRange>
                    <cac:TaxScheme>
                        <cbc:ID>2000</cbc:ID>
                        <cbc:Name>ISC</cbc:Name>
                        <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        ";
            }
            // line 230
            echo "        ";
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "igv", [], "any", false, false, false, 230)) {
                // line 231
                echo "        ";
                $context["igv"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "igv", [], "any", false, false, false, 231)]);
                // line 232
                echo "        <cac:TaxTotal>
            <cbc:TaxAmount currencyID=\"";
                // line 233
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 233);
                echo "\">";
                echo ($context["igv"] ?? null);
                echo "</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID=\"";
                // line 235
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 235);
                echo "\">";
                echo ($context["igv"] ?? null);
                echo "</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:TaxExemptionReasonCode>";
                // line 237
                echo twig_get_attribute($this->env, $this->source, $context["detail"], "tipAfeIgv", [], "any", false, false, false, 237);
                echo "</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        ";
            }
            // line 247
            echo "        <cac:Item>
            <cbc:Description><![CDATA[";
            // line 248
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "descripcion", [], "any", false, false, false, 248);
            echo "]]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID>";
            // line 250
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "codProducto", [], "any", false, false, false, 250);
            echo "</cbc:ID>
            </cac:SellersItemIdentification>
            ";
            // line 252
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "codProdSunat", [], "any", false, false, false, 252)) {
                // line 253
                echo "            <cac:CommodityClassification>
                <cbc:ItemClassificationCode listID=\"UNSPSC\" listAgencyName=\"GS1 US\" listName=\"Item Classification\">";
                // line 254
                echo twig_get_attribute($this->env, $this->source, $context["detail"], "codProdSunat", [], "any", false, false, false, 254);
                echo "</cbc:ItemClassificationCode>
            </cac:CommodityClassification>
            ";
            }
            // line 257
            echo "        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID=\"";
            // line 259
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 259);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorUnitario", [], "any", false, false, false, 259)]);
            echo "</cbc:PriceAmount>
        </cac:Price>
    </cac:DebitNoteLine>
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['detail'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 263
        echo "</DebitNote>
";
        $___internal_f2b2b827cc9e6fda91857a968755070ae880cd1a72d67d9b94edaa7ac013d9cd_ = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 1
        echo twig_spaceless($___internal_f2b2b827cc9e6fda91857a968755070ae880cd1a72d67d9b94edaa7ac013d9cd_);
    }

    public function getTemplateName()
    {
        return "notadb2.0.xml.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  670 => 1,  666 => 263,  646 => 259,  642 => 257,  636 => 254,  633 => 253,  631 => 252,  626 => 250,  621 => 248,  618 => 247,  605 => 237,  598 => 235,  591 => 233,  588 => 232,  585 => 231,  582 => 230,  569 => 220,  562 => 218,  555 => 216,  552 => 215,  549 => 214,  547 => 213,  544 => 212,  535 => 208,  532 => 207,  523 => 203,  520 => 202,  518 => 201,  511 => 199,  505 => 198,  501 => 197,  498 => 196,  481 => 195,  474 => 193,  468 => 192,  465 => 191,  449 => 180,  442 => 178,  439 => 177,  436 => 176,  433 => 175,  417 => 164,  410 => 162,  407 => 161,  404 => 160,  401 => 159,  385 => 148,  378 => 146,  375 => 145,  372 => 144,  370 => 143,  363 => 139,  360 => 138,  353 => 134,  348 => 132,  344 => 131,  341 => 130,  338 => 129,  336 => 128,  331 => 126,  327 => 125,  324 => 124,  322 => 123,  315 => 119,  308 => 115,  303 => 113,  299 => 112,  295 => 111,  291 => 110,  287 => 109,  284 => 108,  282 => 107,  277 => 105,  270 => 101,  257 => 91,  251 => 88,  245 => 85,  242 => 84,  239 => 83,  236 => 82,  227 => 79,  223 => 78,  220 => 77,  215 => 76,  213 => 75,  207 => 72,  203 => 71,  196 => 67,  192 => 66,  188 => 65,  183 => 63,  179 => 62,  175 => 61,  169 => 60,  158 => 51,  149 => 48,  145 => 47,  142 => 46,  137 => 45,  129 => 42,  125 => 40,  122 => 39,  112 => 32,  108 => 31,  104 => 30,  100 => 29,  97 => 28,  94 => 27,  91 => 26,  83 => 23,  79 => 21,  76 => 20,  68 => 17,  64 => 15,  61 => 14,  53 => 11,  49 => 9,  47 => 8,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "notadb2.0.xml.twig", "/var/www/html/sunat/vendor/greenter/xml/src/Xml/Templates/notadb2.0.xml.twig");
    }
}
