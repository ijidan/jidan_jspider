<?php
namespace Lib\Views;

use Mustache_Loader_StringLoader;
use Psr\Http\Message\ResponseInterface;

/**
 * 模板渲染
 * Class MustacheRender
 * @package Lib\Views
 */
class MustacheRender
{
    protected $engine;
    private $templatePath;

	/**
	 * 构造函数
	 * MustacheRender constructor.
	 * @param string $templatePath
	 * @param string $cachePath
	 */
    public function __construct($templatePath = '', $cachePath = '')
    {
        $options = [
            'loader' => new \Mustache_Loader_FilesystemLoader($templatePath . 'views/', ['extension' => '.html']),
            'partials_loader' => new \Mustache_Loader_FilesystemLoader($templatePath . 'partials/', ['extension' => '.html']),
            'cache' => $cachePath
        ];
        $this->templatePath = $templatePath;
        $this->engine = new \Mustache_Engine($options);
        $this->engine->addHelper('helper', new Helper());
    }

	/**
	 * 渲染
	 * @param ResponseInterface $response
	 * @param $template
	 * @param array $data
	 * @return ResponseInterface
	 */
    public function render(ResponseInterface $response, $template, array $data = [])
    {
        $output = $this->engine->render($template, $data);
        $response->getBody()->write($output);

        return $response;
    }

	/**
	 * @param $template
	 * @param $data
	 * @return string
	 */
    public function fetch($template, $data)
    {
        return $this->engine->render($template, $data);
    }
    
    /**
     * 渲染静态文件
     * @param ResponseInterface $response
     * @param $template
     * @param array $data
     * @return ResponseInterface
     */
    public function renderStaticPages(ResponseInterface $response, $template, array $data = [])
    {
        //渲染模板
        $tplPath = $this->templatePath . "/views/tpl.html";
        $tplContent = file_get_contents($tplPath);
        $loader = new Mustache_Loader_StringLoader();
        $this->engine->setLoader($loader);
        $tplRenderContent = $this->engine->render($tplContent, $data);
    
        //追加内容
        $path = $this->templatePath . "/static_pages/$template";
        if (!\file_exists($path)) {
            $mergedContent = $tplRenderContent;
        } else {
            $content = file_get_contents($path);
            $mergedContent = str_replace("</header>", "</header>" . $content, $tplRenderContent);
        }
        array_walk($data, function ($value, $item) use (&$mergedContent) {
            $find = "{{" . $item . "}}";
            if (strpos($mergedContent, $find) !== false) {
                $mergedContent = str_replace("{{" . $item . "}}", $value, $mergedContent);
            }
        });
        //输出
        $response->getBody()->write($mergedContent);
        return $response;
    }
    
    /**
     * 获取静态文件内容
     * @param $template
     * @return string
     */
    public function getStaticPagesContent($template)
    {
        $path = $this->templatePath . "/static_pages/$template";
        $content = file_exists($path) ? file_get_contents($path) : "";
        return $content;
    }
}