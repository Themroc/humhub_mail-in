### Description
Full featured module to let users post to spaces via email.

### Installation

Add folder */protected/modules/mail_in, unzip this into it and activate the
module Administration / Modules.

Needed configuration:

in **protected/config/console.php** add

    return [
        'components' => [
            'session' => [
                'class' => 'themroc\humhub\modules\mail_in\FakeSession',
            ],
        ],
    ];


In **/etc/crontab** add something like

    */5 *   * * *  /www/humhub/protected/yii mail_in/run

The cronjob MUST run as the same user that the webserver is running as. On debian
based systems, that's usually www-data. On those, you have to supply the username
to the crontab line like this:

    */5 *   * * *  www-data  /www/humhub/protected/yii mail_in/run

On other boxes (where cron-jobs are always run as root), you may need

    */5 *   * * *  su -c "/www/humhub/protected/yii mail_in/run" -l www

or similar.

To test the email-setup, use **/www/humhub/protected/yii mail_in/dry**. This will
only read mails (and show you whats going on), but not post their content or
move them out of inbox.

Of course, you also need an mail-account for each space where mail_in is activated.
Either use some provider like gmail via IMAP (TODO: or run a local one like postfix on
the same box that humhub runs on. In the latter case, the module can access mails
directly in Maildir, which causes less overhead than IMAP). The mailserver should
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
