<?php
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

namespace Sugarcrm\Sugarcrm\custom\Security\Subject;

use Sugarcrm\Sugarcrm\Security\Subject;

/**
 * The APS hook making changes
 */
final class ApplicantProgrammingScore implements Subject
{
    /**
     * @var string
     */
    private $applicantId;

    /**
     * Constructor
     *
     * @param string $applicantId
     */
    public function __construct($applicantId)
    {
        $this->applicantId = $applicantId;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            '_type' => 'aps-hook',
            'applicant_id' => $this->applicantId
        ];
    }
}
