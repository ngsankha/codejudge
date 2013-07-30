/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Codejudge Compiler Server : Thread that runs on each request
 */

package codejudge.compiler;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.Socket;

import codejudge.compiler.languages.C;
import codejudge.compiler.languages.Cpp;
import codejudge.compiler.languages.Java;
import codejudge.compiler.languages.Language;
import codejudge.compiler.languages.Php;
import codejudge.compiler.languages.Python;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Date;
import java.util.Properties;

public class RequestThread extends Thread {
	
	Socket s; // socket connection
	int n; // request number
	File dir; // staging directory
	
	public RequestThread(Socket s, int n) {
		this.s=s;
		this.n=n;
		dir = new File("stage/" + n);
	}
	
	public void run() {
		dir.mkdirs(); // create staging directory
		try {
			BufferedReader in = new BufferedReader(new InputStreamReader(s.getInputStream()));
			PrintWriter out = new PrintWriter(s.getOutputStream(), true);
			// read rowid from the PHP script
			String rowid=in.readLine();
			BufferedReader dbconfigin=new BufferedReader(new FileReader("dbconfig"));
			Properties p=new Properties();
			p.load(dbconfigin);
			String host=p.getProperty("host");
			String database=p.getProperty("db");
			String username=p.getProperty("username");
			String password=p.getProperty("password");
			String url = "jdbc:mysql://"+host+":3306/"+database;
			DataBaseConnector dbin=new DataBaseConnector(rowid);
			Statement statement=dbin.dbconnect(url, username, password);
			dbin.init(statement);
			String file = dbin.getfilename();
			int timeout = (dbin.gettimeout());
			String contents = dbin.getsolution();
			String input = dbin.getinput();
			String lang = dbin.getlang();
			String output=dbin.getoutput();
			System.out.println("Compiling " + file + "...");
			// create the sample input file
			PrintWriter writer = new PrintWriter(new FileOutputStream("stage/" + n +"/in.txt"));
			writer.println(input);
			writer.close();
			Language l = null;
			// create the language specific compiler
			if(lang.equals("c"))
				l = new C(file, timeout, contents, dir.getAbsolutePath());
			else if(lang.equals("cpp"))
				l = new Cpp(file, timeout, contents, dir.getAbsolutePath());
			else if(lang.equals("java"))
				l = new Java(file, timeout, contents, dir.getAbsolutePath());
			else if(lang.equals("python"))
				l = new Python(file, timeout, contents, dir.getAbsolutePath());
			else if(lang.equals("php"))
				l = new Php(file, timeout, contents, dir.getAbsolutePath());
			l.compile(); // compile the file
			String errors = compileErrors();
			if(!errors.equals("")) { // check for compilation errors
				dbin.setstatus(statement, 1, errors); 
				out.println(errors);
			} else {
				// execute the program and return output
				l.execute();
				if(l.timedout){
					dbin.setstatus(statement, 2);
					
				}
				else {
					
					
					String user_output=execMsg();
					if(user_output.trim().equals(output))
						dbin.setstatus(statement, 3);
					else dbin.setstatus(statement, 4); 
					
				}
			}
			s.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
	
	// method to return the compiler errors
	public String compileErrors() {
		String line, content = "";
		try {
			BufferedReader fin = new BufferedReader(new InputStreamReader(new FileInputStream(dir.getAbsolutePath() + "/err.txt")));
			while((line = fin.readLine()) != null)
				content += (line + "\n");
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return content.trim();
	}
	
	// method to return the execution output
	public String execMsg() {
		String line, content = "";
		try {
			BufferedReader fin = new BufferedReader(new InputStreamReader(new FileInputStream(dir.getAbsolutePath() + "/out.txt")));
			while((line = fin.readLine()) != null)
				content += (line + "\n");
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return content.trim();
	}
	
}
