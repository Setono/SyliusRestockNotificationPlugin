/**
 * @typedef {import('./types.js').Product} Product
 */

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
     * @param {Product} product
     */
    show(product) {
        const element = this.#getElement();
        element.querySelector('.product-variant').value = product.code;
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
            this.#element.querySelector('.close').addEventListener('click', () => {
                this.#element.close();
            });
        }

        return this.#element;
    }
}
