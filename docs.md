
# Fission Overview #

Documentation for Fission version 2. Here is a quick overview on how the layout of Fission works to save time and bugs.


## Controllers ##

Fission controllers are laid out a bit differently in Fission based on their purpose. This means that if a controllers main purpose is to serve an Application Interface then you need to explicitly specify a return type based on its purpose.

When the router parses the incoming request it parses something called the requested data type. This data types can be the following:

* view - HTML view based responses
* json - JSON Application Interface requests
* xml - XML Application Interface requests
* hybrid - Wildcard responses
* file - File download responses

### Action Based ###

Here is how you would layout your controller if you are using a standard HTML view based response to the client.


	class Controller_User extends \Framework\Extenders\Controller\Action
	{

		public function view_index() {

		}

	}


### API Based ###

To create a controller that implements API standards like rendering out XML, or JSON responses you will specify API return types.

	class Controller_User extends \Framework\Extenders\Controller\Action
	{

		public function json_index() {

		}

		public function xml_index() {

		}

	}

### Hybrid Based ###

Hybrid based actions allow you to do both html and api based responses. You must explicitly declare the response type in the action name. You can specify a hybrid response that tells fission to respond to all requested data types.


	class Controller_User extends \Framework\Extenders\Controller\Action
	{

		/users.json
		public function hybrid_index() {

			//Can return a multiple data type response

		}

	}


### File Responses ###

You can return a file object to download on the client's machine

	class Controller_User extends \Framework\Extenders\Controller\Action
	{

		/users.download
		public function file_index() {

			return $this->response->setFile( $file_path );

		}

	}






### REST Based ###

	class Controller_User extends \Framework\Extenders\Controller\Action {

		public function view_index() {

			return $this->response->setView( User::all( $params["id"] )->getView() );

		}

		public function view_get() {

			return $this->response->setView( User::find( $params["id"] )->getView() );

		}

		public function view_new() {

			return $this->response->setView( User::create()->getView() );

		}

		public function redirect_post() {

			return User::create()->updateFromPost( function(){

				return $this->response->setRedirect( USERS_PATH, "You have successfully created a new user!" );

			}, function() {

				return $this->response->setRedirect( USERS_PATH, "Failed to create user!" );

			} );

		}

		public function redirect_put() {

			return User::find( $params["id"] )->updateFromPost( function() {

				return $this->response->setRedirect( USERS_PATH, "You have successfully updated user!" );

			}, function() {

				return $this->response->setRedirect( USERS_PATH, "Failed to update user!" );				

			} );

		}


		public function redirect_destroy() {

			return User::find( $params["id"] )->destroy( function() {

				return $this->response->setRedirect( USERS_PATH, "You have successfully updated user!" );

			}, function() {

				return $this->response->setRedirect( USERS_PATH, "Failed to remove user!" );				

			} );

		}

	}




## Responding ##

Below is how you use the controllers to respond to the request object. If the request object is not completed by a response an error will be thrown.


### Responses ###


	class Controller_User extends \Framework\Extenders\Controller\Action
	{

		public function hybrid_index() {

			$user = User::find( 1 );

			$this->response->setView( View::get() )
						   ->setJSON( $user->json() )
						   ->setXML( $user->xml() );

		}

	}




## ORM/Models ##


### Reading ###

	//Fetch a result with the id of 1
	$name = User::find( 1 )->full_name

Fetch a result where price is greater than 40

	$items = Items::where("price", 40, ">")->go();

Fetch a result with multiple where conditions

	$items = Items::where("price", 40, ">")->and_where("price", 80, "<")->go();

Fetching JSON from multiple rows

	$users = User::all()->toJSON();


One challenge that most ORMs face is the array data type. Its easy to fetch one object at a time with a single model call to a specific ID, but when we need multiple rows to be returned ORM gets pretty ugly. With Fission we have a specific object called "ModelArray" which its sole purpose is to act as a middle man for serving an array of objects in a safe fassion.

	
	$users = User::all();
	
	//Looping through the model array

	foreach( $users->each as $user ) {
		echo $user->name;
	}

	//Getting the size of a model array

	echo $users->size;

	//Standard loop using the size property

	for( $i = 0; $i < $users->size; $i++ ) {
		echo $users->each[ $i ]->name;
	}

	//Using an iterator for looping

	$users->each( 
		function( $user ) {  
			echo $user->name;
		} 
	);


Custom SQL

	$user = User::sql("SELECT * FROM ".DB_NAME.".users WHERE id = :id", );


### Updating ###

Updating quickly

	User::find( 1 )->data( $attributes );


Updating traditionally

	$user = User::find( 1 );
	$user->full_name = "Bob";
	$user->save();


















