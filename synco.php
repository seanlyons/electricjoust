<?PHP

$synco = new Synco();
$response = $synco->init();
echo $response;
return;

class Synco {
	public $args = array();
	public $verb = '';
	
	function __construct() {
		$this->args = $this->parseArgs( $_GET );	
	}

	function init() {
		switch($this->args['action']) {
			default:
				throw new Exception('No action undertaken.');
			case 'ts':
				$ts = $this->args['ts'];
				$php_ts = $_SERVER["REQUEST_TIME_FLOAT"] * 1000;
				//Divide by 1000 because microtime = 1e-6 and js Time() = 1e-3.
				$diff = abs($ts - $php_ts) . 'microseconds';
				return $diff;
		}
        
        /************************************************************/
        
        $db = new Db();
        $query = $db->generateQuery('select', 'long_poll_command_queue', NULL, array('client_sid' => $_SESSION['user_sid']));
        $results = $db->performQuery( $query );
        
        
        
        /************************************************************/
	
	
		$response = array('beep' => 'boop');
		$response = $this->args;
		return json_encode($response);
	}


	function parseArgs( $args ) {
		if (!is_array($args) || empty($args)) {
			throw new Exception('Invalid args passed to synco.');
		}
		foreach ($args as $k => $v) {
			$new_args[htmlspecialchars($k)] = htmlspecialchars($v);
		}
		return $new_args;
	}
}