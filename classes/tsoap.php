<?php
// This file is part of the docente paymnts module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Contains class for docente soap call.
 *
 * @package   paygw_docente
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_docente;
use stdClass;
use SoapClient;

require_once($CFG->libdir.'/adminlib.php');
require_once './lib.php';


class tsoap
{
    
    function __construct($codiceVoucher, $import, $conf)
    {
        if($conf->sandbox)
        {
            $this->local_cert = './certificates/sandbox/wccd-demo-certificate.pem';
            $this->location = 'https://wstest.cartadeldocente.istruzione.it/VerificaVoucherDocWEB/VerificaVoucher';
            $this->passphrase = 'm3D0T4aM';
        }else
        {
            $this->local_cert = './certificates/'. $conf->certificatename;
            $this->location = 'https://ws.cartadeldocente.istruzione.it/VerificaVoucherDocWEB/VerificaVoucher';
            $this->passphrase = $conf->passcertificate;
        }

        $this->wsdl = './certificates//VerificaVoucher.wsdl';
        $this->codiceVoucher = $codiceVoucher;
        $this->import = $import;
    }

    public function docente_createSoapClient()
    {   
        try {
            $soapClient = new SoapClient(
                $this->wsdl, 
                array(
                    'local_cert'     => $this->local_cert,
                    'location'       => $this->location,
                    'passphrase'     => $this->passphrase,
                    'stream_context' => stream_context_create(
                        array(
                            'http' => array(
                                'user_agent' => 'PHP/SOAP',
                            ),
                            'ssl' => array(
                                'verify_peer'       => false,
                                'verify_peer_name'  => false,
                            ),
                        )
                    ),
                )
            );
    
            return $soapClient;
    
        } catch (\SoapFault $s) {
            $err = new STDClass();
            $err->exceptionCode = $s->detail->FaultVoucher->exceptionCode;
            $err->exceptionMessage= $s->detail->FaultVoucher->exceptionMessage;

            return $err;
        }
        
    }
    
    /*
     * If operation is 1 just check voucher 
     * if operation == 2 check voucher and use the credit
    */ 
    public function docente_CheckVoucher($operation = 1) {

        $myclass = new stdClass();
        $myclass->valid = 0;
        
        try {
            $check = $this->docente_createSoapClient()->Check(array(
                'checkReq' => array(
                    'tipoOperazione' => $operation,
                    'codiceVoucher'  => $this->codiceVoucher,
                    'allow_self_signed' => true //remove on production
                )
            ));

            $myclass->valid = 1;
            $myclass->message = $check;

            return $myclass;

        } catch (\SoapFault $s) {

            $err = new STDClass();
            $err->exceptionCode = $s->detail->FaultVoucher->exceptionCode;
            $err->exceptionMessage= $s->detail->FaultVoucher->exceptionMessage;

            $myclass->valid = 0;
            $myclass->message = $err;

            return $myclass;
        } 
        
    }

    /*
     * operation performed when credit value is above requested price
     * only the correct amount is taken but the "buono" is not valid anymore
     * this are guidelines from the Ministero.
    */ 
    public function docente_confirm_partial() {
        
        $confirm = $this->docente_createSoapClient()->Confirm(array(
            'checkReq' => array(
                'tipoOperazione' => '1',
                'codiceVoucher'  => $this->codiceVoucher,
                'importo'=> $this->import
            )
        ));

        return $confirm;
    }


}