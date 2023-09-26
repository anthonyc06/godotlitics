<?php														
    DEFINE("HOST", "localhost");			
    DEFINE("DBNAME", "godotlitics");
    DEFINE("USERNAME", "root");
    DEFINE("PASS", "");
    DEFINE("API_KEY", "ABC123");

    class Helper {

        function ConnectBDD()
        {
            return new PDO('mysql:host='.HOST.';dbname='.DBNAME, USERNAME, PASS);
        }

        function CheckApiKey($key = null)
        {
            return $key == API_KEY;
        }
        
        // Get all events or get 1 event by ID
        function GetEvents($key = null, $id = null, $page = null)
        {
            $offset = ($page-1) * 25;
            
            $where = ($id != null) ? "AND id=".$id : "";
            $sql = "SELECT * FROM events WHERE id>0 ".$where . "ORDER BY id DESC LIMIT 25 OFFSET " . $offset;
            
            return $this->GetEventsFromDb($sql, $key);
        }

        // Get all events by game name
        function GetEventsByGame($key = null, $game = null, $page = null)
        {
            $offset = ($page-1) * 25;

            $where = ($game != null) ? 'AND gameName="'.$game.'"' : '';
            $sql = "SELECT * FROM events WHERE id>0 ".$where . "ORDER BY id ASC LIMIT 25 OFFSET " . $offset;
            
            return $this->GetEventsFromDb($sql, $key);
        }

        function GetEventsFromDb($sql, $key)
        {
            if (!$this->CheckApiKey($key))
            {
                die("Wrong API key!");
            }

            $bdd = $this->ConnectBDD();
            
            $result = $bdd->prepare($sql);
            $result->execute();
            
            $d = $result->fetchAll(PDO::FETCH_ASSOC);
            return $this->ParseEventsJson($d);
        }

	    /* JSON Parsing*/
        function ParseEventsJson($array = array())
        {
            $r = array();
                
            foreach($array as $k => $v)
            {
                $t = array(
                    "id" 	     => intval($v["id"]),
                    "studioName" => (string)$v["studioName"],
                    "gameName" 	 => (string)$v["gameName"],
                    "gameVersion"=> (string)$v["gameVersion"],
                    "eventType"  => (string)$v["eventType"],
                    "eventParam" => (string)$v["eventParam"],
                    "eventDate"  => (string)$v["eventDate"],
                    "userId"     => (string)$v["userId"]
                );
                array_push($r, $t);
            }
        
            return json_encode($r);
        }

        // Create event
        function AddEvent($studioName = null, $gameName = null, $gameVersion = null, $eventType = null, $eventParam = null, $userId = null, $key = null)
        {
            
            if (!$this->CheckApiKey($key))
            {
                die("Wrong API key!");
            }

            $bdd = $this->ConnectBDD();
            
            $sql = "INSERT INTO `events` (`studioName`, `gameName`, `gameVersion`, `eventType`, `eventParam`, `userId`) VALUES ('".$studioName."', '".$gameName."', '".$gameVersion."', '".$eventType."', '".$eventParam."', '".$userId."');";
            
            $result = $bdd->prepare($sql);
            $result->execute();

            $data = $result->fetchAll();
            $last_id = $bdd->lastInsertId();

            return $last_id;
        }

        function GetGames()
        {
            $bdd = $this->ConnectBDD();

            $sql = "SELECT DISTINCT gameName FROM events ORDER BY gameName ASC";
            
            $result = $bdd->prepare($sql);
            $result->execute();
            
            $d = $result->fetchAll(PDO::FETCH_ASSOC);
            return $this->ParseNamesJson($d);
        }

        /* JSON Parsing*/
        function ParseNamesJson($array = array())
        {
            $r = array();
                
            foreach($array as $k => $v)
            {
                array_push($r, (string)$v["gameName"]);
            }
        
            return json_encode($r);
        }

        // Get events Count
        function GetEventsCount($key = null, $game = null)
        {
            $bdd = $this->ConnectBDD();

            $where = ($game != null) ? 'AND gameName = "'.$game.'"' : '';

            $sqlLaunch = 'SELECT COUNT(eventType) FROM events WHERE eventType = "launch_game" ' . $where;
            $nbLaunch = $bdd->query($sqlLaunch)->fetchColumn();

            $sqlPages = 'SELECT COUNT(eventType) FROM events WHERE eventType = "view_screen" ' . $where;
            $nbPages = $bdd->query($sqlPages)->fetchColumn();

            $sqlAd = 'SELECT COUNT(eventType) FROM events WHERE eventType = "ad_showed" ' . $where;
            $nbAd = $bdd->query($sqlAd)->fetchColumn();

            $sqlProgress = 'SELECT COUNT(eventType) FROM events WHERE eventType = "progression" ' . $where;
            $nbProgress = $bdd->query($sqlProgress)->fetchColumn();

            $sqlErr = 'SELECT COUNT(eventType) FROM events WHERE eventType = "error" ' . $where;
            $nbErr = $bdd->query($sqlErr)->fetchColumn();

            $d = array(
                "launch_game" => $nbLaunch,
                "view_screen" => $nbPages,
                "ad_showed" => $nbAd,
                "progression" => $nbProgress,
                "error" => $nbErr
            );

            return $d;
        }


        // Get retention
        function GetRetention($key = null, $game = null)
        {
            $bdd = $this->ConnectBDD();

            $where = ($game != null) ? ' AND gameName = "'.$game.'"' : '';

            $retentionSql = 'SELECT DISTINCT(userId), MIN(eventDate) AS minDays, MAX(eventDate) AS maxDays, DATEDIFF(MAX(eventDate), MIN(eventDate)) AS diffDays FROM events WHERE `eventType` = "launch_game"' . $where . ' GROUP BY userId ORDER BY diffDays ASC;';

            $result = $bdd->prepare($retentionSql);
            $result->execute();
            $d = $result->fetchAll(PDO::FETCH_ASSOC);
            return $d;
        }
    }
?>