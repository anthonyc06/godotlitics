<?php
    require_once "Helper.php";
    $helper = new Helper();

    $studioName  = strip_tags($_GET['studioName']);
    $gameName    = strip_tags($_GET['gameName']);
    $gameVersion = strip_tags($_GET['gameVersion']);
    $eventType   = strip_tags($_GET['eventType']);
    $eventParam  = strip_tags($_GET['eventParam']);
    $userId      = strip_tags($_GET['userId']);
    $key         = strip_tags($_GET['key']);
        
    $event = $helper->AddEvent($studioName, $gameName, $gameVersion, $eventType, $eventParam, $userId, $key);
            
    echo $event;
?>