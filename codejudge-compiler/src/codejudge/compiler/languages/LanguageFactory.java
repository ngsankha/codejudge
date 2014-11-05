package codejudge.compiler.languages;

public class LanguageFactory {
	
	
	
	private LanguageFactory(){
		
	}
	
	public static Language getInstance(String language,String file,int timeOut,String contents,String absolutePath){
		if(language.equals(Language.C_LANGUAGE))
			return new C(file, timeOut, contents, absolutePath);
		else if(language.equals(Language.CPP_LANGUAGE))
			return new Cpp(file, timeOut, contents, absolutePath);
		else if(language.equals(Language.JAVA_LANGUAGE))
			return new Java(file, timeOut, contents, absolutePath);
		else if(language.equals(Language.PYTHON_LANGUAGE))
			return new Python(file, timeOut, contents, absolutePath);
		
		return null;
	}

}
