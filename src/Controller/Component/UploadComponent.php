<?php

/**
 * SonataLMS
 * Copyright (c) 2015 Sonata Learning (sonatalearning.com)
 * 
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
/**
 * This component primarily exists to hold common constants. I'm not comfortable
 * with the notion of dumping constants into config/app.php, because they're
 * not really configurable so much as core behavioral flags that should rarely,
 * if ever, be altered. - NS
 */
class UploadComponent extends Component
{
    /**
     * List of views for "key tables" export
     */
    public static $exportViews = ["export_core_context_attribute_score", "export_crop_cultivation", "export_feed_source_availability", "export_focus_group", "export_focus_group_monthly_statistics", "export_fodder_crop_cultivation", "export_income_activity", "export_labour_activity", "export_livestock_holding", "export_livestock_sale", "export_project_site", "export_purchased_feed", "export_respondent", "export_respondent_monthly_statistics"];
    /**
     * These tables can have records excluded, and exclusion is cascading.
     */
    public static $canExclude = ["crop_cultivation", "focus_group", "fodder_crop_cultivation", "labour_activity", "livestock_holding", "livestock_sale", "project", "purchased_feed", "respondent", "site", "techfit_assessment", "respondent_monthly_statistics", "income_activity", "focus_group_monthly_statistics", "feed_source_availability", "core_context_attribute_score"];
    /**
     * Records in these tables can be marked "keep_private"
     */
    public static $canKeepPrivate = ["project", "site", "focus_group", "labour_activity", "respondent", "crop_cultivation", "fodder_crop_cultivation", "livestock_holding", "livestock_sale", "purchased_feed", "techfit_assessment", "respondent_monthly_statistics", "income_activity", "focus_group_monthly_statistics", "feed_source_availability", "core_context_attribute_score"];
    /**
     * These tables do not have canonical values.
     */
    public static $notCanonical = ["app_registration", "project", "site", "focus_group", "focus_group_monthly_statistics", "techfit_assessment", "respondent", "respondent_monthly_statistics", "crop_cultivation", "feed_source_availability", "fodder_crop_cultivation", "income_activity", "labour_activity", "livestock_holding", "purchased_feed", "decision_making_by_household", "womens_income_activity", "feed_labor_division"];
    /**
     * These tables have an uploaded_at field.
     */
    public static $trackUploadTime = ["agriculture_system_type", "animal_category", "animal_species", "animal_type", "app_registration", "community_type", "core_commodity", "core_context_attribute", "core_context_attribute_score", "core_context_attribute_score_calc_method", "crop_cultivation", "crop_type", "feed_source", "feed_source_availability", "focus_group", "focus_group_monthly_statistics", "fodder_crop_cultivation", "fodder_crop_type", "income_activity", "income_activity_category", "income_activity_type", "intervention", "labour_activity", "livestock_holding", "livestock_sale", "livestock_sale_category", "organization_type", "project", "purchased_feed", "purchased_feed_type", "respondent", "respondent_monthly_statistics", "season", "site", "techfit_assessment", "unit_area", "unit_mass_weight"];
    /**
     * These tables are currently assumed not to change and are thus excluded from import.
     */
    public static $excludeTables = ["core_context_attribute_type", "country", "currency", "gender", "landholding_category", "month", "project_type", "scale_zero_five", "scale_zero_ten", "techfit_scale", "unit_type", "world_region"];
    /**
     * !! IMPORTANT !!
     * NOTE: These arrays match the exported CSV from SQLite which does NOT contain
     * header rows. If that data format changes, the headers need to be updated
     * here.
     */
    public static $columnHeaders = [
        ["agriculture_system_type", "unique_identifier", "description", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["animal_species", "unique_identifier", "description", "canonical_data", "created_at", "updated_at", "created_by", "updated_by"],
        ["animal_category", "unique_identifier", "id_animal_species", "description", "canonical_data", "updated_at", "created_at", "created_by", "updated_by"],
        ["gender", "unique_identifier", "description", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["animal_type", "unique_identifier", "id_animal_category", "description", "lactating", "dairy", "canonical_data", "updated_at", "created_at", "id_gender", "weight_lower_limit", "weight_upper_limit", "created_by", "updated_by"],
        ["unit_type", "unique_identifier", "description", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["unit_area", "unique_identifier", "name", "id_unit_type", "conversion_ha", "canonical_data", "updated_at", "created_at", "created_by", "updated_by"],
        ["unit_mass_weight", "unique_identifier", "name", "id_unit_type", "conversion_kg", "canonical_data", "created_at", "updated_at", "created_by", "updated_by"],
        ["organization_type", "unique_identifier", "description", "canonical_data", "created_at", "updated_at", "created_by", "updated_by"],
        ["currency", "unique_identifier", "name", "default_usd_exchange_rate", "abbreviation", "updated_at", "created_at", "canonical_data", "current_usd_exchange_rate", "created_by", "updated_by"],
        ["world_region", "unique_identifier", "name", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["country", "unique_identifier", "name", "id_currency", "id_world_region", "created_by", "updated_by", "created_at", "updated_at", "canonical_data"],
        ["app_registration", "unique_identifier", "name_first", "name_last", "email", "id_organization_type", "random_identifier", "created_at", "updated_at", "version", "hard_drive_volume_serial", "concatenated_identifier", "synchronized_at", "app_path", "created_by", "updated_by", "organization_name", "id_country"],
        ["community_type", "unique_identifier", "description", "canonical_data", "created_at", "updated_at", "created_by", "updated_by"],
        ["core_commodity", "unique_identifier", "description", "created_by", "updated_by", "created_at", "updated_at", "canonical_data"],
        ["core_context_attribute_type", "unique_identifier", "description", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["core_context_attribute_score_calc_method", "unique_identifier", "description", "created_by", "updated_by", "created_at", "updated_at", "canonical_data"],
        ["core_context_attribute", "unique_identifier", "prompt", "reference", "id_core_context_attribute_type", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["project_type", "unique_identifier", "description", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["project", "unique_identifier", "id_project_type", "title", "description", "id_world_region", "id_country", "start_date", "end_date", "validate_synchronized_to_web", "created_at", "updated_at", "partner_organization", "created_by", "updated_by"],
        ["site", "unique_identifier", "id_project", "id_country", "name", "community", "sub_region", "minor_region", "major_region", "description", "id_community_type", "id_currency", "grazing_metabolisable_energy", "grazing_crude_protein_percentage", "collected_fodder_metabolisable_energy", "collected_fodder_crude_protein_percentage", "created_at", "updated_at", "created_by", "updated_by"],
        ["focus_group", "unique_identifier", "id_site", "meeting_date_time", "participant_count_male", "participant_count_female", "venue_name", "community_type", "gps_latitude_degrees", "gps_longitude_degrees", "threshold_large_farm_ha", "threshold_small_farm_ha", "percent_households_landless", "percent_households_small", "percent_households_medium", "percent_households_large", "created_at", "updated_at", "Description", "start_time", "end_time", "major_region", "minor_region", "sub_region", "community", "id_community_type", "partner_organization", "other_attendees", "id_unit_area", "households_total", "households_average_members", "household_percent_migrating", "percent_credit_informal", "percent_credit_formal", "percent_reproduction_ai", "percent_reproduction_bull", "percent_processing_male", "percent_processing_female", "percent_processing_overall", "market_avg_distance_km", "market_avg_cost_travel", "gps_latitude_minutes", "gps_longitude_minutes", "gps_latitude_seconds", "gps_longitude_seconds", "validate_no_respondents", "created_by", "updated_by"],
        ["scale_zero_five", "unique_identifier", "number", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["scale_zero_ten", "unique_identifier", "number", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["month", "unique_identifier", "name", "days", "ordering", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["season", "unique_identifier", "name", "id_focus_group", "active", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["focus_group_monthly_statistics", "unique_identifier", "id_focus_group", "id_month", "id_season", "id_scale_zero_five", "placeholder", "created_by", "updated_by", "created_at", "updated_at"],
        ["techfit_assessment", "unique_identifier", "id_focus_group", "id_core_commodity", "id_agriculture_system_type", "lease_price_ha_land", "percent_land_fallow", "percent_land_cash_crop", "percent_land_subsistence", "percent_land_fodder", "labour_daily_cost_max", "labour_daily_cost_min", "percent_land_cultivated", "created_at", "updated_at", "created_by", "updated_by", "title", "id_site", "weight_core_feed_issue", "weight_commodity", "weight_fs", "weight_context_attribute", "weight_impact"],
        ["techfit_scale", "unique_identifier", "number", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["core_context_attribute_score", "unique_identifier", "id_techfit_scale", "id_techfit_assessment", "id_core_context_attribute", "id_core_context_attribute_score_calc_method", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["crop_type", "unique_identifier", "name", "harvest_index", "content_percent_dry_matter", "content_metabolisable_energy", "content_crude_protein", "canonical_data", "updated_at", "created_at", "user_citation", "created_by", "updated_by"],
        ["landholding_category", "unique_identifier", "description", "created_by", "updated_by", "created_at", "updated_at", "canonical_data"],
        // NOTE: "respondent" table's field "id_gender_1" renamed to "id_gender_head_of_household" in MariaDB so reflected here.
        ["respondent", "unique_identifier", "id_focus_group", "interview_date", "name", "age", "id_gender", "head_of_household_is_respondent", "head_of_household_name", "head_of_household_age", "id_gender_head_of_household", "community", "id_community_type", "sub_region", "minor_region", "major_region", "id_country", "gps_latitude", "gps_longitude", "id_landholding_category", "land_under_cultivation", "head_of_household_occupation", "diet_percent_collected_fodder", "diet_percent_grazing", "organization_affiliation", "id_unit_area", "validate_no_crops", "validate_no_livestock_holdings", "validate_no_cultivated_fodder", "validate_no_purchased_feed", "validate_no_livestock_sales", "validate_no_market_prices", "validate_no_milk_yield", "created_at", "updated_at", "created_by", "updated_by", "diet_percent_cultivated_fodder", "diet_percent_purchased_feed", "diet_percent_crop_residue"],
        ["respondent_monthly_statistics", "unique_identifier", "id_respondent", "id_month", "milk_average_yield", "milk_average_price_litre", "milk_retained_for_household", "market_price_cattle", "market_price_sheep", "market_price_goat", "id_scale_zero_ten", "created_at", "updated_at", "created_by", "updated_by"],
        ["crop_cultivation", "unique_identifier", "id_respondent", "id_crop_type", "cultivated_land", "id_unit_area", "annual_yield", "id_unit_mass_weight", "percent_fed", "percent_burned", "percent_mulched", "percent_sold", "percent_other", "created_by", "updated_by", "created_at", "updated_at"],
        ["feed_source", "unique_identifier", "canonical_data", "description", "id_site", "created_by", "updated_by", "created_at", "updated_at", "list_order"],
        ["feed_source_availability", "unique_identifier", "id_feed_source", "id_month", "id_respondent", "contribution", "created_at", "updated_at", "created_by", "updated_by"],
        ["fodder_crop_type", "unique_identifier", "name", "annual_dry_matter_per_hectare", "content_metabolisable_energy", "content_crude_protein", "canonical_data", "created_at", "updated_at", "user_citation", "created_by", "updated_by"],
        ["fodder_crop_cultivation", "unique_identifier", "id_respondent", "id_fodder_crop_type", "cultivated_land", "id_unit_area", "created_at", "updated_at", "created_by", "updated_by"],
        ["income_activity_category", "unique_identifier", "description", "created_by", "updated_by", "created_at", "updated_at", "canonical_data"],
        ["income_activity_type", "unique_identifier", "id_income_activity_category", "description", "canonical_data", "created_by", "updated_by", "created_at", "updated_at"],
        ["income_activity", "unique_identifier", "id_respondent", "id_income_activity_type", "percent_of_hh_income", "created_at", "updated_at", "created_by", "updated_by"],
        ["labour_activity", "unique_identifier", "id_focus_group", "description", "daily_rate_female", "daily_rate_male", "created_by", "updated_by", "created_at", "updated_at"],
        ["livestock_holding", "unique_identifier", "id_respondent", "id_animal_type", "average_weight", "headcount", "created_at", "updated_at", "created_by", "updated_by", "dominant_breed"],
        ["livestock_sale_category", "unique_identifier", "id_animal_species", "id_gender", "created_by", "updated_by", "created_at", "updated_at", "canonical_data"],
        ["livestock_sale", "unique_identifier", "id_respondent", "id_livestock_sale_category", "number_sold", "approximate_weight", "created_at", "updated_at", "created_by", "updated_by", "canonical_data"],
        ["purchased_feed_type", "unique_identifier", "name", "content_percent_dry_matter", "content_metabolisable_energy", "content_crude_protein", "canonical_data", "created_at", "updated_at", "user_citation", "created_by", "updated_by"],
        ["purchased_feed", "unique_identifier", "id_respondent", "quantity_purchased", "unit_price", "id_unit_mass_weight", "id_purchased_feed_type", "purchases_per_year", "id_currency", "created_by", "updated_by", "created_at", "updated_at"],
        ["decision_making_by_household", "unique_identifier", "id_decision", "id_gender_group", "created_at", "updated_at", "created_by", "updated_by", "id_respondent"],
        ["womens_income_activity", "unique_identifier", "id_respondent", "id_income_activity_type", "pct_womens_income", "created_at", "updated_at", "created_by", "updated_by"],
        ["feed_labor_division", "unique_identifier", "id_respondent", "id_feed_labor_type", "id_labor_division_group", "created_by", "updated_by", "created_at", "updated_at"],
    ];
}
