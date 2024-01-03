<html>
	<head>
		<!-- This adapter normalizes cross-browser differences in WebRTC APIs. Currently necessary in order to support Firefox. -->
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/8.1.2/adapter.min.js"
			integrity="sha512-l40eBFtXx+ve5RryIELC3y6/OM6Nu89mLGQd7fg1C93tN6XrkC3supb+/YiD/Y+B8P37kdJjtG1MT1kOO2VzxA=="
			crossorigin="anonymous"
			referrerpolicy="no-referrer"
		></script>
		<link rel="stylesheet" href="style.css" />
	</head>
	<body>
		<h4>Playing video using WHEP</h4>
		<h5>(remote content)</h5>
		<video id="remote-video" controls autoplay muted></video>

		<script type="module">
            var searchParams = new URLSearchParams(window.location.search);
			var whep = searchParams.get('data');
			import WHEPClient from "./WHEPClient.js";

			const url = whep;
			const videoElement = document.getElementById("remote-video");

			self.client = new WHEPClient(url, videoElement);
		</script>
	</body>
</html>