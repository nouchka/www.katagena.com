<?php
session_start ();
// change the following paths if necessary
$config = dirname ( __FILE__ ) . '/library/config.php';
require_once ("library/Hybrid/Auth.php");

if (! isset ( $_SESSION ['username'] )) {
	
	try {
		// create an instance for Hybridauth with the configuration file path as parameter
		$hybridauth = new Hybrid_Auth ( $config );
		
		// try to authenticate the user with twitter,
		// user will be redirected to Twitter for authentication,
		// if he already did, then Hybridauth will ignore this step and return an instance of the adapter
		$twitter = $hybridauth->authenticate ( "Twitter" );
		
		// get the user profile
		$twitter_user_profile = $twitter->getUserProfile ();
		
		// echo "Ohai there! U are connected with: <b>{$twitter->id}</b><br />";
		// echo "As: <b>{$twitter_user_profile->displayName}</b><br />";
		$_SESSION ['username'] = $twitter_user_profile->displayName;
		
		// echo "And your provider user identifier is: <b>{$twitter_user_profile->identifier}</b><br />";
		
		// debug the user profile
		// print_r( $twitter_user_profile );
		
		// exp of using the twitter social api: Returns settings for the authenticating user.
		// $account_settings = $twitter->api()->get( 'account/settings.json' );
		
		// print recived settings
		// echo "Your account settings on Twitter: " . print_r( $account_settings, true );
		
		// disconnect the user ONLY form twitter
		// this will not disconnect the user from others providers if any used nor from your application
		// echo "Logging out..";
		$twitter->logout ();
	} catch ( Exception $e ) {
		// Display the recived error,
		// to know more please refer to Exceptions handling section on the userguide
		switch ($e->getCode ()) {
			case 0 :
				echo "Unspecified error.";
				break;
			case 1 :
				echo "Hybriauth configuration error.";
				break;
			case 2 :
				echo "Provider not properly configured.";
				break;
			case 3 :
				echo "Unknown or disabled provider.";
				break;
			case 4 :
				echo "Missing provider application credentials.";
				break;
			case 5 :
				echo "Authentification failed. " . "The user has canceled the authentication or the provider refused the connection.";
				break;
			case 6 :
				echo "User profile request failed. Most likely the user is not connected " . "to the provider and he should authenticate again.";
				$twitter->logout ();
				break;
			case 7 :
				echo "User not connected to the provider.";
				$twitter->logout ();
				break;
			case 8 :
				echo "Provider does not support this feature.";
				break;
		}
		
		// well, basically your should not display this to the end user, just give him a hint and move on..
		echo "<br /><br /><b>Original error message:</b> " . $e->getMessage ();
	}
}
?>
<?php require('php/top.php');?>
<?php if(isset($_GET['p_id']) && $_GET['p_id'] > 0):?>
<?php $p_id = $_GET['p_id'];?>
<?php

	$req = "SELECT ri.* FROM users INNER JOIN rights ri ON user_id = right_user WHERE right_project = " . $p_id . " AND user_login = '" . $_SESSION ['username'] . "';";
	$res = $mysqli->query ( $req );
	if (mysqli_num_rows ( $res ) > 0) {
		$user = mysqli_fetch_array ( $res );
	}
	?>
<?php require('php/matrix.php');?>
<hr />
<script>
  $(function() {
    $(".form").hide();
    $($(".form").get(0)).show();
    $($(".nav-tabs li").get(0)).addClass("active");
    $(".nav-tabs li a").click(function (event) {
      event.preventDefault();
      $(".nav-tabs li").removeClass('active');
      $(this).parent('li').addClass("active");
      $(".form").hide();
      $($(".form").get($('.nav-tabs li a').index(this))).show();
    });
  });
  </script>
<ul class="nav nav-tabs">
	<li><a href="#">Manage tasks</a></li>
	<li><a href="#">Manage permissions</a></li>
	<li style="display: none;"><a href="#">Logs</a></li>
</ul>
<div class="form">
	<form class="form-horizontal" action="./save.php" method="post">
		<input type="hidden" name="action" value="task-name" /> <input
			type="hidden" name="p_id" value="<?php echo $p_id;?>" />
		<fieldset>
			<legend>Add or modify a task</legend>
			<div class="control-group">
				<label class="control-label" for="t_id">Create or Update</label>
				<div class="controls">
					<select id="t_id" name="t_id" placeholder="Id">
						<option value="0">new task</option>
					</select>
					<script>
$("select").change(function () {
	$("select option:selected").each(function () {
		if($(this).val() && $(this).val() > 0){
			$.getJSON('./get.php?t_id='+$(this).val(), function(data) {
				$('#inputTask').val(data.task.task_name);
				$('#inputCategory').val(data.task.task_category);
				$('#inputHours').val(data.task.task_time);
			});
		}else{
			$('#inputTask').val("");
			$('#inputCategory').val("");
			$('#inputHours').val("");
		}
	});
});
</script>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputTask">Name</label>
				<div class="controls">
					<input type="text" id="inputTask" name="inputTask"
						placeholder="Task">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputCategory">Category</label>
				<div class="controls">
					<input type="text" id="inputCategory" name="inputCategory"
						placeholder="Category">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputHours">Hours</label>
				<div class="controls">
					<input type="text" id="inputHours" name="inputHours"
						placeholder="Hours">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn">Save</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>
<div class="form">
	<legend>Users who can access the planning</legend>
	<div style="margin: 0;" class="btn-toolbar" id="user-rights"></div>
	<hr />
	<form class="form-horizontal" action="./save.php?action=add-user"
		method="post">
		<fieldset>
			<legend>Add an user to the planning</legend>
			<div class="control-group">
				<label class="control-label" for="u_name">Specify twitter username</label>
				<div class="controls">
					<input type="text" name="u_name" placeholder="twitter username"> <input
						id="add-user-pid" type="hidden" name="p_id" value="" />
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn">Add user to planning</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>
<?php else: ?>
<?php require('php/projects.php');?>
<?php endif;?>
<?php require('php/bottom.php');?>