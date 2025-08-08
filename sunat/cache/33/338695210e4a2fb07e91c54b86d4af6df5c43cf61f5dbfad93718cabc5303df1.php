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

/* notacr2.0.xml.twig */
class __TwigTemplate_9b3c516d6e5365ff77ebdcfb65cc520bcc2cd3b4193e3c556ee9df5805555cdf extends Template
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
<CreditNote xmlns=\"urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2\" xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\" xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\" xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\" xmlns:ext=\"urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2\" xmlns:sac=\"urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1\">
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
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "guias", [], "any", false, false, false, 75)) {
            // line 76
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "guias", [], "any", false, false, false, 76));
            foreach ($context['_seq'] as $context["_key"] => $context["guia"]) {
                // line 77
                echo "    <cac:DespatchDocumentReference>
        <cbc:ID>";
                // line 78
                echo twig_get_attribute($this->env, $this->source, $context["guia"], "nroDoc", [], "any", false, false, false, 78);
                echo "</cbc:ID>
        <cbc:DocumentTypeCode>";
                // line 79
                echo twig_get_attribute($this->env, $this->source, $context["guia"], "tipoDoc", [], "any", false, false, false, 79);
                echo "</cbc:DocumentTypeCode>
    </cac:DespatchDocumentReference>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['guia'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 82
            echo "    ";
        }
        // line 83
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "relDocs", [], "any", false, false, false, 83)) {
            // line 84
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "relDocs", [], "any", false, false, false, 84));
            foreach ($context['_seq'] as $context["_key"] => $context["rel"]) {
                // line 85
                echo "    <cac:AdditionalDocumentReference>
        <cbc:ID>";
                // line 86
                echo twig_get_attribute($this->env, $this->source, $context["rel"], "nroDoc", [], "any", false, false, false, 86);
                echo "</cbc:ID>
        <cbc:DocumentTypeCode>";
                // line 87
                echo twig_get_attribute($this->env, $this->source, $context["rel"], "tipoDoc", [], "any", false, false, false, 87);
                echo "</cbc:DocumentTypeCode>
    </cac:AdditionalDocumentReference>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rel'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 90
            echo "    ";
        }
        // line 91
        echo "    ";
        $context["emp"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "company", [], "any", false, false, false, 91);
        // line 92
        echo "    <cac:Signature>
        <cbc:ID>";
        // line 93
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 93);
        echo "</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>";
        // line 96
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 96);
        echo "</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[";
        // line 99
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 99);
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
        // line 109
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 109);
        echo "</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>6</cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PartyName>
                <cbc:Name><![CDATA[";
        // line 113
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "nombreComercial", [], "any", false, false, false, 113);
        echo "]]></cbc:Name>
            </cac:PartyName>
            ";
        // line 115
        $context["addr"] = twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "address", [], "any", false, false, false, 115);
        // line 116
        echo "            <cac:PostalAddress>
                <cbc:ID>";
        // line 117
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 117);
        echo "</cbc:ID>
                <cbc:StreetName><![CDATA[";
        // line 118
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "direccion", [], "any", false, false, false, 118);
        echo "]]></cbc:StreetName>
                <cbc:CityName>";
        // line 119
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "departamento", [], "any", false, false, false, 119);
        echo "</cbc:CityName>
                <cbc:CountrySubentity>";
        // line 120
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "provincia", [], "any", false, false, false, 120);
        echo "</cbc:CountrySubentity>
                <cbc:District>";
        // line 121
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "distrito", [], "any", false, false, false, 121);
        echo "</cbc:District>
                <cac:Country>
                    <cbc:IdentificationCode>";
        // line 123
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "codigoPais", [], "any", false, false, false, 123);
        echo "</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[";
        // line 127
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 127);
        echo "]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    ";
        // line 131
        $context["client"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "client", [], "any", false, false, false, 131);
        // line 132
        echo "    <cac:AccountingCustomerParty>
        <cbc:CustomerAssignedAccountID>";
        // line 133
        echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "numDoc", [], "any", false, false, false, 133);
        echo "</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>";
        // line 134
        echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "tipoDoc", [], "any", false, false, false, 134);
        echo "</cbc:AdditionalAccountID>
        <cac:Party>
            ";
        // line 136
        if (twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "address", [], "any", false, false, false, 136)) {
            // line 137
            echo "            ";
            $context["addr"] = twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "address", [], "any", false, false, false, 137);
            // line 138
            echo "            <cac:PostalAddress>
                <cbc:ID>";
            // line 139
            echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 139);
            echo "</cbc:ID>
                <cbc:StreetName><![CDATA[";
            // line 140
            echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "direccion", [], "any", false, false, false, 140);
            echo "]]></cbc:StreetName>
                <cac:Country>
                    <cbc:IdentificationCode>";
            // line 142
            echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "codigoPais", [], "any", false, false, false, 142);
            echo "</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            ";
        }
        // line 146
        echo "            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[";
        // line 147
        echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "rznSocial", [], "any", false, false, false, 147);
        echo "]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    ";
        // line 151
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoISC", [], "any", false, false, false, 151)) {
            // line 152
            echo "    ";
            $context["iscT"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoISC", [], "any", false, false, false, 152)]);
            // line 153
            echo "    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"";
            // line 154
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 154);
            echo "\">";
            echo ($context["iscT"] ?? null);
            echo "</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID=\"";
            // line 156
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 156);
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
        // line 167
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoIGV", [], "any", false, false, false, 167)) {
            // line 168
            echo "    ";
            $context["igvT"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoIGV", [], "any", false, false, false, 168)]);
            // line 169
            echo "    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"";
            // line 170
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 170);
            echo "\">";
            echo ($context["igvT"] ?? null);
            echo "</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID=\"";
            // line 172
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 172);
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
        // line 183
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "sumOtrosCargos", [], "any", false, false, false, 183)) {
            // line 184
            echo "    ";
            $context["othT"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "sumOtrosCargos", [], "any", false, false, false, 184)]);
            // line 185
            echo "    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"";
            // line 186
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 186);
            echo "\">";
            echo ($context["othT"] ?? null);
            echo "</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID=\"";
            // line 188
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 188);
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
        // line 199
        echo "    <cac:LegalMonetaryTotal>
        <cbc:ChargeTotalAmount currencyID=\"";
        // line 200
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 200);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [((twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOtrosTributos", [], "any", true, true, false, 200)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOtrosTributos", [], "any", false, false, false, 200), 0)) : (0))]);
        echo "</cbc:ChargeTotalAmount>
        <cbc:PayableAmount currencyID=\"";
        // line 201
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 201);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoImpVenta", [], "any", false, false, false, 201)]);
        echo "</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    ";
        // line 203
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "details", [], "any", false, false, false, 203));
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
            // line 204
            echo "    <cac:CreditNoteLine>
        <cbc:ID>";
            // line 205
            echo twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, false, 205);
            echo "</cbc:ID>
        <cbc:CreditedQuantity unitCode=\"";
            // line 206
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "unidad", [], "any", false, false, false, 206);
            echo "\">";
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "cantidad", [], "any", false, false, false, 206);
            echo "</cbc:CreditedQuantity>
        <cbc:LineExtensionAmount currencyID=\"";
            // line 207
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 207);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorVenta", [], "any", false, false, false, 207)]);
            echo "</cbc:LineExtensionAmount>
        <cac:PricingReference>
            ";
            // line 209
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorGratuito", [], "any", false, false, false, 209)) {
                // line 210
                echo "            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID=\"";
                // line 211
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 211);
                echo "\">";
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorGratuito", [], "any", false, false, false, 211)]);
                echo "</cbc:PriceAmount>
                <cbc:PriceTypeCode>02</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
            ";
            } else {
                // line 215
                echo "            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID=\"";
                // line 216
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 216);
                echo "\">";
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoPrecioUnitario", [], "any", false, false, false, 216)]);
                echo "</cbc:PriceAmount>
                <cbc:PriceTypeCode>01</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
            ";
            }
            // line 220
            echo "        </cac:PricingReference>
        ";
            // line 221
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "isc", [], "any", false, false, false, 221)) {
                // line 222
                echo "        ";
                $context["isc"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "isc", [], "any", false, false, false, 222)]);
                // line 223
                echo "        <cac:TaxTotal>
            <cbc:TaxAmount currencyID=\"";
                // line 224
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 224);
                echo "\">";
                echo ($context["isc"] ?? null);
                echo "</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID=\"";
                // line 226
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 226);
                echo "\">";
                echo ($context["isc"] ?? null);
                echo "</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:TierRange>";
                // line 228
                echo twig_get_attribute($this->env, $this->source, $context["detail"], "tipSisIsc", [], "any", false, false, false, 228);
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
            // line 238
            echo "        ";
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "igv", [], "any", false, false, false, 238)) {
                // line 239
                echo "        ";
                $context["igv"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "igv", [], "any", false, false, false, 239)]);
                // line 240
                echo "        <cac:TaxTotal>
            <cbc:TaxAmount currencyID=\"";
                // line 241
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 241);
                echo "\">";
                echo ($context["igv"] ?? null);
                echo "</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID=\"";
                // line 243
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 243);
                echo "\">";
                echo ($context["igv"] ?? null);
                echo "</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:TaxExemptionReasonCode>";
                // line 245
                echo twig_get_attribute($this->env, $this->source, $context["detail"], "tipAfeIgv", [], "any", false, false, false, 245);
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
            // line 255
            echo "        <cac:Item>
            <cbc:Description><![CDATA[";
            // line 256
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "descripcion", [], "any", false, false, false, 256);
            echo "]]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID>";
            // line 258
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "codProducto", [], "any", false, false, false, 258);
            echo "</cbc:ID>
            </cac:SellersItemIdentification>
            ";
            // line 260
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "codProdSunat", [], "any", false, false, false, 260)) {
                // line 261
                echo "            <cac:CommodityClassification>
                <cbc:ItemClassificationCode listID=\"UNSPSC\" listAgencyName=\"GS1 US\" listName=\"Item Classification\">";
                // line 262
                echo twig_get_attribute($this->env, $this->source, $context["detail"], "codProdSunat", [], "any", false, false, false, 262);
                echo "</cbc:ItemClassificationCode>
            </cac:CommodityClassification>
            ";
            }
            // line 265
            echo "        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID=\"";
            // line 267
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 267);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorUnitario", [], "any", false, false, false, 267)]);
            echo "</cbc:PriceAmount>
        </cac:Price>
    </cac:CreditNoteLine>
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
        // line 271
        echo "</CreditNote>
";
        $___internal_6e8b8b790468c6d599b5edb4e6499e0c87f8e56d869383315a0d57b9b9b3eee5_ = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 1
        echo twig_spaceless($___internal_6e8b8b790468c6d599b5edb4e6499e0c87f8e56d869383315a0d57b9b9b3eee5_);
    }

    public function getTemplateName()
    {
        return "notacr2.0.xml.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  697 => 1,  693 => 271,  673 => 267,  669 => 265,  663 => 262,  660 => 261,  658 => 260,  653 => 258,  648 => 256,  645 => 255,  632 => 245,  625 => 243,  618 => 241,  615 => 240,  612 => 239,  609 => 238,  596 => 228,  589 => 226,  582 => 224,  579 => 223,  576 => 222,  574 => 221,  571 => 220,  562 => 216,  559 => 215,  550 => 211,  547 => 210,  545 => 209,  538 => 207,  532 => 206,  528 => 205,  525 => 204,  508 => 203,  501 => 201,  495 => 200,  492 => 199,  476 => 188,  469 => 186,  466 => 185,  463 => 184,  460 => 183,  444 => 172,  437 => 170,  434 => 169,  431 => 168,  428 => 167,  412 => 156,  405 => 154,  402 => 153,  399 => 152,  397 => 151,  390 => 147,  387 => 146,  380 => 142,  375 => 140,  371 => 139,  368 => 138,  365 => 137,  363 => 136,  358 => 134,  354 => 133,  351 => 132,  349 => 131,  342 => 127,  335 => 123,  330 => 121,  326 => 120,  322 => 119,  318 => 118,  314 => 117,  311 => 116,  309 => 115,  304 => 113,  297 => 109,  284 => 99,  278 => 96,  272 => 93,  269 => 92,  266 => 91,  263 => 90,  254 => 87,  250 => 86,  247 => 85,  242 => 84,  239 => 83,  236 => 82,  227 => 79,  223 => 78,  220 => 77,  215 => 76,  213 => 75,  207 => 72,  203 => 71,  196 => 67,  192 => 66,  188 => 65,  183 => 63,  179 => 62,  175 => 61,  169 => 60,  158 => 51,  149 => 48,  145 => 47,  142 => 46,  137 => 45,  129 => 42,  125 => 40,  122 => 39,  112 => 32,  108 => 31,  104 => 30,  100 => 29,  97 => 28,  94 => 27,  91 => 26,  83 => 23,  79 => 21,  76 => 20,  68 => 17,  64 => 15,  61 => 14,  53 => 11,  49 => 9,  47 => 8,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "notacr2.0.xml.twig", "/var/www/html/sunat/vendor/greenter/xml/src/Xml/Templates/notacr2.0.xml.twig");
    }
}
