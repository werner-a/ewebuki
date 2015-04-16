<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "TCPDF connector";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

    This script is a part of eWeBuKi

    eWeBuKi is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    eWeBuKi is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with eWeBuKi; If you did not, you may download a copy at:

    URL:  http://www.gnu.org/licenses/gpl.txt

    You may also request a copy from:

    Free Software Foundation, Inc.
    59 Temple Place, Suite 330
    Boston, MA 02111-1307
    USA

    You may contact the author/development team at:

    Chaos Networks
    c/o Werner Ammon
    Lerchenstr. 11c

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    foreach($cfg["pdfc"]["constant"] as $key => $value) {
        if ( isset($cfg["pdfc"]["constant"][$key]) ) define($key, $value);
    }
    
    if ( file_exists($cfg["pdfc"]["path"]["lib"]) ) {
        require_once($cfg["pdfc"]["path"]["lib"]);
    } else {
        die("Can't find TCPDF library.");
    }

    $defined = get_defined_constants(true);    
    foreach($defined["user"] as $key => $value) {
        if ( substr($key, 0, 4) == "PDF_" ) { 
            if ( !isset($cfg["pdfc"]["change"][$key]) ) $cfg["pdfc"]["change"][$key] = constant($key);
        }
    }

    function tcpdf_it($buffer) {

        global $debugging, $cfg;

        if ( $cfg["pdfc"]["debug"] == False ) {
            $debugging["html_enable"] = 0;
            $debugging["sql_enable"] = 0;
        }

        // create new PDF document
        // TCPDF(P, mm, A4, , , );
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        #$pdf->SetAuthor('Nicola Asuni');
        $pdf->SetAuthor('ChaoS Networks');
        #$pdf->SetTitle('TCPDF Example 061');
        $pdf->SetTitle('eWeBuKi Test');
        #$pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetSubject('Direkte Ausgabe der Seite als PDF');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        #$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);
        $pdf->SetHeaderData($cfg["pdfc"]["change"]["PDF_HEADER_LOGO"],
                            $cfg["pdfc"]["change"]["PDF_HEADER_LOGO_WIDTH"],
                            $cfg["pdfc"]["change"]["PDF_HEADER_TITLE"],
                            $cfg["pdfc"]["change"]["PDF_HEADER_STRING"]
                            );

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        #$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins($cfg["pdfc"]["change"]["PDF_MARGIN_LEFT"], // 15
                         $cfg["pdfc"]["change"]["PDF_MARGIN_TOP"], // 27, 30
                         $cfg["pdfc"]["change"]["PDF_MARGIN_RIGHT"] // 15
                         );
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER); // 5
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER); // 10

        // remove default header/footer
        $pdf->setPrintHeader(true);
        #$pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
        #$pdf->setPrintFooter(false);

        // set auto page breaks
        #$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        #$pdf->SetAutoPageBreak(TRUE, 25);
        $pdf->SetAutoPageBreak(TRUE, 20);

        // set image scale factor
        #$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setImageScale(1.25);

        // set font
        $pdf->SetFont('helvetica', '', 10);

        // add a page
        $pdf->AddPage();

        /* NOTE:
         * *********************************************************
         * You can load external XHTML using :
         *
         * $html = file_get_contents('/path/to/your/file.html');
         *
         * External CSS files will be automatically loaded.
         * Sometimes you need to fix the path of the external CSS.
         * *********************************************************
         */

        // define some HTML content with style
        #$html = <<<EOF
        #EOF;

        //[IMG=/file/picture/medium/img_10.jpg;;0;b]Wolkenblick[/IMG]
        #$suchmuster = '~src="/file/(jpg|png|gif)/(\d+)/(b|m|s|o|tn)/.+"~';
        #$ersetzung = 'src="/file/picture/$3/img_${2}.${1}"';
        #$buffer = preg_replace($suchmuster, $ersetzung, $buffer);

        $s = '~src="/file/(jpg|png|gif)/(\d+)/tn/.+"~';
        $r = 'src="/file/picture/thumbnail/tn_${2}.${1}"';
        #$buffer = preg_replace($s, $r, $buffer);

        $s = '~src="/file/(jpg|png|gif)/(\d+)/s/.+"~';
        $r = 'src="/file/picture/small/img_${2}.${1}"';
        #$buffer = preg_replace($s, $r, $buffer);

        $s = '~src="/file/(jpg|png|gif)/(\d+)/m/.+"~';
        $r = 'src="/file/picture/medium/img_${2}.${1}"';
        #$buffer = preg_replace($s, $r, $buffer);

        $s = '~src="/file/(jpg|png|gif)/(\d+)/b/.+"~';
        $r = 'src="/file/picture/big/img_${2}.${1}"';
        #$buffer = preg_replace($s, $r, $buffer);

        $s = '~src="/file/(jpg|png|gif)/(\d+)/o/.+"~';
        $r = 'src="/file/picture/original/img_${2}.${1}"';
        #$buffer = preg_replace($s, $r, $buffer);

        if ( $cfg["pdfc"]["force_utf8"] == true ) {
            $html = utf8_encode($buffer);
        } else {
            $html = $buffer;
        }

        // html tip & tricks
        #$pdf->SetCellPadding(0);
        #$pdf->setCellHeightRatio(1.25);
        #$pdf->setImageScale(1.2);

        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // reset pointer to the last page
        $pdf->lastPage();

        //Close and output PDF document
        if ( $cfg["pdfc"]["debug"] == true ) {
            echo $html;
        } else {
            #$pdf->Output('example_061.pdf', 'I');
            $pdf->Output('ewebuki_test.pdf', 'I');
        }
    }

    tcpdf_it($ausgaben["buffer"]);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
