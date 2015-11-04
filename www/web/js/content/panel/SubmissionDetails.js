asm.ui.form.SubmissionInfoForm = asm.ui.DynamicForm.extend({
    constructor: function (config) {
        var defaults = {
            formProps: {
                offline: true
            },
            formStructure: { main: {
                icon: asm.ui.globals.icons.submissionDetails,
                caption: asm.lang.submissionDetails.formCaption,
                fields: {
                    realName: {
                        label: asm.lang.submissionDetails.realName,
                        type: 'info',
                        value: asm.lang.general.loadingData
                    },
                    email: {
                        label: asm.lang.submissionDetails.email,
                        type: 'info',
                        value: asm.lang.general.loadingData,
                    },
                    points: {
                        label: asm.lang.submissionDetails.pointsAwarded,
                        type: 'info',
                        value: asm.lang.general.loadingData
                    },
                    details: {
                        label: asm.lang.submissionDetails.autoCorrectDetails,
                        type: 'info',
                        value: asm.lang.general.loadingData
                    },
                    uploaded: {
                        label: asm.lang.submissionDetails.submissionDate,
                        type: 'info',
                        value: asm.lang.general.loadingData
                    },
                    status: {
                        label: asm.lang.submissionDetails.submissionStatus,
                        type: 'info',
                        value: asm.lang.general.loadingData
                    },
                    downloadLink: {
                        label: asm.lang.submissionDetails.downloadLinkLabel,
                        type: 'info',
                        value: asm.lang.general.loadingData
                    },
                    help: {
                        label: asm.lang.submissionDetails.infoLabel,
                        type: 'info',
                        value: asm.lang.submissionDetails.info
                    }
                }
            }}
        };
        this.base($.extend(true, defaults, config));
    },
    _adjustContent: function() {
    }
});
asm.ui.table.SimilaritiesTable = asm.ui.DynamicTable.extend({
    constructor: function (config) {

        var defaults = {
            icon: asm.ui.globals.icons.submissionDetails,
            actions : {
                raw: [
                    {
                        icon: 'ui-icon-' + asm.ui.globals.icons.downloadOutput,
                        label: asm.lang.submissionDetails.downloadOlderSubmission,
                        action: $.proxy(function (id, data) {
                            var triggerError = $.proxy(this._triggerError, this);
                            asm.ui.globals.fileSaver.request('DownloadSubmissionInput',
                                {id: data.submissionId}, null, triggerError);
                        }, this)
                    },
                    {
                        icon: 'ui-icon-' + asm.ui.globals.icons.submissionDetails,
                        label: asm.lang.submissionDetails.goToSubmission,
                        action: $.proxy(function (id, data) {
                            this.trigger('goToSubmissionDetails', { newId : data.submissionId } );
                        }, this)
                    }
                ]
            },
            structure: {
                id: { key: true, hidden: true, comparable: true },
                submissionId: { hidden: false, label: "ID", comparable: true},
                suspicious : { label:asm.lang.submissionDetails.suspicious, comparable: true, string: true, renderer: function(data)
                {
                    if (data == 'yes') {
                        return asm.lang.general.yes;
                    }
                    else {
                        return asm.lang.general.no;
                    }
                }},
                similarityScore: { label: asm.lang.submissionDetails.similarityScore, comparable: true, renderer: function(data) { return data + "%"; } },
                similarityReport: { label: asm.lang.submissionDetails.similarityReport, comparable: true, string: true},
                author : { label: asm.lang.submissionDetails.oldRealName, comparable: true, string: true},
                date : { label: asm.lang.submissionDetails.oldDate, comparable: true, string: true},
                status : {label: asm.lang.submissionDetails.oldSubmissionStatus, comparable: true, string: true }
            },
            title: asm.lang.submissionDetails.tableCaption,
            stores: [asm.ui.globals.stores.similarities]
        };
        this.base($.extend(true, defaults, config));
    },
    _adjustContent: function () {

    }
});

asm.ui.panel.SubmissionDetails = asm.ui.Container.extend({
    constructor: function (config) {
        var defaults = {
            children: {
                submissionInfo : new asm.ui.form.SubmissionInfoForm(),
                similaritiesTable : new asm.ui.table.SimilaritiesTable()
            }
        };
        this.base($.extend(defaults, config));
    },
    _adjustContent: function () {
         var newId = this._params[0];
         var newSubmission = undefined;
         var infoForm = this.config.children.submissionInfo;
         var similaritiesTables = this.config.children.similaritiesTable;
         asm.ui.globals.stores.similarities.config.arguments.newId = this._params[0];

         similaritiesTables.refresh(true);
         asm.ui.globals.stores.similarities.get(function(sts){
             similaritiesTables.refresh(true);
             similaritiesTables.table('sort', 'similarityScore', false);
        })
         asm.ui.globals.stores.correctionAbsolutelyAll.get($.proxy(function(storedData) {
             for (var index = 0; index < storedData.length; ++index) {
                 if (storedData[index].id == newId) {
                     newSubmission = storedData[index];
                     break;
                 }
             }
             if (newSubmission === undefined) {
                 this._triggerError(new asm.ui.Error("This submission id does not exist.", asm.ui.Error.ERROR));
                 return;
             }

             infoForm.setFieldValue('realName', '');
             infoForm.setFieldValue('realName', "<a id='realNameId' href='' onclick='return false;'>" + newSubmission.author + "</a>");
             infoForm.setFieldValue('email', "<a href='mailto:" + newSubmission.authorEmail + "'>" + newSubmission.authorEmail + "</a>");
             infoForm.setFieldValue('points', newSubmission.rating);
             infoForm.setFieldValue('details', newSubmission.details);
             infoForm.setFieldValue('uploaded', newSubmission.date);
             infoForm.setFieldValue('status', newSubmission.status);
             infoForm.setFieldValue('downloadLink', '');
             infoForm.setFieldValue('downloadLink', "<a id='downloadLinkId' href='' onclick='return false;'>" + asm.lang.submissionDetails.downloadLink + "</a>");
             var triggerError = $.proxy(this._triggerError, this);
             $("#downloadLinkId").click(
                 $.proxy(
                     function () {
                         asm.ui.globals.fileSaver.request('DownloadSubmissionInput', {id: newSubmission.id }, null, triggerError);
                     }
                     ,this
                 )
             );
             $("#realNameId").click(
                 $.proxy(
                     function () {
                        this.trigger('goToUsersSubmissions', { newId : newSubmission.authorId });
                     }
                     ,this
                 )
             )
             /*var triggerError = $.proxy(this._triggerError, this);
             return {
                 icon: 'ui-icon-' + asm.ui.globals.icons.downloadInput,
                 label: asm.lang.grading.downloadSubmission,
                 action: function (id) {
                     asm.ui.globals.fileSaver.request('DownloadSubmissionInput',
                         {id: id}, null, triggerError);
                 }
             };*/
         }, this));

        // this.config.arguments
    }
});