<?php 
	
	function spartanapp()
	{
		$ci =& get_instance();
		$ci->load->helper('url');
		//$ci->load->database();

		require_once BASEPATH.'../'.APPPATH.'third_party/spartan/controller/my.php';
		$app = my::app();
		$app->model_path = BASEPATH.'../'.APPPATH.'models';

		//$app->connection(site_url(), $ci->database->hostname, $ci->database->database, $ci->database->username, $ci->database->password);
		$app->server(base_url(), 'no-log', 'no-track','no-profile','no-debug','UTC','not',false);
		
		if($ci->config->item('spartan_theme'))
			$app->theme = '../../../'.'views'.$ci->config->item('spartan_theme');
		else
			$app->theme = '../../../'.'views';
		$app->base_tag = base_url().APPPATH.'views';

		$app->forced_templates = true;
		if($ci->router->fetch_method() == 'index')
			$app->forced_template = $ci->router->fetch_class();
		else
			$app->forced_template = $ci->router->fetch_class() . '/' . $ci->router->fetch_method();

		$app->default = $ci->router->routes['default_controller'];

		$app->observe('load_model', NULL, 'ci_load_model');
			
	}

	function ci_load_model()
	{
		$app = my::app();
		$ci =& get_instance();
		$path = $app->loading_model;

		if(strstr($model, '/'))
		{
			$entity = explode('/', $path);
			$model = $entity[1];
		}
		else $model = $path;

		$ci->load->model($path);
		$app->objects[$model] = $ci->$model;

		if(file_exists(BASEPATH.APPPATH.$path."/".$model."_sql.php"))
		{
			include BASEPATH.APPPATH.'models/'.$path."/".$model."_sql.php";
			$app->database->querries[$model] = $querries;
		}
		return true;
	}

?>