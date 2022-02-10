<?php


class StudentVitalsApi extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'getStudentVitalData' => array(
                'reqType' => 'GET',
                'noLoginRequired' => false,
                'path' => array('professorM', 'getStudentVitalData', '?'),
                'pathVars' => array('', '', 'supergroup'),
                'method' => 'getStudentVitalData',
                'shortHelp' => 'API End point to retrieve data for vitals dashlet',
                'longHelp' => 'custom/include/api/help/student_vitals_api_help.html',
            ),
        );
    }

    /**
     * API Endpoint for student vitals chart
     * @param $api
     * @param $args
     *
     * @return false|string
     * @throws SugarQueryException
     */
    public function getStudentVitalData($api, $args)
    {

        global $app_list_strings;
        $supergroup = $args['supergroup'];
        $helper = new \Sugarcrm\Sugarcrm\custom\inc\ProfessorM\StudentVitalHelper();
        $status_data = $helper->countStudentIncidents($supergroup);


        // Sort if we have an array
        if (is_array($status_data)) {
            arsort($status_data);
        }
        $chart_data = array();

        $seriesIdx = 1;
        foreach ($status_data as $key => $value) {
            if (!empty($key)) {

                $app_list_strings['problems_list'][$key];

                $chart_data[] = array(
                    "key" => $app_list_strings['problems_list'][$key],
                    "value" => $value,
                    "total" => $value,
                    "seriesIndex" => $seriesIdx++
                );
            }

        }

        $title = '';
        if($supergroup != 'all') {
            $sg = BeanFactory::retrieveBean("Accounts", $supergroup);
            $title = "$sg->name Student Problems";
        } else {
            $title = "All Student Problems";
        }

        $data = array(
            "properties" => array(
                "title" => $title,
                "seriesName" => "Problems"
            ),
            "data"=> $chart_data


        );


        return $data;

    }

}
