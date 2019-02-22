<?php

$viewdefs['base']['view']['student-vital-chart'] = array(
    'dashlets' => array(
        array(
            'label' => 'LBL_STUDENT_VITAL_CHART',
            'description' => 'LBL_STUDENT_VITAL_CHART_DESC',
            'config' => array(
                'date_range' => 'all',
                'supergroup' => 'all',
            ),
            'preview' => array(
            ),
            'filter' => array(
                'view' => array(
                    'records',
                    'record'
                )
            ),

        ),

    ),
    'panels' => array(
        array(
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => array(

                array(
                    'name' => 'vitals_dashlet_supergroup',
                    'label' => 'LBL_VITALS_DASHLET_SELECT_TEAM',
                    'type' => 'enum',
                    'span' => 6,
                    'options' => array('all' => 'All'),
                ),
                array(
                    'name' => 'vitals_dashlet_date_range',
                    'label' => 'LBL_VITALS_DASHLET_SELECT_DATE_RANGE',
                    'type' => 'enum',
                    'span' => 6,
                    'options' => array('all' => 'All Time', 'ThisYear' => 'This Year', 'LastYear' => 'Last Year'),
                ),
            ),
        ),
    ),
);
