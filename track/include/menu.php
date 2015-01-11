	<!-- Menu -->
	<div id="menu" class="box">


		<ul class="box">
		<?if ($session->isAdmin() || $session->userlevel == 2) {?>
			<li><a href="./home.php"><span>Home</span></a></li> 
		<?php }?>
		<?if ($session->userlevel == 1) {?>
			<li><a href="./formmanager-therapist.php"><span>Forms</span></a></li> 
		<?php }?>
		<?if ($session->isAdmin()) {?>
			<li id="menu-active" onmouseover="this.className = 'dropdown-on'" onmouseout="this.className = 'dropdown-off'"><div><a href="#"><span>User Admin</span></a>
				<!-- Dropdown menu -->
				<div class="drop">
					<ul class="box">
						<li><a href="./alluser.php">All Users</a></li>
					</ul>
				</div> <!-- /drop -->
			</div></li>
		<?php }?>	
		<?if ($session->isAdmin() || $session->userlevel == 2) {?>
			<li id="menu-active" onmouseover="this.className = 'dropdown-on'" onmouseout="this.className = 'dropdown-off'"><div><a href="#"><span>Forms</span></a>
				<!-- Dropdown menu -->
				<div class="drop">
					<ul class="box">
							<li><a href="formmanager-all.php">Form Manager - All By Status</a></li>
							<li><a href="formmanager.php">Form Manager - By Agency, Status</a></li>
							<li><a href="formmanager-patient.php">Form Manager - By Patient</a></li>
					</ul>
				</div> <!-- /drop -->
			</div></li>
			<li><a href="transfers.php"><span>Transfers</span></a></li> 
		<?}?>

		</ul>
	</div>
