<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
 
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="http://clutchgaming.co.nz/"><h1>CG</h1></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
            <li><a href="index.php">Home</a></li>
            <li><a href="#bio">Bio</a></li>
            <li><a href="#twitch">Watch</a></li>
            <li><a href="#schedule">Schedule</a></li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li><a href="http://twitch.tv/clutchnz" target="_blank"><i class="fa fa-twitch fa-2x"></i></a></li>
        <li><a href="https://www.facebook.com/clutchgamingnz/" target="_blank"><i class="fa fa-facebook fa-2x"></i></a></li>
        <li><a href="http://youtube.com/edenlol" target="_blank"><i class="fa fa-youtube fa-2x"></i></a></li> 
    </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav> <!-- end #nav -->


<script>
    $(".navbar-collapse a[href^='#']").on('click', function(e) {

   // prevent default anchor click behavior
   e.preventDefault();

   // store hash
   var hash = this.hash;

   // animate
   $('html, body').animate({
       scrollTop: $(hash).offset().top
     }, 500, function(){

       // when done, add hash to url
       // (default click behaviour)
       window.location.hash = hash;
     });

});
    
</script>