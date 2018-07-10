## Online Applications ##

Professor M wants to ensure he has a quality pipeline of students for his school.  He has decided to allow prospective
students to apply online.

### Implementation Technique ###

A [Web to Lead Form](http://support.sugarcrm.com/Knowledge_Base/Campaigns_Target_Lists/Creating_a_Web-to-Lead_Form/) was 
created in Sugar and then customized to become the online application for the school.

### Implementation Details ###

[ApplyOnline.html](../package/src/custom/online_application_form/ApplyOnline.html) is a 
customized Web to Lead Form.  This form is the online application that students can complete.  When students submit
the form, they are redirected to 
[ApplyOnlineSuccess.html](../package/src/custom/online_application_form/ApplyOnlineSuccess.html), a static html file.

Note that these two files are stored in the 
[custom/online_application_form](../package/src/custom/online_application_form) directory for simplicity of deploying
this example.  You could host these two files on any server.

When the form is submitted, a new Applicant (Lead) record is automatically created in the system.

### Extensions ###

| Module | Extension | Name | Description |
| --- | --- | ---| ---|
| Leads | Vardefs | `highschool_c` | Varchar that displays the applicant's high school. |
| Leads | Vardefs | `transcript_c` | Text field that displays the applicant's transcript. |
| Leads | Vardefs | `gpa_c` | Decimal field that displays the applicant's grade point average (GPA). |
| Leads | Vardefs | `programminglanguages_c` | Multienum field that displays the applicant's programming languages based on the options stored in the `languages` drop down list.|


### View Customizations ###
| Module | View | Description |
| --- | --- | ---|
| Leads | Record | A new panel (Application) has been added to the record view below the Business Card.  The panel displays the new custom fields for the application. |

