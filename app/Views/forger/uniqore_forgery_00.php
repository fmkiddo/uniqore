		<div class="row">
			<div class="col-md-6 offset-md-3">
				<hr />
        		<form id="superuser-form" method="post" action="{+ siteURL uniqore/forge/1 +}" enctype="application/x-www-form-urlencoded">
        			<input type="hidden" name="{csrf_name}" value="{csrf_data}" tabindex="-1" autocomplete="off" />
        			<input type="hidden" name="begin" value="true" />
        			<div class="text-center my-3">
        				<p></p>
        			</div>
        			<div class="form-group my-3">
        				<label for="key">Secure Key:</label>
        				<div class="input-group">
        					<button type="button" class="btn btn-outline-secondary" id="btn-keygen" onclick="keygen()" title="Generate key">
        						<i class="fas fa-key fa-fw"></i>
        					</button>
        					<input type="password" class="form-control" name="key" id="key" placeholder="Click on generate button" readonly required />
        				</div>
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
        				<div class="input-group">
        					<button type="button" class="btn btn-outline-secondary" id="btn-keygen" onclick="password_generate()">
        						<i class="fas fa-key fa-fw"></i>
        					</button>
        					<input type="password" class="form-control" name="dbpswd" id="dbpswd" placeholder="Database Password" required />
        					<button type="button" class="btn btn-outline-secondary" id="btn-see" onmousedown="eyePassword(this)" onmouseup="eyePassword(this)">
        						<i class="fas fa-eye fa-fw"></i>
        					</button>
        				</div>
        			</div>
        			<div class="form-group my-3">
        				<label for="cfpswd">Confirm Password:</label>
        				<div class="input-group">
        					<input type="password" class="form-control" name="cfpswd" id="cfpswd" placeholder="Confirm the password" required />
        					<button type="button" class="btn btn-outline-secondary" id="btn-see" onmousedown="eyePassword(this)" onmouseup="eyePassword(this)">
        						<i class="fas fa-eye fa-fw"></i>
        					</button>
        				</div>
        			</div>
        			<div class="text-end ">
        				<button type="submit" class="btn btn-primary" title="Click to proceed to next page">
        					<i class="fas fa-arrow-right fa-fw"></i>
        				</button>
        			</div>
        		</form>
        	</div>
        </div>
        <script type="text/javascript">
        function keygen () {
            const xhttp = new XMLHttpRequest ();
            xhttp.onload = function () {
                var reply = JSON.parse (this.responseText);
                if (reply.status === 200) 
                	document.getElementById ('key').value = reply.data.key;
            }
            xhttp.open ('get', '{+ siteURL /uniqore/generate-key +}', true);
            xhttp.send ();
        }

        function password_generate () {
            const xhttp = new XMLHttpRequest ();
            xhttp.onload = function () {
                var reply = JSON.parse (this.responseText);
                if (reply.status === 200) {
                    document.getElementById ('dbpswd').value = reply.data.password;
                    document.getElementById ('cfpswd').value = reply.data.password;
                }
            }
            xhttp.open ('get', '{+ siteURL /uniqore/fortknox-password +}', true);
            xhttp.send ();
        }
        
        {noparse}
        function eyePassword (el) {
            var siblings = el.parentNode.children;
            for (let i = 0; i < siblings.length; i++) {
                if (siblings[i].tagName === 'INPUT') {
                    if (siblings[i].type === 'password') siblings[i].type = 'text';
                    else siblings[i].type = 'password';
                    break;
                }
            }
        } {/noparse}
        </script>
        <footer>
        </footer>

