/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Codejudge Compiler Server: Compiler for the Python language
 */

package codejudge.compiler.languages;

import java.io.BufferedWriter;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;

import codejudge.compiler.TimedShell;

public class Python extends Language {
	
	String file, contents, dir;
	int timeout;
	
	public Python(String file, int timeout, String contents, String dir) {
		this.file = file;
		this.timeout = timeout;
		this.contents = contents;
		this.dir = dir;
	}

	public void compile() {
		try {
			BufferedWriter out = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(dir + "/" + file)));
			out.write(contents);
			out.close();
			// create the execution script
			out = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(dir + "/run.sh")));
			out.write("cd \"" + dir +"\"\n");
			out.write("chroot .\n");
			out.write("python " + file + "< in.txt > out.txt 2>err.txt");
			out.close();
			Runtime r = Runtime.getRuntime();
			Process p = r.exec("chmod +x " + dir + "/run.sh");
			p.waitFor();
			p = r.exec(dir + "/run.sh"); // execute the script
			TimedShell shell = new TimedShell(this, p, 3000);
			shell.start();
			p.waitFor();
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
	}

	public void execute() {
		// nothing to be done here
	}
}
