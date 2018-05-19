<?php
//Define interface class for router
use \Psr\Http\Message\ServerRequestInterface as Request;        //PSR7 ServerRequestInterface   >> Each router file must contains this
use \Psr\Http\Message\ResponseInterface as Response;            //PSR7 ResponseInterface        >> Each router file must contains this

//Define your modules class
use \modules\backup\Backup as Backup;                           //Your main modules class

//Define additional class for any purpose
use \classes\middleware\ApiKey as ApiKey;                       //ApiKey Middleware             >> To authorize request by using ApiKey generated by reSlim
use \classes\SimpleCache as SimpleCache;                        //SimpleCache class             >> To cache response ouput server side
use \classes\JSON as JSON;                                      //JSON class                    >> To handle JSON in better way (also for debug purpose)

    // Get module information (include cache)
    $app->get('/modules/backup/get/info/', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(3600,["apikey"])){
            $datajson = SimpleCache::load(["apikey"]);
        } else {
            $datajson = SimpleCache::save($backup->viewInfo(),["apikey"]);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200);
    })->add(new ApiKey);

    // Backup all tables
    $app->get('/modules/backup/all/{username}/{token}', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $backup->username = $request->getAttribute('username');
        $backup->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($backup->table());
        return classes\Cors::modify($response,$body,200);
    });
    
    // Backup for spesific table only
    $app->get('/modules/backup/table/{tablename}/{username}/{token}', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $backup->username = $request->getAttribute('username');
        $backup->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($backup->table($request->getAttribute('tablename')));
        return classes\Cors::modify($response,$body,200);
    });

    // Show all backup files
    $app->get('/modules/backup/show/all/{username}/{token}', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $backup->username = $request->getAttribute('username');
        $backup->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($backup->showAllFiles());
        return classes\Cors::modify($response,$body,200);
    });