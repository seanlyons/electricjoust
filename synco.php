<?PHP
include_once 'config.php';

//This is an API call that allows the client to synchronize time.
//Once time is synched, this allows the server to dictate options and slowtime.

$config = getConfig();

if (empty($config)) {
	$error = 'Config is empty.';
	error_log($error);
	echo $error;
	return;
}

$url = "http://www.wordsmith.org/anagram/anagram.cgi?anagram=". $arg ."&t=1000&a=n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$head = curl_exec($ch);
curl_close($ch);

go( $head );

function go ( $input ) {
	$begin = 'found. Displaying all:';
	$end = 'bottomlinks';
	
	$begin = strlen($begin) + strpos($input, $begin);
	$end = strpos($input, $end);

	$words = substr($input, $begin, $end - $begin);
	
	$words = str_replace(array("\n", '<br>','</b>', '<'), array(' '), $words);
	
	$words = explode(' ', $words);
	foreach ($words as $i => $w) {
		if (strlen($w) >= 3) {
			$sufficient[] = $w;
		}
	}
	$sufficient = json_encode(array_unique($sufficient));
error_log('sufficient = ' . $sufficient);
	header('Content-type: application/json');
	echo $sufficient;
}
?>