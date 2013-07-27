<?php

//Directory structure paths
define('ROOT'       , '../');
define('FRAMEWORK'  , 'framework/');
define('DEFINE', 'define/');
define('DATABASE', 'database/');
define('CORE', 'core/');
define('AUTOLOADERS', 'autoloaders/');
define('EXTENDERS', 'extenders/');
define('OBJECTS', 'objects/');
define('RUNTIMES', 'runtimes/');
define('SECURITY', 'security/');
define('APPLICATION', 'application/');
define('CONFIG', 'config/');
define('CONTROLLERS', 'controllers/');
define('CONTROLLER', 'controller/');
define('MODEL', 'model/');
define('VIEW', 'view/');
define('MODELS', 'models/');
define('VIEWS', 'views/');
define('LIBS', 'libs/');
define('MODEL', 'model/');
define('VIEW', 'view/');


//Common Paths
define('PUBLIC_PATH', ROOT.'public');
define('ASSET_PATH'  , PUBLIC_PATH."assets");
define('FRAMEWORK_PATH'  , ROOT.'framework');
define('LIBRARY_PATH'  , ROOT.LIBS);
define('APPLICATION_PATH'  , ROOT.APPLICATION);
define('MODELS_PATH'  , ROOT.APPLICATION.MODELS);
define('VIEWS_PATH'  , ROOT.APPLICATION.VIEWS);
define('CONTROLLERS_PATH'  , ROOT.APPLICATION.CONTROLLERS);
define('CONFIG_PATH'  , ROOT.CONFIG);

define('APPLICATION_ENV', getenv("APPLICATION_ENV"));


define('NUMERIC', 0);
define('NON_NUMERIC', 1);
define('REGEXPR', 3);
define('MULTIPLE', 4);

define('PROPERTY_FILTER_NOT_PUBLIC', 'PROPERTY_FILTER_NOT_PUBLIC');