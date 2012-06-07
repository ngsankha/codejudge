package codejudge.compiler;

import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;

public class CodejudgeCompiler {
	
	public static void main(String args[]) {
		int n=0;
		try {
			ServerSocket server = new ServerSocket(3029);
			System.out.println("Codejudge compilation server running ...");
			while(true) {
				n++;
				Socket s = server.accept();
				RequestThread request = new RequestThread(s, n);
				request.start();
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		
	}
}
