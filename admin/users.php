<?php
/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Shows the list of users
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
              <li><a href="index.php">Admin Panel</a></li>
              <li class="active"><a href="#about">Users</a></li>
              <li><a href="logout.php">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
    <?php
        if(isset($_GET['banned']))
          echo("<div class=\"alert alert-error\">\nThe user has been banned.\n</div>");
        else if(isset($_GET['unbanned']))
          echo("<div class=\"alert alert-success\">\nThe user has been unbanned.\n</div>");
    ?>
    Below is a list of users registered. You can view the details of the user or ban him.
    <table class="table table-striped">
      <thead><tr>
        <th>Name</th>
        <th>Solved</th>
        <th>Attempted</th>
        <th>Action</th>
      </tr></thead>
      <tbody>
      <?php
        $query = "SELECT username, status FROM users WHERE username!='admin'";
        $result = mysql_query($query);
       	while($row = mysql_fetch_array($result)) {
       		// lists all the users
       		$sql = "SELECT * FROM solve WHERE (status='2' AND username='".$row['username']."')";
       		$res = mysql_query($sql);
       		echo("<tr><td><a href=\"profile.php?uname=".$row['username']."\">".$row['username']);
       		if($row['status'] == 0)
       			echo("</a> <span class=\"label label-important\">Banned</span>");
       		echo("</td><td><span class=\"badge badge-success\">".mysql_num_rows($res));
       		$sql = "SELECT * FROM solve WHERE (status='1' AND username='".$row['username']."')";
       		$res = mysql_query($sql);
       		echo("</span></td><td><span class=\"badge badge-warning\">".mysql_num_rows($res)."</span></td><td>");
       		if($row['status'] == 1)
       			echo("<a href=\"update.php?action=ban&username=".$row['username']."\" class=\"btn btn-mini btn-danger\">Ban</a></td></tr>\n");
       		else if($row['status'] == 0)
       			echo("<a href=\"update.php?action=unban&username=".$row['username']."\" class=\"btn btn-mini btn-danger\">Unban</a></td></tr>\n");
       	}
      ?>
      </tbody>
      </table>
    </div> <!-- /container -->

<?php
	include('footer.php');
?>
