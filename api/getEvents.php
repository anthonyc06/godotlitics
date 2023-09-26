<?php
    require_once "Helper.php";
    $helper = new Helper();
      
    if(isset($_GET["key"]) && $_GET["key"] != "")
    {  
        $key = strip_tags($_GET["key"]);
        $page = isset($_GET["page"]) ? strip_tags($_GET["page"]) : 1;
        
        // By ID
        if(isset($_GET["id"]) && $_GET["id"] > 0)
        {
            $id = $_GET["id"];
            $events = $helper->GetEvents($key, $id, $page);
        } 
        // By game name
        else if(isset($_GET["game"]) && $_GET["game"] != "")
        {
            $game = $_GET["game"];
            $events = $helper->GetEventsByGame($key, $game, $page);
        } 
        // All
        else {
            $events = $helper->GetEvents($key, null, $page);
        }
            
        echo $events;
    }
    else 
    {
        echo "Missing API key!";
    }
?>