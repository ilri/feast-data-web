<?php

namespace App\Mailer\Transport;

use Aws\Ses\SesClient;
use Cake\Core\Configure;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Mailer;
use Cake\Mailer\Message;
use Cake\Log\Log;
/**
 * Currently this relies on a ~/.aws/credentials file containing
 * an access key and secret generated on the AWS Console IAM tool
 * https://console.aws.amazon.com/iam/home?region=us-west-2#users
 * 
 * TODO: Decide if it makes sense to try to centralize that config into the 
 * general CakePHP config file for this project.
 */
class AmazonTransport extends AbstractTransport
{
    public function send(Message $email): array
    {
		try {
			$client = SesClient::factory(array('key' => Configure::read('AmazonSES.aws_access_key_id'), 'secret' => Configure::read('AmazonSES.aws_secret_access_key'), 'profile' => 'default', 'region' => Configure::read('AmazonSES.aws_region')));
			Log::debug(print_r($email, true));
			Log::debug(print_r($email->getFrom(), true));
			$result = $client->sendEmail(array(
				// Source is required
				'Source' => key($email->getFrom()),
				// Destination is required
				'Destination' => array('ToAddresses' => array(key($email->getTo()))),
				// Message is required
				'Message' => array(
					// Subject is required
					'Subject' => array(
						// Data is required
						'Data' => $email->getSubject(),
						'Charset' => 'UTF-8',
					),
					// Body is required
					'Body' => array('Text' => array(
						// Data is required
						'Data' => $email->getBodyText(),
						'Charset' => 'UTF-8',
					)),
				),
			));
			Log::debug(print_r($result, true));

			return ['success' => $result];
		} catch (Exception $e) {
			 //An error happened and the email did not get sent
			 Log::debug(print_r($e->getMessage(), true));
			 return ['error' => $e->getMessage()];
		}
    }
}

