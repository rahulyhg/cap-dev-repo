<?php
//Define interface class for router
use \Psr\Http\Message\ServerRequestInterface as Request;        //PSR7 ServerRequestInterface   >> Each router file must contains this
use \Psr\Http\Message\ResponseInterface as Response;            //PSR7 ResponseInterface        >> Each router file must contains this

//Define your modules class
use \modules\cargoagent\CargoAgent as CargoAgent;               //Your main modules class
use \modules\cargoagent\Transaction as Transaction;

//Define additional class for any purpose
use \classes\middleware\ValidateParam as ValidateParam;         //ValidateParam                 >> To validate the body form request  
use \classes\middleware\ValidateParamURL as ValidateParamURL;   //ValidateParamURL              >> To validate the query parameter url
use \classes\middleware\ApiKey as ApiKey;                       //ApiKey Middleware             >> To authorize request by using ApiKey generated by reSlim
use \classes\SimpleCache as SimpleCache;                        //SimpleCache class             >> To cache response ouput server side


    // Get module information
    $app->get('/cargoagent/get/info/', function (Request $request, Response $response) {
        $cm = new CargoAgent($this->db);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($cm->viewInfo());
        return classes\Cors::modify($response,$body,200);
    })->add(new ApiKey);

    // Installation 
    $app->get('/cargoagent/install/{username}/{token}', function (Request $request, Response $response) {
        $cm = new CargoAgent($this->db);
        $cm->username = $request->getAttribute('username');
        $cm->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cm->install());
        return classes\Cors::modify($response,$body,200);
    });

    // Uninstall (This will clear all data) 
    $app->get('/cargoagent/uninstall/{username}/{token}', function (Request $request, Response $response) {
        $cm = new CargoAgent($this->db);
        $cm->username = $request->getAttribute('username');
        $cm->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cm->uninstall());
        return classes\Cors::modify($response,$body,200);
    });


    //CRUD======================================================


    // POST api to create new transaction
    $app->post('/cargoagent/transaction/data/new', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $datapost = $request->getParsedBody();
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        
        $cargo->company_logo = $datapost['Company_logo'];
        $cargo->company_name = $datapost['Company_name'];
        $cargo->company_address = $datapost['Company_address'];
        $cargo->company_phone = $datapost['Company_phone'];
        $cargo->company_fax = $datapost['Company_fax'];
        $cargo->company_email = $datapost['Company_email'];
        $cargo->company_tin = $datapost['Company_tin'];
        $cargo->signature = $datapost['Signature'];

        $cargo->customerid = $datapost['CustomerID'];
        $cargo->consignor_name = $datapost['Consignor_name'];
        $cargo->consignor_alias = $datapost['Consignor_alias'];
        $cargo->consignor_address = $datapost['Consignor_address'];
        $cargo->consignor_phone = $datapost['Consignor_phone'];
        $cargo->consignor_fax = $datapost['Consignor_fax'];
        $cargo->consignor_email = $datapost['Consignor_email'];

        $cargo->referenceid = $datapost['ReferenceID'];
        $cargo->consignee_name = $datapost['Consignee_name'];
        $cargo->consignee_attention = $datapost['Consignee_attention'];
        $cargo->consignee_address = $datapost['Consignee_address'];
        $cargo->consignee_phone = $datapost['Consignee_phone'];
        $cargo->consignee_fax = $datapost['Consignee_fax'];

        $cargo->mode = $datapost['Mode'];
        $cargo->origin = $datapost['Origin'];
        $cargo->destination = $datapost['Destination'];
        $cargo->estimation = $datapost['Estimation'];

        $cargo->instruction = $datapost['Instruction'];
        $cargo->description = $datapost['Description'];
        $cargo->goods_data = $datapost['Goods_data'];
        $cargo->goods_koli = $datapost['Goods_koli'];
        $cargo->weight = $datapost['Weight'];
        $cargo->weight_real = $datapost['Weight_real'];

        $cargo->insurance_rate = $datapost['Insurance_rate'];
        $cargo->goods_value = $datapost['Goods_value'];

        $cargo->payment = $datapost['Payment'];
        $cargo->shipping_cost = $datapost['Shipping_cost'];
        $cargo->shipping_insurance = $datapost['Shipping_insurance'];
        $cargo->shipping_packing = $datapost['Shipping_packing'];
        $cargo->shipping_forward = $datapost['Shipping_forward'];
        $cargo->shipping_handling = $datapost['Shipping_handling'];
        $cargo->shipping_surcharge = $datapost['Shipping_surcharge'];
        $cargo->shipping_admin = $datapost['Shipping_admin'];
        $cargo->shipping_discount = $datapost['Shipping_discount'];
        $cargo->shipping_cost_total = $datapost['Shipping_cost_total'];

        $body = $response->getBody();
        $body->write($cargo->add());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Insurance_rate','0-7','decimal'))
        ->add(new ValidateParam('Goods_value','0-10','numeric'))
        ->add(new ValidateParam('Goods_data','0-1000'))
        ->add(new ValidateParam(['Company_logo','Instruction'],'0-250'))
        ->add(new ValidateParam(['CustomerID','ReferenceID'],'0-20'))
        ->add(new ValidateParam(['Company_fax','Consignor_fax','Consignee_fax'],'0-15','numeric'))
        ->add(new ValidateParam(['Company_email','Consignor_email'],'0-50','email'))
        ->add(new ValidateParam(['Company_tin','Consignor_alias','Consignee_attention'],'0-50'))
        ->add(new ValidateParam(['Shipping_cost','Shipping_insurance','Shipping_packing','Shipping_forward','Shipping_handling','Shipping_surcharge','Shipping_admin','Shipping_discount','Shipping_cost_total'],'1-10','numeric'))
        ->add(new ValidateParam('Goods_koli','1-5','numeric'))
        ->add(new ValidateParam(['Weight','Weight_real'],'1-7','decimal'))
        ->add(new ValidateParam(['Mode','Payment'],'1-50','required'))
        ->add(new ValidateParam('Estimation','1-7','numeric'))
        ->add(new ValidateParam(['Company_name','Signature','Consignor_name','Consignee_name','Origin','Destination','Username'],'1-50','required'))
        ->add(new ValidateParam(['Company_address','Consignor_address','Consignee_address','Description','Token'],'1-250','required'))
        ->add(new ValidateParam(['Company_phone','Consignor_phone','Consignee_phone'],'1-15','numeric'));

    // POST api to update transaction
    $app->post('/cargoagent/transaction/data/update', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $cargo->waybill = $datapost['Waybill'];
        
        $cargo->company_logo = $datapost['Company_logo'];
        $cargo->company_name = $datapost['Company_name'];
        $cargo->company_address = $datapost['Company_address'];
        $cargo->company_phone = $datapost['Company_phone'];
        $cargo->company_fax = $datapost['Company_fax'];
        $cargo->company_email = $datapost['Company_email'];
        $cargo->company_tin = $datapost['Company_tin'];
        $cargo->signature = $datapost['Signature'];

        $cargo->customerid = $datapost['CustomerID'];
        $cargo->consignor_name = $datapost['Consignor_name'];
        $cargo->consignor_alias = $datapost['Consignor_alias'];
        $cargo->consignor_address = $datapost['Consignor_address'];
        $cargo->consignor_phone = $datapost['Consignor_phone'];
        $cargo->consignor_fax = $datapost['Consignor_fax'];
        $cargo->consignor_email = $datapost['Consignor_email'];

        $cargo->referenceid = $datapost['ReferenceID'];
        $cargo->consignee_name = $datapost['Consignee_name'];
        $cargo->consignee_attention = $datapost['Consignee_attention'];
        $cargo->consignee_address = $datapost['Consignee_address'];
        $cargo->consignee_phone = $datapost['Consignee_phone'];
        $cargo->consignee_fax = $datapost['Consignee_fax'];

        $cargo->mode = $datapost['Mode'];
        $cargo->origin = $datapost['Origin'];
        $cargo->destination = $datapost['Destination'];
        $cargo->estimation = $datapost['Estimation'];

        $cargo->instruction = $datapost['Instruction'];
        $cargo->description = $datapost['Description'];
        $cargo->goods_data = $datapost['Goods_data'];
        $cargo->goods_koli = $datapost['Goods_koli'];
        $cargo->weight = $datapost['Weight'];
        $cargo->weight_real = $datapost['Weight_real'];

        $cargo->insurance_rate = $datapost['Insurance_rate'];
        $cargo->goods_value = $datapost['Goods_value'];

        $cargo->payment = $datapost['Payment'];
        $cargo->shipping_cost = $datapost['Shipping_cost'];
        $cargo->shipping_insurance = $datapost['Shipping_insurance'];
        $cargo->shipping_packing = $datapost['Shipping_packing'];
        $cargo->shipping_forward = $datapost['Shipping_forward'];
        $cargo->shipping_handling = $datapost['Shipping_handling'];
        $cargo->shipping_surcharge = $datapost['Shipping_surcharge'];
        $cargo->shipping_admin = $datapost['Shipping_admin'];
        $cargo->shipping_discount = $datapost['Shipping_discount'];
        $cargo->shipping_cost_total = $datapost['Shipping_cost_total'];

        $body = $response->getBody();
        $body->write($cargo->update());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Insurance_rate','0-7','decimal'))
        ->add(new ValidateParam('Goods_value','0-10','numeric'))
        ->add(new ValidateParam('Goods_data','0-1000'))
        ->add(new ValidateParam(['Company_logo','Instruction'],'0-250'))
        ->add(new ValidateParam(['CustomerID','ReferenceID'],'0-20'))
        ->add(new ValidateParam(['Company_fax','Consignor_fax','Consignee_fax'],'0-15','numeric'))
        ->add(new ValidateParam(['Company_email','Consignor_email'],'0-50','email'))
        ->add(new ValidateParam(['Company_tin','Consignor_alias','Consignee_attention'],'0-50'))
        ->add(new ValidateParam(['Shipping_cost','Shipping_insurance','Shipping_packing','Shipping_forward','Shipping_handling','Shipping_surcharge','Shipping_admin','Shipping_discount','Shipping_cost_total'],'1-10','numeric'))
        ->add(new ValidateParam('Goods_koli','1-5','numeric'))
        ->add(new ValidateParam(['Weight','Weight_real'],'1-7','decimal'))
        ->add(new ValidateParam(['Mode','Payment'],'1-50','required'))
        ->add(new ValidateParam('Estimation','1-7','numeric'))
        ->add(new ValidateParam(['Company_name','Signature','Consignor_name','Consignee_name','Origin','Destination','Username'],'1-50','required'))
        ->add(new ValidateParam(['Company_address','Consignor_address','Consignee_address','Description','Token'],'1-250','required'))
        ->add(new ValidateParam(['Company_phone','Consignor_phone','Consignee_phone'],'1-15','numeric'))
        ->add(new ValidateParam('Waybill','1-20','alphanumeric'));

    // POST api to delete transaction
    $app->post('/cargoagent/transaction/data/delete', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->waybill = $datapost['Waybill'];
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($cargo->delete());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Waybill','1-20','alphanumeric'))
        ->add(new ValidateParam('Token','1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // POST api to void transaction
    $app->post('/cargoagent/transaction/data/void', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $cargo->waybill = $datapost['Waybill'];
        $cargo->description = $datapost['Description'];
        $body = $response->getBody();
        $body->write($cargo->void());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Description','1-250','required'))
        ->add(new ValidateParam('Waybill','1-20','alphanumeric'))
        ->add(new ValidateParam('Token','1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // POST api to delivered transaction
    $app->post('/cargoagent/transaction/data/pod/delivered', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];

        $cargo->waybill = $datapost['Waybill'];
        $cargo->recipient = $datapost['Recipient'];
        $cargo->relation = $datapost['Relation'];
        $body = $response->getBody();
        $body->write($cargo->delivered());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Waybill','1-20','alphanumeric'))
        ->add(new ValidateParam(['Token','Relation'],'1-250','required'))
        ->add(new ValidateParam(['Username','Recipient'],'1-50','required'));

    // POST api to failed transaction
    $app->post('/cargoagent/transaction/data/pod/failed', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];

        $cargo->waybill = $datapost['Waybill'];
        $cargo->description = $datapost['Description'];
        $body = $response->getBody();
        $body->write($cargo->failed());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Description','1-250','required'))
        ->add(new ValidateParam('Waybill','1-20','alphanumeric'))
        ->add(new ValidateParam('Token','1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // POST api to returned transaction
    $app->post('/cargoagent/transaction/data/pod/returned', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];

        $cargo->waybill = $datapost['Waybill'];
        $body = $response->getBody();
        $body->write($cargo->returned('1'));
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Waybill','1-20','alphanumeric'))
        ->add(new ValidateParam('Token','1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // POST api to returned asked by consignor
    $app->post('/cargoagent/transaction/data/pod/returned/consignor', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $cargo->waybill = $datapost['Waybill'];
        $body = $response->getBody();
        $body->write($cargo->returned('2'));
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Waybill','1-20','alphanumeric'))
        ->add(new ValidateParam('Token','1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // POST api to returned asked by consignee
    $app->post('/cargoagent/transaction/data/pod/returned/consignee', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $datapost = $request->getParsedBody();    
        $cargo->username = $datapost['Username'];
        $cargo->token = $datapost['Token'];
        $cargo->waybill = $datapost['Waybill'];
        $body = $response->getBody();
        $body->write($cargo->returned('3'));
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Waybill','1-20','alphanumeric'))
        ->add(new ValidateParam('Token','1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // GET api to show data waybill registered user
    $app->get('/cargoagent/transaction/data/waybill/{username}/{token}/{waybill}', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $cargo->username = $request->getAttribute('username');
        $cargo->token = $request->getAttribute('token');
        $cargo->waybill = $request->getAttribute('waybill');
        $body = $response->getBody();
        $body->write($cargo->showWaybillDetail());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show data trace waybill registered user
    $app->get('/cargoagent/transaction/data/trace/waybill/{username}/{token}/', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $cargo->username = $request->getAttribute('username');
        $cargo->token = $request->getAttribute('token');
        $cargo->waybill = filter_var((empty($_GET['no'])?'':$_GET['no']),FILTER_SANITIZE_STRING);
        $body = $response->getBody();
        $body->write($cargo->traceWaybillDetail());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParamURL('no','1-20'));

    // GET api to show data trace waybill detail public
    $app->get('/cargoagent/transaction/data/public/trace/detail/waybill/', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $cargo->waybill = filter_var((empty($_GET['no'])?'':$_GET['no']),FILTER_SANITIZE_STRING);
        $response = $this->cache->withEtag($response, $this->etag.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body = $response->getBody();
        $body->write($cargo->traceWaybillDetailPublic());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParamURL('no','1-20'))->add(new ApiKey);

    // GET api to show data trace waybill simple public
    $app->get('/cargoagent/transaction/data/public/trace/simple/waybill/', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $cargo->waybill = filter_var((empty($_GET['no'])?'':$_GET['no']),FILTER_SANITIZE_STRING);
        $response = $this->cache->withEtag($response, $this->etag.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body = $response->getBody();
        $body->write($cargo->traceWaybillSimplePublic());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParamURL('no','1-20'))->add(new ApiKey);

    // GET api to show all data transaction pagination registered user
    $app->get('/cargoagent/transaction/data/search/{username}/{token}/{page}/{itemsperpage}/', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $cargo->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $cargo->firstdate = filter_var((empty($_GET['firstdate'])?'':$_GET['firstdate']),FILTER_SANITIZE_STRING);
        $cargo->lastdate = filter_var((empty($_GET['lastdate'])?'':$_GET['lastdate']),FILTER_SANITIZE_STRING);
        $cargo->username = $request->getAttribute('username');
        $cargo->token = $request->getAttribute('token');
        $cargo->page = $request->getAttribute('page');
        $cargo->itemsPerPage = $request->getAttribute('itemsperpage');
        $body = $response->getBody();
        $body->write($cargo->searchTransactionAsPagination());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParamURL(['query']))
    ->add(new ValidateParamURL(['firstdate','lastdate'],'','date'));

    // GET api to test generate waybill
    $app->get('/cargoagent/transaction/generate/waybill/{username}/{token}', function (Request $request, Response $response) {
        $cargo = new Transaction($this->db);
        $cargo->username = $request->getAttribute('username');;
        $cargo->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($cargo->getWaybillID());
        return classes\Cors::modify($response,$body,200);
    });