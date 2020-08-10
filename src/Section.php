<?php

namespace BenMajor\APIDocs;

use Symfony\Component\Yaml\Yaml;

class Section
{
	private $isDir;
	private $subdir;

	private $app;
	private $filename;
	private $basename;
	private $raw;

	function __construct( App $app, string $file )
	{
		$this->app = $app;
		$this->raw = $this->parseFile($file);
		$this->filename = $file;
		$this->basename = basename($file);

		if( substr(basename($this->filename), 0, 1) == 0 )
		{
			$this->isDir = true;

			$this->subdir = substr(
				$this->filename,
				0,
				(0 - strlen(basename($this->filename)))
			);
		}
	}

	# Get the title:
	public function getTitle()
	{
		if( !isset($this->raw['title']) )
		{
			if( substr($this->basename, 0, 1) == 0 )
			{
				$parts = explode(DIRECTORY_SEPARATOR, $this->filename);
				array_pop($parts);
				$name = end($parts);
			}
			else
			{
				$name = pathinfo($this->basename, PATHINFO_FILENAME);
			}

			# Remove the numeric:
			$parts = explode('-', $name);
			array_shift($parts);
			
			return ucwords(implode(' ', $parts));
		}

		return $this->raw['title'];
	}

	# Check if the current element ha children:
	public function hasChildren()
	{
		if( $this->isDir )
		{
			return (count(glob($this->subdir.'*.yaml')) > 1);
		}

		return false;
	}

	# Get the children:
	public function getChildren()
	{
		$children = [ ];
		$childFiles = glob($this->subdir.'*.yaml');

		natsort($childFiles);

		# Remove the first element:
		if( basename($childFiles[0]) == $this->basename )
		{
			array_shift($childFiles);
		}
		
		foreach( $childFiles as $child )
		{
			$children[] = new self($this->app, $child);
		}

		return $children;
	}

	private function parseFile( $file )
	{
		return Yaml::parseFile($file);
	}
}