<?php

	global $link;	
	//require_once __DIR__.'/includes/config_names.php';

	$usrPath = "usr/";
	$configFile= "configs.csv";
	// Column order of config file
	$ShowTapNumCol = 0;
	$ShowBeerLabel = 1;
	$ShowSrmCol = 2;
	$ShowIbuCol = 3;
	$ShowCalCol = 4;
	$ShowAbvCol = 5;
	$ShowAbvImg = 6;
	$ShowKegCol = 7;
	$UseHighResolution = 8;
	$LogoUrl = 9;
	$HeaderText = 10;
	$HeaderTextTruncLen = 11;
	$NumberOfTaps =12;
	$UseFlowMeter = 13;
	$UseColorsXML = 14;
	$HeaderBacklinkText = 15;
	$WebsiteUrl = 16;
	$beersFile= 17;
	$sytleSheet = 18;


	//require_once __DIR__.'/includes/config.php';
	//require_once __DIR__.'/admin/includes/managers/tap_manager.php';
	
	//This can be used to choose between CSV or MYSQL DB
	$db = false;
	
	// Setup array for all the beers that will be contained in the list
	$beers = array();
	
	// read in config file info into arrays
	$handle = fopen($usrPath.$configFile, "r");
	$configPos = 0;
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$config[$configPos] = $data[1];
			//echo "configPos: ".$configPos."     FileLabel: ".$data[0]."         File Value: ".$data[1]."         Config  Value: ".$config[$configPos]."<br>";
			//echo "Array Test, ShowKegCol pos= ".$ShowKegCol."       Value = ".$config[$ShowKegCol]."<br>";

			$configPos++;
		}
	fclose($handle);
	
	//echo "If test";
	//if($config[$ShowKegCol]){ 
	//		echo "ShowKegCol = True";
	//}
	//else {
	//	echo "ShowKegCol = False";
	//}

	// writes csv info into arrays		
   //$beersurl = "beers.csv";
	//$handle = fopen($beersurl, "r");		
	$handle = fopen($usrPath. $config[$beersFile], "r");
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			
			$beeritem = array(
				"id" => $data[0],
				"onTapNumber" => $data[1],
				"beername" => $data[2],
				"style" => $data[3],
				"notes" => $data[4],
				"og" => $data[5],
				"fg" => $data[6],
				"srm" => $data[7],
				"ibu" => $data[8],
				"label" => $data[9],
				//"startAmount" => $data[8],
				//"amountPoured" => $data[9],
				//"remainAmount" => $data[10],
				//"srmRgb" => $data[11]
			);
			$beers[$beeritem['onTapNumber']] = $beeritem;	

	}
	fclose($handle);


	if($config[$UseColorsXML]) {
		//Read Colors.xml
		$SRMlookup = array();
		// xml file path 
		$path = "colors.xml"; 
		// Read entire file into string 
		$xmlfile = file_get_contents($path); 
		// Convert xml string into an object 
		$new = simplexml_load_string($xmlfile); 
		// Convert into json 
		$con = json_encode($new); 
		// Convert into associative array 
		$newArr  = json_decode($con, true); 
		//print_r($newArr); 
		// Array ( [COLOR] => Array ( [0] => Array ( [SRM] => 0.1 [RGB] => 248,248,230 ) [1] => Array ( [SRM] => 0.2 [RGB] => 248,248,220 ) [2] => Array ( [SRM] => 0.3 [RGB] => 247,247,199 ) [3] => Array ( [SRM] => 0.4 [RGB] => 244,249,185 ) [4] => Array ( [SRM] => 0.5 [RGB] => 247,249,180 ) [5] => Array ( [SRM] => 0.6 [RGB] => 248,249,178 ) [6] => Array ( [SRM] => 0.7 [RGB] => 244,246,169 ) [7] => Array ( [SRM] => 0.8 [RGB] => 245,247,166 ) 
		foreach ($newArr [COLOR] as $value){
		 //echo $value["SRM"];
		 $SRMlookup[$value["SRM"]] = $value["RGB"] ;
		 //echo "SRM: ".$value["SRM"]."    RGB:  ".$SRMlookup[$value["SRM"]]." \n";
		 
		}
		//print_r($SRMlookup);
	}


	//function modified from  http://brew-engine.com/js/beer_color_calculator.js
	function srmToRGB(int $srm) {
		// Returns an RGB value based on SRM
		//echo $srm;
		$r=0;
		$g=0;
		$b=0;
	
		if ($srm>=0 && $srm<=1) {
			$r = 240;
			$g = 239;
			$b = 181;
		} else if ($srm>1 && $srm<=2) {
			$r = 233;
			$g = 215;
			$b = 108;
		} else if ($srm>2) {
			// Set red decimal
			if ($srm<70.6843) {        
				$r = 243.8327-6.4040*$srm+0.0453*$srm*$srm;
			} else {
				$r = 17.5014;
			}
			// Set green decimal
			if ($srm<35.0674) {
				$g = 230.929-12.484*$srm+0.178*$srm*$srm;
			} else {
				$g = 12.0382;
			}
			// Set blue decimal
			if ($srm<4) {
				$b = -54*$srm+216;
			} else if ($srm>=4 && $srm<7) {
				$b = 0;
			} else if ($srm>=7 && $srm<9) {
				$b = 13*$srm-91;
			} else if ($srm>=9 && $srm<13) {
				$b = 2*$srm+8;
			} else if ($srm>=13 && $srm<17) {
				$b = -1.5*$srm+53.5;
			} else if ($srm>=17 && $srm<22) {
				$b = 0.6*$srm+17.8;
			} else if ($srm>=22 && $srm<27) {
				$b = -2.2*$srm+79.4;
			} else if ($srm>=27 && $srm<34) {
				$b = -0.4285*$srm + 31.5714;
			} else {
				$b = 17;
			}
		}
		return $r.",".$g.",".$b;
	}
	
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
		<title>Simple Pints</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<!-- Set location of Cascading Style Sheet -->
		<link rel="stylesheet" type="text/css" href="<?php echo $config[$sytleSheet] ?>">
		
		<?php if($config[$UseHighResolution]) { ?>
			<link rel="stylesheet" type="text/css" href="high-res.css">
		<?php } ?>
		
		<link rel="shortcut icon" href="img/pint.ico">
	</head> 

	<body>
		<div class="bodywrapper">
			<!-- Header with Brewery Logo and Project Name -->
			<div class="header clearfix">
				<div class="HeaderLeft">
					<?php if($config[$UseHighResolution]) { ?>			
					  <a href="<?php echo $config[$WebsiteUrl] ?>"><img src="<?php echo $usrPath . $config[$LogoUrl] . "?" . time(); ?>" height="200" alt=""></a>
					  <?php } else { ?>
                      <!-- http://bt.beerprojects.com/wordpress/ -->
					  <a href="<?php echo $config[$WebsiteUrl];?>"><img src="<?php echo $usrPath . $config[$LogoUrl] . "?" . time(); ?>" height="100" alt=""></a>
					  <?php } ?>
                </div>
				<div class="HeaderCenter">
					<h1 id="HeaderTitle">
						<?php
							//if (mb_strlen($config[$HeaderText], 'UTF-8') > ($config[$HeaderTextTruncLen])) {
							//	$headerTextTrunced = substr($config[$HeaderText],0,$config[$HeaderTextTruncLen]) . "...";
							//	echo $headerTextTrunced ; }
							//else
								echo $config[$HeaderText];
						?>
					</h1>
				</div>
				<div class="HeaderRight">
                 <table cellspacing="0">
					<tr>
					<?php if($config[$UseHighResolution]) { ?>			
						<a href="https://github.com/Btazbt/SimplePints"><img src="img/SimplePints-4k.png" height="200" alt=""></a>
					<?php } else { ?>
						<a href="https://github.com/Btazbt/SimplePints"><img src="img/SimplePints.png" height="100" alt=""></a>
					<?php } ?>
                 </tr>
                </table>   
				</div>
			</div>
            
			<!-- End Header Bar -->
			
			<table>
				<thead>
					<tr>
						<?php if($config[$ShowTapNumCol]){ ?>
							<th class="tap-num">
								TAP<br>#
							</th>
						<?php } ?>
						
						<?php if($config[$ShowSrmCol]){ ?>
							<th class="srm">
								GRAVITY<hr>COLOR
							</th>
						<?php } ?>
						
						<?php if($config[$ShowIbuCol]){ ?>
							<th class="ibu">
								BALANCE<hr>BITTERNESS
							</th>
						<?php } ?>
						
						<th class="name">
							BEER NAME &nbsp; & &nbsp; STYLE<hr>TASTING NOTES
						</th>
						
						<?php if($config[$ShowAbvCol]){ ?>
							<th class="abv">
								CALORIES<hr>ALCOHOL
							</th>
						<?php } ?>
						
						<?php if($config[$ShowKegCol]){ ?>
							<th class="keg">
								POURED<hr>REMAINING
							</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php 
					//echo "Tap Count = ".$config[$NumberOfTaps]."<br>";
						for($i = 1; $i <= $config[$NumberOfTaps]; $i++) {
					//	echo "Tap Count = ".$config[$NumberOfTaps]."      Curr# = ".$i."<br>";
						if( isset($beers[$i]) ) {
							$beer = $beers[$i];
					?>
							<tr class="<?php if($i%2 > 0){ echo 'altrow'; }?>" id="<?php echo $beer['id']; ?>">
								<?php if($config[$ShowTapNumCol]){ ?>
									<td class="tap-num">
										<span class="tapcircle"><?php echo $i; ?></span>
                                        <?php if($config[$ShowBeerLabel]){ 
                                        	echo '<img src="';
											echo $usrPath.$beer['label']; 
											echo '" height="200" alt="Beerl Label">';
										}?>
									</td>
								<?php } ?>
							
								<?php if($config[$ShowSrmCol]){ ?>
									<td class="srm">
										<h3><?php echo $beer['og']; ?> OG</h3>
										<h3><?php echo $beer['fg']; ?> FG</h3>
                                        <!--- <h3><?php echo $beer['srm']. " RGB:".$SRMlookup[$beer['srm']]; ?> FG</h3>  --->
										<div class="srm-container">
											<div class="srm-indicator" style="background-color: rgb(<?php 
											//echo $beer['srmRgb'] != "" ? $beer['srmRgb'] : "0,0,0" 
                                           if($config[$UseColorsXML]) { 
											  echo $SRMlookup[$beer['srm']];
										   }
										   else { 
										   	echo srmToRGB($beer['srm']);
										   }
													?>)"></div>
											<div class="srm-stroke"></div> 
										</div>
										
										<h2><?php echo $beer['srm']; ?> SRM</h2>
									</td>
								<?php } ?>
							
								<?php if($config[$ShowIbuCol]){ ?>
									<td class="ibu">
										<h3>
											<?php 
												if( $beer['og'] > 1 ){
													echo number_format((($beer['ibu'])/(($beer['og']-1)*1000)), 2, '.', '');
												}else{
													echo '0.00';
												}
											?> 
											BU:GU
										</h3>
										
										<div class="ibu-container">
											<div class="ibu-indicator"><div class="ibu-full" style="height:<?php echo $beer['ibu'] > 100 ? 100 : $beer['ibu']; ?>%"></div></div>
												
											<?php 
												/*
												if( $remaining > 0 ){
													?><img class="ibu-max" src="img/ibu/offthechart.png" /><?php
												}
												*/
											?>
										</div>
										<h2><?php echo $beer['ibu']; ?> IBU</h2>
									</td>
								<?php } ?>
							
								<td class="name">
									<h1><?php echo $beer['beername']; ?></h1>
									<h2 class="subhead"><?php echo str_replace("_","",$beer['style']); ?></h2>
									<p><?php echo $beer['notes']; ?></p>
								</td>
							
								<?php if(($config[$ShowAbvCol]) && ($config[$ShowAbvImg])){ ?>
									<td class="abv">
										<h3><?php
											$calfromalc = (1881.22 * ($beer['fg'] * ($beer['og'] - $beer['fg'])))/(1.775 - $beer['og']);
											$calfromcarbs = 3550.0 * $beer['fg'] * ((0.1808 * $beer['og']) + (0.8192 * $beer['fg']) - 1.0004);
											if ( ($beer['og'] == 1) && ($beer['fg'] == 1 ) ) {
												$calfromalc = 0;
												$calfromcarbs = 0;
												}
											echo number_format($calfromalc + $calfromcarbs), " kCal";
											?>
										</h3>
										<div class="abv-container">
											<?php
												// Simple ABV Formula
												// $abv = ($beer['og'] - $beer['fg']) * 131;
												// Complex ABV Formula
												$abv = (76.08 * ($beer['og']-$beer['fg']) / (1.775-$beer['og'])) * ($beer['fg'] / 0.794);
												$numCups = 0;
												$remaining = $abv * 20;
												do{
														if( $remaining < 100 ){
																$level = $remaining;
														}else{
																$level = 100;
														}
														?><div class="abv-indicator"><div class="abv-full" style="height:<?php echo $level; ?>%"></div></div><?php
														
														$remaining = $remaining - $level;
														$numCups++;
												}while($remaining > 0 && $numCups < 2);
												
												if( $remaining > 0 ){
													?><div class="abv-offthechart"></div><?php
												}
											?>
										</div>
										<h2><?php echo number_format($abv, 1, '.', ',')."%"; ?> ABV</h2>
									</td>
								<?php } ?>
								
								<?php if(($config[$ShowAbvCol]) && ! ($config[$ShowAbvImg])){ ?>
									<td class="abv">
										<h3><?php
											$calfromalc = (1881.22 * ($beer['fg'] * ($beer['og'] - $beer['fg'])))/(1.775 - $beer['og']);
											$calfromcarbs = 3550.0 * $beer['fg'] * ((0.1808 * $beer['og']) + (0.8192 * $beer['fg']) - 1.0004);
											if ( ($beer['og'] == 1) && ($beer['fg'] == 1 ) ) {
												$calfromalc = 0;
												$calfromcarbs = 0;
												}
											echo number_format($calfromalc + $calfromcarbs), " kCal";
											?>
										</h3>
										<div class="abv">
											<?php
												$abv = ($beer['og'] - $beer['fg']) * 131;
											?>
										</div>
										<h2><?php echo number_format($abv, 1, '.', ',')."%"; ?> ABV</h2>
									</td>
								<?php } ?>
								
                                <!-- Functionality Removed  This should be false all the time  -->
								<?php if($config[$ShowKegCol]){ ?>
									<td class="keg">
										
										
										<h3><?php echo number_format((($beer['startAmount'] - $beer['remainAmount']) * 128)); ?> fl oz poured</h3>
										<?php 
											// Code for new kegs that are not full
                                                                                        $tid = $beer['id'];
                                                                                        $sql = "Select kegId from taps where id=".$tid." limit 1";
                                                                                        $kegID = mysql_query($sql);
                                                                                        $kegID = mysql_fetch_array($kegID);
                                                                                        //echo $kegID[0];
                                                                                        $sql = "SELECT `kegTypes`.`maxAmount` as kVolume FROM  `kegs`,`kegTypes` where  kegs.kegTypeId = kegTypes.id and kegs.id =".$kegID[0]."";
                                                                                        $kvol = mysql_query($sql);
                                                                                        $kvol = mysql_fetch_array($kvol);
                                                                                        $kvol = $kvol[0];
                                                                                        $kegImgClass = "";
                                                                                        if ($beer['startAmount']>=$kvol) {
                                                                                        $percentRemaining = $beer['remainAmount'] / $beer['startAmount'] * 100;
                                                                                        } else {
                                                                                        $percentRemaining =  $beer['remainAmount'] / $kvol * 100;
                                                                                        }
											if( $beer['remainAmount'] <= 0 ) {
												$kegImgClass = "keg-empty";
												$percentRemaining = 100; }
											else if( $percentRemaining < 15 )
												$kegImgClass = "keg-red";
											else if( $percentRemaining < 25 )
												$kegImgClass = "keg-orange";
											else if( $percentRemaining < 45 )
												$kegImgClass = "keg-yellow";
											else if ( $percentRemaining < 100 )
												$kegImgClass = "keg-green";
											else if( $percentRemaining >= 100 )
												$kegImgClass = "keg-full";
										?>
										<div class="keg-container">
											<div class="keg-indicator"><div class="keg-full <?php echo $kegImgClass ?>" style="height:<?php echo $percentRemaining; ?>%"></div></div>
										</div>
										<h2><?php echo number_format(($beer['remainAmount'] * 128)); ?> fl oz left</h2>
									</td>
								<?php } ?>
							</tr>
						<?php }else{ ?>
							<tr class="<?php if($i%2 > 0){ echo 'altrow'; }?>">
								<?php if($config[$ShowTapNumCol]){ ?>
									<td class="tap-num">
										<span class="tapcircle"><?php echo $i; ?></span>
									</td>
								<?php } ?>
							
								<?php if($config[$ShowSrmCol]){ ?>
									<td class="srm">
										<h3></h3>										
										<div class="srm-container">
											<div class="srm-indicator"></div>
											<div class="srm-stroke"></div> 
										</div>
										
										<h2></h2>
									</td>
								<?php } ?>
							
								<?php if($config[$ShowIbuCol]){ ?>
									<td class="ibu">
										<h3></h3>										
										<div class="ibu-container">
											<div class="ibu-indicator"><div class="ibu-full" style="height:0%"></div></div>
										</div>								
										<h2></h2>
									</td>
								<?php } ?>
							
								<td class="name">
									<h1>Nothing on tap</h1>
									<h2 class="subhead"></h2>
									<p></p>
								</td>
								
								<?php if(($config[$ShowAbvCol]) && ($config[$ShowAbvImg])){ ?>
									<td class="abv">
										<h3></h3>
										<div class="abv-container">
											<div class="abv-indicator"><div class="abv-full" style="height:0%"></div></div>
										</div>
										<h2></h2>
									</td>
								<?php } ?>

								<?php if(($config[$ShowAbvCol]) && ! ($config[$ShowAbvImg])){ ?>
									<td class="abv">
										<h3></h3>

										<h2></h2>
									</td>
								<?php } ?>								
								
								<?php if($config[$ShowKegCol]){ ?>
									<td class="keg">
										<h3></h3>
										<div class="keg-container">
											<div class="keg-indicator"><div class="keg-full keg-empty" style="height:0%"></div></div>
										</div>
										<h2>0 fl oz left</h2>
									</td>
								<?php } ?>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</body>
</html>
