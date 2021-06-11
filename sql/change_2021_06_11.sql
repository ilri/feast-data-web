ALTER TABLE spatial_data_focus_group
ADD COLUMN sp_tlu_ha_2010_aw DOUBLE AFTER geo_json ;

ALTER TABLE spatial_data_site
ADD COLUMN sp_livestock_system_id INT AFTER longitude;

ALTER TABLE spatial_data_site
ADD COLUMN sp_livestock_system VARCHAR(255) AFTER sp_livestock_system_id ;


DROP VIEW IF EXISTS export_project_site ;

CREATE  VIEW export_project_site AS select now() AS export_time,project.id_user AS user_id,project.uploaded_at AS uploaded_at,project.keep_private AS private,site.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,project.description AS project_description,project.partner_organization AS project_partner_organization,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,getalias(site.id,'site','major_region',site.major_region) AS site_major_region,world_region.name AS site_world_region,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country_name,community_type.description AS site_community_type,site.grazing_metabolisable_energy AS site_grazing_metabolisable_energy,site.grazing_crude_protein_percentage AS site_grazing_crude_protein_percentage,site.collected_fodder_metabolisable_energy AS site_collected_fodder_metabolisable_energy,site.collected_fodder_crude_protein_percentage AS site_collected_fodder_crude_protein_percentage,getalias(focus_group.id,'focus_group','venue_name',focus_group.venue_name) AS venue_name,focus_group.id AS focus_group_id,(select ifnull(sp.latitude,0) from spatial_data_site sp where (sp.id_site = site.id) order by sp.id desc limit 1) AS site_lat,(select ifnull(sp.longitude,0) from spatial_data_site sp where (sp.id_site = site.id) order by sp.id desc limit 1) AS site_lng,(select count(0) from focus_group f where (f.id_site = site.id)) AS focus_group_count,(select ifnull(sp.updated_at,sp.created_at) from spatial_data_site sp where (sp.id_site = site.id) limit 1) AS sp_site_lastup,(select ifnull(sp.updated_at,sp.created_at) from spatial_data_focus_group sp where (sp.id_focus_group = focus_group.id) limit 1) AS sp_fg_lastup, (select sp.sp_livestock_system from spatial_data_site sp where sp.id_site = site.id limit 1) AS sp_livestock_system from (((((project left join site on((site.id_project = project.id))) left join country on((site.id_country = country.id))) left join world_region on((world_region.id = country.id_world_region))) left join community_type on((site.id_community_type = community_type.id))) left join focus_group on((focus_group.id_site = site.id))) group by site.id;



DROP VIEW IF EXISTS export_focus_group;
CREATE  VIEW export_focus_group AS select now() AS export_time,focus_group.id_user AS user_id,focus_group.uploaded_at AS uploaded_at,focus_group.keep_private AS private,focus_group.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,getalias(project.id,'project','start_date',project.start_date) as project_start_date,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,getalias(site.id,'site','major_region',site.major_region) AS site_major_region,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,focus_group.threshold_large_farm_ha AS focus_group_threshold_large_farm_ha,focus_group.threshold_small_farm_ha AS focus_group_threshold_small_farm_ha,focus_group.percent_households_landless AS focus_group_percent_households_landless,focus_group.percent_households_small AS focus_group_percent_households_small,focus_group.percent_households_medium AS focus_group_percent_households_medium,focus_group.percent_households_large AS focus_group_percent_households_large,focus_group.percent_credit_formal AS focus_group_percent_credit_formal,focus_group.percent_credit_informal AS focus_group_percent_credit_informal,focus_group.household_percent_migrating AS focus_group_household_percent_migrating,focus_group.percent_reproduction_bull AS focus_group_percent_reproduction_bull,focus_group.percent_reproduction_ai AS focus_group_percent_reproduction_ai,focus_group.percent_processing_female AS focus_group_percent_processing_female,focus_group.percent_processing_male AS focus_group_percent_processing_male,focus_group.percent_processing_overall AS focus_group_percent_processing_overall,focus_group.market_avg_distance_km AS focus_group_market_avg_distance_km,focus_group.market_avg_cost_travel AS focus_group_market_avg_cost_travel,focus_group.partner_organization AS focus_group_partner_organization,getalias(focus_group.id,'focus_group','venue_name',focus_group.venue_name) AS focus_group_venue_name,focus_group.community_type AS focus_group_community_type,focus_group.community AS focus_group_community,focus_group.sub_region AS focu_group_sub_region,((ifnull(focus_group.gps_latitude_degrees,0) + (ifnull(focus_group.gps_latitude_minutes,0) / 60)) + (ifnull(focus_group.gps_latitude_seconds,0) / 3600)) AS focus_group_lat,((ifnull(focus_group.gps_longitude_degrees,0) + (ifnull(focus_group.gps_longitude_minutes,0) / 60)) + (ifnull(focus_group.gps_longitude_seconds,0) / 3600)) AS focus_group_lng,(select ifnull(sp.latitude,0) from spatial_data_site sp where (sp.id_site = site.id) limit 1) AS site_lat,(select ifnull(sp.longitude,0) from spatial_data_site sp where (sp.id_site = site.id) limit 1) AS site_lng,(select spfg.loc_json from spatial_data_focus_group spfg where (spfg.id_focus_group = focus_group.id) limit 1) AS loc_json,(select spfg.geo_json from spatial_data_focus_group spfg where (spfg.id_focus_group = focus_group.id) limit 1) AS geo_json, (select spfg.sp_tlu_ha_2010_aw from spatial_data_focus_group spfg where (spfg.id_focus_group = focus_group.id) limit 1) AS sp_tlu_ha_2010_aw from ((((focus_group left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region)));



