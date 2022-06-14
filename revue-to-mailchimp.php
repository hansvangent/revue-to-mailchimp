<?php
/**
 * This file will allow the syncing of new subscribers from Revue (https://www.getrevue.co/) to Mailchimp (https://mailchimp.com/)
 *
 * Upon running it should check the subscribers in Revue and synchronize (add or update them) into Mailchimp.
 *
 * If a user is already in Mailchimp it should update the user unless the user has unsubscribed in Mailchimp,
 * then the user should be unsubscribed in Revue as well.
 */


/* ---------------------------------------------------------------------------
 * Setup API keys and List ID + server prefix (Mailchimp) needed for syncing
 *
 * Revue API key can be found here: https://www.getrevue.co/app/integrations
 * Mailchimp API key can be found here: https://us1.admin.mailchimp.com/account/api/
 * Mailchimp List ID can be found by clicking on Audience > All contacts > Settings > Audience name and defaults > In the Audience ID section, you’ll see a string of letters and numbers. This is your audience List ID. (see also: https://mailchimp.com/en-gb/help/find-audience-id/)
 * Mailchimp server prefix  (You can see this in your URL bar after loggin in, e.g. us1.)
 * --------------------------------------------------------------------------- */

$revue_api_key = 'replace_me_with_your_revue_api_key';
$mailchimp_api_key = 'replace_me_with_your_mailchimp_api_key';
$mailchimp_list_id = 'replace_me_with_your_mailchimp_list_id';
$mailchimp_server_prefix = 'replace_me_with_your_mailchimp_server_prefix';

/* ---------------------------------------------------------------------------
 * Main class
 * --------------------------------------------------------------------------- */
class RevueToMailChimp
{
    protected $revue_api_key;
    protected $mailchimp_api_key;
    protected $mailchimp_list_id;
    protected $mailchimp_server_prefix;

    public function __construct($revue_api_key, $mailchimp_api_key, $mailchimp_list_id, $mailchimp_server_prefix)
    {
        $this->revue_api_key = $revue_api_key;
        $this->mailchimp_api_key = $mailchimp_api_key;
        $this->mailchimp_list_id = $mailchimp_list_id;
        $this->mailchimp_server_prefix = $mailchimp_server_prefix;
    }

    public function getSubscriberRevue()
    {
        $curl = curl_init();
        $headers = array(
            "Authorization: Bearer {$this->revue_api_key}",// send token in Bearer header request
            "accept: application/json"
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL,"https://www.getrevue.co/api/v2/subscribers");

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_USERAGENT => 'Test',
            CURLOPT_SSL_VERIFYPEER => false
        ));

        $list_user_subscribers = curl_exec($curl);

        curl_close($curl);

        return $list_user_subscribers;
    }

    public function unsubscribesRevue($email)
    {
        $curl = curl_init();
        $fields = ([
            "email" => $email
        ]);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                "Authorization: Bearer {$this->revue_api_key}",// send token in Bearer header request
                "accept: application/json"
            )
        );

        curl_setopt($curl, CURLOPT_URL,"https://www.getrevue.co/api/v2/subscribers/unsubscribe");

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        $server_output = curl_exec($curl);

        curl_close($curl);

        return true;
    }

    public function checkHasUserMailChimp($email)
    {
        $userId = md5( strtolower($email));

        $fields = json_encode([
            'apikey'        => $this->mailchimp_api_key,
            'email_address' => $email
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://$this->mailchimp_server_prefix.api.mailchimp.com/3.0/lists/$this->mailchimp_list_id/members/$userId");
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json', // for define content type that is json
                "Authorization: Bearer {$this->mailchimp_api_key}"
            )
        );
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $server_output = curl_exec($ch);
        $server_output = json_decode($server_output);

        curl_close($ch);

        if (!isset($server_output->title)) {
            return $server_output; //exists user in mailchimp
        } else {
            return null; //don't exists user in mailchimp
        }
    }

    public function addToMailChimp($user)
    {
        $fields = json_encode([
            'apikey'        => $this->mailchimp_api_key,
            'email_address' => $user->email,
            'status'        => 'subscribed',
            'merge_fields'  => array(
                'FNAME' => $user->first_name,
                'LNAME' => $user->last_name
                )

        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://$this->mailchimp_server_prefix.api.mailchimp.com/3.0/lists/$this->mailchimp_list_id/members");
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json', // for define content type that is json
                "Authorization: Bearer {$this->mailchimp_api_key}"
            )
        );
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $server_output = curl_exec($ch);

        return true;
    }

    public function debug($message) {
        return file_put_contents('debug.log', "\n $message" , FILE_APPEND);
    }

    public function sync()
    {
        $countAddToMail = 0;
        $countUnsubscribes = 0;
        $list_sub = $this->getSubscriberRevue();
        $list_sub = json_decode($list_sub);

        if (count($list_sub) < 1) {
            $error = "Syncing error: you don't have any subscribers in Revue";
            $this->debug($error);
        }

        $date = date("Y/m/d h:i:sa");

        foreach ($list_sub as $sub) {
            $checkUser = $this->checkHasUserMailChimp($sub->email);
            if ($checkUser == null) {
                $countAddToMail++;
                $this->addToMailChimp($sub);
                $message = "$date Added $sub->email to MailChimp from Revue";
                $this->debug($message);
                continue;
            }


            if ($checkUser->status == "unsubscribed") {
                $countUnsubscribes++;
                $this->unsubscribesRevue($sub->email);
                $message = "$date Unsubscribed $sub->email from Revue";
                $this->debug($message);
                continue;
            }
        }

        if ($countAddToMail == 0 && $countUnsubscribes == 0) {
            $message = "$date Nothing to update";
            $this->debug($message);
        }

        echo "Sync successfully!!\n";
    }
}

/* ---------------------------------------------------------------------------
 * Run the script
 * --------------------------------------------------------------------------- */

$app = new RevueToMailChimp($revue_api_key, $mailchimp_api_key, $mailchimp_list_id, $mailchimp_server_prefix);
$app->sync();
?>
