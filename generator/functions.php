<?php

//
//*******
//
//	filename: functions.php
//	author: Zack Brown
//	date: 07/11/2013
//
//*******
//

function check_directory_exists_and_create_if_needed($directory)
{
	if(!file_exists($directory))
	{
		mkdir($directory);
	}
}

function create_comment_block_for_filename($filename)
{
	$string = "//\n";
	$string .= "//*******\n";
	$string .= "//\n";
	$string .= "//\tfilename: $filename\n";
	$string .= "//\tauthor: Zack Brown\n";
	$string .= "//\tdate: " . date("d/m/Y", mktime()) . "\n";
	$string .= "//\n";
	$string .= "//*******\n";
	$string .= "//\n\n";

	return $string;
}

function create_markdown_header_for_color_list($color_list)
{
	$string = $color_list->category() . "\n";
	$string .= "===============\n\n";
	$string .= "Objective C " . $color_list->name . " " . $color_list->subclass() . " category. Because everybody loves " . $color_list->name . "!\n\n";
	$string .= "List of " . count($color_list->colors) . " " . $color_list->name . " colors sourced from <a href=\"" . $color_list->source() . "\" title=\"" . $color_list->name . "\">" . $color_list->source() . "</a> written as a programming exercise and is not intended for profit.\n\n";
	$string .= "This list of colors is the property of " . $color_list->ownership() . ". Usage in a commerical application is at your own risk and I (Zack Brown) accept no liability.\n\n";

	return $string;
}

function create_cache_key_with_color_method()
{
	$string = "+ (NSString *)cacheKeyWithRed:(CGFloat)red green:(CGFloat)green blue:(CGFloat)blue alpha:(CGFloat)alpha\n";
	$string .= "{\n";
	$string .= "\treturn [NSString stringWithFormat:@\"%.2f%.2f%.2f%.2f\", red, green, blue, alpha];\n";
	$string .= "}\n\n";

	return $string;
}

function create_color_lookup_method_for_color_list($color_list)
{
	$string = "+ (instancetype)" . strtolower($color_list->name) . "ColorWithRed:(CGFloat)red green:(CGFloat)green blue:(CGFloat)blue alpha:(CGFloat)alpha\n";
	$string .= "{\n";
	$string .= "\tstatic NSCache *cache = nil;\n\n";
	$string .= "\tif(!cache)\n";
	$string .= "\t{\n";
	$string .= "\t\tcache = [[NSCache alloc] init];\n\n";
	$string .= "\t\t[cache setName:@\"" . $color_list->category() . "\"];\n";
	$string .= "\t}\n\n";
	$string .= "\tNSString *cacheKey = [[self class] cacheKeyWithRed:red green:green blue:blue alpha:alpha];\n\n";
	$string .= "\t" . $color_list->subclass() . " *color = [cache objectForKey:cacheKey];\n\n";
	$string .= "\tif(!color)\n";
	$string .= "\t{\n";
	$string .= "\t\tcolor = [" . $color_list->subclass() . " colorWithRed:red green:green blue:blue alpha:alpha];\n\n";
	$string .= "\t\t[cache setObject:color forKey:cacheKey];\n";
	$string .= "\t}\n\n";
	$string .= "\treturn color;\n";
	$string .= "}\n\n";

	return $string;
}

function generate_image_thumbnail_for_color_with_name($hex, $image_filepath)
{
	$width = 32;
	$height = 32;

	list($red, $green, $blue) = rgb_from_hex_0_to_255($hex);
	
	$image = imagecreatetruecolor($width, $height);

	$background_color = imagecolorallocate($image, $red, $green, $blue);

	imagefilledrectangle($image, 0, 0, $width, $height, $background_color);

	imagepng($image, $image_filepath);
}

function method_implementation_for_color_with_name($hex, $name, $color_list)
{
	list($red, $green, $blue) = rgb_from_hex_0_to_1($hex);

	$string = $color_list->method_signature_for_color_with_name($name) . "\n";
	$string .= "{\n";
	$string .= "\treturn [[self class] " . strtolower($color_list->name) . "ColorWithRed:" . number_format($red, 3) . " green:" . number_format($green, 3) . " blue:" . number_format($blue, 3) . " alpha:1.0];\n";
	$string .= "}\n\n";

	return $string;
}

function create_table_row_for_color_with_name($hex, $name, $color_list, $image_filepath)
{
	list($red, $green, $blue) = rgb_from_hex_0_to_1($hex);

	$string = "\t<tr>\n";
	$string .= "\t\t<td><img src=\"$image_filepath\" width=\"32\" height=\"32\" alt=\"$name\" /></td>\n";
	$string .= "\t\t<td>$name</td>\n";
	$string .= "\t\t<td>[" . $color_list->subclass() . " " . strtolower($color_list->name) . str_replace(" ", "", $name) . "Color]</td>\n";
	$string .= "\t\t<td>[" . $color_list->subclass() . " colorWithRed:" . number_format($red, 3) . " green:" . number_format($green, 3) . " blue:" . number_format($blue, 3) . " alpha:1.0]</td>\n";
	$string .= "\t</tr>\n\n";

	return $string;
}

function rgb_from_hex_0_to_1($hex)
{
	if(substr($hex, 0, 1) == "#")
	{
		$hex = substr($hex, 1, strlen($hex) - 1);
	}

	$array = array();

	$dec = hexdec($hex);
	
	$red_255 = 0xFF & $dec >> 0x10;
	$green_255 = 0xFF & $dec >> 0x8;
	$blue_255 = 0xFF & $dec;

	$array[] = ((1 / 255) * $red_255);
	$array[] = ((1 / 255) * $green_255);
	$array[] = ((1 / 255) * $blue_255);

	return $array;
}

function rgb_from_hex_0_to_255($hex)
{
	if(substr($hex, 0, 1) == "#")
	{
		$hex = substr($hex, 1, strlen($hex) - 1);
	}

	$array = array();

	$dec = hexdec($hex);
	
	$array[] = 0xFF & $dec >> 0x10;
	$array[] = 0xFF & $dec >> 0x8;
	$array[] = 0xFF & $dec;

	return $array;
}

?>