<?php 
use PHPUnit\Framework\TestCase;

/**
*  Corresponding Class to test EpubParser class
*
*  @author Abdulsamet ŞAHİN
*/
class EpubParserTest extends TestCase
{
	
	
  	public function testIsThereAnySyntaxError()
  	{
		$var = new abdulsametsahin\EpubParser\EpubParser;
		$this->assertTrue(is_object($var));
		unset($var);
  	}
  
	/**
	 * Just check if the YourClass has no syntax error 
	*
	* This is just a simple check to make sure your library has no syntax error. This helps you troubleshoot
	* any typo before you even use this library in a real project.
	*
	*/
	public function epubPathTest()
	{
		$var = new abdulsametsahin\EpubParser\EpubParser;
		$this->assertTrue(is_string($var->load("hunger-games")->epub_path));
		unset($var);
	} 
}
