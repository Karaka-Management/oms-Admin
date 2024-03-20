<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Media
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class DefaultExcel extends \PhpOffice\PhpSpreadsheet\Spreadsheet
{
    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        parent::__construct();

        $this->getActiveSheet()
            ->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $this->getActiveSheet()
            ->getHeaderFooter()
            ->setOddHeader("&L&B&20Jingga\n&B&10Business solutions made simple.");

        $this->getActiveSheet()
            ->getHeaderFooter()
            ->setOddFooter('&RPage &P/&N');

        /*
        Tested with LibreOffice and not working (requires &G above in the text).
        Either it is broken or LibreOffice cannot show the image.
        $drawing = new HeaderFooterDrawing();
        $drawing->setName('PhpSpreadsheet logo');
        $drawing->setPath(__DIR__ . '/../Web/Backend/img/logo.png');
        $drawing->setHeight(50);

        $this->getActiveSheet()
            ->getHeaderFooter()
            ->addImage($drawing, HeaderFooter::IMAGE_HEADER_LEFT);
        */
    }
}
