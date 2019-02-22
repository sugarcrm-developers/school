<?php


use Sugarcrm\Sugarcrm\ProcessManager\Registry;


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
        require_once('custom/include/ProfessorM/StudentVitalHelper.php');
        $supergroup = $args['supergroup'];
        $helper = new \Sugarcrm\ProfessorM\Helpers\StudentVitalHelper();
        $status_data = $helper->getStudentVitalsByDays($supergroup);

        // Sort if we have an array
        if (is_array($status_data)) {
            arsort($status_data);
        }
        $chart_data = array(

        );

        foreach ($status_data as $key => $value) {

            $chart_data[] = array(
                "key" => $app_list_strings['vitals_list'][$key],
                "values" => array(
                    array("x"=> 1, "y"=> $value),
                )
            );

        }
        $data = array(
            "properties" => array(
                "title" => "Student Vitals Days Count",
            ),
            "data"=> $chart_data


        );



        return json_encode($data);

    }

}
