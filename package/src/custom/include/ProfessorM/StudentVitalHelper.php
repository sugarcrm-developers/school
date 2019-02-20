<?php


namespace Sugarcrm\ProfessorM\Helpers;


/**
 * Class StudentVitalHelper
 *
 * Helper to retrieve data for student vitals dashboard
 *
 * @package Sugarcrm\ProfessorM\Helpers
 */
class StudentVitalHelper
{

    public $status_list;
    public $now;
    public $timedate;

    /**
     * StudentVitalHelper constructor.
     * Set class variables for re-use.
     */
    public function __construct()
    {
        global $timedate;
        $this->timedate = $timedate;
        $now_dt = clone $this->timedate;
        $this->now = $now_dt->nowDb();

    }

    /**
     * Called by API to query days in Vitals status using contacts (Students)
     * and contacts_audit tables
     * @param $team
     *
     * @return mixed
     * @throws \SugarQueryException
     */
    public function getStudentVitalsByDays($team)
    {

        $change_list = $this->getVitalChangeData($team);

        $students_array = $this->compressStudentListToStudentIDArray($change_list);

        $this->setStatusList($change_list);

        $this->countStatusDays($students_array);

        return $this->status_list;

    }


    /**
     * Query to retrieve student vitals transactions
     * @param $team
     *
     * @return array
     * @throws \SugarQueryException
     */
    public function getVitalChangeData($team)
    {

        /**
         * Query 1 is to get create date of student record
         */
        $query1 = new \SugarQuery();
        $query1->from(\BeanFactory::newBean('Contacts'));
        $query1->select(
            array(
                array('id', 'student_id'),
                array('date_entered', 'transaction_date'),
                array('vitals_c', 'start_status'),
                array('vitals_c', 'end_status'),
            )
        );


        /**
         * Query 2 retrieves data from Audit table
         */
        $query2 = new \SugarQuery();

        $query2->from(\BeanFactory::newBean('Contacts'));
        $query2->joinTable('contacts_audit', array(
            'alias'        => 'ca',
            'joinType'     => 'INNER',
            'linkingTable' => true,
        ))->on()
            ->equalsField('contacts.id', 'ca.parent_id')
            ->equals('ca.field_name', 'vitals_c');
        $query2->select(array(
            array('id', 'student_id'),
            array('ca.date_created', 'transaction_date'),
            array('ca.before_value_string', 'start_status'),
            array('ca.after_value_string', 'end_status'),
        ));


        /**
         * Format for for teams
         */
        if ($team != 'all') {
            $query1->joinTable('accounts_contacts', array('alias' => 'ac'))->on()
                ->equalsField('contacts.id','ac.contact_id')
                ->equals('ac.account_id',$team);
            $query2 ->joinTable('accounts_contacts', array('alias' => 'ac'))->on()
                ->equalsField('contacts.id','ac.contact_id')
                ->equals('ac.account_id',$team);
        }

        $sqUnion = new \SugarQuery();
        $sqUnion->union($query1);
        $sqUnion->union($query2);
        $sqUnion->orderBy('student_id', 'ASC');
        $sqUnion->orderBy('transaction_date', 'ASC');

        $results = $sqUnion->execute();

        return $results;

    }

    /**
     * Retrieves unique values from an array.
     * Used to get listing of vitals transaction in recordset
     * @param $array
     * @param $key
     *
     * @return array
     */
    protected function getUniqueArray($array, $key)
    {

        $grouped_array = array();
        foreach ($array as $row) {
            $grouped_array[] = $row[$key];
        }

        $grouped_array = array_unique($grouped_array);

        return $grouped_array;
    }

    /**
     * Set array of all unique start and end statuses in record set.
     * Used to populate return data to API
     *
     * @param $change_list
     */
    protected function setStatusList($change_list)
    {

        $start_statuses = $this->getUniqueArray($change_list, 'start_status');
        $end_statuses = $this->getUniqueArray($change_list, 'end_status');

        $combined_statuses = array_unique(array_merge($start_statuses, $end_statuses));

        foreach ($combined_statuses as $status) {

            //Set initial days for each status to zero
            $this->status_list[$status] = 0;

        }
    }

    /**
     * Format query record set to be grouped by Student IDs
     * @param $change_list
     *
     * @return array
     */
    protected function compressStudentListToStudentIDArray($change_list)
    {
        $student_change_array = array();
        foreach ($change_list as $row) {
            $student_change_array[$row['student_id']][] = $row;
        }

        return $student_change_array;
    }

    /**
     * Loop through transactions (changes in vitals) by student and
     * appends days in each transaction to totals
     * @param $student_change_list
     */
    protected function countStatusDays($student_change_list)
    {

        foreach ($student_change_list as $student_id => $changes) {

            $max_array = count($changes) - 1;

            for ($i = 0; $i <= $max_array; $i++) {
                $days_in_status = $this->countTransaction($changes[$i], $changes[$i + 1]);

                if ($i == $max_array) {
                    $append_status = $changes[$i]['end_status'];
                } else {
                    $append_status = $changes[$i + 1]['start_status'];
                }

                $this->status_list[$append_status] += $days_in_status;
            }

        }
    }


    /**
     * Count days in vital status between before and after transaction
     * @param      $before
     * @param null $after
     *
     * @return mixed
     */
    protected function countTransaction($before, $after = null)
    {

        $start_string = $before['transaction_date'];

        if (!$after) {
            $end_string = $this->now;
        } else {
            $end_string = $after['transaction_date'];
        }

        $days = $this->countDaysDiff($start_string, $end_string);

        return $days;

    }

    /**
     * Count days between dates
     * @param $before_date
     * @param $after_date
     *
     * @return mixed
     */
    protected function countDaysDiff($before_date, $after_date)
    {
        $start = clone $this->timedate;
        $end = clone $this->timedate;

        $start_td = $start->fromString($before_date);
        $end_td = $end->fromString($after_date);
        $interval = $start_td->diff($end_td);

        $days = $interval->days;

        return $days;

    }

}