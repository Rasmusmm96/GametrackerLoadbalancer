<?php
class DataAccess {

    private function getDatabase() {
        $con = new mysqli(
            '127.0.0.1',
            'root',
            'root',
            'Servers',
            '8889');

        return $con;
    }

    public function getGames($server) {
        $json = @file_get_contents('http://'. $server .'/GametrackerPublicBackend/api.php/games');
        if ($json === FALSE) {
            return false;
        } else {
            return $json;
        }
    }

    public function getGame($id, $server) {
        $json = @file_get_contents('http://'. $server .'/GametrackerPublicBackend/api.php/games/' . $id);
        if ($json === FALSE) {
            return false;
        } else {
            return $json;
        }
    }

    public function getOnlineServers() {
        $db = $this->getDatabase();

        $statement = 'SELECT Server FROM Servers WHERE Online = 1';

        $result = $db->query($statement);

        if (!is_null($result)) {
            $myResult = array();
            while($row = $result->fetch_assoc()){
                $myResult[] = $row;
            }
            return $myResult;
        } else {
            return false;
        }
    }

    public function setServerOffline($server) {
        $db = $this->getDatabase();

        $statement = 'UPDATE Servers SET Online = 0 WHERE Server = "' . $server . '"';

        echo $statement;

        $db->query($statement);

        if ($db->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }

}
?>
