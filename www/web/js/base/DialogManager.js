/**
 * Manages display of modal dialog windows.
 * Ensures that only one window is displayed at a time (others wait in queue) and
 * provides means for dialog highlighting.
 */
asm.ui.DialogManager = Base.extend({
    /**
     * Initializes instance with supplied configuration.
     * @tparam object config configuration options
     *
     * All supplied configuration options are passed to
     * <a href="http://jqueryui.com/demos/dialog/">jQuery dialog widget</a>. New
     * defaults are set for some of the options:
     * @arg @a closeOnEscape true to close dialog window on @c [Esc] key pressed (default)
     * @arg @a draggable ... @c true
     * @arg @a minHeight ... 100
     * @arg @a minWidth ... 100
     * @arg @a resizable ... @c true
     * @arg @a width ... 400
     *
     * Following options can not be changed:
     * @arg @a autoOpen (@c false)
     * @arg @a modal (@c true)
     */
    constructor: function (config) {
        var defaults = {
            draggable: true,
            minHeight: 100,
            minWidth: 100,
            open: function () {
                $('button:first', $(this).siblings('.ui-dialog-buttonpane')).focus();
            },
            resizable: true,
            width: 400
        };
        this.config = $.extend(defaults, config);
        this.dialog = $('<div></div>')
          	.appendTo('body')
            .dialog($.extend(this.config, {
                autoOpen: false,
                modal: true
            }));
        this.container = this.dialog.parent('.ui-dialog');
        this.contentIcon = $('<div></div>')
            .icon()
            .appendTo(this.dialog);
        this.content = $('<div></div>')
            .appendTo(this.dialog);
        this.header = this.dialog.prev();
        this.headerIcon = $('<div></div>')
            .icon()
            .prependTo(this.header);
        /*
         this.closeButton = this.header.find('.ui-dialog-titlebar-close');
         this.closeButton.children('.ui-icon')
         .removeClass('ui-icon-closethick')
         .addClass('ui-icon-close');
         */
        this.busy = false;
        this.stack = [];

        this.dialog.bind('dialogclose', { 'DialogManager-instance': this }, function (event) {
            var self = event.data['DialogManager-instance'];
            self.busy = false;
            self._manage.call(self);
        });
    },
    /**
     * Stores configuration of dialog and shows it as soon as all previously stored
     * dialogs are closed.
     * @tparam mixed content to be inserted to dialog window body
     * @tparam object options following options (all optional):
     * @arg @a buttons (object) dialog buttons as accepted by dialog widget
     * @arg @a callback (function) to be called after dialog is closed
     * @arg @a icons (mixed) names of dialog body and header icons respectively
     *        (array), or just the name of body icon (string)
     * @arg @a scope (mixed) @a callback scope
     * @arg @a skin (string) dialog skin - @c highlight, @c error, or
     *        @c default (default)
     *    @arg @a title (string) dialog header title
     */
    show: function (content, options) {
        this._store(content, options);
        this._manage();
    },
    /**
     * Stores configuration of dialog to be shown later.
     * @tparam mixed content to be inserted to dialog window body
     * @tparam object options (see show())
     * @see _manage()
     */
    _store: function (content, options) {
        var defaults = {
            buttons: {},
            callback: $.noop,
            icons: [false, false],
            scope: window,
            skin: 'default',
            title: ''
        };
        this.stack.push([content, $.extend(defaults, options)]);
    },
    /**
     * Shows first dialog in the queue if none is currently shown.
     */
    _manage: function () {
        // If no dialog is displayed
        if (!this.busy) {
            this.busy = (this.stack.length > 0);
            // If there is a dialog in the queue
            if (this.busy) {
                var self = this;
                setTimeout(function () {
                    self._show.apply(self, self.stack.shift());
                }, 1);
            }
        }
    },
    /**
     * Shows dialog with supplied configuration & content.
     * @tparam mixed content to be inserted to dialog window body
     * @tparam object options (see show())
     */
    _show: function (content, options) {
        if (typeof options.icons == 'string') {
            options.icons = [options.icons];
        }
        //this.closeButton.blur();
        var containers = this.container.add(this.dialog)
            .removeClass('ui-background-solid')
            .removeClass('ui-state-highlight')
            .removeClass('ui-state-error');
        this.header.show()
            .removeClass('ui-state-highlight')
            .removeClass('ui-state-error');
        this.headerIcon.hide();
        this.dialog.removeClass('ui-labeled-icon');
        this.contentIcon.hide();

        switch (options.skin) {
            case 'highlight':
                this.header.addClass('ui-state-highlight');
                containers.addClass('ui-background-solid')
                    .addClass('ui-state-highlight');
                break;
            case 'error':
                this.header.addClass('ui-state-error');
                containers.addClass('ui-background-solid')
                    .addClass('ui-state-error');
                break;
        }

        if (options.icons[0]) {
            this.contentIcon.icon('option', 'type', options.icons[0])
                .show();
            this.dialog.addClass('ui-labeled-icon');
        }
        if (options.icons[1]) {
            this.headerIcon.icon('option', 'type', options.icons[1])
                .show();
            this.header.addClass('ui-labeled-icon');
        } else if (!options.title) {
            this.header.hide();
        }

        this.content.html(content);

        this.dialog.dialog('option', 'title', options.title)
            .dialog('option', 'buttons', options.buttons)
            .dialog('open');

        this.dialog.one('dialogclose.DialogManager', function () {
            options.callback.call(options.scope);
        });
    },
    /**
     * Shows simple message dialog.
     * @tparam mixed content see show()
     * @tparam string title @optional ^
     * @tparam mixed icons @optional ^
     *    @tparam string skin @optional ^
     *    @tparam function callback @optional ^
     *    @tparam mixed scope @optional ^
     */
    alert: function (content, title, icons, skin, callback, scope) {
        this.show(content, {
            callback: callback,
            icons: icons,
            scope: scope,
            skin: skin,
            title: title
        });
    },
    /**
     * Shows simple message dialog with default skin & "info" body icon.
     * @tparam mixed content see alert()
     * @tparam string title @optional ^ (defaults to 'Notice')
     *    @tparam function callback @optional ^
     *    @tparam mixed scope @optional ^
     */
    notice: function (content, title, callback, scope) {
        this.alert(content, title || 'Notice', 'info', 'default', callback, scope);
    },
    /**
     * Shows simple message dialog with highlight skin & "info" body icon.
     * @tparam mixed content see alert()
     * @tparam string title @optional ^ (defaults to 'Warning')
     *    @tparam function callback @optional ^
     *    @tparam mixed scope @optional ^
     */
    warning: function (content, title, callback, scope) {
        this.alert(content, title || 'Warning', 'info', 'highlight', callback, scope);
    },
    /**
     * Shows simple message dialog with error skin & "alert" body icon.
     * @tparam mixed content see alert()
     * @tparam string title @optional ^ (defaults to 'Error')
     *    @tparam function callback @optional ^
     *    @tparam mixed scope @optional ^
     */
    error: function (content, title, callback, scope) {
        this.alert(content, title || 'Error', 'alert', 'error', callback, scope);
    },
    /**
     * Shows confirmation dialog with default skin & no icons.
     * @tparam function callback to be called if dialog was "confirmed" (as opposed
     *        to "canceled")
     *    @tparam mixed content to be inserted to dialog body
     *    @tparam string title @optional dialog title (empty by default)
     */
    confirm: function (callback, content, title) {
        var buttons = {};
        buttons[asm.lang.general.yes] = function () {
            $(this).dialog('close');
            if ($.isFunction(callback)) {
                callback();
            }
        };
        buttons[asm.lang.general.no] = function () {
            $(this).dialog('close');
        };

        this.show(content, {
            buttons: buttons,
            title: title
        });
    },
    /**
     * Shows form dialog with supplied structure.
     * Dialog is closed on form submit, after @a callback is called.
     * @tparam function callback to be called on form submit
     * @tparam object formOptions following options:
     * @arg @a fields as accepted by @ref asm.ui.FormBuilder::_buildForm() "form builder"
     * @arg @a submitIcon @optional submit button icon (also used as dialog header
     *        icon)
     * @arg @a submitText @optional submit button text
     * @tparam string title @optional dialog header title
     */
    form: function (callback, formOptions, title) {
        var o = formOptions,
            formBuilder = new asm.ui.FormBuilder().extend({
                build: function (options) {
                    return this._buildForm(options);
                }
            });
        var form = formBuilder.build({ '': { fields: o.fields } })
            .form({
                blend: true,
                simple: true,
                submitIcon: o.submitIcon,
                submitText: o.submitText,
                submit: $.proxy(function (form, data) {
                    callback.call(form, data);
                    this.dialog.dialog('close');
                    return false;
                }, this)
            });
        this.show(form, {
            icons: [null, o.submitIcon],
            title: title
        });
    }
});