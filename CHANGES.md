Changelog
=========
## 2022/09/20 Eskul - Fixes text area field and metadata analysis style

## 2022/09/09 Eskul - Fixes Vulnerabilities and fix inscription update
***Fix***
- Fixed get file petition, now asks for admin or user property
- Fixed update my registry to a contest

## 2022/09/02 Eskul - Fixes Vulnerabilities xss injection
***Fix***
- text field inputs for both inscriptions and register forms with a regex (not allowing < and >)


## 2022/08/31 Eskul - Fixes Vulnerabilities and metadata fields
***Fix***
- text field input sanitized (no html tags admitted)
- Fixed date metadata field
- Fixed select metadata field
- 
## 2022/08/31 Eskul - Fixes Vulnerabilities and Select metadata field
***Fix***
- When a user get and entry by url, it shows any entry
- Select metadata field now show the options in different languages.
***TODO***
- Select metadata field keeps the value in the selected language.

## 2022/12/05 Eskul - Metadata Analysis, Meta Fixes and hardcodes
***Features***
- Metadata analysis (begining)

***Fixes***
- Description field centered
- not showing my payments if the contest has no payment methods
- print button only for admins

***Hardcode***
- Hardcode meta banners (by language)

## 2022/12/05 Eskul - Fix Terms and conditions
***Fix***
- Terms and conditions were not shown in register blade
- Terms and conditions in english (lang)

## 2021/12/03 Eskul - Fix Sent verification email
***Fix***
- Sent email verification email: button is disabled after click

***TODO***
- New page after clicking the button

## 2021/12/03 Eskul - Fix count all users systemMetrics
***Fix***
- SystemMetrics allusers changed to only count
- Disabled Contest Wizard

## 2021/12/03 Eskul - Public voting judge with no progress
- Public voting judge with no progress to avoid query overflow (entries.blade, voteSession.blade, VotingSession.php)

- ***TODO***
- update progress query

## 2021/11/30 Eskul - Public voting sessions with no progress
- Public voting sessions dont show progress to get better performance
- Public voting sessions requires users to verify email

***TODO***
- update progress query 

## 2021/5/17 Eskul - fixes & resetPassword
***Fix***
- collections categories and entries (from previous shortlist or marked shortlist in session)
- added admin can reset password buton in contest config 

## 2021/5/13 Eskul - fixes & resetPassword
***fix***
- fixed admin users list: entry card removed (overflowed by pictures)
- reset user password by contest admin
***TODO***
- reset user password enable/disable in new contest config

## 2021/4/19 Eskul - collections
***Features***
- Delete a collection

***fixes***
- contest with only 1 category
- collection with no prizes

## 2021/05/04 Eskul - system errors
***Features***
- checkbox in admins config "receive errors email"
- Capture errors and fatal errors and send an email to admins (option selected)

## 2021/4/14 Eskul - collections
***Features***
- Prizes filter 
- It shows the selected category and selected prizes on top of entries
- Fixed collection entry view style

## 2021/02/24 Pica - Fixed export judges excel when a voting user doesn't have an inscription
***Fixes***
- Fixed export judges excel when a voting user doesn't have an inscription
  (The only case was where the inscription was actually for a Collaborator, voting user id: 26902)

## 2021/02/24 Eskul - collections
***Features***
- CollectionKeys Model
- Collection key page
- If collection is private, it requiers a key or login
- key generates a user and is updated with its email in collectionKeys table 

## 2021/02/23 Eskul - collections
***Features***
- Private collection
- Invite to private collection by email or key code

## 2021/02/19 Eskul - collections
***Features***
- Search by name

## 2021/02/05 Eskul - collections
***Features***
- Filter entries in collection by prize

## 2021/02/05 Eskul - collections
***Features***

- Collection entry view
- Collection entry shows only selected metadata in collection config

## 2021/02/01 Eskul - collections
***Fixes***

- Show categories and category childrens

## 2021/01/12 Eskul - collections
***features***
- public collection view
- categories, entries list with name and prize (optional)

## 2021/01/12 Eskul - collections
***features***
- collections edit
    - name, start, end, private, metadata fields, voting session

## 2021/01/08 - Eskul - collections
***Features***
- Collections model
- Collections posts (create a collection)
- Collections get (get all collections - get a created collection edit)

## 2021/01/06 - Eskul - collections
***Features***
- Collection edit routes
- Collection edit blade
- Beginning of collection Edit options
  - name, dates, private (public is default) 

## 2021/01/05 - Eskul - collections
***Features***
- Collections tab for admins 
- Collections list view (all collections) 

## 2021/01/05 - Eskul - IAB Brief
***Hardcode***
- Hardcode brief email for IAB 2021 students

## 2020/11/17 - Eskul - Sockets servers
***Features***
- Metalero: moderator can guide the voting session one entry per time (optional)

## 2020/11/17 - Eskul - Sockets servers
***Features***
- Metalero: moderator can guide the voting session one entry per time

## 2020/11/17 - Eskul - Sockets servers
***Features***
- Added socketio
- socket factory (services.js)
- socket event for lobby in oxomeet

## 2020/10/12 - Eskul - OxoMeet server
***Feature***
- judges in lobby can return to call by moderator

## 2020/10/11 - Eskul - OxoMeet server
***Feature***
- Server can be setted in voting session

## 2020/07/06 - Eskul - OxoMeet server
***Feature***
- OxoMeet server is up, change API link to meet.oxoawards.com

## 2020/07/03 - Eskul - OxoMeet password and moderators
***Features***
- Added Password to oxomeet and moderators (comma separated)

## 2020/07/02 - Pica - OxoMeet panel resizer
***Feature***
- OxoMeet panel can be resized by the user
- OxoMeet panel can be set as fullscreen
- OxoMeet panel can be minimized
- OxoMeet panel can panned to columns

***Fixes***
- Fixed datetimepicker template path (relative, not absolute)
- Fixed position of oxomeet panel (fixed, not absolute)
- Fixed ngDraggable onmousemove after releasing the button inside an iframe

## 2020/06/24 - Eskul - OxoMeet over Voting session
***Feature***
- OxoMeet: in voting sessions, can add a jitsi meet link and it will be displayed in the voting session 

## 2020/05/13 - Eskul - Voting session

***Features***
- Judges Pagination (not in groups)

## 2020/03/10 - Eskul - checked Entry feature

***Features***
- New admin option Block finalized entries
- Check as reviewed the finalized entries
- Filter by finalized entries


## 2020/01/22 - Eskul - Entries and Mercado Pago SDK

***Features***
- Entries open in new tab from entries list
- Mercado Pago New SDK (Web checkout)

## 2019/12/09 - Eskul - Pdfs
Fixed pdf render when config attribute is null 

## 2019/12/04 - Eskul - logout fix 
- Fix logout problem
- Add static js-libraries (not from cdn)

 
## 2019/11/22 - Eskul - export results script (guion) 

- Generates a html instead of a .doc
TODO
- styles

## 2019/10/16 - Eskul - fix filter entries by not payed
- added user_id in orwhere clause when user filter by not payed

## 2019/10/16 - Eskul - send invites with codes
***Feature***
Resend emails and check if it already had a code associated

## 2019/10/16 - Eskul - admin users
***Fixes***
Fix in messages in entries list
Fix in admin results - yes/no voting system

## 2019/07/31 - Eskul - admin users
***Fixes***
 * Fixed screening 
    * Order of categories and entries
    * thumbnails

## 2019/05/20 - Eskul - admin users
***Features***
 Judges invitations with codes
 
## 2019/05/20 - Eskul - admin users
***Fixes***
* Fix autoabstains
* TODO
    * print-entries-pdf
        * Multiple with columns
        * images  
        
## 2019/05/20 - Eskul - admin users
***Fixes***
* Super admin can log to any user (in contest) from any page
* multiple field in entries excel export
***Features***
* Download users excel list from users view 

## 2019/05/20 - Eskul - PDF and entry
***Fixes***
* Fixed next and previous in voting session entry (conflict with newentriescontroller)
* Styles in pdf export, not finished

***TODO***
* Use newentriescontroller in voting session

## 2019/05/20 - Eskul - PDF and OxoValley
***Features***
* PDF export for entries
* can export pdfs
TODO 
* pdf style
* limit time exceeded (php) when there are more than 50 pdfs

***Fixes***

* Inscription metadata multiple fields

## 2019/04/17 - Eskul - Ranking
***Features***
* Set a default language for contest
(works with session variable, if language is changed, session is using that language
during the session)

## 2019/04/10 - Eskul - Ranking
***Features***
* Rankings in voting sessions
* User select the metadata and category
* Export excel with multiple sheets (one for each metadata field)
* Implement phpspreadsheet

## 2019/03/18 - Eskul - Entries filter
***Features***
* the contests dropdown in heaer now shows contests by status
* Fixed the view for mobiles
* new entries view, +new inscription is now on the sidebar

## 2019/03/18 - Eskul - Entries filter
Complex filter for admins

* Can filter by metadata (select the metadata field or in all metadata)

## 2018/09/24 - Pica - Error handler for vote request to show errors when it could not save the vote
***Features***
- When posting a Vote, if the request fails (ie. no internet) it resets the vote and shows a message of the error

***Fixes***
- Fix on ContestController undefined variable $countVotes
- Fix on ContestController undefined index $data['vote']

## 2018/09/21 - Pica - Quick fix to assign queued contestFileVersions to a manual encoder

## 2018/09/03 - Eskul - Public pages with entries
* Show incomplete fields in entries list *

## 2018/09/03 - Eskul - Public pages with entries
* Pages : entries are loaded by get, reducing the first load time

## 2018/08/17 - Eskul - Public pages with entries

*Features*
* Select entries from contest in public pages
    * By Entry status.
    * By csv search.
* Pages show entries w/o registration.

## 2018/08/17 - Eskul - OxoTickets
*Features*
* Payment methods
* Email to admins with the payment info

## 2018/08/10 - Eskul - OxoTickets
*Features*
* Email to buyer - code + QRCode - FIAP style.

## 2018/08/10 - Eskul - TextAngular
*Fixes*
* Fixed TextAngular lost focus
* Add regular expression for rich text, to avoid text style, etc
* Send emails to collaborators

## 2018/08/09 - Eskul - OxoTickets
*Features*
* Approve and disapprove tickets
* Registration of ticket users 
* Edit languague in buttons


## 2018/08/08 - Eskul - OxoTickets
*Features*
* tickets (code and QR)
* Check tickets (valid, invalid, unpaid, already checked)
* Laravel routes, if user is not logged in, is redirected to login

## 2018/08/01 - Eskul - OxoTickets
*Behaviour*
* Contest now can be created as OxoTickets
* Entries are now tickets groups
* Categories are tickets
* User can buy multiple tickets (categories)

## 2018/07/29 - Eskul - Voting groups, payed entry, import contest
*Voting groups*
* Filter hides the groups with no matches

*Payed entry*
* Email with template

*Import contest*
* User can only import contests that administrates

*Entry form*
* Fixed metadata fields width 

## 2018/07/29 - Eskul - Contest

*Feature*
* Filters in contest admin by status

## 2018/07/19 - Eskul - Billing 
*Feature*
* When an entry is payed, an email with info is sent to admins
info: users data, billing data, entry form.

* activated the export for effie latam 2018

## 2018/07/06 - Eskul - Many fixes 
*Fixes*
* Viewer user now can export data and see users
* Fixed entry form, adjust fields size

## 2018/06/27 - Eskul - Create contest
*Behaviour*
* Contest status
    * Wizard
    * Complete
    * Ready
    * Public
    * Closed
    * Banned

* When the contest is created, is in a complete status. The user send a habilitation request via contest.
The superadmins control the contests status from the "contests" menu. 
Once approved, the user can public its contest, via "public button" or setting open and close dates.

* checkRoutes.
The angular routes are verified, if user is not logged in and contest is not public, always redirect to landing page.
if user is not logged in and contest is public, always redirect to contest home.
if user is logged but try to access pages beyond permission, always redirect to home.

*Features*
* contests cron. Search for contests with open dates and set them as public or closed.

*Fixes*
* Contest wizard look and feel fixes.

## 2018/06/06 - Eskul - Create contest
*Features*
If user ask for create a contest
*Logged in: redirect to the create contest wizard
* Not logged in, redirect to login or register form
     * once registered, it redirects to applyForContest

## 2018/06/01 - Eskul - Routes Validation
*Features*
* Controllers js routes validation
    * If contest is in wizard status, only the creator can access, all other users are redirected to landing page
    * If contest is not logged in, all routes redirect to /home 

## 2018/06/01 - Eskul - Wizard
*Features*
* User can create a contest
    * pick a name and a code if available
    * Select to begin wizard or import and old contest
    * Contest is created and user is assigned as admin
* Step by step wizard
    * User inscriptions (select if gonna have or not)
    * entries (select if gonna have or not)
    * Categories (only if entries is selected)
    * Billing (only if entries is selected)
    * Style
    * Dates
* Wizard stores status and config
* if the creator user is in wizard step and leaves the platform, next time it will start from last checkpoint 

## 2018/05/18 - Pica - Many fixes on files uploads cancels and deletes
*Fixes*
* File uploads error now change the contestFile and contestFileVersion status to Upload interrupted
* ContestFileVersions on Uploading are now checked against the upload timeout to check if the upload was interrupted
* Errors trying to update the uploadProgress will cancel the upload entirely
* Cancelling an upload now refreshes the files list on myfiles to removed the canceled file
* On ContestFile delete the file is now deselected from the entry, and if the file is uploading it is canceled

*Features*
* Better style on files status on "my files" 

## 2018/05/17 - Pica - Fixed checkUserPermission function when nobody is logged

## 2018/05/17 - Pica - Script to update all contest banners to HTML banners
*Features*
* A new url (admin/fixBanners) that updates all banners
    * Uploads the banner to GCS and deletes the contents from DB
    * Updates the ContestAssets type to GeneralFile
    * Creates a new HTML Banner using the old banner url inside

*Behaviour changes*
* Recovered BIGBANNER const from ContestAsset to make the script work
* Removed $IMAGES static array from ContestAssets as it is no longer used
* Removed getAssetURL function from Contest as it is no longer used

## 2018/05/17 - Eskul - filters in users inscriptions list 
*Features*
* Filters in users inscriptions list by inscriptor, judge, owner, colaborator and inscription types

*Fixes*
* Fixed labels shown in sign up page, when the registration is closed, it shows the form disabled.
 
## 2018/05/17 - Pica - Banners HTML default on contest creation, using a view
*Features*
* New contests will have a basic HTML banner

*Behaviour changes*
* Removed BIG_BANNER const from ContestAsset as it is no longer used
    * Public contests used to show their BIG_BANNERs on the website home. This has been disabled
      and should be replaced with the BIG BANNER HTML.
* Removed BIG_BANNER and SMALL_BANNER from default ContestAssets to be created on contest create 

## 2018/05/16 - Pica - Contest Assets on Google Storage
*Features*
* ContestAssets files are now saved on Google Cloud Storage instead of MySQL
* Added UploadFileToGCStorage function to Cloud class

*Behaviour changes*
* Removed post asset url (ContestController::postAsset) as it was only used to save banners
* ContestAssets URL functions to redirect the file to a GCS signed url
* Added extension attribute to ContestAsset class

## 2018/05/15 - Pica - Contest Assets delete
*Features*
* Delete Contests Assets from assets section list
* Added a link to the contest Home on the sidebar
* Banner editor advanced view to edit raw HTML

## 2018/05/14 - Pica - Documents thumbnails inside file selector
*Features*
* Documents thumbnails in file selector in entries form

*Behaviour changes*
* Files thumbnails are now displayed as backgrounds that have size contain, instead of an img tag

## 2018/05/14 - Pica - Documents thumbnails url
*Features*
* Now documents have thumbnails, but the encoder works only with PDF files

## 2018/05/14 - Pica - Contest Assets section
*Features*
* Added a new section to list and upload static assets for the contest
* ContestAsset new contest_type attribute
* Format new function getTypeFromMimeType to get the file type from mime/type (content_type for ContestAsset)
* Upload files to create ContestAssets

*Behaviour changes*
* ContestAsset's url now uses the id instead of the type (we used to use type because there where going to be always one of each type per contest)
* ContestAsset's url will throw an error on asset not found instead of a generic placeholder
* ContestAsset data now has a preview attribute to preview images on admin list
* Added GENERAL_FILE const to ContestAsset to save static assets for contests
* Removed unused IMPORT_FILE const from ContestAsset

## 2018/05/14 - Pica - Saving banners HTMLs
*Features*
* Saving banners as ContestAssets from header-banner directive
* New contest API endpoint to save ContestAssets

*Behaviour changes*
* Removed Big banner and Small banner editor from Styles admin

## 2018/05/14 - Pica - Banner editor styles and data injection
*Changes*
* Moved textAngular toolbar to the bottom on banner html editor
* Retrieved banner data from injected contest service

## 2018/05/09 - Pica - Connected controller with DOM on HTML banners directive
*Fixes*
* Fixed banner min-height so buttons are always displayed
* Added banners ContestAsset object to contest constant on angularjs
* Added textAngular editor on banner to start implementing WYSIWYG banners editor
* Removed unused files for textAngular

## 2018/05/09 - Pica - Fixed print styles
*Fixes*
* Fixed entry print styles
    * Removed font size from styles attributes
    * Changed some font sizes on app.css for printEntry modal

## 2018/05/09 - Pica - Updated ContestFile status to Upload Interrupted when source fileversion is in that state

## 2018/05/08 - Pica - Fixed files error message style while modifing the entry

## 2018/05/08 - Pica - Files status styles changed
*Behaviour changes*
* New ContestFile checkVersionsStatus function to check if all fileversions threw an error to set the contestFile status to error
* Files on file panel now show their status differently:
    * Queued and available files are shown without status
    * Encoding and uploading files are shown with a progress bar
    * Files with errors now show an error message indicating the user to reupload the file or select a different one
    * Files canceled or with the upload interrupted are also shown with a message to replace the file
    * This function is only called on the /files API endpoint
* User on files list in the Tech view

*Fixes*
* Fixed progress calculation for a file
    * When uploading the progress is according to the source fileversion
    * When encoding the progress is calculated on all non source fileversions that are not canceled nor with error
* Fixed check for upload interrupted on "uploading" files (it used to check on encoding files)
* Fixed some styles on files panel (removed some row classes)
* Removed an .error class that was way too global to use
* Fixed files styles for dark theme
* Fixed structure of the user inscriptor form for modal (click on user name for admin)

## 2018/05/07 - Pica - Started HTML banners implementation
*Features*
* headerBanner directive to control banner editor in home section
* Started banner editor in contest home section
* New Contest checkUserPermission function to validate a permission for the current user, including owners and superadmins 

## 2018/05/07 - Pica - My payments view
*Features*
* Users payments views for list and payment details
* GetUserPayment api call to get details of a payment for current user

*Behaviour changes*
* Changed billing setup routes
* getCategoriesData API call now without Contest admin filter so users can get categories data

## 2018/05/04 - Pica - Inscriptos section to view all their payments
*Features*
* New section for users to see their payments
* Added the payments list view for that section
* New API endpoint for inscriptors to request their payments

*Fixes*
* Fixed billings filters buttons styles

## 2018/05/03 - Pica - Fixed sidebar and messages on registration form

## 2018/05/03 - Pica - Sidebar on all sections
*Features*
* Sidebar on entries list and entry form
* Sidebar on entries list for voting
* Sidebar on registration form (only for registered users)
* Sidebar on my files and tech files

*Fixes*
* Fixed spacing between containers and banners
* Fixed some sections headers tags
* Fixed the-sticky-div error on loading entries list section without entries

## 2018/05/03 - Pica - Sidebar on all sections
*Features*
* Added sidebar with options to the left on the entries section
* Sidebar now available for inscriptors and judges
* Added files and registration to sidebar options
* Added a few method to Contest model to check if isAdmin, isOwner, isInscriptor, isJudge

*Fixes*
* Better logic in isCollaborator method on Contest model

*Behaviour changes*
* Moved contest tabs view file from admin to contest folder 

## 2018/05/10 - Eskul - Feature newsletters
*features*
* Newsletters 
* Emails to all contest users, only inscriptors or only judges 

*To Do*
* Send emails in background

## 2018/05/03 - Pica - Fixed Title field class to separate the description help-block

## 2018/04/25 - Eskul - import, style, filters, voting sessions and billing
*Fixes*
* Fix light style
* Fix Filter judges in voting session by invite status
* FIX show no voting sessions for judges
* FIX resend mails
* Finish import contest (style and voting sessions)

*Features*
* Added cancel payment (only for admin, super admin and collaborators)

## 2018/05/03 - Eskul - Fixed signup dates check
*Features*
* Sign up page now controls the inscription dates for inscriptors and judges, and shows alerts

## 2018/05/03 - Pica - PDF player
*Features*
* Now PDFs are iframed inside the player

*Fixes*
* Fixed some JS errors and logo in 404 page
* Fixed player modal contest height to remove scrollbar

*Behaviour changes*
* Removed PDF from the list of downloadable formats

## 2018/05/03 - Pica - Fixed images zoom
*Fixes*
* Fixed imageZoomer size and scales after changing the image
* Disabled imageZoomer transitions while loading
* Fixed an issue when loading lightbox with an undefined file
* Removed some console.log

## 2018/08/02 - Pica - Format active attribute for use at platform level
*Features*
* Format active attribute to select formats to create FileVersions
    * Contests will no longer setup which formats they want to use
* Format admin
* Shown active status on Formats list
* New Cloud Config option to have a default storage sources bucket selected on new contests form
* Added max entries default value on new contests form

*Behaviour changes*
* ContestFileVersions are now created for each Format active for that file type
* Removed all relations between Contest and Format
    * Removed ContestFormat model
    * Removed Formats selection in Contest admin
    * Removed Contests panel in Formats admin
    * Removed usage of getMissingFormats javascript function 

## 2018/04/27 - Pica - Fixed new message e-mails template
*Fixes*
* Fixed new message e-mails template
* Fixed use of placehold.it images with https
* Fixed text of "notification settings" link in e-mail template footer

## 2018/04/25 - Pica - Blocked notifications according to user settings
*Features*
* Blocked notifications according to user settings
    * New user
    * New entry
    * Entry finalized
    * Entry with error
    * Entry paid
    * New message
    * Entry approved

## 2018/04/25 - Pica - Notifications users settings
*Features*
* users can change their notification settings in the account config panel (although it doesn't work yet)
* Added some functions to userInscriptions factory

*Fixes*
* Fixed logo on login and register page for light/dark theme
* Fixed "Unsuscribe" link in e-mails template

## 2018/04/25 - Pica - Styles fixes
*Features*
* Updated FontAwesome
* Added icon for collaborator and owners inscriptions
 
*Fixes*
* Fixed header logo for light/dark themes
* Fixed contest home menu design button to be visible by admins and superadmins
* Fixed contest home menu design link
* Fixed popover background color, help-block font color, and panel-heading font color
* Removed unnecessary console.log

## 2018/04/24 - Pica - History and entries list styles fixed
*Features*
* OxoMailer now has an enable property to disable sending e-mails from config

*Fixes*
* Fixed entries columns width
* Fixed messages (history) styles
* Fixed deadlines validation rules for contest admin

## 2018/04/23 - Pica - Fixed css files order
*Fixes*
* Fixed css files order
* Removed entry selected style for effie

## 2018/04/23 - Pica - Dark and Light themes
*Features*
* Added bootstrap css file in public folder (we won't use bootstrap cdn anymore)
* Used Contest style attribute to load dark or light stylessheets
* Used Bootstrap Slate as Dark theme and Lumen as Light theme
* Changed header logo for dark and light themes
* Added edit dates link in deadlines
* Added Contest custom style to css

*Fixes*
* Fixed row class on entries tool bar
* Fixed footer class
* Fixed drop-down styles
* Fixed header users dropdown styles
* Fixed entry page navigation between entries style

## 2018/04/20 - Pica - Theme style selector in contest styles admin
*Features*
* Contest class with style and custom_style attributes for selecting a theme and saving custom css code
* Admin panel in Contest styles admin for saving styles attributes

## 2018/04/25 - Eskul - voting sessions
*Features*
* Auto Abstains, can select if showing or not the abstained entries

*Fix*
* Fixed auto abstains 
* Fixed some filters 

TODO
check all the results filters for the diff types of votes 

## 2018/04/19 - Eskul - voting user and voting user in group
*Features*
* entries in voting group user are shown with categories tree.
* Can add individual entries to a judge in a voting group

## 2018/04/18 - Eskul - voting group
*Features*
* entries in voting groups are shown with categories tree.

## 2018/04/13 - Eskul - login as from contest
*Features*
* New import contest, with its own menu

*IAB*
* blocked registration delete

## 2018/04/11 - Eskul - login as from contest
*Features*
* Can login as any user of a contest, from the navbar

*Fixes*
* User form with templates, as shown in contest users

## 2018/04/09 - Eskul - import categories
*Fixes*
* templates in import categories and forms had problems with ids, fixed

*ToDo*
* Fix multiple payments with Mercado Pago

## 2018/03/23 - Pica - E-mail notifications on entry finalized, approved and errors
*Features*
* Centralized e-mail sent on changeEntryStatus, using ContestAssets instead of languages for the body of the e-mails
* Created ENTRY_FINALIZED_MAIL ContestAsset, with admin on emails section

*Fixes*
* Removed :sitelink replace on emails as it isn't used
* Commented all lines on changeEntryStatus where it sends an e-mail with language
* Fixed max_entries error message on Contest main form

## 2018/03/19 - Pica - Changed the way that pages work
*Behaviour changes*
* Pages now work with contest home.js controller, loading a view and the contents through xhr requests
* Pages links now have a hashtag before page/

## 2018/03/19 - Pica - Max entries per contest
*Features*
* Contests can be configured to allow a max number of entries per user
* Edit max entries from admin
* Block create new entry from entries list, entry form and entry save api call

## 2018/03/16 - Pica - Contest pages now have url
*Features*
* ContestsAssets of type STATIC_PAGE now have an url to view its contents

*Fixes*
* Fixed error page styles (removed red background)
* Added error message text to 404 error

*Behaviour changes*
* ContestAssets now have a code attribute, which is the name in an url friendly fashion
* ContestController now has a getContestMainData function to centralize the inscriptions and contest data usage for angular js apps

## 2018/03/16 - Pica - Added public/docs to git to upload files to server
*Fixes*
* Fixed multiple html editor on styles admin form

## 2018/03/13 - Pica - Changed styles of metadata field rows when previewing a template

## 2018/03/09 - Pica - Vote config per category (only for votetype yes/no so far)
*Features*
* postEntryCategoryVote user the voteconfig object on VotingCategory to changes other votes to yes or no
depending on the voting category config
* postVotingSession saves the voteconfig object on VotingCategory
* form-voting now uses the categoryTreeVoteConfig.html ng-template to allow vote config per category
* VotingCategory calls json_decode on vote_config on toArray
* Added yesPerCategory config on category header

## 2018/03/09 - Pica - Changed Inscription name property to invitename to avoid ambiguity in queries

## 2018/03/08 - Pica - Added name to inscription to send personalized invite e-mails
* Added name property to Inscription model
* Saved name for inscription on invite new judges
    * Names are added after the e-mails, separated with a space
* Small fixes in Entry class

## 2017/05/05 - Pelado - GC Instances List
* Added in the admin home the list of instances deployed according to defined zones
in the cloud config

## 2017/04/28 - Pica - Uploads changes
* Cancel upload deletes file and storage media
* Pause and resume upload
* Upload interrupted status test
* My files styles changed
* Uploading progress shown on files

## 2017/04/24 - Pica - Execute CloucManager after upload
* ContestFileVersion status Encoded to Available
* Reencode resets status to Queued only on non source ContestFileVerions
* Source ContestFileVersion status to Uploading on NewFile
* Reencode resets cloud_encoder_id to null

## 2017/04/04 - Pica - Fixed player bug when changing from video to video or audio to audio

### 2017/04/12 - Pica - Select entries styles
* Filters for judges

### 2017/04/11 - Pica - Discounts model save
* Remove discount
* Filtros de entries ocultos por default
* Discounts with value and change percentage/price

### 2017/04/10 - Pica - Discounts model
* Request de config de payments con discounts
* Pequeños fixes de estilos para el home

### 2017/04/05 - Pica - Metalero de shortlist puede ver todos los entries
* Botón de cambio entre shortlist y todos los entries
* Request de entries filtrado por resultados de la votación marcada para shortlist
* Request de progreso filtrado por resultados de la votación marcada para shortlist
* GetAllEntriesResults from voting session inside Class
* Opción de editar shortlist de sesión de votación anterior
* Botón de editar shortlist para jurado
* Botón de reload

### 2017/03/31 - Pica - Google Cloud Storage Download
* Get ContestFileVersion URL for Cloud Storage
* Fixes in files-panel directive
* Fixes in tech view
* Delete files from Cloud Storage
* Stream files from Cloud Storage

### 2017/03/31 - Pica - Google Cloud Storage Upload done
* Select file immediately after upload starts
* ContestFile Uploading status
* Stream thumbnails from previews bucket
* No thumbnail default images

### 2017/03/29 - Pica - Google Cloud Storage Upload
* Google Cloud API integration
* Upload with flow directly to GCS
* Inform files to be uploaded before upload
* Inform upload progress to GCS
* Create ContestFile with ContestFileVersion with bucket

### 2017/03/28 - Pica - Google Cloud Config
* Config Contest Sources Bucket
* Singleton to connect to Google Cloud
* ContestFileVersion bucket field

### 2017/03/21 - Pica - Campo de texto con formato

### 2017/02/21 - Pica - Cambio de chequeo de inscripción para encuestador
* Luego del login redireccionar al registro de inscriptor O jurado, segun sean publicos
* Mail al administrador al registrarse un nuevo jurado o inscriptor

### 2017/01/06 - Pica - Link de ClicPago para comprar códigos

### 2017/01/03 - Pica - Fixes finales para ClicPago

### 2016/12/27 - Pica - Implementación de ClicPago
* Controller para recibir el pago (transactionBackUrl)
* Fixes de escapeo al pegar los datos encriptados de clicpago

### 2016/12/19 - Pica - Implementación de ClicPago
* Guardado de configuración de ClicPago en Contest
* Armado de links para redireccionar al usuario a ClicPago
* Inicio de recepción de pago

### 2016/11/25 - Pica - ContestFileVersions devueltos por position de Format
* Administrador de Formats para propiedad position

### 2016/11/23 - Pica - Campo de fecha con límites

### 2016/11/23 - Pica - Fix de asignación de entrycategories a un jurado
* Cambios de contador, listado y guardado de entry categories de un votinguser
* Arreglo en listado de entries de una sesión de votación con categories_id filtrados
* Cambios de template de CategoryList por CategoryListVoting

### 2016/11/21 - Pica - Fix de contadores del jurado (en vista del jurado)

### 2016/11/20 - Pica - Thumbnails en lista de entries de jurado
* Metalero vota desde la lista de entries

### 2016/11/18 - Pica - Guardado de estado de los pagos de MercadoPago

### 2016/11/17 - Pica - Fixes Diente 2016
* Categorías del entry para los administradores
* Fix de errores de entry sin nombre para los administradores
* Error handler para responder las peticiones que esperan json con el trace del error
* Anterior y siguiente en entry dentro de la categoría desde donde se lo abrió
* Mostrado solo el votador de un entry de la categoría desde donde se lo abrió
* Cantidad de entries en categoría y suma de subcategorías
* Voto desde lista de entries

### 2016/11/16 - Pica - Arreglos para votación del Diente
* Filtrado de categorías en listado de entries por configuración de la sesión de votación
* Filtrado de categorías en el entry y sus votos según la configuración de la sesión de votación
* Arreglos de herramienta de votación Veritron
* Arreglos de herramienta de votación de metales
* Resultados de votación filtrados por categorías (categorías y entries)
* %Si para veritron

### 2016/11/14 - Pica - Técnico: Crear FileVersions de formatos faltantes
* Fix de rotación de imágenes a 0 grados
* Fix de botón de reencode que no hacía nada

### 2016/11/07 - Pica - Finalizada la integración de MercadoPago
* Cambiado de lugar la configuración de MercadoPago
* Fix de página de bienvenida cuando el pago no se realizó
* Agregada la URL de notificaciones en el administrador de métodos de pago
  
### 2016/11/01 - Pica - Métodos de pago: Tarjeta de crédito y Otro
* Comentarios guardados en Billing
* Administración de billing para owner y collaborator con billing

### 2016/10/27 - Pica - Config para path de uploadchunks

### 2016/09/19 - Pica - Vista estática para Sesiones de votación
  * Fix de ng-init en form-voting para los configs de votos
  
### 2016/10/31 - Pica - MercadoPago
  * Config de MercadoPago en contest
  * Página de redirección de MercadoPago
  * URL de reportpayment para recibir notificaciones IPN (falta terminar proceso)
  * Billing status: Partially paid
  * Fix de header cuando no hay un banner subido
  
### 2016/08/09 - Pica - Códigos de invitación para jurados
  * Model de VotingSessionKey
  * Generación de keys de invitación con grupos
  * Página de bienvenida de código de invitación
  * Generación y autologin de códigos de invitación

### 2016/08/08 - Pica - Progreso de jurado con grupos
  * Cálculo de jurados que votan un entry
  * Resultados con jurados asignados con grupos
  * Cantidad de jurados que votan asignados con grupos

### 2016/08/05 - Pica - Guardado de grupos de jurados
  * Fix: Ejecutar encoder al tirar a reencodear desde el técnico
  
### 2016/08/04 - Pica - Grupos de jurados en Voting Session
  * Creación de grupos
  * Progreso de votación de grupo
  * Voting Users en múltiples Voting Groups
  * Dropdown de jurado
  * Cambio de grupo de jurado (No guarda todavía)
  * Invitaciones en un grupo

### 2016/08/01 - Pica - Página estática con categorías y entries (Screening)

### 2016/07/26 - Pica - Vote config con MinVotes
  * CategoryManager guarda la lista de cats con id como índice
  * Configuración default de lista de resultados
  * Lista de resultados por categorías con breadcrums, no escalonadas

### 2016/07/19 - Pica - Entries ordenados por resultado en voting session

### 2016/07/19 - Pica - Entries filtrados por categorías en resultados de voting session

### 2016/07/15 - Pica - Entries filtrados por categorías
  * Agregada la directiva in-view
  * Reemplazo del bind en scroll de la lista de entries por in-view
  * Nuevo loading en entries
  
### 2016/07/13 - Martin - Sort de jurados

### 2016/07/12 - Martin - Fix de progreso de jurados
  * Agregado un jurado al ingest de latam effie

### 2016/07/11 - Martin - Voting Bottom Html
  * Agregamos un contest asset llamado Voting Bottom Html
  * Figura debajo del listado de sesiones de botación del usuario (/CONTEST_CODE/voting)
  

### 2016/07/09 - Pica - Votos extra
  * Guardado y listado de votos extra
  * Resultados con votos extra
  * Filtro de resultados por query
  
### 2016/07/07 - Pica - Envío de invitaciones a jurados con OxoMailer

### 2016/07/06 - Pica - Rechazo de invitación de jurado

### 2016/07/04 - Pica - Proceso de invitación de jurados
  * Aceptación de invitación con login o register
  * Para invitaciones a usuarios registrados hay sólo login
  
### 2016/07/04 - Pica - Proceso de invitación de jurados
  * Status en VotingUser
  * Envío de e-mail con link de invitación
  * Fix en página de verify email
  
### 2016/07/04 - Martin - Metricas
  * Agregamos actualizacion de datos cada 5 segundos
  * Metricas de Network
  * Metricas de usuarios
    * Totales, activosy verificados
    * Conectados
      * Listado de usuario conectador con boton de LOG IN AS.

### 2016/07/01 - Pica - Admin de 'Otros' en config de Vote

### 2016/07/01 - Pica - Admin de comments en config de Vote y otros fixes

### 2016/07/01 - Pica - Resultados de VotingSession
  * Listado de entries a votar en voting session
  * Resultados para votos de tipo Average
  * Cantidades de votos, jueces y abstenciones

### 2016/06/30 - Martin - Agregamos Metricas
  * Creamos en laravel un model para captar las metricas del sistema SystemMetrics
  * Metricas de CPU
  * Metricas de HDD

### 2016/06/30 - Pica - Cambio de lista de sesiones de votación
  * Directiva de judgeProgress
  * Fix de información del api para jurados
  * Fix de autoupdate de jurados
  * Fix del $watch del voto al entrar a un entry

### 2016/06/29 - Pica - Votos del jurado en listado de entries
  * Vote-Result-Tool
  * Vote-Tool con readOnly y hideResult
  * Cambios del comportamiento del Vote-Tool

### 2016/06/28 - Pica - VotingSession: Progreso de jurados
  * Fix de total de entries a votar
  * Votos realizados del jurado
  * Progreso total de la votación
  * Autoupdate
  * Cantidad de abstenciones

### 2016/06/28 - Pica - VotingSession: Progreso de jurados
  * Total de entries a votar por el jurado

### 2016/06/27 - Pica - VoteTool: Average vote con criterios
  * Resultado ponderado con weight de voto con criterios
  * Directiva include-replace

### 2016/06/24 - Pica - FIX IMPORTANTE: guardado de VotingCategories
  * VoteTool
  * VoteTool preview en admin de Voting Session
  
### 2016/06/24 - Pica - VotingUserEntryCategory
  * Cambio de edición de categorías en admin de voting session

### 2016/06/22 - Pica - Recuperado el guardado de categorías en Voting Session
  * Borrado de jurados en sesión de votación
  * Arreglos de urls y acceso a voting session por code, no por id

### 2016/06/16 - Pica - Jurados en voting session
  * Listado de jurados asignados a voting session
  * Quitado el boton de eliminar cuenta!
  * UserCard con email opcional
  * SoftDelete en Inscription
 
### 2016/06/14 - Pica - Envío de los emails para invitar jurados en voting session

### 2016/06/07 - Pica - Admin de sesiones de votación para tipo de voto

### 2016/06/03 - Pica - Administrador de superadmin movido
  * Edición de formatos en tab general de superadmin
  
### 2016/06/02 - Pica - Administrador de sesiones de votación con ABM completo
  * Cambio de controles para cambiar de categoría con un contest con 1 categoría por entry
  * Modal con errores al guardar un entry finalizado con errores

### 2016/06/02 - Pica - Arreglos de estilos en vista de entries como thumbs
  * Fix de borrado de categorías

### 2016/05/31 - Pica - Admin de billing
  * Editado del precio del entry por categoría
  * Busqueda de facturación por número de entry
  * Fix de estilos en home de contest
  * Acomodadas algunas secciones con el botón modificar 
  
### 2016/05/27 - Pica - Fix al cambiar de categoría un entry pagado

### 2016/05/10 - Pica - Spinner abajo de los entries

### 2016/05/09 - Pica - Listado de entries por partes
  * Separación de entries en rows con control de scroll
  * Cambiado el sistema de filstros y contadores
  * Quitado el tab de la vista de print

### 2016/05/04 - Pica - Datos de entries en Billing
  * Bug fix: No se podían pagar entries vacíos

### 2016/05/03 - Pica - Descarga de documentos
  * Links de descarga de documentos en files gallery
  * Devolución de contestfileversions con sources
  * Fix de status de ContestFile en Encoded cuando no necesita encodear

### 2016/04/07 - Pica - De todo un poco
  * Quitado el portugues
  * Fix de validación por php para las inscripciones
  * Quitado el requerido de los tabs
  * Templado de formulario con botón de guardar/modificar anclado
  * Alert de cargando en administrador de contest
  * Datos de usuario en formulario de registro

### 2016/04/07 - Pica - Entries por usuario
  * Entries por usuario, en menu de entries, para admin, owner y colaboradores

### 2016/04/06 - Pica - Campo de archivo requerido

### 2016/04/06 - Pica - Algunos arreglos
  * Fix de links de templado de mails
  * Nuevos términos y condiciones
  * Fix de oAuth en mi perfil

### 2016/04/05 - Pica - Esconder descripciones de campos
  * Validación interna del Entry
  * Listado de campos incompletos

### 2016/03/30 - Pica - Traducción de Columnas y opciones

### 2016/03/30 - Pica - Fix de guardado de entries existentes
  * Filtros para no guardar campos no editables (title, description, tab)
  * Fix para no guardar registro de campos multiple vacío o files vacíos
  * Modificado el manejo de la función getMetadata

### 2016/03/29 - Pica - Arreglos de status de entries en JS para billing
  * Fixes para poner 2CheckOut en producción
  
### 2016/03/29 - Pica - Footer de formularios anclado abajo
  * Ajustes varios de estilos

### 2016/03/29 - Pica - Fix de formularios sin tabs y tabs en entry

### 2016/03/29 - Pica - Tabs en metadata
  * Tipo de campo Tab
  * Preview de formulario
  * Tabs en entry y en signup
  
### 2016/03/23 - Pica - Estado Incompleto en Entry
  * Validación de formulario para estado completo e incompleto
  * Validación de formulario para evitar guardado una vez finalizado o aprobado
  * Fix para un nuevo campo en formulario y los EntryMetadataTemplate
  * Fix de botón agregar campo al no editar el entry
  * Fix de guardado de entry existente

### 2016/03/22 - Pica - Filtrado de campos en formulario de entry
  * Ocultados y requeridos los campos según EntryMetadataTemplate
  
### 2016/03/22 - Pica - Captcha en delete Contest y delete User
  * Captcha en borrado de entries
  * Validador de Inscription y creador de entry al borrarlo
  
### 2016/03/21 - Pica - EntryMetadataTemplate con EntryMetadataField y Categories
  * Guardado de configuración de campo de EntryMetadata y EntryMetadataTemplate
  * Configuración de EntryMetadataTemplate y Categories
  * Previsualización de formulario con filtro de EntryMetadataTemplate

### 2016/03/18 - Pica - Editor de EntryMetadataTemplateConfig

### 2016/03/09 - Pica - Introducción del EntryMetadataTemplate
  
### 2016/03/07 - Pica - Relación entre Category y MetadataField
  * Cargada la relación en el contest para editarla
  * Funciones de checkall y uncheckall para MD y categories
  * Fix en funciones de checkall y uncheckall para inscriptiontype y category
  * Campo visible por hidden en EntryMetadataConfigCategory
  * Templado para configurar hidden y required en una categoría

### 2016/03/02 - Pica - Relación entre Category y MetadataField
  * Modal de configuración de relación

### 2016/02/29 - Pica - Admin de propiedad privada en metadatafield de entries

### 2016/02/26 - Pica - Chequeos de pagos realizados por entry
  * Guardado de fecha de pago al administrar un pago
  * Guardado de precio de BillingEntryCategory
  * Modal de listado de pagos
  * Pagos individuales para categorías no pagas de entries

### 2016/02/25 - Pica - Service para categories
  * Datos de categorías en billings
  * Fecha de pago realizado en model (falta guardar)
  * Precio en BillingEntryCategory
  * Listado de pagos realizados en entry-pay-form

### 2016/02/22 - Pica - Pago de entries sin finalizar
  * Actualización del billing del entry luego de pagar
  * Fix de display de descripciones del formulario con nl2br
  * Fix de display de valores del formulario con nl2br

### 2016/02/18 - Pica - Billing status en lista de entries
  * Entries pedidos con billings
  * Reordenamiento de datos en entry
  * Botón para pgar entry
  * Deshabilitada el registro por oauth

### 2016/02/17 - Pica - Bill admin
  * Cambio de estado de un pago
  * Guardado de configuración de 'Un entry por categoría'
  * Guardado de configuración de 'Forzar Prepay'

### 2016/02/15 - Pica - Billing admin
  * Guardado de entries y categorías del billing
  * Campo description en Billing
  * Factory de Alert
  * Arreglo de botones del filtro de entries
  * Admin de billing, listado y vista de Bill
  * Fix de pagination a uib-pagination

### 2016/02/12 - Pica - Arreglo de locale en moment.js
  * Fix de botones de filtros
  * Pago con 2CheckOut
  * Config de billing sandbox
  
### 2016/02/12 - Pica - Cambios del home del sitio
  * Administración de contest: public
  
### 2016/02/11 - Pica - Guardado de Billing al finalizar un entry
  * Campos de payment_data, error y currency en billing
  * GetPrice en Category
  * GetTotalPrice en Entry
  * Relación con Categorías en Entry
  * Agregada la librería de 2Checkout a composer
  * Filtro de angularjs nl2br
  * Fix de GetCategoryPrice en angularjs

### 2016/02/10 - Pica - Cambios del model de Billing
  * Fix de seguridad de entry sifter
  * Fix de botón de quitar error para el inscriptor
  * Update structure.sql

### 2016/02/05 - Pica - Formulario de datos de tarjeta de TCO
  * Precio base y moneda en contest
  * Precio en categoría
  * Formulario de finalización con detalle de pago
  * Config de billing

### 2016/02/02 - Pica - Formulario de datos de tarjeta de TCO
  * Petición de token para realizar el pago
  
### 2016/02/01 - Pica - Entry card, actions
  * Entry card
  * Datos de billing guardados en objetos
  * Actions en inglude
  * Estilos de Entry log
  * Fix de registro con números en los campos (mostraba fechas)
  * Arreglos de blades para modals
  * Comienzo de formulario de billing

### 2016/01/29 - Pica - Cambios de estilos de botones de tamizador

### 2016/01/29 - Pica - Fixes de angular-ui.bootstrap

### 2016/01/28 - Pica - Administrador de billing en contest
  * Guardado de datos de billing para transferencia y cheque
  * Guardado de datos de billing para 2CheckOut

### 2016/01/27 - Pica - Fixes para ir a oxoawards.com
  * Fix de seguimiento de archivo de entorno local
  * Arreglos de estilos del gallery
  
### 2016/01/27 - Pica - Abierto el administrador del contest al superadmin

### 2016/01/27 - Pica - Control de status de file en gallery
  * Actualización de status y progress en archivos desde file-panel
  
### 2016/01/27 - Pica - Actualización JWPlayer
  * Quality switcher en jwplayer
  * Título de entry en lightbox
 
### 2016/01/26 - Pica - JWPlayer para video y audio 
  * Pasado el field al scope del modal del Lightbox
  * Agregada la librería de angular-jwplayer
  * Listado de ContestFileVersions en files y entry
  * Route del archivo del ContestFileVersion
  * Fix de translation en previews del editor de formularios
  
### 2016/01/20 - Pica - Player con lightbox
  * Agregada la librería angular-bootstrap-lightbox
  * Actualizada la librería ui-bootstrap
  * Borrado de archivos y thumbs
  * Sacado el Captcha del eliminar archivo
  * Directiva para galería de archivos
  * Cambios en lightbox para fullscreen
  * Fix del disabled en signup
  
### 2016/01/20 - Pica - Fix de guardado de entries
  * Arreglo de generación de códigos random para ContestFile y VotingSession

### 2016/01/19 - Pica - Flags en idiomas
  * Cambio de en => us y pt => br
  
### 2016/01/19 - Pica - Redirect en cambio de idioma
  * InscriptionType con traducción
  * Fix de descripción de categorías
  * LangDropdown en angular con controller propio
  
### 2016/01/18 - Pica - Descripción del MetadataField

### 2016/01/18 - Pica - Idiomas en contest
  * Directivas de traducción
  * Editado y guardado de traducciones para formularios de inscripción y entries
  * Editado y guardado de traducciones para tipos de inscripción

### 2016/01/18 - Pica - Categorías con idioma
  * Directiva de traducción de categoría
  * Arbol de categorías en entries con traducción
  * Dropdown para formulario de edición de entry con traducción
  * Breadcrumbs de categoría con traducción
  * Fix Files panel route
  
### 2016/01/15 - Pica - Categorías con idioma
  * Service para angularjs con los idiomas
  * Editor de categorías con name y description traducibles

### 2016/01/13 - Pica - Cambio de idioma
  * Dropup para cambiar el idioma
  * Routes y controllers para cambiar el idioma
  * Arreglos de inglés
  * Fix de guardado de formulario de entries
  * Descripción de categorías
  * Preview de formulario en editor de formulario de entries
  * Recuperado el SiteController

### 2016/01/13 - Pica - Fix del drag and drop de campos de formulario de registro
  * Fix en signup
  
### 2016/01/08 - Pica - Fixes varios al crear un contest
  * Fix de JS al administrar formularios de inscripción
  * Fix de deadlines y mensajes de error
  * Fix de name de entry
  * Fix de reglas de save de deadlines
  * Fix de guardado de formulario de entries con campos nuevos

### 2016/01/06 - Pica - Arreglos varios
  * Arreglos de permisos para super user para crear entries de otros usuarios
  * Arreglos de permisos para super user y colaboradores para seleccionar files de otro user
  * Fix de view para eliminar inscripciones
  * Fix de título del asset escondido para algunos roles
  * Botones de listado de entries colapsados
  * Fix en creación de user nuevo como superadmin
  
### 2016/01/06 - Pica - Panel de archivos
  * Cambio de funcionamiento y estilos de listado de mis archivos
  * Cambio de vista de listado
  * Fix para archivos seleccionados
  * Fix para adjuntar archivo subido
  * Acomodado el lugar de mis piezas
  
### 2016/01/05 - Pica - Arreglos varios
  * Includes para categorías
  * Arreglo de título de entry
  * Recuperados algunos elementos de la UI (selector de usuarios, botones)
  * Dehabilitado el formulario de inscripción para el owner o colaborador
  
### 2015/12/30 - Pica - Arreglos de permisos
  * Previsualización de formulario de registro
  
### 2015/12/28 - Pica - Arreglos varios de admin 
  * constante inAdmin para saber si estamos dentro del administrador del superadmin
  * Recuperado el borrar tipo de inscripción
  
### 2015/12/28 - Pica - Arreglos de forms de inscripción
  * Fix error de js
  * Controller para panel de usuario
  * Service de userInscriptions con update y delete inscription
  * Fix para borrar inscripción
  * ToggleCat en toda la línea
  * Algunos cambios de routes
  
### 2015/12/22 - Pica - Arreglos de forms de inscripción
  * Escondidos algunos campos en varios models para que no se impriman en los services
  * Movidos los valores de los formularios de registro en los services de angular
    del contest a los inscriptions

### 2015/12/22 - Pica - Cambios varios
  * Recuperado el nombre del entry desde metadata importante
  * Cambiado updateSignup por updateInscription
  * Eliminar instripción a contest arreglado
  * Listado completo de entries y por categorías

### 2015/08/03 - Pica - Fix de administrador de contest

### 2015/07/17 - Pica - Listado de entries en estructura de carpetas
  * Service contest para pedir el objeto del contest donde estamos
  
### 2015/07/17 - Martin - Seeding

### 2015/07/16 - Pica - Factory de UserInscriptions
  * Creado factory para injectar en runs y controllers para pedir y validar las inscripciones del usuario
  * Route y resolves para inscriptor y jurado
  * Api de entries separada para jurado, sin terminar

### 2015/07/14 - Pica - Administración de deadlines por inscription type
  * Edición y guardado de fechas de start, deadline 1 y deadlin2 para los inscription types
  * Botón de registro como inscriptor y como jurado en home de contest
  * Chequeo de deadlines por inscription types
  * Chequeo de inscription types públicos
  * Indicador de manejo de deadlines desde inscription types
  * Inicio de routes y formulario de registro para inscriptor y jurado
  * Registro como inscriptor o jurado
  * Home con botones para mis inscripciones y ver inscripciones

### 2015/07/14 - Martin - My files
  * Agregamos en la directiva un interval para chequear el status de los archivos
  * Progress bar

### 2015/07/14 - Pica - Administración de deadlines por inscription type
  * Edición y guardado de fechas de start, deadline 1 y deadlin2 para los inscription types
  * Botón de registro como inscriptor y como jurado en home de contest
  * Chequeo de deadlines por inscription types
  * Chequeo de inscription types públicos
  * Indicador de manejo de deadlines desde inscription types
  * Inicio de routes y formulario de registro para inscriptor y jurado

### 2015/07/14 - Martin - My files
  * Agregamos en la directiva un interval para chequear el status de los archivos
  * Progress bar

### 2015/07/10 - Martin - My Files
  * Arreglamos errores
  * Llevamos a una directiva el panel de archivos
  * Falta actualizar status del archivo (progreso)
  * Agregar una vista de la relacion con los entries

### 2015/05/13 - Pica - Inicio de chequeo de deadlines para inscripciones

### 2015/05/11 - Pica - Edición de deadlines
  * Edición del tipo de registro en el editor de una inscripción
  * Editor de deadlines en línea de InscriptionType
  * include para datetimepicker
  * Agregada la librería angular-moment

### 2015/07/10 - Martin - My Files
  * Arreglamos errores
  * Llevamos a una directiva el panel de archivos
  * Falta actualizar status del archivo (progreso)
  * Agregar una vista de la relacion con los entries

### 2015/05/08 - Pica - Deadlines para InscriptionType, e Inscription

### 2015/05/07 - Pica - Deadlines para InscriptionType
  * Administrador de fechas del contest para el administrador de contest
  * Formulario de configuración de deadlines para el InscriptionType
  * Include para editor de deadlines, usado en form de fechas del contest y form de inscription type, y luego en form de inscription
  * Fix de error en login de administrador con adminServices
  * Fix de error en formulario de registro sin inscription

### 2015/05/06 - Martin - Manejo de thumbnails.

### 2015/05/05 - Pica - Deadlines modificados en contest
  * Modificados los deadlines en un Contest, con editor
  * Agregados los deadlines (start,deadline1,deadline2) a InscriptionType y Inscription

### 2015/05/07 - Pica - Deadlines para InscriptionType
  * Administrador de fechas del contest para el administrador de contest
  * Formulario de configuración de deadlines para el InscriptionType
  * Include para editor de deadlines, usado en form de fechas del contest y form de inscription type, y luego en form de inscription
  * Fix de error en login de administrador con adminServices
  * Fix de error en formulario de registro sin inscription

### 2015/05/05 - Pica - Deadlines modificados en contest
  * Modificados los deadlines en un Contest, con editor
  * Agregados los deadlines (start,deadline1,deadline2) a InscriptionType y Inscription

### 2015/05/05 - Pica - Fixes de renombres

### 2015/05/04 - Pica - Renombramiento de clases, tablas, indices, relaciones
  * categories_inscription_types_config		->	category_config_type
  * contest_inscription_metadatas			->	inscription_metadata_fields	
  * contest_metadatas						->	entry_metadata_fields
  * contest_metadata_config					->	entry_metadata_config_category
  * file_versions							->	contest_file_versions
  * inscription_metadatas					->	inscription_metadata_values
  * inscription_metadata_config				->	inscription_metadata_config_type
  * metadatas								->	entry_metadata_values
  * metadata_has_files						->	entry_metadata_files
 
### 2015/04/30 - Pica - Estilo para ramas de las categorías 

### 2015/04/29 - Pica - Edición de relaciones entre InscriptionType y Categories 
  * Model de CategoryInscriptionTypeConfig
  * Formularios de edición de configs en modal
  * Edición de relación de Category con InscriptionType desde ambos lados
  * Edición de relación de InscriptionType con Metadata
  * ng-templates para category tree
  
### 2015/04/29 - Martin - Encoders.
  * Finalizamos los encoders de Video, Image y Audio.
  * Resta el de Document.
  * Agregamos creación de thumb de Audio (waveform), imagen y video.

### 2015/04/28 - Pica - Configuración de MetadataInscription por InscriptionType
  
### 2015/04/27 - Pica - Inicio de configuración de MetadataInscription por InscriptionType
  * Controller para el inscriptiontype y su config
  * Guardado de configuración
  * Inicio de listado de configs
  * DropDowns con checkboxes
  
### 2015/04/24 - Pica - Handle error en peticiones http
  * Fixes varios del handle error
  * Nuevo template de editor de metadata y de inscriptionsMetadata

### 2015/04/23 - Pica - Finalización de llevar admin de contest del front-end de nuevo al back-end
  * Mejora de feedback de guardado
  * Arreglo de mensajes de error
  * Separados los services de angularjs para el superadmin
  * Eliminado el service de Contest, reemplazado por peticiones $http
  
### 2015/04/22 - Pica - Inicio de llevar admin de contest del front-end de nuevo al back-end

### 2015/04/22 - Pica - Orden final antes de llevar admin de contest del front-end de nuevo al back-end
  * Mejorado el feedback de guardando y guardado
  * Cambios de estilos en los títulos y popovers de explicaciones
  * Pequeñas mejoras de estilo
  
### 2015/04/21 - Pica - Formularios de administración de contest en front-end
  * Listado de páginas, eliminar página
  * Editor de páginas en administrador front-end
  * Listado de inscripciones
  * Visualización de formulario de inscripción (sin guardar)
  * Formulario de estilos de contest con guardado de HTMLs
  * Routes de administración de contest para front-end
  
### 2015/04/20 - Pica - Inicio de separación de views/controllers/resolves de Angular y Laravel antre adminsitrador de contest y superadmin
  * Creado el ContestAdminControllers de AngularJS para llevar al front-end el control de un contest
  * Route de Laravel Auth/check para chequear también si es superadmin 
  * ContestAdminController de Laravel para llevar los views y actions del Contest al front-end
  * Filter para chequear que sea un admin del contest indicado en la URL por el código de contest
  * Filter para chequear que tenga una inscripción al contest indicado en la URL por el código de contest
  * Inicio de cambio de URLs del API para utilizar el código de contest y no el id
  * Formularios de edición de contest con dos layouts, para superadmin y para admin en el front-end
  * Fix de login, redirect y demás cosas en el admin
  * Intento fallido de configuración de Captcha
  
### 2015/04/17 - Martin - Ffmpeg wrapper
  * Wrap para el uso del FFMPEG

### 2015/04/15 - Pica - Selector de archivos dependiendo del usuario seleccionado (para admin de contest)
  * Dropdown en panel de usuario con los Contest donde tengo una inscripción

### 2015/04/14 - Pica - Creador de entries por usuario para admin, login views fix
  * Directive para User Card
  * Reload forzado de la ventana al loguearte
  * Return a la url anterior luego de redirigirte al login y loguearse
  * Agregado un include en el layout default para los ng-templates
  * Creación de entries para un usuario determinado desde un Owner o Colaborador

### 2015/04/13 - Pica - Listado de entries para admin, y editor de entries para admin
  * Filtro de búsqueda de entries y sortBy por id, nombre y usuario
  * Entry con atributo name sacado de main_metadata y main_metadata privada
  * User card para mostrar siempre a los usuarios igual
  * Selector de usuarios con user card

### 2015/04/10 - Pica - Inicio de movida del administrador del contest al front end
  * Injección del Inscription en el index de un contest
  * Sección de código onRun de angularJS

### 2015/04/10 - Pica - Reordenamiento general de archivos
  * Angular Routes movidos a la carpeta de los controllers
  * base y currentBase reemplazados por constantes rootUrl y currentBaseUrl
  * Mejorado el funcionamiento y la visualización de la página de reestablecer contraseña
  * Mejorado el funcionamiento de las páginas del sitio

### 2015/04/09 - Pica - Fix del error 401 al subir muchos archivos
  * Cambio de driver de session para utilizar mysql en vez de files para resolver error 401
  * Paginado, filtrado y ordenado de archivos por PHP en vez de angular
  * Fix para usuario logueado sin inscripción en página de contest
  * Agregado el campo de role para InscriptionType y para InscriptionMetadataField
  * Administrador de Formulario de inscripción para roles
  
### 2015/04/08 - Pica - Funciones del panel de archivos
  * Bloquear cambio de página, cierre de tab o refresh si se está subiendo algún archivo\
  * Cancelar todas las subidas
  * Limpiar la lista de subidars
  * Fix de falta de inscripción en página de contest, por no auth o por no inscripción
  * Código único para archivos
  * Descripción de configuración de campo de archivos

### 2015/04/07 - Pica - Paginación, sort de files y rename de files
  * Paginación de mis files, con datos en angularjs\
  * Sort de files por nombre y fecha, en ambas direcciones
  * Rename file
  * Delete file con captcha
  
### 2015/04/06 - Pica - FIX de redireccion, tipos y nombre de archivo
  * Fix de redirección al home
  * Guardado de archivos sólo con nombre
  * Mostrado el tipo de archivo

### 2015/04/01 - Martin - Admin
  * Agregamos páginas estáticas.

### 2015/04/01 - Martin - Admin
  * Finalizamos el manejo de inscripciones de los usuaios dentro de un concurso.

### 2015/03/31 - Martin - Admin
  * Continuamos con el listado y edición de las incripciones de un concurso

### 2015/03/30 - Martin - Admin
  * Agremamos listado de Registros + edición de los mismos
  
### 2015/04/01 - Pica - Guardado de selección de archivos y manejo de archivos
  * Guardado de archivos seleccionados en campo de metadata
  * Mejor sortable de archivos seleccionados
  * Borrado de archivos
  * Filtro de archivos por nombre
  
### 2015/03/31 - Pica - Upload y selección de archivos
  * Creación de ContestFile y FileVersions según configuración del Contest
  * Servicio de angularjs para manejar UserMedia (my files)
  * Mejoras de actualización de UserMedia
  * Selección y sort de archivos en campo de metadata
  
### 2015/03/30 - Pica - Upload de archivos
  * Editor de MetadataField de tipo archivo, con configuración
  * Vista de formulario de MetadataField de tipo archivo
  * Subida de archivos y creación de ContestFile y FileVersion del source
  * Listado simple de ContestFiles en vista de mis piezas
  * Listado de categorías en los que está un entry, en listado de entries
  * Mejora de grilla de a 4 columnas
  * Listado de formulario de entry sólo cuando hay una categoría seleccionada
  * FileVersion status y percentage
  * Tipo de Format: Otro

### 2015/03/27 - Pica - Mejoras del formulario de entries
  * Vista estática del entry
  * Actualización de datos al guardar
  * ContestAsset Home Bottom HTML
  
### 2015/03/27 - Martin - Admin users
  * Log in as
  * Sistema de tabs
  * Agregamos listado con las inscripciones del usuario

### 2015/03/26 - Martin - Estilo del contest
  * Agregamos un editor WYSIWYG
  * Agregamos carda de imágenes (BASE 64)

### 2015/03/26 - Pica - Creación y guardado de entries
  * Validación de metadata
  * Guardado de entries y categorías
  * Borrado de categorías
  * Dropdown para agregar categorías

### 2015/03/25 - Pica - Formulario de creación y edición de entries

### 2015/03/20 - Pica - Listado de mis inscripciones por lista y categorías
  * Listado de entries con metadata ordenada
  * Colapsar y expandir categorías

### 2015/03/19 - Pica - Inicio de sección de mis inscripciones
  * Routes, controllers para los datos y los views
  * Listado simple de entries y cambio de visualización

### 2015/03/19 - Pica - Guardado de formulario de metadata de entries
  * Centralizados los views de edición de campos de metadata
  * Centralizadas las Clases de InscriptionMetadataField y ContestMetadata extendiendo MetadataField
  
### 2015/03/18 - Pica - Guardado de categorías
  * Guardado de categorías con nombre, parent_id, descripción, orden y final
  * Agregado de categorías y subcategorías
  * Eliminación de categorías (y subcategorías)

### 2015/03/17 - Pica - Administrador de categorías
  * Sortable entre listas
  * Petición de Contest con Categorías recursivas
  * Controller y formulario con visualización de categorías (falta edición y guardado)
  
### 2015/03/13 - Pica - Administrador de formulario de inscripción a concursos
  * Controller de Contest para guardar tipos de inscripciones y formulario de inscripción
  * Arreglos para indicar la sección correcta en administrador
  * Modificación del formulario de contests para mostrar mensajes al guardar y no redireccionar
  * Agregada la librería ui-sortable y jquery-ui

### 2015/03/12 - Pica - Selección de tipo de inscripción en formulario

### 2015/03/11 - Pica - Formulario de inscripción a festival, creación de inscripción
  * Tipos de campos para inscripción a contest: Text, Textarea, Select, Multiple, Date, E-mail, Title, Description
  * Validación de campos requeridos y algunos tipos de campos (date, e-mail)
  * Agregado checklist-modal
  * Términos y condiciones en fomulario de inscripción (falta terminar modal)
  * Guardado de inscripción del usuario al contest (falta guardar metadata)
  
### 2015/03/10 - Pica - Formulario de inscripción a festival
  * Chequeo de existencia de inscripción del usuario
  * Inicio de creación de formulario de inscripción
  
### 2015/03/09 - Pica - Página de inicio de concurso y 404
  * Página de inicio de Concurso resolviendo datos
  * Página de error 404 con global handler
  * Agrupados los routes de contest bajo un sólo pattern
  
### 2015/02/25 - Pica - Formulario de contacto en la home
  * Formulario de contacto en el home del sitio
  * Slides para carousel en el home
  * Arreglos varios de estilos
  
### 2015/02/23 - Pica - Idioma en e-mails
  * Links corregidos en templado de e-mails
  * Fix de información del usuario para JS
  * Arreglos varios de estilos

### 2015/02/23 - Pica - Páginas institucionales
  * Página de inicio con templado
  * Home con formulario de contacto
  * Home con listado de clientes
  * Página de Política de privacidad template
  * Página de Términos de uso template
  
### 2015/02/13 - Martin - Cambio en el sistema de TAB y en el manejo del formulario

### 2015/02/13 - Martin - Cambio en el sistema de TAB y en el manejo del formulario
  
### 2015/02/13 - Pica - URL para contests
  * Routes para home de contests
  * Login compacto para incluir
  * Arreglo de comportamiento de redirecciones con autenticación
  * Templado de prueba de home de contests
  * Header fluido y fijo

### 2015/02/13 - Pica - Arreglos de estilos
  * Fixes de posibles problemas de seguridad
  * Algunos arreglos de links

### 2015/02/09 - Martin - CRUD
  * TABS Form.

### 2015/02/06 - Pica - Filtro para superadmins
  * Administrador de permisos de usuario: superadmin

### 2015/02/03 - Pica - Servicio Imagick para manipular imágenes
  * Imagen de perfil de usuario con 3 tamaños

### 2015/02/03 - Martin - CRUD
  * TABS Form.

### 2015/01/30 - Pica - Captcha
  * Agregado el chequeo de Captcha en el registro de usuarios
  * Fix para el error al cargar con hash vacío

### 2015/01/30 - Martin - CRUD
  * CRUD de Contest Files.

### 2015/01/29 - Pica - Imagen de perfil
  * Upload de imagen de perfil con flow-ng
  
### 2015/01/28 - Pica - OAuth de Facebook, Twitter y Google+
  * Registro y login con OAuth de Facebook, Twitter y G+
  * Desvinculación del servicio con la cuenta del usuario
  * Barra de mensajes de sesión
  
### 2015/01/27 - Pica - OAuth de Facebook
  * Actualización de OAuth para versionado del GraphAPI de Facebook v2.2
  * Inicio de sesión y registro de usuarios con OAuth de Facebook

### 2015/01/26 - Martin - CRUD
  * Reorden de los Controllers de Angular para el administrador. 
  * CRUD de Formats.

### 2015/01/26 - Martin - CRUD

### 2015/01/24 - Pica - Update AngularJS 1.3.10

### 2015/01/23 - Pica - Cambio de idioma
  * Verificación de e-mail
  * Configuración de registro
    * Deshabilitación de registro de usuarios
    * Deshabilitación de registro con redes sociales
    * Autologin
    * Allow Unverified Users

### 2015/01/22 - Pica - Mi cuenta
  * Cambiado el campo name de User por first_name y last_name
  * Inicialización de currentUser en angularjs desde $rootscope
  * Encabezado con panel de usuario
  * Mi cuenta, con edición de datos personales
  * Mi cuenta - Seguridad, con cambio de contraseña
  * Configuración de Mi cuenta, Borrar cuenta
  
### 2015/01/21 - Pica - Remember password email templates
  * Templado para e-mails
  * E-mail de requisito de cambio de contraseña y de confirmación de cambio de contraseña
  * Archivos de idiomas

### 2015/01/20 - Pica - Remember password routes y form
  * Views con blade templates
  * Validación de contraseña alfanumérica y mínimo 6 caracteres
  * Recuperar contraseña completado
  * Modal template
  * Modal de términos y condiciones

### 2015/01/19 - Martin - CRUD
  * Seguimos completando el idioma
  * Continuamos con el CRUD de Contests
  * Implementación del Datetime picker
  
### 2015/01/19 - Martin - CRUD
  * Seguimos completando el idioma
  * Comenzamod con el CRUD del los CONTESTS

### 2015/01/19 - Martin - Lenguaje
  * Seteo del lenguaje
  * Un archivo por view
  * Españo e Ingles.

### 2015/01/19 - Pica - Remember password
  * View, table y controller para Remember password
  
### 2015/01/16 - Pica - Routes para login y registro globales
  * Comienzo de implementación de remember me
  * AuthService para manejo de sesión entre controllers de angularjs

### 2015/01/15 - Pica - Registro de usuarios
  * Fix en migration de tabla users para password
  * Orden de carpetas de apps y controllers
  * Validación y errores en formulario

### 2015/01/14 - Pica - Formulario de registro
  * Agregado el tema Slate
  * Separados los controllers de login, site y admin
  * Agregados los toolsControllers para manejar, por ahora, alerts
  * Inicio de validación y registro de usuario
  * Arreglado el bootstrapping

### 2015/01/14 - Pica - Separación de Sitio y Admin
  * Routes de Laravel agrupados para sitio y admin
  * app.js y adminApp.js
  * loginControllers.js y adminControllers.js
  
### 2015/01/12 - Pica - Merge de AdminUsers con master
  * Corrección de htmls a phps de Laravel

### 2015/01/06 - Pica - Sesión de JS compartido entre tabs
  * initController: Chequeo de sesión en la raiz y redirección a home o login
  
### 2014/12/19 - Pica - Uploader con Flow.js para subir archivos grandes por chunks
  * https://github.com/flowjs/ng-flow
  * https://github.com/flowjs/flow.js
  * https://github.com/flowjs/flow-php-server

### 2014/12/18 - Pica - SortBy y numeración de paginado en administrador de usuarios

### 2014/12/16 - Pica - Paginación de administrador de usuarios
  * Paginador para servicio con RequestFactory

### 2014/12/15 - Pica - Validación de administrador de usuarios
  * Validación de formularios desde AngularJS
  * Validación de formularios desde Laravel

### 2014/12/12 - Pica - Administración de usuarios
  * Elimiado de usuarios con modal de confirmación desde lista y formulario de edición
  * Modificación de contraseña con repetición de contraseña
  * Creación de usuario nuevo con contraseña
  * Validación de angularjs para formulario de usuarios

### 2014/12/11 - Pica - Inicio de administrador
  * Controller de administrador
  * Listado de usuarios
  * Formulario de edición y creación de usuarios
  * Header compartido
  * Creación, guardado y borrado de usuarios

### 2014/12/05 - Pica - Actualizado el jwplayer con licencia Premium, con skin

### 2015/01/06 - Pica - Adaptación de TUAGrid a Awards2
  * Inicio de login con oAuth para Facebook, Google y Twitter
  * Views desde Laravel
  * Manejo de authentication y angularjs