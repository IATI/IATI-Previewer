IATI Previewer Checklist
========================

We should know which code is 'ours'
-----------------------------------

This is ours

All code should have a lead person identified
---------------------------------------------

Ben Webb - `https://github.com/Bjwebb <https://github.com/Bjwebb>`__ 

Our projects/code should be appropriately branded.
--------------------------------------------------

This application fails this test. In absence of branding guidance this uses a plain theme, but should be 
better branded.

Our code/projects should be in version control and present links to issue trackers and source code.
----------------------------------------------------------------------------------------------------

The project is on GitHub
There is a link in the footer to the source code
No direct link to the issue tracker


Each piece of code should have a document(!), a roadmap, and estimate of resources, and a licence
-------------------------------------------------------------------------------------------------

Open, permissive licence is included in the source code

We should be confident that updates to our code will not break existing functionality
-------------------------------------------------------------------------------------

There are no tests written for this code

It should make sense in the way people access our tools/code
------------------------------------------------------------

| Currently this application is liked to from the 'Preview' button on registry records.
| However it is hosted at: http://tools.aidinfolabs.org/showmydata
| It is also named inconsistently as showmydata, Preview IATI Data, and IATI-Previewer

Our code should be on our servers - we should be able to monitor the performance of those servers
-------------------------------------------------------------------------------------------------

This application is on our servers. No specific monitoring of this application is in place.

We should know how our code is being used - logs!
-------------------------------------------------

The only logs are web logs. Currently we do not analyse
these.

Our code will need to adapt with schema changes and changes to external systems upon which it relies
----------------------------------------------------------------------------------------------------

This application should be schema independent

Developers should be able to find useful resources and help easily
------------------------------------------------------------------

-  Link to the source code is in the footer.
-  Link to using IATI data page of the IATI Registry in sidebar.
-  Link back to iatistandard in the sidebar.

Each project should clearly describe how other developers can get involved
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Has a CONTRIBUTING.rst file

We should be able to communicate with the users of our code.
------------------------------------------------------------

Nothing is in place.

Users should be able to communicate with us about our code
----------------------------------------------------------

If people visit the source code pages they can contact the team there.

We should protect our users privacy
-----------------------------------

-  This is just a website, same concerns as our other websites.
-  No logins, or collection of users data
-  Cookie compliance should be assessed.
-  Terms and conditions have not been written and may need to be.

We should be clear about how we work with contractors
-----------------------------------------------------

N/A

If our code works with IATI data, have we considered how it will work as the IATI datasets grow, both in terms of individual file size and as a corpus
------------------------------------------------------------------------------------------------------------------------------------------------------

This application may struggle with very large files

Our code should be secure
-------------------------

This is a php site that takes user input, and XML files and parses them
and then displayes them back to the user.

 * XML injectiuon attack is considered
 * User input is escaped


We should know that our code is working properly
------------------------------------------------

Currently there is not a pingdom type service monitoring it for up time.

The iatiregistry does point people to this application, so problems are likely to be reported.



