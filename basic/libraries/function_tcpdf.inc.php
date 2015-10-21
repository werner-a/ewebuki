<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_tcpdf.inc.php v2 chaot
// TCPDF connector
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

        global $pathvars, $debugging, $cfg, $environment, $kekse;

        if ( $cfg["pdfc"]["debug"] == False ) {
            $debugging["html_enable"] = 0;
            $debugging["sql_enable"] = 0;
        }

        $array = array_slice($kekse["label"], 1); $subject = null;
        foreach ( $array as $value ) {
            if ( !empty($subject) ) $subject .= " > ";
            $subject .= $value;
        }

        // create new PDF document
        // TCPDF(P, mm, A4, , , );
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator('eWeBuKi'); // PDF_CREATOR
        $pdf->SetTitle($cfg["pdfc"]["document"]["title"]); // TCPDF Example 061
        $pdf->SetAuthor($cfg["pdfc"]["document"]["author"]); // Nicola Asuni
        $pdf->SetSubject($subject); // TCPDF Tutorial
        $pdf->SetKeywords($cfg["pdfc"]["document"]["keywords"]); // 'TCPDF, PDF, example, test, guide'

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
                         $cfg["pdfc"]["change"]["PDF_MARGIN_TOP"], // 27
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
        #$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); // 25
        $pdf->SetAutoPageBreak(TRUE, 20);

        // set image scale factor
        #$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setImageScale(1.25);

        // set font
        $pdf->SetFont('helvetica', '', 10);

        // add a page
        $pdf->AddPage();

        // TCPDF connector regex extender
        if ($handle=opendir($pathvars["libraries"]))
        {
            while ( false!==( $file=readdir($handle )) ) {
                if ( strstr($file, "function_tcpdf_regex_") )
                {
                    require_once $pathvars["libraries"].$file;
                }
            }
        }       

        if ( $cfg["pdfc"]["force_utf8"] == true ) {
            $html = utf8_encode($buffer);
        } else {
            $html = $buffer;
        }

        // html tip & tricks
        #$pdf->SetCellPadding(0);
        #$pdf->setCellHeightRatio(1.25);
        #$pdf->setImageScale(1.2);

        if ( $cfg["pdfc"]["debug"] == true ) {
            echo $html;
        } else {
            // output the HTML content
            $pdf->writeHTML($html, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();

            // close and output PDF document
            #echo "<pre>"; print_r($kekse); echo "</pre>";
            $name = $cfg["pdfc"]["document"]["name_prefix"].end($kekse["label"]).".pdf";
            $pdf->Output($name, 'I');
        }
    }

    tcpdf_it($ausgaben["buffer"]);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
