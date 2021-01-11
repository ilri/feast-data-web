pool <- dbPool(
  drv      = RMySQL::MySQL(),
  dbname   = "feast_web",
  host     = "localhost",
  username = "root", 
  password = "Feast19@254",
  port     = 3306
)