/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Codejudge Timer Shell that executes a commend with a timeout period
 */

package codejudge.compiler;

import codejudge.compiler.languages.Language;

public class TimedShell extends Thread {
	
	Language parent;
	Process p;
	long time;
	
	public TimedShell(Language parent, Process p, long time){
		this.parent = parent;
		this.p = p;
		this.time = time;
	}
	
	// Sleep until timeout and then terminate the process
	public void run() {
		try {
			sleep(time);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		try {
			p.exitValue();
			parent.timedout = false;
		} catch (IllegalThreadStateException e) {
			parent.timedout = true;
			p.destroy();
		}
	}
}