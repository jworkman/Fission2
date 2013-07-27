Fission2
========

PHPFission version 2.0 




Directory Structure


Fission2
	-> framework

		-> database
			-> adapters
				/mysql.php
				/pdo.php
			/model.php

		-> extenders

			-> model
				/orm.php
				/gateway.php
				/migration.php

			-> view
				/view.php
				/partial.php
				/template.php

			-> controller
				/api.php
				/action.php
				/hybrid.php
				/base.php

			/config.php

		-> runtimes
			/console.php
			/plugins.php
			/rod.php
			/tune.php

		-> autoloaders
			/bootstrap.php

		-> core
			/init.php

		-> define
			/functions.php
			/constants.php

		-> security
			/utils.php

		-> objects
			/application.php
			/buffer.php
			/error.php
			/request.php
			/response.php
			/router.php
			/template.php
			/view.php

	-> application
		-> models
		-> views
			/template.php
		-> controllers
		-> routes.php

	-> libs
		-> fission
			-> file.php
			-> date.php
			-> xml.php
			-> json.php
			-> html.php
			-> tag.php
			-> scenario.php
			-> user.php

		-> rosborn
			-> cfdump.php

	-> public
		-> stylesheets
		-> scripts
		-> images
		-> files
		/index.php

	-> config
		/system.php
		/db.php
		/application.php
		/bootstrap.php

	-> database
		-> migrations
	
	-> logs
		/error.log
		/access.log

	-> docs

