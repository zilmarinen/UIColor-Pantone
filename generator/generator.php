<?php

//
//*******
//
//	filename: generator.php
//	author: Zack Brown
//	date: 07/11/2013
//
//*******
//

//set timezone to mute warnings
date_default_timezone_set("UTC");

$color_lists = array();

require_once "functions.php";
require_once "color_lists/crayola.php";
require_once "color_lists/pantone.php";

echo "-------\n";
echo "Generating colors\n";
echo "-------\n\n";

foreach($color_lists as $color_list)
{
	echo "Generating source for " . $color_list->name . " on platform " . $color_list->platform . ".\n";

	$root_directory = "../" . $color_list->category();
	$source_directory = $root_directory . "/Source";
	$image_directory = $root_directory . "/images";
	$header_filepath = $source_directory . "/" . $color_list->category() . ".h";
	$main_filepath = $source_directory . "/" . $color_list->category() . ".m";
	$markdown_filepath = $root_directory . "/README.md";

	//create directories
	check_directory_exists_and_create_if_needed($root_directory);
	check_directory_exists_and_create_if_needed($source_directory);
	check_directory_exists_and_create_if_needed($image_directory);

	//create file handles
	$header_file_handle = fopen($header_filepath, "w+");
	$main_file_handle = fopen($main_filepath, "w+");
	$markdown_file_handle = fopen($markdown_filepath, "w+");

	//write headers
	fwrite($header_file_handle, create_comment_block_for_filename($color_list->category() . ".h"));
	fwrite($main_file_handle, create_comment_block_for_filename($color_list->category() . ".m"));
	fwrite($markdown_file_handle, create_markdown_header_for_color_list($color_list));

	//append imports
	fwrite($header_file_handle, "#import " . $color_list->framework() . "\n\n");
	fwrite($main_file_handle, "#import \"" . $color_list->category() . ".h\"\n\n");

	//begin interface and implementation
	fwrite($header_file_handle, "@interface " . $color_list->subclass() . " (" . $color_list->name . ")\n\n");
	fwrite($main_file_handle, "@implementation " . $color_list->subclass() . " (" . $color_list->name . ")\n\n");

	//start table
	fwrite($markdown_file_handle, "<table>\n\n");

	//loop through colors
	foreach($color_list->colors as $name => $hex)
	{
		$image_file_name = strtolower($color_list->name) . $name . "Color.png";

		$image_file_path = $image_directory . "/" . $image_file_name;

		$relative_image_url = $color_list->base_image_url() . "/" . $image_file_name;

		//generate thumbnail image
		generate_image_thumbnail_for_color_with_name($hex, $image_file_path);

		//append method signature
		fwrite($header_file_handle, $color_list->method_signature_for_color_with_name($name) . ";\n");

		//append method implementation
		fwrite($main_file_handle, method_implementation_for_color_with_name($hex, $name, $color_list));

		//append markdown table row
		fwrite($markdown_file_handle, create_table_row_for_color_with_name($hex, $name, $color_list, $relative_image_url));
	}

	//append color lookup method
	fwrite($main_file_handle, create_color_lookup_method_for_color_list($color_list));

	//append cache method
	fwrite($main_file_handle, create_cache_key_with_color_method());

	//end table
	fwrite($markdown_file_handle, "</table>\n\n");

	//end interface and implementation
	fwrite($header_file_handle, "\n@end");
	fwrite($main_file_handle, "@end");

	//append contributors
	fwrite($markdown_file_handle, "Contributors\n");
	fwrite($markdown_file_handle, "===============\n\n");

	//loop through contributors
	foreach($color_list->contributors() as $name => $url)
	{
		fwrite($markdown_file_handle, "<a href=\"$url\" title=\"$name\">$name</a>\n");
	}

	//close file handles
	fclose($markdown_file_handle);
	fclose($main_file_handle);
	fclose($header_file_handle);
}

echo "\n-------\n";
echo "Complete\n";
echo "-------\n";

?>