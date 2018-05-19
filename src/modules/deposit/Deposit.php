<?php

namespace modules\deposit;                          //Make sure namespace is same structure with parent directory

use \classes\Auth as Auth;                          //For authentication internal user
use \classes\JSON as JSON;                          //For handling JSON in better way
use \classes\CustomHandlers as CustomHandlers;      //To get default response message
use PDO;                                            //To connect with database

	/**
     * Deposit class
     *
     * @package    reSlim-modules
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2018 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class Deposit {
        // modules information var
        protected $information = [
            'package' => [
                'name' => 'Deposit',
                'uri' => 'https://github.com/aalfiann/reSlim-modules/tree/master/deposit',
                'description' => 'Deposit class',
                'version' => '1.0',
                'require' => [
                    'reSlim' => '1.9.0'
                ],
                'license' => [
                    'type' => 'MIT',
                    'uri' => 'https://github.com/aalfiann/reSlim-modules/tree/master/deposit/LICENSE.md'
                ],
                'author' => [
                    'name' => 'M ABD AZIZ ALFIAN',
                    'uri' => 'https://github.com/aalfiann'
                ],
            ]
        ];

        // database var
        protected $db;

        //master var
		var $username,$token;
        
        //construct database object
        function __construct($db=null) {
			if (!empty($db)) {
    	        $this->db = $db;
        	}
        }

        //Get modules information
        public function viewInfo(){
            return JSON::encode($this->information,true);
        }

        /**
         * Build database table 
         */
        public function install(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                $role = Auth::getRoleID($this->db,$this->token);
    		    try {
    				$this->db->beginTransaction();
	    			$sql = file_get_contents(dirname(__FILE__).'/deposit.sql');
					$stmt = $this->db->prepare($sql);
					if ($stmt->execute()) {
						$data = [
                            'status' => 'success',
							'code' => 'RS101',
                            'message' => CustomHandlers::getreSlimMessage('RS101')
						];	
					} else {
    					$data = [
					    	'status' => 'error',
				    		'code' => 'RS201',
			    			'message' => CustomHandlers::getreSlimMessage('RS201')
		    			];
	    			}
	    			$this->db->commit();
    			} catch (PDOException $e) {
			        $data = [
    	    			'status' => 'error',
	    				'code' => $e->getCode(),
    	    			'message' => $e->getMessage()
    			    ];
	    		    $this->db->rollBack();
    		    }
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
            }

			return JSON::encode($data,true);
			$this->db = null;
        }

        /**
         * Remove database table 
         */
        public function uninstall(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                $role = Auth::getRoleID($this->db,$this->token);
    		    try {
    				$this->db->beginTransaction();
	    			$sql = "DROP TABLE IF EXISTS deposit_balance;DROP TABLE IF EXISTS deposit_history;DROP TABLE IF EXISTS deposit_mutation;";
					$stmt = $this->db->prepare($sql);
					if ($stmt->execute()) {
						$data = [
							'status' => 'success',
							'code' => 'RS104',
							'message' => CustomHandlers::getreSlimMessage('RS104')
						];	
					} else {
    					$data = [
					    	'status' => 'error',
				    		'code' => 'RS204',
			    			'message' => CustomHandlers::getreSlimMessage('RS204')
		    			];
	    			}
	    			$this->db->commit();
    			} catch (PDOException $e) {
			        $data = [
    	    			'status' => 'error',
	    				'code' => $e->getCode(),
    	    			'message' => $e->getMessage()
    			    ];
	    		    $this->db->rollBack();
    		    }
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
            }

			return JSON::encode($data,true);
			$this->db = null;
        }


        //DEPOSIT============================================


        /**
         * Topup Deposit 
         */
        public function add(){

        }
    }