<?php
function calendar($tag="") {

    $tage = array("So", "Mo", "Di", "Mi","Do", "Fr", "Sa");
    if ( $tag == "" ) {
        $heute = getdate();
    } else {
        $heute = getdate($tag);
    }

    // einige daten die spaeter vielleicht noch nuetzlich sind :)
    $tage_monat = $heute["mday"];
    $wochentag_ziffer = $heute["wday"];
    $wochentag = $heute["weekday"];
    $monat = $heute["month"];
    $monat_id = $heute["mon"];
    $jahr = $heute["year"];

    // start-tag
    $start = mktime ( 0, 0, 0, $monat_id, 1, $jahr );
    $start = getdate($start);
    $start =  $start["wday"];
    // start-tag

    $ausgabe = "<table border =\"1\">";
    $counter=0;
    $int_counter = "";

    // bauen er tabellenbeschriftung
    $ausgabe .= "<tr>";
    foreach ( $tage as $key => $value ) {
        $ausgabe .= "<td>".$value."</td>";
    }
    $ausgabe .= "</tr>";
    // bauen er tabellenbeschriftung

    while ( $stop != "-1" ) {
        $ausgabe .= "<tr>";
        foreach ( $tage as $key => $value ) {
            $counter++;
            if ( $counter > $start && $counter <= ($heute["mday"]+$start) ) {
                $int_counter++;
            } else {
                $int_counter = "";
            }
            $ausgabe .= "<td>".$int_counter."</td>";
        }
        $ausgabe .= "</tr>";
        if ( $counter >= $tage_monat) $stop = -1;
    }
    $ausgabe .= "</table>";

    return $ausgabe;
}
?>