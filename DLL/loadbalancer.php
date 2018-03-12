<?php

require_once "DAL/dataaccess.php";

$dataaccess = new DataAccess();
$shm_resource = null;


class LoadBalancer {

    /**
     * LoadBalancer constructor.
     */
    public function __construct() {
        global $shm_resource;

        $shm_resource = shm_attach(1);
    }

    public function getGames() {
        global $dataaccess;

        $server = $this->pickServer();

        if (!$server)
            return false;

        $result = $dataaccess->getGames($server);

        if (!$result) {
            $dataaccess->setServerOffline($server);
            return $this->getGames();
        }

        return $dataaccess->getGames($server);
    }

    public function getGame($id) {
        global $dataaccess;

        $server = $this->pickServer();

        if (!$server)
            return false;

        $result = $dataaccess->getGame($id, $server);

        if (!$result) {
            $dataaccess->setServerOffline($server);
            return $this->getGame($id);
        }

        if ($result == 'null')
            return 'No game with that ID';
        else {
            return $result;
        }
    }

    private function getOnlineServers() {
        global $dataaccess;

        return $dataaccess->getOnlineServers();
    }

    private function pickServer() {
        global $shm_resource;

        $servers = $this->getOnlineServers();

        if (!$servers) {
            return false;
        }

        if (!shm_has_var($shm_resource, 1)) {
            shm_put_var($shm_resource, 1, 0);
        }

        $lastUsedServer = shm_get_var($shm_resource, 1);

        if (count($servers) - 1 <= $lastUsedServer) {
            shm_put_var($shm_resource, 1,  0);
            return $servers[0]['Server'];
        }

        $nextServer = $lastUsedServer + 1;

        shm_put_var($shm_resource, 1, $nextServer);
        return $servers[$nextServer]['Server'];
    }

}

?>