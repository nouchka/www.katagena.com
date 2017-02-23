<h1>My plannings</h1>
<div class="bs-docs-example">
	<ul class="nav nav-tabs nav-stacked">
<?php
$req = "SELECT project_name, project_id FROM `users` INNER JOIN rights ON right_user = user_id INNER JOIN projects ON right_project = project_id WHERE user_login = '" . $_SESSION ['username'] . "' ORDER BY project_name ASC;";
$res = mysqli_query ( $req );
$options = "";
while ( $line = mysqli_fetch_array ( $res ) ) {
	echo "<li><a href='./form.php?p_id=" . $line ['project_id'] . "'>" . $line ['project_name'] . "</a></li>";
	$options .= "<option value=\"" . $line ['project_id'] . "\">" . $line ['project_name'] . "</option>\n";
}
?>
</ul>
</div>
<hr />
<form class="form-horizontal" action="./save.php" method="post">
	<input type="hidden" name="action" value="project-name" />
	<fieldset>
		<legend>Create planning</legend>
		<div class="control-group">
			<label class="control-label" for="inputId">Create or Update</label>
			<div class="controls">
				<select id="inputId" name="inputId" placeholder="Id">
					<option value="0">new planning</option>
			<?php echo $options;?>
		</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputName">Name</label>
			<div class="controls">
				<input type="text" id="inputName" name="inputName"
					placeholder="Name">
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Save</button>
			</div>
		</div>
	</fieldset>
</form>