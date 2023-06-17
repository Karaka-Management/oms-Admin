<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Media
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

/**
 * Default Word class.
 *
 * @package Modules\Media
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class DefaultWord extends \PhpOffice\PhpWord\PhpWord
{
    /**
     * Font
     *
     * @var string
     * @since 1.0.0
     */
    public string $fontName = 'helvetica';

    /**
     * Font size
     *
     * @var int
     * @since 1.0.0
     */
    public int $fontSize = 8;

    /**
     * Doc language
     *
     * @var string
     * @since 1.0.0
     */
    public string $language = 'en';

    /**
     * Localization
     *
     * @var array
     * @since 1.0.0
     */
    public array $lang = [
        'en' => [
            'Page'        => 'Page',
            'CEO'         => 'CEO',
            'TaxOffice'   => 'Tax office',
            'TaxNumber'   => 'Tax number',
            'Swift'       => 'BIC',
            'BankAccount' => 'Account',
        ],
        'de' => [
            'Page'        => 'Seite',
            'CEO'         => 'Geschäftsführer',
            'TaxOffice'   => 'Finanzamt',
            'TaxNumber'   => 'Steuernummer',
            'Swift'       => 'BIC',
            'BankAccount' => 'IBAN',
        ],
    ];

    /**
     * Attributes
     *
     * @var string[]
     * @since 1.0.0
     */
    public array $attributes = [
        'logo'         => __DIR__ . '/../Web/Backend/img/logo.png',
        'title_name'   => 'Jingga',
        'slogan'       => 'Business solutions made simple.',
        'legal_name'   => '',
        'address'      => '',
        'city'         => '',
        'country'      => '',
        'ceo'          => '',
        'tax_office'   => '',
        'tax_number'   => '',
        'bank_name'    => '',
        'swift'        => '',
        'bank_account' => '',
        'website'      => '',
        'email'        => '',
        'phone'        => '',
    ];

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        parent::__construct();

        $generalTableStyle = ['cellMargin' => 100, 'bgColor' => 'f5f5f5'];
        $this->addTableStyle('FooterTableStyle', $generalTableStyle);
    }

    /**
     * Create the first page
     *
     * @return \PhpOffice\PhpWord\Element\Section
     *
     * @since 1.0.0
     */
    public function createFirstPage()
    {
        $section = $this->addSection([
            'marginLeft'   => 1000,
            'marginRight'  => 1000,
            'marginTop'    => 2000,
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
        $table->addCell(1300)->addImage($this->attributes['logo'], ['width' => 50, 'height' => 50]);

        //second column
        $cell    = $table->addCell(8700, ['valign' => 'bottom']);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['title_name'], ['name' => $this->fontName, 'bold' => true, 'size' => 20]);

        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['slogan'], ['name' => $this->fontName, 'size' => 10]);

        // first page footer
        $firstFooter = $section->addFooter();
        $firstFooter->firstPage();
        $firstFooter->addPreserveText($this->lang[$this->language]['Page'] . ' {PAGE}/{NUMPAGES}', ['name' => $this->fontName, 'italic' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);
        $firstFooter->addTextRun();

        $table = $firstFooter->addTable('FooterTableStyle');
        $table->addRow();

        // columns
        $cell = $table->addCell(500);

        $cell    = $table->addCell(2000);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['legal_name'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['address'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['city'], ['name' => $this->fontName, 'size' => 7]);

        $cell    = $table->addCell(2700);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['CEO']. ': ' . $this->attributes['ceo'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['TaxOffice']. ': ' . $this->attributes['tax_office'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['TaxNumber']. ': ' . $this->attributes['tax_number'], ['name' => $this->fontName, 'size' => 7]);

        $cell    = $table->addCell(2700);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['bank_name'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['Swift']. ': ' . $this->attributes['swift'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['BankAccount']. ': ' . $this->attributes['bank_account'], ['name' => $this->fontName, 'size' => 7]);

        $cell    = $table->addCell(2100);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['website'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['email'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['phone'], ['name' => $this->fontName, 'size' => 7]);

        return $section;
    }

    /**
     * Create second page
     *
     * @return \PhpOffice\PhpWord\Element\Section
     *
     * @since 1.0.0
     */
    public function createSecondPage()
    {
        $section = $this->addSection([
            'marginLeft'   => 1000,
            'marginRight'  => 1000,
            'marginTop'    => 2000,
            'marginBottom' => 2000,
        //    'headerHeight' => 50,
        //    'footerHeight' => 50,
        ]);

        $header = $section->addHeader();
        $table  = $header->addTable();
        $table->addRow();

        // first column
        $table->addCell(1300)->addImage(__DIR__ . '/../Web/Backend/img/logo.png', ['width' => 50, 'height' => 50]);

        //second column
        $cell    = $table->addCell(8700, ['valign' => 'bottom']);
        $textrun = $cell->addTextRun();
        $textrun->addText('Jingga', ['name' => $this->fontName, 'bold' => true, 'size' => 20]);

        $textrun = $cell->addTextRun();
        $textrun->addText('Business solutions made simple.', ['name' => $this->fontName, 'size' => 10]);

        $footer = $section->addFooter();
        $footer->addPreserveText($this->lang[$this->language]['Page'] . ' {PAGE}/{NUMPAGES}', ['name' => $this->fontName, 'italic' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);
        $footer->addTextRun();

        $table = $footer->addTable('FooterTableStyle');
        $table->addRow();

        // columns
        $cell = $table->addCell(500);

        $cell    = $table->addCell(2000);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['legal_name'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['address'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['city'], ['name' => $this->fontName, 'size' => 7]);

        $cell    = $table->addCell(2700);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['CEO']. ': ' . $this->attributes['ceo'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['TaxOffice']. ': ' . $this->attributes['tax_office'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['TaxNumber']. ': ' . $this->attributes['tax_number'], ['name' => $this->fontName, 'size' => 7]);

        $cell    = $table->addCell(2700);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['bank_name'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['Swift']. ': ' . $this->attributes['swift'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->lang[$this->language]['BankAccount']. ': ' . $this->attributes['bank_account'], ['name' => $this->fontName, 'size' => 7]);

        $cell    = $table->addCell(2100);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['website'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['email'], ['name' => $this->fontName, 'size' => 7]);
        $textrun = $cell->addTextRun();
        $textrun->addText($this->attributes['phone'], ['name' => $this->fontName, 'size' => 7]);

        return $section;
    }
}
