<?php
<!DOCTYPE html>

<!-- Navbar goes here -->
<html>
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation" id="navbar">
      <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><img src="images/csteachingtips.png" id="cstt-logo"/></a>
      </div>

      <!-- NAV BAR -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tips<b class="caret"></b></a>
                <ul class="dropdown-menu">
                      <li><a href="#">Delivering Content</a></li>
                      <li><a href="#">Managing & Assessing</a></li>
                      <li><a href="#">Organizing Curriculum</a></li>
                      <li class="divider"></li>
                      <li><a href="#">Browse All</a></li>
                  </ul>
              </li>
              <li><a href="#">About</a></li>
              <li><a href="#">Contribute</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
              <li><a href="#">Login</a></li>
          </ul>
          <form class="navbar-form navbar-right" role="search">
              <div class="form-group">
                  <input type="text" class="form-control" placeholder="Search">
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
          </form>
      </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

</html>
<!-- End of navbar -->
?>