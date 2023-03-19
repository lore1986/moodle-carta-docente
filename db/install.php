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
 * paygw_docente installer script.
 *
 * @package   paygw_docente
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_paygw_docente_install()
{
    global $DB;

    $general_beni = array(
        'Libri e testi (anche in formato digitale)',
        'Hardware e software',
        'Formazione e aggiornamento',
        'Teatro',
        'Cinema',
        'Mostre ed eventi culturali',
        'Spettacoli dal vivo',
        'Musei',
    );

    foreach($general_beni as &$bene)
    {
        $o = array('name' => $bene);
        $DB->insert_record('paygw_docente_beni', $o);
    }
    
}
