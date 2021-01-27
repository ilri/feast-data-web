<?php

/**
 * FEAST Data Aggregator / Repository
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Controller\Component\SystemComponent as ComSystem;
use App\Controller\Component\UploadComponent as ComUpload;
/**
 * 
 * Controller for downloader views
 * 
 */
class DataController extends AppController
{
    // Cascade exclusion settings
    private $cascadeTree = ['tableName' => 'project', 'tableChildren' => [
            ['tableName' => 'site', 'tableChildren' => [
                    ['tableName' => 'focus_group', 'tableChildren' => [
                            ['tableName' => 'focus_group_monthly_statistics',],
                            ['tableName' => 'respondent', 'tableChildren' => [
                                    [
                                        'tableName' => 'crop_cultivation',
                                    ],
                                    [
                                        'tableName' => 'feed_source_availability',
                                    ],
                                    [
                                        'tableName' => 'fodder_crop_cultivation',
                                    ],
                                    [
                                        'tableName' => 'income_activity',
                                    ],
                                    [
                                        'tableName' => 'livestock_holding',
                                    ],
                                    [
                                        'tableName' => 'livestock_sale',
                                    ],
                                    [
                                        'tableName' => 'purchased_feed',
                                    ],
                                    [
                                        'tableName' => 'respondent_monthly_statistics',
                                    ],
                                    [
                                        'tableName' => 'coop_membership',
                                    ],
                                    [
                                        'tableName' => 'decision_making_by_household',
                                    ],
                                    [
                                        'tableName' => 'feed_labor_division',
                                    ],
                                    [
                                        'tableName' => 'womens_income_activity',
                                    ],
                                ]
                            ],
                            ['tableName' => 'techfit_assessment', 'tableChildren' => [
                                    [
                                        'tableName' => 'core_context_attribute_score',
                                    ],
                                ]
                            ],
                            ['tableName' => 'labour_activity',],
                        ],
                    ]
                ]
            ]
        ]
    ];
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
        $this->Auth->allow(['getDirectoryData', 'getUserData']);
    }
    public $users;
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
    public function getPrivateProjects()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        if (isset($this->request->getQueryParams()['last'])) {
            $lastID = $this->request->getQueryParams()['last'];
        }
        $tableAlias = 'ProjectView';
        $table = TableRegistry::get($tableAlias);
        $query = $table->find('all')->contain(['SystemCountry', 'User', 'SiteView']);
        if (!empty($query)) {
            $whereQuery = [];
            if (!$isAdmin || (empty($this->request->getQueryParams()['isAdmin']) || $this->request->getQueryParams()['isAdmin'] != "true")) {
                Log::debug("Restricting to logged-in user");
                $whereQuery["{$tableAlias}.id_user"] = $this->Auth->user('id');
            }
            if (!empty($lastID)) {
                $whereQuery["{$tableAlias}.id >"] = $lastID;
            }
            $whereQuery['ProjectView.keep_private'] = true;
            if (count($whereQuery) > 0) {
                $query->where($whereQuery);
            }
            $this->addConditions($query, $tableAlias);
            $result = $query->limit(100)->toArray();
        } else {
            $result = 'No data.';
        }
        $this->set('results', $result);
        $this->set('_serialize', ['results']);
    }
    public function getUserData($scope = '')
    {
        $this->RequestHandler->renderAs($this, 'json');
        $tableName = $this->request->getAttribute('params')['table'];
        $isAdmin = false;
        if (!$this->Auth->user() && in_array($tableName, ['site', 'project'])) {
            $scope = 'directory';
        } else {
            if (!$this->Auth->user()) {
                $this->RequestHandler->renderAs($this, 'json');
                $this->set('results', 'Not Authorized');
                $this->set('_serialize', ['results']);
            } else {
                $isAdmin = ComSystem::isAdmin($this->Auth->user());
	    }
	    $scope = empty($scope) ? 'mine' : 'directory';
        }
        if (isset($this->request->getQueryParams()['last'])) {
            $lastID = $this->request->getQueryParams()['last'];
        }

        if ($this->Auth->user() && isset($this->request->getAttribute('params')['scope']) && $this->request->getAttribute('params')['scope'] == 'all') {
            $scope = 'all';
        } else {
            if ($isAdmin && isset($this->request->getQueryParams()['isAdmin']) && $this->request->getQueryParams()['isAdmin'] == 'true') {
                $scope = 'admin';
            } else {
                if (isset($this->request->getAttribute('params')['scope']) && $this->request->getAttribute('params')['scope'] == 'directory') {
                    $scope = 'directory';
                }
            }
        }
        $queryInfo = $this->getBaseQuery($tableName, $isAdmin);
        $query = $queryInfo['query'];
        $tableAlias = $queryInfo['tableAlias'];
        if (!empty($query)) {
            $whereQuery = [];
            if ($scope == 'mine') {
                Log::debug("Restricting to logged-in user");
                $whereQuery["{$tableAlias}.id_user"] = $this->Auth->user('id');
            } else {
                if ($scope == 'all') {
                    if (in_array($tableName, ComUpload::$canKeepPrivate) && in_array($tableName, ComUpload::$canExclude)) {
                        $date_sub = $query->func()->date_sub(['CURDATE()' => 'literal', 'INTERVAL 1 YEAR' => 'literal']);
                        $whereQuery[] = ["OR" => [["{$tableAlias}.keep_private" => 0], ["{$tableAlias}.keep_private IS" => null], ["{$tableAlias}.uploaded_at <" => $date_sub], ["{$tableAlias}.id_user" => $this->Auth->user('id')]]];
                        $whereQuery[] = ["OR" => [["{$tableAlias}.exclude" => 0], ["{$tableAlias}.exclude IS" => null], ["{$tableAlias}.id_user" => $this->Auth->user('id')]]];
                    } else {
                        if (in_array($tableName, ComUpload::$canExclude)) {
                            $whereQuery["OR"] = [["{$tableAlias}.exclude = " => 0], ["{$tableAlias}.exclude IS" => null], ["{$tableAlias}.id_user" => $this->Auth->user('id')]];
                        } else {
                            if (in_array($tableName, ComUpload::$canKeepPrivate)) {
                                $whereQuery["OR"] = [["{$tableAlias}.keep_private =" => 0], ["{$tableAlias}.keep_private IS" => null], ["{$tableAlias}.uploaded_at <" => $date_sub], ["{$tableAlias}.id_user" => $this->Auth->user('id')]];
                            }
                        }
                    }
                } else {
                    if ($scope == 'directory') {
                        if (in_array($tableName, ComUpload::$canExclude)) {
                            $whereQuery[] = ["OR" => [["{$tableAlias}.exclude" => 0], ["{$tableAlias}.exclude IS" => null]]];
                        }
                    }
                }
            }
            if (!empty($lastID)) {
                $whereQuery["{$tableAlias}.id >"] = $lastID;
            }
            if (count($whereQuery) > 0) {
                $query->where($whereQuery);
            }
            $this->addConditions($query, $tableAlias);
            // Log::debug($query);
            $resultCount = $query->count();
            $result = $query->limit(100)->toArray();
            if ($scope == 'directory') {
                foreach ($result as $thisResult) {
                    unset($thisResult->user);
                    unset($thisResult->id_user);
                }
            }
            $resultObj['count'] = $resultCount;
            $resultObj['data'] = $result;
            $resultObj['currentUser'] = $this->Auth->user() ? $this->Auth->user()['id'] : null;
        } else {
            $resultObj = 'No data.';
        }
        $this->set('results', $resultObj);
        $this->set('_serialize', ['results']);
    }
    function getBaseQuery($tableName, $isAdmin)
    {
        $tableAlias = null;
        $query = null;
        switch ($tableName) {
            case 'project':
                $tableAlias = 'ProjectView';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['SystemCountry', 'User']);
                break;
            case 'site':
                $tableAlias = 'SiteView';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['ProjectView', 'SystemCountry', 'User']);
                break;
            case 'focus_group':
                $tableAlias = 'FocusGroupView';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['SiteView.ProjectView', 'Gender', 'User']);
                break;
            case 'focus_group_monthly_statistics':
                $tableAlias = 'FocusGroupMonthlyStatistics';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['FocusGroupView.SiteView.ProjectView', 'Month', 'ScaleZeroFive', 'User']);
                break;
            case 'respondent':
                $tableAlias = 'Respondent';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['FocusGroupView.SiteView.ProjectView', 'Gender', 'LandholdingCategory', 'User']);
                break;
            case 'respondent_monthly_statistics':
                $tableAlias = 'RespondentMonthlyStatistics';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['Respondent.FocusGroupView.SiteView.ProjectView', 'Month', 'User']);
                break;
            case 'fodder_crop_cultivation':
                $tableAlias = 'FodderCropCultivation';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['Respondent.FocusGroupView.SiteView.ProjectView', 'FodderCropType', 'UnitArea', 'User']);
                break;
            case 'focus_group':
                $tableAlias = 'FocusGroupView';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'season':
                $tableAlias = 'Season';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'livestock_holding':
                $tableAlias = 'LivestockHolding';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['Respondent.FocusGroupView.SiteView.ProjectView', 'AnimalType', 'User']);
                break;
            case 'livestock_sale_category':
                $tableAlias = 'LivestockSaleCategory';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['AnimalSpecies', 'Gender', 'User']);
                break;
            case 'livestock_sale':
                $tableAlias = 'LivestockSale';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['Respondent.FocusGroupView.SiteView.ProjectView', 'LivestockSaleCategory.AnimalSpecies', 'LivestockSaleCategory.Gender', 'User']);
                break;
            case 'labour_activity':
                $tableAlias = 'LabourActivity';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['FocusGroupView.SiteView.ProjectView', 'User']);
                break;
            case 'purchased_feed':
                $tableAlias = 'PurchasedFeed';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['Respondent.FocusGroupView.SiteView.ProjectView', 'Respondent.FocusGroupView.SiteView.Currency', 'PurchasedFeedType', 'UnitMassWeight', 'FeedCurrency', 'User']);
                break;
            case 'techfit_assessment':
                $tableAlias = 'TechfitAssessment';
                break;
            case 'animal_category':
                $tableAlias = 'AnimalCategory';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['AnimalSpecies', 'User']);
                break;
            case 'animal_species':
                $tableAlias = 'AnimalSpecies';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'animal_type':
                $tableAlias = 'AnimalType';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['AnimalCategory.AnimalSpecies', 'User']);
                break;
            case 'community_type':
                $tableAlias = 'CommunityType';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'core_context_attribute_score':
                $tableAlias = 'CoreContextAttributeScore';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['CoreContextAttribute.CoreContextAttributeType', 'TechfitScale', 'TechfitAssessment', 'User']);
                break;
            case 'crop_cultivation':
                $tableAlias = 'CropCultivation';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['Respondent.FocusGroupView.SiteView.ProjectView', 'CropType', 'UnitArea', 'UnitMassWeight', 'User']);
                break;
            case 'crop_type':
                $tableAlias = 'CropType';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'feed_source':
                $tableAlias = 'FeedSource';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'feed_source_availability':
                $tableAlias = "FeedSourceAvailability";
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['Respondent.FocusGroupView.SiteView.ProjectView', 'FeedSource', 'Month', 'User']);
                break;
            case 'fodder_crop_type':
                $tableAlias = 'FodderCropType';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'income_activity_type':
                $tableAlias = 'IncomeActivityType';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['IncomeActivityCategory', 'User']);
                break;
            case 'income_activity':
                $tableAlias = 'IncomeActivity';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['Respondent.FocusGroupView.SiteView.ProjectView', 'IncomeActivityType.IncomeActivityCategory', 'User']);
                break;
            case 'purchased_feed_type':
                $tableAlias = 'PurchasedFeedType';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'unit_area':
                $tableAlias = 'UnitArea';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'unit_mass_weight':
                $tableAlias = 'UnitMassWeight';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            case 'intervention':
                $tableAlias = 'Intervention';
                $table = TableRegistry::get($tableAlias);
                $query = $table->find('all')->contain(['User']);
                break;
            default:
        }
        $queryInfo = ['query' => $query, 'tableAlias' => $tableAlias];
        return $queryInfo;
    }
    /**
     * Use a switch out of abundance of paranoia...if the term doesn't match,
     * fall through.
     */
    function addConditions(&$query, $tableAlias)
    {
        if (!empty($this->request->getQueryParams()['sc'])) {
            $searchCount = intval($this->request->getQueryParams()['sc']);
            Log::debug(serialize($this->request->getQueryParams()));
            $where = [];
            for ($i = 0; $i < $searchCount; $i++) {
                $thisTerm = $this->request->getQueryParams()['st' . $i];
                $thisValue = $this->request->getQueryParams()['sv' . $i];
                switch ($thisTerm) {
                    case 'Currency.name':
                        $where[] = ["OR" => [["Currency.name LIKE" => '%' . $thisValue . '%'], ["FeedCurrency.name LIKE" => '%' . $thisValue . '%']]];
                        break;
                    case 'GenderGroup.description':
		    case 'Gender.description':
                        if (strtolower($thisValue) == 'male') {
                            $where[$thisTerm] = $thisValue;
                        } else {
                            $where[$thisTerm . ' LIKE'] = '%' . $thisValue . '%';
                        }
                        break;
                    case 'ID':
                        $where["{$tableAlias}.id"] = $thisValue;
                        break;
                    case 'AnimalCategory.description':
                    case 'AnimalSpecies.description':
                    case 'AnimalType.description':
                    case 'CommunityType.description':
                    case 'CoreContextAttributeType.description':
                    case 'CoreContextAttribute.prompt':
		    case 'CropType.name':
                    case 'Decision.description':
                    case 'DecisionType.description':
                    case 'FeedLaborType.description':			    
                    case 'FeedSource.description':
                    case 'FocusGroupView.community':
                    case 'FocusGroupView.venue_name':
                    case 'FodderCropType.name':
                    case 'IncomeActivity.description':
                    case 'IncomeActivityCategory.description':
                    case 'IncomeActivityType.description':
                    case 'Intervention.description':
		    case 'LabourActivity.description':
		    case 'LaborDivisionGroup.description':
                    case 'LandholdingCategory.description':
                    case 'LivestockSaleCategory.Gender.description':
                    case 'Month.name':
                    case 'ProjectView.description':
                    case 'ProjectView.unique_identifier':
                    case 'ProjectView.title':
                    case 'Respondent.head_of_household_occupation':
                    case 'Respondent.unique_identifier':
                    case 'PurchasedFeedType.name':
                    case 'Season.name':
                    case 'SiteView.name':
                    case 'SystemCountry.name':
                    case 'UnitArea.name':
                    case 'UnitMassWeight.name':
                        $where[$thisTerm . ' LIKE'] = '%' . $thisValue . '%';
                        break;
                    case 'User.id':
                    case 'AnimalCategory.uploaded_at':
                    case 'AnimalCategory.replaced_by_id':
                    case 'AnimalSpecies.dairy':
                    case 'AnimalSpecies.lactating':
                    case 'AnimalSpecies.replaced_by_id':
                    case 'AnimalSpecies.uploaded_at':
                    case 'AnimalType.replaced_by_id':
                    case 'AnimalType.uploaded_at':
                    case 'AnimalType.weight_lower_limit':
                    case 'AnimalType.weight_upper_limit':
                    case 'CommunityType.replaced_by_id':
                    case 'CommunityType.uploaded_at':
                    case 'CoreContextAttribute.prompt':
                    case 'CoreContextAttribute.uploaded_at':
                    case 'CoreContextAttributeScore.uploaded_at':
                    case 'TechfitScale.number':
                    case 'TechfitAssessment.id':
                    case 'CropCultivation.annual_yield':
                    case 'CropCultivation.exclude':
                    case 'CropCultivation.cultivated_land':
                    case 'CropCultivation.uploaded_at':
                    case 'CropType.content_crude_protein':
                    case 'CropType.content_metabolisable_energy':
                    case 'CropType.content_percent_dry_matter':
                    case 'CropType.harvest_index':
                    case 'CropType.replaced_by_id':
                    case 'CropType.uploaded_at':
                    case 'FeedSource.replaced_by_id':
                    case 'FeedSource.uploaded_at':
                    case 'FeedSourceAvailability.contribution':
                    case 'FeedSourceAvailability.uploaded_at':
                    case 'FocusGroupView.id':
                    case 'FocusGroupView.exclude':
                    case 'FocusGroupView.keep_private':
                    case 'FocusGroupView.meeting_date_time':
                    case 'FocusGroupView.uploaded_at':
                    case 'FocusGroupView.SiteView.ProjectView.id':
                    case 'FocusGroupView.SiteView.id':
                    case 'FocusGroupMonthlyStatistics.id':
                    case 'FocusGroupMonthlyStatistics.exclude':
                    case 'FocusGroupMonthlyStatistics.keep_private':
                    case 'FodderCropCultivation.cultivated_land':
                    case 'FodderCropCultivation.exclude':
                    case 'FodderCropCultivation.keep_private':
                    case 'FodderCropCultivation.uploaded_at':
                    case 'FodderCropType.annual_dry_matter_per_hectare':
                    case 'FodderCropType.content_crude_protein':
                    case 'FodderCropType.content_metabolisable_energy':
                    case 'FodderCropType.replaced_by_id':
                    case 'FodderCropType.uploaded_at':
                    case 'IncomeActivity.keep_private':
                    case 'IncomeActivity.percent_of_hh_income':
                    case 'IncomeActivity.uploaded_at':
                    case 'IncomeActivityCategory.uploaded_at':
                    case 'IncomeActivityType.exclude':
                    case 'IncomeActivityType.keep_private':
                    case 'IncomeActivityType.uploaded_at':
                    case 'Intervention.uploaded_at':
                    case 'LabourActivity.daily_rate_female':
                    case 'LabourActivity.daily_rate_male':
                    case 'LabourActivity.exclude':
                    case 'LabourActivity.keep_private':
                    case 'LabourActivity.uploaded_at':
                    case 'LivestockHolding.average_weight':
                    case 'LivestockHolding.exclude':
                    case 'LivestockHolding.headcount':
                    case 'LivestockHolding.keep_private':
                    case 'LivestockHolding.uploaded_at':
                    case 'LivestockSale.approximate_weight':
                    case 'LivestockSale.exclude':
                    case 'LivestockSale.keep_private':
                    case 'LivestockSale.number_sold':
                    case 'LivestockSale.uploaded_at':
                    case 'LivestockSaleCategory.uploaded_at':
                    case 'ProjectView.exclude':
                    case 'ProjectView.keep_private':
                    case 'ProjectView.id':
                    case 'ProjectView.start_date':
                    case 'ProjectView.uploaded_at':
                    case 'PurchasedFeed.quantity_purchased':
                    case 'PurchasedFeed.exclude':
                    case 'PurchasedFeed.keep_private':
                    case 'PurchasedFeed.uploaded_at':
                    case 'PurchasedFeed.unit_price':
                    case 'PurchasedFeed.purchases_per_year':
                    case 'PurchasedFeedType.content_crude_protein':
                    case 'PurchasedFeedType.content_metabolisable_energy':
                    case 'PurchasedFeedType.percent_dry_matter':
                    case 'PurchasedFeedType.uploaded_at':
                    case 'Respondent.id':
                    case 'Respondent.diet_percent_collected_fodder':
                    case 'Respondent.diet_percent_grazing':
                    case 'Respondent.exclude':
                    case 'Respondent.keep_private':
                    case 'Respondent.uploaded_at':
                    case 'Respondent.FocusGroupView.SiteView.ProjectView.id':
                    case 'Respondent.FocusGroupView.SiteView.id':
                    case 'Respondent.FocusGroupView.id':
                    case 'RespondentMonthlyStatistics.keep_private':
                    case 'RespondentMonthlyStatistics.market_price_cattle':
                    case 'RespondentMonthlyStatistics.market_price_goat':
                    case 'RespondentMonthlyStatistics.market_price_sheep':
                    case 'RespondentMonthlyStatistics.milk_average_price_litre':
                    case 'RespondentMonthlyStatistics.milk_average_yield':
                    case 'RespondentMonthlyStatistics.milk_retained_for_household':
                    case 'RespondentMonthlyStatistics.uploaded_at':
                    case 'ScaleZeroFive.number':
                    case 'Season.uploaded_at':
                    case 'SiteView.exclude':
                    case 'SiteView.id':
                    case 'SiteView.uploaded_at':
                    case 'UnitArea.conversion_ha':
		    case 'UnitMassWeight.conversion_kg':
                    case 'Decision.uploaded_at':
                    case 'DecisionType.uploaded_at':
                    case 'FeedLaborType.uploaded_at':
                    case 'DecisionMakingByHousehold.exclude':
                    case 'DecisionMakingByHousehold.keep_private':
                    case 'DecisionMakingByHousehold.uploaded_at':                         
                    case 'FeedLaborDivision.exclude':
                    case 'FeedLaborDivision.keep_private':
                    case 'FeedLaborDivision.uploaded_at':    
                    case 'CoopMembership.exclude':
                    case 'CoopMembership.keep_private':
                    case 'CoopMembership.name_free_entry':
                    case 'CoopMembership.uploaded_at':
                    case 'WomensIncomeActivity.exclude':
                    case 'WomensIncomeActivity.keep_private':
                    case 'WomensIncomeActivity.pct_womens_income':
                    case 'WomensIncomeActivity.uploaded_at':
                        $where[$thisTerm] = $thisValue;
                        break;
                    default:
                }
                Log::debug(serialize($where));
                if (count($where) > 0) {
                    $query->where($where);
                }
            }
        }
    }
    /**
     * Convert private project record to public
     */
    public function publishUserData()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $projectID = $this->request->getAttribute('params')['projectID'];
        $this->publishCascade('project', null, [$projectID], $this->cascadeTree);
        $this->set('results', 1);
        $this->set('_serialize', ['results']);
    }
    /**
     * Exclude a record (and all child records) from reports/exports.
     */
    public function excludeUserData()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $tableName = $this->request->getAttribute('params')['table'];
        $records = $this->request->getData()['records'];
        $this->cascadeExclusion($tableName, null, $records, null, null, $this->cascadeTree);
        $this->set('results', 1);
        $this->set('_serialize', ['results']);
    }

    /**
     * Revert alias values
     */
    public function revertAlias()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $tableName = $this->request->getAttribute('params')['table'];
        $table = str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName)));
        $table = $this->getTableLocator()->get($table);

        $records = $this->request->getData()['records'];
        $aliasValueTable = $this->getTableLocator()->get('AliasValue');
        $results = [];
        $columns = [];

        foreach ($records as $record) {
            foreach ($record as $key => $value) {
                if ($key == 'id') {
                    $aliasValues = $aliasValueTable->find()->where([
                        'table_name' => $tableName,
                        'tableid' => $record['id']
                    ])->all();

                    $results[$value] = [];
                    foreach ($aliasValues as $aliasValue) {
                        $original = $table->find()->where([
                            'id' => $value
                        ])->first();

                        if ($original) {
                            $aliasValue->alias_value = $original->{$aliasValue->actual_column_name};
                            $aliasValueTable->save($aliasValue);
                            $results[$value] = array_merge($results[$value], [$aliasValue->actual_column_name => $aliasValue->alias_value]);
                        }
                        $columns[$aliasValue->actual_column_name] = ucwords(str_replace('_', ' ', $aliasValue->actual_column_name));
                    }

                    if (empty($results[$value])) {
                        unset($results[$value]);
                    }

                }
            }
        }

        $this->set('results', $results);
        $this->set('columns', $columns);
        $this->set('_serialize', ['results', 'columns']);
    }

    /**
     * Update alias values.
     */
    public function updateAliasValue()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $tableName = $this->request->getAttribute('params')['table'];
        $records = $this->request->getData()['records'];
        $aliasValueTable = $this->getTableLocator()->get('AliasValue');
        foreach ($records as $record) {
            foreach ($record as $key => $value) {
                if ($key != 'id' && isset($record['id'])) {
                    $aliasValue = $aliasValueTable->find()->where([
                        'table_name' => $tableName,
                        'actual_column_name' => $key,
                        'tableid' => $record['id']
                    ])->first();

                    if (!$aliasValue) {
                        $aliasValue = $aliasValueTable->newEmptyEntity();
                        $aliasValue->table_name = $tableName;
                        $aliasValue->actual_column_name = $key;
                        $aliasValue->tableid = $record['id'];
                    }

                    $aliasValue->alias_value = $value;
                    $aliasValueTable->save($aliasValue);
                }
            }
        }
        $this->set('results', 1);
        $this->set('_serialize', ['results']);
    }
    /**
     * NOTE: Recursive. Publish a project and all child tables. One-way operation, no toggle.
     */
    private function publishCascade($thisTable, $parentTable, $affectedRecords, $operationTree)
    {
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        $doExclusion = false;
        if ($thisTable == $operationTree['tableName']) {
            $doExclusion = true;
        }
        Log::debug("Publishing records for {$thisTable}: {$doExclusion}");
        $recordList = [];
        if ($doExclusion) {
            $queryInfo = $this->getBaseQuery($thisTable, $isAdmin);
            $tableAlias = $queryInfo['tableAlias'];
            $table = TableRegistry::get($tableAlias);
            $whereQuery = [];
            if (!$isAdmin) {
                $whereQuery["id_user"] = $this->Auth->user('id');
            }
            if ($thisTable == 'project') {
                $whereQuery["id IN"] = $affectedRecords;
            } else {
                $whereQuery["id_{$parentTable} IN"] = $affectedRecords;
            }
            $query = $table->query()->update();
            $query->set(['keep_private' => null])->where($whereQuery);
            Log::debug(serialize($query));
            $result = $query->execute();
            Log::debug(serialize($affectedRecords));
            $query = $table->find('all')->where($whereQuery);
            $affectedRecords = $query->toArray();
            // Get current state of affected records
            foreach ($affectedRecords as $thisRecord) {
                $recordList[] = $thisRecord->id;
            }
        } else {
            $recordList = $affectedRecords;
        }
        // Process tree children, if there are any.
        if (!empty($operationTree['tableChildren'])) {
            $parentTable = $thisTable;
            foreach ($operationTree['tableChildren'] as $thisChildTree) {
                $operationTable = $thisTable;
                if ($doExclusion) {
                    // We've already found the root table, so exclude all child records
                    $operationTable = $thisChildTree['tableName'];
                }
                $this->publishCascade($operationTable, $parentTable, $recordList, $thisChildTree);
            }
        }
    }
    /**
     * NOTE: Recursive.
     * 
     * Given a node of the tree, walk that node and all its children to
     * set value. First/top node is toggled, children inherit parent state.
     * 
     * @param string $thisTable name of table for which to set exclusion
     * @param string $parentTable name of parent table for which exclusion was toggled (needed for FK check)
     * @param array[int] $affectedRecords list of records to exclude in table that is root of exclusion operation
     * @param array[int] $excludedRecords list of excluded records in parent table
     * @param array[int] $includedRecords list of included records in parent table
     * @param object $excludeTree [remaining] tree of tables to walk through
     */
    private function cascadeExclusion($thisTable, $parentTable, $affectedRecords, $excludedRecords, $includedRecords, $excludeTree)
    {
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        $doExclusion = false;
        if ($thisTable == $excludeTree['tableName']) {
            $doExclusion = true;
        }
        //Log::debug("Excluding records for $thisTable: $doExclusion");
        $recordList = [];
        $excludedList = [];
        $includedList = [];
        if ($doExclusion) {
            $queryInfo = $this->getBaseQuery($thisTable, $isAdmin);
            $tableAlias = $queryInfo['tableAlias'];
            Log::debug("Excluding records for $tableAlias: $doExclusion");
            $table = TableRegistry::get($tableAlias);
            $whereQuery = [];
            if (!$isAdmin) {
                $whereQuery["id_user"] = $this->Auth->user('id');
            }
            $checkRecords = $affectedRecords;
            if (empty($excludedRecords) && empty($includedRecords)) {
                // If we're at the top, just toggle this table's records...
                $whereQuery["id IN"] = $checkRecords;
                $query = $table->query()->update();
                $expr = $query->newExpr()->add('NOT(COALESCE(exclude,0))');
                $query->set(['exclude' => $expr])->where($whereQuery);
                Log::debug($query);
                $result = $query->execute();
                //Log::debug($checkRecords);
                $query = $table->find('all')->where($whereQuery);
                $affectedRecords = $query->toArray();
            } else {
                // ...otherwise make sure exclude/include status matches parent state.
                if (!empty($excludedRecords)) {
                    $whereQuery["id_{$parentTable} IN"] = $excludedRecords;
                    $query = $table->query()->update()->set(['exclude' => 1])->where($whereQuery);
                    //Log::debug($query);
                    $result = $query->execute();
                }
                if (!empty($includedRecords)) {
                    $whereQuery["id_{$parentTable} IN"] = $includedRecords;
                    $query = $table->query()->update()->set(['exclude' => 0])->where($whereQuery);
                    //Log::debug($query);
                    $result = $query->execute();
                }
                $checkRecords = array_merge($excludedRecords, $includedRecords);
                //Log::debug($checkRecords);
                $whereQuery["id_{$parentTable} IN"] = $checkRecords;
                $query = $table->find('all')->where($whereQuery);
                $affectedRecords = $query->toArray();
            }
            // Get current state of affected records
            foreach ($affectedRecords as $thisRecord) {
                if ($thisRecord->exclude) {
                    $excludedList[] = $thisRecord->id;
                } else {
                    $includedList[] = $thisRecord->id;
                }
                $recordList[] = $thisRecord->id;
            }
        } else {
            $recordList = $affectedRecords;
        }
        // Process tree children, if there are any.
        if (!empty($excludeTree['tableChildren'])) {
            $parentTable = $thisTable;
            foreach ($excludeTree['tableChildren'] as $thisChildTree) {
                $excludeTable = $thisTable;
                if ($doExclusion) {
                    // We've already found the root table, so exclude all child records
                    $excludeTable = $thisChildTree['tableName'];
                }
                $this->cascadeExclusion($excludeTable, $parentTable, $recordList, $excludedList, $includedList, $thisChildTree);
            }
        }
    }
    /**
     * Given one or more records, set "replaced_by" to updated record.
     * 
     * ALSO update all tables with FKs pointing to the replaced records.
     * 
     */
    public function consolidateUserData()
    {
        $this->RequestHandler->renderAs($this, 'json');
        $isAdmin = ComSystem::isAdmin($this->Auth->user());
        $tableName = $this->request->getAttribute('params')['table'];
        $queryInfo = $this->getBaseQuery($tableName, $isAdmin);
        $tableAlias = $queryInfo['tableAlias'];
        $table = TableRegistry::get($tableAlias);
        $oldRecords = $this->request->getData()['oldRecords'];
        $newRecord = $this->request->getData()['newRecord'];
        // 0. Find all records with FKs pointing at our old record[s], audit-log them, and update them to point at the new record.
        $this->connection = ConnectionManager::get('default');
        $rawQuery = "SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE \n            WHERE REFERENCED_TABLE_NAME = ? AND REFERENCED_COLUMN_NAME = 'id'";
        $results = $this->connection->execute($rawQuery, [$tableName])->fetchAll('assoc');
        foreach ($results as $thisResult) {
            $thisField = $thisResult['COLUMN_NAME'];
            $thisTable = $thisResult['TABLE_NAME'];
            $integerIDs = array_map('intval', $oldRecords);
            $oldRecordIDs = implode(',', $integerIDs);
            // Find rows where the original FK pointed at one of the records we're re-consolidating
            $lookupQuery = "SELECT * FROM consolidation_audit WHERE table_name = '{$thisTable}' AND field_name = '{$thisField}' AND old_value IN ({$oldRecordIDs})";
            $results = $this->connection->execute($lookupQuery)->fetchAll('assoc');
            $extraRows = [];
            foreach ($results as $thisResult) {
                $extraRows[] = $thisResult['row_id'];
            }
            // Insert audit entry for each affected row
            $insertQuery = "INSERT IGNORE INTO consolidation_audit SELECT NULL,NOW(),?,?,?,id,{$thisField},? FROM {$thisTable} WHERE {$thisField} IN ({$oldRecordIDs})";
            if (!$isAdmin) {
                $insertQuery .= " AND user_id = " . $this->Auth->user('id');
            }
            $this->connection->execute($insertQuery, [$this->Auth->user('contact_email'), $thisTable, $thisField, $newRecord]);
            $extraExpr = '';
            // Update FKs
            if (count($extraRows) > 0) {
                $extraRowIDs = implode(',', $extraRows);
                $extraExpr = " OR id IN ({$extraRowIDs})";
            }
            $updateQuery = "UPDATE {$thisTable} SET {$thisField} = ? WHERE ({$thisField} IN ({$oldRecordIDs}) {$extraExpr})";
            if (!$isAdmin) {
                $updateQuery .= " AND user_id = " . $this->Auth->user('id');
            }
            // Log::debug($updateQuery);
            $this->connection->execute($updateQuery, [$newRecord]);
        }
        // 1. Set all oldRecords replaced_by to newRecord, as well as any records where replaced_by oldRecords
        $whereQuery = [];
        if (!$isAdmin) {
            $whereQuery["id_user"] = $this->Auth->user('id');
        }
        $whereQuery["OR"] = [["id IN" => $oldRecords], ["replaced_by_id IN" => $oldRecords]];
        $query = $table->query()->update()->set(['replaced_by_id' => $newRecord])->where($whereQuery);
        $result = $query->execute();
        // 2. Set newRecord replaced_by to NULL
        $whereQuery = [];
        if (!$isAdmin) {
            $whereQuery["id_user"] = $this->Auth->user('id');
        }
        $whereQuery["id"] = $newRecord;
        $query = $table->query()->update()->set(['replaced_by_id' => null])->where($whereQuery);
        $result = $query->execute();
        $result = ['oldRecords' => $oldRecords, 'newRecord' => $newRecord];
        $this->set('results', $result);
        $this->set('_serialize', ['results']);
    }
    public function getDirectoryData()
    {
        $tableName = $this->request->getAttribute('params')['table'];
        if (in_array($tableName, ['site', 'project'])) {
            return $this->getUserData('directory');
        } else {
            $this->RequestHandler->renderAs($this, 'json');
            $this->set('results', 'Not Authorized');
            $this->set('_serialize', ['results']);
        }
    }
}
