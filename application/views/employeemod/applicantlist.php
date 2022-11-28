<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="form-inline">
                    <h5><b style="font-weight: 1500">Sort by: </b>&emsp;
                        <input type="radio" class="form-check-input" name="radSort" value="sortCurrent" checked>Current&emsp;
                        <input type="radio" class="form-check-input" name="radSort" value="sortHistory"> History&emsp;
                        <input type="radio" class="form-check-input" name="radSort" value="sortViewing"> For Viewing</h5>
                </div>
                <div class="panel animated fadeIn">
                    <div class="panel-heading"><h4><b>Applicant List</b></h4></div>
                    <br>
                    <div id="employeelist">
                        
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script>
    $(document).ready(function(){
        loadApplicantList();
    });

    $("input[name='radSort']").click(function(){
        if($(this).val() == "sortCurrent"){
            loadApplicantList();
        }else if($(this).val() == "sortHistory"){
            loadApplicantListHistory();
        }else{
            loadApplicantListForViewing();
        }
    })

    function loadApplicantList(){
        $.ajax({
            url: $("#site_url").val() + "/employeemod_/applicantlist",
            success:function(res){
                $("#employeelist").html(res);
            }
        })
    }

    function loadApplicantListHistory(){
        $.ajax({
            url: $("#site_url").val() + "/employeemod_/applicantlisthistory",
            success:function(res){
                $("#employeelist").html(res);
            }
        })
    }

    function loadApplicantListForViewing(){
        $.ajax({
            url: $("#site_url").val() + "/employeemod_/applicantlistforviewing",
            success:function(res){
                $("#employeelist").html(res);
            }
        })
    }
</script>