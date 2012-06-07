package codejudge.compiler;

import java.io.BufferedWriter;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;

public class Python {
	
	String file, contents, dir;
	
	public Python(String file, String contents, String dir) {
		this.file = file;
		this.contents = contents;
		this.dir = dir;
	}

	public void execute() {
		try {
			BufferedWriter out = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(dir + "/" + file)));
			out.write(contents);
			out.close();
			out = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(dir + "/run.sh")));
			out.write("cd \"" + dir +"\"\n");
			out.write("python " + file + "< in.txt > out.txt 2>err.txt");
			out.close();
			Runtime r = Runtime.getRuntime();
			Process p = r.exec("chmod +x " + dir + "/run.sh");
			p.waitFor();
			p = r.exec(dir + "/run.sh");
			p.waitFor();
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
	}
}
