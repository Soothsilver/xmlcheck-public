/**
 * Base of form panels with ability to interact with server.
 */
asm.ui.DynamicForm = asm.ui.DynamicContentPanel.extend({
	/**
	 * @copydoc DynamicContentPanel::DynamicContentPanel()
	 *
	 * Additional configuration options:
	 * @arg @a callbacks object with three optional properties @c completion,
	 *		@c failure, and @c success that will be called on submit request
	 *		completion, failure, and success respectively
	 * @arg @a defaultValues default form field values
	 * @arg @a formProps options passed to used @ref widget.form "form widget" on creation
	 * @arg @a formStructure data used for form creation
	 * @arg @a request name of core request used to handle submitted form data
	 */
	constructor: function (config) {
		var defaults = {
			callbacks: {
				completion: $.noop,
				failure: $.noop,
				success: $.noop
			},
			defaultValues: {},
			formProps: {},
			formStructure: {},
			request: null
		};
		this.base($.extend(true, defaults, config));
	},
	/**
	 * @copybrief DynamicContentPanel::_buildContent()
	 *
	 * Builds form using structure from configuration and creates @ref widget.form "form widget"
	 * from it with special submit function that interacts properly with server.
	 * Fills created form with default values form configuration.
	 */
	_buildContent: function () {
		var o = this.config;

		this._formElem = this._buildForm(o.formStructure)
			.form($.extend({}, {
				submit: $.proxy(function (form, data) {
					if (o.preSubmitAction)
					{
						o.preSubmitAction(form, data);
					}
					var nonEditableFields = $();
					$(form).form('getFields').each(function () {
						var field = $(this);
						if (!field.field('option', 'editable')) {
							nonEditableFields = nonEditableFields.add(field);
						}
					});
					nonEditableFields.field('option', 'editable', true);
					webtoolkit.Aim.submit(form, {onComplete: $.proxy(function (result) {
						asm.ui.globals.coreCommunicator.handleResult(result, $.proxy(function (response, errors) {							
							this.config.callbacks.success(response, data);
							this.trigger('form.success', { response: response, data: data });
						}, this), $.proxy(function () {
							this.config.callbacks.failure();
							this.trigger('form.failure');
						}, this), $.proxy(function (errors) {
							if (errors.length) {
								this._triggerError(errors);
							}
							this.config.callbacks.completion.apply(this, arguments);
							nonEditableFields.field('option', 'editable', false);
						}, this), o.request, data);
					}, this)});
				}, this)
			}, o.formProps))
			.attr({
				action: asm.ui.globals.coreUrl,
				method: 'post',
				enctype: 'multipart/form-data'
			})
			.appendTo(o.target);

		this.fill(o.defaultValues);

		$('<input type="hidden"/>').attr('name', 'action')
			.val(o.request)
			.appendTo(this._formElem);
	},
	destroy: function () {
		this.form('destroy');
	},
	/**
	 * Fills form with supplied values.
	 * @tparam object values field names as keys, values as values
	 */
	fill: function (values) {
		this.form('fill', values || {});
	},
	/**
	 * Calls @ref widget.form "form widget" method on form.
	 * @tparam mixed [...] arguments passed to widget method
	 * @returns mixed widget method output
	 */
	form: function () {
		return this._formElem.form.apply(this._formElem, arguments);
	},
	/**
	 * Calls @ref widget.field "field widget" method on selected field.
	 * @tparam string fieldName field @ref widget.field::name "name"
	 * @tparam mixed [...] arguments passed to widget method
	 * @treturn mixed widget method output
	 */
	field: function (fieldName) {
		var args = Array.prototype.slice.call(arguments, 1),
			fieldEl = this.form('getFieldByName', fieldName);
		return fieldEl.field.apply(fieldEl, args);
	},
	/**
	 * Calls @ref widget.field "field widget" method on all form fields.
	 * @tparam mixed [...] arguments passed to widget method
	 * @treturn mixed widget method output
	 */
	fields: function () {
		var fields = this._formElem.form('getFields');
		return fields.field.apply(fields, arguments);
	},
	/**
	 * Sets field value options for selected field.
	 * @tparam string fieldName field @ref widget.field::name "name"
	 * @tparam object options field @ref widget.field::options "options"
	 */
	setFieldOptions: function (fieldName, options) {
		this.field(fieldName, 'option', 'options', options);
	},

	setFieldValue: function(fieldName, fieldValue) {
		this.field(fieldName, 'option', 'value', fieldValue);
	}
});
asm.ui.DynamicForm.implement(asm.ui.FormBuilder);