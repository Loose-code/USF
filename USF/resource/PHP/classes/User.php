<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Peter
 */
class User {
    private $authorised = false;
    private $authority = 0;
    private $elevated = false;
    private $phpSession = null;
    private $dbConnection = null;
    
    public function __construct($userName, $password, $dbConnection) {
        /** @todo Stuff
         * - Check PHP Session Active, reject/error/log if not
         * - Validate credentials via PDO
         */
        ;
    }
    
    public function ValidateUser ($userName, $password) {
        $returnStatus = false;
        $returnValue = 'Invalid Credentials';
        /** @todo PDO Validate User Credentials **/
        // BYPASS HACK START
        $returnStatus = true;
        $returnValue = $this->GetUserDetails('brief');
        // BYPASS HACK END
        return [
            'status' => $returnStatus,
            'data' => $returnValue,
        ];
    }
    public function GetUserDetails($contentQuantity) {
        $returnStatus = false;
        $returnValue = 'Unspecified Error';
        
    }
