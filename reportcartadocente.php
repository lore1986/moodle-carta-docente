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


require_once __DIR__ . '/../../../config.php';
require_once './lib.php';

require_login();

global $DB;

$context = \context_system::instance(); 
$PAGE->set_context($context);
require_capability('paygw/docente:reportcartadocente', $context);

$PAGE->set_url('/payment/gateway/docente/reportcartadocente.php');
$PAGE->set_pagelayout('admin');

$pagetitle = 'Reports Carta del Docente Plugin'; //get_string('manage', 'paygw_docente');

$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$PAGE->navbar->add(get_string('pluginname', 'paygw_docente'), $PAGE->url);

echo $OUTPUT->header();

$sqlPdocente = 'SELECT * FROM {paygw_docente}';
$resultDocente = $DB->get_records_sql($sqlPdocente);

$trow = array();

$totalAmount = 0;

foreach ($resultDocente as $r) {

    $myR = array();
    $myR['description'] = $r->description;

    $sqlPinfo = $DB->get_record('payment_accounts', ['id' => $r->paymentaccountid]);
    $myR['accountname'] = $sqlPinfo->name;
    $sqlPI = $DB->get_record('payments', ['id' => $r->paymentid]);
    $paymentarea = $sqlPI->paymentarea;
    $idAC = $sqlPI->itemid;

    $myR['type'] = '';

    if($paymentarea === 'cmfee') 
    {
        $myR['type'] = 'Module Enrolment';

    }else if($paymentarea === 'fee')
    {
        $myR['type'] = 'Course Enrolment';
    }
    else
    {
        $myR['type'] = 'Unknown Enrolment Type: ' . $paymentarea;
    }
    
    $theUser = $DB->get_record("user", ["id" => $sqlPI->userid]);

    $myR['username'] = $theUser->username;
    $myR['amount'] = $sqlPI->amount;

    $totalAmount += $sqlPI->amount;

    $trow[] = $myR;
}


$post_url= new moodle_url($PAGE->url, array('sesskey'=>sesskey()));

$data = array();
$data['operations'] = count($trow);



$data['amounts'] = $totalAmount;
$data['trow'] = $trow;

echo $OUTPUT->render_from_template('paygw_docente/table_report', $data);

echo $OUTPUT->footer();
