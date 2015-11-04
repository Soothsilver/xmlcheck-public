/**
 * Form for changing own user account properties.
 */
asm.ui.form.EmailSettings = asm.ui.DynamicForm.extend({
	constructor: function (config) {
		var defaults = {
			callbacks: {
				success: $.proxy(function (response, data) {
					asm.ui.globals.session.sendEmailOnAssignmentAvailableStudent(document.getElementById('sendEmailOnAssignmentAvailableStudent').checked ? "1" : "0");
                    asm.ui.globals.session.sendEmailOnSubmissionRatedStudent(document.getElementById('sendEmailOnSubmissionRatedStudent').checked ? "1" : "0");
                    asm.ui.globals.session.sendEmailOnSubmissionConfirmedTutor(document.getElementById('sendEmailOnSubmissionConfirmedTutor').checked ? "1" : "0");
					asm.ui.globals.stores.users.expire();
                    this._triggerError(new asm.ui.Error(asm.lang.emailNotifications.savedMessage, asm.ui.Error.NOTICE));
				}, this)
			},
			formStructure: { main: {
				icon: asm.ui.globals.icons.account,
				caption: asm.lang.emailNotifications.caption,
				fields: {
                    sendEmailOnSubmissionRatedStudent: {
						label: asm.lang.emailNotifications.whenRated,
						type: 'checkbox',
						hint: asm.lang.emailNotifications.whenRatedHint,
                        value: 'on'
					},
                    sendEmailOnAssignmentAvailableStudent: {
                        label: asm.lang.emailNotifications.whenGiven,
                        type: 'checkbox',
                        hint: asm.lang.emailNotifications.whenGivenHint,
                        value: 'on'
                    },
                    sendEmailOnSubmissionConfirmedTutor: {
                        label: asm.lang.emailNotifications.whenStudentConfirms,
                        type: 'checkbox',
                        hint: asm.lang.emailNotifications.whenStudentConfirmsHint,
                        value: 'on'
                    }
				}
			}},
			request: 'EditEmailNotificationOptions'
		};
		this.base($.extend(true, defaults, config));
	},
	_adjustContent: function () {
		var get = $.proxy(asm.ui.globals.session.getProperty, asm.ui.globals.session);
		this.fill({
			id: get('id'),
			name: get('username')
		});
        document.getElementById('sendEmailOnSubmissionRatedStudent').checked =
            (get('sendEmailOnSubmissionRatedStudent') == '1' );
        document.getElementById('sendEmailOnAssignmentAvailableStudent').checked =
            (get('sendEmailOnAssignmentAvailableStudent') == '1' );
        document.getElementById('sendEmailOnSubmissionConfirmedTutor').checked =
            (get('sendEmailOnSubmissionConfirmedTutor') == '1' );

	}
});