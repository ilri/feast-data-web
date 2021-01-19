#!/usr/local/bin/Rscript

options(warn = -1) #Suppress warnings

library(DBI)
library(pool)
library(dplyr)
library(stringr)
library(jsonlite)
library(openxlsx)

#if(length(grep("sf",installed.packages())) >0 & length(grep("raster",installed.packages())) >0){
#	library(sf)
#	library(raster)
#}
#if(length(grep("exactextractr",installed.packages())) >0 & length(grep("geojsonsf",installed.packages())) >0){
#	library(exactextractr)
#}

setwd("/var/www/html/webroot/rscripts/")

args <- commandArgs(TRUE)

fileType <- as.character(args[1])
userID_arg <- fromJSON(args[2]) #User id is only sent to this arg if requesting own data
region_arg <- fromJSON(args[3])
project_arg <- fromJSON(args[4])
country_arg <- fromJSON(args[5])
site_arg <- fromJSON(args[6])
u2_arg <- fromJSON(args[7]) #User id is always sent to this arg and used to give unique name to subsetted file


#Initialise persist data directory
if(!dir.exists(file.path("/tmp/FEASTpersist/"))) { 
  dir.create(file.path("/tmp/FEASTpersist/"))
  } #Check if cache data directory exists and if not, create it.
  
  if(!dir.exists(file.path("/var/www/html/tmp/feast/rdatadownload"))) { 
  dir.create(file.path("/var/www/html/tmp/feast/rdatadownload"))
  } #Check if cache data directory exists and if not, create it.
  
if(!dir.exists(file.path("/var/www/html/tmp/feast/rdatadownload/csv"))) { 
  dir.create(file.path("/var/www/html/tmp/feast/rdatadownload/csv"))
  } #Check if cache data directory exists and if not, create it.
  
persistDIR <- "/tmp/FEASTpersist"
outDIR <- "/var/www/html/tmp/feast/rdatadownload"
csvDIR <- "/var/www/html/tmp/feast/rdatadownload/csv"

source('/var/www/html/webroot/rscripts/feastCred.R')
#pool <- dbPool(
#  drv      = RMySQL::MySQL(),
#  dbname   = "feast_web",
#  host     = "localhost",
#  username = "root", 
#  password = "",
#  port     = 3306
#)

##Make new export environment
env.export <- new.env()
env.export.sub <- new.env()

##Load reference from pool
dfeast <- data.frame(tbl(pool, "export_project_site"))

#Data preparation
tabFull <- dbListTables(pool)
tablesExport <- tabFull[grepl("export", tabFull)] # alt: exportTables <- dbGetQuery(pool, "select TABLE_NAME FROM information_schema.tables WHERE substring(TABLE_NAME, 1, 6) = 'export'") #get all expor table names
tablesInputDisp <- gsub("export_", "", tablesExport)
tablesInputDisp <- data.frame(tabFull = tablesExport, tabFile = tablesInputDisp, tabLab = gsub("_", " ", tablesInputDisp))
tablesInputDisp$tabLab <- paste0(toupper(substr(tablesInputDisp$tabLab, 1, 1)), substr(tablesInputDisp$tabLab, 2, nchar(tablesInputDisp$tabLab)))

##Prepare file name suffixes	

if(nrow(dfeast[dfeast$user_id %in% userID_arg,]) == 0 & nrow(dfeast[dfeast$project_id %in% project_arg,]) == 0 & nrow(dfeast[dfeast$site_world_region_id %in% region_arg,]) == 0){
	userID_file <- "_full"
	out_file <- "data"

} else{
	userID_file <- paste0("_sub_", u2_arg[1])
	out_file <- paste0("_sub_", u2_arg[1])
}

##Revise arguments
if(nrow(dfeast[dfeast$project_id %in% project_arg,]) == 0){
project_arg <- dfeast$project_id
}

if(nrow(dfeast[dfeast$site_world_region_id %in% region_arg,]) == 0){
region_arg <- dfeast$site_world_region_id[!is.na(dfeast$site_world_region_id)]
}

if(nrow(dfeast[dfeast$site_country_id %in% country_arg,]) == 0){
country_arg <- dfeast$site_country_id[!is.na(dfeast$site_country_id)]
}

if(nrow(dfeast[dfeast$site_id %in% site_arg,]) == 0){
site_arg <- dfeast$site_id[!is.na(dfeast$site_id)]
}

#############
#Prepare user data extract if user ID provided - seperate so none of user's data is keep_private
if(length(userID_arg) >0){

	for(i in 1:length(tablesExport)){ #Bring all export tables into R assigning each table as an object
		assign(tablesExport[i], data.frame(tbl(pool, tablesExport[i])))
  ##Exclude data here. Filter out observations with a 1 year embargo. First create a new variable exclDate and then filter
		#assign(tablesExport[i], `[[<-`(get(tablesExport[i]), 'exclDate', value = as.POSIXct(eval(parse(text = tablesExport[i]))$uploaded_at) + (365*24*60*60))) 
		#assign(tablesExport[i], filter(eval(parse(text = tablesExport[i])), !(exclDate > Sys.time() & !(private %in% c(0, NA))) & excluded %in% c(0, NA)))
		assign(tablesExport[i], filter_at(eval(parse(text = tablesExport[i])), vars(starts_with("site_country")), all_vars(. != "Antarctica ")))
		assign(tablesExport[i], select(eval(parse(text = tablesExport[i])), -excluded, -export_time, -private))

  }
  
  
	export_focus_group$site_country <- trimws(export_focus_group$site_country)
	export_focus_group$farmSizeU1prop <- ifelse(export_focus_group$focus_group_threshold_small_farm_ha <= 1 & export_focus_group$focus_group_threshold_large_farm_ha <=1, rowSums(export_focus_group[, c("focus_group_percent_households_small", "focus_group_percent_households_medium")], na.rm = T), 
                                            export_focus_group$focus_group_percent_households_small)
	
	export_coop_membership$site_country <- trimws(export_coop_membership$site_country)                 
	export_income_activity$site_country <- trimws(export_income_activity$site_country)
	export_core_context_attribute_score$site_country  <- trimws(export_core_context_attribute_score$site_country)   
	export_labour_activity$site_country <- trimws(export_labour_activity$site_country)
	export_crop_cultivation$site_country <- trimws(export_crop_cultivation$site_country)      
	export_livestock_holding$site_country <- trimws(export_livestock_holding$site_country)
	export_decision_making_by_household$site_country <- trimws(export_decision_making_by_household$site_country)   
	export_livestock_sale$site_country <- trimws(export_livestock_sale$site_country)
	export_feed_labor_division$site_country <- trimws(export_feed_labor_division$site_country)            
	export_project_site$site_country <- trimws(export_project_site$site_country_name)
	export_project_site$site_country_name <- trimws(export_project_site$site_country_name) #@Duplicating temporarily
	export_feed_source_availability$site_country <- trimws(export_feed_source_availability$site_country)       
	export_purchased_feed$site_country <- trimws(export_purchased_feed$site_country)
	export_focus_group$site_country <- trimws(export_focus_group$site_country)               
	export_respondent$site_country <- trimws(export_respondent$site_country)
	export_focus_group_monthly_statistics$site_country <- trimws(export_focus_group_monthly_statistics$site_country)  
	export_respondent_monthly_statistics$site_country <- trimws(export_respondent_monthly_statistics$site_country)
	export_fodder_crop_cultivation$site_country <- trimws(export_fodder_crop_cultivation$site_country)        
	export_womens_income_activity$site_country <- trimws(export_womens_income_activity$site_country)

	export_coop_membership$site_name <- str_to_sentence(export_coop_membership$site_name)                 
	export_income_activity$site_name <- str_to_sentence(export_income_activity$site_name)
	export_core_context_attribute_score$site_name  <- str_to_sentence(export_core_context_attribute_score$site_country)   
	export_labour_activity$site_name <- str_to_sentence(export_labour_activity$site_name)
	export_crop_cultivation$site_name <- str_to_sentence(export_crop_cultivation$site_name)      
	export_livestock_holding$site_name <- str_to_sentence(export_livestock_holding$site_name)
	export_decision_making_by_household$site_name <- str_to_sentence(export_decision_making_by_household$site_name)   
	export_livestock_sale$site_name <- str_to_sentence(export_livestock_sale$site_name)
	export_feed_labor_division$site_name <- str_to_sentence(export_feed_labor_division$site_name)            
	export_project_site$site_name <- str_to_sentence(export_project_site$site_name)
	export_feed_source_availability$site_name <- str_to_sentence(export_feed_source_availability$site_name)       
	export_purchased_feed$site_name <- str_to_sentence(export_purchased_feed$site_name)
	export_focus_group$site_name <- str_to_sentence(export_focus_group$site_name)               
	export_respondent$site_name <- str_to_sentence(export_respondent$site_name)
	export_focus_group_monthly_statistics$site_name <- str_to_sentence(export_focus_group_monthly_statistics$site_name)  
	export_respondent_monthly_statistics$site_name <- str_to_sentence(export_respondent_monthly_statistics$site_name)
	export_fodder_crop_cultivation$site_name <- str_to_sentence(export_fodder_crop_cultivation$site_name)        
	export_womens_income_activity$site_name <- str_to_sentence(export_womens_income_activity$site_name)

	export_coop_membership$site_name <- trimws(export_coop_membership$site_name)                 
	export_income_activity$site_name <- trimws(export_income_activity$site_name)
	export_core_context_attribute_score$site_name  <- trimws(export_core_context_attribute_score$site_country)   
	export_labour_activity$site_name <- trimws(export_labour_activity$site_name)
	export_crop_cultivation$site_name <- trimws(export_crop_cultivation$site_name)      
	export_livestock_holding$site_name <- trimws(export_livestock_holding$site_name)
	export_decision_making_by_household$site_name <- trimws(export_decision_making_by_household$site_name)   
	export_livestock_sale$site_name <- trimws(export_livestock_sale$site_name)
	export_feed_labor_division$site_name <- trimws(export_feed_labor_division$site_name)            
	export_project_site$site_name <- trimws(export_project_site$site_name)
	export_feed_source_availability$site_name <- trimws(export_feed_source_availability$site_name)       
	export_purchased_feed$site_name <- trimws(export_purchased_feed$site_name)
	export_focus_group$site_name <- trimws(export_focus_group$site_name)               
	export_respondent$site_name <- trimws(export_respondent$site_name)
	export_focus_group_monthly_statistics$site_name <- trimws(export_focus_group_monthly_statistics$site_name)  
	export_respondent_monthly_statistics$site_name <- trimws(export_respondent_monthly_statistics$site_name)
	export_fodder_crop_cultivation$site_name <- trimws(export_fodder_crop_cultivation$site_name)        
	export_womens_income_activity$site_name <- trimws(export_womens_income_activity$site_name)

	export_feed_source_availability <- export_feed_source_availability[!is.na(export_feed_source_availability$feed_source_description),]
	export_feed_source_availability <- export_feed_source_availability[export_feed_source_availability$feed_source_description != "GRAZING",]

	export_feed_source_availability$feed_source_description <- str_to_sentence(export_feed_source_availability$feed_source_description)
	export_feed_source_availability$feed_source_description <- gsub("(?<=[\\s])\\s*|^\\s+|\\s+$", "", export_feed_source_availability$feed_source_description, perl=TRUE) #Remove more than one space in a row
	export_feed_source_availability$feed_source_description <- trimws(export_feed_source_availability$feed_source_description)
	export_feed_source_availability$feed_source_description <- gsub(",([A-Za-z])", ", \\1", export_feed_source_availability$feed_source_description) #Add space to cases where a character follows a comma
	export_feed_source_availability$feed_source_description <- gsub("Collected fooder", "Collected fodder", export_feed_source_availability$feed_source_description) #Add space to cases where a character follows a comma
	export_feed_source_availability$feed_source_description <- gsub("Collect fodder", "Collected fodder", export_feed_source_availability$feed_source_description) #Add space to cases where a character 

	export_livestock_holding$site_country <- trimws(export_livestock_holding$site_country)
	export_livestock_holding$livestock_holding_dominant_breed <- str_to_sentence(export_livestock_holding$livestock_holding_dominant_breed)
	export_livestock_holding$livestock_holding_dominant_breed <- gsub("(?<=[\\s])\\s*|^\\s+|\\s+$", "", export_livestock_holding$livestock_holding_dominant_breed, perl=TRUE) #Remove more than one space in a row
	export_livestock_holding$livestock_holding_dominant_breed <- trimws(export_livestock_holding$livestock_holding_dominant_breed)
	export_livestock_holding$livestock_holding_dominant_breed <- gsub(",([A-Za-z])", ", \\1", export_livestock_holding$livestock_holding_dominant_breed) #Add space to cases where a character follows a comma
	export_livestock_holding$livestock_holding_dominant_breed <- gsub("Collected fooder", "Collected fodder", export_livestock_holding$livestock_holding_dominant_breed) #Add space to cases where a character follows a comma
	export_livestock_holding$livestock_holding_dominant_breed <- gsub("Collect fodder", "Collected fodder", export_livestock_holding$livestock_holding_dominant_breed) #Add space to cases where a character follows a comma

	export_fodder_crop_cultivation$fodderName <- gsub("\\s*\\([^\\)]+\\)", "", export_fodder_crop_cultivation$fodder_crop_type_name)
	export_fodder_crop_cultivation$fodder_crop_cultivation_cultiavted_land_ha <- as.numeric(export_fodder_crop_cultivation$fodder_crop_cultivation_cultiavted_land_ha)

	
	###Add spatial data to project_site and focus_group
		library(sf)
		library(raster)
		library(rgdal)
	
		if(file.exists('/var/www/html/webroot/rscripts/spatial/glps_gleam_61113_10km.tif') & file.exists('/var/www/html/webroot/rscripts/spatial/glps_gleam_61113_10km.tif')){
			glps <- raster('/var/www/html/webroot/rscripts/spatial/glps_gleam_61113_10km.tif')
			
			tlu <- raster('/var/www/html/webroot/rscripts/spatial/TLU_2010_Aw_ha.tif')
			
			glpsLegend <- data.frame(sp_livestock_system_id = 1:15, sp_livestock_system = c("Livestock only systems HyperArid", "Livestock only systems Arid", "Livestock only systems Humid", "Livestock only systems Temperate (and Tropical Highlands)", "Mixed rainfed HyperArid", "Mixed rainfed Arid", "Mixed rainfed Humid", "Mixed rainfed Temperate (and Tropical Highlands)", "Mixed irrigated HyperArid", "Mixed irrigated Arid", "Mixed irrigated Humid", "Mixed irrigated Temperate (and Tropical Highlands)", "Urban areas", "Other_Tree based systems", "Unsuitable"))
		
			export_focus_group$sp_livestock_system_id <- NA
			export_focus_group$sp_TLU_ha_2010_Aw <- NA
			for(i in 1:nrow(export_focus_group)){
				if(!is.na(export_focus_group$geo_json[i]) & substr(export_focus_group$geo_json[i], 1, 10) == '{\"type\":\"F'){
					if(st_is_valid(st_read(export_focus_group$geo_json[i], quiet = T)) == TRUE & !is.na(st_is_valid(st_read(export_focus_group$geo_json[i], quiet = T)))){
						
							s1 <- st_read(export_focus_group$geo_json[i], quiet = T)
						
							export_focus_group$sp_TLU_ha_2010_Aw[i] <- round(as.numeric(extract(tlu, s1, fun = mean)), 2)
							
							export_focus_group$sp_livestock_system_id[i] <- as.numeric(extract(glps, s1, fun = max))
							
							export_focus_group <- left_join(export_focus_group, glpsLegend)
				
					}}
			
				}
		
		}
		detach("package:sf", unload=TRUE)
		detach("package:raster", unload=TRUE)
		detach("package:rgdal", unload=TRUE)


	
	##Prepare data for export and visualisation by putting in a new environment with less verbose table names
	for(i in 1:length(tablesExport)){
		assign(substr(tablesExport[i], 8, nchar(tablesExport[i])), eval(parse(text = tablesExport[i])), envir = env.export) #Add the table to a new environment and remove 'export_' from the start
	
	}
	

	if(fileType == "rdata"){
		for(i in 1:length(tablesExport)){ 
			tmpExportTab <- eval(parse(text = tablesExport[i]))
			tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_id %in% region_arg & project_id %in% project_arg & site_country_id %in% country_arg & site_id %in% site_arg) 

			assign(paste0(substr(tablesExport[i], 8, nchar(tablesExport[i]))), tmpExportTab, envir = env.export.sub) 		 
		}

	save(list = ls(env.export.sub), file = paste0(outDIR, "/FEAST", out_file, ".RDATA"), envir = env.export.sub)
	
	} else if(fileType == "csv"){
		for(i in 1:length(tablesExport)) {
		    tmpExportTab <- eval(parse(text = tablesExport[i])) #eval(parse()) to return the object of the same name as the string

			tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_id %in% region_arg & project_id %in% project_arg & site_country_id %in% country_arg & site_id %in% site_arg)

			write.csv(tmpExportTab, paste0(csvDIR, "/", substr(tablesExport[i], 8, nchar(tablesExport[i])), userID_file, ".csv"))
		}
		
		csvFiles <- list.files(csvDIR, full.names = T)[grep(paste0(userID_file, ".csv"), list.files(csvDIR))]
		zip(paste0(outDIR, "/FEAST", out_file, ".zip"), csvFiles, flags = "-j") #, flags = "-j" - remove file paths
		unlink(csvFiles) #Delete csv files
	} else if(fileType == "xlsx") {
		wb = createWorkbook()
	    for(i in 1:length(tablesExport)) {#! Fix disjointed tables Export and tabLab. Bring into one DF and use consistently throughout
			    tmpExportTab <- eval(parse(text = tablesExport[i])) #eval(parse()) to return the object of the same name as the string

	   		  	tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_id %in% region_arg & project_id %in% project_arg & site_country_id %in% country_arg & site_id %in% site_arg) 

				sheet = addWorksheet(wb, tablesInputDisp$tabLab[i])
				writeData(wb, sheet=sheet, tmpExportTab) 
			}
		
		saveWorkbook(wb, paste0(outDIR, "/FEAST", out_file, ".xlsx"), overwrite = T)

		}
	
}



#########
#Other non-private data use cases






##Prepare data for export - first check cache and if needed, update data
if(file.exists(paste0(persistDIR, "/NewDataCheck.rds")) & length(userID_arg)  == 0) {newDataCheck <- readRDS(paste0(persistDIR, "/NewDataCheck.rds"))
} else{newDataCheck <- dfeast}

##Update rdata file if needed
if(file.exists(paste0(persistDIR, "/FEASTdatCache.RDATA")) & length(userID_arg) == 0 & identical(newDataCheck$project_title, data.frame(tbl(pool, "export_project_site"))$project_title) & 
identical(newDataCheck$site_name, data.frame(tbl(pool, "export_project_site"))$site_name) & 
identical(newDataCheck$sp_site_lastup, data.frame(tbl(pool, "export_project_site"))$sp_site_lastup) & identical(newDataCheck$sp_fg_lastup, data.frame(tbl(pool, "export_project_site"))$sp_fg_lastup) & !exists("export_project_site")){ #load data only if the file exists and the objects haven't already been imported due to data update

  load(paste0(persistDIR, "/FEASTdatCache.RDATA"))
  ##Prepare data for export and visualisation by putting in a new environment with less verbose table names
  for(i in 1:length(tablesExport)){
	assign(substr(tablesExport[i], 8, nchar(tablesExport[i])), eval(parse(text = tablesExport[i])), envir = env.export) #Add the table to a new environment and remove 'export_' from the start
 }
 
 	if(fileType == "rdata"){
		for(i in 1:length(tablesExport)){ 
			tmpExportTab <- eval(parse(text = tablesExport[i]))
			tmpExportTab <- tmpExportTab %>% filter(site_world_region_id %in% region_arg & project_id %in% project_arg & site_country_id %in% country_arg & site_id %in% site_arg) 
			
		assign(paste0(substr(tablesExport[i], 8, nchar(tablesExport[i]))), tmpExportTab, envir = env.export.sub) 		 
	}

	save(list = ls(env.export.sub), file = paste0(outDIR, "/FEAST", out_file, ".RDATA"), envir = env.export.sub)
	
	} else if(fileType == "csv"){
		for(i in 1:length(tablesExport)) {
		    tmpExportTab <- eval(parse(text = tablesExport[i])) #eval(parse()) to return the object of the same name as the string
		    tmpExportTab <- tmpExportTab %>% filter(site_world_region_id %in% region_arg & project_id %in% project_arg & site_country_id %in% country_arg & site_id %in% site_arg) 

			write.csv(tmpExportTab, paste0(csvDIR, "/", substr(tablesExport[i], 8, nchar(tablesExport[i])), userID_file, ".csv"))

		}
		
		csvFiles <- list.files(csvDIR, full.names = T)[grep(paste0(userID_file, ".csv"), list.files(csvDIR))]
		zip(paste0(outDIR, "/FEAST", out_file, ".zip"), csvFiles, flags = "-j") #, flags = "-j"
		unlink(csvFiles) #Delete csv files
		
	} else if(fileType == "xlsx") {
		wb = createWorkbook()
	    for(i in 1:length(tablesExport)) {#! Fix disjointed tables Export and tabLab. Bring into one DF and use consistently throughout
			    tmpExportTab <- eval(parse(text = tablesExport[i])) #eval(parse()) to return the object of the same name as the string

	   		  	tmpExportTab <- tmpExportTab %>% filter(site_world_region_id %in% region_arg & project_id %in% project_arg & site_country_id %in% country_arg & site_id %in% site_arg) 

			sheet = addWorksheet(wb, tablesInputDisp$tabLab[i])
			writeData(wb, sheet=sheet, tmpExportTab) 
			}
		
		saveWorkbook(wb, paste0(outDIR, "/FEAST", out_file, ".xlsx"), overwrite = T)

		}
} else if(length(userID_arg) == 0){

#if(!file.exists(paste0(persistDIR, "/FEASTdatCache.RDATA")) | !file.exists(paste0(persistDIR, "/NewDataCheck.rds"))){ #Just in case the RDATA file is deleted but the RDS file is there with equal rows.
		for(i in 1:length(tablesExport)){ #Bring all export tables into R assigning each table as an object
		assign(tablesExport[i], data.frame(tbl(pool, tablesExport[i])))
  ##Exclude data here. Filter out observations with a 1 year embargo. First create a new variable exclDate and then filter
		assign(tablesExport[i], `[[<-`(get(tablesExport[i]), 'exclDate', value = as.POSIXct(eval(parse(text = tablesExport[i]))$uploaded_at) + (365*24*60*60))) 
		assign(tablesExport[i], filter(eval(parse(text = tablesExport[i])), !(exclDate > Sys.time() & !(private %in% c(0, NA))) & excluded %in% c(0, NA)))
		assign(tablesExport[i], filter_at(eval(parse(text = tablesExport[i])), vars(starts_with("site_country")), all_vars(. != "Antarctica ")))
		assign(tablesExport[i], select(eval(parse(text = tablesExport[i])), -exclDate, -excluded, -export_time, -private))

  }
  
  
	export_focus_group$site_country <- trimws(export_focus_group$site_country)
	export_focus_group$farmSizeU1prop <- ifelse(export_focus_group$focus_group_threshold_small_farm_ha <= 1 & export_focus_group$focus_group_threshold_large_farm_ha <=1, rowSums(export_focus_group[, c("focus_group_percent_households_small", "focus_group_percent_households_medium")], na.rm = T), 
                                            export_focus_group$focus_group_percent_households_small)
	
	export_coop_membership$site_country <- trimws(export_coop_membership$site_country)                 
	export_income_activity$site_country <- trimws(export_income_activity$site_country)
	export_core_context_attribute_score$site_country  <- trimws(export_core_context_attribute_score$site_country)   
	export_labour_activity$site_country <- trimws(export_labour_activity$site_country)
	export_crop_cultivation$site_country <- trimws(export_crop_cultivation$site_country)      
	export_livestock_holding$site_country <- trimws(export_livestock_holding$site_country)
	export_decision_making_by_household$site_country <- trimws(export_decision_making_by_household$site_country)   
	export_livestock_sale$site_country <- trimws(export_livestock_sale$site_country)
	export_feed_labor_division$site_country <- trimws(export_feed_labor_division$site_country)            
	export_project_site$site_country <- trimws(export_project_site$site_country_name)
	export_project_site$site_country_name <- trimws(export_project_site$site_country_name) #@Duplicating temporarily
	export_feed_source_availability$site_country <- trimws(export_feed_source_availability$site_country)       
	export_purchased_feed$site_country <- trimws(export_purchased_feed$site_country)
	export_focus_group$site_country <- trimws(export_focus_group$site_country)               
	export_respondent$site_country <- trimws(export_respondent$site_country)
	export_focus_group_monthly_statistics$site_country <- trimws(export_focus_group_monthly_statistics$site_country)  
	export_respondent_monthly_statistics$site_country <- trimws(export_respondent_monthly_statistics$site_country)
	export_fodder_crop_cultivation$site_country <- trimws(export_fodder_crop_cultivation$site_country)        
	export_womens_income_activity$site_country <- trimws(export_womens_income_activity$site_country)

	export_coop_membership$site_name <- str_to_sentence(export_coop_membership$site_name)                 
	export_income_activity$site_name <- str_to_sentence(export_income_activity$site_name)
	export_core_context_attribute_score$site_name  <- str_to_sentence(export_core_context_attribute_score$site_country)   
	export_labour_activity$site_name <- str_to_sentence(export_labour_activity$site_name)
	export_crop_cultivation$site_name <- str_to_sentence(export_crop_cultivation$site_name)      
	export_livestock_holding$site_name <- str_to_sentence(export_livestock_holding$site_name)
	export_decision_making_by_household$site_name <- str_to_sentence(export_decision_making_by_household$site_name)   
	export_livestock_sale$site_name <- str_to_sentence(export_livestock_sale$site_name)
	export_feed_labor_division$site_name <- str_to_sentence(export_feed_labor_division$site_name)            
	export_project_site$site_name <- str_to_sentence(export_project_site$site_name)
	export_feed_source_availability$site_name <- str_to_sentence(export_feed_source_availability$site_name)       
	export_purchased_feed$site_name <- str_to_sentence(export_purchased_feed$site_name)
	export_focus_group$site_name <- str_to_sentence(export_focus_group$site_name)               
	export_respondent$site_name <- str_to_sentence(export_respondent$site_name)
	export_focus_group_monthly_statistics$site_name <- str_to_sentence(export_focus_group_monthly_statistics$site_name)  
	export_respondent_monthly_statistics$site_name <- str_to_sentence(export_respondent_monthly_statistics$site_name)
	export_fodder_crop_cultivation$site_name <- str_to_sentence(export_fodder_crop_cultivation$site_name)        
	export_womens_income_activity$site_name <- str_to_sentence(export_womens_income_activity$site_name)

	export_coop_membership$site_name <- trimws(export_coop_membership$site_name)                 
	export_income_activity$site_name <- trimws(export_income_activity$site_name)
	export_core_context_attribute_score$site_name  <- trimws(export_core_context_attribute_score$site_country)   
	export_labour_activity$site_name <- trimws(export_labour_activity$site_name)
	export_crop_cultivation$site_name <- trimws(export_crop_cultivation$site_name)      
	export_livestock_holding$site_name <- trimws(export_livestock_holding$site_name)
	export_decision_making_by_household$site_name <- trimws(export_decision_making_by_household$site_name)   
	export_livestock_sale$site_name <- trimws(export_livestock_sale$site_name)
	export_feed_labor_division$site_name <- trimws(export_feed_labor_division$site_name)            
	export_project_site$site_name <- trimws(export_project_site$site_name)
	export_feed_source_availability$site_name <- trimws(export_feed_source_availability$site_name)       
	export_purchased_feed$site_name <- trimws(export_purchased_feed$site_name)
	export_focus_group$site_name <- trimws(export_focus_group$site_name)               
	export_respondent$site_name <- trimws(export_respondent$site_name)
	export_focus_group_monthly_statistics$site_name <- trimws(export_focus_group_monthly_statistics$site_name)  
	export_respondent_monthly_statistics$site_name <- trimws(export_respondent_monthly_statistics$site_name)
	export_fodder_crop_cultivation$site_name <- trimws(export_fodder_crop_cultivation$site_name)        
	export_womens_income_activity$site_name <- trimws(export_womens_income_activity$site_name)

	export_feed_source_availability <- export_feed_source_availability[!is.na(export_feed_source_availability$feed_source_description),]
	export_feed_source_availability <- export_feed_source_availability[export_feed_source_availability$feed_source_description != "GRAZING",]

	export_feed_source_availability$feed_source_description <- str_to_sentence(export_feed_source_availability$feed_source_description)
	export_feed_source_availability$feed_source_description <- gsub("(?<=[\\s])\\s*|^\\s+|\\s+$", "", export_feed_source_availability$feed_source_description, perl=TRUE) #Remove more than one space in a row
	export_feed_source_availability$feed_source_description <- trimws(export_feed_source_availability$feed_source_description)
	export_feed_source_availability$feed_source_description <- gsub(",([A-Za-z])", ", \\1", export_feed_source_availability$feed_source_description) #Add space to cases where a character follows a comma
	export_feed_source_availability$feed_source_description <- gsub("Collected fooder", "Collected fodder", export_feed_source_availability$feed_source_description) #Add space to cases where a character follows a comma
	export_feed_source_availability$feed_source_description <- gsub("Collect fodder", "Collected fodder", export_feed_source_availability$feed_source_description) #Add space to cases where a character 

	export_livestock_holding$site_country <- trimws(export_livestock_holding$site_country)
	export_livestock_holding$livestock_holding_dominant_breed <- str_to_sentence(export_livestock_holding$livestock_holding_dominant_breed)
	export_livestock_holding$livestock_holding_dominant_breed <- gsub("(?<=[\\s])\\s*|^\\s+|\\s+$", "", export_livestock_holding$livestock_holding_dominant_breed, perl=TRUE) #Remove more than one space in a row
	export_livestock_holding$livestock_holding_dominant_breed <- trimws(export_livestock_holding$livestock_holding_dominant_breed)
	export_livestock_holding$livestock_holding_dominant_breed <- gsub(",([A-Za-z])", ", \\1", export_livestock_holding$livestock_holding_dominant_breed) #Add space to cases where a character follows a comma
	export_livestock_holding$livestock_holding_dominant_breed <- gsub("Collected fooder", "Collected fodder", export_livestock_holding$livestock_holding_dominant_breed) #Add space to cases where a character follows a comma
	export_livestock_holding$livestock_holding_dominant_breed <- gsub("Collect fodder", "Collected fodder", export_livestock_holding$livestock_holding_dominant_breed) #Add space to cases where a character follows a comma

	export_fodder_crop_cultivation$fodderName <- gsub("\\s*\\([^\\)]+\\)", "", export_fodder_crop_cultivation$fodder_crop_type_name)
	export_fodder_crop_cultivation$fodder_crop_cultivation_cultiavted_land_ha <- as.numeric(export_fodder_crop_cultivation$fodder_crop_cultivation_cultiavted_land_ha)

	
	###Add spatial data to project_site and focus_group
		library(sf)
		library(raster)
		library(rgdal)
	
		if(file.exists('/var/www/html/webroot/rscripts/spatial/glps_gleam_61113_10km.tif') & file.exists('/var/www/html/webroot/rscripts/spatial/glps_gleam_61113_10km.tif')){
			glps <- raster('/var/www/html/webroot/rscripts/spatial/glps_gleam_61113_10km.tif')
			
			tlu <- raster('/var/www/html/webroot/rscripts/spatial/TLU_2010_Aw_ha.tif')
			
			glpsLegend <- data.frame(sp_livestock_system_id = 1:15, sp_livestock_system = c("Livestock only systems HyperArid", "Livestock only systems Arid", "Livestock only systems Humid", "Livestock only systems Temperate (and Tropical Highlands)", "Mixed rainfed HyperArid", "Mixed rainfed Arid", "Mixed rainfed Humid", "Mixed rainfed Temperate (and Tropical Highlands)", "Mixed irrigated HyperArid", "Mixed irrigated Arid", "Mixed irrigated Humid", "Mixed irrigated Temperate (and Tropical Highlands)", "Urban areas", "Other_Tree based systems", "Unsuitable"))
		
			export_focus_group$sp_livestock_system_id <- NA
			export_focus_group$sp_TLU_ha_2010_Aw <- NA
			for(i in 1:nrow(export_focus_group)){
				if(!is.na(export_focus_group$geo_json[i]) & substr(export_focus_group$geo_json[i], 1, 10) == '{\"type\":\"F'){
					if(st_is_valid(st_read(export_focus_group$geo_json[i], quiet = T)) == TRUE & !is.na(st_is_valid(st_read(export_focus_group$geo_json[i], quiet = T)))){
						
							s1 <- st_read(export_focus_group$geo_json[i], quiet = T)
							
							export_focus_group$sp_TLU_ha_2010_Aw[i] <- round(as.numeric(extract(tlu, s1, fun = mean)), 2)
							
							export_focus_group$sp_livestock_system_id[i] <- as.numeric(extract(glps, s1, fun = max))
							
							export_focus_group <- left_join(export_focus_group, glpsLegend)
				
					}}
			
				}
		
		}
		detach("package:sf", unload=TRUE)
		detach("package:raster", unload=TRUE)
		detach("package:rgdal", unload=TRUE)
	

	saveRDS(data.frame(tbl(pool, "export_project_site")), paste0(persistDIR, "/NewDataCheck.rds"))

	save(list = ls()[grepl("export", ls())], file = paste0(persistDIR, "/FEASTdatCache.RDATA"))
	
	##Prepare data for export and visualisation by putting in a new environment with less verbose table names
	for(i in 1:length(tablesExport)){
		assign(substr(tablesExport[i], 8, nchar(tablesExport[i])), eval(parse(text = tablesExport[i])), envir = env.export) #Add the table to a new environment and remove 'export_' from the start
	
	}
	

	if(fileType == "rdata"){
		for(i in 1:length(tablesExport)){ 
			tmpExportTab <- eval(parse(text = tablesExport[i]))
			tmpExportTab <- tmpExportTab %>% filter(site_world_region_id %in% region_arg & project_id %in% project_arg & site_country_id %in% country_arg & site_id %in% site_arg) 

			assign(paste0(substr(tablesExport[i], 8, nchar(tablesExport[i]))), tmpExportTab, envir = env.export.sub) 		 
		}

	save(list = ls(env.export.sub), file = paste0(outDIR, "/FEAST", out_file, ".RDATA"), envir = env.export.sub)
	
	} else if(fileType == "csv"){
		for(i in 1:length(tablesExport)) {
		    tmpExportTab <- eval(parse(text = tablesExport[i])) #eval(parse()) to return the object of the same name as the string

			tmpExportTab <- tmpExportTab %>% filter(site_world_region_id %in% region_arg & project_id %in% project_arg & site_country_id %in% country_arg & site_id %in% site_arg)

			write.csv(tmpExportTab, paste0(csvDIR, "/", substr(tablesExport[i], 8, nchar(tablesExport[i])), userID_file, ".csv"))
		}
		
		csvFiles <- list.files(csvDIR, full.names = T)[grep(paste0(userID_file, ".csv"), list.files(csvDIR))]
		zip(paste0(outDIR, "/FEAST", out_file, ".zip"), csvFiles, flags = "-j") #, flags = "-j" - remove file paths
		unlink(csvFiles) #Delete csv files
	} else if(fileType == "xlsx") {
		wb = createWorkbook()
	    for(i in 1:length(tablesExport)) {#! Fix disjointed tables Export and tabLab. Bring into one DF and use consistently throughout
			    tmpExportTab <- eval(parse(text = tablesExport[i])) #eval(parse()) to return the object of the same name as the string

	   		  	tmpExportTab <- tmpExportTab %>% filter(site_world_region_id %in% region_arg & project_id %in% project_arg & site_country_id %in% country_arg & site_id %in% site_arg) 

				sheet = addWorksheet(wb, tablesInputDisp$tabLab[i])
				writeData(wb, sheet=sheet, tmpExportTab) 
			}
		
		saveWorkbook(wb, paste0(outDIR, "/FEAST", out_file, ".xlsx"), overwrite = T)

		}
	
}

##Close pool connection
poolClose(pool)


