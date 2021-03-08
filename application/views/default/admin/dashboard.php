<?PHP
	$CI = get_instance();
	$CI->load->model("settings_model");			
	$allmother = $CI->settings_model->getMothertongueCount();
	$allReligion = $CI->settings_model->getReligionCount();
	$allJoptitle = $CI->settings_model->getjotitleCount();
	$allGender = $CI->settings_model->getgenderCount();
	$allAge = $CI->settings_model->getAgeCount();
	$allCountry = $CI->settings_model->getCountryCount();		
?>	
<div class="br-mainpanel">
	<div class="pd-30">
		<h4 class="tx-gray-800 mg-b-5"><?PHP echo $title; ?></h4>
	</div><!-- d-flex -->

	<div class="br-pagebody mg-t-5 pd-x-30">
        <div class="row row-sm">
			<div class="col-sm-6 col-xl-3">
				<div class="bg-teal rounded overflow-hidden">
					<div class="pd-25 d-flex align-items-center">
						<i class="ion ion-person tx-60 lh-0 tx-white op-7"></i>
						<div class="mg-l-20">
							<p class="tx-10 tx-spacing-1 tx-mont tx-medium tx-uppercase tx-white-8 mg-b-10">Total Users</p>
							<p class="tx-24 tx-white tx-lato tx-bold mg-b-2 lh-1"><?PHP echo $users; ?></p>
							<span class="tx-11 tx-roboto tx-white-6">&nbsp;</span>
						</div>
					</div>
				</div>
			</div><!-- col-3 -->
			<div class="col-sm-6 col-xl-3 mg-t-20 mg-sm-t-0">
				<div class="bg-danger rounded overflow-hidden">
				<div class="pd-25 d-flex align-items-center">
				<i class="ion ion-bag tx-60 lh-0 tx-white op-7"></i>
				<div class="mg-l-20">
				<p class="tx-10 tx-spacing-1 tx-mont tx-medium tx-uppercase tx-white-8 mg-b-10">Total Products</p>
				<p class="tx-24 tx-white tx-lato tx-bold mg-b-2 lh-1"><?PHP echo $products; ?></p>
				<span class="tx-11 tx-roboto tx-white-6">&nbsp;</span>
				</div>
				</div>
				</div>
			</div><!-- col-3 -->
			<div class="col-sm-6 col-xl-3 mg-t-20 mg-xl-t-0">
				<div class="bg-primary rounded overflow-hidden">
					<div class="pd-25 d-flex align-items-center">
					<i class="ion ion-clock tx-60 lh-0 tx-white op-7"></i>
					<div class="mg-l-20">
					<p class="tx-10 tx-spacing-1 tx-mont tx-medium tx-uppercase tx-white-8 mg-b-10">Total Active Events</p>
					<p class="tx-24 tx-white tx-lato tx-bold mg-b-2 lh-1"><?PHP echo $events; ?></p>
					<span class="tx-11 tx-roboto tx-white-6">&nbsp;</span>
					</div>
					</div>
				</div>
			</div><!-- col-3 -->
		
			<div class="col-sm-6 col-xl-3 mg-t-20 mg-xl-t-0">
				<div class="bg-orange rounded overflow-hidden">
				<div class="pd-25 d-flex align-items-center">
				<i class="ion ion-pinpoint tx-60 lh-0 tx-white op-7"></i>
				<div class="mg-l-20">
				<p class="tx-10 tx-spacing-1 tx-mont tx-medium tx-uppercase tx-white-8 mg-b-10">Total Active Polls</p>
				<p class="tx-24 tx-white tx-lato tx-bold mg-b-2 lh-1"><?PHP echo $polls; ?></p>
				<span class="tx-11 tx-roboto tx-white-6">&nbsp;</span>
				</div>
				</div>
				</div>
			</div><!-- col-3 -->
        </div><!-- row -->
		
		 <div class="row row-sm mg-t-20">
			<div class="col-sm-6 col-xl-3">
				<div class="card pd-0 bd-0 shadow-base">
					<div class="pd-x-30 pd-t-30 pd-b-15">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<h6 class="tx-13 tx-uppercase tx-inverse tx-semibold tx-spacing-1">Mothertongue Statistics Summary</h6>
							</div>
						</div><!-- d-flex -->
					</div>
					<div class="pd-x-15 pd-b-15">
						<div class="bd pd-20"><div id="flotPie1" class="ht-200 ht-sm-300"></div></div>
					</div>
				</div><!-- card -->
			</div>
			
			<div class="col-sm-6 col-xl-3">
				<div class="card pd-0 bd-0 shadow-base">
					<div class="pd-x-30 pd-t-30 pd-b-15">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<h6 class="tx-13 tx-uppercase tx-inverse tx-semibold tx-spacing-1">Religion Statistics Summary</h6>
							</div>
							
						</div><!-- d-flex -->
					</div>
					
					<div class="pd-x-15 pd-b-15">
						<div class="bd pd-20"><div id="flotPie2" class="ht-200 ht-sm-300"></div></div>
					</div>
					
				</div><!-- card -->
			</div>
			
			<div class="col-sm-6 col-xl-3">
				<div class="card pd-0 bd-0 shadow-base">
					<div class="pd-x-30 pd-t-30 pd-b-15">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<h6 class="tx-13 tx-uppercase tx-inverse tx-semibold tx-spacing-1">Gender Statistics Summary</h6>
							</div>
							
						</div><!-- d-flex -->
					</div>
					
					<div class="pd-x-15 pd-b-15">
						<div class="bd pd-20"><div id="flotPie4" class="ht-200 ht-sm-300"></div></div>
					</div>
					
				</div><!-- card -->
			</div>
			
			<div class="col-sm-6 col-xl-3">
				<div class="card pd-0 bd-0 shadow-base">
					<div class="pd-x-30 pd-t-30 pd-b-15">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<h6 class="tx-13 tx-uppercase tx-inverse tx-semibold tx-spacing-1">Age Statistics Summary</h6>
							</div>
							
						</div><!-- d-flex -->
					</div>
					
					<div class="pd-x-15 pd-b-15">
						<div class="bd pd-20"><div id="flotPie6" class="ht-200 ht-sm-300"></div></div>
					</div>
					
				</div><!-- card -->
			</div>
		</div>
		<div class="row row-sm mg-t-20">
			<div class="col-sm-6 col-xl-6 mg-t-20">
				<div class="card pd-0 bd-0 shadow-base">
					<div class="pd-x-30 pd-t-30 pd-b-15">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<h6 class="tx-13 tx-uppercase tx-inverse tx-semibold tx-spacing-1">Jobtitle Statistics Summary</h6>
							</div>
							
						</div><!-- d-flex -->
					</div>
					
					<div class="pd-x-15 pd-b-15">
						<div class="bd pd-20"><div id="flotPie3" class="ht-200 ht-sm-500" ></div></div>
					</div>
					
				</div><!-- card -->
			</div>
			<div class="col-sm-6 col-xl-6 mg-t-20">
				<div class="card pd-0 bd-0 shadow-base">
					<div class="pd-x-30 pd-t-30 pd-b-15">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<h6 class="tx-13 tx-uppercase tx-inverse tx-semibold tx-spacing-1">Province Statistics Summary</h6>
							</div>
							
						</div><!-- d-flex -->
					</div>
					
					<div class="pd-x-15 pd-b-15">
						<div class="bd pd-20"><div id="containerhs" class="ht-200 ht-sm-500" ></div></div>
					</div>
					
				</div><!-- card -->
			</div>
		</div>
		
		<?PHP /*
		<div class="row row-sm mg-t-20">	
			<div class="col-sm-6 col-xl-12">                 
            <div class="card bd-0 shadow-base pd-30">
              <div class="d-flex align-items-center justify-content-between mg-b-30">
                <div>
					<h6 class="tx-13 tx-uppercase tx-inverse tx-semibold tx-spacing-1">Upcoming Events</h6>
                </div>
                <a href="<?PHP echo base_url(); ?>events" class="btn btn-outline-info btn-oblong tx-11 tx-uppercase tx-mont tx-medium tx-spacing-1 pd-x-30 bd-2">See more</a>
              </div><!-- d-flex -->

              <table class="table table-valign-middle mg-b-0">
                <thead>
					<tr>
						<th>Title</th>
						<th>Start Date/Time</th>
						<th>End Date/Time</th>
						<th>Location</th>
						<th>Going</th>
						<th>Interested</th>
					</tr>
				</thead>
				<tbody>
					<?PHP 
						$orderbyevt['event.eventId'] = 'DESC';
						$limitevt = 5;
						$events = $this->events_model->get('',$orderbyevt,'','','',$limitevt,'',"",'','','', '','');
					if(count($events) > 0){ $i = 1;
					foreach($events as $row){
						$goingStatuscnt = $this->events_model->goingStatuscnt($row->eventId);
						$interestStatuscnt = $this->events_model->interestStatuscnt($row->eventId);
					?>
						<tr >
							<td><?PHP echo $row->title; ?></td>
							<td><b><?PHP echo $row->startDate; ?> <?PHP echo date('h:i a', strtotime($row->startTime)); ?></b></td>
							<td><b><?PHP echo $row->endDate; ?> <?PHP echo date('h:i a', strtotime($row->endTime)); ?></b></td>	
							<td><b><?PHP echo $row->locationDetails; ?></b></td>
							<td><b><?PHP echo $goingStatuscnt; ?></b></td>								
							<td><b><?PHP echo $interestStatuscnt; ?></b></td>
						</tr>
					<?php $i++; } } else { ?>
						<tr align="center">
							<td colspan="10">No Records Found</td>
						</tr>
					<?php } ?>                  
                </tbody>
              </table>
            </div><!-- card -->
          </div><!-- col-9 -->
		</div>
		
        <div class="row row-sm mg-t-20">
          <div class="col-sm-6 col-xl-12">            
            <div class="card bd-0 shadow-base pd-30 mg-t-20">
              <div class="d-flex align-items-center justify-content-between mg-b-30">
                <div>
					<h6 class="tx-13 tx-uppercase tx-inverse tx-semibold tx-spacing-1">Active Polls</h6>
                </div>
                <a href="<?PHP echo base_url(); ?>polls" class="btn btn-outline-info btn-oblong tx-11 tx-uppercase tx-mont tx-medium tx-spacing-1 pd-x-30 bd-2">See more</a>
              </div><!-- d-flex -->

              <table class="table table-valign-middle mg-b-0">
					<thead>
						<tr>
							<!--<th>Sno</th>-->
							<th>Title</th>
							<th>Answers</th>
							<th>Start Date</th>
							<th>End Date</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
						$orderbypoll['poll.pollingId'] = 'DESC';
						$limitpoll = 5;
						$polls = $this->polls_model->get('',$orderbypoll,'','','',$limitpoll,'',"",'','','', '','');
						if(count($polls) > 0){ $i = 1;
						foreach($polls as $row){
					?>
					<tr>
						<td><?PHP echo $row->pollingQuestion; ?></td>	
						<td>
							<?PHP 
								$pollAnswer = getPollsanswer($row->pollingId);
								if(!empty($pollAnswer)){
									$i=1;
									foreach($pollAnswer as $pans){
										$percentage = getTotalpollcnt($pans['pollingAnswerId'], $row->pollingId);
										echo "<span>".$i.") </span> <span class='tx-info tx-bold'>".$pans['answer']."</span> - <span class='tx-inverse'>".$percentage."</span><span class='tx-danger'>&nbsp;%</span>&nbsp;&nbsp;";
										//if($i == 2){
											echo "<br />";
										//}
										$i++;
									}
								}
							?>
						</td>			
						<td><?PHP echo date("d-m-Y", strtotime($row->startDate)); ?></td>
						<td><?PHP echo date("d-m-Y", strtotime($row->endDate)); ?></td>						
					</tr>
					<?php $i++; } } else { ?>
						<tr align="center">
							<td colspan="10">No Records Found</td>
						</tr>
					<?php } ?>
                 
					</tbody>
				</table>
            </div><!-- card -->
          </div><!-- col-9 -->
        </div><!-- row -->
		*/ ?>

<script src="<?PHP echo base_url(); ?>skin/default/js/flot/jquery.flot.js"></script>
<script src="<?PHP echo base_url(); ?>skin/default/js/flot/jquery.flot.pie.js"></script>
<script src="<?PHP echo base_url(); ?>skin/default/js/flot/jquery.flot.resize.js"></script>
<script src="<?PHP echo base_url(); ?>skin/default/js/flot-spline/jquery.flot.spline.js"></script>
<script src="https://envato.stammtec.de/themeforest/melon/plugins/flot/jquery.flot.tooltip.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>



<script type="text/javascript" >
$(function() {
  'use strict';
	/**************** PIE CHART *******************/
	/* Mothertongue Statistics Summary */
	var piedatamother = [
		<?php
		if(count($allmother) > 0){
			foreach($allmother as $mother){				
				echo "{ label:'".$mother->mothertongueName." (".$mother->mocount.")', data:[[".$mother->mothertongueId.",".$mother->mocount."]], color:'#".$mother->mothertongueColor."'},";
			}
		} 
		?>
	];

	$.plot('#flotPie1', piedatamother, {
		series: {
			pie: {
				show: true,
				radius: 1,
				label: {
					show: true,
					radius: 1,
					formatter: labelFormatter,
					background: {
						opacity: 0.9
					}
				}
				
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		},
		legend: {
			show: false
		}
	});
	
	/* Religion Statistics Summary */	
	var piedataReligion = [
		<?php
		if(count($allReligion) > 0){
			foreach($allReligion as $religion){				
				echo "{ label:'".$religion->religionName." (".$religion->recount.")', data:[[".$religion->religionId.",".$religion->recount."]], color:'#".$religion->religionColor."'},";
			}
		} 
		?>
	];
	
	$.plot('#flotPie2', piedataReligion, {
		series: {
			pie: {
				show: true,
				radius: 1,
				innerRadius: 0.3,
				label: {
					show: true,
					radius: 2/3,
					formatter: labelFormatter,
					background: {
						opacity: 0.9
					}
				}
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		},
		legend: {
			show: false
		}
	});
	
	/* Jobtitle Statistics Summary */
	var piedatajobTitle = [
		<?php
		if(count($allJoptitle) > 0){
			foreach($allJoptitle as $job){				
				echo "{ label:'".$job->jobtitleName." (".$job->jobcount.")', data:[[".$job->jobtitleId.",".$job->jobcount."]], color:'#".$job->jobtitleColor."'},";
			}
		} 
		?>
	];
	
	$.plot('#flotPie3', piedatajobTitle, {
		series: {
			pie: {
				show: true,
				radius: 1,
				innerRadius: 0.5,
				label: {
					show: true,
					radius: 2/3,
					threshold: 0.1					
				},
				background: {
					color: '#fff',
				}
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		},
		tooltip: true,
		tooltipOpts: {
			cssClass: "flotTip",
			content: "%p.0%, %s",
			shifts: {
				x: 20,
				y: 0
			},
			defaultTheme: false
		},
		legend: {
			show: false
		}
	});
	
	/* Gender Statistics Summary */
	var piedataGender = [
		<?php
		if(count($allGender) > 0){
			foreach($allGender as $gender){				
				echo "{ label:'".$gender->genderName." (".$gender->gendercount.")', data:[[".$gender->genderId.",".$gender->gendercount."]], color:'#".$gender->genderColor."'},";
			}
		} 
		?>
	];
	
	$.plot('#flotPie4', piedataGender, {
		series: {
			pie: {
				show: true,
				radius: 1,
				innerRadius: 0.4,
				label: {
					show: true,
					radius: 2/3,
					formatter: labelFormatter,
					threshold: 0.1
				}
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		},
		legend: {
			show: false
		}
	});
	
	/* Province Statistics Summary */
	var piedataProvince = [
		<?php
		if(count($allJoptitle) > 0){
			foreach($allJoptitle as $job){				
				echo "{ label:'".$job->jobtitleName." (".$job->jobcount.")', data:[[".$job->jobtitleId.",".$job->jobcount."]], color:'#".$job->jobtitleColor."'},";
			}
		} 
		?>
	];
	
	$.plot('#flotPie5', piedataProvince, {
		series: {
			pie: {
				show: true,
				radius: 1,
				innerRadius: 0.5,
				label: {
					show: true,
					radius: 2/3,
					threshold: 0.1					
				},
				background: {
					color: '#fff',
				}
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		},
		tooltip: true,
		tooltipOpts: {
			cssClass: "flotTip",
			content: "%p.0%, %s",
			shifts: {
				x: 20,
				y: 0
			},
			defaultTheme: false
		},
		legend: {
			show: false
		}
	});
	
	/* Age Statistics Summary */
	var piedataAge = [
		<?php
		if(count($allAge) > 0){
			$colors = array(0=>'#FE8FB9', 1=>'#FFD75C', 2=>'#4BC4D5', 3=>'#f4835b', 4=>'#ff0');
			$i = 0;
			foreach($allAge as $age){				
				echo "{ label:'".$age->range." (".$age->count.")', data:[['',".$age->count."]], color:'".$colors[$i]."'},";
				$i++;
			}
		} 
		?>
	];
	
	$.plot('#flotPie6', piedataAge, {
		series: {
			pie: {
				show: true,
				radius: 1,
				innerRadius: 0.3,
				label: {
					show: true,
					radius: 2/3,
					formatter: labelFormatter,
					background: {
						opacity: 0.9
					}
				}
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		},
		tooltip: true,
		tooltipOpts: {
			cssClass: "flotTip",
			content: "%p.0%, %s",
			shifts: {
				x: 20,
				y: 0
			},
			defaultTheme: false
		},
		legend: {
			show: false
		}
	});

	function labelFormatter(label, series) {
		return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
	}
	
	Highcharts.chart('containerhs', {
		chart: {
			type: 'pie'
		},
		title: {
			text: ''
		},
		subtitle: {
			text: ''
		},
		plotOptions: {
			series: {
				dataLabels: {
					enabled: true,
					format: '{point.name}: {point.y}'
				}
			}
		},

		tooltip: {
			headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
			pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> Users<br/>'
		},
		//$allCountry
		"series": [
			{
				"name": "Countries",
				"colorByPoint": true,
				"data": [
				<?PHP 
					if(count($allCountry) > 0){						
						foreach($allCountry as $con){							
							echo "{ name:'".htmlspecialchars($con->countryName)."', y:".$con->councount.", drilldown: '".htmlspecialchars($con->countryName)."'},";													
						}
					}
				?>
				]
			}
		],
		"drilldown": {
			"series": [
				<?PHP 
					if(count($allCountry) > 0){						
						foreach($allCountry as $con){							
							echo "{ 
								name:'".htmlspecialchars($con->countryName)."', 
								id:'".htmlspecialchars($con->countryName)."', 
								data:[";
								$allProvince = $CI->settings_model->getProvinceCount($con->countryId);
								foreach($allProvince as $province){
									echo "[
										'".$province->provinceName."',
										".$province->councount."
									],";
								}	
							echo "]
							},";													
						}
					}
				?>
			]
		}
	});
	
	$(window).resize(function(){
        var chart = $('#containerhs').highcharts();
        
        console.log('redraw');
        var w = $('#containerhs').closest(".wrapper").width()
        // setsize will trigger the graph redraw 
        chart.setSize(       
            w,w * (2/4),false
        );
     });
 });
</script>

