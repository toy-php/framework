<?php

namespace Toy\View;

use Toy\Interfaces\WebApplicationInterface;
use Toy\Exceptions\Exception;
use Toy\Interfaces\WebServiceInterface;
use Toy\Interfaces\TemplateInterface;
use Toy\Interfaces\ParserInterface;
use Toy\Interfaces\ViewModelInterface;

class Template implements TemplateInterface, WebServiceInterface
{

    protected $templateDir;
    protected $templateExt;
    protected $functions = [];

    public function __construct($templateDir, $templateExt = '.php')
    {
        $this->templateDir = $templateDir;
        $this->templateExt = $templateExt;
    }

    /**
     * Получить путь к файлу шаблона
     * @param string $templateName
     * @return string
     * @throws Exception
     */
    public function getTemplatePath(string $templateName): string
    {
        $templatePath = $this->templateDir . $templateName;
        if (!is_file($templatePath)) {
            $templatePath .= $this->templateExt;
        }
        if (!file_exists($templatePath)) {
            throw new Exception(sprintf('Файл шаблона по пути "%s" недоступен', $templatePath));
        }
        return $templatePath;
    }

    /**
     * Получить экземпляр парсера
     * @return ParserInterface
     */
    public function makeParser(): ParserInterface
    {
        return new Parser($this);
    }

    /**
     * Рендеринг шаблона
     * @param $templateName
     * @param ViewModelInterface $viewModel
     * @return string
     */
    public function render($templateName, ViewModelInterface $viewModel = null): string
    {
        return $this->makeParser()->render($templateName, $viewModel);
    }


    /**
     * Зарегистрировать сервис в контейнере
     * @param WebApplicationInterface $application
     * @param array ...$arguments
     * @return void
     */
    static public function register(WebApplicationInterface $application, ...$arguments)
    {
        $application[Template::class] = function () use ($arguments){
            return new static(...$arguments);
        };
    }
}