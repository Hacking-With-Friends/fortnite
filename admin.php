<?php
// * IP block on accessing the admin functions.
/*
	$allowed_IP = ['77.80.235.37', '77.218.254.202'];

	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_IP)) {
		die('401');
	}
*/
?>
<html>
	<head>
		<meta name="twitter:card" content="summary" />
		<meta name="twitter:site" content="@luke_binwalker" />
		<meta name="twitter:creator" content="@luke_binwalker" />
		<meta name="github:creator" content="Tamm" />
		<meta property="og:url" content="https://fnite.se/" />
		<meta property="og:title" content="Malmö LAN" />
		<meta property="og:description" content="Malmö LAN - Fortnite Score submission" />
		<meta property="og:image" content="https://fnite.se/logo.png" />
		<meta charset="utf-8" />
		<title>Fortnite @ Malmö LAN</title>
		<!-- A joint effort by Tamm and DoXiD to create a quick tournament site during rDreamHack Summer 2018.
			Needless to say, we put this site together in less than 6 hours, some features were intended, some wasn't.
			All in the good spirit of hackathon.

			Thread lightly, Ye who enter here.. Here be dragons -->
		<style type="text/css">
			@font-face {
				font-family: LuckyGuy;
				src: url('/LuckiestGuy-Regular.ttf');
			}

			:root {
				--blue: #62CFEE;
				--pink: #F92472;
				--green: #A6E22C;
				--yellow: #E7DB74;
				--orange: #f60;
				--moreorange: #66D9EF;
				--teal: #66D9EF;
				--darkish: #74705D;
				--dark: #2a2a2a;
			}

			body {
				background-image: url('./background.jpg');
				background-size: cover;
				background-position: center center;
				background-attachment: fixed;
				margin: 0px;
				padding: 0px;
				color: #FFFFFF;
				font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
			}

			input {
				vertical-align: middle;
				outline: none;
				box-shadow: none;
				border: 0px;

				color: #000000;
				border-radius: 2px;

				width: 120px;
				height: 50px;
			}

			input[type=text], input[type=password] {
				height: 40px;
				color: #e7e7e7;
				background-color: #393939;

				width: 202px;
				margin: 1px;
				padding: 5px;
				border-radius: 2px;
			}

			input[type=submit] {
				/*color: #e7e7e7;*/
				background-color: #FF0;
				cursor: pointer;
			}

			#menu {
				width: 100%;
				height: 50px;
				border-bottom: 1px solid var(--blue);
				background-color: var(--dark);
			}

				#menu > .company {
					font-family: 'LuckyGuy', cursive;
					font-size: 30px;
					max-width: 140px;
					padding: 0px;
					line-height: 24px;
					margin: 0px;
					padding-top: 5px;
					padding-left: 10px;
					cursor: pointer;
				}

					#menu > .company > .upper {
						display: block;
						margin: 0px;
					}

					#menu > .company > .bottom {
						color: var(--orange);
						display: inline;
						margin: 0px;
						font-size: 14px;
					}

				#menu > .buttons {
					position: absolute;
					top: 0px;
					left: 160px;
				}

					#menu > .buttons > input[type=button] {
						border-radius: 0px;
					}
					#menu > .buttons > input[type=button]:hover {
						background-color: var(--blue);
					}
					#menu > .buttons > input[type=button]:active {
						background-color: var(--blue);
					}

			.entry {
				display: flex;
				width: 100%;
				flex-grow: 1;
				flex-direction: column;
			}

				.entry:nth-child(even) {background: #4A4A4A}
				.entry:nth-child(odd) {background: #C2C2C2}
				.header {
					background-color: #272822 !important;
				}
				.entry > .row {
					display: flex;
					flex-direction: row;
				}

				.entry > .row > * {
					flex: 1 1;
				}

				.entry > .row > .screenshot {
					width: 100%;
					background-size: cover;
					visibility: hidden;
				}

			#content {
				position: absolute;
				left: 50%;
				top: 50px;
				width: 750px;
				margin-left: -375px;
				background-color: #272822;
				border: 1px solid #66D9EF;

				column-count: 1;
				column-width: 100%;
				column-gap: 2px;

				padding-bottom: 30px;

				display: flex;
				flex-direction: column;
			}

				#content > div > table {
					text-align: center;
					break-inside: avoid;
					width: 100%;
				}

					.mode {
						font-size: 34px;
						padding: 20px;
					}

					.pname, .pscore {
						font-size: 34px;
						padding: 20px;
						opacity: 0.8;
					}

					.name {
						font-family: Arial, sans-serif;
						font-weight: bold;
					}

					.solo {
						padding-bottom: 0px;
						padding-top: 40px;
						font-family: 'LuckyGuy', cursive;
						color: var(--green);
					}

					.duo {
						padding-bottom: 0px;
						padding-top: 40px;
						font-family: 'LuckyGuy', cursive;
						color: var(--blue);
					}

			#formFields {
				width: 202px;
				height: 320px;
				position: absolute;
				left: 50%;
				top: 40PX;
				margin-left: -101px;
			}


				#formFields > form > p {
					font-size: 12px;
					color: #F8F8F0;
				}

					#formFields > form > p > b {
						font-size: 12px;
						color: #66D9EF;
					}

				#submit {
					position: absolute;
					left: 50%;
					bottom: 0px;
					width: 170px !important;
					margin-left: -85px !important;
				}

			#popup {
				border: 1px solid var(--blue);
				text-align: center;
			}
				#popup > input[type=submit] {
					/*color: #e7e7e7;*/
					background-color: var(--green);
					cursor: pointer;
				}

				.popup_p {
					color: var(--pink);
					padding: 1px;
					padding-left: 4px;
					font-size: 10px;
					font-weight: bold;
					display: inline-block;
					cursor: pointer;
				}
					.popup_p:hover {
						font-size: 12px;
					}

				.button {
					position: absolute;
					bottom: 0px;
					left: 0px;
					width: 100%;
				}

			#notification {
				width: 450px;
				position: absolute;
				left: 50%;
				top: 120px;
				margin-left: -225px;
				margin-top: -90px;
				background-color: #272822;
				border: 1px solid var(--blue);
				text-align: center;
			}
				.error {
					border: 1px solid var(--pink) !important;
					background-color: var(--orange) !important;
				}

				.error > h3 {
					color: var(--pink) !important;
				}

				#notification > h3 {
					font-family: 'LuckyGuy', cursive;
					font-size: 25px;
				}

			.error_msg {
				text-align: left;
			}

		</style>
		<script type="text/javascript">
			Element.prototype.remove = function() {
				if (this.parentElement)
					this.parentElement.removeChild(this);
			}
			NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
				for(var i = this.length - 1; i >= 0; i--) {
					if(this[i] && this[i].parentElement) {
						this[i].parentElement.removeChild(this[i]);
					}
				}
			}

			var timers = {};
			let mouse = {};
			let sprites = {};
			let fileToUpload = null; // Will hold the screenshot, because lack of better understanding shady javascript.

			function clearTimer(name) {
				if(timers[name] !== undefined) {
					window.clearInterval(timers[name]);
					return true;
				}
				return false;
			}

			function setTimer(name, func, time=10) {
				timers[name] = setInterval(func, time);
			}

			function destroy(obj) {
				if(obj)
					obj.remove();
			}

			function expand(id) {
				let s = document.getElementById('screenshot_'+id);
				if (s.getAttribute('visible') == "1") {
					s.style.height = "0px";
					s.style.visibility = "hidden";
					s.setAttribute('visible', '0');
				} else {
					s.style.height = "400px";
					s.style.visibility = "visible";
					s.setAttribute('visible', '1');
				}
			}

			function populateChildren(parent, objects) {
				Object.entries(objects).forEach(([index, val]) => {
					let key = Object.keys(val)[0];
					let o = document.createElement(key);

					if(typeof val[key]["styles"] !== 'undefined') {
						Object.entries(val[key]["styles"]).forEach(([s, sval]) => {
							o.style[s] = sval;
						});
					}

					Object.entries(val[key]).forEach(([property, propval]) => {
						if(property == "objects" || property == "styles")
							return;

						if(property == "innerHTML")
							o.innerHTML += propval;
						else
							o.setAttribute(property, propval);
					});

					if(typeof val[key]["objects"] !== 'undefined') {
						populateChildren(o, val[key]["objects"]);
					}

					parent.appendChild(o);
				});
			}

			function destoryPopup(e) {
				if(Date.now() - sprites['popup'].time < 100)
					return;

				let height = sprites['popup'].obj.scrollHeight;
				let width = sprites['popup'].obj.scrollWidth;
				let x_pos = sprites['popup'].obj.offsetLeft;
				let y_pos = sprites['popup'].obj.offsetTop;

				let x = e.clientX;
				let y = e.clientY;

				if ((x < x_pos || x > (x_pos+width)) || (y < y_pos || y > (y_pos+height))) {
					destroy(sprites['popup'].obj);
					clearTimeout(timers['closePopup']);
					delete timers['closePopup'];
				}
			}

			function showPopup(struct) {
				if(typeof mouse['close_popup'] !== 'undefined') {
					delete mouse['close_popup'];
				}
				if(typeof sprites['popup'] !== 'undefined') {
					destroy(sprites['popup'].obj);
					delete sprites['popup'];
				}

				let d = document.createElement('div');
				d.id = 'popup';
				Object.entries(struct["styles"]).forEach(([key, val]) => {
					d.style[key] = val;
				});

				if(typeof struct["objects"] !== 'undefined')
					populateChildren(d, struct["objects"]);
				document.body.appendChild(d);

				mouse["close_popup"] = destoryPopup;
				sprites['popup'] = {"obj" : d, "time" : Date.now()};
			}

			function notify(title, content, error=false, clear_popup) {
				if(clear_popup) {
					let popup = document.getElementById('popup');
					if(popup)
						popup.remove();
				}

				notification = document.createElement('div');
				notification.id = 'notification';

				notification.innerHTML = '<h3 id="popup_title">'+title+'</h3>';
				notification.innerHTML += '<p>'+content+'</p>';


				document.body.appendChild(notification);
				if (error) {
					document.getElementById('popup_title').style.color = '#FD971F';
					notification.setAttribute('class', 'error');
				}

				setTimer('clear_popup', function() {
					let notification = document.getElementById('notification');
					if(notification)
						notification.remove();
					clearTimer('clear_popup');
				}, 5000);
			}

			function getScreenshots() {
				let xhr = new XMLHttpRequest();
				xhr.open("GET", "./admin_backend.php", true);

				xhr.onreadystatechange = function() {
					if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
						document.getElementById('content').innerHTML = this.responseText;
					}
				}

				xhr.send();
			}

			window.onload = function() {
				getScreenshots();
			}
		</script>
	</head>
	<body>
		<div id="menu">
			<div class="company" id="company">
				Fortnite
				<p class="bottom">Malmö LAN</p>
			</div>
		</div>
		<div id="content">
		</div>
	</body>
</html>
