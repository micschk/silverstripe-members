# SilverStripe FrontUsers Module

This module is a combination of functionalities of burnbright/silverstripe-members, i-lateral/silverstripe-users & silverstripe-australia/silverstripe-memberprofiles. Edited to be a compact, simple module based around one 'useraccounts' page and optionally a registration page. Idea is that controller configuration is handled by the developer and basic notifications & groups etc configuration from within the CMS.


TODO's
 - Check if members ->canEdit other members in Form::doUpdate
 - Finish registration steps (send verification & dont allow login unless verified)
 - A round of checks on account modification security

Adds various (optional) extra member features. They will not all be enabled by default.

 * Registration page
 * Profile page for updating details.
 * 
 * Send temporary password via email (todo).

## Add 403/forbidden ErrorPage to CMS
If a 403 forbidden page is present, this will be returned for disallowed actions instead of a 404 (e.g. editing another user's account (configurable))

## Overriding default config
You can override the default module config with your own by placing a yaml block in mysite/_config:
```yaml
---
Name: useraccountsdefaultconfigoverride
After:
  - '#useraccountsdefaultconfig'
---
UserAccountPage:
  allow_profile_viewing: false
```
 
## Registration Page

Optionally add a registration page in the CMS to allow registrations.

## User Account Page

User accounts can be viewed & edited (CRUD) one you have added a UserAccountPage to the CMS.

### Update Notifications

You can configure front-end user account registrations & updates to be notified to specific email addresses (from the UserAccountPage).

```yaml
Member:
    send_update_notifications: true
```

## Temporary Password Email (TO ADD BACK IN)

This is enabled by default.

## CSV Export Fields (TO ADD BACK IN)

This module introduces a way to define `export_fields` to for CSV exporting in yaml:

```yaml
Member:
  export_fields:
    FirstName: 'First Name'
    Surname: 'Last Name'
    Organisation.Name: 'Business Name'
    Email: 'Email Address'
```
