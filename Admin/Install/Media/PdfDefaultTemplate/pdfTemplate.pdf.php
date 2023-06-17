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
 * Default PDF class.
 *
 * The TCPDF class must be included previously in the parent code.
 *
 * @package Modules\Media
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @link ../../../../../../Resources/tcpdf/tcpdf.php
 */
class DefaultPdf extends TCPDF
{
    /**
     * Font
     *
     * @var string
     * @since 1.0.0
     */
    public string $fontName = '';

    /**
     * Font size
     *
     * @var int
     * @since 1.0.0
     */
    public int $fontSize = 8;

    /**
     * Side margins
     *
     * @var int
     * @since 1.0.0
     */
    public int $sideMargin = 15;

    public string $language = 'en';

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
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);

        // set default header data
        $this->setHeaderData('', 15, $this->attributes['title_name'], $this->attributes['slogan']);

        // set header and footer fonts
        $this->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $this->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

        // set default monospaced font
        $this->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->setMargins(15, 30, 15);

        // set auto page breaks
        $this->setAutoPageBreak(true, 25);

        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }

    /**
     * Create header
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function Header() : void
    {
        if ($this->header_xobjid === false) {
            $this->header_xobjid = $this->startTemplate($this->w, 0);

            // Set Logo
            if (!empty($this->header_logo)) {
                $this->Image(
                    $this->header_logo,
                    15, 15,
                    $this->header_logo_width, 0,
                    'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false
                );
            }

            // Set Title
            $this->setFont('helvetica', 'B', 20);
            $this->setX(15 + 15 + 3);
            $this->Cell(0, 14, $this->header_title, 0, false, 'L', 0, '', 0, false, 'T', 'M');

            $this->setFont('helvetica', '', 10);
            $this->setX(15 + 15 + 3);
            $this->Cell(0, 26, $this->header_string, 0, false, 'L', 0, '', 0, false, 'T', 'M');

            $this->endTemplate();
        }

        $x  = 0;
        $dx = 0;

        if (!$this->header_xobj_autoreset && $this->booklet && (($this->page % 2) == 0)) {
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

    /**
     * Create footer
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function Footer() : void
    {
        $this->setY(-25);

        $this->setFont('helvetica', 'I', 7);
        $this->Cell($this->getPageWidth() - 22, 0, $this->lang[$this->language]['Page'] . ' '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        $this->Ln();
        $this->Ln();

        $this->SetFillColor(245, 245, 245);
        $this->setX(0);
        $this->Cell($this->getPageWidth(), 25, '', 0, 0, 'L', true, '', 0, false, 'T', 'T');

        $this->setFont('helvetica', '', 7);
        $this->setXY(15 + 10, -15, true);
        $this->MultiCell(
            30, 0,
            $this->attributes['legal_name'] . "\n"
            . $this->attributes['address'] . "\n"
            . $this->attributes['city'],
            0, 'L', false, 1, null, null, true, 0, false, true, 0, 'B'
        );

        $this->setXY(25 + 15 + 20, -15, true);
        $this->MultiCell(
            40, 0,
            $this->lang[$this->language]['CEO']. ': ' . $this->attributes['ceo'] . "\n"
            . $this->lang[$this->language]['TaxOffice']. ': ' . $this->attributes['tax_office'] . "\n"
            . $this->lang[$this->language]['TaxNumber']. ': ' . $this->attributes['tax_number'],
            0, 'L', false, 1, null, null, true, 0, false, true, 0, 'B'
        );

        $this->setXY(25 + 45 + 15 + 30, -15, true);
        $this->MultiCell(
            35, 0,
            $this->attributes['bank_name'] . "\n"
            . $this->lang[$this->language]['Swift']. ': ' . $this->attributes['swift'] . "\n"
            . $this->lang[$this->language]['BankAccount']. ': ' . $this->attributes['bank_account'],
            0, 'L', false, 1, null, null, true, 0, false, true, 0, 'B'
        );

        $this->setXY(25 + 45 + 35 + 15 + 40, -15, true);
        $this->MultiCell(
            35, 0,
            $this->attributes['website'] . "\n"
            . $this->attributes['email'] . "\n"
            . $this->attributes['phone'],
            0, 'L', false, 1, null, null, true, 0, false, true, 0, 'B'
        );
    }
}
