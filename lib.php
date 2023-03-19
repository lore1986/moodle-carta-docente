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
 * Plugin version and other meta-data are defined here.
 *
 * @package   paygw_docente
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



defined('MOODLE_INTERNAL') || die();
use paygw_docente\tsoap;


function docente_ispurchasable($bene, $ambiti)
{
    global $DB;

    $sql = "SELECT bg.name FROM {paygw_docente_beni} AS bg
            WHERE bg.id = :aid";

    $ambiti_names = array();

    if(!empty($ambiti))
    {
        foreach($ambiti as &$ambito)
        {
            $ambiti_names[] = strtoupper($DB->get_fieldset_sql($sql, ['aid' => ($ambito + 1)])[0]);
        }

        if(!empty($ambiti_names) && in_array($bene, $ambiti_names))
        {
            return 1;
        }

        return 0;

    }else
    {
        return 0;
    }
    
}


function paygw_docente_paywithCartaDocente($orderitem, $import, $codiceVoucher, $config)
{
    $output  = 1; 
    
    try {

        $soapClient = new tsoap($codiceVoucher, $import, $config);
        $response = $soapClient->docente_CheckVoucher();

        if($response->valid)
        {
            $bene          = strtoupper($response->message->checkResp->ambito); //il bene acquistabile con il buono inserit
            $importo_buono = floatval($response->message->checkResp->importo); //l'importo del buono inserito

            $purchasable = 0;

            if(!empty($config->ambiti) && $import != 0)
            {
                $purchasable = docente_ispurchasable($bene, $config->ambiti);
            }
            

            if ( ! $purchasable ) {
                
                $output = "Il buono non puo' essere utilizzato per l'acquisto del bene selezionato.";

                if($import == 0)
                {
                    $output = "Il bene selezionato e' gratuito. Contattare amministratore. L'acquisto non puo' essere effettuato";
                }
                
            } else {

                $type = null;

                if ( $importo_buono === $import ) {

                    $type = 'check';

                } else {

                    $type = 'confirm';

                }

                if ( $type ) {

                    try {
                        
                        $operation = $type === 'check' ? $soapClient->docente_CheckVoucher(2) : $soapClient->docente_confirm_partial();

                    } catch ( Exception $e ) {

                        $output = $e->detail->FaultVoucher->exceptionMessage;
                
                    } 

                }

            }

        }
        else{

            $output = $response->message->exceptionMessage;

            if($response->message->exceptionCode == '01')
            {
                $output = "Codice voucher non e' valido. Probabilmente c'e' un semplice errore nella trascrizione del codice.
                Controllare maiuscole e numeri. Molte grazie.";
            }

            

        }

        
    } catch ( Exception $e ) {

        $output = $e->detail->FaultVoucher->exceptionMessage;
    
    }

    return $output;
}
 