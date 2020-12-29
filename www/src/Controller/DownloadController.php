<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use ZipArchive;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Controller\Component\UploadComponent as ComUpload;
use App\Controller\Component\SystemComponent as ComSystem;
/**
 * 
 * Controller for downloader views
 * 
 */
class DownloadController extends AppController
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
        //$this->Auth->allow('index');
        // Public index only by default
    }
    public $users;
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Upload');
        $this->connection = ConnectionManager::get('default');
        $this->tmpPath = Configure::read('TempFilePath');
        $this->rScriptPath = Configure::read('RScriptPath');
    }
    public function index()
    {
        // Get the course catalog skeleton.
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        $this->set(compact('isAdmin'));
    }
    public function exportAllSQL()
    {
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        if (!$isAdmin) {
            $this->RequestHandler->renderAs($this, 'json');
            $this->set('error', "You don't have permission to do this.");
            return $this->set('_serialize', ['results']);
        }
        // Export entire database as SQLite DB
        $user = $this->connection->config()['username'];
        $pass = $this->connection->config()['password'];
        $dbName = $this->connection->config()['database'];
        $outputPath = $this->tmpPath . 'feast/' . $this->Auth->user()['id'];
        $outputFile = "{$outputPath}-public-feast.db";
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }
        foreach (ComUpload::$columnHeaders as $thisMetaTable) {
            $table = $thisMetaTable[0];
            $exportFile = $outputPath . "-{$table}.sql";
            if (file_exists($exportFile)) {
                unlink($exportFile);
            }
            $exportQuery = '';
            $conditions = [];
            if (in_array($table, ComUpload::$canExclude)) {
                $conditions[] = "(exclude != 1 OR exclude IS NULL)";
            }
            if (in_array($table, ComUpload::$canKeepPrivate)) {
                $conditions[] = "(keep_private != 1 OR keep_private IS NULL OR uploaded_at < DATE_SUB(CURDATE(), INTERVAL 1 YEAR))";
            }
            if (count($conditions) > 0) {
                $exportQuery .= ' --where="' . implode(" AND ", $conditions) . '" ';
            }
            /**
             * Respondent table needs the name replaced with the unique identifier.
             * To make this happen, we're abusing the where condition per 
             * bartavelle's answer here: http://stackoverflow.com/questions/4495912/what-some-ways-to-mask-a-mysqldump
             * Note that this requires "--single-transaction" to prevent issues 
             * with mysqldump complaining that tables are locked.
             */
            if ($table == "respondent") {
                $headers = 'id,id_focus_group,interview_date,unique_identifier as name,age,id_gender,head_of_household_is_respondent,head_of_household_name,head_of_household_age,id_gender_head_of_household,community,id_community_type,sub_region,minor_region,major_region,id_country,gps_latitude,gps_longitude,id_landholding_category,land_under_cultivation,head_of_household_occupation,diet_percent_collected_fodder,diet_percent_grazing,organization_affiliation,id_unit_area,diet_percent_cultivated_fodder,diet_percent_purchased_feed,diet_percent_crop_residue,created_at,updated_at,created_by,updated_by,id_user,replaced_by_id,keep_private,uploaded_at,exclude,unique_identifier';
                $exportQuery .= ' --where="0=1 UNION SELECT ' . $headers . ' FROM respondent WHERE ' . implode(" AND ", $conditions) . '" ';
                $mysqlCmd = "mysqldump --single-transaction=true -u{$user} -p'{$pass}' {$exportQuery} {$dbName} respondent > {$exportFile}";
                exec($mysqlCmd);
            } else {
                $mysqlCmd = "mysqldump -u{$user} -p'{$pass}' {$exportQuery} {$dbName} {$table} > {$exportFile}";
                exec($mysqlCmd);
            }
            $sqlCmd = "/var/www/feastrepo/mysql2sqlite.sh {$exportFile} | sqlite3 {$outputFile}";
            exec($sqlCmd);
            unlink($exportFile);
        }

        $response = $this->response;
        $response->setTypeMap('db', ['application/x-sqlite3', 'application/octet-stream']);
        $response = $response->withType('db');
        $response = $response->withFile($outputFile, ['download' => true]);
        return $response;
    }
    public function exportAllCSV()
    {
        // Export entire database as zipped set of CSV files.
        foreach (ComUpload::$columnHeaders as $thisMetaTable) {
            $table = $thisMetaTable[0];
            $exportFile = $this->tmpPath . 'feast/' . $this->Auth->user()['id'] . "-{$table}.csv";
            if (file_exists($exportFile)) {
                unlink($exportFile);
            }
            $exportQuery = "SELECT * INTO OUTFILE '{$exportFile}'\n                FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'\n                LINES TERMINATED BY '\n'\n                FROM {$table}";
            $conditions = [];
            if (in_array($table, ComUpload::$canExclude)) {
                $conditions[] = "(exclude != 1 OR exclude IS NULL)";
            }
            if (in_array($table, ComUpload::$canKeepPrivate)) {
                $conditions[] = "(keep_private != 1 OR keep_private IS NULL OR uploaded_at < DATE_SUB(CURDATE(), INTERVAL 1 YEAR))";
            }
            if (count($conditions) > 0) {
                $exportQuery .= " WHERE " . implode(" AND ", $conditions);
            }
            Log::debug($exportQuery);
            $this->connection->execute($exportQuery);
            $headerQuery = "SELECT GROUP_CONCAT(COLUMN_NAME) AS headers\n                            FROM INFORMATION_SCHEMA.COLUMNS\n                            WHERE TABLE_NAME = '{$table}'\n                            ORDER BY ORDINAL_POSITION";
            $result = $this->connection->execute($headerQuery)->fetchAll('assoc');
            $this->prepend($result[0]['headers'] . "\n", $exportFile);
	}

        $response = $this->response;
        $response->setTypeMap('vcf', ['application/zip', 'application/octet-stream']);
        $response = $response->withType('zip');
        $response = $response->withFile($this->zipExports(''), ['download' => true]);
        return $response;
    }
    public function exportAllKeyCSV()
    {
        // Export key tables as CSV reports
        $exportType = $this->request->getAttribute('params')['exportType'];
        $sizeQuery = "SET @@group_concat_max_len = 10000;";
        $this->connection->execute($sizeQuery);
        foreach (ComUpload::$exportViews as $table) {
            $exportFile = $this->tmpPath . 'feast/' . $this->Auth->user()['id'] . "-{$table}.csv";
            if (file_exists($exportFile)) {
                unlink($exportFile);
            }
            $headerQuery = "SELECT GROUP_CONCAT(COLUMN_NAME) AS headers\n                            FROM INFORMATION_SCHEMA.COLUMNS\n                            WHERE TABLE_NAME = '{$table}'\n                            ORDER BY ORDINAL_POSITION";
            $result = $this->connection->execute($headerQuery)->fetchAll('assoc');
            // If headers contain "private" or "excluded", remove them but set flags.
            $hasPrivate = 0;
            $hasExcluded = 0;
            // Remove headers only used for filtering
            $headers = str_replace('private,', '', $result[0]['headers'], $hasPrivate);
            $headers = str_replace('excluded,', '', $headers, $hasExcluded);
            $headers = str_replace('uploaded_at,', '', $headers);
            Log::debug("{$table} - {$headers} \n Private: {$hasPrivate} Excluded: {$hasExcluded} ExportType: {$exportType}");
            $whereQuery = '';
            if ($exportType != 'mine') {
                $whereQuery = " WHERE (excluded != 1 OR excluded IS NULL) AND (private != 1 OR private IS NULL OR uploaded_at < DATE_SUB(CURDATE(), INTERVAL 1 YEAR))";
            } else {
                $whereQuery = " WHERE user_id = " . $this->Auth->user('id');
                // Re-add filter headers since we're showing personal data.
                if ($hasPrivate > 0) {
                    $headers .= ',private';
                }
                if ($hasExcluded > 0) {
                    $headers .= ',excluded';
                }
            }
            $filterQuery = $this->getFilters();
            $exportQuery = "SELECT {$headers} INTO OUTFILE '{$exportFile}'\n                FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'\n                LINES TERMINATED BY '\n'\n                FROM {$table} {$whereQuery} {$filterQuery}";
            Log::debug($exportQuery);
            $this->connection->execute($exportQuery);
            $this->prepend($headers . "\n", $exportFile);
        }
        $this->getAttributions();

        $response = $this->response;
        $response->setTypeMap('vcf', ['application/zip', 'application/octet-stream']);
        $response = $response->withType('zip');
        $response = $response->withFile($this->zipExports('view'), ['download' => true]);
        return $response;
    }
    public function exportUserCSV()
    {
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        if (!$isAdmin) {
            $this->RequestHandler->renderAs($this, 'json');
            $this->set('error', "You don't have permission to do this.");
            return $this->set('_serialize', ['results']);
        }
        $exportFile = $this->tmpPath . 'feast/' . $this->Auth->user()['id'] . "-all-users.csv";
        $this->unlinkUserCSV();
        $table = 'user';
        $exportQuery = "SELECT id,contact_email, name_first, name_middle, name_last INTO OUTFILE '{$exportFile}'\n                FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'\n                LINES TERMINATED BY '\n'\n                FROM {$table}";
        $this->connection->execute($exportQuery);
        $this->prepend('id,contact_email,name_first,name_middle,name_last' . "\n", $exportFile);

        $response = $this->response;
        $response->setTypeMap('csv', ['text/csv']);
        $response = $response->withType('csv');
        $response = $response->withFile($exportFile, ['download' => true]);
        return $response;
    }
    public function exportData()
    {
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        if (!$isAdmin) {
            $this->RequestHandler->renderAs($this, 'json');
            $this->set('error', "You don't have permission to do this.");
            return $this->set('_serialize', ['results']);
        }

        $types = [
            'rdata' => ['rdata', ["application/x-rdata"], 'RDATA'],
            'csv' => ['vcf', ['application/zip', 'application/octet-stream'], 'zip'],
            'xlsx' => ['xlsx', ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"], 'xlsx']
        ];
        $type = $this->request->getQueryParams()['d'];
        $typeMap = $types[$type];
        $extension = $typeMap[2];
        $mineOnly = filter_var($this->request->getQueryParams()['m'], FILTER_VALIDATE_BOOLEAN);

        $exportFile = $this->tmpPath . '/feast/rdatadownload/FEAST' . ($mineOnly ? "sub_".$this->Auth->user()['id'] : "data") . "." . $extension;
        $u = $mineOnly ? $this->Auth->user()['id'] : "100000";
        $w = isset($this->request->getQueryParams()['w']) ? $this->sanitizeMultiFilter($this->request->getQueryParams()['w']) : "";
        $p = isset($this->request->getQueryParams()['p']) ? $this->sanitizeMultiFilter($this->request->getQueryParams()['p']) : "";
        $c = isset($this->request->getQueryParams()['c']) ? $this->sanitizeMultiFilter($this->request->getQueryParams()['c']) : "";
        $s = isset($this->request->getQueryParams()['s']) ? $this->sanitizeMultiFilter($this->request->getQueryParams()['s']) : "";

        shell_exec("Rscript " . $this->rScriptPath . "downloadFiltered.R " . $type . " '[$u]' '[$w]' '[$p]'  '[$c]' '[$s]'");

        $response = $this->response;
        $response->setTypeMap($typeMap[0], $typeMap[1]);
        $response = $response->withType($type);
        $response = $response->withFile($exportFile, ['download' => true]);
        $this->unlinkData($exportFile);
        return $response;
    }
    private function zipExports($prefix)
    {
        $this->unlinkUserCSV();
        // Make sure if there IS a user CSV, it doesn't end up in the export.
        $zip = new \ZipArchive();
        $origFiles = [];
        $dateString = date('Ymd');
        $filename = 'feast-' . ($prefix == '' ? '' : 'key-tables-') . $this->Auth->user()['id'] . "-{$dateString}.zip";
        $filePath = $this->tmpPath . 'feast/' . $filename;
        $zip->open($filePath, \ZipArchive::CREATE);
        foreach (glob($this->tmpPath . "feast/" . $this->Auth->user()['id'] . "-*.csv") as $file) {
            $origFiles[] = $file;
            $zip->addFile($file, basename($file));
        }
        $attrFilename = 'feast-attribution-' . $this->Auth->user()['id'] . ".txt";
        $attrFilePath = $this->tmpPath . 'feast/' . $attrFilename;
        if (file_exists($attrFilePath)) {
            $zip->addFile($attrFilePath, basename($attrFilePath));
        }
        $zip->close();
        // Remove the raw .csv files from the temp directory.
        foreach ($origFiles as $file) {
            unlink($file);
        }
        if (file_exists($attrFilePath)) {
            unlink($attrFilePath);
        }
        return $filePath;
    }
    private function unlinkData($exportFile)
    {
        if (file_exists($exportFile)) {
            unlink($exportFile);
        }
    }
    private function unlinkUserCSV()
    {
        $exportFile = $this->tmpPath . 'feast/' . $this->Auth->user()['id'] . "-all-users.csv";
        if (file_exists($exportFile)) {
            unlink($exportFile);
        }
    }
    private function prepend($string, $filename)
    {
        $context = stream_context_create();
        $fp = fopen($filename, 'r', 1, $context);
        $tmpname = tempnam(sys_get_temp_dir(), "feast-key-export-");
        file_put_contents($tmpname, $string);
        file_put_contents($tmpname, $fp, FILE_APPEND);
        fclose($fp);
        unlink($filename);
        rename($tmpname, $filename);
    }
    private function getAttributions()
    {
        Log::debug("Trying to fetch attributions");
        $tableAlias = 'FocusGroup';
        $table = TableRegistry::get($tableAlias);
        $results = [];
        if (isset($this->request->getQueryParams()['w']) && !isset($this->request->getQueryParams()['c'])) {
            $list = $this->sanitizeMultiFilterToArray($this->request->getQueryParams()['w']);
            $query = $table->find('all')->matching('Site.SystemCountry.SystemWorldRegion', function ($q) use ($list) {
                return $q->where(['SystemWorldRegion.id IN' => $list]);
            })->contain(['User', 'Site.Project', 'Site.SystemCountry.SystemWorldRegion']);
            $groups = $query->toArray();
            foreach ($groups as $thisGroup) {
                $results[$thisGroup->id] = $thisGroup;
            }
        }
        if (isset($this->request->getQueryParams()['c'])) {
            $list = $this->sanitizeMultiFilterToArray($this->request->getQueryParams()['c']);
            $query = $table->find('all')->matching('Site.SystemCountry', function ($q) use ($list) {
                return $q->where(['SystemCountry.id IN' => $list]);
            })->contain(['User', 'Site.Project', 'Site.SystemCountry.SystemWorldRegion']);
            $groups = $query->toArray();
            foreach ($groups as $thisGroup) {
                $results[$thisGroup->id] = $thisGroup;
            }
        }
        if (isset($this->request->getQueryParams()['p']) && !isset($this->request->getQueryParams()['s'])) {
            $list = $this->sanitizeMultiFilterToArray($this->request->getQueryParams()['p']);
            $query = $table->find('all')->matching('Site.Project', function ($q) use ($list) {
                return $q->where(['Project.id IN' => $list]);
            })->contain(['User', 'Site.Project', 'Site.SystemCountry.SystemWorldRegion']);
            $groups = $query->toArray();
            foreach ($groups as $thisGroup) {
                $results[$thisGroup->id] = $thisGroup;
            }
        }
        if (isset($this->request->getQueryParams()['s'])) {
            $list = $this->sanitizeMultiFilterToArray($this->request->getQueryParams()['s']);
            $query = $table->find('all')->matching('Site', function ($q) use ($list) {
                return $q->where(['Site.id IN' => $list]);
            })->contain(['User', 'Site.Project', 'Site.SystemCountry.SystemWorldRegion']);
            $groups = $query->toArray();
            foreach ($groups as $thisGroup) {
                $results[$thisGroup->id] = $thisGroup;
            }
        }
        if (!isset($this->request->getQueryParams()['s']) && !isset($this->request->getQueryParams()['c']) && !isset($this->request->getQueryParams()['w']) && !isset($this->request->getQueryParams()['p'])) {
            $query = $table->find('all')->contain(['User', 'Site.Project', 'Site.SystemCountry.SystemWorldRegion']);
            $groups = $query->toArray();
            foreach ($groups as $thisGroup) {
                $results[$thisGroup->id] = $thisGroup;
            }
        }
        // Figure out earliest focus group date for each site
        $siteDates = [];
        foreach ($results as $thisResult) {
            if (!isset($thisResult->site)) { continue; }
            $lastCreateDate = array_key_exists($thisResult->site->id, $siteDates) ? $siteDates[$thisResult->site->id] : new \DateTime($thisResult->created_at);
            $thisCreateDate = new \DateTime($thisResult->created_at);
            if ($lastCreateDate >= $thisCreateDate) {
                $siteDates[$thisResult->site->id] = $thisCreateDate;
            }
        }
        $attributions = [];
        foreach ($results as $thisResult) {
            if (!isset($thisResult->site)) { continue; }
            $creatorName = $thisResult->user->name_first . ' ' . ($thisResult->user->name_middle != null && $thisResult->user->name_middle != '' ? $thisResult->user->name_middle . ' ' : '') . $thisResult->user->name_last;
	    $creatorName .= $thisResult->user->affiliation != null && $thisResult->user->affiliation != '' ? ' of ' . $thisResult->user->affiliation : '';
            $formattedDate = $siteDates[$thisResult->site->id] != null ? " in " . $siteDates[$thisResult->site->id]->format('Y') : "";
            $attributions[] = 'FEAST feed assessment data from ' . $thisResult->site->name . ', ' . $thisResult->site->project->title . ' was created by ' . $creatorName . $formattedDate . '.';
        }
        $attribution = "This data has been downloaded from http://feastdata.ilri.org. You are free to use and adapt it with attribution under a creative commons license.\r\n";
        foreach ($attributions as $thisAttr) {
            $attribution .= "\r\n{$thisAttr}";
        }
        $filename = 'feast-attribution-' . $this->Auth->user()['id'] . ".txt";
        $filePath = $this->tmpPath . 'feast/' . $filename;
        Log::debug($filePath);
        Log::debug($attribution);
        file_put_contents($filePath, $attribution);
    }
    private function getFilters()
    {
        $filters = [];
        if (isset($this->request->getQueryParams()['w'])) {
            $filters[] = 'site_world_region_id IN (' . $this->sanitizeMultiFilter($this->request->getQueryParams()['w']) . ')';
        }
        if (isset($this->request->getQueryParams()['c'])) {
            $filters[] = 'site_country_id IN (' . $this->sanitizeMultiFilter($this->request->getQueryParams()['c']) . ')';
        }
        if (isset($this->request->getQueryParams()['p'])) {
            $filters[] = 'project_id IN (' . $this->sanitizeMultiFilter($this->request->getQueryParams()['p']) . ')';
        }
        if (isset($this->request->getQueryParams()['s'])) {
            $filters[] = 'site_id IN (' . $this->sanitizeMultiFilter($this->request->getQueryParams()['s']) . ')';
        }
        $query = implode($filters, ' AND ');
        if (count($filters) > 0) {
            return " AND {$query}";
        } else {
            return '';
        }
    }
    private function sanitizeMultiFilter($filter)
    {
        $filterArray = explode(",", $filter);
        foreach ($filterArray as &$filter) {
            $filter = intval($filter);
        }
        return join(",", $filterArray);
    }
    private function sanitizeMultiFilterToArray($filter)
    {
        $filterArray = explode(",", $filter);
        $result = [];
        foreach ($filterArray as $filter) {
            $result[] = intval($filter);
        }
        return $result;
    }
}
