<!DOCTYPE html> 
<html> 
<head>
	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
  <style type="text/css">
    body {
      font-family: sans-serif;
    }
    #gameover {
        display:none;
        font-size:30px;
        position:relative;
        height:100%;
        width:100%;
        background:red;
    }
  </style>
</head>
<body> 


<div id='game'>
    <button onclick="gameOver(5)" type="button">Game Over</button>
    <!--button id="changespeed" onclick="setPlaySpeed()" type="button">Set audio to be play in slow motion</button-->
    <br> 
    <!--audio id='song' controls>
      <source src="e6.mp3" type="audio/mpeg">
      Not supported on your device or browser.  Sorry. (a)
    </audio-->
    <br/><br/><br/><br/><br/><br/>
    <div id='vibration'></div>
    <div id='doEvent'></div>
    <div id='deltahistory'>nulllll</div>
    <div id='historysum'>0</div>
    <div id='difference'>0</div>
</div>
<div id='gameover'>bye</div>
<script>
    song = document.getElementById("song");
    pbr = 1;
    old = -1;
    delta = 0;
    init();
    deltahistory = [0,0,0,0,0];

    function getPlaySpeed() { 
        alert(song.playbackRate);
    } 

    function setPlaySpeed() {
        if (song.playbackRate == 0.5) { 
            pbr = 1;
            document.getElementById("changespeed").innerHTML = 'Set audio to be played at slow speed.';
        } else {
            pbr = 0.5;
            document.getElementById("changespeed").innerHTML = 'Set audio to be played at regular speed.';
        }
        song.playbackRate = pbr;
    }
    
    function init() {
        // enable vibration support
        navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;
     
        if (navigator.vibrate) {
            <!-- navigator.vibrate(1000); -->
            document.getElementById("vibration").innerHTML = "Yaybration!";
        } else {
            document.getElementById("vibration").innerHTML = "Vibration not supported.";
        }
    
        if (window.DeviceOrientationEvent) {
            document.getElementById("doEvent").innerHTML = "DeviceOrientation";
            // Listen for the deviceorientation event and handle the raw data
            window.addEventListener('deviceorientation', function(eventData) {
                // gamma is the left-to-right tilt in degrees, where right is positive
                var tiltLR = eventData.gamma;
                // beta is the front-to-back tilt in degrees, where front is positive
                var tiltFB = eventData.beta;
                // alpha is the compass direction the device is facing in degrees
                var dir = eventData.alpha
                // call our orientation event handler
                deviceOrientationHandler(tiltLR, tiltFB, dir);
                }
            , false);
        } else {
            document.getElementById("doEvent").innerHTML = "Not supported on your device or browser.  Sorry. (o)"
        }
    }

    function deviceOrientationHandler(tiltLR, tiltFB, dir) {
        current = Math.round(tiltLR) + Math.round(tiltFB) + Math.round(dir);
        historysum = 0;
            
        if (old == -1) {
            old = current;
            delta = 0;
        } else {
            delta = Math.abs(old - current);
            delta_msg = "orientation: was " + old + ", now is " + current + ' >>> ' + delta;
            old = current;

            deltahistory.shift()
            deltahistory.push(delta)
            document.getElementById("deltahistory").innerHTML = deltahistory;
            
            for (i = 0; i <= deltahistory.length; i++) {
                historysum += deltahistory[0];
            }
            document.getElementById("historysum").innerHTML = historysum;
        }
console.log('historysum = ' + historysum);
        if (historysum > 25 && pbr == 1) {
            //document.getElementById("gameover").innerHTML = 'GAMEOVER';
            gameOver(historysum);
        } else {
            document.getElementById("difference").innerHTML = delta_msg;
        }

    }
    // Some other fun rotations to try...
    //var rotation = "rotate3d(0,1,0, "+ (tiltLR*-1)+"deg) rotate3d(1,0,0, "+ (tiltFB*-1)+"deg)";
    //var rotation = "rotate("+ tiltLR +"deg) rotate3d(0,1,0, "+ (tiltLR*-1)+"deg) rotate3d(1,0,0, "+ (tiltFB*-1)+"deg)";
    
    function gameOver(historysum) {
        //Delete game node
        var del = document.getElementById("game");
        del.parentNode.removeChild(del);
        //Display gameover
        document.getElementById("gameover").style.display = 'inherit';
        document.getElementById("gameover").style.background = 'green';
        document.getElementById("gameover").innerHTML = synco();
    }
    
    function synco() {
        return $.ajax({ type: "GET",
            url: "/get_anagram_words.php?" + "acbdefg",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            async: false
        }).responseText;    
    }
</script>
</body> 
</html>

