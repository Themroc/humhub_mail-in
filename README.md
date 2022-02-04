### Description
Full featured module to let users post to spaces via email. Only space
members can post via email and only from their registered address. Emails
from any other address go into the 'ignored'-folder.

### Screenshot
![Themroc/humhub_mail-in screenshot](https://raw.githubusercontent.com/Themroc/humhub_mail-in/master/assets/screenshot1.png)

### Installation
Unzip this into */protected/modules/ and activate in it Administration / Modules.
Also activate the module in every space that should get it's own Email-address.

Needed configuration:

in **protected/config/console.php** add

    return [
        'components' => [
            'session' => [
                'class' => 'themroc\humhub\modules\mail_in\FakeSession',
            ],
        ],
    ];


Add another cronjob to the 2 ones already there for humhub. Something like


    0-59/5 *   * * *  /www/humhub/protected/yii mail_in/run

in **/etc/crontab** should do. The cronjob MUST run as the same user that
the webserver is running as. On debian based systems, that's usually
www-data. On those, you have to supply the username to the crontab line
like this:

    0-59/5 *   * * *  www-data  /www/humhub/protected/yii mail_in/run

Polling will be done every 5 minutes in these examples.

To test the email-setup, run **/www/humhub/protected/yii mail_in/dry**. This will
only read mails (and show you whats going on), but not post their content or
move them out of inbox.

Of course, you also need an mail-account for each space where mail_in is activated.
Use some provider that supports IMAP (pretty much all of them do, but lately you
have to activate that feature in their preferences). Ideally, the mailserver should
make sure that envelope-sender-adresses can not be faked, like by requiring SPF.

For IMAP-configuration, see <https://www.php.net/manual/en/function.imap-open.php>
section "Optional flags for names" for flags to append to the IMAP-source-string.

__Module website:__ <https://github.com/Themroc/humhub_mail-in>

__Author:__ Themroc <7hemroc@gmail.com>

### Changelog

<https://github.com/Themroc/humhub_mail-in/commits/master>

### Bugtracker

<https://github.com/Themroc/humhub_mail-in/issues>

### ToDos

- implement Maildir
- more testing
- allow posting to user profiles
- continue to not implement crappy protocols like POP3 or mbox

### License

GNU AFFERO GENERAL PUBLIC LICENSE
Version 3, 19 November 2007
https://www.humhub.org/de/licences
