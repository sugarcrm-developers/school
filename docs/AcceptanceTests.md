## Acceptance Test Plan ##

This test plan represents high-level acceptance tests for features implemented in the Professor M's School for Gifted
Coders package.  These tests are not automated and should be executed manually.

Before executing these tests, ensure you have done the following:
1. Installed Sugar.
1. Created an Admin user.
1. Installed the Professor M Module Loadable Package.
1. [Installed the Professor M sample data using the Professor M Postman Collection](https://github.com/sugarcrm/school#use-the-sugar-rest-api-to-create-the-professor-m-sample-data).

| Use case | Test steps | Expected Results |
| --- | --- | ---|
| [Online Applications](OnlineApplications.md) | <ol><li>Navigate to http://{site_url}/<br>custom/online_application_form/ApplyOnline.html.</li><li>Input the following information:<br>**First Name:** Joe<br>**Last Name:** Morwasky<br>**Email Address:** joetheweatherman@example.com<br>**Street Address:** 222 Lightning Lane<br>**City:** Monroe<br>**State:** CT<br>**Zip:** 06468<br>**Country:** USA<br>**High School:** Monroe High<br>**Grade Point Average (GPA):** 3.85<br>**Programming Languages:** PHP, Java<br>**Transcript:** Meteorology A+<br>Math: A-<br>English: B+<br>Agility: A+<br></li><li>Click **Submit**.</li>|<ol><li>A "Thank you!" page is displayed.</li><li>A new Applicant (Lead) record has been created with the following data:<br>**Name:** Joe Morwasky<br>**High School:** Monroe High<br>**Grade Point Average (GPA):** 3.85000000<br>**Programming Languages:** PHP, Java<br>**Transcript:** <br>Meteorology A+<br>Math: A-<br>English: B+<br>Agility: A+<br>**Email Address:** joetheweatherman@example.com<br>**Primary Address:** <br>222 Lightning Lane<br>Monroe, CT 06468<br>USA<br></li><li>The new Applicant (Lead) has a link to the **Online Applications** campaign in the Campaign Log.</li>
| [Application Routing to Admissions Advisors](ApplicationRouting.md) | <ol><li>Create a new applicant record with the following information:<br>**First Name:** Casey<br>**Last Name:** Doganzaro</li></ol> | <ol><li> A new Applicant (Lead) record has been created with the following data:<br>**Name:** Casey Doganzaro<br>**User:** Matthew Aysman</li></ol> |
| [Application Routing to Admissions Advisors](ApplicationRouting.md) | <ol><li>Create a new applicant record with the following information:<br>**First Name:** Andrew<br>**Last Name:** 'P-Reshel</li></ol> | <ol><li> A new Applicant (Lead) record has been created with the following data:<br>**Name:** Andrew 'P-Reshel<br>**User:** Matthew Aysman</li></ol> |
| [Application Routing to Admissions Advisors](ApplicationRouting.md) | <ol><li>Create a new applicant record with the following information:<br>**First Name:** Vance<br>**Last Name:** Veekus</li></ol> | <ol><li> A new Applicant (Lead) record has been created with the following data:<br>**Name:** Vance Veekus<br>**User:** Ackburr Bahabialila</li></ol> |
| [Application Ratings](ApplicationRatings.md) | <ol><li>Create a new applicant record with the following information:<br>**First Name:** Don<br>**Last Name:** Pexis Sr.<br>**Grade Point Average (GPA):** 4.0<br>**Programming Languages:** PHP, Javascript, Go, Ruby</li></ol> | <ol><li> A new Applicant (Lead) record has been created.</li><li>The application is rated 4 out of 5 stars.</li></ol> |
