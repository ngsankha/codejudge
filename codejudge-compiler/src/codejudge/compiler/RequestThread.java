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
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.Socket;

import codejudge.compiler.languages.C;
import codejudge.compiler.languages.Cpp;
import codejudge.compiler.languages.Java;
import codejudge.compiler.languages.Language;
import codejudge.compiler.languages.Python;

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
			// read input from the PHP script
			String file = in.readLine();
			int timeout = Integer.parseInt(in.readLine());
			String contents = in.readLine().replace("$_n_$", "\n");
			String input = in.readLine().replace("$_n_$", "\n");
			String lang = in.readLine();
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
			l.compile(); // compile the file
			String errors = compileErrors();
			if(!errors.equals("")) { // check for compilation errors
				out.println("0");
				out.println(errors);
			} else {
				// execute the program and return output
				l.execute();
				if(l.timedout)
					out.println(2);
				else {
					out.println("1");
					out.println(execMsg());
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
