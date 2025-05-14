import Repository from './repository.js';
import Select from './select.js';
import { OptionSelectedEvent } from './events.js';

/**
 * @typedef {Object} SelectManagerOptions
 * @property {String} selector
 */
export default class SelectManager {
    /**
     * @type {Repository}
     */
    #repository;

    /**
     * @type SelectManagerOptions
     */
    #options;

    /**
     * @type {Select[]}
     */
    #selects = [];

    /**
     * @param {Repository} repository
     * @param {SelectManagerOptions} options
     */
    constructor(repository, options = {}) {
        this.#repository = repository;
        this.#options = Object.assign({
                selector: '.ssrn-select',
            },
            options
        );

        document.querySelectorAll(this.#options.selector).forEach(select => {
            this.#selects.push(new Select(select));
        });

        document.addEventListener(
            OptionSelectedEvent.name,
            /**
             * @param {OptionSelectedEvent} event
             */
            (event) => {
                // Update all other selects _after_ the one the user changed
                const lowerThreshold = this.#selects.findIndex(select => select === event.option.select);

                this.#selects.filter((select, index) => index > lowerThreshold).forEach(select => {
                    this.updateSelect(select);
                });
            }
        );
    }

    /**
     * @param {Select} select
     */
    updateSelect(select) {
        /** @type {string[]} */
        const optionCombination = [];
        this.#selects.filter((s) => s.isSelected() && s !== select).forEach(selectedSelected => {
            optionCombination.push(selectedSelected.getSelectedValue());
        });

        select.options.forEach(option => {
            const concreteOptionCombination = optionCombination.concat(option.getValue());
            const variant = this.#repository.getVariantFromOptionCombination(concreteOptionCombination);
            if(null !== variant) {
                option.setInStock(variant.inStock);
            }
            option.setAvailable(this.#repository.hasOptionCombination(concreteOptionCombination));
        });
    }
}
