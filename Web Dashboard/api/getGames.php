<?php
    require_once "Helper.php";
    $helper = new Helper();
      
    $games = $helper->GetGames();
            
    echo $games;
?>