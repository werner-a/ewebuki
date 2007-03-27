<?php

    #echo $_SERVER["argc"]."\n";
    #print_r($_SERVER["argv"]);


    #for($i=0; $i<$_SERVER["argc"]; $i++) {
    #    print($_SERVER["argv"][$i]."\n");
    #}



    $source = $_SERVER["argv"][1];
    #$target = $_SERVER["argv"][1].".new";

    if ( file_exists($source) ) {

        if ( file_exists($source.".bak") ) {

            echo "\nabbruch: sicherung ".$source.".bak bereits vorhanden.\n\n";

        } else {

            if ( rename($source,$source.".bak") ) {

                $target = $source;
                $source = $source.".bak";

                $rhandle = fopen($source, r);
                $whandle = fopen($target, w);
                while (!feof($rhandle)) {

                    $buffer = fgets($rhandle, 4096);

                    // verify  R(\[[a-zA-Z])
                    while ( ereg("\\$[a-zA-Z_]{1,}\\[[a-zA-Z_]{1,}\\]", $buffer, $found ) ) {
                        $i++;


                        $new = str_replace("[","[\"",$found[0]);
                        $new = str_replace("]","\"]",$new);
                        #echo $new."\n";

                        $buffer = str_replace($found[0],$new,$buffer);


                    }

                    fputs($whandle, $buffer);

                }
                echo "\n".$i." ohne \" gefunden und ersetzt\n\n";

                fclose($rhandle);
                fclose($whandle);

            } else {
                echo "\nabbruch: datei konnte nicht gesichert werden.\n\n";
            }
        }

    } else {

        echo "\ndatei nicht gefunden: ".$source."\n";
        echo "aufruf mit\n\n";
        echo "php -q varfix.php changefile.php\n\n\n";

    }

?>
