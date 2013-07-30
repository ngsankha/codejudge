package codejudge.compiler;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

/*
 * @author devenbhooshan
 * 
 */
public class DataBaseConnector {
	private int rowid;
	private String file;
	private String lang;
	private int timeout;
	private String solution;
	private String output;
	private String input;
	
	public void init(Statement statement){
		try {
			ResultSet resultSet = statement
			          .executeQuery("select * from solve where sl="+rowid);
			resultSet.next();
			
			this.solution = resultSet.getString("soln");
			this.lang= resultSet.getString("lang");
			this.file = resultSet.getString("filename");
			int problem_id=resultSet.getInt("problem_id");
			
			resultSet = statement
			          .executeQuery("select input,output,time from problems where sl="+problem_id);
			resultSet.next();
			this.input = resultSet.getString("input");
			this.output = resultSet.getString("output");
			this.timeout = resultSet.getInt("time");
			resultSet.close();
			
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
	}
	public DataBaseConnector(String id){
		this.rowid=Integer.parseInt(id);
		
		//System.out.println(rowid);
	}
	
	public Statement dbconnect(String url,String username,String password){
		Connection connect=null;
		Statement statement=null;
	      // Setup the connection with the DB
	      try {
	    	  Class.forName("com.mysql.jdbc.Driver");
			connect = DriverManager.getConnection(url, username, password);
			statement = connect.createStatement();
			
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	      //Connection connect = DriverManager.getConnection("
	      catch (ClassNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return statement;
		
	}
	
	
	public String getsolution(){
		
		return this.solution;
		
	}
	public String getlang(){
		
		return this.lang;
		
	}
	public String getfilename(){
		
		return this.file;
		
	}
	public String getinput(){
		
		return this.input;
	}
	public String getoutput(){
		return this.output;
		
	}

	public int gettimeout( ) {
		return this.timeout;
	}
	public void setstatus(Statement statement,int status,String errors){
		ResultSet resultSet;
		try {
			resultSet = statement
			          .executeQuery("update solve set status="+(status)+",CompileStatus="+errors+"  where sl="+rowid);
			if(!resultSet.rowUpdated())
			{
				System.out.println("Something failed");
			}
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
	}
	public void setstatus(Statement statement,int status){
		ResultSet resultSet;
		try {
			resultSet = statement
			          .executeQuery("update solve set status="+(status)+"  where sl="+rowid);
			if(!resultSet.rowUpdated())
			{
				System.out.println("Something failed");
			}
			
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
	}
}
