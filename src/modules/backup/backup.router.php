<?php
//Define interface class for router
use \Psr\Http\Message\ServerRequestInterface as Request;        //PSR7 ServerRequestInterface   >> Each router file must contains this
use \Psr\Http\Message\ResponseInterface as Response;            //PSR7 ResponseInterface        >> Each router file must contains this

//Define your modules class
use \modules\backup\Backup as Backup;                           //Your main modules class

//Define additional class for any purpose
use \classes\middleware\ValidateParam as ValidateParam;         //ValidateParam Middleware      >> To validate the body form request
use \classes\middleware\ApiKey as ApiKey;                       //ApiKey Middleware             >> To authorize request by using ApiKey generated by reSlim


    // Get module information
    $app->get('/backup/get/info/', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($backup->viewInfo());
        return classes\Cors::modify($response,$body,200);
    })->add(new ApiKey);

    // Backup all tables
    $app->get('/backup/all/{username}/{token}', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $backup->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $backup->username = $request->getAttribute('username');
        $backup->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($backup->table());
        return classes\Cors::modify($response,$body,200);
    });
    
    // Backup for spesific table only
    $app->get('/backup/table/{tablename}/{username}/{token}', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $backup->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $backup->username = $request->getAttribute('username');
        $backup->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($backup->table($request->getAttribute('tablename')));
        return classes\Cors::modify($response,$body,200);
    });

    // Show all backup files
    $app->get('/backup/show/all/{username}/{token}', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $backup->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $backup->username = $request->getAttribute('username');
        $backup->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($backup->showAllFiles());
        return classes\Cors::modify($response,$body,200);
    });

    // Post delete backup file
    $app->post('/backup/delete', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $backup->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $datapost = $request->getParsedBody();
        $backup->username = $datapost['Username'];
        $backup->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($backup->deleteFile($datapost['Filename']));
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam(['Token','Filename'],'1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // Post delete backup all files
    $app->post('/backup/delete/all', function (Request $request, Response $response) {
        $backup = new Backup($this->db);
        $backup->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $datapost = $request->getParsedBody();
        $backup->username = $datapost['Username'];
        $backup->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($backup->deleteAll($datapost['Filename']));
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Filename','0-250'))
        ->add(new ValidateParam('Token','1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));