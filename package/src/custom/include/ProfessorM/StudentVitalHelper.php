<?php


namespace Sugarcrm\Sugarcrm\custom\inc\ProfessorM;


/**
 * Class StudentVitalHelper
 *
 * Helper to retrieve data for student vitals dashboard
 *
 * @package Sugarcrm\ProfessorM\Helpers
 */
class StudentVitalHelper
{


    /**
     * Called by API to count changes in health status affecting Contacts (Students)
     * Also an example of using Doctrine QueryBuilder to query contacts_audit table
     * @param $supergroup
     *
     * @return mixed
     * @throws \SugarQueryException
     */
    public function countStudentIncidents($supergroup)
    {

        $pie = array();
        $queryResults = $this->getVitalChangeData($supergroup);

        foreach ($queryResults as $row) {
            $pie[$row['problem']] = $row['count'];
        }

        return $pie;
    }


    /**
     * Query to retrieve student vitals transactions for given supergroup
     * @param $supergroup
     *
     * @return array
     * @throws \SugarQueryException
     */
    public function getVitalChangeData($supergroup = 'all')
    {
        global $app_list_strings;
        $results = array();
        $trackedStatuses = array_keys($app_list_strings['problems_list']);

        foreach($trackedStatuses as $status){
            $qb = $conn = $GLOBALS['db']->getConnection()->createQueryBuilder();
            $qb->from("contacts");
            $qb->innerJoin('contacts', 'contacts_audit', 'ca', "contacts.id=ca.parent_id");
            if ($supergroup != 'all') {
                $qb ->join('contacts', 'accounts_contacts', 'ac', "contacts.id = ac.contact_id AND ac.account_id = :sg");
                $qb->setParameter('sg', $supergroup);
            }
            $qb->select("COUNT(*) AS count, ca.after_value_string AS problem");
            $qb->andWhere("contacts.deleted = 0");
            $qb->andWhere("ca.field_name = 'vitals_c'");
            $qb->andWhere("ca.after_value_string = :status");
            $qb->setParameter('status', $status);
            $results = array_merge($results, $qb->execute()->fetchAll());
        }
        return $results;
    }


}
