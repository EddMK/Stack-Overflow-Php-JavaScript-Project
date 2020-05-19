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
		<script src="lib/jquery-3.4.1.min.js" type="text/javascript"></script>
		<script>
			let number,period;
			document.write(period);
			function numberChange(){
				number = $("#numbers").val();
				document.write(number);
			}
			
			function periodeChange(){
				period = $("#period").val();
				document.write(period);
			}
			
		</script>
    </head>
    <body>
        <?php include('menu.html'); ?>
		
		<label>Period : Last</label>

		<select id="numbers" onchange="numberChange()">
			<?php for($i = 1; $i<100; $i ++){ ?>
				<option value="<?= $i ?>"  id="number"  ><?= $i ?></option>
			<?php } ?>
		</select>
		
		<select id="period" onchange="periodeChange()">
		  <option value="jour">Jour(s)</option>
		  <option value="semaine">Semaine(s)</option>
		  <option value="mois">Mois</option>
		  <option value="annee">Année(s)</option>
		</select>
		
		<canvas id="myChart"></canvas>
		<script>
			var ctx = document.getElementById('myChart').getContext('2d');
			var chart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'bar',

				// The data for our dataset
				data: {
					labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
					datasets: [{
						label: 'My First dataset',
						backgroundColor: 'rgb(255, 99, 132)',
						borderColor: 'rgb(255, 99, 132)',
						data: [0, 10, 5, 2, 20, 30, 45]
					}]
				},

				// Configuration options go here
				options: {}
			});
		</script>
    </body>
</html>