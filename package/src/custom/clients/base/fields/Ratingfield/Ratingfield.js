({

    /**
     * Ratingfield is a custom data type that can be configured in Studio.  This data type will show 5 stars for the
     * field.  The stars will be filled based on a calculated value.  The values can be 0 - 100, where partial stars
     * will be displayed if necessary.  A field of this data type will be read-only and populated by a calculation.
     *
     * For more information on how this field is used in Professor M's School for Gifted Coders, see
     * docs/ApplicationRatings.md.
     */

    /**
     * Called when initializing the field
     * @param options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
    },

    /**
     * Called when rendering the field
     * @private
     */
    _render: function() {
        this._super('_render');
    },

    /**
     * Called when formatting the value for display
     * @param value
     */
    format: function(value) {
		value = isNaN(value) ? 0 : value;
        this.percentage = value;
        return this._super('format', [value]);
    },

    /**
     * Called when unformatting the value for storage
     * @param value
     */
    unformat: function(value) {
        return this._super('unformat', [value]);
    }
})
