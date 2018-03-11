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

        return $dataaccess->getGames();
    }

    public function getGame($id) {
        global $dataaccess;

        return $dataaccess->getGame($id);
    }

    private function getOnlineServers() {
        global $dataaccess;

        return $dataaccess->getOnlineServers();
    }

    public function pickServer() {
        global $shm_resource;

        $servers = $this->getOnlineServers();

        if (!$servers) {
            return "Servers are offline";
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