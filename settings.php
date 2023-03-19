<?php

// This file is part of the voucher paymnts module for Moodle - http://moodle.org/
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
 * Settings for the voucher payment gateway
 *
 * @package   paygw_docente
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


if ($ADMIN->fulltree) {
    \core_payment\helper::add_common_gateway_settings($settings, 'paygw_docente');
}

$settings->add(new \paygw_docente\admin_setting_link('paygw/reportdocente',
    get_string('reportdocente', 'paygw_docente'), get_string('reportdocentedesc', 'paygw_docente'),
    get_string('reportdocente', 'paygw_docente'), new moodle_url('/payment/gateway/docente/reportcartadocente.php'), 'paygw/docente:reportcartadocente'));
    
    
    