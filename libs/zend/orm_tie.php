<?php


//Load in Fission ORM to the Zend Framework

//Load in the Database configuration object
require_once APPLICATION_PATH."/../library/Fission/config/database.php";

//Load in the Database adapter interface
require_once APPLICATION_PATH."/../library/Fission/framework/database/interfaces/iadapter.php";

//Load the MySQL adapter
require_once APPLICATION_PATH."/../library/Fission/framework/database/adapters/mysql_pdo.php";

//Load the MySQL model object
require_once APPLICATION_PATH."/../library/Fission/framework/database/model.php";


//Load the ORM objects
require_once APPLICATION_PATH."/../library/Fission/framework/extenders/model/orm.php";
require_once APPLICATION_PATH."/../library/Fission/framework/extenders/model/orm_collection.php";
require_once APPLICATION_PATH."/../library/Fission/libs/rosborne/cfdump.php";



