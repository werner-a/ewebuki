<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "image resize on demand";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $image["file"] = $HTTP_GET_VARS["path"];

    if ( !file_exists($image["file"]) ) {
        die("can't find source file: ".$image["file"]);
    }

    $image["size"] = getimagesize($image["file"]);

    switch ( $image["size"][2] ) {
        case 1: // gif
            $img_src = @imagecreatefromgif($image["file"]);
            $img_dst = resize( $img_src, $HTTP_GET_VARS["size"]);
            header("Content-type: image/gif");
            echo imagegif($img_dst);
            break;
        case 2: // jpg
            $img_src = @imagecreatefromjpeg($image["file"]);
            $img_dst = resize( $img_src, $HTTP_GET_VARS["size"]);
            header("Content-type: image/jpeg");
            echo imagejpeg($img_dst);
            break;
        case 3: // png
            $img_src = @imagecreatefrompng($image["file"]);
            $img_dst = resize( $img_src, $HTTP_GET_VARS["size"]);
            header("Content-type: image/png");
            echo imagepng($img_dst);
            break;
        default:
            die("source is not a valid image (gif, jpg, png)");
    }

    imagedestroy($img_src);
    imagedestroy($img_dst);

    function resize($img_src, $max_size) {

        $src_width = imagesx($img_src);
        $src_height = imagesy($img_src);

        if ( $src_width > $src_height ) {
            $dest_width = $max_size;
            $dest_height = (int)(($max_size * $src_height) / $src_width );
        } else {
            $dest_height = $max_size;
            $dest_width = (int)(($max_size * $src_width) / $src_height );
        }

        if ($dest_height == '') $dest_height  = $src_height;
        if ($dest_width == '') $dest_width  = $src_width;

        if ( function_exists(imagecreatetruecolor) ) {
            // leeres image erstellen und groesse aendern
            $img_dst = @imagecreatetruecolor($dest_width,$dest_height);
            imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
        } else {
            // gd < 2.0 fallback
            $img_dst = imagecreate($dest_width,$dest_height);
            imagecopyresized($img_dst, $img_src, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
        }

        return $img_dst;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
