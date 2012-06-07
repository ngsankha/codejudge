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

public class RequestThread extends Thread {
	
	Socket s;
	int n;
	File dir;
	
	public RequestThread(Socket s, int n) {
		this.s=s;
		this.n=n;
		dir = new File("stage/" + n);
	}
	
	public void run() {
		dir.mkdirs();
		try {
			BufferedReader in = new BufferedReader(new InputStreamReader(s.getInputStream()));
			PrintWriter out = new PrintWriter(s.getOutputStream(), true);
			String file = in.readLine();
			String contents = in.readLine().replace("$_n_$", "\n");
			String input = in.readLine().replace("$_n_$", "\n");
			String lang = in.readLine();
			System.out.println("Compiling " + file + "...");
			PrintWriter writer = new PrintWriter(new FileOutputStream("stage/" + n +"/in.txt"));
			writer.println(input);
			writer.close();
			if(lang.equals("c")) {
				C c = new C(file, contents, dir.getAbsolutePath());
				c.compile();
				String errors = compileErrors();
				if(errors.equals("")) {
					c.execute();
					String output = execMsg();
					out.println("1");
					out.println(output);
				} else {
					out.println("0");
					out.println(errors);
				}
			} else if(lang.equals("cpp")) {
				Cpp cpp = new Cpp(file, contents, dir.getAbsolutePath());
				cpp.compile();
				String errors = compileErrors();
				if(errors.equals("")) {
					cpp.execute();
					String output = execMsg();
					out.println("1");
					out.println(output);
				} else {
					out.println("0");
					out.println(errors);
				}
			} else if(lang.equals("java")) {
				Java java = new Java(file, contents, dir.getAbsolutePath());
				java.compile();
				String errors = compileErrors();
				if(errors.equals("")) {
					java.execute();
					String output = execMsg();
					out.println("1");
					out.println(output);
				} else {
					out.println("0");
					out.println(errors);
				}
			} else if(lang.equals("python")) {
				Python py = new Python(file, contents, dir.getAbsolutePath());
				py.execute();
				String errors = compileErrors();
				if(errors.equals("")) {
					String output = execMsg();
					out.println("1");
					out.println(output);
				} else {
					out.println("0");
					out.println(errors);
				}
			}
			s.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
	
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
