
/*!50001 DROP TABLE IF EXISTS `export_project_site`*/;
/*!50001 DROP VIEW IF EXISTS `export_project_site`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `export_project_site` AS select current_timestamp() AS `export_time`,`project`.`id_user` AS `user_id`,`project`.`uploaded_at` AS `uploaded_at`,`project`.`keep_private` AS `private`,`site`.`exclude` AS `excluded`,`project`.`id` AS `project_id`,`getalias`(`project`.`id`,'project','title',`project`.`title`) AS `project_title`,`project`.`description` AS `project_description`,`project`.`partner_organization` AS `project_partner_organization`,`site`.`id` AS `site_id`,`getalias`(`site`.`id`,'site','name',`site`.`name`) AS `site_name`,`getalias`(`site`.`id`,'site','major_region',`site`.`major_region`) AS `site_major_region`,`world_region`.`name` AS `site_world_region`,`world_region`.`id` AS `site_world_region_id`,`world_region`.`name` AS `site_world_region_name`,`country`.`id` AS `site_country_id`,`country`.`name` AS `site_country_name`,`community_type`.`description` AS `site_community_type`,`site`.`grazing_metabolisable_energy` AS `site_grazing_metabolisable_energy`,`site`.`grazing_crude_protein_percentage` AS `site_grazing_crude_protein_percentage`,`site`.`collected_fodder_metabolisable_energy` AS `site_collected_fodder_metabolisable_energy`,`site`.`collected_fodder_crude_protein_percentage` AS `site_collected_fodder_crude_protein_percentage`,`getalias`(`focus_group`.`id`,'focus_group','venue_name',`focus_group`.`venue_name`) AS `venue_name`,`focus_group`.`id` AS `focus_group_id`,(select ifnull(`sp`.`latitude`,0) from `spatial_data_site` `sp` where `sp`.`id_site` = `site`.`id` order by `sp`.`id` desc limit 1) AS `site_lat`,(select ifnull(`sp`.`longitude`,0) from `spatial_data_site` `sp` where `sp`.`id_site` = `site`.`id` order by `sp`.`id` desc limit 1) AS `site_lng`,(select count(0) from `focus_group` `f` where `f`.`id_site` = `site`.`id`) AS `focus_group_count`,(select ifnull(`sp`.`updated_at`,`sp`.`created_at`) from `spatial_data_site` `sp` where `sp`.`id_site` = `site`.`id` limit 1) AS `sp_site_lastup`,(select ifnull(`sp`.`updated_at`,`sp`.`created_at`) from `spatial_data_focus_group` `sp` where `sp`.`id_focus_group` = `focus_group`.`id` limit 1) AS `sp_fg_lastup` from (((((`project` left join `site` on(`site`.`id_project` = `project`.`id`)) left join `country` on(`site`.`id_country` = `country`.`id`)) left join `world_region` on(`world_region`.`id` = `country`.`id_world_region`)) left join `community_type` on(`site`.`id_community_type` = `community_type`.`id`)) left join `focus_group` on(`focus_group`.`id_site` = `site`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
