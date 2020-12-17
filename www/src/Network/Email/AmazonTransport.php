<?php

namespace App\Network\Email;

use Aws\Ses\SesClient;
use Cake\Core\Configure;
use Cake\Network\Email\AbstractTransport;
use Cake\Network\Email\Email;
use Cake\Log\Log;
/**
 * Currently this relies on a ~/.aws/credentials file containing
 * an access key and secret generated on the AWS Console IAM tool
 * https://console.aws.amazon.com/iam/home?region=us-west-2#users
 * 
 * TODO: Decide if it makes sense to try to centralize that config into the 
 * general CakePHP config file for this project.
 */
class AmazonTransport extends \Cake\Network\Email\AbstractTransport
{
    public function send(\Cake\Network\Email\Email $email)
    {
        $client = \Aws\Ses\SesClient::factory(array('key' => \Cake\Core\Configure::read('AmazonSES.aws_access_key_id'), 'secret' => \Cake\Core\Configure::read('AmazonSES.aws_secret_access_key'), 'profile' => 'default', 'region' => \Cake\Core\Configure::read('AmazonSES.aws_region')));
        \Cake\Log\Log::debug(print_r($email, true));
        \Cake\Log\Log::debug(print_r($email->from(), true));
        $result = $client->sendEmail(array(
            // Source is required
            'Source' => key($email->from()),
            // Destination is required
            'Destination' => array('ToAddresses' => array(key($email->to()))),
            // Message is required
            'Message' => array(
                // Subject is required
                'Subject' => array(
                    // Data is required
                    'Data' => $email->subject(),
                    'Charset' => 'UTF-8',
                ),
                // Body is required
                'Body' => array('Text' => array(
                    // Data is required
                    'Data' => $email->message(\Cake\Mailer\Message::MESSAGE_TEXT),
                    'Charset' => 'UTF-8',
                )),
            ),
        ));
        \Cake\Log\Log::debug(print_r($result, true));
    }
}

