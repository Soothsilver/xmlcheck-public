/**
 * @copybrief Builder
 * 
 * Adds method to build form pseudo-widget.
 */
asm.ui.FormBuilder = asm.ui.Builder.extend({
	/**
	 * Creates form consisting of fieldset widgets with field pseudo-widgets
	 * inside from supplied data.
	 * @tparam object sets object with following properties:
	 * @arg @a icon @optional name of icon to be used in fieldset header
	 * @arg @a caption @optional fieldset header title
	 * @arg @a fields object with field names as keys and configuration objects
	 *		to be supplied to field widget constructor as values (field name will
	 *		be added to configuration automatically)
	 */
	_buildForm: function (sets) {
		var form = this._buildTag('form');
		$.each(sets, function (i, o) {
			var set = $('<fieldset></fieldset>')
				.appendTo(form);
			$.each(o.fields, function (name, config) {
				$('<div></div>')
					.appendTo(set)
					.field($.extend(config, { name: name }));
			});
			set.fieldset({
				icon: o.icon,
				label: o.caption
			});
		});
		return form;
	}
});