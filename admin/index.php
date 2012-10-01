<?php
/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Codjudge admin panel
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
        <li class="active"><a href="#">General</a></li>
        <li><a href="problems.php">Problems</a></li>
        <li><a href="scoring.php">Scoring</a></li>
      </ul>
      <div>
        <div>
          <form method="post" action="update.php">
          <?php
          	$query = "SELECT name, accept, c, cpp, java, python FROM prefs";
          	$result = mysql_query($query);
          	$fields = mysql_fetch_array($result);
          ?>
          <input type="hidden" name="action" value="settings"/>
          Name of event: <input name="name" type="text" value="<?php echo($fields['name']);?>"/><br/>
          <input name="accept" type="checkbox" <?php if($fields['accept']==1) echo("checked=\"true\"");?>/> <span class="label label-important">Accept submissions</span><br/>
          <h1><small>Languages</small></h1>
          <input name="c" type="checkbox" <?php if($fields['c']==1) echo("checked=\"true\"");?>/> C<br/>
          <input name="cpp" type="checkbox" <?php if($fields['cpp']==1) echo("checked=\"true\"");?>/> C++<br/>
          <input name="java" type="checkbox" <?php if($fields['java']==1) echo("checked=\"true\"");?>/> Java<br/>
          <input name="python" type="checkbox" <?php if($fields['python']==1) echo("checked=\"true\"");?>/> Python<br/><br/>
          <input class="btn" type="submit" name="submit" value="Save Settings"/>
          </form>
          <hr/>
          
          <form method="post" action="update.php">
          <input type="hidden" name="action" value="password"/>
          <h1><small>Change Password</small></h1>
          Old password: <input type="password" name="oldpass"/><br/>
          New password: <input type="password" name="newpass"/><br/><br/>
          <input class="btn" type="submit" name="submit" value="Change Password"/>
          </form>
          <hr/>
          
          <form method="post" action="update.php">
          <input type="hidden" name="action" value="email"/>
          <h1><small>Change Email</small></h1>
          <?php
          	$query = "SELECT email FROM users WHERE username='admin'";
          	$result = mysql_query($query);
          	$fields = mysql_fetch_array($result);
          ?>
          Email: <input type="email" name="email" value="<?php echo $fields['email'];?>"/><br/><br/>
          <input class="btn" type="submit" name="submit" value="Change Email"/>
          </form>
        </div>
      </div>
    </div> <!-- /container -->

<?php
	include('footer.php');
?>
