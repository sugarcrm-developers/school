# Student Health Tracking

When doing battle against the forces of evil, it is possible that a coder is incapacitated by carpal tunnel syndrome or even killed in a freak hoverboard accident. Professor M needs to keep track of the health of his students to make sure they are happy and productive and stay out of danger.

## Implementation Technique

Sugar Studio was used to add custom fields, add a new drop down list, and to make Record View customizations within a dev instance. Sugar Logic is used with simple manually created custom Dependencies that are applied to conditionally change Record view panel visibility and make fields required.
 
Finally, these changes are extracted out of dev instance and included in this package.

Next we rely on out of the box Reporting functionality to help Prof. M keep track of the health of the student body.
 
## Implementation Details

A custom Vitals dropdown field (`vital_c`) was added to the Students module and the Record view. The following vital statuses are selectable.
    
    'active' => 'Active'
    'injured' => 'Injured'
    'comatose' => 'Comatose'
    'deceased' => 'Deceased'

When `deceased` is selected, a new Death Information panel appears on the Record view and users are required to enter a Cause of Death (`cause_of_death_c`).

For the last step, Reports were created using the standard Reports module within in a dev instance. These reports rely primarily on `vital_c` and `cause_of_death_c` fields.

Report JSON data is then retrieved from dev instance using the `GET /rest/Reports` REST endpoint. The JSON report representations were then converted by hand into `POST /rest/Reports` requests and tested. The final working `POST` requests are then added to Prof M. data collection which makes deploying these reports into other instances easy. 

## Extensions

| Module | Extension | Name | Description | 
| :--- | :--- | :---- | :---- |
|Contacts|Vardefs|`vital_c`|Dropdown for tracking vital status of student.|
|Contacts|Vardefs|`cause_of_death_c`|Text field that allows Prof. M to enter cause of death.|
|Contacts|Vardefs|`flowers_sent_c`|Checkbox field that allows Prof M. to track if flowers were ordered.|
|Contacts|Dependencies|`cause_of_death_required`|Makes `cause_of_death_c` a required field when `vital_c` is set to `deceased`.|
|Contacts|Dependencies|`show_death_panel`|Displays the `death_panel` in the Contacts Record View when `vital_c` is set to `deceased`.|
|application|Language|`sugar_vitals_list`|List of supported dropdown values for `vitals_c`. Described above.|
 
 ## View customizations 
 
| Module | View | Description | 
| :--- | :--- | :--- |
|Contacts|Record|Vitals dropdown (`vital_c`) has been added to main business card. A Death Information (`death_panel`) panel was also added that contains custom fields named `cause_of_death_c` and `flowers_sent_c`.|
 
 
## Reports

|Name | Module | Type | Description |
| :--- | :--- | :--- | :--- |
|Student Health Report|Contacts|Summation with Details| A pie chart that is grouped by Student vital status. The details section shows student names, alias, etc. |
|Cause of Death|Contacts|Rows and Columns| A table of 'deceased' students that includes name, alias, and cause of death. |

## Pull Request

The pull request associated with these changes is [#24](https://github.com/sugarcrm/school/pull/24).

## Trying the Use Case

Update a student's health status by opening a Student record and updating the Vitals field.  If you select **Deceased**,
the Death Information panel will appear so you can also input the Cause of Death and indicated if flowers have been 
sent.

View the **Cause of Death Report** and the **Student Health Report** by navigating to the Reports module and clicking 
on the appropriate report title.