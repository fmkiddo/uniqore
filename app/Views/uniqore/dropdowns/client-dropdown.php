														<div class="dropend">
															<div class="d-none" id="data">
																<input type="hidden" id="uuid" value="<?= esc($uuid); ?>" />
																<input type="hidden" id="ccode" value="<?= esc($code); ?>" />
																<input type="hidden" id="capi" value="<?= esc($api); ?>" />
																<input type="hidden" id="cname" value="<?= esc($name); ?>" />
																<input type="hidden" id="clname" value="<?= esc($lname); ?>" />
																<input type="hidden" id="addr1" value="<?= esc($addr1); ?>" />
																<input type="hidden" id="addr2" value="<?= esc($addr2); ?>" />
																<input type="hidden" id="cphone" value="<?= esc($cphone); ?>" />
																<input type="hidden" id="ctax" value="<?= esc($taxno); ?>" />
																<input type="hidden" id="cpic" value="<?= esc($picname); ?>" />
																<input type="hidden" id="cpicmail" value="<?= esc($picmail); ?>" />
																<input type="hidden" id="cpicphone" value="<?= esc($picphone); ?>" />
																<input type="hidden" id="cdbname" value="<?= esc($dbname); ?>" />
																<input type="hidden" id="cdbuser" value="<?= esc($dbuser); ?>" />
																<input type="hidden" id="cdbpswd" value="<?= esc($dbpswd); ?>" />
																<input type="hidden" id="cdbprefix" value="<?= esc($dbprefix); ?>" />
																<input type="hidden" id="status" value="<?= esc($status); ?>" />
															</div>
                                                            <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown">More</a>
                                                            <ul class="dropdown-menu">
                                                            	<li>
                                                            		<a id="active-toggle" class="dropdown-item" href="#modal-deactivate" data-bs-toggle="modal"><?= esc(($status === 'true') ? 'Deactivate' : 'Activate'); ?></a>
                                                            	</li>
                                                                <li>
                                                                    <hr class="dropdown-divider" />
                                                                </li>
                                                                <li>
                                                                    <a id="edit-data" class="dropdown-item" href="#modal-form" data-bs-toggle="modal">Update</a>
                                                                </li>
                                                            </ul>
														</div>
