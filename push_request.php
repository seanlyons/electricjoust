<?PHP
session_start();
// session_destroy();
// session_start();

error_log('starting!');
$loop = new LPLoop();
$loop->indexAction();

class LPLoop {
    $last_reject = '';

    function indexAction() {
        $this->init();
        for($i = 0; $i < 60 /*ten minutes*/; $i++) {
            activityLoop($i);
        }
        echo json_encode('timeout');
        exit;        
    }
    function init() {
        if (!isset($_SESSION['last_line_seen'])) {
            $_SESSION['last_line_seen'] = 0;
            $_SESSION['id'] = uniqid();
        }
    }

    function activityLoop( $i ) {
        $highest = 0;
        
        error_log('sleeping...' . $_SESSION['id']);
        error_log('last line seen: ' . $_SESSION['last_line_seen']);
        
        // error_log('microtime = ' . microtime());
        if ($i > 0) {
            sleep(10);
        }
        $this->last_reject = '';
        
        error_log('response size = ' . sizeof($response));
        $comm = file(__dir__ . '/response.txt', FILE_IGNORE_NEW_LINES);    

        $response = compareActivityCounts( $comm );
        
        if ($this->last_reject) {
            error_log($this->last_reject);
        }
        error_log("highest : $highest ; i : $i");
        
        if (!empty($response)) {
            //Override to make all clients stop pinging.
            $kill9 = file(__dir__ . '/kill9.txt', FILE_IGNORE_NEW_LINES);
            if ($kill9) {
                $response['kill9'] = TRUE;
            }

            $_SESSION['last_line_seen'] = $highest;
            $r = json_encode($response);
            error_log('responses is set, echoing ' . $r);
            echo $r;
            exit;
        }
        return;
    }

    function compareActivityCounts( $comm ) {
        $response = array();
        foreach ($comm as $l_num => $l_val) {
            $highest = $l_num;
            if ($l_num > $_SESSION['last_line_seen']) {
                $response[] = $l_val;
                error_log("ACCEPT: #$l_num => $l_val");
            } else {
                $this->last_reject = "REJECT: #$l_num => $l_val";
            }
        }
        return $response;
    }
}