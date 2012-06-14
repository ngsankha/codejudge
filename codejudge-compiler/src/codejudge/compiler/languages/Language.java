/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Codejudge Compiler Server: The base interface for each language
 */
 
package codejudge.compiler.languages;

public interface Language {
	
	public void execute(); // method to override when executing a program
	public void compile(); // method to override when compiling a program
	
}
