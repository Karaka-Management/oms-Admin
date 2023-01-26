<?php

declare(strict_types=1);

require_once __DIR__ . '/../phpOMS/Autoloader.php';

use phpOMS\Autoloader;
Autoloader::addPath(__DIR__ . '/../Resources');

require_once __DIR__ . '/../Resources/tcpdf/tcpdf.php';

class DefaultPdf extends TCPDF
{
    public string $fontName = '';
    public int $fontSize = 8;
    public int $sideMargin = 15;

    //Page header
    public function Header() {
    	if ($this->header_xobjid === false) {
    		$this->header_xobjid = $this->startTemplate($this->w, 0);

	        // Set Logo
	        $image_file = __DIR__ . '/../Web/Backend/img/logo.png';
	        $this->Image($image_file, 15, 15, 15, 15, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

	        // Set Title
	        $this->SetFont('helvetica', 'B', 20);
	        $this->setX(15 + 15 + 3);
	        $this->Cell(0, 14, $this->header_title, 0, false, 'L', 0, '', 0, false, 'T', 'M');

	        $this->SetFont('helvetica', '', 10);
	        $this->setX(15 + 15 + 3);
	        $this->Cell(0, 26, $this->header_string, 0, false, 'L', 0, '', 0, false, 'T', 'M');

	        $this->endTemplate();
	    }

	    $x  = 0;
		$dx = 0;

		if (!$this->header_xobj_autoreset AND $this->booklet AND (($this->page % 2) == 0)) {
			// adjust margins for booklet mode
			$dx = ($this->original_lMargin - $this->original_rMargin);
		}

		if ($this->rtl) {
			$x = $this->w + $dx;
		} else {
			$x = 0 + $dx;
		}

	    $this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
		if ($this->header_xobj_autoreset) {
			// reset header xobject template at each page
			$this->header_xobjid = false;
		}
    }

    // Page footer
    public function Footer() {
        $this->SetY(-25);

        $this->SetFont('helvetica', 'I', 7);
        $this->Cell($this->getPageWidth() - 22, 0, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        $this->Ln();
        $this->Ln();

        $this->SetFillColor(245, 245, 245);
        $this->SetX(0);
        $this->Cell($this->getPageWidth(), 25, '', 0, 0, 'L', true, '', 0, false, 'T', 'T');

        $this->SetFont('helvetica', '', 7);
        $this->SetXY(15 + 10, -15, true);
        $this->MultiCell(30, 0, "Jingga e.K.\nGartenstr. 26\n61206 Woellstadt", 0, 'L', false, 1, null, null, true, 0, false, true, 0, 'B');

        $this->SetXY(25 + 15 + 20, -15, true);
        $this->MultiCell(40, 0, "Geschäftsführer: Dennis Eichhorn\nFinanzamt: HRB ???\nUSt Id: DE ??????????", 0, 'L', false, 1, null, null, true, 0, false, true, 0, 'B');

        $this->SetXY(25 + 45 + 15 + 30, -15, true);
        $this->MultiCell(35, 0, "Volksbank Mittelhessen\nBIC: ??????????\nIBAN: ???????????", 0, 'L', false, 1, null, null, true, 0, false, true, 0, 'B');

        $this->SetXY(25 + 45 + 35 + 15 + 40, -15, true);
        $this->MultiCell(35, 0, "www.jingga.app\ninfo@jingga.app\n+49 0152 ???????", 0, 'L', false, 1, null, null, true, 0, false, true, 0, 'B');
    }

    public function __construct()
    {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);

        $this->SetCreator("Jingga");

        // set default header data
        $this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Jingga', 'Business solutions made simple.');

        // set header and footer fonts
        $this->SetHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $this->SetFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->SetMargins(15, 30, 15);

        // set auto page breaks
        $this->SetAutoPageBreak(true, 25);

        // set image scale factor
        $this->SetImageScale(PDF_IMAGE_SCALE_RATIO);

        // add a page
        $this->AddPage();
    }
}

/*
[
    'company' => '',
    'slogan' => '',
    'company_full' => '',
    'address' => '',
    'ciry' => '',
    'manager' => '',
    'tax_office' => '',
    'tax_id' => '',
    'tax_vat' => '',
    'bank_name' => '',
    'bank_bic' => '',
    'bank_iban' => '',
    'website' => '',
    'email' => '',
    'phone' => '',
    'creator' => '',
    'date' => '',
]
*/
