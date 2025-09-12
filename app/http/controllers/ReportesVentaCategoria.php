<?php

require_once 'utils/lib/vendor/autoload.php';
require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/exel/vendor/autoload.php';
require_once 'app/models/TipoProductoModel.php';
require_once 'app/models/CategoriaProductoModel.php';
require_once 'app/models/Usuario.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportesVentaCategoria extends Controller
{
    private $conexion;
    /*  private $mpdf; */

    public function __construct()
    {
        /*  $this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']); */
        $this->conexion = (new Conexion())->getConexion();
    }

}