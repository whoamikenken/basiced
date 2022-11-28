		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2">
		<title>elFinder 2.1.x source version with PHP connector</title>

		<!-- Section CSS -->
		<!-- jQuery UI (REQUIRED) -->
		<!-- <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css"> -->
		<!-- elFinder CSS (REQUIRED) -->
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/elfinder.min.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/theme.css">

		<!-- Section JavaScript -->
		<!-- jQuery and jQuery UI (REQUIRED) -->
		<!--[if lt IE 9]>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<!--<![endif]-->
		<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

		<!-- elFinder JS (REQUIRED) -->
		<script src="<?=base_url()?>js/elfinder.min.js"></script>

		<!-- Extra contents editors (OPTIONAL) -->
		<script src="<?=base_url()?>js/extras/editors.default.min.js"></script>

		<!-- GoogleDocs Quicklook plugin for GoogleDrive Volume (OPTIONAL) -->
		<!--<script src="js/extras/quicklook.googledocs.js"></script>-->

		<!-- elFinder initialization (REQUIRED) -->
		<script type="text/javascript" charset="utf-8">
			// Documentation for client options:
			// https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
			$(document).ready(function() {
				$('#elfinder').elfinder(
					// 1st Arg - options
					{
						cssAutoLoad : false,               // Disable CSS auto loading
						baseUrl : './',                    // Base URL to css/*, js/*
						url : '<?=base_url()?>elfinder/connector.minimal.php'  // connector URL (REQUIRED)
						// , lang: 'ru'                    // language (OPTIONAL)
					},
					// 2nd Arg - before boot up function
					function(fm, extraObj) {
						// `init` event callback function
						fm.bind('init', function() {
							// Optional for Japanese decoder "encoding-japanese.js"
							if (fm.lang === 'ja') {
								fm.loadScript(
									[ '//cdn.rawgit.com/polygonplanet/encoding.js/1.0.26/encoding.min.js' ],
									function() {
										if (window.Encoding && Encoding.convert) {
											fm.registRawStringDecoder(function(s) {
												return Encoding.convert(s, {to:'UNICODE',type:'string'});
											});
										}
									},
									{ loadType: 'tag' }
								);
							}
						});
						// Optional for set document.title dynamically.
						var title = document.title;
						fm.bind('open', function() {
							var path = '',
								cwd  = fm.cwd();
							if (cwd) {
								path = fm.path(cwd.hash) || null;
							}
							document.title = path? path + ':' + title : title;
						}).bind('destroy', function() {
							document.title = title;
						});
					}
				);
			});
		</script>

		<div id="content" class="row fileupload-buttonbar clearfix">
        <div class="col-md-12">
		<br />
        <br />
        <br />
        </div>
      </div>
      <div id="content" class="row fileupload-buttonbar clearfix">
        <div class="col-md-12">
		<div id="elfinder"></div>
        </div>
      </div>
      <script>
      	validateCanWrite();
		function validateCanWrite(){
			if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
			else $(".btn").css("pointer-events", "");
		}
      </script>