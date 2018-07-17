<?php
//Define interface class for router
use \Psr\Http\Message\ServerRequestInterface as Request;        //PSR7 ServerRequestInterface   >> Each router file must contains this
use \Psr\Http\Message\ResponseInterface as Response;            //PSR7 ResponseInterface        >> Each router file must contains this

//Define your modules class
use \modules\cargoagent\CargoAgent as CargoAgent;               //Your main modules class

//Define additional class for any purpose
use \classes\middleware\ApiKey as ApiKey;                       //ApiKey Middleware             >> To authorize request by using ApiKey generated by reSlim


    // Get module information
    $app->map(['GET','OPTIONS'],'/cargoagent/get/info/', function (Request $request, Response $response) {
        $cargo = new CargoAgent($this->db);
        $cargo->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($cargo->viewInfo());
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ApiKey);

    // Installation 
    $app->get('/cargoagent/install/{username}/{token}', function (Request $request, Response $response) {
        $cargo = new CargoAgent($this->db);
        $cargo->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $cargo->username = $request->getAttribute('username');
        $cargo->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cargo->install());
        return classes\Cors::modify($response,$body,200);
    });

    // Uninstall (This will clear all data) 
    $app->get('/cargoagent/uninstall/{username}/{token}', function (Request $request, Response $response) {
        $cargo = new CargoAgent($this->db);
        $cargo->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $cargo->username = $request->getAttribute('username');
        $cargo->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cargo->uninstall());
        return classes\Cors::modify($response,$body,200);
    });