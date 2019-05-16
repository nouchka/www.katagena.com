<?php

require ("lib/bdd.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"
	prefix="og: http://ogp.me/ns#">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Setagaya Priorities Matrix</title>
<meta name="Description"
	content="Setagaya Priorities Matrix provide you a free online tools to prioritize your tasks, organize your planning based on Eisenhower Method which evaluated tasks using the criteria important/unimportant and urgent/not urgent." />
<meta name="Keywords"
	content="Eisenhower Method, Urgent, important, matrix, prioritization, prioritisation, Action Priority Matrix, time management, free time management skills training,time-management skills,personal time management tips,time management tips,time management techniques,time management articles,time management systems" />
<link
	href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap.min.css"
	media="screen" rel="stylesheet" type="text/css" />
<link
	href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap-responsive.min.css"
	media="screen" rel="stylesheet" type="text/css" />
<link rel="stylesheet"
	href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script type="text/javascript" src="//code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript"
	src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript"
	src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/js/bootstrap.js"></script>
<script type="text/javascript"
	src="//cdnjs.cloudflare.com/ajax/libs/hogan.js/2.0.0/hogan.min.js"></script>
<style>
.connectedSortable {
	list-style-type: none;
	margin: 0;
	padding: 0 0 2.5em;
	float: left;
	margin-right: 10px;
	width: 100%;
}

.connectedSortable li {
	margin: 0 5px 5px 5px;
	padding: 5px;
	font-size: 1.2em;
	width: 100%;
}

.right {
	float: right;
}

body .alert .close {
	right: 0;
	top: -17px;
}
</style>
<meta http-equiv="content-language" content="en" />
<meta name="google-site-verification"
	content="v6qF3Z-u8ipZVdfEns_TszcTYisE5AUYlHmTEq9yd90" />
</head>
<body>
	<div class="container">
		<div class="masthead">
			<h3 class="muted"></h3>
			<div class="navbar">
				<div class="navbar-inner">
					<div class="container">
						<ul class="nav">
							<li
								<?php

if (strtolower($_SERVER["SCRIPT_NAME"]) == "/setagaya/index.php") {
            echo ' class="active"';
        }
        ?>><a
								href="./">Home</a></li>
							<li
								<?php

if (strtolower($_SERVER["SCRIPT_NAME"]) == "/setagaya/demo.php") {
            echo ' class="active"';
        }
        ?>><a
								href="./demo.php">Demo</a></li>
							<li
								<?php

if (strtolower($_SERVER["SCRIPT_NAME"]) == "/setagaya/form.php") {
            echo ' class="active"';
        }
        ?>><a
								href="./form.php">My plannings</a></li>
						</ul>
					</div>
				</div>
			</div>
			<!-- /.navbar -->
		</div>