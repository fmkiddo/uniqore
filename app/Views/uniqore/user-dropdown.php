                                						<div class="dropend">
                                                            <div class="d-none" id="data" data-user="<?= esc($password); ?>">
                                                                <input type="hidden" id="uuid" value="<?= esc($uuid); ?>" />
                                                                <input type="hidden" id="username" value="<?= esc($username); ?>" />
                                                                <input type="hidden" id="email" value="<?= esc($email); ?>" />
                                                                <input type="hidden" id="phone" value="<?= esc($phone); ?>" />
                                                                <input type="hidden" id="active" value="<?= esc($status); ?>" />
                                                            </div>
                                                            <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown">More</a>
                                                            <ul class="dropdown-menu">
                                                            	<li>
                                                            		<a id="pswd-change" class="dropdown-item" href="#modal-changepassword" data-bs-toggle="modal">Change Password</a>
                                                                </li>
                                                                <li>
                                                                    <hr class="dropdown-divider" />
                                                                </li>
                                                                <li>
                                                                    <a id="edit-data" class="dropdown-item" href="#modal-form" data-bs-toggle="modal">Edit</a>
                                                                </li>
                                                            </ul>
                                                        </div>
