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

    public function getGames() {
        $json = file_get_contents('http://localhost:8888/GametrackerPublicBackend/api.php/games');
        return $json;
    }

    public function getGame($id) {
        $json = file_get_contents('http://localhost:8888/GametrackerPublicBackend/api.php/games/' . $id);
        return $json;
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

}
?>
