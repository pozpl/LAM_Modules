<?php
/*
// To connect to an IMAP server running on port 143 on the local machine,
// do the following:
$mbox = imap_open("{localhost:143}INBOX", "user_id", "password");

// To connect to a POP3 server on port 110 on the local server, use:
$mbox = imap_open ("{localhost:110/pop3}INBOX", "user_id", "password");

// To connect to an SSL IMAP or POP3 server, add /ssl after the protocol
// specification:
$mbox = imap_open ("{localhost:993/imap/ssl}INBOX", "user_id", "password");

// To connect to an SSL IMAP or POP3 server with a self-signed certificate,
// add /ssl/novalidate-cert after the protocol specification:
$mbox = imap_open ("{localhost:995/pop3/ssl/novalidate-cert}", "user_id", "password");

// To connect to an NNTP server on port 119 on the local server, use:
$nntp = imap_open ("{localhost:119/nntp}comp.test", "", "");
// To connect to a remote server replace "localhost" with the name or the
// IP address of the server you want to connect to.
*/

//$mbox = imap_open("{localhost:143/notls}", "pozpl", "ghjcnj_,ju");
//$mbox = imap_open("{localhost:143/notls}", "cyrus", "Njht0l0h");
//,OP_HALFOPEN)

$mbox = imap_open("{localhost:993/ssl/validate-cert}", "cyrus", "Njht0l0h", OP_HALFOPEN)
        or die("can't connect: " . imap_last_error());

$name1 = "phpnewbox";
$name2 = imap_utf7_encode("phpnewb&ouml;x");

$newname = $name1;

// we will now create a new mailbox "phptestbox" in your inbox folder,
// check its status after creation and finaly remove it to restore
// your inbox to its initial state

if (@imap_createmailbox($mbox, imap_utf7_encode("{localhost:993/ssl/validate-cert}user.$newname"))) {
    //imap_close($mbox);
    //$mbox = imap_open("{localhost:143/tls/novalidate-cert}user.$newname", "cyrus", "Njht0l0h", OP_HALFOPEN)
    // or die("can't connect: " . imap_last_error());

    //imap_reopen($mbox, "{localhost:143/tls/novalidate-cert}user.$newname") or die(implode("Can't reopen, ", imap_errors()));

    $list = imap_list($mbox, "{localhost:143/tls/novalidate-cert}", "user.$name1");
    if (is_array($list) && sizeof($list) == 1) {
        //if(sizeof($list) == 1) {
            foreach ($list as $val) {
                echo $substr_count = substr_count($val,"user.$name1") . " <br/>";
                echo imap_utf7_decode($val) . "<br/>\n";
            }
        //}
    } else {
        echo "imap_list failed: " . imap_last_error() . "\n";
    }


    //$status = imap_status($mbox, '{localhost:143/tls/novalidate-cert}user'.$newname, SA_MESSAGES);
    $status = @imap_status($mbox, "{localhost:143/tls/novalidate-cert}user.$newname", SA_ALL);
    if ($status) {
        echo "your new mailbox '$name1' has the following status:<br />\n";
        echo "Messages:   " . $status->messages    . "<br />\n";
        echo "Recent:     " . $status->recent      . "<br />\n";
        echo "Unseen:     " . $status->unseen      . "<br />\n";
        echo "UIDnext:    " . $status->uidnext     . "<br />\n";
        echo "UIDvalidity:" . $status->uidvalidity . "<br />\n";

        if (imap_renamemailbox($mbox, "{localhost:143/tls/novalidate-cert}INBOX.$newname", "{localhost:143/tls/novalidate-cert}user.$name2")) {
            echo "renamed new mailbox from '$name1' to '$name2'<br />\n";
            $newname = $name2;
        } else {
            echo "imap_renamemailbox on new mailbox failed: " . imap_last_error() ."<br/>". implode("<br />\n", imap_errors()) . "<br />\n";
        }
    } else {
        echo "imap_status on new mailbox failed: " . imap_last_error() . "<br />\n";
    }

    if(imap_setacl ($mbox, "user.$newname", "cyrus", "c")) {
        echo "ACL to mailbox changed<br/>\n";
    }else {
        echo "imap_setacl failed:" . implode("<br />\n", imap_errors()) . "<br />\n";
    }

    if (@imap_deletemailbox($mbox, "{localhost:143/notls}user.$newname")) {
        echo "new mailbox removed to restore initial state<br />\n";
    } else {
        echo "imap_deletemailbox on new mailbox failed: " . implode("<br />\n", imap_errors()) . "<br />\n";
    }

} else {
    echo "could not create new mailbox: " . implode("<br />\n", imap_errors()) . "<br />\n";
}

imap_close($mbox);
?>
