({
    /**
     * Add custom validation to the Applicants (Leads) Create View.
     * Note: If you add validation here, you probably want to add it to the Record view as well in
     * custom/modules/Leads/clients/base/views/record/record.js
     */
    extendsFrom: 'LeadsCreateView',

    initialize: function (options) {

        this._super('initialize', [options]);

        app.error.errorName2Keys['gpa_error'] = 'ERROR_GPA_NOT_IN_RANGE';

        //add validation tasks
        this.model.addValidationTask('check_GPA', _.bind(this._doValidateGPA, this));
    },

    /**
     * Validate the GPA to ensure it is at least 0 and not higher than 4.0
     * @private
     */
    _doValidateGPA: function(fields, errors, callback) {

        if (this.model.get('gpa_c') < 0 ||  this.model.get('gpa_c') > 4)
        {
            errors['gpa_c'] = errors['gpa_c'] || {};
            errors['gpa_c'].gpa_error = true;
        }

        callback(null, fields, errors);
    },
})
