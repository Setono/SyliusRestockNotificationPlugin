import Select from './select.js';
import { OptionSelectedEvent } from './events.js';

export default class Dialog {
    /**
     * If the dialog hasn't been shown before, the element is null
     *
     * @type {HTMLDialogElement|null}
     */
    #element = null;

    /**
     * @type {HTMLTemplateElement}
     */
    #template;

    /**
     * @param {string|HTMLTemplateElement} template
     */
    constructor(template) {
        if(typeof template === 'string') {
            template = document.querySelector(template);
        }

        this.#template = template;
    }

    /**
     * @param {string} variantCode
     */
    show(variantCode) {
        const element = this.#getElement();
        element.querySelector('.product-variant').value = variantCode;
        element.showModal();
    }

    /**
     * @returns {HTMLDialogElement}
     */
    #getElement() {
        if (null === this.#element) {
            document.body.appendChild(this.#template.content.cloneNode(true));

            const dialog = document.querySelector('dialog.ssrn-dialog');
            if (null === dialog) {
                throw new Error('Dialog template is missing dialog.ssrn-dialog element');
            }

            this.#element = dialog;
        }

        return this.#element;
    }
}
