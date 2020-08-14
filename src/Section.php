<?php

namespace BenMajor\APIDocs;

use Symfony\Component\Yaml\Yaml;
use Adbar\Dot;
use Minwork\Helper\Arr;

class Section
{
	const GET_PROPS = [
		'id' => 'getID()',
		'title' => 'getTitle()',
		'content' => 'getContent()',
		'sidebar' => 'sidebar',

		'endpoint' => 'request.endpoint',
		'requestMethod' => 'request.method',
		'requestParams' => 'getRequestParams()',
		'responseParmas' => 'getResponseParams()'
	];

	private $isDir;
	private $subdir;

	private $app;
	private $filename;
	private $basename;
	private $directory;
	private $raw;
	private $data;

	function __construct( App $app, string $file )
	{
		$this->app = $app;
		$this->markdown = new \ParsedownExtra();

		$this->raw = $this->parseFile($file);
		$this->filename = $file;
		$this->basename = basename($file);
		$this->directory = pathinfo($file, PATHINFO_DIRNAME );

		if( substr(basename($this->filename), 0, 1) == 0 )
		{
			$this->isDir = true;

			$this->subdir = substr(
				$this->filename,
				0,
				(0 - strlen(basename($this->filename)))
			);
		}

		$this->data = new Dot($this->raw);
	}

	# Magic getter:
	function __get($name)
	{
		if( array_key_exists($name, self::GET_PROPS) )
		{
			$action = self::GET_PROPS[$name];

			if( substr($action, -2, 2) == '()' )
			{
				return call_user_func(__CLASS__.'::'.substr($action, 0, -2));
			}

			return $this->data[$action];
		}

		return null;
	}

	# Magic isset:
	function __isset($name)
	{
		if( array_key_exists($name, self::GET_PROPS) )
		{
			return true;
		}
		
		return isset($this->{$name});
	}

	# Get the app instance:
	public function getApp()
	{
		return $this->app;
	}

	# Get the ID:
	public function getId()
	{
		if( $this->isChild() )
		{
			return $this->getParent()->getId().'_'.str_replace(
				[ '.', ' ' ], 
				[ null, '-' ], 
				strtolower($this->getTitle())
			);
		}

		return str_replace(
			[ '.', ' ' ], 
			[ null, '-' ], 
			strtolower($this->getTitle())
		);
	}

	# Get the ID attribute:
	public function getAttrID()
	{
		return $this->getID();
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

	# Get the content:
	public function getContent( $parseMarkdown = true )
	{
		if( isset($this->raw['content']) )
		{
			if( is_file($this->directory.DIRECTORY_SEPARATOR.$this->raw['content']) )
			{
				$content = file_get_contents($this->directory.DIRECTORY_SEPARATOR.$this->raw['content']);

				return ($parseMarkdown) ? $this->markdown->text($content) : $content;
			}

			return ($parseMarkdown) ? $this->markdown->text($this->raw['content']) : $this->raw['content'];
		}

		return;
	}

	# Check if the current element has children:
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

	# Check if the current section is a child:
	public function isChild()
	{
		return (
			$this->directory !== $this->getApp()->getContentDir() 
			&& 
			substr($this->basename, 0, 1) != 0
		);
	}

	# Get the parent:
	public function getParent()
	{
		if( $this->isChild() )
		{
			$files = glob($this->directory.DIRECTORY_SEPARATOR.'*.yaml');
			natsort($files);

			return new self($this->getApp(), $files[0]);
		}

		return;
	}

	# Get the request parameters:
	public function getRequestParams()
	{
		$params = [ ];

		if( $this->hasRequestParams() )
		{
			foreach($this->raw['request']['params'] as $name => $param)
			{
				$params[] = [
					'name' => str_replace('*', null, $name),
					'type' => $this->getVarType($param[0]),
					'required' => (substr($name, -1, 1) == '*'),
					'value' => $param[0],
					'description' => $param[1]
				];
			}
		}

		return $params;
	}

	# Check if the section contains an endpoint:
	public function hasEndpoint()
	{
		return (isset($this->raw['request']) && isset($this->raw['request']['method']));
	}

	# Check if the section has a request defined:
	public function hasRequest()
	{
		return isset($this->raw['request']);
	}

	# Check if the section has request elements:
	public function hasRequestParams()
	{
		return (isset($this->raw['request']) && isset($this->raw['request']['params']));
	}

	# Check if the section has a response defined:
	public function hasResponse()
	{
		return (isset($this->raw['response']));
	}

	# Check if the section has response parameters:
	public function hasResponseParams()
	{
		return (isset($this->raw['response']) && isset($this->raw['response']['params']));
	}

	# Get the response params:
	public function getResponseParams()
	{
		$params = [ ];

		if( $this->hasResponseParams() )
		{
			$params = $this->buildParamArray($this->raw['response']['params']);
		}

		return $params;
	}

	# Get the verbose response status:
	public function getVerboseResponseStatus()
	{
		$code = (isset($this->raw['response']['statusCode'])) ? $this->raw['response']['statusCode'] : 200;

		switch( $code ) 
		{
            case 100: 
            	$text = 'Continue';
                break;
            case 101: 
            	$text = 'Switching Protocols';
                break;
            case 200: 
            	$text = 'OK';
                break;
            case 201: 
            	$text = 'Created';
                break;
            case 202: 
            	$text = 'Accepted';
                break;
            case 203: 
            	$text = 'Non-Authoritative Information';
                break;
            case 204: 
            	$text = 'No Content';
                break;
            case 205: 
            	$text = 'Reset Content';
                break;
            case 206: 
            	$text = 'Partial Content';
                break;
            case 300: 
            	$text = 'Multiple Choices';
                break;
            case 301: 
            	$text = 'Moved Permanently';
                break;
            case 302: 
            	$text = 'Moved Temporarily';
                break;
            case 303: 
            	$text = 'See Other';
                break;
            case 304: 
            	$text = 'Not Modified';
                break;
            case 305:
            	$text = 'Use Proxy';
                break;
            case 400: 
            	$text = 'Bad Request';
                break;
            case 401: 
            	$text = 'Unauthorized';
                break;
            case 402: 
            	$text = 'Payment Required';
                break;
            case 403: 
            	$text = 'Forbidden';
                break;
            case 404: 
            	$text = 'Not Found';
                break;
            case 405: 
            	$text = 'Method Not Allowed';
                break;
            case 406: 
            	$text = 'Not Acceptable';
                break;
            case 407: 
            	$text = 'Proxy Authentication Required';
                break;
            case 408: 
            	$text = 'Request Time-out';
                break;
            case 409: 
            	$text = 'Conflict';
                break;
            case 410: 
            	$text = 'Gone';
                break;
            case 411: 
            	$text = 'Length Required';
                break;
            case 412: 
            	$text = 'Precondition Failed';
                break;
            case 413: 
            	$text = 'Request Entity Too Large';
                break;
            case 414:
            	$text = 'Request-URI Too Large';
                break;
            case 415: 
            	$text = 'Unsupported Media Type';
                break;
            case 500: 
            	$text = 'Internal Server Error';
                break;
            case 501: 
            	$text = 'Not Implemented';
                break;
            case 502: 
            	$text = 'Bad Gateway';
                break;
            case 503: 
            	$text = 'Service Unavailable';
                break;
            case 504: 
            	$text = 'Gateway Time-out';
                break;
            case 505: 
            	$text = 'HTTP Version not supported';
                break;
            default:
                $text = 'Unknown Status';
                break;
        }

        return $code.' - '.$text;
	}

	# Build the param array:
	public function buildParamArray( $params )
	{
		$return = [ ];

		foreach( $params as $name => $param )
		{
			if( count($param) == 2 )
			{
				if( is_scalar($param[1]) )
				{
					$param = [
						'name' => str_replace('*', null, $name),
						'type' => $this->getVarType($param[0]),
						'value' => $param[0],
						'description' => $param[1]
					];
				}
				elseif( is_array($param[1]) )
				{
					$param = [
						'name' => str_replace('*', null, $name),
						'type' => 'object',
						'description' => $param[0],
						'children' => $this->buildParamArray($param[1])
					];
				}
			}
			
			
			$return[] = $param;
		}

		return $return;
	}

	# Get the response object:
	public function getResponseObject( $parent = null )
	{
		return $this->getResponseObjectElement($this->raw['response']['params']);
	}

	# Get response object from element:
	public function getResponseObjectElement( $params, $encode = true )
	{
		$return = [ ];
		
		foreach( $params as $name => $param )
		{
			if( count($param) == 2 )
			{
				if( is_scalar($param[1]) )
				{
					$return[$name] = $param[0];
				}
				elseif( is_array($param[1]) )
				{
					$return[$name] = $this->getResponseObjectElement($param[1], false);
				}
			}
		}
		
		return ($encode) ? json_encode($return, JSON_PRETTY_PRINT) : $return;
	}

	# Check if the section has a sidebar defined:
	public function hasSidebar()
	{
		return (isset($this->raw['sidebar']));
	}

	# Get the sidebar template:
	public function getSidebarTemplate()
	{
		if( isset($this->raw['sidebar']['content']) )
		{
			if( is_array($this->raw['sidebar']['content']) )
			{
				return 'examples/table.twig';
			}
		}

		if( isset($this->raw['sidebar']['code']) )
		{
			return 'examples/code.twig';
		}

		return null;
	}

	# Format a JSON object for nice display in the sidebar:
	public function getFormattedSidebarCode()
	{
		$code = $this->raw['sidebar']['code'];

		if( is_object($code) || is_array($code) )
		{
			return json_encode($this->raw['sidebar']['code'], JSON_PRETTY_PRINT);
		}

		# Parse the variables:
		foreach( $this->getApp()->getConfig('twig') as $name => $val )
		{
			$code = str_replace("{{ $name }}", $val, $code);
		}

		return $code;
	}

	# Parse a YAML file:
	private function parseFile( $file )
	{
		return Yaml::parseFile($file);
	}

	private function getVarType( $var ) :string 
	{
		if( is_string($var) )
		{
			if( ctype_digit($var) )
			{
				return 'integer';
			}

			if( is_numeric($var) )
			{
				return 'number';
			}

			if( $var == 'true' || $var == 'false' )
			{
				return 'boolean';
			}

			return 'string';
		}

		if( is_int($var) )
		{
			return 'integer';
		}

		if( is_bool($var) )
		{
			return 'boolean';
		}

		if( is_array($var) )
		{
			return 'array';
		}

		return 'object';
	}
}