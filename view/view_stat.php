<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Stuck Overflow</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
		<script src="https://kit.fontawesome.com/9f16cf7640.js" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
		<script type="text/javascript" src="lib/jquery-3.5.1.min.js"></script>
		<!--
		<script>
			let number,period;
			
			$(function(){
				$( "#numbers" ).change(function() {
					number = $("#numbers").val();
					//period = $("#period").val()
					alert(number+"  " + period);
				});
				$( "#period" ).change(function() {
					//number = $("#numbers").val();
					period = $("#period").val()
					alert(number+"  " + period);
				});			
			});
		</script>
		-->
    </head>
    <body>
        <?php include('menu.html'); ?>
		
		<label>Period : Last</label>

		<select id="numbers">
			<?php for($i = 1; $i<100; $i ++){ ?>
				<option value="<?= $i ?>" ><?= $i ?></option>
			<?php } ?>
		</select>
		
		<select id="period">
		  <option value="jour">Jour(s)</option>
		  <option value="semaine">Semaine(s)</option>
		  <option value="mois">Mois</option>
		  <option value="annee">Année(s)</option>
		</select>
		
		<canvas id="myChart"></canvas>
		<script>
			let number,period,dateLimit,abcisse,ordonnee;
			var chartx;
			$("#numbers").val(3);
			$("#period").val("mois");
			number = 3;
			period = "mois";
			dateLimit = calculDifference(3,"mois");
			dateLimit = changeFormat(dateLimit);
			$.post("post/graph",{dateLimit : dateLimit}, function(donnees){
						var obj = jQuery.parseJSON(donnees);
						abcisse = obj.users
						ordonnee = obj.values;
						chartx = showGraphic(abcisse,ordonnee);
			});
			$(function(){
				$("#numbers").change(function() {
					$("#table").empty();
					number = $("#numbers").val();
					dateLimit = calculDifference(number,period);
					dateLimit = changeFormat(dateLimit);
					$.post("post/graph",{dateLimit : dateLimit}, function(donnees){
						var obj = jQuery.parseJSON(donnees);
						abcisse = obj.users
						ordonnee = obj.values;
						chartx.destroy();
						chartx = showGraphic(abcisse,ordonnee);
					});
				});
				$("#period").change(function() {
					$("#table").empty();
					period = $("#period").val();
					dateLimit = calculDifference(number,period);
					dateLimit = changeFormat(dateLimit);
					$.post("post/graph",{dateLimit : dateLimit}, function(donnees){
						var obj = jQuery.parseJSON(donnees);
						abcisse = obj.users
						ordonnee = obj.values;
						chartx.destroy();
						chartx = showGraphic(abcisse,ordonnee);
					});					
				});	
			});
			
			function changeFormat(date){
				var day = date.getDate();
				var month = date.getMonth()+1;
				var year = date.getFullYear();
				var hour = date.getHours();
				var minutes = date.getMinutes();
				var secondes = date.getSeconds();
				return year + '-' + month + '-' + day+ ' '+hour+':'+minutes+':'+secondes;
			}
			
			function calculDifference(number,period){
				var d = new Date();
				if(period == "annee"){
					d.setFullYear(d.getFullYear()-number);
				}else if (period == "mois"){
					d.setMonth(d.getMonth()-number);
				}else if (period == "semaine"){
					number = 7*number;
					d.setDate(d.getDate()-number);
				}else{
					d.setDate(d.getDate()-number);
				}
				return d;		
			}
			
			
				function showGraphic(abc,ord){
					var ctx = document.getElementById('myChart').getContext('2d');
					
					var chart = new Chart(ctx, {
								// The type of chart we want to create
								type: 'bar',

								// The data for our dataset
								data: {
									labels: abc,
									
									datasets: [{
										label: 'My First dataset',
										backgroundColor: 'rgb(255, 99, 132)',
										borderColor: 'rgb(255, 99, 132)',
										data:ord
									}]
								},

								// Configuration options go here
								options: {
									onClick: handleClick
								}
							});
					
					return chart;

				}
				
				function handleClick(evt) 
				{ 
					var activeElement = chartx.getElementAtEvent(evt);
					if(!jQuery.isEmptyObject( activeElement )){
						$("#table").empty();
						var pseudo_choisi = chartx.data.labels[activeElement[0]._index];
						$.post("post/actions",{dateLimit : dateLimit, pseudo_choisi : pseudo_choisi}, function(donnees){
							var retour_table = '<thead><tr><th>Moment</th><th>Type</th><th>Question</th></tr></thead><tbody>';
							$.each(JSON.parse(donnees),function(key,value){
								retour_table += '<tr>';
								retour_table += '<td>'+value.moment+'</td>';
								retour_table += '<td>'+value.titre+'</td>';
								retour_table += '<td>'+value.type+'</td>';
								retour_table += '</tr>';
							});
							retour_table += '</tbody>';
							$("#table").append(retour_table);
						});
					}
				}
		</script>
		
			<table id="table">
			</table>

    </body>
</html>