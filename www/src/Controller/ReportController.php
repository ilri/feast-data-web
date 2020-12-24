<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use ZipArchive;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use App\Controller\Component\SystemComponent as ComSystem;
/**
 * 
 * Controller for report views
 * 
 */
class ReportController extends AppController
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
        if (isset($this->currentEntity['allow_report']) && $this->currentEntity['allow_report']) {
            $this->Auth->allow(['index', 'getReportResults']);
        }
        // Public everything by default.
    }
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Upload');
        $this->connection = ConnectionManager::get('default');
    }
    public function index()
    {
        // Get the course catalog skeleton.
    }
    public function getReportResults()
    {
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        $groupBy = $this->request->getAttribute('params')['groupBy'];
        switch ($groupBy) {
            case 'country':
                $groupQuery = " GROUP BY country.name";
                $groupAlias = "country.name AS description,";
                $groupJoin = "country.name";
                break;
            case 'world_region':
                $groupQuery = " GROUP BY world_region.name";
                $groupAlias = "world_region.name AS description,";
                $groupJoin = "world_region.name";
                break;
            case 'project':
                $groupQuery = " GROUP BY project.title";
                $groupAlias = "project.title AS description,";
                $groupJoin = "project.title";
                break;
            case 'site':
                $groupQuery = " GROUP BY site.name";
                $groupAlias = "site.name AS description,";
                $groupJoin = "site.name";
                break;
            default:
                $groupQuery = " GROUP BY gender.description";
                $groupAlias = "gender.description AS description,";
                $groupJoin = "gender.description";
        }
        $filters = $this->getFilters();
        $chartType = $this->request->getAttribute('params')['chartType'];
        switch ($chartType) {
            case 'drymatter':
                $query = "SELECT\n            interview_respondents,\n            description,\n            (dm_crop_residue_raw / interview_respondents) as dm_crop_residue,\n            (dm_cultivated_fodder_raw / interview_respondents) as dm_cultivated_fodder,\n            (dm_purchased_feed_raw / interview_respondents) as dm_purchased_feed,\n            (dm_collected_fodder_raw / interview_respondents) as dm_collected_fodder,\n            (dm_grazing_raw / interview_respondents) as dm_grazing\n            FROM\n            (SELECT \n            COUNT(DISTINCT(respondent.id)) AS interview_respondents,\n            {$groupAlias}\n            SUM(crop_residue_stats.crop_residue_dm) as dm_crop_residue_raw, \n            SUM(cultivated_fodder_stats.cultivated_fodder_dm) as dm_cultivated_fodder_raw, \n            SUM(purchased_feed_stats.purchased_feed_dm) as dm_purchased_feed_raw, \n            SUM(((IFNULL(respondent.diet_percent_grazing, 0) * 0.01) * ((IFNULL(purchased_feed_stats.purchased_feed_dm, 0)) + (IFNULL(crop_residue_stats.crop_residue_dm, 0)) + (IFNULL(cultivated_fodder_stats.cultivated_fodder_dm, 0))) /(1-((IFNULL(respondent.diet_percent_grazing, 0) + IFNULL(respondent.diet_percent_collected_fodder, 0))*0.01)))) as dm_grazing_raw, \n            SUM(((IFNULL(respondent.diet_percent_collected_fodder, 0) * 0.01) * ((IFNULL(purchased_feed_stats.purchased_feed_dm, 0)) + (IFNULL(crop_residue_stats.crop_residue_dm, 0)) + (IFNULL(cultivated_fodder_stats.cultivated_fodder_dm, 0))) /(1-((IFNULL(respondent.diet_percent_grazing, 0) + IFNULL(respondent.diet_percent_collected_fodder, 0))*0.01)))) as dm_collected_fodder_raw \n            FROM \n            respondent\n            LEFT JOIN crop_residue_stats ON crop_residue_stats.respondent_id = respondent.id\n            LEFT JOIN cultivated_fodder_stats ON cultivated_fodder_stats.respondent_id = respondent.id \n            LEFT JOIN purchased_feed_stats ON purchased_feed_stats.respondent_id = respondent.id \n            INNER JOIN gender ON respondent.id_gender = gender.id\n            INNER JOIN focus_group ON focus_group.id = respondent.id_focus_group AND (focus_group.exclude IS NULL OR focus_group.exclude = 0)\n            INNER JOIN site ON focus_group.id_site = site.id AND (site.exclude IS NULL OR site.exclude = 0)\n            INNER JOIN country ON country.id = site.id_country\n            INNER JOIN world_region ON world_region.id = country.id_world_region\n            INNER JOIN project ON site.id_project = project.id AND (project.exclude IS NULL OR project.exclude = 0)\n            WHERE (respondent.exclude IS NULL OR respondent.exclude = 0)\n            {$filters}\n            {$groupQuery}) as baseQuery";
                break;
            case 'cropareas':
                $query = "SELECT\n            secondaryQuery.interview_respondents,\n            secondaryQuery.description,\n            baseQuery.type_of_crop,\n            (overall_ha_per_hh/(interview_respondents)) as average_ha\n            FROM\n            (SELECT \n            {$groupAlias}\n            crop_type.name as type_of_crop,\n            SUM(crop_cultivation.cultivated_land * unit_area.conversion_ha) as overall_ha_per_hh\n            FROM \n            respondent\n            LEFT JOIN crop_cultivation ON crop_cultivation.id_respondent = respondent.id AND (crop_cultivation.exclude IS NULL OR crop_cultivation.exclude = 0) \n            LEFT JOIN crop_type ON crop_type.id = crop_cultivation.id_crop_type  \n            LEFT JOIN  unit_area on crop_cultivation.id_unit_area = unit_area.id \n            INNER JOIN gender ON respondent.id_gender = gender.id\n            INNER JOIN focus_group ON focus_group.id = respondent.id_focus_group AND (focus_group.exclude IS NULL OR focus_group.exclude = 0)\n            INNER JOIN site ON focus_group.id_site = site.id  AND (site.exclude IS NULL OR site.exclude = 0) \n            INNER JOIN country ON country.id = site.id_country\n            INNER JOIN world_region ON world_region.id = country.id_world_region\n            INNER JOIN project ON site.id_project = project.id AND (project.exclude IS NULL OR project.exclude = 0) \n            WHERE crop_cultivation.cultivated_land IS NOT NULL AND (respondent.exclude IS NULL OR respondent.exclude = 0) \n            GROUP BY {$groupJoin}, crop_type.name) as baseQuery\n            JOIN\n            (SELECT\n            {$groupAlias}\n            COUNT(DISTINCT(respondent.id)) AS interview_respondents\n            FROM \n            respondent\n            INNER JOIN gender ON respondent.id_gender = gender.id\n            INNER JOIN focus_group ON focus_group.id = respondent.id_focus_group AND (focus_group.exclude IS NULL OR focus_group.exclude = 0) \n            INNER JOIN site ON focus_group.id_site = site.id AND (site.exclude IS NULL OR site.exclude = 0) \n            INNER JOIN country ON country.id = site.id_country\n            INNER JOIN world_region ON world_region.id = country.id_world_region\n            INNER JOIN project ON site.id_project = project.id AND (project.exclude IS NULL OR project.exclude = 0) \n            WHERE (respondent.exclude IS NULL OR respondent.exclude = 0)\n            {$filters}\n            {$groupQuery}) as secondaryQuery\n            ON baseQuery.description = secondaryQuery.description";
                break;
            case 'livestockholdings':
                $query = "SELECT\n            interview_respondents,\n            secondaryQuery.description,\n            category_of_animal,\n            (average_weight/(250*interview_respondents)) as average_tlus\n            FROM\n            (SELECT \n            {$groupAlias}\n            animal_category.description as category_of_animal,\n            (SUM(livestock_holding.average_weight * livestock_holding.headcount)) AS average_weight\n            FROM \n            respondent\n            LEFT JOIN livestock_holding ON livestock_holding.id_respondent = respondent.id AND (livestock_holding.exclude IS NULL OR livestock_holding.exclude = 0) \n            LEFT JOIN animal_type ON animal_type.id = livestock_holding.id_animal_type\n            LEFT JOIN animal_category ON animal_category.id = animal_type.id_animal_category\n            LEFT JOIN animal_species ON animal_species.id = animal_category.id_animal_species\n            INNER JOIN gender ON respondent.id_gender = gender.id\n            INNER JOIN focus_group ON focus_group.id = respondent.id_focus_group AND (focus_group.exclude IS NULL OR focus_group.exclude = 0)\n            INNER JOIN site ON focus_group.id_site = site.id AND (site.exclude IS NULL OR site.exclude = 0) \n            INNER JOIN country ON country.id = site.id_country\n            INNER JOIN world_region ON world_region.id = country.id_world_region\n            INNER JOIN project ON site.id_project = project.id AND (project.exclude IS NULL OR project.exclude = 0) \n            WHERE livestock_holding.headcount IS NOT NULL AND (respondent.exclude IS NULL OR respondent.exclude = 0)\n            GROUP BY {$groupJoin}, animal_category.description) as baseQuery\n            JOIN\n            (SELECT\n            {$groupAlias}\n            COUNT(DISTINCT(respondent.id)) AS interview_respondents\n            FROM \n            respondent\n            INNER JOIN gender ON respondent.id_gender = gender.id\n            INNER JOIN focus_group ON focus_group.id = respondent.id_focus_group AND (focus_group.exclude IS NULL OR focus_group.exclude = 0) \n            INNER JOIN site ON focus_group.id_site = site.id AND (site.exclude IS NULL OR site.exclude = 0) \n            INNER JOIN country ON country.id = site.id_country\n            INNER JOIN world_region ON world_region.id = country.id_world_region\n            INNER JOIN project ON site.id_project = project.id AND (project.exclude IS NULL OR project.exclude = 0) \n            WHERE (respondent.exclude IS NULL OR respondent.exclude = 0)\n            {$filters}\n            {$groupQuery}) as secondaryQuery\n            ON baseQuery.description = secondaryQuery.description";
                break;
            case 'incomesources':
                $query = "SELECT\n            secondaryQuery.interview_respondents,\n            secondaryQuery.description,\n            baseQuery.income_category,\n            (overall_percent/(interview_respondents)) as percentage\n            FROM\n            (SELECT \n            {$groupAlias}\n            income_activity_category.description as income_category,\n            SUM(COALESCE((income_activity.percent_of_hh_income * .01), 0)) as overall_percent \n            FROM \n            respondent\n            LEFT JOIN income_activity ON income_activity.id_respondent = respondent.id \n            LEFT JOIN income_activity_type ON income_activity_type.id = income_activity.id_income_activity_type \n            LEFT OUTER JOIN income_activity_category ON income_activity_category.id = income_activity_type.id_income_activity_category \n            INNER JOIN gender ON respondent.id_gender = gender.id\n            INNER JOIN focus_group ON focus_group.id = respondent.id_focus_group AND (focus_group.exclude IS NULL OR focus_group.exclude = 0) \n            INNER JOIN site ON focus_group.id_site = site.id AND (site.exclude IS NULL OR site.exclude = 0) \n            INNER JOIN country ON country.id = site.id_country\n            INNER JOIN world_region ON world_region.id = country.id_world_region\n            INNER JOIN project ON site.id_project = project.id AND (project.exclude IS NULL OR project.exclude = 0) \n            WHERE income_activity_type.description IS NOT NULL AND (respondent.exclude IS NULL OR respondent.exclude = 0)\n            GROUP BY {$groupJoin}, income_activity_category.description) as baseQuery\n            JOIN\n            (SELECT\n            {$groupAlias} \n            COUNT(DISTINCT(respondent.id)) AS interview_respondents\n            FROM \n            respondent\n            INNER JOIN gender ON respondent.id_gender = gender.id\n            INNER JOIN focus_group ON focus_group.id = respondent.id_focus_group AND (focus_group.exclude IS NULL OR focus_group.exclude = 0) \n            INNER JOIN site ON focus_group.id_site = site.id AND (site.exclude IS NULL OR site.exclude = 0) \n            INNER JOIN country ON country.id = site.id_country\n            INNER JOIN world_region ON world_region.id = country.id_world_region\n            INNER JOIN project ON site.id_project = project.id AND (project.exclude IS NULL OR project.exclude = 0) \n            WHERE (respondent.exclude IS NULL OR respondent.exclude = 0)\n            {$filters}\n            {$groupQuery}) as secondaryQuery\n            ON baseQuery.description = secondaryQuery.description";
                break;
            case 'rainfall':
                $rainfallFilter = '';
                $feedFilter = '';
                $respondentFilter = '';
                $rainfallFilters = [];
                $feedFilters = [];
                $respondentFilters = [];
                $feedTable = "feed_source_values_all";
                $respondentTable = "interview_respondents_all";
                if ($this->Auth->user() && isset($this->request->getQueryParams()['mine'])) {
                    $rainfallFilters[] = 'focus_group.id_user = ' . $this->Auth->user('id');
                    $feedFilters[] = 'feed_source_values_user.user_id = ' . $this->Auth->user('id');
                    $respondentFilters[] = 'interview_respondents_user.user_id = ' . $this->Auth->user('id');
                    $feedTable = "feed_source_values_user";
                    $respondentTable = "interview_respondents_user";
                } else {
                    $rainfallFilters[] = '(focus_group.keep_private IS NULL OR focus_group.keep_private = 0 OR focus_group.uploaded_at < DATE_SUB(CURDATE(), INTERVAL 1 YEAR))';
                }
                if (isset($this->request->getQueryParams()['c'])) {
                    $rainfallFilters[] = 'country.id = ' . $this->request->getQueryParams()['c'];
                    $feedFilters[] = "{$feedTable}.country_id = " . $this->request->getQueryParams()['c'];
                    $respondentFilters[] = "{$respondentTable}.country_id = " . $this->request->getQueryParams()['c'];
                }
                if (isset($this->request->getQueryParams()['w'])) {
                    $rainfallFilters[] = 'country.id_world_region = ' . $this->request->getQueryParams()['w'];
                    $feedFilters[] = "{$feedTable}.world_region_id = " . $this->request->getQueryParams()['w'];
                    $respondentFilters[] = "{$respondentTable}.world_region_id = " . $this->request->getQueryParams()['v'];
                }
                if (isset($this->request->getQueryParams()['p'])) {
                    $rainfallFilters[] = 'site.id_project = ' . $this->request->getQueryParams()['p'];
                    $feedFilters[] = "{$feedTable}.project_id = " . $this->request->getQueryParams()['p'];
                    $respondentFilters[] = "{$respondentTable}.project_id = " . $this->request->getQueryParams()['p'];
                }
                if (isset($this->request->getQueryParams()['s'])) {
                    $rainfallFilters[] = 'site.id = ' . $this->request->getQueryParams()['s'];
                    $feedFilters[] = "{$feedTable}.site_id = " . $this->request->getQueryParams()['s'];
                    $respondentFilters[] = "{$respondentTable}.site_id = " . $this->request->getQueryParams()['s'];
                }
                if (count($rainfallFilters) > 0) {
                    $rainfallFilter = ' AND ' . implode(' AND ', $rainfallFilters);
                }
                if (count($feedFilters) > 0) {
                    $feedFilter = ' WHERE ' . implode(' AND ', $feedFilters);
                }
                if (count($respondentFilters) > 0) {
                    $respondentFilter = ' WHERE ' . implode(' AND ', $respondentFilters);
                }
                $query = "SELECT month.name AS name_of_month, month.ordering AS order_of_month, 'Rainfall' as resource_type, AVG(scale_zero_five.number) AS numerical_value \n            FROM month \n            LEFT OUTER JOIN focus_group_monthly_statistics ON focus_group_monthly_statistics.id_month = month.id AND (focus_group_monthly_statistics.exclude IS NULL OR focus_group_monthly_statistics.exclude = 0)\n            LEFT OUTER JOIN focus_group ON focus_group.id = focus_group_monthly_statistics.id_focus_group AND (focus_group.exclude IS NULL OR focus_group.exclude = 0)  \n            LEFT OUTER JOIN site ON site.id = focus_group.id_site AND (site.exclude IS NULL OR site.exclude = 0)\n            LEFT OUTER JOIN country ON country.id = site.id_country\n            LEFT OUTER JOIN scale_zero_five ON focus_group_monthly_statistics.id_scale_zero_five = scale_zero_five.id \n            WHERE scale_zero_five.number IS NOT NULL AND (focus_group.exclude IS NULL OR focus_group.exclude = 0) {$rainfallFilter}\n            GROUP BY month.name\n            UNION ALL \n            SELECT tfsv.name_of_month AS name_of_month, tfsv.month_order AS order_of_month, tfsv.resource_type AS resource_type,\n            SUM(tfsv.numerical_value) \n            FROM (\n            SELECT month_order, name_of_month, resource_type, numerical_value_raw, numerical_value_raw / tir.number_of AS numerical_value FROM \n            {$feedTable}, (SELECT SUM(number_of) AS number_of FROM {$respondentTable} {$respondentFilter}) as tir\n            {$feedFilter}\n            ) as tfsv\n            GROUP BY tfsv.name_of_month, tfsv.resource_type\n            ORDER BY order_of_month";
                break;
            default:
                $this->set('report', []);
                return $this->set('_serialize', ['report']);
        }
        //Log::debug($query);
        $result = $this->connection->execute($query)->fetchAll('assoc');
        if (!isset($this->request->getQueryParams()['csv'])) {
            $this->RequestHandler->renderAs($this, 'json');
            $this->set('report', $result);
            $this->set('_serialize', ['report']);
        } else {
	    $response = $this->response;
            $response->setTypeMap('csv', ['text/csv']);
            $response = $response->withType('csv');
            $response = $response->withStringBody($this->str_putcsv($result));
            $response = $response->withDownload("{$chartType}.csv");
            return $response;
        }
    }
    private function getFilters()
    {
        $filters = [];
        if (isset($this->request->getQueryParams()['c'])) {
            $filters[] = 'country.id = ' . $this->request->getQueryParams()['c'];
        }
        if (isset($this->request->getQueryParams()['w'])) {
            $filters[] = 'world_region.id = ' . $this->request->getQueryParams()['w'];
        }
        if (isset($this->request->getQueryParams()['p'])) {
            $filters[] = 'project.id = ' . $this->request->getQueryParams()['p'];
        }
        if (isset($this->request->getQueryParams()['s'])) {
            $filters[] = 'site.id = ' . $this->request->getQueryParams()['s'];
        }
        if (isset($this->request->getQueryParams()['g'])) {
            $filters[] = 'gender.id = ' . $this->request->getQueryParams()['g'];
        }
        if ($this->Auth->user() && isset($this->request->getQueryParams()['mine'])) {
            $filters[] = 'respondent.id_user = ' . $this->Auth->user('id');
        } else {
            $filters[] = '(respondent.keep_private = 0 OR respondent.keep_private IS NULL OR respondent.uploaded_at < DATE_SUB(CURDATE(), INTERVAL 1 YEAR))';
        }
        $query = implode($filters, ' AND ');
        if (count($filters) > 0) {
            return " AND {$query}";
        } else {
            return '';
        }
    }
    /**
     * Convert a multi-dimensional, associative array to CSV data
     * @param  array $data the array of data
     * @return string       CSV text
     */
    function str_putcsv($data)
    {
        # Generate CSV data from array
        $fh = fopen('php://temp', 'rw');
        # don't create a file, attempt
        # to use memory instead
        # write out the headers
        fputcsv($fh, array_keys(current($data)));
        # write out the data
        foreach ($data as $row) {
            fputcsv($fh, $row);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);
        return $csv;
    }
}
