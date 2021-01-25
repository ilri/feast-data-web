<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Aws\S3\S3Client;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_ModifyMessageRequest;
use Cake\Mailer\Mailer;
use App\Mailer\Transport\AmazonTransport;
use App\Controller\Component\SystemComponent as ComSystem;
use App\Controller\Component\UploadComponent as ComUpload;
/**
 * 
 * Controller for course views
 * 
 */
class UploadController extends AppController
{
    /**
     * Allow everything for a logged-in user right now.
     * @param Auth user $user
     * @return boolean
     */
    public function isAuthorized($user = null)
    {
        return true;
    }
    /**
     * Set Auth parameters (allow/deny)
     * @param EventInterface $event
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->Auth->allow('fetchMail');
    }
    public $users;
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Upload');
        ConnectionManager::setConfig('default', [
            'encoding' => 'utf8mb4',
        ]);
        $this->connection = ConnectionManager::get('default');
        $this->tmpPath = Configure::read('TempFilePath');
    }
    public function index()
    {
        // Get the course catalog skeleton.
    }
    public function fetchMail()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $client = $this->getClient();
        $service = new \Google_Service_Gmail($client);
        $messages = [];
        $options = [];
        $options['q'] = "is:unread has:attachment";
        $results = $service->users_messages->listUsersMessages('me', $options);
        if ($results->getMessages()) {
            $messages = array_merge($messages, $results->getMessages());
        }
        foreach ($messages as $thisMessage) {
            $message = $service->users_messages->get('me', $thisMessage->getId());
            $headers = $message['payload']['headers'];
            $fromEmail = null;
            $private = false;
            foreach ($headers as $thisHeader) {
                if ($thisHeader['name'] == 'From') {
                    $fromEmail = $thisHeader['value'];
                    if (strpos($fromEmail, '>') !== false) {
                        $fromEmail = $this->get_string_between($fromEmail, '<', '>');
                    }
                    Log::debug("From Email: {$fromEmail}");
                }
                if ($thisHeader['name'] == 'Subject') {
                    $private = strpos(strtolower($thisHeader['value']), 'private') !== false;
                    $this->request->getData()['keep_private'] = $private !== false;
                }
            }
            $parts = $message['payload']['parts'];
            foreach ($parts as $thisPart) {
                if (!empty($thisPart['filename'])) {
                    $filename = $thisPart['filename'];
                    if (pathinfo($filename, PATHINFO_EXTENSION) != "zlib") {
                        Log::debug('Skipping non-ZLIB file attachment -- not importing');
                        continue;
                        // Basic validation.
                    }
                    $attachmentId = $thisPart['body']['attachmentId'];
                    $attachmentResult = $service->users_messages_attachments->get('me', $thisMessage->getId(), $attachmentId);
                    $uploadFile = $this->tmpPath . 'email-import.zlib';
                    $data = strtr($attachmentResult->data, array('-' => '+', '_' => '/'));
                    $fh = fopen($uploadFile, "w+");
                    fwrite($fh, base64_decode($data));
                    fclose($fh);
                    $userID = $this->getUserID($fromEmail);
                    if (!empty($userID)) {
                        $this->importFile($uploadFile, $userID);
                        // Mark email read
                        $mods = new \Google_Service_Gmail_ModifyMessageRequest();
                        $mods->setRemoveLabelIds(["UNREAD"]);
                        $message = $service->users_messages->modify('me', $thisMessage->getId(), $mods);
                    } else {
                        Log::warning("Unable to find user - not importing data.");
                    }
                }
            }
        }
        // Set the view vars that have to be serialized.
        $this->set('result', 'complete');
        // Specify which view vars JsonView should serialize.
        $this->set('_serialize', ['result']);
    }
    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    private function getClient()
    {
        $scopes = implode(' ', array('https://mail.google.com/'));
        $client = new \Google_Client();
        $client->setApplicationName('FEAST Data Repository');
        $client->setScopes($scopes);
        $client->setAuthConfig($this->currentEntity['gmail_client_secret']);
        $client->setAccessType('offline');
        $accessToken = $this->currentEntity['gmail_access_token'];
        $client->setAccessToken($accessToken);
        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            Log::debug("Attempting to refresh access token");
            $client->refreshToken($client->getRefreshToken());
            $this->currentEntity['gmail_access_token'] = $client->getAccessToken();
            $this->request->getSession()->write('currentEntity', $this->currentEntity);
        }
        return $client;
    }
    /**
     * Handle a user-submitted import:
     * 1) Upload file to S3 for storage
     * 2) Parse .zlib for CSV records
     * 3) Import records to database
     */
    public function importUploadData()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $userResourceTable = TableRegistry::get('UserResource');
        $awsConfig = [
            'key' => Configure::read('AmazonS3.aws_access_key_id'),
            'secret' => Configure::read('AmazonS3.aws_secret_access_key'),
            'profile' => 'default',
            'region' => Configure::read('AmazonS3.aws_region')
        ];

        if (Configure::read('AmazonS3.signature')) {
            $awsConfig['signature'] = Configure::read('AmazonS3.signature');
        }

        $client = S3Client::factory($awsConfig);
        $errors = null;
        $filename = explode(".", $this->request->getData('file')->getClientFilename());
        $filename = Text::uuid() . '.' . end($filename);
        $S3Key = md5($this->Auth->user()['contact_email']) . '/' . $filename;
        try {
            $result = $client->putObject(array('Bucket' => Configure::read('AmazonS3.upload_bucket'), 'Key' => $S3Key, 'SourceFile' => $this->request->getData('file')->getStream()->getMetadata('uri')));
        } catch (S3Exception $e) {
            $this->set('error', __("Unable to store file on S3"));
            $this->set('_serialize', ['error']);
            return;
        }

        $tableAlias = 'FocusGroup';
        $table = TableRegistry::get('FocusGroup');
        $countBefore = $table->find('all')->count();

        $newFile = $this->importFile($this->request->getData('file')->getStream()->getMetadata('uri'), $this->Auth->user('id'));
        if (!$newFile) {
            $error = __("Can't import file");
            $this->set('error', $error);
            $this->set('_serialize', ['error']);
            return;
        }

        //Check if it is imported by focus_group
        $countAfter = $table->find('all')->count();
        if ($countBefore == $countAfter) {
            $error = __("No new data has been added to the database");
            $this->set('error', $error);
            $this->set('_serialize', ['error']);
            return;
        }
        $this->set('newFile', $newFile);
        $this->set('_serialize', ['newFile']);
    }
    /**
     * Process a user-uploaded .zlib file and import to MariaDB
     * 
     * 
     * FORMAT: 
     * [FILE COUNT = 4 bytes]
     * ...then repeating:
     * (File Name Size = 4 bytes)
     * (File Name = N bytes)
     * (UNCOMPRESSED data size = 4 bytes)
     * (Compressed Data = N bytes)
     * 
     * NOTE: This is hacky right now because we don't know the compressed data
     * size for each file in the archive. So we are using a simple string search
     * to jump past the compressed data to the beginning of the next compression
     * block.
     * 
     * @param string $fileName
     */
    private function importFile($filename, $userID) {
        ini_set('memory_limit', '-1');
        $data = [];

        $fsize = filesize($filename);
        $handle = fopen($filename, "rb");
        $fileCount = unpack('i', fread($handle, 4)); // Delphi int = 4 bytes

        $rawData = fread($handle, $fsize);

        Log::debug("File count: {$fileCount[1]}\n");
        $version = 0;
        if ($fileCount[1] == 48) {
            $version = 1;
        } else if ($fileCount[1] == 57) {
            $version = 2;
        }
        
        if ($version == 0) {
            return;
        }

        /**
         * NOTE: This has been refactored to do a simple text search for the filename, then skip bytes for extension and compressed data size.
         *
         * Compressed data is followed by another four bytes of filename size, but zlib_decode stops decoding after the end of each valid block of 
         * data so no need to worry about it - the text search skips these bytes automatically.
         */
        $processCount = 0;
        foreach (ComUpload::$columnHeaders as $thisMetaTable) {
            $processCount++;
            if ($processCount > $fileCount[1]){
                // Don't try to process tables that we already know don't exist. 
                // TODO: Use version + tablename instead of this check. As long as new tables are added at the end, it will work, but it's messy.
                break; 
            }
            $table = $thisMetaTable[0];
            $thisPos = strpos($rawData, $table);

            Log::debug("Found Table: {$table} at {$thisPos}");

            $gzresult = zlib_decode(substr($rawData, $thisPos + strlen($table) + 8)); // 8 bytes for filename size and compressed data size, which are useless info.
            $fileData = preg_split("/\r\n|\n|\r/", $gzresult);
            foreach ($fileData as $thisRow) {
                if ($thisRow != "") {
                    $thisRow = $this->remove_utf8_bom($thisRow);
                    $data["{$table}.csv"][] = str_getcsv($thisRow, '|');
                }
            }
        }
        fclose($handle);

        return $this->loadDatabase($data, $userID);
    }
    /**
     * Using our column headers, match up data to table fields and populate 
     * in the correct order to ensure FK-referenced data exists first.
     * @param array of CSV $data
     */
    private function loadDatabase($data, $userID)
    {
        Log::debug("Keep private: " . $this->request->getData()['keep_private']);
        foreach (ComUpload::$columnHeaders as $thisMetaTable) {
            $table = $thisMetaTable[0];
            if (in_array($table, ComUpload::$excludeTables)) {
                Log::debug("Excluding {$table}");
                continue;
            }
            // Don't bother with excluded tables.
            foreach ($data as $thisTablename => $thisTable) {
                //Log::debug("Checking $thisTablename");
                if ($thisTablename == $table . '.csv') {
                    // Make sure we do this in metadata order to ensure data exists for FK lookups by later tables.
                    Log::debug("Importing {$thisTablename}");
                    // Step 1: build list of unique IDs and get rows matching
                    $uniqueIDList = [];
                    foreach ($thisTable as $thisUIDRow) {    
                        if ($thisUIDRow[0] == "") { continue; }
                        $uniqueIDList[] = '"' . $thisUIDRow[0] . '"';
                    }
                    
                    if (count($uniqueIDList) < 1) {
                        Log::debug("No records to import.");
                        continue;
                    }
                    
                    Log::debug("Importing " . implode(',', $uniqueIDList)); //Miheretab check
                    // Only fetch canonical data column if it exists.
                    if (!in_array($table, ComUpload::$notCanonical)) {
                        $results = $this->connection->execute("SELECT id, unique_identifier, updated_at, canonical_data FROM {$table} WHERE unique_identifier IN (" . implode(',', $uniqueIDList) . ")")->fetchAll('assoc');
                    } else {
                        $results = $this->connection->execute("SELECT id, unique_identifier, updated_at FROM {$table} WHERE unique_identifier IN (" . implode(',', $uniqueIDList) . ")")->fetchAll('assoc');
                    }
                    $uniqueIDMap = [];
		    foreach ($thisTable as $thisTableKey => $thisExistRow) {
			if ($thisExistRow[0] == "") { continue; }
                        $uniqueIDMap[$thisExistRow[0]] = $thisTableKey;
                    }
                    // Do data checks for uniqueness and handle dupes / missing canonical rows
                    $replaceRows = [];
                    $skipRows = [""];
                    foreach ($results as $thisResKey => $thisResValue) {
                        $foundRow = false;
                        $isCanonical = false;
                        $rowKey = 0;
                        if (array_key_exists($thisResValue['unique_identifier'], $uniqueIDMap)) {
                            $foundRow = true;
                            $rowKey = $uniqueIDMap[$thisResValue['unique_identifier']];
                        }
                        $newTime = 0;
                        $oldTime = 0;
                        if ($foundRow) {
                            Log::debug($thisResValue['unique_identifier'] . " exists");
                            if (array_key_exists('canonical_data', $thisResValue)) {
                                $isCanonical = $thisResValue['canonical_data'] == 1;
                            }
                            $oldTime = strtotime($thisResValue['updated_at']);
                            // Row exists - do we need to replace?
                            for ($i = 1; $i < count($thisTable[$rowKey]); $i++) {
                                $field = $thisMetaTable[$i + 1];
                                $value = $thisTable[$rowKey][$i];
                                if ($field == 'updated_at') {
                                    // Only update if the new record is more recent and the existing record isn't canonical
                                    $newTime = strtotime($value);
                                    if ($newTime > $oldTime && !$isCanonical) {
                                        $replaceRows[] = [$thisResValue['id'], $thisResValue['unique_identifier']];
                                        // [id, unique_identifier] so we can update replaced_by
                                    } else {
                                        $skipRows[] = $thisResValue['unique_identifier'];
                                        // unique_identifier
                                        break;
                                    }
                                }
                            }
                        } else {
                            // Row doesn't exist, so if canon, set canon = false;
                            for ($i = 1; $i < count($thisTable[$rowKey]); $i++) {
                                $field = $thisMetaTable[$i + 1];
                                $value = $thisTable[$rowKey][$i];
                                Log::debug("Updating row?");
                                Log::debug(serialize($thisTable[$rowKey]));
                                if ($field == 'canonical_data' && $value == 1) {
                                    Log::debug("Downgrading canonical data...");
                                    $thisTable[$rowKey][$i] = "0";
                                    break;
                                }
                            }
                        }
                    }
                    // Step 2: insert data
                    foreach ($thisTable as $thisRow) {
                        $values = [];
                        $valueParams = null;
                        $fields = null;
                        if (in_array($thisRow[0], $skipRows)) {
                            Log::debug("Skipping row {$thisRow[0]}");
                            continue;
                        }
                        Log::debug(serialize($thisRow));
                        $values[] = $thisRow[0];
                        $valueParams = "'$thisRow[0]'";//"?";
                        $fields = $thisMetaTable[1];
                        for ($i = 1; $i < count($thisRow); $i++) {
                            if (!isset($thisMetaTable[$i + 1])) { continue; }
                            $field = $thisMetaTable[$i + 1];
                            $value = $thisRow[$i];
                            if ($field == "synchronized_at") {
                                continue;
                                // SKIP for now (see uploaded_at below)
                            }
                            if (is_null($value) || strlen($value) == 0 || explode("_", $field)[0] == "validate") {
                                // Don't add column if there's no value OR the column doesn't exist in MariaDB
                                continue;
                            }
                            // Insert foreign keys via subselect if possible.
                            $fkTable = null;
                            if (explode("_", $field)[0] == "id") {
                                $fkTable = substr($field, 3);
                                if ($fkTable == 'gender_head_of_household') {
                                    $fkTable = 'gender';
                                }
                                $valueParams .= ",(SELECT id FROM {$fkTable} WHERE unique_identifier = '{$value}' ORDER BY id DESC LIMIT 1)";
                            } else {
                                $valueParams .= utf8_encode(",'$value'");//",?";
                                $values[] = utf8_encode("$value");
                            }
                            $fields .= ',' . $field;
                        }
                        // Add user ID for record
                        $fields .= ',id_user';
                        $valueParams .= ",'$userID'";//",?";
                        $values[] = $userID;
                        // Keep private if necessary
                        if (in_array($table, ComUpload::$canKeepPrivate) && $this->request->getData()['keep_private'] == "true") {
                            Log::debug("KP: " . $this->request->getData()['keep_private']);
                            $fields .= ',keep_private';
                            $valueParams .= ',1';
                        }
                        // Track uploaded date if necessary
                        if (in_array($table, ComUpload::$trackUploadTime)) {
                            $fields .= ',uploaded_at';
                            $valueParams .= ',NOW()';
                        }
                        $insertQuery = "INSERT INTO {$table} ({$fields}) VALUES ({$valueParams})";
                        Log::debug($insertQuery); // Miheretab check
                        Log::debug(serialize($values)); // Miheretab check
                        $stmt = $this->connection->execute($insertQuery);//, $values);
                        $newRowID = $stmt->lastInsertId($table, 'id');
                        foreach ($replaceRows as $thisReplaceRow) {
                            if ($thisReplaceRow[1] == $thisRow[0]) {
                                $updateQuery = "UPDATE {$table} SET replaced_by_id = {$newRowID} WHERE id = " . $thisReplaceRow[0];
                                Log::debug($updateQuery);
                                $this->connection->execute($updateQuery);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    /**
     * Given an email address, return the ID of the user (registering a new user if necessary).
     */
    private function getUserID($fromEmail)
    {
        $users = TableRegistry::get('User');
        $query = $users->find('all')->where(['contact_email LIKE' => strtolower($fromEmail)]);
        $user = $query->first();
        if (empty($user)) {
            // Add the user
            $password = $this->generatePassword(16);
            $user = $users->newEmptyEntity();
            $user->contact_email = $fromEmail;
            $user->name_first = $fromEmail;
            $user->password = (new DefaultPasswordHasher())->hash($password);
            $user->contact_email_confirmed = 1;
            $user->created_by = "SYSTEM";
            $user->user_approval_status_id = ComSystem::APPROVAL_STATUS_ACTIVE;
            if ($users->save($user)) {
                // Send new registration message
                $email = new Mailer();
                $transport = new AmazonTransport();
                $email->setTransport($transport);
                $from = $this->currentEntity['no_reply_address'];
                $portalName = $this->currentEntity['name'];
                $entityDomain = $this->currentEntity['primary_domain'];
                $email->setViewVars(['contactName' => $this->currentEntity['contact_name'], 'contactEmail' => $this->currentEntity['contact_email'], 'entityDomain' => $entityDomain, 'isAdmin' => true, 'userEmail' => $user->contact_email, 'password' => $password, 'portalName' => $portalName]);
                $email->setEmailFormat('text')->setSubject($portalName . ': ' . __('Registration Approved'))->setTo($user->contact_email)->setFrom($from)->viewBuilder()->setTemplate('registration_welcome')->setLayout('sonata');
				$email->deliver();
                return $user->id;
            } else {
                Log::warning("Unable to create new user on email import.");
                return null;
            }
        } else {
            return $user->id;
        }
    }
    private function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
    function generatePassword($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);
        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }
        return $result;
    }

    function remove_utf8_bom($text) { //Miheretab to check this function
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }
}
