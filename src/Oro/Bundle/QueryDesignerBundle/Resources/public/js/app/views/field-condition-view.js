define(function(require) {
    'use strict';

    var FieldConditionView;
    var _ = require('underscore');
    var __ = require('orotranslation/js/translator');
    var tools = require('oroui/js/tools');
    var mapFilterModuleName = require('orofilter/js/map-filter-module-name');
    var AbstractConditionView = require('oroquerydesigner/js/app/views/abstract-condition-view');
    var FieldChoiceView = require('oroentity/js/app/views/field-choice-view');

    FieldConditionView = AbstractConditionView.extend({
        getDefaultOptions: function() {
            var defaultOptions = FieldConditionView.__super__.getDefaultOptions.call(this);
            return _.extend({}, defaultOptions, {
                fieldChoice: {},
                hierarchy: []
            });
        },

        _renderFilter: function(fieldId) {
            var filterOptions = this._createFilterOptions(fieldId);
            var moduleName = mapFilterModuleName(filterOptions.type);
            var requires = [moduleName];

            if (filterOptions.init_module) {
                requires.push(filterOptions.init_module);
            }

            // show loading message, if loading takes more than 100ms
            var showLoadingTimeout = setTimeout(_.bind(function() {
                this.$filterContainer.html('<span class="loading-indicator">' + __('Loading...') + '</span>');
            }, this), 100);

            tools.loadModules(requires, _.bind(function(modules) {
                var Filter = _.first(modules);
                var appendFilter = _.bind(function() {
                    clearTimeout(showLoadingTimeout);
                    var filter = new (Filter.extend(filterOptions))();
                    if (!this.disposed) {
                        this._appendFilter(filter);
                    }
                }, this);
                if (modules.length > 1) {
                    var optionResolver = modules[1];
                    var promise = optionResolver(filterOptions, this.subview('choice-input').splitFieldId(fieldId));
                    promise.done(appendFilter);
                } else {
                    appendFilter();
                }
            }, this));
        },

        _createFilterOptions: function(fieldId) {
            var filterOptions;
            var conditions = this.subview('choice-input').getApplicableConditions(fieldId);

            if (!_.isEmpty(conditions) && !(conditions.entity === 'Oro\\Bundle\\AccountBundle\\Entity\\Account' &&
                conditions.field === 'lifetimeValue')) {
                filterOptions = this.options.filters[this._getApplicableFilterId(conditions)];
            }

            if (!filterOptions) {
                filterOptions = {
                    type: 'none',
                    applicable: {},
                    popupHint: '<span class="deleted-field">' +
                    __('oro.querydesigner.field_condition.filter_not_supported') + '</span>'
                };
            }

            return filterOptions;
        },

        initChoiceInputView: function() {
            var fieldChoiceView = new FieldChoiceView(_.extend({
                autoRender: true,
                el: this.$choiceInput,
                entity: this.options.rootEntity
            }, this.options.fieldChoice));
            return fieldChoiceView;
        }
    });

    return FieldConditionView;
});
