#Valid usage:
#sudo Rscript /srv/shiny-server/feastSpatial2MySQL.R 5 '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[-6.500082492828369,37.56523513793945],[-6.5000595074878875,37.565215757407714],[-6.5000944851795,37.56521609268384],[-6.500098482629818,37.56524157366948], [-6.500082492828369,37.56523513793945]]]},"properties":{"id":"283"}} '


library(DBI)
library(pool)
library(dplyr)
library(sf)
library(raster)
library(rgdal)
#library(jsonlite)

source('/srv/shiny-server/feastCred.R')

#sp_id = 5
#geo_json <- '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[-6.500082492828369,37.56523513793945],[-6.5000595074878875,37.565215757407714],[-6.5000944851795,37.56521609268384],[-6.500098482629818,37.56524157366948], [-6.500082492828369,37.56523513793945]]]},"properties":{"id":"283"}} '

args <- commandArgs(TRUE)
sp_id <- as.numeric(args[1])
#geo_lat <- as.character(args[2]) 
#geo_lon <- as.character(args[3])

#dat_geo <- data.frame(lat = geo_lat, lon = geo_lon)

dat_geo <- data.frame(tbl(pool, "spatial_data_site")) %>% subset(id_site == sp_id) %>% dplyr::select(c("longitude", "latitude"))
s1 <- st_as_sf(dat_geo, coords = c("longitude", "latitude"), crs = 4326, agr = "constant")
#s1 <- st_buffer(s1, 1)

if(file.exists('/home/sfra/Documents/glps_gleam_61113_10km.tif') & file.exists('/home/sfra/Documents/TLU_2010_Aw_ha.tif')){	
#if(file.exists('/var/www/html/webroot/rscripts/spatial/glps_gleam_61113_10km.tif') & file.exists('/var/www/html/webroot/rscripts/spatial/glps_gleam_61113_10km.tif')){
		glps <- raster('/home/sfra/Documents/glps_gleam_61113_10km.tif')
		#tlu <- raster('/home/sfra/Documents/TLU_2010_Aw_ha.tif')
		#glps <- raster('/var/www/html/webroot/rscripts/spatial/glps_gleam_61113_10km.tif')	
		#tlu <- raster('/var/www/html/webroot/rscripts/spatial/TLU_2010_Aw_ha.tif')
			
		glpsLegend <- data.frame(sp_livestock_system_id = 1:15, sp_livestock_system = c("Livestock only systems HyperArid", "Livestock only systems Arid", "Livestock only systems Humid", "Livestock only systems Temperate (and Tropical Highlands)", "Mixed rainfed HyperArid", "Mixed rainfed Arid", "Mixed rainfed Humid", "Mixed rainfed Temperate (and Tropical Highlands)", "Mixed irrigated HyperArid", "Mixed irrigated Arid", "Mixed irrigated Humid", "Mixed irrigated Temperate (and Tropical Highlands)", "Urban areas", "Other_Tree based systems", "Unsuitable"))
		
			
		tmp_livestock_system <- as.numeric(extract(glps, s1, fun = max))
					
			if(!is.na(tmp_livestock_system)){
				dbExecute(pool, paste0("UPDATE spatial_data_site ", "SET sp_livestock_system_id = \'", tmp_livestock_system, "\', sp_livestock_system = \'", glpsLegend$sp_livestock_system[glpsLegend$sp_livestock_system_id == tmp_livestock_system], "\' WHERE id_site = \'", sp_id, "\'"))	
				}	
					
		
		}
#detach("package:sf", unload=TRUE)
#detach("package:raster", unload=TRUE)
#detach("package:rgdal", unload=TRUE)    

poolClose(pool)
     
