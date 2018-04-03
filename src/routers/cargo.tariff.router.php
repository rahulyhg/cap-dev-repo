<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\SimpleCache as SimpleCache;

    // POST api to create new cargo tariff data
    $app->post('/cargo/tariff/data/new', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $datapost = $request->getParsedBody();
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $cargo->branchid = $datapost['BranchID'];
        $cargo->kabupaten = $datapost['Kabupaten'];
        $cargo->kgp = $datapost['KGP'];
        $cargo->kgs = $datapost['KGS'];
        $cargo->minkg = $datapost['Minkg'];
        $cargo->estimasi = $datapost['Estimasi'];
        $body = $response->getBody();
        $body->write($cargo->addTariff());
        return classes\Cors::modify($response,$body,200);
    });

    // POST api to update cargo tariff data
    $app->post('/cargo/tariff/data/update', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $cargo->branchid = $datapost['BranchID'];
        $cargo->kabupaten = $datapost['Kabupaten'];
        $cargo->kgp = $datapost['KGP'];
        $cargo->kgs = $datapost['KGS'];
        $cargo->minkg = $datapost['Minkg'];
        $cargo->estimasi = $datapost['Estimasi'];
        $body = $response->getBody();
        $body->write($cargo->updateTariff());
        return classes\Cors::modify($response,$body,200);
    });

    // POST api to delete cargo tariff data
    $app->post('/cargo/tariff/data/delete', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->branchid = $datapost['BranchID'];
        $cargo->kabupaten = $datapost['Kabupaten'];
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($cargo->deleteTariff());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show all tariff data pagination registered user
    $app->get('/cargo/tariff/data/search/{username}/{token}/{page}/{itemsperpage}/', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $cargo->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $cargo->username = $request->getAttribute('username');
        $cargo->token = $request->getAttribute('token');
        $cargo->page = $request->getAttribute('page');
        $cargo->itemsPerPage = $request->getAttribute('itemsperpage');
        $body = $response->getBody();
        $body->write($cargo->searchTariffAsPagination());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show all list origin tariff
    $app->get('/cargo/tariff/data/list/origin/search/{token}/', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $cargo->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $cargo->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cargo->listOrigin());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show all list destinasi tariff
    $app->get('/cargo/tariff/data/list/destination/search/{token}/', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $cargo->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $cargo->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cargo->listDestination());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to search tariff
    $app->get('/cargo/tariff/data/get/search/{token}/', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $cargo->origin = filter_var((empty($_GET['origin'])?'':$_GET['origin']),FILTER_SANITIZE_STRING);
        $cargo->destination = filter_var((empty($_GET['destination'])?'':$_GET['destination']),FILTER_SANITIZE_STRING);
        $cargo->weight = filter_var((empty($_GET['weight'])?'':$_GET['weight']),FILTER_SANITIZE_STRING);
        $cargo->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cargo->searchTariff());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to search tariff public
    $app->get('/cargo/tariff/data/get/public/search/', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $cargo->origin = filter_var((empty($_GET['origin'])?'':$_GET['origin']),FILTER_SANITIZE_STRING);
        $cargo->destination = filter_var((empty($_GET['destination'])?'':$_GET['destination']),FILTER_SANITIZE_STRING);
        $cargo->weight = filter_var((empty($_GET['weight'])?'':$_GET['weight']),FILTER_SANITIZE_STRING);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(600,["apikey","origin","destination","weight"])){
            $datajson = SimpleCache::load(["apikey","origin","destination","weight"]);
        } else {
            $datajson = SimpleCache::save($cargo->searchTariffPublic(),["apikey","origin","destination","weight"]);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new \classes\middleware\ApiKey());

    // GET api to show all list origin tariff public
    $app->get('/cargo/tariff/data/list/origin/public/search/', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $cargo->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(600,["apikey"])){
            $datajson = SimpleCache::load(["apikey"]);
        } else {
            $datajson = SimpleCache::save($cargo->listOriginPublic(),["apikey"]);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new \classes\middleware\ApiKey());

    // GET api to show all list destinasi tariff public
    $app->get('/cargo/tariff/data/list/destination/public/search/', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $cargo->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(600,["apikey"])){
            $datajson = SimpleCache::load(["apikey"]);
        } else {
            $datajson = SimpleCache::save($cargo->listDestinationPublic(),["apikey"]);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new \classes\middleware\ApiKey());

    // POST api to create new cargo tariff handling
    $app->post('/cargo/tariff/handling/data/new', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $datapost = $request->getParsedBody();
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $cargo->kabupaten = $datapost['Kabupaten'];
        $cargo->kgp = $datapost['KGP'];
        $cargo->kgs = $datapost['KGS'];
        $cargo->minkg = $datapost['Minkg'];
        $body = $response->getBody();
        $body->write($cargo->addHandling());
        return classes\Cors::modify($response,$body,200);
    });

    // POST api to update cargo tariff handling
    $app->post('/cargo/tariff/handling/data/update', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $cargo->kabupaten = $datapost['Kabupaten'];
        $cargo->kgp = $datapost['KGP'];
        $cargo->kgs = $datapost['KGS'];
        $cargo->minkg = $datapost['Minkg'];
        $body = $response->getBody();
        $body->write($cargo->updateHandling());
        return classes\Cors::modify($response,$body,200);
    });

    // POST api to delete cargo tariff handling
    $app->post('/cargo/tariff/handling/data/delete', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $datapost = $request->getParsedBody();
        $cargo->kabupaten = $datapost['Kabupaten'];
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($cargo->deleteHandling());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show all tariff data pagination registered user
    $app->get('/cargo/tariff/handling/data/search/{username}/{token}/{page}/{itemsperpage}/', function (Request $request, Response $response) {
        $cargo = new classes\system\cargo\Tariff($this->db);
        $cargo->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $cargo->username = $request->getAttribute('username');
        $cargo->token = $request->getAttribute('token');
        $cargo->page = $request->getAttribute('page');
        $cargo->itemsPerPage = $request->getAttribute('itemsperpage');
        $body = $response->getBody();
        $body->write($cargo->searchHandlingAsPagination());
        return classes\Cors::modify($response,$body,200);
    });