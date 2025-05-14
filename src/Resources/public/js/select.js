import Option from './option.js';
import Trigger from './trigger.js';
import ValueHolder from './value-holder.js';
import {OptionSelectedEvent} from "./events.js";

/**
 * This class represents a custom <select> as created by the form theme in this plugin
 */
export default class Select {
    /**
     * @type {HTMLElement}
     */
    #element;

    /**
     * Holds the actual value and updates the underlying form element (which can be either a <input type="hidden"> or a <select>)
     *
     * @type {ValueHolder}
     */
    #valueHolder;

    /**
     * @type {Trigger}
     */
    #trigger;

    /**
     * @type {Option[]}
     */
    options= [];

    /**
     * @type {Option|null}
     */
    #selected= null;

    /**
     * todo we need to be able to inject the selectors in an options argument
     *
     * @param select {HTMLSelectElement}
     */
    constructor(select) {
        this.#element = select;
        this.#valueHolder = new ValueHolder(document.getElementById(this.#element.dataset.valueHolder));
        this.#trigger = new Trigger(this.#element.querySelector('.ssrn-select-trigger'));

        this.#element.querySelectorAll('.ssrn-option').forEach(option => {
            this.options.push(new Option(this, option));
        });

        // Toggle select on trigger click
        this.#trigger.getElement().addEventListener('click', () => {
            this.setOpen(!this.isOpen());
        });

        // Close select when clicking outside
        document.addEventListener('click', (event) => {
            if (!this.#element.contains(event.target)) {
                this.setOpen(false);
            }
        });

        this.#element.addEventListener(
            OptionSelectedEvent.name,
            /** @param {OptionSelectedEvent} event */
            (event) => {
                this.#selected = event.option;

                this.#trigger.setValue(event.option.getLabel());

                this.options.forEach(option => {
                    option.setSelected(option === event.option);
                });

                this.setOpen(false);

                this.#valueHolder.setValue(event.option.getValue());
            }
        );
    }

    isSelected() {
        return null !== this.#selected;
    }

    /**
     * @returns {string|null}
     */
    getSelectedValue() {
        return this.#selected?.getValue();
    }

    setOpen(open = true) {
        this.#element.classList.toggle('opened', open);
    }

    isOpen() {
        return this.#element.classList.contains('opened');
    }

    reset() {
        this.#selected?.setSelected(false);
        this.#selected = null;

        this.#trigger.reset();
        this.#valueHolder.reset();
    }
}
