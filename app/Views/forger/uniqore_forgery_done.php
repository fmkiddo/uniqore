		<div class="row">
			<div class="col-md-6 offset-md-3">
				<hr />
				<div class="card">
					<div class="card-body m-3">
        				<div class="text-center">{if $forger_success}
        					<div class="text-success">
        						<h4>Uniqore System Initiated</h4>
        						<p>The Uniqore API System has been successfully initited!</p>
        						<p>Now you can log into the dashboard for system controls!</p>
        						<p>Please click <a href="{redirect}">here</a> if the page is not redirected to Uniqore API System Login Menu automatically</p>
        					</div>{else}
        					<div class="text-danger">
        						<h4>Initiation Failed!</h4>
        						<p>The initiator unable to initiate the Uniqore API System right now!</p>
        						<p>Please try again later or contact your system administrator if the problem persist!</p>
        					</div>{endif}
        				</div>
        			</div>
        		</div>
			</div>
		</div>
