/**
 * Container with two tables: submissions ready to be rated and already rated submissions.
 */
asm.ui.panel.CorrectionWithSeparatedAssignments = asm.ui.Container.extend({
	constructor: function (config) {
		var defaults = {
            stores: [
                asm.ui.globals.stores.correctionAll,
                asm.ui.globals.stores.assignments
            ],
			children: {
                tableCurrent: new asm.ui.table.StudentAssignmentsCurrent(),
				loadingPanel: new asm.ui.ContentPanel().extend(
                    {
                        _buildContent : function() {
                            var panel = $('<div></div>')
                                .appendTo(this.config.target)
                                .append(asm.lang.general.loadingData)
                                .panel({ icon: 'info' });
                        }
                    }
                )
			}
		};
		this.base($.extend(true, defaults, config));
	},
    _filterAssignment: function(assignmentId) {
        return function(id, values) {
          return values["assignmentId"] == assignmentId;
        };
    },
    loaded: false,
    _showContent: function() {
        /*
        this.config.children = {};
        this.config.children.tablePast = new asm.ui.table.StudentAssignmentsPast();
        this.config.children["234"] = new asm.ui.table.StudentAssignmentsPast();

        this._callOnChildren('setParent', this);
        this._moveChildren(this.config.target);
        this._callOnChildren('show', this._params);
        */
    },
    _loadAllTables: function() {
        var assignmentData = asm.ui.globals.stores.assignments.get();
        var correctionData = asm.ui.globals.stores.correctionAll.get();
        for (var submissionIndex = 0; submissionIndex < correctionData.length; submissionIndex++)
        {
            for (var assignmentIndex = 0; assignmentIndex < assignmentData.length; assignmentIndex++)
            {
                if (correctionData[submissionIndex].assignmentId == assignmentData[assignmentIndex].id &&
                correctionData[submissionIndex].status != 'graded')
                {
                    assignmentData[assignmentIndex].correctible = true;
                }
            }
        }
        for(var index = 0; index < assignmentData.length; index++)
        {
            var assignment = assignmentData[index];
            if (!assignment.correctible) { continue; }
            if (!this._tables) { this._tables = []; }
            /*
            var table = new asm.ui.table.Correction2({
                title: assignment.problem + " (" + assignment.group + ")"//,
             //   filter: this._filterAssignment(assignmentData[index].id)
            });
            */
            var table = new asm.ui.table.Assignments();
            this._tables.push(table);
            this.config.children[assignment.id] = table;
            this.config.children[assignment.id].setParent(this);
            this._moveSingleChild(assignment.id);
        }
        this.adjust([], true);
    },
	_adjustContent: function () {
        if (!this.loaded)
        {
            asm.ui.globals.stores.assignments.get(
                $.proxy(function ()
                {
                    asm.ui.globals.stores.correctionAll.get($.proxy(this._loadAllTables, this));
                }, this));
            this.loaded = true;
        }
        this._callOnChildren('hide');
        this._callOnChildren('show');
        /*
        this._callOnChildren('adjust', this._params);
       */
        /*
        if (this.loaded)
        {
            this._callOnChildren('adjust', this._params);
        }
        else
        {
            asm.ui.globals.stores.assignments.get(
                $.proxy(function ()
                    {
                        asm.ui.globals.stores.correctionAll.get($.proxy(this._loadAllTables, this));
                    }, this));
        }
        */
	}
});