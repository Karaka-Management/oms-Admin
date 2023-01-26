<?php
declare(strict_types=1);

require_once __DIR__ . '/../phpOMS/Autoloader.php';

use phpOMS\Autoloader;
Autoloader::addPath(__DIR__ . '/../Resources');

class DefaultWord extends \PhpOffice\PhpWord\PhpWord {
    public function __construct()
    {
        parent::__construct();

        $generalTableStyle = ['cellMargin' => 100, 'bgColor' => 'f5f5f5'];
        $this->addTableStyle('FooterTableStyle', $generalTableStyle);
    }

    public function createFirstPage()
    {
        $section = $this->addSection([
            'marginLeft' => 1000,
            'marginRight' => 1000,
            'marginTop' => 2000,
            'marginBottom' => 2000,
        //    'headerHeight' => 50,
        //    'footerHeight' => 50,
        ]);

        // first page header
        $firstHeader = $section->addHeader();
        $firstHeader->firstPage();

        $table = $firstHeader->addTable();
        $table->addRow();

        // first column
        $table->addCell(1300)->addImage(__DIR__ . '/../Web/Backend/img/logo.png', ['width' => 50, 'height' => 50]);

        //second column
        $cell = $table->addCell(8700, ['valign' => 'bottom']);
        $textrun = $cell->addTextRun();
        $textrun->addText('Jingga', ['name' => 'helvetica', 'bold' => true, 'size' => 20]);

        $textrun = $cell->addTextRun();
        $textrun->addText('Business solutions made simple.', ['name' => 'helvetica', 'size' => 10]);

        // first page footer
        $firstFooter = $section->addFooter();
        $firstFooter->firstPage();
        $firstFooter->addPreserveText('Page {PAGE}/{NUMPAGES}', ['name' => 'helvetica', 'italic' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);
        $firstFooter->addTextRun();

        $table = $firstFooter->addTable('FooterTableStyle');
        $table->addRow();

        // columns
        $cell = $table->addCell(500);

        $cell = $table->addCell(2000);
        $textrun = $cell->addTextRun();
        $textrun->addText('Jingga e.K.', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('Gartenstr. 26', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('61206 Woellstadt', ['name' => 'helvetica', 'size' => 7]);

        $cell = $table->addCell(2700);
        $textrun = $cell->addTextRun();
        $textrun->addText('Geschäftsführer: Dennis Eichhorn', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('Finanzamt: HRB ???', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('USt Id: DE ??????????', ['name' => 'helvetica', 'size' => 7]);

        $cell = $table->addCell(2700);
        $textrun = $cell->addTextRun();
        $textrun->addText('Volksbank Mittelhessen', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('BIC: ??????????', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('IBAN: ???????????', ['name' => 'helvetica', 'size' => 7]);

        $cell = $table->addCell(2100);
        $textrun = $cell->addTextRun();
        $textrun->addText('www.jingga.app', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('info@jingga.app', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('+49 0152 ???????', ['name' => 'helvetica', 'size' => 7]);

        return $section;
    }

    public function createSecondPage()
    {
        $section = $this->addSection([
            'marginLeft' => 1000,
            'marginRight' => 1000,
            'marginTop' => 2000,
            'marginBottom' => 2000,
        //    'headerHeight' => 50,
        //    'footerHeight' => 50,
        ]);

        $header = $section->addHeader();
        $table = $header->addTable();
        $table->addRow();

        // first column
        $table->addCell(1300)->addImage(__DIR__ . '/../Web/Backend/img/logo.png', ['width' => 50, 'height' => 50]);

        //second column
        $cell = $table->addCell(8700, ['valign' => 'bottom']);
        $textrun = $cell->addTextRun();
        $textrun->addText('Jingga', ['name' => 'helvetica', 'bold' => true, 'size' => 20]);

        $textrun = $cell->addTextRun();
        $textrun->addText('Business solutions made simple.', ['name' => 'helvetica', 'size' => 10]);

        $footer = $section->addFooter();
        $footer->addPreserveText('Page {PAGE}/{NUMPAGES}', ['name' => 'helvetica', 'italic' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);
        $footer->addTextRun();

        $table = $footer->addTable('FooterTableStyle');
        $table->addRow();

        // columns
        $cell = $table->addCell(500);

        $cell = $table->addCell(2000);
        $textrun = $cell->addTextRun();
        $textrun->addText('Jingga e.K.', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('Gartenstr. 26', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('61206 Woellstadt', ['name' => 'helvetica', 'size' => 7]);

        $cell = $table->addCell(2700);
        $textrun = $cell->addTextRun();
        $textrun->addText('Geschäftsführer: Dennis Eichhorn', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('Finanzamt: HRB ???', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('USt Id: DE ??????????', ['name' => 'helvetica', 'size' => 7]);

        $cell = $table->addCell(2700);
        $textrun = $cell->addTextRun();
        $textrun->addText('Volksbank Mittelhessen', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('BIC: ??????????', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('IBAN: ???????????', ['name' => 'helvetica', 'size' => 7]);

        $cell = $table->addCell(2100);
        $textrun = $cell->addTextRun();
        $textrun->addText('www.jingga.app', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('info@jingga.app', ['name' => 'helvetica', 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText('+49 0152 ???????', ['name' => 'helvetica', 'size' => 7]);

        return $section;
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
