<!DOCTYPE html>
<html>
  <head>
		<meta charset="UTF-8">
		<?php print $tags; ?>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="application-name" content="Correctarium"/>
		<meta name="msapplication-TileColor" content="#FFFFFF" />
		<meta name="msapplication-TileImage" content="/gfx/mstile-144x144.png" />
		<meta name="msapplication-square70x70logo" content="/gfx/mstile-70x70.png" />
		<meta name="msapplication-square150x150logo" content="/gfx/mstile-150x150.png" />
		<meta name="msapplication-wide310x150logo" content="/gfx/mstile-310x150.png" />
		<meta name="msapplication-square310x310logo" content="/gfx/mstile-310x310.png" />
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="/gfx/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/gfx/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/gfx/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/gfx/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="/gfx/apple-touch-icon-60x60.png" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="/gfx/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="/gfx/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="/gfx/apple-touch-icon-152x152.png" />
    <link rel="icon" type="image/png" href="/gfx/favicon-196x196.png" sizes="196x196" />
    <link rel="icon" type="image/png" href="/gfx/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/png" href="/gfx/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/gfx/favicon-16x16.png" sizes="16x16" />
    <link rel="icon" type="image/png" href="/gfx/favicon-128.png" sizes="128x128" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=PT+Serif:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/css/main/style.min.css?v=9">
    <link rel="stylesheet" href="/css/switchery.min.css">
    <link rel="stylesheet" href="/css/likely.css">
    <link rel="stylesheet" href="/css/bootstrap-datetimepicker.min.css">
    <script type="text/javascript">
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-64399636-1', 'auto');
      ga('send', 'pageview');

    </script>
  <meta name="google-site-verification" content="oFT-7HsUQNtB5LM_BkD7IuJJrMQI3CFkR5pWtgz4p7o" />
	</head>
<body>
  <div class="container">
    <header class="navbar navbar-static-top line">
    	<div class="m-title">
    		<a class="home-link" href="/" alt="Correctarium" title="Correctarium"></a>
    	</div>
    	<ul class="main-menu">
    		<?php

    		foreach ($items as $value) {
    			print_r($value);
    		}

    		 ?>
    		 <li class="menu-item"><a href="http://blog.correctarium.com/">Блог</a></li>
         <li class="menu-item fb-page">
           <a href="https://www.facebook.com/correctarium/" title="Страница «Корректариума» в «Фейсбуке»">
             <span class="fb-icon"></span>
             Facebook
           </a>
         </li>
         <li class="menu-item user-widget"><?php echo $session ?></li>
    	</ul>
    </header>

    <?php include 'application/views/'.$content_view; ?>
  </div>
  <footer>
	<div class="footer_wrap">
		<div class="date menu-item">
			<p>©<span id="year"></span></p>
		</div>
		<div class="my-social-buttons">
			<div class="likely">
				<div class="facebook">Facebook</div>
				<div class="vkontakte">Вконтакте</div>
			</div>
      <div class="vcard menu-item anchor-object foot-email">Напишите нам:
        <span class="email"><a class="contact" href="mailto:mail@correctarium.com?subject=Новый заказ">mail@correctarium.com</a></span>
      </div>
		</div>
	</div>
  </footer>
  <script src="https://www.parsecdn.com/js/parse-latest.js"></script>
  <script src="//correctarium.com/js/moment-with-locales.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="/js/likely.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

  <!-- Facebook Pixel Code -->
  <script>
  !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
  n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
  document,'script','//connect.facebook.net/en_US/fbevents.js');

  fbq('init', '773771236068000');
  fbq('track', "PageView");</script>
  <noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=773771236068000&ev=PageView&noscript=1"
  /></noscript>
  <!-- End Facebook Pixel Code -->

  <script src="/js/bootstrap-datetimepicker.min.js"></script>
  <script src="/js/validator.min.js"></script>
  <script src="/js/main.js?v=4"></script>
  <script src="/js/switchery.min.js"></script>
  <script type="text/javascript">
      var elem = document.querySelector('.js-switch');
      var init = new Switchery(elem, { size: 'small' });
    </script>
</body>
</html>
