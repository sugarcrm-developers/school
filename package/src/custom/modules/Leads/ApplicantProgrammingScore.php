<?php
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\Security\Context;

use Sugarcrm\Sugarcrm\custom\Security\Subject\ApplicantProgrammingScore as ApsSubject;


/**
 * Class ApplicantProgrammingScore
 * Updates an applicant's Programming Score
 */
class ApplicantProgrammingScore
{
    private $version = '1.0.0';
    /**
     * Update an applicant's Programming Score and then update calculated fields.
     *
     * @param $bean The bean for the Applicant (Lead) record
     * @param $event The current event
     * @param $arguments Additional information related to the event
     */
    public function updateProgrammingScore($bean, $event, $arguments)
    {
        //Set the security context to better track the source of changes
        $context = Container::getInstance()->get(Context::class);
        $subject = new ApsSubject($bean->id);
        $context->activateSubject($subject);
        $context->setAttribute('aps_calc_version', $this->version);

        // The programming languages are stored as a comma separated list in the bean. Convert them to an array.
        $programmingLanguages = explode(",", $bean->programminglanguages_c);

        // Store the calculated programming score in the Applicant bean
        $bean->programming_score_c = $this->getProgrammingScore($programmingLanguages);

        // Update the calculated fields.  This is necessary for the Rating Star field to be updated immediately.
        $bean->updateCalculatedFields();
        
        //Have to commit the changes we made to the audit log because we didn't call save.
        //Passing the subject rather than null here would override the additional attributes that were set on the context
        $bean->commitAuditedStateChanges(null);
        //Always deactivate the subject before leaving the function
        $context->deactivateSubject($subject);
    }

    /**
     * Get the Programming Score for the array of programming languages.  The function assumes the programming languages
     * in the array start and end with ^
     *
     * Languages PHP and Javascript are worth 15 points each.
     * All other languages are worth 5 points each.
     * The Programming Score is a sum of points for all languages.  The total for all languages should sum to 60 so that
     * the Rating Star calculation works as expected.
     *
     * @param array $programmingLanguages
     * @return programming score. Integer between 0 and 60.
     */
    public function getProgrammingScore($programmingLanguages){
        // Set the initial $programmingScore to 0
        $programmingScore = 0;

        // Iterate over the array of $programmingLanguages and add to the $programmingScore for each language
        foreach($programmingLanguages as $key => $language){
            switch ($language) {
                case "^php^":
                    $programmingScore += 15;
                    break;
                case "^javascript^":
                    $programmingScore += 15;
                    break;
                case "^net^":
                    $programmingScore += 5;
                    break;
                case "^java^":
                    $programmingScore += 5;
                    break;
                case "^c^":
                    $programmingScore += 5;
                    break;
                case "^go^":
                    $programmingScore += 5;
                    break;
                case "^python^":
                    $programmingScore += 5;
                    break;
                case "^ruby^":
                    $programmingScore += 5;
                    break;
                case "":
                    break;
                default:
                    $GLOBALS['log']->warn("Unable to assign a value for $language in the Application Rating calculation. The language should be added as a possible value in custom/modules/Leads/ApplicantProgrammingScore.php.");
            }
        }

        return $programmingScore;
    }
}
