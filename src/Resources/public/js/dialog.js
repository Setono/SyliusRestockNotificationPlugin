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

        element.querySelector('form').addEventListener('submit', (event) => {
            event.preventDefault();

            /** @type {HTMLFormElement} */
            const form = event.target;

            fetch(form.action, {
                method: form.method || 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then((result) => {
                    if(result.success) {
                        element.classList.remove('error');
                        element.classList.add('success');
                    } else {
                        element.classList.remove('success');
                        element.classList.add('error');
                        element.querySelector('.ssrn-error-message').innerHTML = result.errors.join('<br>');
                    }
                })
                .catch((error) => {
                    element.classList.add('error');
                    console.error(error.message);
                });
        });

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
