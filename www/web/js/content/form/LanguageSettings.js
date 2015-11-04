asm.ui.form.LanguageSettings = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			formProps: {
                submit : function(form,data)
                {
                    if (data.language === 'čeština')
                    {
                        cookies.set('language', 'cs');
                    }
                    else if (data.language === 'English')
                    {
                        cookies.set('language', 'en');
					}
                    window.location.reload(true);
                    return false;
                }
			},
			formStructure: { main: {
				icon: asm.ui.globals.icons.settings,
				caption: asm.lang.languageSettings.caption,
				fields: {
					language: {
						type: 'select',
						label: asm.lang.languageSettings.language,
                        hint : asm.lang.languageSettings.languageHint,
						value: (cookies.exists('language') && cookies.get('language') == 'cs' ? 'čeština' : 'English'),
						options: asm.ui.ArrayUtils.combine(['čeština', 'English'],['čeština', 'English'])
					}
				}
			}}
		};
		this.base($.extend(true, defaults, config));
	}
});