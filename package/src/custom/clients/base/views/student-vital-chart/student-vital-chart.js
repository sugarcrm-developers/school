/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
({
    plugins: ['Dashlet', 'Chart'],
    className: 'student-vital-chart',
    chartCollection: null,
    hasData: false,
    total: 0,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.listenTo(this, 'render', function () {
            this.retrieveSuperGroupOptions();
        } );
        this.chart = sucrose.charts.pieChart()
            .donut(true)
            .donutLabelsOutside(true)
            .donutRatio(0.25)
            .hole(false)
            .showTitle(true)
            .tooltips(true)
            .showLegend(true)
            .colorData('class')
            .tooltipContent(_.bind(function(eo, properties) {

                var value = parseInt(this.chart.getValue()(eo), 10);
                var total = parseInt(properties.total, 10);
                (total == 0) ? total = 1 : '';
                var percentage = value/total * 100;
                return '<h3>' + this.chart.fmtKey()(eo) + '</h3>' +
                    '<p>' + value + ' days</p>' +
                    '<p>' + percentage.toFixed(2) + '%</p>';
            }, this))
            .strings({
                noData: app.lang.get('LBL_CHART_NO_DATA')
            });
    },


    /**
     * Generic method to render chart with check for visibility and data.
     * Called by _renderHtml and loadData.
     */
    renderChart: function() {
        var self = this;
        if (!self.isChartReady()) {

            return;
        }


        d3sugar.select(this.el).select('svg#' + this.cid)
            .datum(self.chartCollection)
            .call(self.chart);

        this.chart_loaded = _.isFunction(this.chart.update);
        this.displayNoData(!this.chart_loaded);

    },

    /**
     * @inheritdoc
     */
    loadData: function(options) {

        if(this.meta.config) {

            return;
        }
        var self = this;
        var team_value = this.meta.vitals_dashlet_team ? this.meta.vitals_dashlet_team : 'all';
        url = app.api.buildURL('contacts/professorM/getStudentVitalData/' + team_value);
        this.hasData = false;
        app.api.call('GET', url, null, {
            success: function(data) {
                self.hasData = true;
                self.evaluateResponse(data);
                self.render();
            },
            complete: options ? options.complete : null
        });

    },

    evaluateResponse: function(data) {

        this.total = 1;
        this.hasData = true;
        this.chartCollection = $.parseJSON(data);

    },

    retrieveSuperGroupOptions: function() {
      if (!this.meta.config) {
          return;
      }
      console.log(this.meta.vitals_dashlet_team);
      var team_value = this.meta.vitals_dashlet_team ? this.meta.vitals_dashlet_team : 'all';
      var accounts = app.data.createBeanCollection('Accounts');
      var teams = [];
      teams.push({id: 'all', text:'All'})
      accounts.fetch({
          success: function() {
              accounts.comparator = 'name';
              accounts.sort({silent: true});
              _.each(accounts.models, function(account){
                 teams.push({id: account.id, text: account.attributes.name});
              });

              $('[name="vitals_dashlet_team"]').html('').select2({data: teams});
              $('[name="vitals_dashlet_team"]').val(team_value).trigger('change');
          }
      })
    },
})
