// Standard license omitted.
/**
 * @module     paygw_docente
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalFactory from 'core/modal_factory';
import Templates from 'core/templates';

export const init = () => {
    alert('we have been started');
};

const createModal = async($d) => {
    ModalFactory.create({
        title:'Pagamento confermato',
        body: '',
    }).done(function(modal) {
      modal.setBody(Templates.render('paygw_docente/modal_confirm', $d));
      modal.show();
   });
};

export const processmodal = (d) => {
    return createModal(d);
};

