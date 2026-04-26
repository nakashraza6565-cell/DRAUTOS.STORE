<!DOCTYPE html>
<html lang="zxx">
<head>
	@include('frontend.layouts.head')	
</head>
<body class="js">
	<div style="background: red; color: white; padding: 20px; text-align: center; font-weight: bold; font-size: 24px; z-index: 9999; position: relative;">
		GLOBAL DEPLOYMENT TEST: IF YOU SEE THIS, UPDATE IS WORKING!
	</div>
	
	<!-- Preloader -->
	<div class="preloader">
		<div class="preloader-inner">
			<div class="preloader-icon">
				<span></span>
				<span></span>
			</div>
		</div>
	</div>
	<!-- End Preloader -->
	
	@include('frontend.layouts.notification')
	<!-- Header -->
	@include('frontend.layouts.header')
	<!--/ End Header -->
	@yield('main-content')
	
	@include('frontend.layouts.footer')

</body>
</html>
