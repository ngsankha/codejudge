<?php
/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Codjudge admin panel scoring system
 */
	require_once('../functions.php');
	if(!loggedin())
		header("Location: login.php");
	else if($_SESSION['username'] !== 'admin')
		header("Location: login.php");
	else
		include('header.php');
		connectdb();
?>
              <li class="active"><a href="#">Admin Panel</a></li>
              <li><a href="users.php">Users</a></li>
              <li><a href="logout.php">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
    
      <?php
        if(isset($_GET['changed']))
          echo("<div class=\"alert alert-success\">\nSettings Saved!\n</div>");
        else if(isset($_GET['passerror']))
          echo("<div class=\"alert alert-error\">\nThe old password is incorrect!\n</div>");
        else if(isset($_GET['derror']))
          echo("<div class=\"alert alert-error\">\nPlease enter all the details asked before you can continue!\n</div>");
      ?>
      <ul class="nav nav-tabs">
        <li><a href="#">General</a></li>
        <li><a href="problems.php">Problems</a></li>
        <li class="active"><a href="scoring.php">Scoring</a></li>
      </ul>
      <div>
        <div>
          Type out the score calculation method for each of the problems. The total score of the user will be calculated as a sum of individual scores for the problems by using the same formula on all the solved or attempted problems.<br/><br/>
          <b>You can use any of the following variables in your calculation:</b><br/>
          <code>$attempts</code> The number of attempts by the user for that problem.<br/><br/>

          <form method="post" action="update.php">
            <textarea style="height:200px;" class="span9" name="formula" id="text"></textarea><br/>
            <input class="btn btn-primary btn-large" type="submit" value="Update Formula"/>
          </form>
        </div>
      </div>
    </div> <!-- /container -->

<?php
	include('footer.php');
?>
