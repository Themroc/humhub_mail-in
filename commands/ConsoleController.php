<?php

namespace themroc\humhub\modules\mail_in\commands;

use DateTime;
use League\HTMLToMarkdown\HtmlConverter;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\post\models\Post;
use humhub\modules\file\models\File;
use humhub\modules\file\models\FileUpload;
use humhub\modules\content\models\Content;
use humhub\modules\content\permissions\CreatePublicContent;
use humhub\modules\content\components\ContentContainerPermissionManager;

use themroc\humhub\modules\mail_in\lib\mime_parser_class;

class ConsoleController extends Controller
{
    const MBX_POSTED= 'hh-posted';
    const MBX_IGNORED= 'hh-ignored';

    public  $user;

    private $dry;

    private $mbx_ignored;
    private $mbx_posted;
    private $f_get;
    private $f_move;
    private $f_close;

    ###########################################################################

    const IMAP_BODY_OPTS= FT_PEEK;

    private $imap;

    private function get_imap ($space) {
        $source= $space->getSetting('source', 'mail_in');
        printf("\n----------\nSpace: '%s' Source:'%s'\n", $space->getDisplayName(), $source);
        if (! preg_match('!([^:]+):([^@]+)@([^/]+)/?(.*)!', $source, $m))
            return null;

        $username= $m[1];
        $password= $m[2];
        $server= $m[3];
        $mailbox= empty($m[4]) ? 'INBOX' : $m[4];

        // Suppress bogus warning on imap_open (and non-bogus ones on imap_createmailbox)
        error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);

        $conn= "{".$server.'}';
        $this->imap= @imap_open(imap_utf7_encode($conn.'INBOX'), $username, $password);
        if ($this->imap === false) {
            printf("get_mails_imap(): Failed to open imap connection '%s' user:'%s' pw:'%s'.\n", $conn, $username, $password);
            return null;
        }

        //TODO: check if mailbox already exists
        imap_createmailbox($this->imap, imap_utf7_encode($conn.self::MBX_IGNORED));
        imap_createmailbox($this->imap, imap_utf7_encode($conn.self::MBX_POSTED));

        $this->mbx_ignored= imap_utf7_encode(self::MBX_IGNORED);
        $this->mbx_posted= imap_utf7_encode(self::MBX_POSTED);

        $r= [];
        $count= imap_num_msg($this->imap);
        printf("Imap '%s' opened, message count: %d\n", $conn, $count);
        for ($msgno= 1; $msgno<=$count; $msgno++) {
            $mail_hdr= imap_fetchheader($this->imap, $msgno);
            if (null === $user= $this->check_email($space, $mail_hdr)) {
                $this->move_imap($msgno, $this->mbx_ignored);
                continue;
            }

            $mail_body= imap_body($this->imap, $msgno, self::IMAP_BODY_OPTS);
            $ret_mail= $this->parse_mime($mail_hdr.$mail_body);
            $ret_mail['user']= $user;
            $r[ $msgno ]= $ret_mail;
        }

        return $r;
    }

    private function move_imap ($msgno, $mailbox) {
        if ($this->dry)
            return;

        imap_mail_move($this->imap, $msgno, $mailbox);
    }

    private function close_imap () {
        imap_expunge($this->imap);
        imap_close($this->imap);
    }

    ###########################################################################

    private $maildir;

    private function get_maildir ($space) {
        //TODO:
    }

    private function move_maildir ($msg, $mailbox) {
        //TODO:
    }

    private function close_maildir () {
        //TODO:
    }

    ###########################################################################

    private function html2md($html)
    {
        require_once(__DIR__.'/../lib/vendor/autoload.php');

        // see ../lib/vendor/league/html-to-markdown/README.md for possible options
        $conv= new HtmlConverter([
            'strip_tags' => 1,
#            'hard_break' => 1,

            // HumHub-style:
            'italic_style' => '*',
            'bold_style' => '**',
        ]);

        return $conv->convert($html);
    }

    private function parse_mime ($data) {
        require_once(__DIR__.'/../lib/rfc822_addresses.php');
        require_once(__DIR__.'/../lib/mime_parser.php');

        $mime= new mime_parser_class;

        /*
         * Set to 0 for parsing a single message file
         * Set to 1 for parsing multiple messages in a single file in the mbox format
         */
        $mime->mbox= 0;

        /*
         * Set to 0 for not decoding the message bodies
         */
        $mime->decode_bodies= 1;

        /*
         * Set to 0 to make syntax errors make the decoding fail
         */
        $mime->ignore_syntax_errors= 1;

        /*
         * Set to 0 to avoid keeping track of the lines of the message data
         */
        $mime->track_lines= 1;

        /*
         * Set to 1 to make message parts be saved with original file names
         * when the SaveBody parameter is used.
         */
        $mime->use_part_file_names= 0;

        /*
         * Set this variable with entries that define MIME types not yet
         * recognized by the Analyze class function.
         */
        $mime->custom_mime_types = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => [
                'Type' => 'ms-word',
                'Description' => 'Word processing document in Microsoft Office OpenXML format'
            ]
        ];

        $parameters= [
            /* Read a message from a string instead of a file */
            'Data' => $data,

            /* Save the message body parts to a directory     */
            /* 'SaveBody'=>'/tmp',                            */

            /* Do not retrieve or save message body parts     */
            'SkipBody' => 0,
        ];

        if (! $mime->Decode($parameters, $decoded)) {
            echo 'MIME message decoding error: '.$mime->error.' at position '.$mime->error_position;
            if ($mime->track_lines
            && $mime->GetPositionLine($mime->error_position, $line, $column))
                echo ' line '.$line.' column '.$column;
            echo "\n";

            return null;
        }

        for ($warning = 0, Reset($mime->warnings); $warning < count($mime->warnings); Next($mime->warnings), $warning++) {
            $w = Key($mime->warnings);
            echo 'Warning: ', $mime->warnings[$w], ' at position ', $w;
            if ($mime->track_lines
            && $mime->GetPositionLine($w, $line, $column))
                echo ' line '.$line.' column '.$column;
            echo "\n";
        }

        if (! empty($decoded[0]['DecodedHeaders'])) {
            $decoded[0]['OriginalHeaders']= [];
            foreach ($decoded[0]['DecodedHeaders'] as $k => $d) {
                $dec= '';
                foreach ($d as $e)
                    foreach ($e as $blk) {
                        $dec.= strtolower($blk['Encoding']) == 'utf-8'
                            ? $blk['Value']
                            : iconv($blk['Encoding'], 'utf-8', $blk['Value']);
                    }
                $decoded[0]['OriginalHeaders'][$k]= $decoded[0]['Headers'][$k];
                $decoded[0]['Headers'][$k]= $dec;
            }
        }

        return $decoded[0];
    }

    private function check_email ($space, $mail_hdr) {
        if (! preg_match('!^Return-Path:\s*<([^\r\n>]+)!mi', $mail_hdr, $m)) {
            printf("get_mails_imap(): Failed to get real sender address from '%s'.\n", $mail_hdr);
            return null;
        }
        $email= $m[1];

        if (null == $user= User::findOne(['email' => $email])) {
            printf("No user found with email '%s'.\n", $email);
            return null;
        }
        if (! $user->isActive()) {
            printf("Ignoring inactive user #%d '%s' email:'%s'\n", $user->getId(), $user->getDisplayName(), $email);
            return null;
        }

        // We can't just use  $perm= $space->getPermissionManager($user);
        // b/c that only works if Yii::$app->user is defined. Which apparently
        // is not the case in console-mode. So:
        $perm= new ContentContainerPermissionManager([
            'contentContainer' => $space,
            'subject' => $user
        ]);
        if (! $perm->can(CreatePublicContent::class)) {
            printf("User is not allowed to post here. User: #%d '%s' email:'%s'\n", $user->getId(), $user->getDisplayName(), $email);
            return null;
        }

        return $user;
    }

    private function selectPart($parts)
    {
        $pa= [];
        foreach ($parts as $part) {
            if (preg_match('!^text/plain\b!i', $part['Headers']['content-type:']))
                $pa['text']= $part;
            else if (preg_match('!^text/html\b!i', $part['Headers']['content-type:']))
                $pa['html']= $part;
            else {
                printf("Inoring part %s\n", var_export($part, 1));
            }
        }

        return ! empty($pa['html'])
            ? $pa['html']
            : (! empty($pa['text']) ? $pa['text'] : $parts[0]);
    }

    ###########################################################################

    /**
     * Console
     *
     * @return string
     */
    public function go()
    {
        //TODO: maildir
        $mode= 'imap';

        printf("Checking emails (mode:'%s')...\n", $mode);
        if ($mode == 'imap') {
            $this->f_get= 'get_imap';
            $this->f_move= 'move_imap';
            $this->f_close= 'close_imap';
        }
        else if ($mode == 'maildir') {
            //TODO:
        }

        $results= Yii::$app->search->find('', [
            'model' => Space::class,
        ])->getResultInstances();
        foreach ($results as $space) {
            if (! $space->isModuleEnabled('mail_in'))
                continue;

            $mails= $this->{$this->f_get}($space);
            foreach ($mails as $msgno => $m) {
                $u= $this->user= $m['user'];
                $h= $m['Headers'];
                $to= !empty($h['x-original-to:']) ? $h['x-original-to:'] : $h['Delivered-To:'];
                $from= preg_replace('!<?([^\s>]+)>?!', '\1', $h['return-path:']);
                $subject= $h['subject:'];
                $type= $h['content-type:'];
                $attachments= [];
                $msg= null;
/*
                if (preg_match('!^multipart\b!i', $type) && ! empty($body))
                    printf("Warning! Non-empty body in multipart-message ignored!\n");
*/
                if (preg_match('!^multipart/alternative\b!i', $type)) {
                    $msg= $this->selectPart($m['Parts']);
                }
                else if (preg_match('!^multipart\b!i', $type)) {
                    foreach ($m['Parts'] as $p) {
                        $h= $p['Headers'];
                        if (preg_match('!^multipart/alternative\b!i', $h['content-type:'])) {
                            $msg= $this->selectPart($p['Parts']);
                        } else {
                            if (empty($msg) && (preg_match('!^text\b!i', $h['content-type:']) || preg_match('!^inline\b!i', $h['content-disposition:'])))
                                $msg= $p;
                            else
                                $attachments[]= $p;
                        }
                    }
                    if (empty($msg))
                        $msg= $m;
                }
                else {
                    $msg= $m;
                }

                $body= preg_replace('!\s+$!s', '', $msg['Body']);
                $type= $msg['Headers']['content-type:'];
                $chs= '';
                if (preg_match('!([^;]+)\s*;\s*charset\s*=\s*"?([^";]+)"?!', $type, $ta)) {
                    $type= strtolower($ta[1]); $chs= strtolower($ta[2]);
                } else {
                    $type= strtolower($type);
                }
                $markdown= preg_match('!^text/html\b!i', $type) ? $this->html2md($body) : $body;
                printf("\nMail #%d - From:'%s' User:#%d '%s' To:'%s' Subject:'%s'\n", $msgno, $from, $u->getId(), $u->getDisplayName(), $to, $subject);
                printf("\tBody: Type:'%s' Charset:'%s' Size:%d bytes.\n", $type, $chs, strlen($body));
                if (! empty($subject))
                    $markdown= '__'.Yii::t('MailInModule.base', 'Subject: ') . $subject . "__\n\n" . $markdown;

                if (! $this->dry) {
                    Yii::$app->session->fakeSetUser($u);
                    Yii::$app->user->switchIdentity($u);

                    $post= new Post();
                    $post->content->container= $space;
                    $post->content->visibility= Content::VISIBILITY_PRIVATE;
                    $post->message= 'x';

                    // Assign post id, otherwise getFileManager()->attach() will complain
                    $post->save();
                    $post->refresh();

                    $p_id= $post->getAttribute('id');
                }

                $files= [];
                foreach ($attachments as $a_no => $a) {
                    $a_name= $a['FileName'];
                    $a_id= preg_replace('!<?([^>]+)>?!', '\1', @$a['Headers']['content-id:']);
                    $a_type= preg_replace('!;.*$!', '', $a['Headers']['content-type:']);
                    $a_disp= preg_replace('!;.*$!', '', $a['Headers']['content-disposition:']);
                    $a_body= $a['Body'];
                    printf("\tFile #%d: Type:'%s' Size:%d bytes Id:'%s' Name:'%s' Disposition:'%s'.\n", $a_no, $a_type, strlen($a_body), $a_id, $a_name, $a_disp);

                    if (!$this->dry) {
                        $file= new File([
                            'file_name' => $a_name,
                            'mime_type' => $a_type,
                            'size' => strlen($a_body),
                        ]);

                        // Assign file guid, otherwise getStore() will complain
                        $file->save();
                        $file->refresh();

                        $store= $file->getStore();
                        $store->setContent($a_body);

                        $fguid= $file->save();
                        if ($a_disp == 'inline')
                            $markdown= str_replace('cid:'.$a_id, $file->getUrl(), $markdown);
                        else
                            $files[]= $file;
                    }
                }

                if (!$this->dry) {
#                    printf("------\n%s\n------\n", $markdown);

                    $fm= $post->getFileManager();
                    $fm->attach($files);

                    $post->content->visibility= Content::VISIBILITY_PUBLIC;
                    $post->message= $markdown;
                    $post->save();

                    $this->{$this->f_move}($msgno, $this->mbx_posted);
                }

            }
            $this->{$this->f_close}();
        }

        printf("\n");
    }

    public function actionRun()
    {
        return $this->go();
    }

    public function actionDry()
    {
        $this->dry= 1;
        $this->go();
    }

}
