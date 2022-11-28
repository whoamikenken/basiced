<center><button class="btn btn-primary" id="PrintSummary">Print</button></center>
<br><br>
<div class="row">
<?php foreach ($counts as $key => $value): ?>
    <div class="col-md-6">
    	<div class="panel" style="border: #337ab7 !important;height: 476px !important;">
        	<div class="panel-heading" style="background-color: #337ab7!important;color: white;">
				<span class="nav-text" style="font-weight: bold;font-size: 14px;letter-spacing: 1px;"><?= $key ?></span>
        	</div>
        	<div class="panel-body" style="padding-left: 0px;padding-right: 0px;"> 
			    <span>&nbsp;&nbsp;Respondee <?= $value['count'] ?></span><br>
			    <canvas id="<?= str_replace("?","",str_replace(' ', '', $key)) ?>"></canvas>
			</div>
		</div>
	</div>
<?php endforeach ?>
</div>

<script type="text/javascript">
	var list = JSON.parse('<?php echo json_encode($counts); ?>');
	$.each(list,function(index, value){
		var chartType = '';
		var scale = '';
		if (value.type == "YN") {
			chartType = "bar";
			scale = JSON.parse('{"yAxes": [{"ticks": {"min": 0,"precision":0}}]}');
		}else{
			chartType = "pie";
		}
		var title = index.replace(/\s/g, "").replace('?', '');
		var labelData = [];
		var datasetData = [];
		var graphColors = [];
		var dataSetBar = [];
		$.each(value.surveyAns,function(label, data){
			var randomR = Math.floor((Math.random() * 130) + 100);
		    var randomG = Math.floor((Math.random() * 130) + 100);
		    var randomB = Math.floor((Math.random() * 130) + 100);
		  
		    var graphBackground = "rgb(" 
		            + randomR + ", " 
		            + randomG + ", " 
		            + randomB + ")";
			if (value.type == "YN") {

				dataSetBar.push({
		            label: label, 
		            data:  [data],
		            backgroundColor: graphBackground
		        });

			}else{
				labelData.push(label);
				datasetData.push(data);
			    graphColors.push(graphBackground);
			}
		});
	    var ctx = $('#'+title);
		var height = $('#'+title).parent().height();
		var width = $('#'+title).parent().width();
		$('#'+title).attr('height', height);
		$('#'+title).attr('width', width);
		if (value.type == "YN") {
			var myChart = new Chart(ctx, {
			    type: chartType,
			    data: {
						datasets: dataSetBar,
						labels: [""]
					},
					options: {
						responsive: true,
			            scales: scale
					}
			});
		}else if(value.type == "TIME"){
			var myChart = new Chart(ctx, {
			    type: 'line',
			    data: {
						labels: labelData,
						datasets: [{
							label: 'Time',
							fill: false,
							backgroundColor: 'rgb(76, 130, 211)',
							borderColor: 'rgb(7, 81, 191)',
							data: datasetData,
						}]
					},
					options: {
						responsive: false,
						title: {
							display: false,
							text: 'Min and Max Settings'
						},
						legend: {
					        display: false
					    },
						scales: {
							yAxes: [{
								ticks: {
									// the data minimum used for determining the ticks is Math.min(dataMin, suggestedMin)
									suggestedMin: 1
								}
							}]
						}
					}
			});
		}else{
			var myChart = new Chart(ctx, {
			    type: chartType,
			    data: {
						datasets: [{
							data: datasetData,
							backgroundColor: graphColors,
							labels: labelData,
						}],
						labels: labelData
					},
					options: {
						responsive: true,
			            scales: scale
					}
			});
		}

	});

	$("#PrintSummary").click(function(){  
        html2canvas($("#table")).then(canvas => {
        	var imgData = canvas.toDataURL('image/png');
			var imgWidth = 210; 
			var pageHeight = 295;  
			var imgHeight = canvas.height * imgWidth / canvas.width;
			var heightLeft = imgHeight;
			var doc = new jsPDF('p', 'mm');
			var position = 10; // give some top padding to first page

			doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
			heightLeft -= pageHeight;

			while (heightLeft >= 0) {
			  position += heightLeft - imgHeight; // top padding for other pages
			  doc.addPage();
			  doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
			  heightLeft -= pageHeight;
			}
			doc.save( 'Survey Summary.pdf');

            // var pdf = new jsPDF("p", 'mm', 'a4');
            // var pageWidth = pdf.internal.pageSize.width;
            // var pageHeight = pdf.internal.pageSize.height;

            // var widthRatio = pageWidth / canvas.width;
            // var heightRatio = pageHeight / canvas.height;
            // var ratio = widthRatio > heightRatio ? heightRatio : widthRatio;

            // var canvasWidth = canvas.width * ratio;
            // var canvasHeight = canvas.height * ratio;

            // var marginX = (pageWidth - canvasWidth) / 2;
            // var marginY = (pageHeight - canvasHeight) / 2;
            
            // pdf.setFillColor(0,0,0,0);
            // pdf.rect(10, 10, 150, 160, "F");
            // pdf.addImage(canvas, 'PNG', marginX, marginY);
            // pdf.save("Survey Summary.pdf");
        });
    });
</script>