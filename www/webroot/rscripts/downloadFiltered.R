library(DBI)
library(pool)
library(dplyr)
library(stringr)
library(jsonlite)

setwd("/var/www/html/webroot/rscripts/")

args <- commandArgs(TRUE)

fileType <- as.character(args[1])
userID_arg <- fromJSON(args[2])
region_arg <- fromJSON(args[3])
project_arg <- fromJSON(args[4])
country_arg <- fromJSON(args[5])
site_arg <- fromJSON(args[6])


#Initialise persist data directory
if(!dir.exists(file.path("/var/www/html/tmp/feast/rdatadownload"))) { 
  dir.create(file.path("/var/www/html/tmp/feast/rdatadownload"))
  } #Check if cache data directory exists and if not, create it.
  
if(!dir.exists(file.path("/var/www/html/tmp/feast/rdatadownload/csv"))) { 
  dir.create(file.path("/var/www/html/tmp/feast/rdatadownload/csv"))
  } #Check if cache data directory exists and if not, create it.
  
persistDIR <- "/var/www/html/tmp/feast/rdatadownload"
csvDIR <- "/var/www/html/tmp/feast/rdatadownload/csv"

pool <- dbPool(
  drv      = RMySQL::MySQL(),
  dbname   = "feast_web",
  host     = "localhost",
  username = "root", 
  password = "Feast19@254",
  port     = 3306
)

start_time <- Sys.time() #Get start time to allow data to load before download

#Data preparation
tabFull <- dbListTables(pool)
tablesExport <- tabFull[grepl("export", tabFull)] # alt: exportTables <- dbGetQuery(pool, "select TABLE_NAME FROM information_schema.tables WHERE substring(TABLE_NAME, 1, 6) = 'export'") #get all expor table names

##Make new export environment
env.export <- new.env()

##Load cached reference
dfeast <- data.frame(tbl(pool, "export_project_site"))
dfeast$uploaded_at <- as.Date(substr(dfeast$uploaded_at, 1, 10), "%Y-%m-%d")
dfeast$site_country_name <- trimws(dfeast$site_country_name)
dfeast$site_name <- trimws(dfeast$site_name)
dfeast$site_name <- str_to_sentence(dfeast$site_name)

userID_file <- userID_arg[1]

if(nrow(dfeast[dfeast$user_id %in% userID_arg,]) == 0){
userID_arg <- dfeast$user_id
}

if(nrow(dfeast[dfeast$project_title %in% project_arg,]) == 0){
project_arg <- dfeast$project_title
}

if(nrow(dfeast[dfeast$site_world_region_name %in% region_arg,]) == 0){
region_arg <- dfeast$site_world_region_name
}

if(nrow(dfeast[dfeast$site_country %in% country_arg,]) == 0){
country_arg <- dfeast$site_country
}

if(nrow(dfeast[dfeast$site_name %in% site_arg,]) == 0){
site_arg <- dfeast$site_name
}

region_arg <- trimws(region_arg)
project_arg <- trimws(project_arg)
country_arg <- trimws(country_arg)
site_arg <- trimws(site_arg)

region_arg <- str_to_sentence(region_arg)
project_arg <- str_to_sentence(project_arg)
country_arg <- str_to_sentence(country_arg)
site_arg <- str_to_sentence(site_arg)




if(file.exists(paste0(persistDIR, "/NewDataCheck.rds"))) {newDataCheck <- readRDS(paste0(persistDIR, "/NewDataCheck.rds"))
} else{newDataCheck <- dfeast}

##Update rdata file if needed
if(file.exists(paste0(persistDIR, "/FEASTdatCache.RDATA")) & identical(newDataCheck$project_title, data.frame(tbl(pool, "export_project_site"))$project_title) & 
identical(newDataCheck$site_name, data.frame(tbl(pool, "export_project_site"))$site_name) & 
identical(newDataCheck$site_lat, data.frame(tbl(pool, "export_project_site"))$site_lat) & !exists("export_project_site")){ #load data only if the file exists and the objects haven't already been imported due to data update

  load(paste0(persistDIR, "/FEASTdatCache.RDATA"))
  ##Prepare data for export and visualisation by putting in a new environment with less verbose table names
  for(i in 1:length(tablesExport)){
	assign(substr(tablesExport[i], 8, nchar(tablesExport[i])), eval(parse(text = tablesExport[i])), envir = env.export) #Add the table to a new environment and remove 'export_' from the start
 }
 
 	if(fileType == "rdata"){
		for(i in 1:length(tablesExport)){ 
			tmpExportTab <- eval(parse(text = tablesExport[i]))
			if("site_country" %in% colnames(eval(parse(text = tablesExport[i])))){ #site_name and site_country is in all. Just in case
				tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_name %in% region_arg & project_title %in% project_arg & site_country %in% country_arg & site_name %in% site_arg) #SI_Site already filtered by date. # 
			}

			if("site_country_name" %in% colnames(eval(parse(text = tablesExport[i])))){ #site_name and site_country is in all. Just in case
				tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_name %in% region_arg & project_title %in% project_arg & site_country_name %in% country_arg & site_name %in% site_arg)  #SI_Site already filtered by date. # 
			}
	
	
		assign(paste0(substr(tablesExport[i], 8, nchar(tablesExport[i])), "Sub"), tmpExportTab, envir = env.export) 		 
	}

	save(list = ls(env.export)[grepl("Sub", ls(env.export))], file = paste0(persistDIR, "/FEASTsub_", userID_file, ".RDATA"), envir = env.export)
	
	} else if(fileType == "csv"){
		for(i in 1:length(tablesExport)) {
		    tmpExportTab <- eval(parse(text = tablesExport[i])) #eval(parse()) to return the object of the same name as the string
		    if("site_country" %in% colnames(eval(parse(text = tablesExport[i])))){ #site_name and site_country is in all. Just in case
				tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_name %in% region_arg & project_title %in% project_arg & site_country %in% country_arg & site_name %in% site_arg) #SI_Site already filtered by date. 
	  		}
	  	
			if("site_country_name" %in% colnames(eval(parse(text = tablesExport[i])))){ #site_name and site_country is in all. Just in case
				tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_name %in% region_arg & project_title %in% project_arg & site_country_name %in% country_arg & site_name %in% site_arg) #SI_Site already filtered by date. 
			}

			write.csv(tmpExportTab, paste0(csvDIR, "/", substr(tablesExport[i], 8, nchar(tablesExport[i])), "_sub_", userID_file, ".csv"))

		}
		
		csvFiles <- list.files(csvDIR, full.names = T)[grep(paste0("_sub_", userID_file, ".csv"), list.files(csvDIR))]
		zip(paste0(persistDIR, "/FEASTsub_", userID_file, ".zip"), csvFiles) #, flags = "-j"
		unlink(csvFiles) #Delete csv files
	}
} else{

#if(!file.exists(paste0(persistDIR, "/FEASTdatCache.RDATA")) | !file.exists(paste0(persistDIR, "/NewDataCheck.rds"))){ #Just in case the RDATA file is deleted but the RDS file is there with equal rows.
		for(i in 1:length(tablesExport)){ #Bring all export tables into R assigning each table as an object
		assign(tablesExport[i], data.frame(tbl(pool, tablesExport[i])))
  ##Exclude data here. Filter out observations with a 1 year embargo. First create a new variable exclDate and then filter
		assign(tablesExport[i], `[[<-`(get(tablesExport[i]), 'exclDate', value = as.POSIXct(eval(parse(text = tablesExport[i]))$uploaded_at) + (365*24*60*60))) 
		assign(tablesExport[i], filter(eval(parse(text = tablesExport[i])), !(exclDate > Sys.time() & excluded == 1) | is.na(excluded)))
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



	saveRDS(data.frame(tbl(pool, "export_project_site")), paste0(persistDIR, "/NewDataCheck.rds"))

	save(list = ls()[grepl("export", ls())], file = paste0(persistDIR, "/FEASTdatCache.RDATA"))
	
	##Prepare data for export and visualisation by putting in a new environment with less verbose table names
	for(i in 1:length(tablesExport)){
		assign(substr(tablesExport[i], 8, nchar(tablesExport[i])), eval(parse(text = tablesExport[i])), envir = env.export) #Add the table to a new environment and remove 'export_' from the start
	
	}
	

	if(fileType == "rdata"){
		for(i in 1:length(tablesExport)){ 
			tmpExportTab <- eval(parse(text = tablesExport[i]))
			if("site_country" %in% colnames(eval(parse(text = tablesExport[i])))){ #site_name and site_country is in all. Just in case
				tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_name %in% region_arg & project_title %in% project_arg & site_country %in% country_arg & site_name %in% site_arg) #SI_Site already filtered by date. # 
			}

			if("site_country_name" %in% colnames(eval(parse(text = tablesExport[i])))){ #site_name and site_country is in all. Just in case
				tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_name %in% region_arg & project_title %in% project_arg & site_country_name %in% country_arg & site_name %in% site_arg)  #SI_Site already filtered by date. # 
			}
	
	
		assign(paste0(substr(tablesExport[i], 8, nchar(tablesExport[i])), "Sub"), tmpExportTab, envir = env.export) 		 
	}

	save(list = ls(env.export)[grepl("Sub", ls(env.export))], file = paste0(persistDIR, "/FEASTsub_", userID_file, ".RDATA"), envir = env.export)
	
	} else if(fileType == "csv"){
		for(i in 1:length(tablesExport)) {
		    tmpExportTab <- eval(parse(text = tablesExport[i])) #eval(parse()) to return the object of the same name as the string
		    if("site_country" %in% colnames(eval(parse(text = tablesExport[i])))){ #site_name and site_country is in all. Just in case
				tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_name %in% region_arg & project_title %in% project_arg & site_country %in% country_arg & site_name %in% site_arg) #SI_Site already filtered by date. 
	  		}
	  	
			if("site_country_name" %in% colnames(eval(parse(text = tablesExport[i])))){ #site_name and site_country is in all. Just in case
				tmpExportTab <- tmpExportTab %>% filter(user_id %in% userID_arg & site_world_region_name %in% region_arg & project_title %in% project_arg & site_country_name %in% country_arg & site_name %in% site_arg) #SI_Site already filtered by date. 
			}

			write.csv(tmpExportTab, paste0(csvDIR, "/", substr(tablesExport[i], 8, nchar(tablesExport[i])), "_sub_", userID_file, ".csv"))

		}
		
		csvFiles <- list.files(csvDIR, full.names = T)[grep(paste0("_sub_", userID_file, ".csv"), list.files(csvDIR))]
		zip(paste0(persistDIR, "/FEASTsub_", userID_file, ".zip"), csvFiles) #, flags = "-j"
		unlink(csvFiles) #Delete csv files
	}
	
}

##Close pool connection
poolClose(pool)


