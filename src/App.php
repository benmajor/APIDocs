<?php

namespace BenMajor\APIDocs;

use BenMajor\APIDocs\Exception\{ContentException};
use Adbar\Dot;
use Webuni\FrontMatter\FrontMatter;

use Twig\Extra\Markdown\{MarkdownExtension,MarkdownRuntime,DefaultMarkdown};
use Twig\RuntimeLoader\RuntimeLoaderInterface;

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
		$this->twig->addExtension(new MarkdownExtension());
		$this->twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
		    public function load($class) {
		        if (MarkdownRuntime::class === $class) {
		            return new MarkdownRuntime(new DefaultMarkdown());
		        }
		    }
		});

		# Add Twig globals:
		foreach( $config['twig'] as $key => $value )
		{
			$this->addTwigGlobal(
				strtoupper($key),
				$value
			);
		}
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

			if( substr($file, -2, 2) != 'md' )
			{
				$sections[] = new Section($this, $file);
			}
		}

		return $sections;
	}

	# Render a specified template (with optional data):
	public function render( string $template, array $data = [ ] )
	{
		$template = $this->getTwig()->load($template);

		$data['app'] = $this;
		
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

	# Add a new global to Twig:
	public function addTwigGlobal( string $name, $value )
	{
		return $this->twig->addGlobal($name, $value);
	}

	# Output the sidebar:
	public function outputSidebar()
	{
		$ulFlag = false;
		$html = '';

		foreach( $this->getSections() as $section )
		{
			if( $section->hasChildren() )
			{
				if( $ulFlag )
				{
					$html.= '</ul>';
				}

				$ulFlag = false;

				$html.= '<div class="sidebar-group">';
				$html.= '<h4 class="sidebar-group-title">'.$section->getTitle().'<ion-icon name="chevron-down"></ion-icon></h4>';
				$html.= '<ul class="sidebar-menu">';

				foreach( $section->getChildren() as $child )
				{
					$html.= '<li><a href="#'.$child->getId().'">'.$child->getTitle().'</a></li>';
				}

				$html.= '</ul>';
				$html.= '</div>';

			}
			else
			{
				if( $ulFlag )
				{
					$html.= '<li><a href="#'.$section->getId().'">'.$section->getTitle().'</a></li>';
				}
				else
				{
					$html.= '<ul class="sidebar-menu">';
					$html.= '<li><a href="#'.$section->getId().'">'.$section->getTitle().'</a></li>';

					$ulFlag = true;
				}
			}
		}

		return $html;
	}
}