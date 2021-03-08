<?PHP
	$user = getUserRole();
	$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$link = substr($actual_link, strrpos($actual_link, '/') + 1);	
	$CI = & get_instance();
?>
<!-- ########## START: LEFT PANEL ########## -->
	<div class="br-logo">
	<a href="<?php echo base_url();?>products">
		<img src="<?PHP echo default_theme_skin_path("images/logo.png")?>" style="width:130px;" />
	</a>
	</div>
	<div class="br-sideleft overflow-y-auto">
		<label class="sidebar-label pd-x-15 mg-t-20">Navigation</label>
		<div class="br-sideleft-menu">	  
			<a href="<?php echo base_url();?>admin/dashboard" class="br-menu-link <?php if($CI->uri->segment(2) == "dashboard"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon ion-ios-home-outline tx-22"></i>
					<span class="menu-item-label">Dashboard</span>
				</div>
			</a>
			<?PHP /**/ ?>
			<a href="<?php echo base_url();?>users" class="br-menu-link <?php if($CI->uri->segment(1) == "users"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-user tx-20"></i>
					<span class="menu-item-label">Manage Users</span>
				</div><!-- menu-item -->
			</a>
			
			<a href="<?php echo base_url();?>newsfeeds" class="br-menu-link <?php if($CI->uri->segment(1) == "newsfeeds"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-feed tx-20"></i>
					<span class="menu-item-label">Manage Newsfeeds</span>
				</div><!-- menu-item -->
			</a>
			<a href="<?php echo base_url();?>groups" class="br-menu-link <?php if($CI->uri->segment(1) == "groups"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-group tx-20"></i>
					<span class="menu-item-label">Manage Groups</span>
				</div><!-- menu-item -->
			</a>
			
			<a href="<?php echo base_url();?>polls" class="br-menu-link <?php if($CI->uri->segment(1) == "polls"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-check tx-20"></i>
					<span class="menu-item-label">Manage Polls</span>
				</div><!-- menu-item -->
			</a>
			
			<a href="<?php echo base_url();?>events" class="br-menu-link <?php if($CI->uri->segment(1) == "events"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-calendar tx-20"></i>
					<span class="menu-item-label">Manage Events</span>
				</div><!-- menu-item -->
			</a>
						
			<a href="<?php echo base_url();?>notifications" class="br-menu-link <?php if($CI->uri->segment(1) == "notifications"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-bell-o tx-20"></i>
					<span class="menu-item-label">Notifications</span>
				</div><!-- menu-item -->
			</a>
			
			<a href="<?php echo base_url();?>sitepages" class="br-menu-link <?php if($CI->uri->segment(1) == "sitepages"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-file-powerpoint-o tx-20"></i>
					<span class="menu-item-label">Manage Sitepages</span>
				</div><!-- menu-item -->
			</a><?PHP /* */ ?>
			
			<a href="javascript:void(0);" class="br-menu-link <?php if($CI->uri->segment(1) == "settings"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon ion-ios-gear-outline tx-24"></i>
					<span class="menu-item-label">Settings</span>
					<i class="menu-item-arrow fa fa-angle-down"></i>
				</div><!-- menu-item -->
			</a>
			<ul class="br-menu-sub nav flex-column">
				<li class="nav-item"><a href="<?php echo base_url();?>settings/district" class="nav-link <?php if($CI->uri->segment(2) == "district"){ echo "active";}?>" >City</a></li>
				<li class="nav-item"><a href="<?php echo base_url();?>settings/city" class="nav-link <?php if($CI->uri->segment(2) == "city"){ echo "active";}?>" >Suburb</a></li>
				<li class="nav-item"><a href="<?php echo base_url();?>settings/religion" class="nav-link <?php if($CI->uri->segment(2) == "religion"){ echo "active";}?>" >Religion</a></li>
				<li class="nav-item"><a href="<?php echo base_url();?>settings/mothertongue" class="nav-link <?php if($CI->uri->segment(2) == "mothertongue"){ echo "active";}?>" >Mothertongue</a></li>
				<li class="nav-item"><a href="<?php echo base_url();?>settings/jobtitle" class="nav-link <?php if($CI->uri->segment(2) == "jobtitle"){ echo "active";}?>" >Job Sector</a></li>
				<li class="nav-item"><a href="<?php echo base_url();?>settings/privacy" class="nav-link <?php if($CI->uri->segment(2) == "privacy"){ echo "active";}?>" >Privacy</a></li>
				<li class="nav-item"><a href="<?php echo base_url();?>settings/gender" class="nav-link <?php if($CI->uri->segment(2) == "gender"){ echo "active"; }?>" >Gender</a></li>
				<li class="nav-item"><a href="<?php echo base_url();?>settings/sitesetting" class="nav-link <?php if($CI->uri->segment(2) == "sitesetting"){ echo "active";}?>" >Site Settings</a></li>
			</ul>
			
			<label class="sidebar-label pd-x-15 mg-t-25 mg-b-20 tx-info op-9">Market Place</label>
			
			<a href="<?php echo base_url();?>category" class="br-menu-link <?php if($CI->uri->segment(1) == "category"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-file-powerpoint-o tx-20"></i>
					<span class="menu-item-label">Manage Categories</span>
				</div><!-- menu-item -->
			</a>
			
			<a href="<?php echo base_url();?>locations" class="br-menu-link <?php if($CI->uri->segment(1) == "locations"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-file-powerpoint-o tx-20"></i>
					<span class="menu-item-label">Manage Locations</span>
				</div><!-- menu-item -->
			</a>
			
			<a href="<?php echo base_url();?>packages" class="br-menu-link <?php if($CI->uri->segment(1) == "packages"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-file-powerpoint-o tx-20"></i>
					<span class="menu-item-label">Manage packages</span>
				</div><!-- menu-item -->
			</a>
			
			<a href="<?php echo base_url();?>ads" class="br-menu-link <?php if($CI->uri->segment(1) == "ads"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-file-powerpoint-o tx-20"></i>
					<span class="menu-item-label">Manage Ads</span>
				</div><!-- menu-item -->
			</a>
			
			<a href="<?php echo base_url();?>products" class="br-menu-link <?php if($CI->uri->segment(1) == "products"){ echo "active";}?>">
				<div class="br-menu-item">
					<i class="menu-item-icon icon fa fa-file-powerpoint-o tx-20"></i>
					<span class="menu-item-label">Manage Products</span>
				</div><!-- menu-item -->
			</a>
			
			<a href="<?php echo base_url();?>admin/logout" class="br-menu-link">
				<div class="br-menu-item">
					<i class="menu-item-icon icon ion-power tx-20"></i>
					<span class="menu-item-label">Logout</span>
				</div>
			</a>
		</div>
	</div>