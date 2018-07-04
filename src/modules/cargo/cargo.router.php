<?php
//Define interface class for router
use \Psr\Http\Message\ServerRequestInterface as Request;        //PSR7 ServerRequestInterface   >> Each router file must contains this
use \Psr\Http\Message\ResponseInterface as Response;            //PSR7 ResponseInterface        >> Each router file must contains this

//Define your modules class
use \modules\cargo\Cargo as Cargo;                              //Your main modules class

//Define additional class for any purpose
use \classes\middleware\ApiKey as ApiKey;                       //ApiKey Middleware             >> To authorize request by using ApiKey generated by reSlim


    // Get module information
    $app->get('/cargo/get/info/', function (Request $request, Response $response) {
        $cm = new Cargo($this->db);
        $cm->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($cm->viewInfo());
        return classes\Cors::modify($response,$body,200);
    })->add(new ApiKey);

    // Installation 
    $app->get('/cargo/install/{username}/{token}', function (Request $request, Response $response) {
        $cm = new Cargo($this->db);
        $cm->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $cm->username = $request->getAttribute('username');
        $cm->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cm->install());
        return classes\Cors::modify($response,$body,200);
    });

    // Uninstall (This will clear all data) 
    $app->get('/cargo/uninstall/{username}/{token}', function (Request $request, Response $response) {
        $cm = new Cargo($this->db);
        $cm->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $cm->username = $request->getAttribute('username');
        $cm->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cm->uninstall());
        return classes\Cors::modify($response,$body,200);
    });