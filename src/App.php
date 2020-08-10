<?php

namespace BenMajor\APIDocs;

use BenMajor\APIDocs\Exception\{ContentException};
use Adbar\Dot;
use Webuni\FrontMatter\FrontMatter;

class App
{
	protected $twig;
	protected $content;

	private $templateDir;
	private $contentDir;
	private $config;

	private $frontMatter;

	function __construct($config)
	{
		$this->config = new Dot($config);

		$this->templateDir = $this->getConfig('dirs.templates');
		$this->contentDir = $this->getConfig('dirs.content');
		$this->cacheDir = $this->getConfig('dirs.cache');

		# Template directory !defined or !exists:
		if( is_null($this->templateDir) || ! is_dir($this->templateDir) )
		{
			throw new ContentException('The specified template directory does not exist!');
		}

		# Content directory !defined or !exists:
		if( is_null($this->contentDir) || ! is_dir($this->contentDir) )
		{
			throw new ContentException('The specified content directory does not exist!');
		}

		# Set up Twig:
		$this->twig = new \Twig\Environment(
			new \Twig\Loader\FilesystemLoader($this->templateDir), [
				'cache' => $this->cacheDir,
				'debug' => true
			]
		);

		$this->twig->addExtension(new \Twig\Extension\DebugExtension());
	}

	# Get the Twig instance:
	public function getTwig()
	{
		return $this->twig;
	}

	public function getContentDir()
	{
		return $this->contentDir;
	}

	# Get the sections:
	public function getSections()
	{
		$sections = [ ];

		$files = glob($this->getContentDir().'/*');
		natsort($files);

		foreach( $files as $file )
		{
			# It's a directory, so get the first file and send it:
			if( is_dir($file) )
			{
				# Get the first file:
				$subFiles = glob($file.DIRECTORY_SEPARATOR.'*');
				natsort($subFiles);
				$file = $subFiles[0];
			}

			$sections[] = new Section($this, $file);
		}

		return $sections;
	}

	# Render a specified template (with optional data):
	public function render( string $template, array $data = [ ] )
	{
		$template = $this->getTwig()->load($template);

		$data['app'] = $this;
		$data['hello'] = 'world';
		
		echo $template->render($data);
		exit(1);
	}

	public function run()
	{
		$this->render('base.twig');
	}

	public function getConfig( $key, $default = null )
	{
		$val = $this->config[$key];

		if( is_null($val) )
		{
			return $default;
		}

		return $val;
	}
}