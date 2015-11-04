asm.ui.form.OtherAdministration = asm.ui.DynamicForm.extend({
    constructor: function (config) {
        var defaults = {
            callbacks: {
                success: $.proxy(function(response, data) {
                     this._triggerError(new asm.ui.Error(response.text, asm.ui.Error.NOTICE));
                }, this)
            },
            formProps: {
                submitText: asm.lang.otherAdministration.reloadManifestsButton
            },
            formStructure: { main: {
                icon: asm.ui.globals.icons.log,
                caption: asm.lang.otherAdministration.reloadManifestsCaption,
                fields: {
                    description: {
                        label: asm.lang.otherAdministration.reloadManifestsLabel,
                        type: 'info',
                        value: asm.lang.otherAdministration.reloadManifestsDescription
                    }
                }
            }},
            request: 'ReloadManifests'
        };
        this.base($.extend(true, defaults, config));
    }
});