<?php

/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
?>


<div class="panel animated fadeIn delay-1s">
    <div class="panel-heading"><h4><b>Web Check-In Detail</b></h4></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6 col-sm-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Employee ID</label>
                                    <div class="col-sm-8">
                                        <h4 style="color: blue"><?= $record[0]['userid'] ?></h4>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Name</label>
                                    <div class="col-sm-8">
                                        <h4 style="color: blue"><?= Globals::_e($record[0]['fullname']) ?></h4>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Log Type</label>
                                    <div class="col-sm-8">
                                        <h4 style="color: blue"><?= Globals::_e($record[0]['log_type']) ?></h4>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Time In</label>
                                    <div class="col-sm-8">
                                        <h4 style="color: blue"><?= $record[0]['localtimein'] ?></h4>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">I.P</label>
                                    <div class="col-sm-8">
                                        <h4 style="color: blue"><?= $record[0]['ip'] ?></h4>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Country</label>
                                    <div class="col-sm-8">
                                        <h4 style="color: blue"><?= Globals::_e($record[0]['country']) ?></h4>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">State</label>
                                    <div class="col-sm-8">
                                        <h4 style="color: blue"><?= Globals::_e($record[0]['state']) ?></h4>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Locallity</label>
                                    <div class="col-sm-8">
                                        <h4 style="color: blue"><?= Globals::_e($record[0]['city']) ?></h4>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row" id="imageRow">
                        <h2 style="text-align: center;">CHECK IN IMAGE</h2>
                        <center>
                            <div id="loadImage"><h2>Loading Image</h2></div>
                            
                        </center>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <h2 style="text-align: center;">LOCATION</h2>
                    <div id="map" style="width:100%;height:600px;">
                        
                    </div>
                </div> 
            </div>
        </div><br><br><br><br>                                                                
    </div>
</div>
<script>
    var lat = "<?= $record[0]['lat'] ?>";
    var long = "<?= $record[0]['long'] ?>";
    $(document).ready(function(){
        initMap();
        if ($("#image").attr("width") > $("#imageRow").width()) {
            $("#image").attr("width", $("#imageRow").width());
        }
        setTimeout(function(){ $('.panel').removeClass("animated fadeIn delay-1s");}, 3000);
        setTimeout(function() {
            getImage();
        }, 1000);
    });

   function initMap() {
    var latlong = { lat: Number(lat), lng: Number(long) }
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: latlong
        });

        var marker = new google.maps.Marker({
          position: latlong,
          map: map,
          title: 'Location'
        });
   }

   function getImage(){
    console.log("wew");
        $.ajax({
            url: $("#site_url").val() + "/webcheckin_/getImageRecordWeb",
            type: "POST",
            data:{id:GibberishAES.enc("<?= $recordID ?>", toks), toks:toks},
            success:function(response){
                $("#loadImage").html(response);
            }
        });
    }

    function employeeList(type){
        $.ajax({
            url: $("#site_url").val() + "/webcheckin_/loadEmployeeListDropdownm",
            type: "POST",
            data:{type:type},
            success:function(response){
                $("#employeeFilter").html(response);
                $("#employeeFilter").trigger("chosen:updated");
            }
        });
    }

</script>