export class OptionSelectedEvent extends Event {
    static name = 'ssrn:option-selected';

    /**
     * @param {Option} option
     */
    constructor(option) {
        super(OptionSelectedEvent.name, {
            bubbles: true,
            cancelable: true
        });

        this.option = option;
    }
}

export class VariantResolvedEvent extends Event {
    static name = 'ssrn:variant-resolved';

    /**
     * @param {Option} option
     */
    constructor(option) {
        super(VariantResolvedEvent.name, {
            bubbles: true,
            cancelable: true
        });

        this.option = option;
    }
}
