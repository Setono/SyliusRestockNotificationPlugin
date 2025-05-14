/**
 * @typedef {Object} Product
 * @property {string} code
 * @property {boolean} inStock
 */

/**
 * OptionReferences is a map where
 * - each key is an option value code, and
 * - each value is either:
 *    • a string leaf (variant code), or
 *    • another OptionReferences map for deeper nesting.
 *
 * @typedef {{ [optionValueCode: string]: string | OptionReferences }} OptionReferences
 */

/**
 * @typedef {Object} RepositorySchema
 * @property {{ [variantCode: string]: Product }} variants
 * @property {OptionReferences} optionReferences
 */
export default class Repository {
    /**
     * @type {RepositorySchema}
     */
    #data;

    /**
     * @param {RepositorySchema} data
     */
    constructor(data) {
        this.#data = data;
    }

    /**
     * @param {string|HTMLScriptElement} element
     */
    static fromElement(element) {
        if(typeof element === 'string') {
            element = document.querySelector(element);
        }

        return new Repository(JSON.parse(element.textContent));
    }

    /**
     * @param {string[]} options
     * @return {Product|null}
     */
    getVariantFromOptionCombination(options) {
        let optionReferences = this.#data.optionReferences;

        for(const option of options) {
            if(!Object.hasOwn(optionReferences, option)) {
                return null;
            }
            optionReferences = optionReferences[option];
            if(typeof optionReferences === 'string') {
                return this.#data.variants[optionReferences];
            }
        }

        return null;
    }

    /**
     * @param {string[]} options
     * @return {boolean}
     */
    hasOptionCombination(options) {
        let optionReferences = this.#data.optionReferences;

        for (const option of options) {
            if (!Object.hasOwn(optionReferences, option)) {
                return false;
            }
            optionReferences = optionReferences[option];
            if (typeof optionReferences === 'string') {
                return true;
            }
        }

        return true;
    }
}
