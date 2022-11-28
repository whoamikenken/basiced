<div class="panel  animated fadeIn delay-1s">
    <div class="panel-heading"><h4><b>Inhouse Seminar</b></h4></div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover table-condensed" id="seminarTable" width="100%">
            <thead>                            
                <tr style="background-color: #FFC72C;">
                    <th width='10%' align="center"><b>Actions</b></th>
                    <th><b>Username</b></th>
                    <th><b>Workshop</b></th>
                    <th><b>Title</b></th>
                    <th><b>Category</b></th>
                    <th><b>Date</b></th>
                    <th><b>Time</b></th>
                    <th><b>Organizer</b></th>
                    <th><b>Venue</b></th>
                    <th><b>Location</b></th>
                    <!--<th><b>Registration</b></th>
                    <th><b>Deadline</b></th>-->
                </tr>
            </thead>
            <tbody>

            </tbody>
            
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var table = $('#seminarTable').DataTable({
        });
        new $.fn.dataTable.FixedHeader(table);
    });
</script>