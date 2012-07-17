#!/usr/bin/php
<?php
#
# Copyright (c) 2008-2010, Mike Shutlar
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions
# are met:
#
# 1. Redistributions of source code must retain the above copyright
#    notice, this list of conditions and the following disclaimer.
# 2. Redistributions in binary form must reproduce the above copyright
#    notice, this list of conditions and the following disclaimer in the
#    documentation and/or other materials provided with the distribution.
#
# THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
# IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
# OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
# IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
# INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
# NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
# DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
# THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
# (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
# THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
#
/**
 * links2lower is a small PHP CLI script that recursively renames
 * all files and folders in the path argument to lowercase, and
 * searches for HTML files and lowercases any URL's within them.
 *
 * This is useful for HTML documentation (or sites) created in
 * Windows where there has been disregard for the case-sensitivity
 * of the URLs, making hyperlinked documentation unusable on a case-
 * sensitive system like Linux.
 *
 * Call the script and provide the path to the directory of the
 * HTML documentation to work on.
 *
 * E.g.
 * php links2lower.php /home/anuser/docs/
 *
 * @author Mike Shutlar
 * @version 1.1 (2010-06-29)
 * @todo test on/alter for Windows
 */

/**
 * Recursively navigates through directories, first renaming files to lowercase,
 * and fixing URL's if that file happens to be an HTML file, then renaming the
 * parent directory to lowercase once everything inside it has been done.
 * @param string $dir path to the directory
 * @return bool true if all is well, false if something isn't writeable
 */
function recursively_fix_urls($dir)
{
	static $newfiles;
	static $orig_target_dir;
	$path_from_target = '';
	$file_ext;
	$dh = opendir($dir);

	if (is_null($orig_target_dir))
	{
		// First run, this is the original target directory
		$orig_target_dir = $dir;
	}
	else
	{
		// We need to find the path from the target to here
		$path_from_target = substr($dir, strlen($orig_target_dir) - 1);
	}

	if (is_null($newfiles))
	{
		// List of renamed files
		$newfiles = array();
	}
	
	while (false !== ($file = readdir($dh)))
	{
		if ($file !== '.' && $file !== '..')
		{
			if (is_dir($dir.'/'.$file))
			{
				// Check we can write to it
				if (!is_writable($dir.'/'.$file) && !make_writeable($dir.'/'.$file))
				{
					// Can't, let's start exiting
					return false;
				}

				// It's a directory, let's do everything inside it first
				if (!recursively_fix_urls($dir.'/'.$file))
				{
					// Something wasn't writeable, continue exiting
					return false;
				}
				// Done with the files within, let's rename this directory
				$newpath = $orig_target_dir.$path_from_target;
				$newpath .= '/'.strtolower($file);
				rename($dir.'/'.$file,
				       $newpath);
				// Ensure we know this has been renamed
				$newfiles[] = $newpath;
			}
			else if (in_array($file, $newfiles))
			{
				// This is a file we just renamed
				continue;
			}
			else
			{
				$newfile = strtolower($file);
				// Ensure we know this will have been renamed
				$newfiles[] = $dir.'/'.$newfile;
				// Rename file
				rename($dir.'/'.$file, $dir.'/'.$newfile);
				// If it's an HTML file...
				$file_ext = pathinfo($newfile, PATHINFO_EXTENSION);
				if ($file_ext === 'htm' || $file_ext === 'html')
				{
					// ... fix it's URL's
					if(!url_replace($dir.'/'.$newfile))
					{
						// An HTML file wasn't writeable, start exiting script
						return false;
					}
				}
			}
		}
	}
	return true;
}

/**
 * Converts URLs in HTML files to lowercase.
 * @param string $file path to the file
 * @return bool true if contents replaced, false otherwise
 */
function url_replace($file)
{
	// Don't think I've forgot any URL attributes...!
	$pattern = '/ (href|src|action|cite|archive|codebase';
	$pattern .= '|code|data|ismap|usemap|longdesc)="(.*?)"/ie';
	$replace = "' \\1=\"'.strtolower('\\2').'\"'";
	
	// If it's not writeable, try to make it writeable
	if (is_writeable($file) || make_writeable($file))
	{
		$contents = file_get_contents($file);
		$contents = preg_replace($pattern, $replace, $contents);
		file_put_contents($file, $contents);
		return true;
	}
	else
	{
		// Not writeable
		return false;
	}
}

/**
 * Makes a file or directory writeable for it's owner, as long as that's the
 * same user running this script.
 * @param string $path path to file or directory
 * @return bool true if made writeable, false otherwise
 */
function make_writeable($path)
{
	if (!chmod($path, is_dir($path) ? 0755 : 0644))
	{
		// The file/dir is owned by someone else and isn't writeable
		echo "Error: $file is owned by someone else and it isn't writeable!\n";
		echo "I can't change that, please make it writeable and try again.\n";
		echo "Exiting.\n\n";
		return false;
	}
	return true; // It's writeable
}

// There's an argument and it's a valid folder
if ($_SERVER['argc'] == 2 && is_dir($_SERVER['argv'][1]))
{
	$target_dir = $_SERVER['argv'][1];
	// Check we can write to it
	if (!is_writable($target_dir) && !make_writeable($target_dir))
	{
		echo "Please check the permissions of $target_dir\n\n";
		return 1; // error
	}

	echo "\nLinks2Lower\n";
	echo "This script will recursively convert all file and folder\n";
	echo "names in the given directory to their lowercase equivalent,\n";
	echo "and correct all URLs within any HTML files to match.\n\n";
	echo "Please note that any directories or HTML files that are read-only\n";
	echo "will be chmod'd to 755 or 644 for the user you are currently\n";
	echo "running under (hello ".$_ENV['USER']."!), unless you don't have\n";
	echo "the necessary permissions to fiddle with them anyway... :)\n\n";
	echo "Perform these actions on all files and folders within\n";
	echo "$target_dir? [y/n]: "; // prompt

	if (fscanf(STDIN, "%s", $confirm_input) == 1)
	{
		// If that's a yes...
		if (strtolower($confirm_input) == 'y')
		{
			// Drum roll please...
			echo "\nProcessing...\n";
			clearstatcache();
			// ... here we go!
			if (recursively_fix_urls($target_dir))
			{
				echo "Woohoo! All done. Bye!\n\n";
				return 0; // Success!
			}
			else
			{
				// Should already be error message
				return 1; // Error
			}
		}
		elseif (strtolower($confirm_input) == 'n') // No
		{
			echo "Ok, bye!\n\n";
			return 0; // exit
		}
	}
	// Not a yes or no
	echo "\nYou must enter y or n. Please try again.\n\n";
	return 1; // error
}
else
{
	// Didn't provide directory name as argument
	echo "\nUsage: php links2lower.php /path/to/docs/directory\n\n";
	return 1; // error
}

?>
