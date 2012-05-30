<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="Page-Enter" content="blendTrans(Duration=1.0)">
    <title>Key Map</title>
    
      </style>
    <link rel="apple-touch-startup-image" sizes="640x960" href="/images/startup_x2.png"/>
   <link rel="stylesheet" media="all" href="iphone_p.css">
   <link rel="apple-touch-icon-precomposed" href="/images/icon.png" />
   
   
<!-- script to disable zooming on input field **START**-->
 <script type="text/javascript">$("input[type=text], textarea").mouseover(zoomDisable).mousedown(zoomDisable);
 function zoomDisable(){
   $('head meta[name=viewport]').remove();
   $('head').prepend('<meta name="viewport" content="user-scalable=0" />');
 }
 function zoomEnable(){
   $('head meta[name=viewport]').remove();
   $('head').prepend('<meta name="viewport" content="user-scalable=1" />');
 }</script>
 <!-- script to disable zooming on input field **END**-->
 
 <!--script to keep in web app below-->
 <!--<script src="" type="text/javascript">
 var a=document.getElementsByTagName("a");
 for(var i=0;i<a.length;i++) {
     if(!a[i].onclick && a[i].getAttribute("target") != "_blank") {
         a[i].onclick=function() {
                 window.location=this.getAttribute("href");
                 return false; 
         }
     }
 }
 </script>-->
 <!--script to keep in web app above-->
<!-- script for css intro-->
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>thanks &middot; Made with Sencha Animator</title>
<script type="text/javascript">
if (typeof(AN) === 'undefined') {
   AN = {}; 
}
AN.Controller = {
    
    scenes: {},
    scenesArray: [],
    currentSceneID: -1,
    olElement: null,
    clickEvents: {},
    useOrmma: false,
    
    setConfig: function(configData) {
        
        this.clickEvents = configData.clickEvents

        this.olElement = document.getElementById(configData.parentId);
        var liElements = this.olElement.children;
        
        if (configData.ormma) {
            this.useOrmma = true;
        }
        
        var scene;
        for (var i=0; i < configData.scenes.length; i++) {
            scene = configData.scenes[i];
            scene.element = liElements[i];
            this.scenes[scene.id] = scene;
            this.scenesArray.push(scene);
        }
        
        this.setupListeners();
        
        this.startSceneByID(this.scenesArray[0].id);
        
        
        
    },
    
    
    runningAnimationCount: 0,
    browser: 'webkit',
    
    setupListeners: function() {
        var me = this;
        
        var eventName = "webkitAnimationEnd";

        if (document.body.style.MozAnimationName !== undefined) {
            eventName = "animationend";
            this.browser = "moz";
        }

        this.olElement.addEventListener(eventName, function() {
            me.onAnimationEnd();
        },false);
        
        function addMousemoveListenerTo(scene) {
            scene.element.addEventListener('mousemove', function(event){
                scene.mousemoveAction(me, event);
            }, false);
        }
        
        var scene;
        for (var i=0; i < this.scenesArray.length; i++) {
            scene = this.scenesArray[i];
            if (scene.mousemoveAction) {
                
                addMousemoveListenerTo(scene);
            }
        }
        
        function addListenerTo(element, event, aFunction) {
            element.addEventListener(event, function(event){
                aFunction(me,event);
            }, false);
        }
        
        //add click events
        var element, clickEvent;
        for (var i=0; i < this.clickEvents.length; i++) {
            clickEvent = this.clickEvents[i];
            element = document.getElementById(clickEvent.id);
            addListenerTo(element, 'click', clickEvent.handler);
        }
        
    },
    
    
    onAnimationEnd: function() {
        
        this.runningAnimationCount--;
        
        if (this.runningAnimationCount === 0) {
            this.onSceneFinish();
        }
        
    },
    
    startSceneByID: function(sceneID) {

        var me = this;

        //restart current scene without flicker
        if (sceneID === this.currentSceneID) {
            this.scenes[sceneID].element.setAttribute('class','run restart');
            
            setTimeout(function(){

                me.runningAnimationCount = me.scenes[sceneID].animationCount;
                me.scenes[sceneID].element.setAttribute('class','run');
                
                if (me.scenes[sceneID].startAction) {
                    me.scenes[sceneID].startAction(me);
                }
                if (me.scenes[sceneID].animationCount === 0 ) {
                    me.onSceneFinish();
                }
                
                },0);
            return;
        } else if (this.currentSceneID !== -1) {
            this.scenes[this.currentSceneID].element.setAttribute('class','');
        }

        this.runningAnimationCount = this.scenes[sceneID].animationCount;
        
        this.currentSceneID = sceneID;
        var nextScene = this.scenes[sceneID];
        
        if (this.browser === 'moz') {
            nextScene.element.setAttribute('class','run restart');
            var unused = nextScene.element.offsetHeight;
            nextScene.element.setAttribute('class','run');    
        } else {
            nextScene.element.setAttribute('class','run');
        }
        
        if (this.useOrmma) {
            
           this.ormmaNextScene(nextScene);
        }
        
        
        if (nextScene.startAction) {
            nextScene.startAction(this);
        }
        if (nextScene.animationCount === 0 ) {
            this.onSceneFinish();
        }


    },
    
    replayScene: function() {
        this.startSceneByID(this.currentSceneID);
    },
    
    onSceneFinish: function() {
        
        if (this.scenes[this.currentSceneID].endAction) {
            this.scenes[this.currentSceneID].endAction(this);
        }
        
    },
    
    goToNextScene: function() {
        var nextIndex = this.scenesArray.indexOf(this.scenes[this.currentSceneID]) + 1;
        var nextScene;
        if (nextScene = this.scenesArray[nextIndex]) {
            this.startSceneByID(nextScene.id);
        }
    },
    
    goToURL: function(aURL) {
        document.location.href = aURL;
    },
    
    ormmaNextScene: function(nextScene) {
        var currentState = ormma.getState();
            
        if (nextScene.dimensions.expanded) {
            //expanded state
            //check if we're expanded
            var maxSize = ormma.getMaxSize()
            if (currentState !== 'expanded') {
                ormma.expand({
                    x:0,
                    y:0,
                    width: maxSize.width,
                    height: maxSize.height
                })
            }
            
            var transform = "";
            var elementHeight = nextScene.element.offsetHeight;
            var elementWidth = nextScene.element.offsetWidth;
            var y = (maxSize.height - elementHeight) / 2;
            var x = (maxSize.width - elementWidth) / 2;
            transform += " translate3d("+Math.round(x)+"px,"+Math.round(y)+"px,0)";   
            
            
            if (nextScene.dimensions.fit) {
                var scaleFactor = Math.min(maxSize.width/elementWidth, maxSize.height/elementHeight);                    
                transform += " scale3d("+scaleFactor+","+scaleFactor+",1)";
            }
            nextScene.element.style.webkitTransform = transform;
            
        } else {
            
            if (currentState === 'expanded') {
                ormma.close();
            }
            ormma.resize(nextScene.dimensions.width,nextScene.dimensions.height);
        }
            
        
    }

}

window.addEventListener('load', function(){
    var configData = {
        parentId: 'AN-sObj-parentOl',
        ormma: false,
        scenes: [{id: 0,animationCount: 10,duration: 4577.922077922078,dimensions: {height: 248,width: 300,expanded: false,fit: false}}],
        clickEvents: [{id: "AN-sObj-27",handler: function(controller) {
controller.goToURL('iphone.html');
}}]
    };
    AN.Controller.setConfig(configData);
}, false);
</script>

<style type="text/css">

.AN-sObj-stage {
    position: relative;
    overflow: hidden;
}

.AN-sObj-stage div {
    position: absolute;
}

.AN-sObj-stage a {
    color: inherit;
    text-decoration: none;
}

.AN-sObj-stage * {
    margin: 0;
    padding: 0;
    -webkit-font-smoothing: antialiased;
}

.AN-sObj-stage img {
    position: absolute;
    top: 0;
    left: 0;
}

body,.AN-sObj-stage,ol,li {
    margin: 0;
    padding: 0;
}

ol {
    list-style: none;
    position: relative;
}

li {
    position: absolute;
    top: 0;
    left: 0;
}

.AN-Scene-Description {
    display: none;
}

@-webkit-keyframes AN-ani-delay {
    0% {
    }

    100% {
    }
}

li {
    display: none;
}

li.run {
    display: block;
}

.restart * {
    -webkit-animation-name: none !important;
}

#AN-sObj-1 {
    -webkit-transform: translate3d(29px, 43px, 0px);
    width: 42px;
    height: 57px;
    top: 0;
    left: 0;
    color: rgba(255,255,255,1);
    font-size: 61px;
    font-weight: bold;
    text-shadow: rgba(75,75,75,0.5) -5px 3px 0px;
}

#AN-sObj-12 {
    -webkit-transform: translate3d(178px, 124px, 0px);
    width: 42px;
    height: 57px;
    top: 0;
    left: 0;
    color: rgba(255,255,255,1);
    font-size: 61px;
    font-weight: bold;
    text-shadow: rgba(75,75,75,0.5) -5px 3px 0px;
    -webkit-transform-origin: 50% 50%;
}

#AN-sObj-13 {
    -webkit-transform: translate3d(124px, 124px, 0px);
    width: 42px;
    height: 57px;
    top: 0;
    left: 0;
    color: rgba(255,255,255,1);
    font-size: 61px;
    font-weight: bold;
    text-shadow: rgba(75,75,75,0.5) -5px 3px 0px;
}

#AN-sObj-14 {
    -webkit-transform: translate3d(80px, 124px, 0px);
    width: 42px;
    height: 57px;
    top: 0;
    left: 0;
    color: rgba(255,255,255,1);
    font-size: 61px;
    font-weight: bold;
    text-shadow: rgba(75,75,75,0.5) -5px 3px 0px;
}

#AN-sObj-15 {
    -webkit-transform: translate3d(228px, 43px, 0px);
    width: 42px;
    height: 57px;
    top: 0;
    left: 0;
    color: rgba(255,255,255,1);
    font-size: 61px;
    font-weight: bold;
    text-shadow: rgba(75,75,75,0.5) -5px 3px 0px;
}

#AN-sObj-16 {
    -webkit-transform: translate3d(181px, 43px, 0px);
    width: 42px;
    height: 57px;
    top: 0;
    left: 0;
    color: rgba(255,255,255,1);
    font-size: 61px;
    font-weight: bold;
    text-shadow: rgba(75,75,75,0.5) -5px 3px 0px;
}

#AN-sObj-17 {
    -webkit-transform: translate3d(127px, 43px, 0px);
    width: 42px;
    height: 57px;
    top: 0;
    left: 0;
    color: rgba(255,255,255,1);
    font-size: 61px;
    font-weight: bold;
    text-shadow: rgba(75,75,75,0.5) -5px 3px 0px;
}

#AN-sObj-18 {
    -webkit-transform: translate3d(73px, 43px, 0px);
    width: 42px;
    height: 57px;
    top: 0;
    left: 0;
    color: rgba(255,255,255,1);
    font-size: 61px;
    font-weight: bold;
    text-shadow: rgba(75,75,75,0.5) -5px 3px 0px;
}

#AN-sObj-27 {
    -webkit-transform: translate3d(19px, 289px, 0px);
    width: 192px;
    height: 27px;
    top: 0;
    left: 0;
    color: rgba(255,255,255,1);
    font-weight: bold;
    text-shadow: rgba(65,63,63,0.5) -5px 1px 3px;
    opacity: 0;
    -webkit-transform-origin: 74.48% 31.48%;
}

#AN-sObj-29 {
    -webkit-transform: translate3d(92px, -30px, 0px) scale3d(0.16, 0.16, 1);
    width: 340px;
    height: 340px;
    top: 0;
    left: 0;
    -webkit-transform-origin: 48.24% 53.53%;
}

#AN-sObj-scene-0 .AN-sObj-stage {
    height: 248px;
    width: 300px;
    background-color: rgba(255,255,255,0);
}



@-webkit-keyframes AN-ani-19 {
    0% {
        -webkit-transform: translate3d(-10px, -70px, 0px);
        -webkit-animation-timing-function: linear;
    }

    100% {
        -webkit-transform: translate3d(73px, 43px, 0px);
        -webkit-animation-timing-function: ease;
    }
}

.run #AN-sObj-18 {
    -webkit-animation-name: AN-ani-19;
    -webkit-animation-duration: 3.1233766233766236s;
    -webkit-animation-delay: 0s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-18 {
    -webkit-transform: translate3d(73px, 43px, 0px);
    -webkit-animation-timing-function: ease;
}

.restart #AN-sObj-18 {
    -webkit-transform: translate3d(-10px, -70px, 0px);
    -webkit-animation-timing-function: linear;
}

@-webkit-keyframes AN-ani-20 {
    0% {
        -webkit-transform: translate3d(94px, -58px, 0px);
        -webkit-animation-timing-function: linear;
    }

    100% {
        -webkit-transform: translate3d(127px, 43px, 0px);
        -webkit-animation-timing-function: ease;
    }
}

.run #AN-sObj-17 {
    -webkit-animation-name: AN-ani-20;
    -webkit-animation-duration: 3.1103896103896105s;
    -webkit-animation-delay: 0s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-17 {
    -webkit-transform: translate3d(127px, 43px, 0px);
    -webkit-animation-timing-function: ease;
}

.restart #AN-sObj-17 {
    -webkit-transform: translate3d(94px, -58px, 0px);
    -webkit-animation-timing-function: linear;
}

@-webkit-keyframes AN-ani-21 {
    0% {
        -webkit-transform: translate3d(186px, -70px, 0px);
        -webkit-animation-timing-function: linear;
    }

    100% {
        -webkit-transform: translate3d(181px, 43px, 0px);
        -webkit-animation-timing-function: ease;
    }
}

.run #AN-sObj-16 {
    -webkit-animation-name: AN-ani-21;
    -webkit-animation-duration: 3.1103896103896105s;
    -webkit-animation-delay: 0s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-16 {
    -webkit-transform: translate3d(181px, 43px, 0px);
    -webkit-animation-timing-function: ease;
}

.restart #AN-sObj-16 {
    -webkit-transform: translate3d(186px, -70px, 0px);
    -webkit-animation-timing-function: linear;
}

@-webkit-keyframes AN-ani-22 {
    0% {
        -webkit-transform: translate3d(314px, -29px, 0px);
        -webkit-animation-timing-function: linear;
    }

    100% {
        -webkit-transform: translate3d(228px, 43px, 0px);
        -webkit-animation-timing-function: ease;
    }
}

.run #AN-sObj-15 {
    -webkit-animation-name: AN-ani-22;
    -webkit-animation-duration: 3.1103896103896105s;
    -webkit-animation-delay: 0s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-15 {
    -webkit-transform: translate3d(228px, 43px, 0px);
    -webkit-animation-timing-function: ease;
}

.restart #AN-sObj-15 {
    -webkit-transform: translate3d(314px, -29px, 0px);
    -webkit-animation-timing-function: linear;
}

@-webkit-keyframes AN-ani-23 {
    0% {
        -webkit-transform: translate3d(-72px, 184px, 0px);
        -webkit-animation-timing-function: linear;
    }

    100% {
        -webkit-transform: translate3d(80px, 124px, 0px);
        -webkit-animation-timing-function: ease;
    }
}

.run #AN-sObj-14 {
    -webkit-animation-name: AN-ani-23;
    -webkit-animation-duration: 3.097402597402598s;
    -webkit-animation-delay: 0s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-14 {
    -webkit-transform: translate3d(80px, 124px, 0px);
    -webkit-animation-timing-function: ease;
}

.restart #AN-sObj-14 {
    -webkit-transform: translate3d(-72px, 184px, 0px);
    -webkit-animation-timing-function: linear;
}

@-webkit-keyframes AN-ani-24 {
    0% {
        -webkit-transform: translate3d(314px, 203px, 0px);
        -webkit-animation-timing-function: linear;
    }

    100% {
        -webkit-transform: translate3d(124px, 124px, 0px);
        -webkit-animation-timing-function: ease;
    }
}

.run #AN-sObj-13 {
    -webkit-animation-name: AN-ani-24;
    -webkit-animation-duration: 3.097402597402598s;
    -webkit-animation-delay: 0s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-13 {
    -webkit-transform: translate3d(124px, 124px, 0px);
    -webkit-animation-timing-function: ease;
}

.restart #AN-sObj-13 {
    -webkit-transform: translate3d(314px, 203px, 0px);
    -webkit-animation-timing-function: linear;
}

@-webkit-keyframes AN-ani-25 {
    0% {
        -webkit-transform: translate3d(314px, 95px, 0px);
        -webkit-animation-timing-function: linear;
    }

    100% {
        -webkit-transform: translate3d(178px, 124px, 0px);
        -webkit-animation-timing-function: ease;
    }
}

.run #AN-sObj-12 {
    -webkit-animation-name: AN-ani-25;
    -webkit-animation-duration: 3.0844155844155847s;
    -webkit-animation-delay: 0s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-12 {
    -webkit-transform: translate3d(178px, 124px, 0px);
    -webkit-animation-timing-function: ease;
}

.restart #AN-sObj-12 {
    -webkit-transform: translate3d(314px, 95px, 0px);
    -webkit-animation-timing-function: linear;
}

@-webkit-keyframes AN-ani-26 {
    0% {
        -webkit-transform: translate3d(-72px, 6px, 0px);
        -webkit-animation-timing-function: linear;
    }

    100% {
        -webkit-transform: translate3d(29px, 43px, 0px);
        -webkit-animation-timing-function: ease;
    }
}

.run #AN-sObj-1 {
    -webkit-animation-name: AN-ani-26;
    -webkit-animation-duration: 3.097402597402598s;
    -webkit-animation-delay: 0s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-1 {
    -webkit-transform: translate3d(29px, 43px, 0px);
    -webkit-animation-timing-function: ease;
}

.restart #AN-sObj-1 {
    -webkit-transform: translate3d(-72px, 6px, 0px);
    -webkit-animation-timing-function: linear;
}

@-webkit-keyframes AN-ani-28 {
    0% {
        -webkit-transform: translate3d(19px, 289px, 0px);
        opacity: 1;
        -webkit-transform-origin: 50% 50%;
    }

    0.31% {
        -webkit-transform: translate3d(19px, 289px, 0px);
        opacity: 0;
        -webkit-transform-origin: 50% 50%;
    }

    97.56% {
        -webkit-transform: translate3d(57px, 216px, 0px);
        opacity: 0;
        -webkit-transform-origin: 50% 50%;
    }

    100% {
        -webkit-transform: translate3d(57px, 215px, 0px);
        opacity: 1;
        -webkit-transform-origin: 50% 50%;
    }
}

.run #AN-sObj-27 {
    -webkit-animation-name: AN-ani-28;
    -webkit-animation-duration: 3.188311688311688s;
    -webkit-animation-delay: 0s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-27 {
    -webkit-transform: translate3d(57px, 215px, 0px);
    opacity: 1;
    -webkit-transform-origin: 50% 50%;
}

.restart #AN-sObj-27 {
    -webkit-transform: translate3d(19px, 289px, 0px);
    opacity: 1;
    -webkit-transform-origin: 50% 50%;
}

@-webkit-keyframes AN-ani-30 {
    0% {
        -webkit-transform: translate3d(92px, -30px, 0px) scale3d(0.16, 0.16, 1);
        opacity: 0;
    }

    0.01% {
        -webkit-transform: translate3d(92px, -30px, 0px) scale3d(0.16, 0.16, 1);
        opacity: 1;
    }

    25.54% {
        -webkit-transform: translate3d(92px, 20px, 0px) scale3d(0.16, 0.16, 1);
        opacity: 1;
    }

    53.24% {
        -webkit-transform: translate3d(92px, -30px, 0px) scale3d(0.16, 0.16, 1);
        opacity: 1;
    }

    100% {
        -webkit-transform: translate3d(92px, 20px, 0px) scale3d(0.16, 0.16, 1);
        opacity: 1;
    }
}

.run #AN-sObj-29 {
    -webkit-animation-name: AN-ani-30;
    -webkit-animation-duration: 1.4999220779220779s;
    -webkit-animation-delay: 3.078s;
    -webkit-animation-fill-mode: both;
}

#AN-sObj-29 {
    -webkit-transform: translate3d(92px, 20px, 0px) scale3d(0.16, 0.16, 1);
}

.restart #AN-sObj-29 {
    -webkit-transform: translate3d(92px, -30px, 0px) scale3d(0.16, 0.16, 1);
}
</style>

<script src="gen_validatorv4.js" type="text/javascript"></script>

</head> 

<body> 

<script language="php">
$email = $HTTP_POST_VARS[email];
$mailto = "emma.brown@gentoogroup.com";
$mailsubj = "Form submission";
$mailhead = "From: $email\n";
reset ($HTTP_POST_VARS);
$mailbody = "Values submitted from web site form:\n";
while (list ($key, $val) = each ($HTTP_POST_VARS)) { $mailbody .= "$key : $val\n"; }
if (!eregi("\n",$HTTP_POST_VARS[email])) { mail($mailto, $mailsubj, $mailbody, $mailhead); }
</script>

<div id="iphone">
<div id="top"></div><div id="spacer"></div>

<div id="holder">
<div id="spacer_big">
	<ol id="AN-sObj-parentOl"><li id="AN-sObj-scene-0"><div class="AN-sObj-stage" id="ext-gen19519"><div id="AN-sObj-1"><span>T</span></div><div id="AN-sObj-12"><span>U</span></div><div id="AN-sObj-13"><span>O</span></div><div id="AN-sObj-14"><span>Y</span></div><div id="AN-sObj-15"><span>K</span></div><div id="AN-sObj-16"><span>N</span></div><div id="AN-sObj-17"><span>A</span></div><div id="AN-sObj-18"><span>H</span></div><div id="AN-sObj-27"><span>Submit another idea?</span></div><div class="AN-Object" id="AN-sObj-29"><div id="AN-sObj-val-29"><img height="340" width="340" src="images/arrow1.png"></div></div></div></li>
	
	</ol>
</div>

		<form id="idea" method="POST"  action="email.php" >
			<br/>
			<br/>
				<p>Tell us who you are:</p>
					<input type="text" name="name" value="" style='width: 290px' />
				<p>What's your idea? <span class="required">(required)</span></p>
					<textarea  rows="3" style='width: 290px' name="suggestion" ></textarea>
				<p>Why should we do it? <span class="required">(required)</span></p>
					<textarea rows="3" style='width: 290px' name="reason"></textarea>
				<p>What do you do for a living?</p>
					<input type="text" name="job" value="" style='width: 290px'/>
				<p>What's your contact number?</p>
					<input type="tel" name="phone" value="" style='width: 290px'/>
				<p><center><input type="submit" ></center></p>
		</form>
		<script type="text/javascript">
		 var frmvalidator  = new Validator("idea");
		                     frmvalidator.addValidation("suggestion","req","Please enter your idea");
		                     frmvalidator.addValidation("reason","req","Please enter the reason we should use your idea");
		</script>
		

</div>
<div id="scroll">Use your finger to scroll</div>
</div>
</body>
</html>
