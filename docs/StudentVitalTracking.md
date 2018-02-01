# Student Vital Tracking

When doing battle against the forces of evil, it is possible that a coder is incapacitated by carpal tunnel syndrome or even killed in a freak hoverboard accident. Professor M needs to report on the cause of death of any coder who has died in order to prevent the same mistakes from happening again.

## Implementation Technique

Sugar Studio was used to add custom fields, add a new drop down list, and to make Record View customizations within a dev instance. Sugar Logic is used with simple manually created custom Dependencies that are applied to conditionally change Record view panel visibility and make fields required.
 
Finally, these changes are extracted out of dev instance and included in this package.
 
Adding these fields is enough to allow Prof. M to use out of the box functionality to create reports or list view filters to achieve his business goals.

## Implementation Details

A custom Vitals dropdown field (`vital_c`) was added to the Students module and the Record view. The following vital statuses are selectable.
    
    'active' => 'Active'
    'injured' => 'Injured'
    'comatose' => 'Comatose'
    'deceased' => 'Deceased'

When `deceased` is selected, a new Death Information panel appears on the Record view and users are required to enter a Cause of Death (`cause_of_death_c`).

Professor M can then build reports based upon vital status and the common causes of death.

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
 
 


