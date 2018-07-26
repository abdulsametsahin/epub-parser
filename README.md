# **Epub Parser for PHP**

You can read content of an epub file.

> It's under development. Don't use it on production.

### Installation

    composer require abdulsametsahin/epub-parser

### Example

    <?php
    include  "vendor/autoload.php";
	
	use abdulsametsahin\EpubParser;
	$test =  new  EpubParser;
	$test->load('hunger-games');
	var_dump($test->getCover());
	?>
