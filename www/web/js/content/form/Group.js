/**
 * Add/edit group form.
 */
asm.ui.form.Group = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formStructure: { main: {
				icon: asm.ui.globals.icons.group,
				caption: asm.lang.groups.editCaption,
				fields: {
					id: {
						type: 'hidden'
					},
					lecture: {
						label: asm.lang.groups.lecture,
						type: 'select',
						hint: asm.lang.groups.lectureHint,
						check: 'isNotEmpty'
					},
					name: {
						label: asm.lang.groups.groupName,
						type: 'text',
						hint: asm.lang.groups.groupNameHint,
						check: [ 'isNotEmpty' ]
					},
					description: {
						label: asm.lang.groups.description,
						type: 'textarea',
						check: 'isNotEmpty'
					},
					'public': {
						label: asm.lang.groups.public,
						type: 'checkbox',
                        value: true,
                        hint : asm.lang.groups.publicHint
					}
				}
			}},
			request: 'EditGroup',
			stores: [asm.ui.globals.stores.lecturesLite]
		};
		if (config && config.stores) {
			$.merge(defaults.stores, config.stores);
			delete config.stores;
		}
		this.base($.extend(true, defaults, config));
	},
	_initContent: function () {
		this.setFieldOptions('lecture', asm.ui.Utils.tableToOptions(
				asm.ui.globals.stores.lecturesLite.get(), 'id', 'name'));

	}
});