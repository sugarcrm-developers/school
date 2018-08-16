# Application Routing to Admissions Advisors

Professor M wants to ensure that every new applicant is assigned an Admissions Advisor.  Applicants with a last name 
beginning with the letters A-L or special characters are assigned to Matthew Aysman.  Applicants with a last name 
beginning with the letters M-Z are assigned to Ackburr Bahabialila.  

**Note:**  because this feature leverages Advanced Workflow, it is only available in Enterprise and Ultimate versions of 
Sugar.

## Implementation Technique

Advanced Workflow is used to assign an Admissions Advisor to new applicant records.  

## Implementation Details

A [Process Business Rule](http://support.sugarcrm.com/Documentation/Sugar_Versions/8.1/Ent/Administration_Guide/Advanced_Workflow/Process_Business_Rules/)
named **Application Routing by Applicant Last Name** contains the logic on how applicant records should be assigned to 
users.

A [Process Definition](http://support.sugarcrm.com/Documentation/Sugar_Versions/8.1/Ent/Administration_Guide/Advanced_Workflow/Process_Definitions/)
named **Application Routing** uses the **Application Routing by Applicant Last Name** Process Business Rule to 
automatically assign an Admissions Advisor to **new** Applicant (Lead) records.  Already existing records and newly 
updated records are not affected by this Process Definition.

The **Application Routing by Applicant Last Name** Process Business Rule and **Application Routing** Process Definition
are both created in the **Import Application Routing Process Definition** REST API call that is part of 
[ProfessorM_PostmanCollection_AdvancedWorkflow.json](../data/ProfessorM_PostmanCollection_AdvancedWorkflow.json). 
The API call uses the multipart/form-data format to send [Application_Routing.bpm](../data/Application_Routing.bpm) (which 
includes the **Application Routing by Applicant Last Name** Process Business Rule and **Application Routing** 
Process Definition) and the ID of the **Application Routing by Applicant Last Name** Process Business Rule.  A second
API call named **Enable Application Routing Process Definition** enables the **Application Routing** Process Definition,
which is disabled by default.

No custom code was written as part of this feature.

## Pull Request

The pull request associated with these changes is [#91](https://github.com/sugarcrm/school/pull/91).

## Trying the Use Case 

Ensure you are using an Enterprise or Ultimate edition of Sugar and that you have inserted the sample data using 
[ProfessorM_PostmanCollection.json](../data/ProfessorM_PostmanCollection.json) and
[ProfessorM_PostmanCollection_AdvancedWorkflow.json](../data/ProfessorM_PostmanCollection_AdvancedWorkflow.json).

Create a new Applicant record and save it.  Note that a user is automatically assigned to the Applicant record.
