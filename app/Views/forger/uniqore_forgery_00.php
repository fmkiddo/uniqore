		<div class="row">
			<div class="col-md-6 offset-md-3">
				<hr />
        		<form id="superuser-form" method="post" action="{base_url}uniqore/forge/1" enctype="application/x-www-form-urlencoded">
        			<input type="hidden" name="{csrf_name}" value="{csrf_data}" tabindex="-1" autocomplete="off" />
        			<input type="hidden" name="begin" value="true" />
        			<div class="text-center my-3">
        				<p></p>
        			</div>
        			<div class="form-group my-3">
        				<label for="key">Secure Key:</label>
        				<input type="password" class="form-control" name="key" placeholder="Paste an encryption key for API system" required />
        			</div>
        			<div class="form-group my-3">
        				<label for="dbname">Database Name:</label>
        				<input type="text" class="form-control" name="dbname" value="admin_uniqore" placeholder="Specify database name" required />
        			</div>
        			<div class="form-group my-3">
        				<label for="dbuser">Database Username:</label>
        				<input type="text" class="form-control" name="dbuser" value="uniqore" placeholder="Specify API database user" required />
        			</div>
        			<div class="form-group my-3">
        				<label for="dbpswd">Database Password:</label>
        				<input type="password" class="form-control" name="dbpswd" placeholder="Database Password" required />
        			</div>
        			<div class="form-group my-3">
        				<label for="cfpswd">Confirm Password:</label>
        				<input type="password" class="form-control" name="cfpswd" placeholder="Confirm the password" required />
        			</div>
        			<div class="text-end ">
        				<button type="submit" class="btn btn-primary" title="Click to proceed to next page">
        					<i class="fas fa-arrow-right fa-fw"></i>
        				</button>
        			</div>
        		</form>
        	</div>
        </div>
