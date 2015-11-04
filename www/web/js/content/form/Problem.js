/**
 * Add/edit problem form.
 */
asm.ui.form.Problem = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formStructure: { main: {
				icon: asm.ui.globals.icons.problem,
				caption: asm.lang.problems.editCaption,
				fields: {
					id: {
						type: 'hidden'
					},
					lecture: {
						label: asm.lang.problems.lecture,
						type: 'select',
						hint: asm.lang.problems.lectureHint,
						check: 'isNotEmpty'
					},
					name: {
						label: asm.lang.problems.problemName,
						type: 'text',
						check: [ 'isNotEmpty' ]
					},
					description: {
						label: asm.lang.problems.description,
						type: 'textarea',
						hint: asm.lang.problems.descriptionHint,
						check: 'isNotEmpty'
					},
					pluginId: {
						label: asm.lang.problems.correctivePlugin,
						type: 'select',
						check: 'isNotEmpty'
					},
					pluginArguments: {
						label: asm.lang.problems.pluginConfiguration,
						type: 'text'
					}
				}
			}},
			request: 'EditProblem',
			stores: [asm.ui.globals.stores.lectures, asm.ui.globals.stores.plugins]
		};
		if (config && config.stores) {
			$.merge(defaults.stores, config.stores);
			delete config.stores;
		}
		this.base($.extend(true, defaults, config));
	},
	_initContent: function () {
		this.setFieldOptions('lecture',
				asm.ui.Utils.tableToOptions(asm.ui.globals.stores.lectures.get(), 'id', 'name'));

		var plugins = asm.ui.globals.stores.plugins.get(),
			pluginArgs = $.extend({'0': ''}, asm.ui.Utils.tableToOptions(plugins, 'id', 'arguments'));

		this.setFieldOptions('pluginId', $.extend({'0': asm.lang.problems.noPlugin},
				asm.ui.Utils.tableToOptions(plugins, 'id', 'name')));

		var pluginSelectEl = this.form('getFieldByName', 'pluginId');
		var pluginConfigEl = this.form('getFieldByName', 'pluginArguments');
		pluginSelectEl.unbind('change.pageInit').bind('change.pageInit', function () {
			var pluginId = pluginSelectEl.field('option', 'value'),
				params = pluginId ? pluginArgs[pluginId].split(';').join(', ') : '',
				enableConfig = (pluginId && (pluginId != '0') && params),
				hint = params ? asm.lang.problems.pluginConfigurationHint + params : asm.lang.problems.pluginHasNoArguments;
			pluginConfigEl.field('option', 'hint', (pluginId != '0') ? hint : '')
				.field('option', 'editable', enableConfig);
		}).change();
	}
});