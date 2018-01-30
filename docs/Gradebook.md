# Gradebook Integration

Professor M needs to do more than just track basic information about his students; he needs to track their grades as 
well. He has decided to integrate Sugar with an external gradebook app.  When a new student is created in Sugar, a new 
record should be automatically created in the gradebook app.

## About the Gradebook App

The gradebook app is being simulated by the files inside of 
[package/src/custom/gradebook_fake](../package/src/custom/gradebook_fake).  If the gradebook app were real, 
it would be stored outside of Sugar. 

The simulated gradebook app does not currently store any data.  It simulates responses that would be returned from an 
external gradebook app.

## Setting up the cron scheduler

The implementation requires the Sugar Schedulers to be running.  In order for the Schedulers to run, a process needs to 
be running on the server to initiate the Schedulers.  The Scheduler is set up automatically for On-Demand Instances. 
Admins of on-premises installations of Sugar will need to set up a cron scheduler manually.  See the Setting up Cron 
Scheduler section of the 
[Sugar Developer Guide](http://support.sugarcrm.com/SmartLinks/Administration_Guide/System/Schedulers/index.html#Setting_up_Cron_Scheduler)
for details.  

Tip:  if you see errors when running cron, you may need to update the command in the crontab file to have the full path
to your php.  For example: 
`* * * * * cd /Applications/MAMP/htdocs/profm711; /Applications/MAMP/bin/php/php7.1.1/bin/php -f cron.php > /dev/null 2>&1`

## Implementation Technique

An after-save logic hook that puts a job on the Sugar Job Queue was created based on the example in the 
[Sugar Developer Guide](http://support.sugarcrm.com/SmartLinks/Developer_Guide/Architecture/Job_Queue/Jobs/Queuing_Logic_Hook_Actions).

## Implementation Details

An after-save logic hook is defined in [profm.php](../package/src/custom/Extension/modules/Contacts/Ext/LogicHooks/profm.php).

Whenever a new record is created in the Contacts (Students) module, the after-save logic hook will be triggered.  The 
logic hook indicates that the `AddStudentToGradebook()` function in 
[Students_Gradebook.php](../package/src/custom/modules/Contacts/Students_Gradebook.php) should be called.

The `AddStudentToGradebook()` function begins by checking if the record is new or just an update.  If the record is new,
it will create a new job and add it to the Sugar Job Queue.  

The next time the Sugar Job Queue is executed, the new job will be run.  The job calls the `AddStudentToGradebookJob()`
function inside of 
[AddStudentToGradebookJob.php](../package/src/custom/Extension/modules/Schedulers/Ext/ScheduledTasks/AddStudentToGradebookJob.php).
This function is what calls our fake gradebook integration's 
[RecordManager](../package/src/custom/gradebook_fake/RecordManager.php).

The [RecordManager](../package/src/custom/gradebook_fake/RecordManager.php) doesn't actually create a record.  It simply
simulates a response.  [RecordManager](../package/src/custom/gradebook_fake/RecordManager.php)'s createStudentRecord() 
function will return true unless the email address `forceerror@example.com` is used, in which case an exception will be
thrown.

Note that this implementation uses a job in the Sugar Job Queue to call the gradebook app instead of calling the 
gradebook app directly from the logic hook.  We chose this implementation so that the saving of new Student records would
not be slowed down by the process of creating a new record in a potentially slow gradebook app.  If we made the call to 
the gradebook app directly from the logic hook, the record would not finish saving until a response was returned from
the gradebook app.

## Trying the Use Case

Before trying the use case, be sure you have set up the cron scheduler.

Navigate to the Students module and create a new student record.  If everything works correctly, you will NOT see any 
errors in sugarcrm.log.

If you want to simulate an error in the gradebook app, navigate to the Students module and create a new student record
with the e-mail address `forceerror@example.com`.  An error similar to the following will be displayed in sugarcrm.log:

```
Wed Feb  7 17:17:02 2018 [61178][1][FATAL] Job 92d9c8dc-0c2a-11e8-9f94-7200080cd7d0 (Add New Student to Gradebook Job - ) failed in CRON run
```

You can browse the `job_queue table` of the database to see a list of all jobs executed.  The name of the job created 
by this implementation is `Add New Student to Gradebook Job`. Regardless of whether the job succeeds or fails,
it will be listed in this table.  
