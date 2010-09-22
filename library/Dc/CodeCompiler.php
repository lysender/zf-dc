<?php

/** 
 * Compiles code from various php files into on large file
 */
class Dc_CodeCompiler
{
	/** 
	 * An array of files - relative path
	 * 
	 * @var array
	 */
	protected $_files = array();
	
	/** 
	 * Output file where the compile codes are to be saved
	 * Relative path
	 * 
	 * @var string
	 */
	protected $_outputFile;
	
	/** 
	 * Filename where paths are written
	 * These paths are file names (relative path) new line seprated
	 * 
	 * @var string
	 */
	protected $_pathsFile;
	
	/** 
	 * Tokens to ignore / skip such as comments /etc
	 * 
	 * @var string
	 */
	protected $_skipTokens = array();
	
	/** 
	 * Paths are saved as relative paths
	 * This is the root path
	 * 
	 * @var string
	 */
	protected $_rootPath;
	
	/** 
	 * __construct()
	 * 
	 * 
	 */
	public function __construct()
	{
		$this->_initSkipTokens();
	}
	
	/** 
	 * Initialize tokens to be ignore / skipped when compiling the code
	 * 
	 * @return $this
	 */
	protected function _initSkipTokens()
	{
		$this->_skipTokens = array(T_COMMENT);
		
		if (defined('T_DOC_COMMENT'))
		{
	        $this->_skipTokens[] = T_DOC_COMMENT;
		}
		
	    if (defined('T_ML_COMMENT'))
	    {
	        $this->_skipTokens[] = T_ML_COMMENT;
	    }
	    
		if (defined('T_OPEN_TAG'))
	    {
	    	$this->_skipTokens[] = T_OPEN_TAG;
	    }
	    
	    if (defined('T_CLOSE_TAG'))
	    {
	    	$this->_skipTokens[] = T_CLOSE_TAG;
	    }
	    
	    return $this;
	}
	
	public function setFiles(array $files)
	{
		$this->_files = $files;
		
		return $this;
	}
	
	public function getFiles()
	{
		return $this->_files;
	}
	
	public function setOutputFile($file)
	{
		$this->_outputFile = $file;
		
		return $this;
	}
	
	public function getOutputFile()
	{
		return $this->_outputFile;
	}
	
	public function setPathsFile($file)
	{
		$this->_pathsFile = $file;
		
		return $this;
	}
	
	public function setRootPath($path)
	{
		$this->_rootPath = $path;
		
		return $this;
	}
	
	public function getRootPath()
	{
		return $this->_rootPath;
	}
	
	public function getPathsFile()
	{
		return $this->_pathsFile;
	}
	
	public function writePaths(array $paths)
	{
		$existingPaths = $this->getPaths();
		
		$paths = array_merge($paths, $existingPaths);
		$paths = array_map(array($this, 'stripRootPath'), $paths);
		
		$paths = implode("\n", $paths);
		$pathsFile = $this->getRootPath() . DIRECTORY_SEPARATOR . $this->getPathsFile();
		
		return file_put_contents($pathsFile, $paths);
	}
	
	public function stripRootPath($value)
	{
		$root = $this->getRootPath() . DIRECTORY_SEPARATOR;
		
		return str_replace($root, '', $value);
	}
	
	public function getPaths()
	{
		$pathsFile = $this->getRootPath() . DIRECTORY_SEPARATOR . $this->getPathsFile();
		
		if ( ! is_file($pathsFile))
		{
			return array();
		}
		
		$paths = file_get_contents($pathsFile);
		$paths = explode("\n", $paths);
		
		return $paths;
	}
	
	public function compile()
	{
		$files = $this->getFiles();
		if (empty($files))
		{
			return false;
		}
		
		$outputFile = $this->getRootPath() . DIRECTORY_SEPARATOR . $this->getOutputFile();
		
		// Insert opening tag and a new line
		$initial = "<?php \n\n";
		file_put_contents($outputFile, $initial);

		// Write the rest of the files
		foreach ($files as $file)
		{
			$input = $this->_getFileCode($file) . "\n";
			file_put_contents($outputFile, $input, FILE_APPEND | LOCK_EX);
		}
		
		return true;
	}
	
	protected function _getFileCode($file)
	{
		$file = $this->getRootPath() . DIRECTORY_SEPARATOR . $file;
		if ( ! is_file($file))
		{
			return false;
		}
	    
		$fileStr = file_get_contents($file);
		$newStr = '';
		
		$tokens = token_get_all($fileStr);
		
		foreach ($tokens as $token)
		{
			if (is_array($token))
			{
				if (in_array($token[0], $this->_skipTokens))
				{
            		continue;
				}
			
				$token = $token[1];
			}

			$newStr .= $token;
		}
		
		return $newStr;
	}
}