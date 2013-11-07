<?php

//
//*******
//
//	filename: color_list.php
//	author: Zack Brown
//	date: 07/11/2013
//
//*******
//

class ColorList
{
	var $name;

	var $colors;

	var $platform;

	function __construct($name, $colors, $platform)
	{
		$this->name = $name;

		$this->colors = $colors;

		$this->platform = $platform;

		ksort($this->colors);
	}

	function framework()
	{
		if($this->platform == "iOS")
		{
			return "<UIKit/UIKit.h>";
		}

		return "<AppKit/AppKit.h>";
	}

	function subclass()
	{
		if($this->platform == "iOS")
		{
			return "UIColor";
		}

		return "NSColor";
	}

	function category()
	{
		return $this->subclass() . "+" . $this->name;
	}

	function source()
	{
		if($this->name == "Crayola")
		{
			return "http://en.wikipedia.org/wiki/Crayola_colors";
		}

		return "http://www.cal-print.com/InkColorChart.htm";
	}
	
	function ownership()
	{
		if($this->name == "Crayola")
		{
			return "Crayola LLC";
		}

		return "Pantone Inc";
	}

	function method_signature_for_color_with_name($name)
	{
		return "+ (instancetype)" . strtolower($this->name) . str_replace(" ", "", $name) . "Color";
	}

	function contributors()
	{
		return array("Abizern" => "https://github.com/Abizern");
	}

	function base_image_url()
	{
		if($this->platform == "iOS")
		{
			if($this->name == "Crayola")
			{
				return "https://raw.github.com/CaptainRedmuff/UIColor-Crayola/master/images";
			}

			return "https://raw.github.com/CaptainRedmuff/UIColor-Pantone/master/images";
		}

		if($this->name == "Crayola")
		{
			return "https://raw.github.com/CaptainRedmuff/NSColor-Crayola/master/images";
		}

		return "https://raw.github.com/CaptainRedmuff/NSColor-Pantone/master/images";
	}
};

?>