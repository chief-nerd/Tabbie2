<?php
/**
 * barcodes.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */
?>
    <!DOCTYPE html>
    <html>
    <head>
    </head>
<body>

<?php
foreach ($person as $a): ?>
    <?
    //$backurltouse = $backurl;
    // if ($a["dietary"] != 'No Preference') {
    //      $backurltouse = 'http://tabbie.dev:8088/uploads/badges/lightblueBadge.jpg';
    // } else {
    //     $backurltouse = 'http://tabbie.dev:8088/uploads/badges/blueBadge.jpg';
    //  }
    //if(in_array($a["code"], ['AA-00004641', 'AA-00004761'])){
    //	$backurltouse = 'http://tabbie.dev/uploads/badges/Badge-warsaw-eudc-language.jpg';
    //}
    ?>
    <div
        class="paper" <? echo ($backurl != "") ? 'style="background-repeat: no-repeat; background-size: 100%; background-image: url(' . $backurltouse . ')"' : '' ?>>
        <div class="badge">
            <div class="dietary">
                <?= $a["dietary"] ?>
            </div>
            <div class="code" style="width: 100%; margin-left: auto; margin-right: auto; margin-top: 0.4cm">
                <? echo \jakobreiter\quaggajs\BarcodeFactory::generateIMG($a["code"], $a["code"] . " " . $a["name"], $height); ?>
            </div>
            <div class="name" style="width: 75%; margin-left: auto; margin-right: auto">
                <?= $a["name"] ?>
            </div>
            <div class="team" style="width: 75%; margin: auto">
                <?= $a["extra"] . ' - ' . substr($a["society"], 0, 75) ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<? endforeach; ?>