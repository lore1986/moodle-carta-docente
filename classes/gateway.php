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
 * Contains class for docente payment gateway.
 * @package   paygw_docente
*/

namespace paygw_docente;


class gateway extends \core_payment\gateway
{
    public static function get_supported_currencies(): array {
        // See https://developer.paypal.com/docs/api/reference/currency-codes/,
        // 3-character ISO-4217: https://en.wikipedia.org/wiki/ISO_4217#Active_codes.
        return [
            'EUR'
        ];
    }

    /**
     * Configuration form for the gateway instance
     *
     *
     * @param \core_payment\form\account_gateway $form
     */

    public static function add_configuration_to_gateway_form(\core_payment\form\account_gateway $form): void
    {
        global $DB;

        $mform = $form->get_mform();
        
        $mform->addElement('advcheckbox', 'sandbox', get_string('sandbox_enable', 'paygw_docente'), 
            get_string('sandbox_label', 'paygw_docente'), array('group' => 1), array(0, 1));
        $mform->setDefault('sandbox', 1);
        
        $mform->addElement('text', 'certificatename', get_string('insert_certificate_name', 'paygw_docente'));
        $mform->setType('certificatename', PARAM_TEXT);
        $mform->setDefault('certificatename', 'etic.pem');
        
        $mform->addElement('passwordunmask', 'passcertificate', get_string('password_input', 'paygw_docente'));
        $sql = "SELECT bg.name FROM {paygw_docente_beni} AS bg";

        $general_beni = $DB->get_fieldset_sql($sql);
        
        $attributes = array('size' => 12);
        $select = $mform->addElement('select', 'ambiti', get_string('select_ambito', 'paygw_docente'), $general_beni, $attributes);
        $select->setMultiple(true);
    }

    /**
     * Validates the gateway configuration form.
     *
     * @param \core_payment\form\account_gateway $form
     * @param \stdClass                          $data
     * @param array                              $files
     * @param array                              $errors form errors (passed by reference)
     */
    public static function validate_gateway_form(
        \core_payment\form\account_gateway $form,
        \stdClass $data,
        array $files,
        array &$errors
    ): void {
        

        if (!$data->enabled ) {
            $errors['enabled'] = "Please enable docente account";
        }

        if(empty($data->ambiti))
        {
            $errors['ambiti'] = "Please select at leas one of the options for the products you can sell";
        }
        
        if(!strlen($data->certificatename))
        {
            $errors['certificatename'] = "Please specify name of one of the certificate you uploaded";
        }

        if(!strlen($data->certificatename))
        {
            $errors['passcertificate'] = "Please insert password for the specified certificate";
        }
    }
}
