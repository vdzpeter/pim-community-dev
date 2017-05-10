webpackJsonp([9,23],{

/***/ 148:
/* unknown exports provided */
/* all exports used */
/*!***********************************************************************************************!*\
  !*** ./~/text-loader!./src/Pim/Bundle/EnrichBundle/Resources/public/templates/form/grid.html ***!
  \***********************************************************************************************/
/***/ (function(module, exports) {

module.exports = "<div class=\"grid-drop\" data-type=\"datagrid\"></div>\n<input type=\"hidden\" id=\"added_objects\"/>\n<input type=\"hidden\" id=\"removed_objects\"/>\n"

/***/ }),

/***/ 150:
/* unknown exports provided */
/* all exports used */
/*!*****************************************************************************!*\
  !*** ./src/Pim/Bundle/EnrichBundle/Resources/public/js/form/common/grid.js ***!
  \*****************************************************************************/
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;

!(__WEBPACK_AMD_DEFINE_ARRAY__ = [
        __webpack_require__(/*! jquery */ 1),
        __webpack_require__(/*! underscore */ 0),
        __webpack_require__(/*! backbone */ 2),
        __webpack_require__(/*! oro/datagrid-builder */ 43),
        __webpack_require__(/*! routing */ 3),
        __webpack_require__(/*! oro/mediator */ 5),
        __webpack_require__(/*! text-loader!pim/template/form/grid */ 148),
        __webpack_require__(/*! oro/pageable-collection */ 22),
        __webpack_require__(/*! pim/datagrid/state */ 44)
    ], __WEBPACK_AMD_DEFINE_RESULT__ = function (
        $,
        _,
        Backbone,
        datagridBuilder,
        Routing,
        mediator,
        template,
        PageableCollection,
        DatagridState
    ) {
        return Backbone.View.extend({
            template: _.template(template),
            className: 'AknTabContainer-content--fullWidth',
            urlParams: {},

            /**
             * {@inheritdoc}
             */
            initialize: function (alias, options) {
                this.alias = alias;
                this.selection = options.selection || [];
                this.selection = _.map(this.selection, function (item) {
                    return String(item);
                });
                this.options = options;

                mediator.on('datagrid:selectModel:' + this.alias, function (model) {
                    this.addElement(model.get('id'));
                }.bind(this));

                mediator.on('datagrid:unselectModel:' + this.alias, function (model) {
                    this.removeElement(model.get('id'));
                }.bind(this));
            },

            /**
             * {@inheritdoc}
             */
            render: function () {
                this.$el.html(this.template({}));

                this.renderGrid(this.alias, this.options);

                return this;
            },

            /**
             * Render the given grid
             *
             * @param {String} alias
             * @param {Object} params
             */
            renderGrid: function (alias, params) {
                this.urlParams = $.extend(true, {}, params);
                this.urlParams.alias = alias;
                this.urlParams.params = $.extend(true, {}, params);
                this.urlParams[alias] = $.extend(true, {}, params);

                var viewStored = DatagridState.get(alias, ['view']);
                if (!viewStored.view) {
                    DatagridState.refreshFiltersFromUrl(alias);
                }

                var state = DatagridState.get(alias, ['view', 'filters', 'columns']) || {};
                this.applyView(state.view, alias);
                this.applyFilters(state.filters, alias);
                this.applyColumns(state.columns, alias);

                //TODO Manage columns for product form (when refactoring product form index)
                //TODO Manage category filter (when refactoring category index)

                $.get(Routing.generate('pim_datagrid_load', this.urlParams)).then(function (response) {

                    this.$el.find('.grid-drop').data({
                        metadata: response.metadata,
                        data: JSON.parse(response.data)
                    });

                    !(function webpackMissingModule() { var e = new Error("Cannot find module \"unsupported\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())
                }.bind(this));
            },

            /**
             * Get the current grid selection
             *
             * @return {Array}
             */
            getSelection: function () {
                return this.selection;
            },

            /**
             * Add an element to the selection
             *
             * @param {Object} element
             */
            addElement: function (element) {
                this.selection = _.union(this.selection, [element]);
                this.trigger('grid:selection:updated', this.selection);
            },

            /**
             * Remove an element to the selection
             *
             * @param {Object} element
             */
            removeElement: function (element) {
                this.selection = _.without(this.selection, element);
                this.trigger('grid:selection:updated', this.selection);
            },

            /**
             * Ask for a refresh of the grid (aware that we should not call the mediator for that but we don't have
             * the choice for now)
             */
            refresh: function () {
                mediator.trigger('datagrid:doRefresh:' + this.alias);
            },

            /**
             * Apply the view to the DatagridState
             * @param viewId
             * @param alias
             */
            applyView: function (viewId, alias) {
                if (!viewId) {
                    return;
                }

                this.urlParams[alias + '[_parameters][view][id]'] = viewId;

                DatagridState.set(alias, {
                    view: viewId
                });
            },

            /**
             * Apply the filters to the DatagridState
             * @param rawFilters
             * @param alias
             */
            applyFilters: function (rawFilters, alias) {
                if (!rawFilters) {
                    return;
                }

                var filters = PageableCollection.prototype.decodeStateData(rawFilters);
                var options = {};

                if (!_.isEmpty(filters.filters)) {
                    options = {
                        state: {
                            filters: _.omit(filters.filters, 'scope')
                        }
                    };
                }

                var collection = new PageableCollection(null, options);
                collection.processFiltersParams(this.urlParams, filters, alias + '[_filter]');

                for (var column in filters.sorters) {
                    this.urlParams[alias + '[_sort_by][' + column + ']'] =
                        1 === parseInt(filters.sorters[column]) ?
                            'DESC' :
                            'ASC';
                }

                if (undefined !== filters.pageSize) {
                    this.urlParams[alias + '[_pager][_per_page]'] = filters.pageSize;
                }

                if (undefined !== filters.currentPage) {
                    this.urlParams[alias + '[_pager][_page]'] = filters.currentPage;
                }

                DatagridState.set(alias, {
                    filters: rawFilters
                });
            },

            /**
             * Apply the columns to the DatagridState
             * @param columns
             * @param alias
             */
            applyColumns: function (columns, alias) {
                if (!columns) {
                    return;
                }

                if (_.isArray(columns)) {
                    columns = columns.join();
                }
                this.urlParams[alias + '[_parameters][view][columns]'] = columns;

                DatagridState.set(alias, {
                    columns: columns
                });
            }
        });
    }.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));


/***/ }),

/***/ 243:
/* unknown exports provided */
/* all exports used */
/*!************************************************************************************!*\
  !*** ./src/Pim/Bundle/EnrichBundle/Resources/public/js/form/common/tab/history.js ***!
  \************************************************************************************/
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;

!(__WEBPACK_AMD_DEFINE_ARRAY__ = [
        __webpack_require__(/*! underscore */ 0),
        __webpack_require__(/*! pim/form */ 41),
        __webpack_require__(/*! pim/common/grid */ 150),
        __webpack_require__(/*! oro/translator */ 4)
    ], __WEBPACK_AMD_DEFINE_RESULT__ = function (
        _,
        BaseForm,
        Grid,
        __
    ) {
        return BaseForm.extend({
            className: 'AknTabContainer-content tabbable tabs-left history',
            historyGrid: null,

            /**
             * @param {Object} meta
             */
            initialize: function (meta) {
                this.config = _.extend({}, meta.config);
                this.config.modelDependent = false;
            },

            /**
             * {@inheritdoc}
             */
            configure: function () {
                this.trigger('tab:register', {
                    code: this.config.tabCode ? this.config.tabCode : this.code,
                    label: __(this.config.title)
                });

                return BaseForm.prototype.configure.apply(this, arguments);
            },

            /**
             * {@inheritdoc}
             */
            render: function () {
                if (!this.historyGrid) {
                    this.historyGrid = new Grid(
                        'history-grid',
                        {
                            object_class: this.config.class,
                            object_id: this.getFormData().meta.id
                        }
                    );
                }

                this.$el.empty().append(this.historyGrid.render().$el);

                return this;
            }
        });
    }.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));


/***/ })

});