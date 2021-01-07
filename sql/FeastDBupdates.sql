DROP TABLE IF EXISTS alias_values;
CREATE TABLE alias_values (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  created_by varchar(255) DEFAULT NULL,
  updated_by varchar(255) DEFAULT NULL,
  tableid int(11) DEFAULT '0',
  table_name varchar(255) DEFAULT NULL,
  actual_column_name varchar(255) DEFAULT NULL,
  alias_value varchar(1024) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY tableid (tableid,table_name(191),actual_column_name(191))
) ;

DROP TABLE IF EXISTS spatial_data_site;
CREATE TABLE spatial_data_site (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  created_by varchar(255) DEFAULT NULL,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_by varchar(255) DEFAULT NULL,
  updated_at datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  id_site int(10) unsigned DEFAULT NULL,
  latitude float NOT NULL DEFAULT '0',
  longitude float NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY id_site (id_site),
  CONSTRAINT spatial_data_site_ibfk_1 FOREIGN KEY (id_site) REFERENCES site (id) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS spatial_data_focus_group;
CREATE TABLE spatial_data_focus_group (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  created_by varchar(255) DEFAULT NULL,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_by varchar(255) DEFAULT NULL,
  updated_at datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  id_focus_group int(10) unsigned DEFAULT NULL,
  loc_json longtext,
  geo_json longtext,
  PRIMARY KEY (id),
  KEY id_focus_group (id_focus_group),
  CONSTRAINT spatial_data_focus_group_ibfk_1 FOREIGN KEY (id_focus_group) REFERENCES focus_group (id) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP VIEW IF EXISTS vw_homestats;
create view vw_homestats as select (select count(distinct id_country) from site)  countries,(select count(*) from site) as sites , (select count(*) from focus_group) as focus_groups,(select round(sum(fodder_crop_cultivation_cultiavted_land_ha),2) from export_fodder_crop_cultivation) as fodder_cultivated;
 
DELIMITER //
 DROP FUNCTION IF EXISTS getalias;
CREATE FUNCTION getalias(tableID int,tableName varchar(255),actualColumn varchar(255),actualValue varchar(255))
RETURNS varchar(1024)
DETERMINISTIC
BEGIN
   DECLARE aliasValue varchar(1024);
   select t.alias_value into aliasValue from alias_values t where t.tableid = tableID and t.table_name = tableName and t.actual_column_name = actualColumn  order by id desc limit 1;
    
   RETURN ifnull(aliasValue,actualValue);
END; //

DELIMITER ;

DELIMITER $$
DROP FUNCTION IF EXISTS cleanUp $$
CREATE FUNCTION cleanUp (str VARCHAR(1024)) RETURNS varchar(1024)
DETERMINISTIC
BEGIN
    set str:= trim(str);
    set str:= ifnull(str,'Unspecified');
    
    set str:= trim(both '`' from str);
    set str:= trim(both '~' from str);
    set str:= trim(both '! ' from str);
    set str:= trim(both '@ ' from str);
    set str:= trim(both '#' from str);
    set str:= trim(both '$' from str);
    set str:= trim(both '%' from str);
    set str:= trim(both '^' from str);
    set str:= trim(both '&' from str);
    set str:= trim(both '*' from str);
    set str:= trim(both '.' from str);
    set str:= trim(both '?' from str);
    set str:= trim(both '_' from str);
    set str:= trim(both '-' from str);
    set str:= trim(both '=' from str);
    set str:= trim(both '+' from str);
    set str:= trim(both '[' from str);
    set str:= trim(both ']' from str);
    set str:= trim(both '{' from str);
    set str:= trim(both '}' from str);
    set str:= trim(both ':' from str);
    set str:= trim(both ';' from str);
    set str:= trim(both '"' from str);
    set str:= trim(both ',' from str); 
    
    set str:= UC_Words(str);
    
    return  str;
END $$
DELIMITER ;

DELIMITER ||  

CREATE FUNCTION UC_Words( str VARCHAR(255) ) RETURNS VARCHAR(255) CHARSET utf8 DETERMINISTIC  
BEGIN  
  DECLARE c CHAR(1);  
  DECLARE s VARCHAR(255);  
  DECLARE i INT DEFAULT 1;  
  DECLARE bool INT DEFAULT 1;  
  DECLARE punct CHAR(17) DEFAULT ' ()[]{},.-_!@;:?/';  
  SET s = LCASE( str );  
  WHILE i < LENGTH( str ) DO  
     BEGIN  
       SET c = SUBSTRING( s, i, 1 );  
       IF LOCATE( c, punct ) > 0 THEN  
        SET bool = 1;  
      ELSEIF bool=1 THEN  
        BEGIN  
          IF c >= 'a' AND c <= 'z' THEN  
             BEGIN  
               SET s = CONCAT(LEFT(s,i-1),UCASE(c),SUBSTRING(s,i+1));  
               SET bool = 0;  
             END;  
           ELSEIF c >= '0' AND c <= '9' THEN  
            SET bool = 0;  
          END IF;  
        END;  
      END IF;  
      SET i = i+1;  
    END;  
  END WHILE;  
  RETURN s;  
END ||  

DELIMITER ; 

DELIMITER $$
DROP TRIGGER IF EXISTS alias_on_site;
CREATE TRIGGER alias_on_site
AFTER INSERT
ON site FOR EACH ROW
BEGIN
    insert into alias_values (tableid,table_name,actual_column_name,alias_value) values(new.id,'site','name',cleanUp(new.name));
	insert into alias_values (tableid,table_name,actual_column_name,alias_value) values(new.id,'site','major_region',cleanUp(new.major_region));
END$$

DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS alias_on_project;
CREATE TRIGGER alias_on_project
AFTER INSERT
ON project FOR EACH ROW
BEGIN
    insert into alias_values (tableid,table_name,actual_column_name,alias_value) values(new.id,'project','title',cleanUp(new.title));
	
END$$

DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS alias_on_focus_group;
CREATE TRIGGER alias_on_focus_group
AFTER INSERT
ON focus_group FOR EACH ROW
BEGIN
    insert into alias_values (tableid,table_name,actual_column_name,alias_value) values(new.id,'focus_group','venue_name',cleanUp(new.venue_name));
	
END$$

DELIMITER ;

insert into alias_values (tableid,table_name,actual_column_name,alias_value) select id,'site','name',cleanUp(name) from site;

insert into alias_values (tableid,table_name,actual_column_name,alias_value) select id,'site','major_region',cleanUp(major_region) from site;

insert into alias_values (tableid,table_name,actual_column_name,alias_value) select id,'project','title',cleanUp(title) from project;

insert into alias_values (tableid,table_name,actual_column_name,alias_value) select id,'focus_group','venue_name',cleanUp(venue_name) from focus_group;


ALTER TABLE agriculture_system_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE animal_category CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE animal_species CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE animal_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE app_registration CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE community_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE consolidation_audit CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE coop_membership CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE core_commodity CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE core_context_attribute CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;
ALTER TABLE core_context_attribute_score CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE core_context_attribute_score_calc_method CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE core_context_attribute_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE country CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE crop_cultivation CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE crop_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE currency CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE decision CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE decision_making_by_household CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE decision_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE feed_labor_division CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE feed_labor_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;
ALTER TABLE feed_source CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE feed_source_availability CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE focus_group CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE focus_group_monthly_statistics CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE fodder_crop_cultivation CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE fodder_crop_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE gender CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE gender_group CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE income_activity CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE income_activity_category CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE income_activity_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE intervention CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;
ALTER TABLE labor_division_group CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE labour_activity CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE landholding_category CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE livestock_holding CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE livestock_sale CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE livestock_sale_category CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE month CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE organization_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE project CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE project_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE purchased_feed CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE purchased_feed_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;
ALTER TABLE resource CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE respondent CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE respondent_monthly_statistics CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE scale_zero_five CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE scale_zero_ten CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE season CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE site CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE system_country CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE system_country_major_region CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE system_setting CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE system_token_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE system_world_region CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;
ALTER TABLE techfit_assessment CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE techfit_scale CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE unit_area CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE unit_mass_weight CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE unit_type CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE user CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE user_approval_status CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE user_gender CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE user_salutation CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE user_token CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE womens_income_activity CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;

ALTER TABLE world_region CHANGE COLUMN updated_at  updated_at datetime ON UPDATE CURRENT_TIMESTAMP,CHANGE COLUMN created_at  created_at datetime default CURRENT_TIMESTAMP;
 

DROP VIEW IF EXISTS export_coop_membership ;

CREATE  VIEW export_coop_membership AS select now() AS export_time,coop_membership.id_user AS user_id,coop_membership.uploaded_at AS uploaded_at,coop_membership.keep_private AS private,coop_membership.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,coop_membership.id AS coop_membership_id,coop_membership.name_free_entry AS coop_membership_name_free_entry,coop_membership.membership_count_male AS coop_membership_membership_count_male,coop_membership.membership_count_female AS coop_membership_membership_count_female from (((((((coop_membership left join respondent on((respondent.id = coop_membership.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region)));

DROP VIEW IF EXISTS export_core_context_attribute_score ;

CREATE  VIEW export_core_context_attribute_score AS select now() AS export_time,core_context_attribute_score.id_user AS user_id,core_context_attribute_score.uploaded_at AS uploaded_at,core_context_attribute_score.keep_private AS private,core_context_attribute_score.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,count(distinct respondent.id) AS respondent_count,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,techfit_scale.number AS techfit_scale_number,core_context_attribute_score.id AS core_context_attribute_score_id,core_context_attribute.id AS core_context_attribute_id,core_context_attribute.prompt AS core_context_attribute_prompt,core_context_attribute_type.id AS core_context_attribute_type_id,core_context_attribute_type.description AS core_context_attribute_type_description from ((((((((((core_context_attribute_score left join techfit_assessment on((techfit_assessment.id = core_context_attribute_score.id_techfit_assessment))) left join focus_group on((focus_group.id = techfit_assessment.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join respondent on((respondent.id_focus_group = focus_group.id))) left join techfit_scale on((techfit_scale.id = core_context_attribute_score.id_techfit_scale))) left join core_context_attribute on((core_context_attribute.id = core_context_attribute_score.id_core_context_attribute))) left join core_context_attribute_type on((core_context_attribute_type.id = core_context_attribute.id_core_context_attribute_type))) group by core_context_attribute_score.id,techfit_assessment.id;


DROP VIEW IF EXISTS export_crop_cultivation ;

CREATE  VIEW export_crop_cultivation AS select now() AS export_time,crop_cultivation.id_user AS user_id,crop_cultivation.uploaded_at AS uploaded_at,crop_cultivation.keep_private AS private,crop_cultivation.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,crop_cultivation.id AS crop_cultivation_id,crop_type.id AS crop_type_id,crop_type.name AS crop_type_name,crop_type.harvest_index AS crop_type_harvest_index,crop_type.content_percent_dry_matter AS crop_type_content_percent_dry_matter,crop_type.content_metabolisable_energy AS crop_type_content_metabolisable_energy,crop_type.content_crude_protein AS crop_type_content_crude_protein,unit_area.id AS unit_area_id,unit_area.name AS unit_area_name,unit_area.conversion_ha AS unit_area_conversion_ha,unit_mass_weight.id AS unit_mass_weight_id,unit_mass_weight.name AS unit_mass_weight_name,unit_mass_weight.conversion_kg AS unit_mass_weight_conversion_kg,crop_cultivation.cultivated_land AS crop_cultivation_cultivated_land,format((crop_cultivation.cultivated_land * unit_area.conversion_ha),5) AS crop_cultivation_cultivated_land_ha,crop_cultivation.annual_yield AS crop_cultivation_annual_yield,coalesce(crop_cultivation.percent_fed,0) AS crop_cultivation_percent_fed,coalesce(crop_cultivation.percent_burned,0) AS crop_cultivation_percent_burned,coalesce(crop_cultivation.percent_mulched,0) AS crop_cultivation_percent_mulched,coalesce(crop_cultivation.percent_sold,0) AS crop_cultivation_percent_sold,coalesce(crop_cultivation.percent_other,0) AS crop_cultivation_percent_other from ((((((((((crop_cultivation left join respondent on((respondent.id = crop_cultivation.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join crop_type on((crop_type.id = crop_cultivation.id_crop_type))) left join unit_area on((unit_area.id = crop_cultivation.id_unit_area))) left join unit_mass_weight on((unit_mass_weight.id = crop_cultivation.id_unit_mass_weight)));


DROP VIEW IF EXISTS export_decision_making_by_household ;

CREATE  VIEW export_decision_making_by_household AS select now() AS export_time,decision_making_by_household.id_user AS user_id,decision_making_by_household.uploaded_at AS uploaded_at,decision_making_by_household.keep_private AS private,decision_making_by_household.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,decision_making_by_household.id AS decision_making_by_household_id,decision.id AS decision_id,decision.description AS decision_description,decision_type.id AS decision_type_id,decision_type.description AS decision_type_description,gender_group.description AS gender_group_description from ((((((((((decision_making_by_household left join respondent on((respondent.id = decision_making_by_household.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join gender_group on((gender_group.id = decision_making_by_household.id_gender_group))) left join decision on((decision.id = decision_making_by_household.id_decision))) left join decision_type on((decision_type.id = decision.id_decision_type)));


DROP VIEW IF EXISTS export_feed_source_availability ;

CREATE  VIEW export_feed_source_availability AS select now() AS export_time,feed_source_availability.id_user AS user_id,feed_source_availability.uploaded_at AS uploaded_at,feed_source_availability.keep_private AS private,feed_source_availability.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,feed_source_availability.id AS feed_source_availability_id,scale_zero_ten.number AS feed_availability,feed_source.id AS feed_source_id,feed_source.description AS feed_source_description,month.id AS month_id,month.name AS month_name,coalesce(feed_source_availability.contribution,0) AS percentage from (((((((((((feed_source_availability left join respondent on((respondent.id = feed_source_availability.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join respondent_monthly_statistics on(((respondent_monthly_statistics.id_respondent = feed_source_availability.id_respondent) and (respondent_monthly_statistics.id_month = feed_source_availability.id_month)))) left join scale_zero_ten on((scale_zero_ten.id = respondent_monthly_statistics.id_scale_zero_ten))) left join month on((month.id = feed_source_availability.id_month))) left join feed_source on((feed_source.id = feed_source_availability.id_feed_source)));


DROP VIEW IF EXISTS export_focus_group;
CREATE  VIEW export_focus_group AS select now() AS export_time,focus_group.id_user AS user_id,focus_group.uploaded_at AS uploaded_at,focus_group.keep_private AS private,focus_group.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,getalias(project.id,'project','start_date',project.start_date) as project_start_date,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,getalias(site.id,'site','major_region',site.major_region) AS site_major_region,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,focus_group.threshold_large_farm_ha AS focus_group_threshold_large_farm_ha,focus_group.threshold_small_farm_ha AS focus_group_threshold_small_farm_ha,focus_group.percent_households_landless AS focus_group_percent_households_landless,focus_group.percent_households_small AS focus_group_percent_households_small,focus_group.percent_households_medium AS focus_group_percent_households_medium,focus_group.percent_households_large AS focus_group_percent_households_large,focus_group.percent_credit_formal AS focus_group_percent_credit_formal,focus_group.percent_credit_informal AS focus_group_percent_credit_informal,focus_group.household_percent_migrating AS focus_group_household_percent_migrating,focus_group.percent_reproduction_bull AS focus_group_percent_reproduction_bull,focus_group.percent_reproduction_ai AS focus_group_percent_reproduction_ai,focus_group.percent_processing_female AS focus_group_percent_processing_female,focus_group.percent_processing_male AS focus_group_percent_processing_male,focus_group.percent_processing_overall AS focus_group_percent_processing_overall,focus_group.market_avg_distance_km AS focus_group_market_avg_distance_km,focus_group.market_avg_cost_travel AS focus_group_market_avg_cost_travel,focus_group.partner_organization AS focus_group_partner_organization,getalias(focus_group.id,'focus_group','venue_name',focus_group.venue_name) AS focus_group_venue_name,focus_group.community_type AS focus_group_community_type,focus_group.community AS focus_group_community,focus_group.sub_region AS focu_group_sub_region,((ifnull(focus_group.gps_latitude_degrees,0) + (ifnull(focus_group.gps_latitude_minutes,0) / 60)) + (ifnull(focus_group.gps_latitude_seconds,0) / 3600)) AS focus_group_lat,((ifnull(focus_group.gps_longitude_degrees,0) + (ifnull(focus_group.gps_longitude_minutes,0) / 60)) + (ifnull(focus_group.gps_longitude_seconds,0) / 3600)) AS focus_group_lng,(select ifnull(sp.latitude,0) from spatial_data_site sp where (sp.id_site = site.id) limit 1) AS site_lat,(select ifnull(sp.longitude,0) from spatial_data_site sp where (sp.id_site = site.id) limit 1) AS site_lng,(select spfg.loc_json from spatial_data_focus_group spfg where (spfg.id_focus_group = focus_group.id) limit 1) AS loc_json,(select spfg.geo_json from spatial_data_focus_group spfg where (spfg.id_focus_group = focus_group.id) limit 1) AS geo_json from ((((focus_group left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region)));



DROP VIEW IF EXISTS export_focus_group_monthly_statistics ;

CREATE  VIEW export_focus_group_monthly_statistics AS select now() AS export_time,focus_group_monthly_statistics.id_user AS user_id,focus_group_monthly_statistics.uploaded_at AS uploaded_at,focus_group_monthly_statistics.keep_private AS private,focus_group_monthly_statistics.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,focus_group_monthly_statistics.id AS focus_group_monthly_statistics_id,month.id AS month_id,month.name AS month_name,season.id AS season_id,season.name AS season_name,scale_zero_five.number AS rainfall from ((((((((focus_group_monthly_statistics left join focus_group on((focus_group.id = focus_group_monthly_statistics.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join month on((month.id = focus_group_monthly_statistics.id_month))) left join season on((season.id = focus_group_monthly_statistics.id_season))) left join scale_zero_five on((scale_zero_five.id = focus_group_monthly_statistics.id_scale_zero_five)));



DROP VIEW IF EXISTS export_fodder_crop_cultivation ;

CREATE  VIEW export_fodder_crop_cultivation AS select now() AS export_time,fodder_crop_cultivation.id_user AS user_id,fodder_crop_cultivation.uploaded_at AS uploaded_at,fodder_crop_cultivation.keep_private AS private,fodder_crop_cultivation.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,currency.id AS currency_id,currency.name AS currency_name,currency.default_usd_exchange_rate AS currency_default_usd_exchange_rate,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,fodder_crop_cultivation.id AS fodder_crop_cultivation_id,fodder_crop_type.id AS fodder_crop_type_id,fodder_crop_type.name AS fodder_crop_type_name,fodder_crop_type.annual_dry_matter_per_hectare AS fodder_crop_type_annual_dry_matter_per_hectare,fodder_crop_type.content_metabolisable_energy AS fodder_crop_type_content_metabolisable_energy,fodder_crop_type.content_crude_protein AS fodder_crop_type_content_crude_protein,unit_area.id AS unit_area_id,unit_area.name AS unit_area_name,unit_area.conversion_ha AS unit_area_conversion_ha,fodder_crop_cultivation.cultivated_land AS fodder_crop_cultivation_cultivated_land,format((fodder_crop_cultivation.cultivated_land * unit_area.conversion_ha),5) AS fodder_crop_cultivation_cultiavted_land_ha from ((((((((((fodder_crop_cultivation left join respondent on((respondent.id = fodder_crop_cultivation.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join currency on((currency.id = site.id_currency))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join fodder_crop_type on((fodder_crop_type.id = fodder_crop_cultivation.id_fodder_crop_type))) left join unit_area on((unit_area.id = fodder_crop_cultivation.id_unit_area)));



DROP VIEW IF EXISTS export_income_activity ;

CREATE  VIEW export_income_activity AS select now() AS export_time,income_activity.id_user AS user_id,income_activity.uploaded_at AS uploaded_at,income_activity.keep_private AS private,income_activity.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,income_activity.id AS income_activity_id,income_activity_type.id AS income_activity_type_id,income_activity_type.description AS income_activity_type_description,income_activity_category.id AS income_activity_category_id,income_activity_category.description AS income_activity_category_description,income_activity.percent_of_hh_income AS income_activity_percent_of_hh_income from (((((((((income_activity left join respondent on((respondent.id = income_activity.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join income_activity_type on((income_activity_type.id = income_activity.id_income_activity_type))) left join income_activity_category on((income_activity_category.id = income_activity_type.id_income_activity_category)));



DROP VIEW IF EXISTS export_labour_activity ;

CREATE  VIEW export_labour_activity AS select now() AS export_time,labour_activity.id_user AS user_id,labour_activity.uploaded_at AS uploaded_at,labour_activity.keep_private AS private,labour_activity.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,currency.id AS currency_id,currency.name AS currency_name,currency.default_usd_exchange_rate AS currency_default_usd_exchange_rate,labour_activity.id AS labour_activity_id,labour_activity.description AS labour_activity_description,labour_activity.daily_rate_female AS labour_activity_daily_rate_female,labour_activity.daily_rate_male AS labour_activity_daily_rate_male from ((((((labour_activity left join focus_group on((focus_group.id = labour_activity.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join currency on((currency.id = site.id_currency)));



DROP VIEW IF EXISTS export_livestock_holding ;

CREATE  VIEW export_livestock_holding AS select now() AS export_time,livestock_holding.id_user AS user_id,livestock_holding.uploaded_at AS uploaded_at,livestock_holding.keep_private AS private,livestock_holding.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,livestock_holding.id AS livestock_holding_id,animal_type.id AS animal_type_id,animal_type.description AS animal_type_description,animal_category.id AS animal_category_id,animal_category.description AS animal_category_description,animal_species.id AS animal_species_id,animal_species.description AS animal_species_description,animal_type.lactating AS animal_type_lactating,animal_type.dairy AS animal_type_dairy,animal_gender.id AS animal_gender_id,animal_gender.description AS animal_gender_description,livestock_holding.dominant_breed AS livestock_holding_dominant_breed,livestock_holding.average_weight AS livestock_holding_average_weight,livestock_holding.headcount AS livestock_holding_headcount from (((((((((((livestock_holding left join respondent on((respondent.id = livestock_holding.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join animal_type on((animal_type.id = livestock_holding.id_animal_type))) left join animal_category on((animal_category.id = animal_type.id_animal_category))) left join animal_species on((animal_species.id = animal_category.id_animal_species))) left join gender animal_gender on((animal_gender.id = animal_type.id_gender)));



DROP VIEW IF EXISTS export_livestock_sale ;

CREATE  VIEW export_livestock_sale AS select now() AS export_time,livestock_sale.id_user AS user_id,livestock_sale.uploaded_at AS uploaded_at,livestock_sale.keep_private AS private,livestock_sale.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,currency.id AS currency_id,currency.name AS currency_name,currency.default_usd_exchange_rate AS currency_default_usd_exchange_rate,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,livestock_sale.id AS livestock_sale_id,livestock_sale_category.id AS livestock_sale_category_id,animal_species.id AS animal_species_id,animal_species.description AS animal_species_description,animal_gender.id AS animal_gender_id,animal_gender.description AS animal_gender_description,livestock_sale.number_sold AS livestock_sale_number_sold,livestock_sale.approximate_weight AS livestock_sale_approximate_weight from (((((((((((livestock_sale left join respondent on((respondent.id = livestock_sale.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join currency on((currency.id = site.id_currency))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join livestock_sale_category on((livestock_sale_category.id = livestock_sale.id_livestock_sale_category))) left join animal_species on((animal_species.id = livestock_sale_category.id_animal_species))) left join gender animal_gender on((animal_gender.id = livestock_sale_category.id_gender)));



DROP TABLE IF EXISTS export_project_site;
CREATE  VIEW export_project_site AS select now() AS export_time,project.id_user AS user_id,project.uploaded_at AS uploaded_at,project.keep_private AS private,project.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,project.description AS project_description,project.partner_organization AS project_partner_organization,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,getalias(site.id,'site','major_region',site.major_region) AS site_major_region,world_region.name AS site_world_region,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country_name,community_type.description AS site_community_type,site.grazing_metabolisable_energy AS site_grazing_metabolisable_energy,site.grazing_crude_protein_percentage AS site_grazing_crude_protein_percentage,site.collected_fodder_metabolisable_energy AS site_collected_fodder_metabolisable_energy,site.collected_fodder_crude_protein_percentage AS site_collected_fodder_crude_protein_percentage,getalias(focus_group.id,'focus_group','venue_name',focus_group.venue_name) AS venue_name,focus_group.id AS focus_group_id,(select ifnull(sp.latitude,0) from spatial_data_site sp where (sp.id_site = site.id) order by sp.id desc limit 1) AS site_lat,(select ifnull(sp.longitude,0) from spatial_data_site sp where (sp.id_site = site.id) order by sp.id desc limit 1) AS site_lng,(select count(0) from focus_group f where (f.id_site = site.id)) AS focus_group_count,(select ifnull(sp.updated_at,sp.created_at) from spatial_data_site sp where (sp.id_site = site.id) limit 1) AS sp_site_lastup,(select ifnull(sp.updated_at,sp.created_at) from spatial_data_focus_group sp where (sp.id_focus_group = focus_group.id) limit 1) AS sp_fg_lastup from (((((project left join site on((site.id_project = project.id))) left join country on((site.id_country = country.id))) left join world_region on((world_region.id = country.id_world_region))) left join community_type on((site.id_community_type = community_type.id))) left join focus_group on((focus_group.id_site = site.id)));



DROP VIEW IF EXISTS export_purchased_feed ;

CREATE  VIEW export_purchased_feed AS select now() AS export_time,purchased_feed.id_user AS user_id,purchased_feed.uploaded_at AS uploaded_at,purchased_feed.keep_private AS private,purchased_feed.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,currency.id AS currency_id,currency.name AS currency_name,currency.default_usd_exchange_rate AS currency_default_usd_exchange_rate,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,purchased_feed.id AS purchased_feed_id,purchased_feed_type.id AS purchased_feed_type_id,purchased_feed_type.name AS purchased_feed_type_name,purchased_feed_type.content_percent_dry_matter AS purchased_feed_type_content_percent_dry_matter,purchased_feed_type.content_metabolisable_energy AS purchased_feed_type_content_metabolisable_energy,purchased_feed_type.content_crude_protein AS purchased_feed_type_content_crude_protein,unit_mass_weight.id AS unit_mass_weight_id,unit_mass_weight.name AS unit_mass_weight_name,unit_mass_weight.conversion_kg AS unit_mass_weight_conversion_kg,purchased_feed.purchases_per_year AS purchased_feed_purchases_per_year,purchased_feed_currency.id AS purchased_feed_currency_id,purchased_feed_currency.name AS purchased_feed_currency_name,purchased_feed_currency.default_usd_exchange_rate AS purchased_feed_currency_default_usd_exchange_rate,purchased_feed.quantity_purchased AS purchased_feed_quantity_purchased,purchased_feed.unit_price AS purchased_feed_unit_price from (((((((((((purchased_feed left join respondent on((respondent.id = purchased_feed.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join currency on((currency.id = site.id_currency))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join purchased_feed_type on((purchased_feed_type.id = purchased_feed.id_purchased_feed_type))) left join unit_mass_weight on((unit_mass_weight.id = purchased_feed.id_unit_mass_weight))) left join currency purchased_feed_currency on((purchased_feed_currency.id = site.id_currency)));



DROP VIEW IF EXISTS export_respondent ;

CREATE  VIEW export_respondent AS select now() AS export_time,respondent.id_user AS user_id,respondent.uploaded_at AS uploaded_at,respondent.keep_private AS private,respondent.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,currency.id AS currency_id,currency.name AS currency_name,currency.default_usd_exchange_rate AS currency_default_usd_exchange_rate,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,respondent.interview_date AS respondent_interview_date,respondent.age AS respondent_age,respondent_gender.id AS respondent_gender_id,respondent_gender.description AS respondent_gender_description,respondent.head_of_household_is_respondent AS respondent_head_of_household_is_respondent,respondent.head_of_household_age AS respondent_head_of_household_age,respondent.head_of_household_occupation AS respondent_head_of_household_occupation,respondent.organization_affiliation AS respondent_organization_affiliation,respondent.community AS respondent_community,community_type.id AS community_type_id,community_type.description AS community_type_description,respondent_country.id AS respondent_country_id,respondent_country.name AS respondent_country_name,landholding_category.id AS landholding_category_id,landholding_category.description AS landholding_category_description,unit_area.id AS unit_area_id,unit_area.name AS unit_area_name,respondent.land_under_cultivation AS respondent_land_under_cultivation,respondent.diet_percent_collected_fodder AS respondent_diet_percent_collected_fodder,respondent.diet_percent_grazing AS respondent_diet_percent_grazing from ((((((((((((respondent left join gender on((gender.id = respondent.id_gender_head_of_household))) left join gender respondent_gender on((respondent_gender.id = respondent.id_gender))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join currency on((currency.id = site.id_currency))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join community_type on((community_type.id = respondent.id_community_type))) left join country respondent_country on((respondent_country.id = respondent.id_country))) left join landholding_category on((landholding_category.id = respondent.id_landholding_category))) left join unit_area on((unit_area.id = respondent.id_unit_area)));



DROP VIEW IF EXISTS export_respondent_monthly_statistics ;

CREATE  VIEW export_respondent_monthly_statistics AS select now() AS export_time,respondent_monthly_statistics.id_user AS user_id,respondent_monthly_statistics.uploaded_at AS uploaded_at,respondent_monthly_statistics.keep_private AS private,respondent_monthly_statistics.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,currency.id AS currency_id,currency.name AS currency_name,currency.default_usd_exchange_rate AS currency_default_usd_exchange_rate,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,respondent_monthly_statistics.id AS respondent_monthly_statistics_id,month.id AS month_id,month.name AS month_name,respondent_monthly_statistics.milk_average_yield AS respondent_monthly_statistics_milk_average_yield,respondent_monthly_statistics.milk_average_price_litre AS respondent_monthly_statistics_milk_average_price_litre,respondent_monthly_statistics.milk_retained_for_household AS respondent_monthly_statistics_milk_retained_for_household,respondent_monthly_statistics.market_price_cattle AS respondent_monthly_statistics_market_price_cattle,respondent_monthly_statistics.market_price_sheep AS respondent_monthly_statistics_market_price_sheep,respondent_monthly_statistics.market_price_goat AS respondent_monthly_statistics_market_price_goat from (((((((((respondent_monthly_statistics left join respondent on((respondent.id = respondent_monthly_statistics.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join currency on((currency.id = site.id_currency))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join month on((month.id = respondent_monthly_statistics.id_month)));



DROP VIEW IF EXISTS export_womens_income_activity ;

CREATE  VIEW export_womens_income_activity AS select now() AS export_time,womens_income_activity.id_user AS user_id,womens_income_activity.uploaded_at AS uploaded_at,womens_income_activity.keep_private AS private,womens_income_activity.exclude AS excluded,project.id AS project_id,getalias(project.id,'project','title',project.title) AS project_title,site.id AS site_id,getalias(site.id,'site','name',site.name) AS site_name,world_region.id AS site_world_region_id,world_region.name AS site_world_region_name,country.id AS site_country_id,country.name AS site_country,focus_group.id AS focus_group_id,respondent.id AS respondent_id,respondent.id_gender_head_of_household AS respondent_head_of_household_gender_id,gender.description AS respondent_head_of_household_gender,focus_group.meeting_date_time AS focus_group_meeting_date_time,focus_group.community AS focus_group_community,focus_group.households_total AS focus_group_households,focus_group.households_average_members AS focus_group_households_average_members,womens_income_activity.id AS womens_income_activity_id,income_activity_type.id AS income_activity_type_id,income_activity_type.description AS income_activity_type_description,income_activity_category.id AS income_activity_category_id,income_activity_category.description AS income_activity_category_description,womens_income_activity.pct_womens_income AS womens_income_activity_pct_womens_income from (((((((((womens_income_activity left join respondent on((respondent.id = womens_income_activity.id_respondent))) left join gender on((gender.id = respondent.id_gender_head_of_household))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join project on((project.id = site.id_project))) left join country on((country.id = site.id_country))) left join world_region on((world_region.id = country.id_world_region))) left join income_activity_type on((income_activity_type.id = womens_income_activity.id_income_activity_type))) left join income_activity_category on((income_activity_category.id = income_activity_type.id_income_activity_category)));



DROP VIEW IF EXISTS feed_source_values_all ;

CREATE  VIEW feed_source_values_all AS select month.ordering AS month_order,month.name AS name_of_month,month.ordering AS order_of_month,feed_source.description AS resource_type,((ifnull(feed_source_availability.contribution,0) * scale_zero_ten.number) * 0.1) AS numerical_value_raw,focus_group.id AS focus_group_id,site.id AS site_id,site.id_country AS country_id,site.id_project AS project_id,country.id_world_region AS world_region_id,respondent.id_user AS user_id from (((((((((respondent join feed_source_availability on((respondent.id = feed_source_availability.id_respondent))) join month on((month.id = feed_source_availability.id_month))) join respondent_monthly_statistics on(((respondent_monthly_statistics.id_respondent = respondent.id) and (respondent_monthly_statistics.id_month = feed_source_availability.id_month)))) join scale_zero_ten on((respondent_monthly_statistics.id_scale_zero_ten = scale_zero_ten.id))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join focus_group_monthly_statistics on((focus_group_monthly_statistics.id_month = month.id))) left join site on((site.id = focus_group.id_site))) left join country on((country.id = site.id_country))) left join feed_source on((feed_source_availability.id_feed_source = feed_source.id))) where (((respondent.exclude is null) or (respondent.exclude = 0)) and ((respondent.keep_private is null) or (respondent.keep_private = 0) or (respondent.uploaded_at < (curdate() - interval 1 year)))) group by respondent.id,month.ordering,feed_source.description order by month.ordering,feed_source.description;



DROP VIEW IF EXISTS feed_source_values_user ;

CREATE  VIEW feed_source_values_user AS select month.ordering AS month_order,month.name AS name_of_month,month.ordering AS order_of_month,feed_source.description AS resource_type,((ifnull(feed_source_availability.contribution,0) * scale_zero_ten.number) * 0.1) AS numerical_value_raw,focus_group.id AS focus_group_id,site.id AS site_id,site.id_country AS country_id,site.id_project AS project_id,country.id_world_region AS world_region_id,respondent.id_user AS user_id from (((((((((respondent join feed_source_availability on((respondent.id = feed_source_availability.id_respondent))) join month on((month.id = feed_source_availability.id_month))) join respondent_monthly_statistics on(((respondent_monthly_statistics.id_respondent = respondent.id) and (respondent_monthly_statistics.id_month = feed_source_availability.id_month)))) join scale_zero_ten on((respondent_monthly_statistics.id_scale_zero_ten = scale_zero_ten.id))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join focus_group_monthly_statistics on((focus_group_monthly_statistics.id_month = month.id))) left join site on((site.id = focus_group.id_site))) left join country on((country.id = site.id_country))) left join feed_source on((feed_source_availability.id_feed_source = feed_source.id))) where ((respondent.exclude is null) or (respondent.exclude = 0)) group by respondent.id,month.ordering,feed_source.description order by month.ordering,feed_source.description;



DROP VIEW IF EXISTS fg_feed_availability ;

CREATE  VIEW fg_feed_availability AS select focus_group.id AS id_focus_group,focus_group.id_site AS id_site,month.ordering AS order_of_month,feed_source.description AS resource_type,avg(((ifnull(feed_source_availability.contribution,0) * scale_zero_ten.number) * 0.1)) AS numerical_value_raw from ((((((((feed_source_availability join respondent on((respondent.id = feed_source_availability.id_respondent))) join month on((month.id = feed_source_availability.id_month))) join respondent_monthly_statistics on((respondent_monthly_statistics.id_respondent = respondent.id))) join scale_zero_ten on((respondent_monthly_statistics.id_scale_zero_ten = scale_zero_ten.id))) left join focus_group on((focus_group.id = respondent.id_focus_group))) left join focus_group_monthly_statistics on((focus_group_monthly_statistics.id_month = month.id))) left join site on((site.id = focus_group.id_site))) left join feed_source on((feed_source_availability.id_feed_source = feed_source.id))) group by focus_group.id,month.id,feed_source.description order by NULL;



DROP VIEW IF EXISTS interview_respondents_all ;

CREATE  VIEW interview_respondents_all AS select count(0) AS number_of,focus_group.id AS focus_group_id,site.id AS site_id,site.id_country AS country_id,site.id_project AS project_id,country.id_world_region AS world_region_id,respondent.id_user AS user_id from (((respondent left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join country on((country.id = site.id_country))) where (((respondent.exclude is null) or (respondent.exclude = 0)) and ((respondent.keep_private is null) or (respondent.keep_private = 0) or (respondent.uploaded_at < (curdate() - interval 1 year)))) group by focus_group.id;



DROP VIEW IF EXISTS interview_respondents_user ;

CREATE  VIEW interview_respondents_user AS select count(0) AS number_of,focus_group.id AS focus_group_id,site.id AS site_id,site.id_country AS country_id,site.id_project AS project_id,country.id_world_region AS world_region_id,respondent.id_user AS user_id from (((respondent left join focus_group on((focus_group.id = respondent.id_focus_group))) left join site on((site.id = focus_group.id_site))) left join country on((country.id = site.id_country))) where ((respondent.exclude is null) or (respondent.exclude = 0)) group by focus_group.id;



DROP VIEW IF EXISTS purchased_feed_stats ;

CREATE  VIEW purchased_feed_stats AS select purchased_feed.id AS purchased_feed_id,purchased_feed.id_respondent AS respondent_id,ifnull(((((purchased_feed.quantity_purchased * unit_mass_weight.conversion_kg) * purchased_feed.purchases_per_year) * purchased_feed_type.content_percent_dry_matter) * 0.01),0) AS purchased_feed_dm,purchased_feed_type.content_metabolisable_energy AS ratio_me,(purchased_feed_type.content_crude_protein * 0.01) AS ratio_cp from ((purchased_feed left join unit_mass_weight on((unit_mass_weight.id = purchased_feed.id_unit_mass_weight))) left join purchased_feed_type on((purchased_feed_type.id = purchased_feed.id_purchased_feed_type)));



DROP VIEW IF EXISTS rainfall ;

CREATE  VIEW rainfall AS select focus_group_monthly_statistics.id_focus_group AS id_focus_group,month.ordering AS order_of_month,'Rainfall' AS resource_type,scale_zero_five.number AS numerical_value_raw from ((focus_group_monthly_statistics left join month on((focus_group_monthly_statistics.id_month = month.id))) left join scale_zero_five on((focus_group_monthly_statistics.id_scale_zero_five = scale_zero_five.id))) where (scale_zero_five.number is not null) group by focus_group_monthly_statistics.id_focus_group,month.ordering order by NULL;


