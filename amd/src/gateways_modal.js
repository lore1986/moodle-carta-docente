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
 * This module is responsible for docente content in the gateways modal.
 *
 * @module     paygw_docente/gateway_modal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import ModalFactory from 'core/modal_factory';

/**
 * Creates and shows a modal that contains a placeholder.
 *
 * @returns {Promise<Modal>}
 */
const showModalWithPlaceholder = async() => {
    const modal = await ModalFactory.create({
        body: await Templates.render('paygw_docente/docente_button_placeholder', {})
    });
    modal.show();
};


/**
 * Process the payment.
 *
 * @param {string} component Name of the component that the itemId belongs to
 * @param {string} paymentArea The area of the component that the itemId belongs to
 * @param {number} itemId An internal identifier that is used by the component
 * @param {string} description Description of the payment
 * @returns {Promise<string>}
 */
export const process = (component, paymentArea, itemId, description) => {
    return showModalWithPlaceholder()
        .then(() => {
            location.href = M.cfg.wwwroot + '/payment/gateway/docente/pay.php?' +
                'component=' + component +
                '&paymentarea=' + paymentArea +
                '&itemid=' + itemId +
                '&description=' + description;
            return new Promise(() => null);
        });
};