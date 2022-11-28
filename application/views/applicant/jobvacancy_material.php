<?php $this->load->view('applicant/header_material'); ?>

<body id="home" class="scrollspy">
    <!-- Navbar -->
    <div class="navbar-fixed">
        <nav class="black">
            <div class="nav-wrapper" style="color: rgb(255, 199, 44);">
                <img class="brand-logo left" src="<?=base_url()?>images/school_logo.png" height="50" width="50" class="img-circle" style="margin-top: 5px;margin-left: 8px;"/>
                <p style="margin-top: 0px;height: 2px;margin-block-start: 0em;line-height: 40px;width: 238px;margin-left: 64px; font-family: Avenir; font-size: 18px; font-weight: 700;">SAINT&nbsp;PEDRO&nbsp;POVEDA&nbsp;COLLEGE</p>
                <p style="margin-block-start: 0em;line-height: 41px;line-height: 41px;margin-left: 64px; font-family: Avenir; font-size: 17px;">Deus&nbsp;Scientiarum&nbsp;Dominus</p>
            </div>
        </nav>
    </div>

    <!-- Element Showed -->
    <div class="fixed-action-btn" id="menu" onclick="topFunction()"  style="bottom: 45px; right: 24px;display: none;">
        <a class="btn btn-floating btn-large cyan"><i class="material-icons">arrow_upward</i></a>
    </div>
  
    <!-- Section: Search -->
    <section id="search" class="section section-search yellow darken-2 white-text center scrollspy">
        <div class="container">
            <div class="row">
                <div class="col s12">
                    <h3 class="black-text" style="font-weight: bold;">Search Career</h3>
                    <div class="input-field valign-wrapper">
                        <input type="text" class="white black-text autocomplete" id="searchJob" style="margin-bottom: 0px;width: calc(100% - 10rem);    margin-right: 6px; padding-left: 15px;" />
                        <button class="btn " style="margin-right: 6px; background-color: #5cb85c !important; border-color: #5cb85c; white-space: nowrap; overflow: hidden; width: 15%" id="searchJobBtn"><i class="material-icons left" style="margin-right: 0px !important">search</i><b>Search</b></button>
                        <button class="btn " style="background-color: #d9534f !important; border-color: #d9534f; white-space: nowrap; overflow: hidden; width: 13%" id="clearSearchbtn"><i class="material-icons left"  style="margin-right: 0px !important">highlight_off</i><b>Clear</b></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Popular Places -->
    <section id="popular" class="section section-popular scrollspy">
        <div style="margin-right: 5%;margin-left: 5%;">
            <div class="row" id="jobs">
                
            </div>
        </div>
    </section>
</body>

<div id="modal1" class="modal modal-fixed-footer" style="overflow-y: visible;">
<div class="modal-content">
    <img class="brand-logo left" src="<?=base_url()?>images/school_logo.png" height="50" width="50" class="img-circle" style="margin-top: 5px;margin-left: 8px;"/>
    <p style="margin-top: 0px;height: 2px;margin-block-start: 0em;line-height: 40px;width: 238px;margin-left: 64px; font-family: Avenir; font-size: 16px; font-weight: 600">SAINT&nbsp;PEDRO&nbsp;POVEDA&nbsp;COLLEGE</p>
    <p style="margin-block-start: 0em;line-height: 41px;line-height: 41px;margin-left: 64px; font-family: Avenir;">Deus&nbsp;Scientiarum&nbsp;Dominus</p>
    <h4 id="modalTitle" class="center" style="border-bottom: 1px solid #fbc02d ;padding-bottom: 12px;border-bottom-width: thick;">Applicant Sign-Up</h4>
    <div class="row" id="applicantContent" >

    </div>
</div>
<div class="modal-footer" style="height: 80px !important">
    <div style="text-align: center">
         <a href="#!" id="btn_modal_close" class="modal-close waves-effect waves-green btn-flat red white-text">Close</a>
          <a class="waves-effect waves-light btn yellow darken-2 black-text" href="#!" id='logsubmit' ><i class="material-icons right">exit_to_app</i>
          Proceed</a>
          <a class="waves-effect waves-light btn yellow darken-2 black-text" href="#!" id='logsubmitexisting' style="display: none;"><i class="material-icons right">exit_to_app</i>
          Proceed</a><br>
          <div id="login_here_div">
            <span><b>Have an account with us? <a style="cursor: pointer;" id="login_here">Login here</a></b></span>
          </div>
    </div>
</div>
</div>

  <!-- Footer -->
  <footer class="section black darken-2 white-text center">
    <p class="flow-text black-text"></p>
  </footer>

 <script>
    var toks = hex_sha512(" ");
    $('.tap-target').tapTarget();
    $(document).ready(function(){
        JobTable('', 1);
        countAvailableJobs();
        checkJobAvailability();
        searchInputComplete();
        $('.modal').modal();
        $('input.autocomplete').autocomplete({
          data: {
            "Apple": null,
            "Microsoft": null,
            "Google" : 'https://placehold.it/250x250'
          },
        });
    });

    function checkJobAvailability(){
        $.ajax({
            url: "<?= site_url('applicant/checkjobs')?>",
            success:function(response){
             }
        });
    }

    function countAvailableJobs(){
    $.ajax({
          url: "<?=site_url('applicant/countAvailableJobs')?>",
          success:function(response){
            // $("#available_jobs").text(response);
          }
        });
    }

    function JobTable(word, page){
      var string = word;
        $.ajax({
            type: 'POST',
            data:{page: GibberishAES.enc( page, toks), string: GibberishAES.enc( string, toks), toks:toks},
            url: "<?= site_url('applicant/loadJobMaterilize')?>",
            success:function(response){
                $("#jobs").html(response);
            }
        });
    }

    function searchInputComplete(){
        $.ajax({
            type: 'POST',
            data:{},
            dataType: "json",
            url: "<?= site_url('applicant/getListOfJobs')?>",
            success:function(response){

                var jobsArray = response;
                var jobsData = {};
                for (var i = 0; i < jobsArray.length; i++) {

                  jobsData[jobsArray[i].title] = null; //countryArray[i].flag or null
                }
                $('input.autocomplete').autocomplete({
                  data: jobsData
                });
                
            }
        });
    }

    mybutton = document.getElementById("menu");

    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        mybutton.style.display = "block";
      } else {
        mybutton.style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0; // For Safari
      document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    // Sidenav
    const sideNav = document.querySelector('.sidenav');
    M.Sidenav.init(sideNav, {});

    // Slider
    const slider = document.querySelector('.slider');
    M.Slider.init(slider, {
      indicators: false,
      height: 500,
      transition: 500,
      interval: 6000
    });

    // Material Boxed
    const mb = document.querySelectorAll('.materialboxed');
    M.Materialbox.init(mb, {});

    // ScrollSpy
    const ss = document.querySelectorAll('.scrollspy');
    M.ScrollSpy.init(ss, {});



  </script>
</body>

</html>