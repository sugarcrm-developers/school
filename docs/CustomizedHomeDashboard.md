# Customized Home Dashboard

Professor M wants to display relevant information on the home 
[dashboard](http://support.sugarcrm.com/Documentation/Sugar_Versions/8.0/Pro/Application_Guide/User_Interface/Dashboards_Dashlets/#Overview).

## Dashlets

The customized home dashboard displays six dashlets.

| Dashlet Title | Dashlet Type | Notes |
| --- | --- | --- |
| Student Health Report | Saved Reports Chart Dashlet | Configured to use the Student Health Report. |
| Open Donations by User by Expected Month | Saved Reports Chart Dashlet | Configured to use the Open Opportunities by User by Expected Month Report. |
| Applicants | List View | Configured to use the All Applicants filter. |
| Students | List View | Configured to use the All Students filter. |
| Professors | List View | Configured to use the All Professors filter. |
| Super Groups | List View | Configured to use the All Super Groups filter. |

## Implementation Technique

The default home dashboard is updated by making a `PUT` request to `/v11/Dashboards/{{Home_Dashboard_Record_ID}}`.

## Implementation Details

The customized home dashboard is implemented entirely through REST API calls in the 
[ProfessorM_PostmanCollection.json](../data/ProfessorM_PostmanCollection.json).  

To prepare the metadata for the customized dashboard, we began by configuring the dashboard in an instance of Sugar with 
the Professor M module loadable package installed and the Professor M sample data inserted.  Next, we retrieved the 
dashboard's metadata by executing `GET /Dashboards` (see 
[/\<module> GET](http://support.sugarcrm.com/Documentation/Sugar_Developer/Sugar_Developer_Guide_8.0/Integration/Web_Services/REST_API/Endpoints/module_GET/)
for more details) and copying the metadata from the appropriate dashboard record in the response.  Finally, we 
pasted the metadata in to the body of the request to update the home dashboard: 
`PUT /Dashboards/{{Home_Dashboard_Record_ID}}` (see 
[/\<module>/:record PUT](http://support.sugarcrm.com/Documentation/Sugar_Developer/Sugar_Developer_Guide_8.0/Integration/Web_Services/REST_API/Endpoints/modulerecord_PUT/) 
for more details).

The following table lists relevant API calls inside of 
[ProfessorM_PostmanCollection.json](../data/ProfessorM_PostmanCollection.json).

| Folder | Request Name | Notes |
| --- | --- | --- |
| Create Reports | Create Student Health Report | This request creates the Student Health Report that the Student Health Report dashlet uses. The tests for this request store the report ID in the Postman Environment Variable `Report_StudentHealth`. |
| Update Home Dashboard | Get Home Dashboard | This request retrieves the ID of the default home dashboard.  The tests for this request store the dashboard ID in the Postman Environment Variable `Dashboard_Home`. |
| Update Home Dashboard | Get Report named Open Opportunities by User by Expected Close Month | This report retrieves the ID of the report named "Open Opportunities by User by Expected Close Month." The tests for this request store the dashboard ID in the Postman Environment Variable `Report_OpenOppsByUserByCloseMonth`. |
| Update Home Dashboard | Update Home Dashboard | This request updates the home dashboard's metadata. It uses the Postman Environment Variables that were set the in the other requests in this table. |

## Pull Request

The pull request associated with these changes is [#104](https://github.com/sugarcrm/school/pull/104).

## Trying the Use Case

Ensure you have inserted the sample data using 
[ProfessorM_PostmanCollection.json](../data/ProfessorM_PostmanCollection.json).

Navigate to http://{site_url} and log in.  Note the customized dashboard.
