<html>
	<head>
		<!-- <link rel="stylesheet" href="style.css" /> -->
		<!-- This adapter normalizes cross-browser differences in WebRTC APIs. Currently necessary in order to support Firefox. -->
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/8.1.2/adapter.min.js"
			integrity="sha512-l40eBFtXx+ve5RryIELC3y6/OM6Nu89mLGQd7fg1C93tN6XrkC3supb+/YiD/Y+B8P37kdJjtG1MT1kOO2VzxA=="
			crossorigin="anonymous"
			referrerpolicy="no-referrer"
		></script>
		
  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
	</head>
	<body>
	<div class="d-flex justify-content-center mt-3">
		<video id="input-video" style="width:70%;" autoplay muted></video>
	</div>
		
		<div class=" fixed-bottom mb-4">
			<div class="container">
				<button class="btn btn-danger   mt-2 form-control" id="backButton">Disconnected</button>
			</div>
			
		</div>
		<script type="module">
			var searchParams = new URLSearchParams(window.location.search);
			const whip = searchParams.get('data');

			$(document).ready(function(){
				$(document).on("click","#backButton",function(){
				var formdata = new FormData();
				formdata.append("whip",whip);
				$.ajax({
					url:"Disconnected.php",
					type:"POST",
					data:formdata,
					dataType:"json",
					contentType:false,
					processData:false,
					success:function(data){
						console.log(data);
						window.location = "https://www.nctsc.space/gameroom/index2.php";
					}
				})
						
				});
			});
			

			
			

			import WHIPClient from "./WHIPClient.js";

			const url = whip;
			const videoElement = document.getElementById("input-video");

			self.client = new WHIPClient(url, videoElement);
		</script>
		 <!-- Bootstrap JavaScript Libraries -->
  
		 <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>
	</body>
</html>

